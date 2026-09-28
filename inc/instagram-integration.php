<?php
/**
 * Instagram連携機能
 * 公式Instagram Graph API（Instagram Login）を使用し、
 * 最新投稿をフロントページに自動反映する。
 *
 * @package RiffTheme
 */

// ================================
// APIエンドポイント定数
// ================================
define('RIFF_IG_AUTHORIZE_URL', 'https://www.instagram.com/oauth/authorize');
define('RIFF_IG_TOKEN_URL', 'https://api.instagram.com/oauth/access_token');
define('RIFF_IG_EXCHANGE_URL', 'https://graph.instagram.com/access_token');
define('RIFF_IG_REFRESH_URL', 'https://graph.instagram.com/refresh_access_token');
define('RIFF_IG_MEDIA_URL', 'https://graph.instagram.com/me/media');

// ================================
// Cronスケジュール登録
// 既に稼働中のテーマのため after_switch_theme は今回のデプロイでは発火しない。
// init での冪等チェックで確実にcronを登録する。
// ================================
function riff_instagram_schedule_cron() {
    if (!wp_next_scheduled('riff_instagram_token_refresh')) {
        wp_schedule_event(time(), 'daily', 'riff_instagram_token_refresh');
    }
}
add_action('init', 'riff_instagram_schedule_cron');
add_action('after_switch_theme', 'riff_instagram_schedule_cron');

function riff_instagram_clear_cron() {
    wp_clear_scheduled_hook('riff_instagram_token_refresh');
}
add_action('switch_theme', 'riff_instagram_clear_cron');

// ================================
// トークン自動更新（60日ごとに失効するため期限10日前から更新を試みる）
// ================================
function riff_instagram_refresh_token() {
    $token = get_option('riff_instagram_access_token', '');
    if (empty($token)) {
        return;
    }

    $expires_at = (int) get_option('riff_instagram_token_expires_at', 0);
    if ($expires_at - time() > 10 * DAY_IN_SECONDS) {
        return; // まだ更新不要
    }

    $url = add_query_arg(array(
        'grant_type'   => 'ig_refresh_token',
        'access_token' => $token,
    ), RIFF_IG_REFRESH_URL);

    $response = wp_remote_get($url, array('timeout' => 15));

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        update_option('riff_instagram_last_sync_status', 'error');
        update_option('riff_instagram_last_sync_message', 'アクセストークンの更新に失敗しました。' . (is_wp_error($response) ? $response->get_error_message() : 'HTTPエラー: ' . wp_remote_retrieve_response_code($response)));
        return;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($body['access_token'])) {
        update_option('riff_instagram_last_sync_status', 'error');
        update_option('riff_instagram_last_sync_message', 'アクセストークンの更新レスポンスが不正です。');
        return;
    }

    update_option('riff_instagram_access_token', $body['access_token'], false);
    $expires_in = isset($body['expires_in']) ? (int) $body['expires_in'] : (60 * DAY_IN_SECONDS);
    update_option('riff_instagram_token_expires_at', time() + $expires_in, false);
    update_option('riff_instagram_last_sync_status', 'ok');
    update_option('riff_instagram_last_sync_message', '');
}
add_action('riff_instagram_token_refresh', 'riff_instagram_refresh_token');

// ================================
// 連携切れ警告（管理画面）
// ================================
function riff_instagram_admin_notice() {
    if (!isset($_GET['page']) || $_GET['page'] !== 'riff-instagram-settings') {
        return;
    }
    $status = get_option('riff_instagram_last_sync_status', '');
    $expires_at = (int) get_option('riff_instagram_token_expires_at', 0);
    $has_token = !empty(get_option('riff_instagram_access_token', ''));

    if ($has_token && $expires_at > 0 && $expires_at < time()) {
        echo '<div class="notice notice-error"><p>Instagramとの連携アクセストークンの有効期限が切れています。再度連携してください。</p></div>';
    } elseif ($status === 'error') {
        $message = get_option('riff_instagram_last_sync_message', '');
        echo '<div class="notice notice-error"><p>Instagram連携でエラーが発生しています。' . esc_html($message) . '</p></div>';
    }
}
add_action('admin_notices', 'riff_instagram_admin_notice');

// ================================
// OAuth: 連携開始
// ================================
function riff_do_instagram_oauth_start() {
    if (!current_user_can('manage_options')) {
        wp_die('権限がありません。');
    }
    check_admin_referer('riff_instagram_oauth_start');

    $app_id = get_option('riff_instagram_app_id', '');
    if (empty($app_id)) {
        wp_redirect(admin_url('admin.php?page=riff-instagram-settings&riff_ig_error=no_app_id'));
        exit;
    }

    $state = wp_create_nonce('riff_instagram_oauth');
    set_transient('riff_instagram_oauth_state', $state, 10 * MINUTE_IN_SECONDS);

    // 注意: Instagram側のOAuthはredirect_uriにクエリパラメータを含められないため、
    // admin.php本体のみを指定する（?action=... 等は付与しない）。
    // コールバックはadmin_init側でcode/stateパラメータの有無から検出する。
    $redirect_uri = admin_url('admin.php');

    $url = add_query_arg(array(
        'client_id'     => $app_id,
        'redirect_uri'  => rawurlencode($redirect_uri),
        'scope'         => 'instagram_business_basic',
        'response_type' => 'code',
        'state'         => $state,
    ), RIFF_IG_AUTHORIZE_URL);

    wp_redirect($url);
    exit;
}
add_action('admin_action_riff_instagram_oauth_start', 'riff_do_instagram_oauth_start');

// ================================
// OAuth: コールバック
// Instagram側のOAuthはredirect_uriにクエリパラメータを付与できないため、
// admin_action方式ではなくadmin_init側でcode/stateの有無から検出する。
// ================================
function riff_instagram_maybe_handle_oauth_callback() {
    if (!is_admin() || empty($_GET['code']) || empty($_GET['state'])) {
        return;
    }
    // 他の管理画面ページと衝突しないよう、admin.php本体への直接アクセス時のみ処理する
    global $pagenow;
    if ($pagenow !== 'admin.php' || !empty($_GET['page']) || !empty($_GET['action'])) {
        return;
    }
    riff_do_instagram_oauth_callback();
}
add_action('admin_init', 'riff_instagram_maybe_handle_oauth_callback');

function riff_do_instagram_oauth_callback() {
    if (!current_user_can('manage_options')) {
        wp_die('権限がありません。');
    }

    $state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';
    $stored_state = get_transient('riff_instagram_oauth_state');
    delete_transient('riff_instagram_oauth_state');

    if (empty($state) || empty($stored_state) || !hash_equals($stored_state, $state)) {
        wp_redirect(admin_url('admin.php?page=riff-instagram-settings&riff_ig_error=invalid_state'));
        exit;
    }

    if (empty($_GET['code'])) {
        wp_redirect(admin_url('admin.php?page=riff-instagram-settings&riff_ig_error=no_code'));
        exit;
    }

    $app_id     = get_option('riff_instagram_app_id', '');
    $app_secret = get_option('riff_instagram_app_secret', '');
    // トークン交換時のredirect_uriは、認可リクエスト時に送信した値と完全一致させる必要がある
    $redirect_uri = admin_url('admin.php');

    // 短期トークンを取得
    $response = wp_remote_post(RIFF_IG_TOKEN_URL, array(
        'timeout' => 15,
        'body'    => array(
            'client_id'     => $app_id,
            'client_secret' => $app_secret,
            'grant_type'    => 'authorization_code',
            'redirect_uri'  => $redirect_uri,
            'code'          => sanitize_text_field($_GET['code']),
        ),
    ));

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        wp_redirect(admin_url('admin.php?page=riff-instagram-settings&riff_ig_error=token_exchange_failed'));
        exit;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($body['access_token'])) {
        wp_redirect(admin_url('admin.php?page=riff-instagram-settings&riff_ig_error=token_exchange_failed'));
        exit;
    }

    $short_token = $body['access_token'];
    $ig_user_id  = isset($body['user_id']) ? sanitize_text_field($body['user_id']) : '';

    // 長期トークンに交換
    $exchange_url = add_query_arg(array(
        'grant_type'    => 'ig_exchange_token',
        'client_secret' => $app_secret,
        'access_token'  => $short_token,
    ), RIFF_IG_EXCHANGE_URL);

    $exchange_response = wp_remote_get($exchange_url, array('timeout' => 15));

    if (is_wp_error($exchange_response) || wp_remote_retrieve_response_code($exchange_response) !== 200) {
        wp_redirect(admin_url('admin.php?page=riff-instagram-settings&riff_ig_error=long_lived_exchange_failed'));
        exit;
    }

    $exchange_body = json_decode(wp_remote_retrieve_body($exchange_response), true);
    if (empty($exchange_body['access_token'])) {
        wp_redirect(admin_url('admin.php?page=riff-instagram-settings&riff_ig_error=long_lived_exchange_failed'));
        exit;
    }

    $long_token = $exchange_body['access_token'];
    $expires_in = isset($exchange_body['expires_in']) ? (int) $exchange_body['expires_in'] : (60 * DAY_IN_SECONDS);

    update_option('riff_instagram_access_token', $long_token, false);
    update_option('riff_instagram_token_expires_at', time() + $expires_in, false);
    update_option('riff_instagram_ig_user_id', $ig_user_id, false);
    update_option('riff_instagram_last_sync_status', 'ok');
    update_option('riff_instagram_last_sync_message', '');
    delete_transient('riff_instagram_media'); // すぐに新しいトークンで再取得させる

    wp_redirect(admin_url('admin.php?page=riff-instagram-settings&riff_ig_connected=1'));
    exit;
}
add_action('admin_action_riff_instagram_oauth_callback', 'riff_do_instagram_oauth_callback');

// ================================
// 連携解除
// ================================
function riff_do_instagram_disconnect() {
    if (!current_user_can('manage_options')) {
        wp_die('権限がありません。');
    }
    check_admin_referer('riff_instagram_disconnect');

    delete_option('riff_instagram_access_token');
    delete_option('riff_instagram_token_expires_at');
    delete_option('riff_instagram_ig_user_id');
    delete_option('riff_instagram_last_sync_status');
    delete_option('riff_instagram_last_sync_message');
    delete_transient('riff_instagram_media');

    wp_redirect(admin_url('admin.php?page=riff-instagram-settings&riff_ig_disconnected=1'));
    exit;
}
add_action('admin_action_riff_instagram_disconnect', 'riff_do_instagram_disconnect');

// ================================
// 手動同期（トランジェント破棄→再取得）
// ================================
function riff_do_instagram_manual_sync() {
    if (!current_user_can('manage_options')) {
        wp_die('権限がありません。');
    }
    check_admin_referer('riff_instagram_manual_sync');

    delete_transient('riff_instagram_media');
    riff_get_instagram_media();

    wp_redirect(admin_url('admin.php?page=riff-instagram-settings&riff_ig_synced=1'));
    exit;
}
add_action('admin_action_riff_instagram_manual_sync', 'riff_do_instagram_manual_sync');

// ================================
// 投稿データ取得（30分キャッシュ＋失敗時フォールバック）
// ================================
function riff_get_instagram_media() {
    $cached = get_transient('riff_instagram_media');
    if (is_array($cached)) {
        return $cached;
    }

    $token = get_option('riff_instagram_access_token', '');
    if (empty($token)) {
        return get_option('riff_instagram_cached_media', array());
    }

    $url = add_query_arg(array(
        'fields'       => 'id,caption,media_type,media_url,thumbnail_url,permalink',
        'limit'        => 8,
        'access_token' => $token,
    ), RIFF_IG_MEDIA_URL);

    $response = wp_remote_get($url, array('timeout' => 15));

    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        update_option('riff_instagram_last_sync_status', 'error');
        update_option('riff_instagram_last_sync_message', '投稿データの取得に失敗しました。' . (is_wp_error($response) ? $response->get_error_message() : 'HTTPエラー: ' . wp_remote_retrieve_response_code($response)));
        return get_option('riff_instagram_cached_media', array());
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($body['data']) || !is_array($body['data'])) {
        update_option('riff_instagram_last_sync_status', 'error');
        update_option('riff_instagram_last_sync_message', '投稿データのレスポンスが不正です。');
        return get_option('riff_instagram_cached_media', array());
    }

    $items = array();
    foreach (array_slice($body['data'], 0, 8) as $post) {
        if (empty($post['permalink'])) {
            continue;
        }
        $media_type = isset($post['media_type']) ? $post['media_type'] : 'IMAGE';
        $image = ($media_type === 'VIDEO' && !empty($post['thumbnail_url']))
            ? $post['thumbnail_url']
            : (!empty($post['media_url']) ? $post['media_url'] : (!empty($post['thumbnail_url']) ? $post['thumbnail_url'] : ''));

        if (empty($image)) {
            continue;
        }

        $items[] = array(
            'id'         => isset($post['id']) ? $post['id'] : '',
            'image'      => $image,
            'permalink'  => $post['permalink'],
            'media_type' => $media_type,
            'caption'    => isset($post['caption']) ? $post['caption'] : '',
        );
    }

    set_transient('riff_instagram_media', $items, 30 * MINUTE_IN_SECONDS);
    update_option('riff_instagram_cached_media', $items);
    update_option('riff_instagram_last_sync_status', 'ok');
    update_option('riff_instagram_last_sync_message', '');
    update_option('riff_instagram_last_sync_time', time());

    return $items;
}

// ================================
// フロント表示
// ================================
function riff_display_instagram_section() {
    $items = riff_get_instagram_media();
    if (empty($items)) {
        return;
    }

    $title    = get_option('riff_instagram_section_title', 'INSTAGRAM');
    $subtitle = get_option('riff_instagram_section_subtitle', '最新の投稿');
    // フォローボタンのURL：個別設定が空の場合はフッターのInstagram URLを再利用する
    $follow_url  = get_option('riff_instagram_follow_url', '');
    if (empty($follow_url)) {
        $follow_url = get_option('riff_footer_instagram_url', '');
    }
    $follow_text = get_option('riff_instagram_follow_button_text', 'Instagramをフォローする');
    ?>
    <style id="riff-instagram-css">
    /* ===== Instagram Section ===== */
    .riff-instagram-section {
        padding: 80px 20px;
    }
    .riff-instagram-inner {
        max-width: 1100px;
        margin: 0 auto;
    }
    .riff-instagram-header {
        text-align: center;
        margin-bottom: 40px;
    }
    .riff-instagram-subtitle {
        font-size: 0.85rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: var(--primary-color, #007cba);
        margin: 0 0 8px;
    }
    .riff-instagram-title {
        font-size: clamp(1.8rem, 4vw, 2.6rem);
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
        letter-spacing: 0.05em;
    }
    .riff-instagram-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
    .riff-instagram-item {
        position: relative;
        display: block;
        aspect-ratio: 1 / 1;
        overflow: hidden;
        border-radius: 8px;
        background: linear-gradient(100deg, #eef1f4 30%, #f7f9fb 50%, #eef1f4 70%);
        background-size: 200% 100%;
        animation: riffInstagramShimmer 1.4s ease-in-out infinite;
    }
    .riff-instagram-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        opacity: 0;
        transition: opacity 0.4s ease, transform 0.3s ease;
    }
    .riff-instagram-item img.riff-instagram-loaded {
        opacity: 1;
    }
    .riff-instagram-item:hover img {
        transform: scale(1.06);
    }
    @keyframes riffInstagramShimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    .riff-instagram-carousel-icon,
    .riff-instagram-video-icon {
        position: absolute;
        top: 8px;
        right: 8px;
        color: #fff;
        filter: drop-shadow(0 0 3px rgba(0,0,0,.6));
        line-height: 0;
    }
    .riff-instagram-follow-wrap {
        text-align: center;
        margin-top: 40px;
    }
    .riff-instagram-follow-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        border-radius: 999px;
        background: linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);
        color: #fff;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: opacity 0.2s ease;
    }
    .riff-instagram-follow-btn:hover {
        opacity: 0.85;
        color: #fff;
    }
    @media (max-width: 992px) {
        .riff-instagram-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 768px) {
        .riff-instagram-section { padding: 56px 16px; }
        .riff-instagram-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    }
    </style>
    <section class="riff-instagram-section fadeDownTrigger" id="instagram">
        <div class="riff-instagram-inner">
            <div class="riff-instagram-header fade-in">
                <p class="riff-instagram-subtitle"><?php echo esc_html($subtitle); ?></p>
                <h2 class="riff-instagram-title"><?php echo esc_html($title); ?></h2>
            </div>

            <div class="riff-instagram-grid fade-in" data-delay="0.1s">
                <?php foreach ($items as $item):
                    $caption = !empty($item['caption']) ? trim($item['caption']) : '';
                    $alt_text = $caption !== '' ? wp_trim_words($caption, 20, '…') : 'Instagramの投稿';
                ?>
                <a class="riff-instagram-item"
                   href="<?php echo esc_url($item['permalink']); ?>"
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="Instagramの投稿を見る">
                    <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($alt_text); ?>" loading="lazy" width="400" height="400" onload="this.classList.add('riff-instagram-loaded')">
                    <?php if ($item['media_type'] === 'CAROUSEL_ALBUM'): ?>
                    <span class="riff-instagram-carousel-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M22 16V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zM11 4h9v9l-3.5-3.5a1 1 0 0 0-1.4 0L11 14V4zM2 8v12a2 2 0 0 0 2 2h12v-2H4V8H2z"/></svg>
                    </span>
                    <?php elseif ($item['media_type'] === 'VIDEO'): ?>
                    <span class="riff-instagram-video-icon" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                    </span>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($follow_url)): ?>
            <div class="riff-instagram-follow-wrap fade-in" data-delay="0.2s">
                <a href="<?php echo esc_url($follow_url); ?>" class="riff-instagram-follow-btn" target="_blank" rel="noopener noreferrer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    <span><?php echo esc_html($follow_text); ?></span>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

// ================================
// 管理画面
// ================================
function riff_instagram_admin_page() {
    add_submenu_page(
        'riff-theme-settings',
        'Instagram設定',
        'Instagram設定',
        'manage_options',
        'riff-instagram-settings',
        'riff_instagram_admin_page_content'
    );
}
add_action('admin_menu', 'riff_instagram_admin_page');

function riff_instagram_admin_page_content() {
    if (isset($_POST['submit'])) {
        if (!isset($_POST['riff_instagram_nonce']) || !wp_verify_nonce($_POST['riff_instagram_nonce'], 'riff_instagram_admin_page')) {
            wp_die('セキュリティチェックに失敗しました。');
        }

        update_option('riff_instagram_app_id', sanitize_text_field($_POST['instagram_app_id']));
        if (!empty($_POST['instagram_app_secret'])) {
            update_option('riff_instagram_app_secret', sanitize_text_field($_POST['instagram_app_secret']), false);
        }
        update_option('riff_instagram_section_title', sanitize_text_field($_POST['instagram_section_title']));
        update_option('riff_instagram_section_subtitle', sanitize_text_field($_POST['instagram_section_subtitle']));
        update_option('riff_instagram_follow_url', esc_url_raw($_POST['instagram_follow_url']));
        update_option('riff_instagram_follow_button_text', sanitize_text_field($_POST['instagram_follow_button_text']));

        if (!empty($_POST['instagram_manual_token'])) {
            update_option('riff_instagram_access_token', sanitize_text_field($_POST['instagram_manual_token']), false);
            update_option('riff_instagram_token_expires_at', time() + (60 * DAY_IN_SECONDS), false);
            update_option('riff_instagram_last_sync_status', 'ok');
            delete_transient('riff_instagram_media');
        }

        echo '<div class="notice notice-success"><p>Instagram設定を保存しました。</p></div>';
    }

    if (!empty($_GET['riff_ig_connected'])) {
        echo '<div class="notice notice-success"><p>Instagramと連携しました。</p></div>';
    }
    if (!empty($_GET['riff_ig_disconnected'])) {
        echo '<div class="notice notice-success"><p>連携を解除しました。</p></div>';
    }
    if (!empty($_GET['riff_ig_synced'])) {
        echo '<div class="notice notice-success"><p>投稿データを再取得しました。</p></div>';
    }
    if (!empty($_GET['riff_ig_error'])) {
        echo '<div class="notice notice-error"><p>連携処理でエラーが発生しました（' . esc_html($_GET['riff_ig_error']) . '）。App ID / App Secretの設定をご確認の上、もう一度お試しください。</p></div>';
    }

    $app_id      = get_option('riff_instagram_app_id', '');
    $has_secret  = !empty(get_option('riff_instagram_app_secret', ''));
    $has_token   = !empty(get_option('riff_instagram_access_token', ''));
    $expires_at  = (int) get_option('riff_instagram_token_expires_at', 0);
    $sync_status = get_option('riff_instagram_last_sync_status', '');

    $section_title    = get_option('riff_instagram_section_title', 'INSTAGRAM');
    $section_subtitle = get_option('riff_instagram_section_subtitle', '最新の投稿');
    $follow_url        = get_option('riff_instagram_follow_url', '');
    $follow_button_text = get_option('riff_instagram_follow_button_text', 'Instagramをフォローする');
    ?>
    <div class="wrap">
        <h1>Instagram設定</h1>

        <div class="riff-admin-header">
            <h1>Instagram設定</h1>
            <p>フロントページの「Instagram」セクションに表示する投稿を、公式Instagram Graph APIと連携して自動更新します。</p>
        </div>

        <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">連携ステータス</h2>
        <table class="form-table">
            <tr>
                <th scope="row">連携状態</th>
                <td>
                    <?php if ($has_token): ?>
                        <strong style="color:#2e7d32;">連携済み</strong>
                        <?php if ($expires_at > 0): ?>
                        <p class="description">アクセストークン有効期限: <?php echo esc_html(date_i18n('Y年n月j日', $expires_at)); ?>（60日ごとに自動更新されます）</p>
                        <?php endif; ?>
                        <?php if ($sync_status === 'error'): ?>
                        <p class="description" style="color:#d32f2f;">直近の同期でエラーが発生しています。</p>
                        <?php endif; ?>
                        <p>
                            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?action=riff_instagram_disconnect'), 'riff_instagram_disconnect')); ?>"
                               class="button" onclick="return confirm('連携を解除しますか？');">連携を解除</a>
                            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?action=riff_instagram_manual_sync'), 'riff_instagram_manual_sync')); ?>"
                               class="button">今すぐ同期</a>
                        </p>
                    <?php else: ?>
                        <strong style="color:#999;">未連携</strong>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row">Instagramと連携する</th>
                <td>
                    <?php if ($app_id && $has_secret): ?>
                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?action=riff_instagram_oauth_start'), 'riff_instagram_oauth_start')); ?>" class="button button-primary">
                        Instagramと連携する
                    </a>
                    <p class="description">下記のApp ID・App Secretを保存してから連携してください。</p>
                    <?php else: ?>
                    <p class="description">先に下記のApp ID・App Secretを入力・保存してください。</p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <form method="post" action="">
            <?php wp_nonce_field('riff_instagram_admin_page', 'riff_instagram_nonce'); ?>

            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">Meta アプリ設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">App ID</th>
                    <td><input type="text" name="instagram_app_id" value="<?php echo esc_attr($app_id); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">App Secret</th>
                    <td>
                        <input type="password" name="instagram_app_secret" value="" class="regular-text" placeholder="<?php echo $has_secret ? '設定済み（変更する場合のみ入力）' : ''; ?>" autocomplete="new-password" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">リダイレクトURI</th>
                    <td>
                        <code><?php echo esc_html(admin_url('admin.php')); ?></code>
                        <p class="description">Meta for Developersのアプリ設定で、このURLを「有効なOAuthリダイレクトURI」として登録してください（末尾にクエリパラメータは付けないでください）。</p>
                    </td>
                </tr>
            </table>

            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">アクセストークン手動入力（連携がうまくいかない場合の代替手段）</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">アクセストークン</th>
                    <td>
                        <input type="text" name="instagram_manual_token" value="" class="regular-text" placeholder="Meta Graph API Explorer等で取得したトークンを貼り付け" />
                        <p class="description">上の「Instagramと連携する」がうまく動作しない場合、手動で取得したトークンをここに貼り付けて保存することでも動作します。</p>
                    </td>
                </tr>
            </table>

            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">セクション表示設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">セクションタイトル</th>
                    <td><input type="text" name="instagram_section_title" value="<?php echo esc_attr($section_title); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">サブタイトル</th>
                    <td><input type="text" name="instagram_section_subtitle" value="<?php echo esc_attr($section_subtitle); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">フォローボタンURL</th>
                    <td>
                        <input type="url" name="instagram_follow_url" value="<?php echo esc_attr($follow_url); ?>" class="regular-text" placeholder="<?php echo esc_attr(get_option('riff_footer_instagram_url', 'https://www.instagram.com/xxxxx/')); ?>" />
                        <p class="description">
                            空欄の場合、「フッター設定」で登録済みのInstagram URLを自動的に使用します。個別に別のURLを使いたい場合のみ入力してください。
                            <?php $footer_ig = get_option('riff_footer_instagram_url', ''); if ($footer_ig): ?>
                            （現在のフッター設定値: <code><?php echo esc_html($footer_ig); ?></code>）
                            <?php endif; ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">フォローボタン文言</th>
                    <td><input type="text" name="instagram_follow_button_text" value="<?php echo esc_attr($follow_button_text); ?>" class="regular-text" /></td>
                </tr>
            </table>

            <p class="description">表示のON/OFF・表示位置は「セクション管理」画面で設定できます。</p>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
