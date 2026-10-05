<?php
/**
 * 日程予約：管理画面（Google連携設定・予約一覧・予約詳細）
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', function () {
    add_submenu_page(
        'riff-theme-settings',
        '日程予約設定',
        '日程予約設定',
        'manage_options',
        'riff-booking-settings',
        'riff_booking_settings_page'
    );
}, 20);

/* ------------------------------------------------------------------
 * 設定の保存・Google連携
 * ------------------------------------------------------------------ */

add_action('admin_post_riff_booking_save', function () {
    if (!current_user_can('manage_options')) {
        wp_die('権限がありません。');
    }
    check_admin_referer('riff_booking_save');
    // 設定画面は複数のフォームに分かれているため、送信された項目だけを更新する
    $changes = array();
    if (isset($_POST['client_id'])) {
        $changes['client_id'] = sanitize_text_field(wp_unslash($_POST['client_id']));
    }
    // シークレットは画面に再表示しないため、入力があった時だけ更新する
    $secret = sanitize_text_field(wp_unslash($_POST['client_secret'] ?? ''));
    if ($secret !== '') {
        $changes['client_secret'] = $secret;
    }
    if (isset($_POST['page_id'])) {
        $changes['page_id'] = absint($_POST['page_id']);
    }
    if (!empty($_POST['notify_email']) && is_email(wp_unslash($_POST['notify_email']))) {
        $changes['notify_email'] = sanitize_email(wp_unslash($_POST['notify_email']));
    }
    if (isset($_POST['busy_calendar_ids_submitted'])) {
        $changes['busy_calendar_ids'] = array_values(array_filter(array_map('sanitize_text_field', wp_unslash((array) ($_POST['busy_calendar_ids'] ?? array())))));
        riff_booking_flush_busy_cache();
    }
    riff_booking_update_settings($changes);
    wp_safe_redirect(admin_url('admin.php?page=riff-booking-settings&updated=1'));
    exit;
});

add_action('admin_post_riff_booking_connect', function () {
    if (!current_user_can('manage_options')) {
        wp_die('権限がありません。');
    }
    check_admin_referer('riff_booking_connect');
    $state = wp_generate_password(24, false);
    set_transient('riff_booking_oauth_state_' . get_current_user_id(), $state, 15 * MINUTE_IN_SECONDS);
    wp_redirect(riff_booking_oauth_auth_url($state));
    exit;
});

// Googleの認可画面からの戻り先（リダイレクトURI）
add_action('admin_post_riff_booking_oauth', function () {
    if (!current_user_can('manage_options')) {
        wp_die('権限がありません。');
    }
    $expected = get_transient('riff_booking_oauth_state_' . get_current_user_id());
    delete_transient('riff_booking_oauth_state_' . get_current_user_id());
    $back = admin_url('admin.php?page=riff-booking-settings');

    if (!$expected || !hash_equals($expected, (string) ($_GET['state'] ?? ''))) {
        wp_safe_redirect(add_query_arg('rb_error', rawurlencode('認証の有効期限が切れました。もう一度「Googleカレンダーと連携する」を押してください。'), $back));
        exit;
    }
    if (!empty($_GET['error']) || empty($_GET['code'])) {
        wp_safe_redirect(add_query_arg('rb_error', rawurlencode('Googleでの許可がキャンセルされました。'), $back));
        exit;
    }
    $result = riff_booking_oauth_exchange_code(sanitize_text_field(wp_unslash($_GET['code'])));
    if (is_wp_error($result)) {
        wp_safe_redirect(add_query_arg('rb_error', rawurlencode($result->get_error_message()), $back));
        exit;
    }
    wp_safe_redirect(add_query_arg('connected', 1, $back));
    exit;
});

add_action('admin_post_riff_booking_disconnect', function () {
    if (!current_user_can('manage_options')) {
        wp_die('権限がありません。');
    }
    check_admin_referer('riff_booking_disconnect');
    $token = riff_booking_decrypt(riff_booking_settings()['refresh_token']);
    if ($token !== '') {
        wp_remote_post('https://oauth2.googleapis.com/revoke', array('timeout' => 10, 'body' => array('token' => $token)));
    }
    riff_booking_update_settings(array('refresh_token' => '', 'connected_email' => '', 'busy_calendar_ids' => array()));
    delete_transient('riff_booking_access_token');
    riff_booking_flush_busy_cache();
    wp_safe_redirect(admin_url('admin.php?page=riff-booking-settings&disconnected=1'));
    exit;
});

/* ------------------------------------------------------------------
 * 設定画面
 * ------------------------------------------------------------------ */

function riff_booking_settings_page() {
    $s = riff_booking_settings();
    $connected = $s['refresh_token'] !== '';
    $calendars = $connected ? riff_booking_list_calendars() : array();
    $pages = get_pages(array('post_status' => 'publish'));
    ?>
    <div class="wrap">
        <h1>日程予約設定</h1>

        <?php if (isset($_GET['updated'])): ?><div class="notice notice-success is-dismissible"><p>設定を保存しました。</p></div><?php endif; ?>
        <?php if (isset($_GET['connected'])): ?><div class="notice notice-success is-dismissible"><p>Googleカレンダーと連携しました。下の「空き判定に使うカレンダー」を確認して保存してください。</p></div><?php endif; ?>
        <?php if (isset($_GET['disconnected'])): ?><div class="notice notice-warning is-dismissible"><p>Googleカレンダーとの連携を解除しました。</p></div><?php endif; ?>
        <?php if (!empty($_GET['rb_error'])): ?><div class="notice notice-error"><p><?php echo esc_html(wp_unslash($_GET['rb_error'])); ?></p></div><?php endif; ?>

        <p>
            状態：
            <?php if (riff_booking_is_ready()): ?>
                <strong style="color:#00a32a;">● 予約受付中</strong>（<a href="<?php echo esc_url(riff_booking_page_url()); ?>" target="_blank">予約ページを開く</a>）
            <?php else: ?>
                <strong style="color:#d63638;">● 停止中</strong>（Google連携と予約ページの設定が済むと受付を開始し、右側の「オンライン面談を予約する」ボタンが表示されます）
            <?php endif; ?>
        </p>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="riff_booking_save">
            <?php wp_nonce_field('riff_booking_save'); ?>

            <h2>1. Google Cloud の OAuth クライアント</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">承認済みのリダイレクトURI</th>
                    <td>
                        <input type="text" class="large-text code" readonly value="<?php echo esc_attr(riff_booking_oauth_redirect_uri()); ?>" onclick="this.select();">
                        <p class="description">Google Cloud で OAuth クライアントを作成するときに、この値をそのまま貼り付けてください。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="rb-client-id">クライアントID</label></th>
                    <td><input type="text" id="rb-client-id" name="client_id" class="large-text code" value="<?php echo esc_attr($s['client_id']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="rb-client-secret">クライアントシークレット</label></th>
                    <td>
                        <input type="password" id="rb-client-secret" name="client_secret" class="regular-text code" autocomplete="new-password" placeholder="<?php echo $s['client_secret'] ? '設定済み（変更する場合のみ入力）' : ''; ?>">
                    </td>
                </tr>
            </table>

            <h2>2. 予約ページ・通知先</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="rb-page">予約ページ</label></th>
                    <td>
                        <select id="rb-page" name="page_id">
                            <option value="0">選択してください</option>
                            <?php foreach ($pages as $page): ?>
                            <option value="<?php echo (int) $page->ID; ?>" <?php selected($s['page_id'], $page->ID); ?>><?php echo esc_html($page->post_title); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">本文にショートコード <code>[riff_booking]</code> を入れた固定ページを選択します。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="rb-notify">通知先メールアドレス</label></th>
                    <td><input type="email" id="rb-notify" name="notify_email" class="regular-text" value="<?php echo esc_attr($s['notify_email']); ?>"></td>
                </tr>
            </table>

            <?php submit_button('設定を保存'); ?>
        </form>

        <h2>3. Googleカレンダーとの連携</h2>
        <?php if ($connected): ?>
            <p>連携中のアカウント：<strong><?php echo esc_html($s['connected_email']); ?></strong></p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('連携を解除すると予約を受け付けられなくなります。よろしいですか？');">
                <input type="hidden" name="action" value="riff_booking_disconnect">
                <?php wp_nonce_field('riff_booking_disconnect'); ?>
                <?php submit_button('連携を解除する', 'delete', 'submit', false); ?>
            </form>
        <?php elseif ($s['client_id'] && $s['client_secret']): ?>
            <p>riffsystem01@gmail.com でログインし、カレンダーへのアクセスを許可してください。</p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="riff_booking_connect">
                <?php wp_nonce_field('riff_booking_connect'); ?>
                <?php submit_button('Googleカレンダーと連携する', 'primary', 'submit', false); ?>
            </form>
        <?php else: ?>
            <p class="description">先に「1. OAuth クライアント」のクライアントIDとシークレットを保存してください。</p>
        <?php endif; ?>

        <?php if ($connected): ?>
        <h2>4. 空き判定に使うカレンダー</h2>
        <p>チェックしたカレンダーに予定がある時間（前後30分を含む）は予約できなくなります。予約の予定は「<?php echo esc_html($s['connected_email']); ?>」のメインカレンダーに作成されます。</p>
        <?php if (is_wp_error($calendars)): ?>
            <div class="notice notice-error inline"><p>カレンダー一覧を取得できませんでした：<?php echo esc_html($calendars->get_error_message()); ?></p></div>
        <?php else: ?>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="riff_booking_save">
                <input type="hidden" name="busy_calendar_ids_submitted" value="1">
                <?php wp_nonce_field('riff_booking_save'); ?>
                <fieldset>
                <?php
                $saved = $s['busy_calendar_ids'];
                foreach ($calendars as $cal):
                    $is_holiday = strpos($cal['id'], '#holiday@') !== false;
                    // 未保存の初期状態では、祝日カレンダー以外の自分のカレンダーをすべて対象にする
                    $on = $saved
                        ? in_array($cal['id'], $saved, true) || (!empty($cal['primary']) && in_array('primary', $saved, true))
                        : !$is_holiday && in_array($cal['accessRole'], array('owner', 'writer'), true);
                    ?>
                    <label style="display:block;margin:6px 0;">
                        <input type="checkbox" name="busy_calendar_ids[]" value="<?php echo esc_attr($cal['id']); ?>" <?php checked($on); ?>>
                        <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:<?php echo esc_attr($cal['backgroundColor'] ?? '#999'); ?>;"></span>
                        <?php echo esc_html($cal['summaryOverride'] ?? $cal['summary']); ?>
                        <?php if (!empty($cal['primary'])): ?><strong>（メイン）</strong><?php endif; ?>
                        <?php if ($is_holiday): ?><span class="description">※祝日カレンダーは通常チェック不要です</span><?php endif; ?>
                    </label>
                <?php endforeach; ?>
                </fieldset>
                <p class="description">別のGoogleアカウントのプライベート予定も反映したい場合は、そのカレンダーを「<?php echo esc_html($s['connected_email']); ?>」に共有（「予定の表示（時間枠のみ）」以上）すると、ここに表示されます。</p>
                <?php submit_button('カレンダー設定を保存'); ?>
            </form>
            <?php if (!$saved): ?>
            <p><strong>まだ保存されていません。</strong>チェック内容を確認して「カレンダー設定を保存」を押してください（保存するまではメインカレンダーのみで空きを判定します）。</p>
            <?php endif; ?>
        <?php endif; ?>
        <?php endif; ?>

        <?php $log = get_option('riff_booking_log', array()); if ($log): ?>
        <h2>エラーログ（直近）</h2>
        <textarea class="large-text code" rows="6" readonly><?php echo esc_textarea(implode("\n", array_slice($log, 0, 20))); ?></textarea>
        <?php endif; ?>
    </div>
    <?php
}

/* ------------------------------------------------------------------
 * 予約一覧
 * ------------------------------------------------------------------ */

add_filter('manage_' . RIFF_BOOKING_POST_TYPE . '_posts_columns', function () {
    return array(
        'cb'          => '<input type="checkbox">',
        'title'       => '予約',
        'rb_status'   => '状態',
        'rb_type'     => '種別',
        'rb_contact'  => '連絡先',
        'date'        => '受付日時',
    );
});

add_action('manage_' . RIFF_BOOKING_POST_TYPE . '_posts_custom_column', function ($column, $post_id) {
    $b = riff_booking_get($post_id);
    switch ($column) {
        case 'rb_status':
            $colors = array('confirmed' => '#00a32a', 'request' => '#dba617', 'cancelled' => '#a7aaad');
            printf('<strong style="color:%s">%s</strong>', esc_attr($colors[$b['status']] ?? '#000'), esc_html(riff_booking_status_label($b['status'])));
            if ($b['status'] === 'confirmed' && $b['start'] < time()) {
                echo '<br><span class="description">実施済み</span>';
            }
            break;
        case 'rb_type':
            echo esc_html($b['inquiry_type']);
            break;
        case 'rb_contact':
            echo esc_html($b['email']) . '<br>' . esc_html($b['phone']);
            break;
    }
}, 10, 2);

/* ------------------------------------------------------------------
 * 予約詳細（編集画面）
 * ------------------------------------------------------------------ */

add_action('add_meta_boxes_' . RIFF_BOOKING_POST_TYPE, function () {
    add_meta_box('riff_booking_detail', '予約内容', 'riff_booking_detail_metabox', RIFF_BOOKING_POST_TYPE, 'normal', 'high');
    add_meta_box('riff_booking_actions', '操作', 'riff_booking_actions_metabox', RIFF_BOOKING_POST_TYPE, 'side', 'high');
    remove_meta_box('submitdiv', RIFF_BOOKING_POST_TYPE, 'side');
});

function riff_booking_detail_metabox($post) {
    $b = riff_booking_sync_from_calendar(riff_booking_get($post->ID));
    $rows = array(
        '状態'               => riff_booking_status_label($b['status']),
        '日時'               => $b['start'] ? riff_booking_format_range($b['start'], $b['end']) : '未確定',
        'Google Meet'        => $b['meet_url'],
        '会社名'             => $b['company'],
        '氏名'               => $b['name'],
        'メールアドレス'     => $b['email'],
        '電話番号'           => $b['phone'],
        'お問い合わせ種別'   => $b['inquiry_type'],
        '現在のホームページURL' => $b['site_url'],
        'ご予算'             => $b['budget'],
        'ご希望の時期'       => $b['timing'],
        '備考'               => $b['note'],
    );
    echo '<table class="widefat striped"><tbody>';
    foreach ($rows as $label => $value) {
        $html = $value === '' || $value === null ? '—' : nl2br(esc_html($value));
        if ($value && in_array($label, array('Google Meet', '現在のホームページURL'), true)) {
            $html = '<a href="' . esc_url($value) . '" target="_blank" rel="noopener">' . esc_html($value) . '</a>';
        }
        echo '<tr><th style="width:180px;">' . esc_html($label) . '</th><td>' . $html . '</td></tr>';
    }
    echo '</tbody></table>';
}

function riff_booking_actions_metabox($post) {
    $b = riff_booking_get($post->ID);
    $action_url = admin_url('admin-post.php');
    if (isset($_GET['rb_notice'])) {
        echo '<div class="notice notice-info inline"><p>' . esc_html(wp_unslash($_GET['rb_notice'])) . '</p></div>';
    }
    if ($b['status'] === 'request') {
        $default = (new DateTimeImmutable('tomorrow', wp_timezone()))->setTime(RIFF_BOOKING_OPEN_HOUR, 0)->format('Y-m-d\TH:i');
        ?>
        <p>備考の希望日時を確認し、日時を決めて確定してください。Meet付きの予定作成と確定メールの送信を自動で行います（受付時間外の日時も指定できます）。</p>
        <form method="post" action="<?php echo esc_url($action_url); ?>">
            <input type="hidden" name="action" value="riff_booking_admin_confirm">
            <input type="hidden" name="booking_id" value="<?php echo (int) $b['id']; ?>">
            <?php wp_nonce_field('riff_booking_admin_' . $b['id']); ?>
            <p><input type="datetime-local" name="start" value="<?php echo esc_attr($default); ?>" step="1800" required style="width:100%;"></p>
            <p><button type="submit" class="button button-primary" style="width:100%;">この日時で確定する</button></p>
        </form>
        <?php
    }
    if ($b['status'] !== 'cancelled') {
        ?>
        <form method="post" action="<?php echo esc_url($action_url); ?>" onsubmit="return confirm('この予約をキャンセルし、お客様にキャンセルメールを送ります。よろしいですか？');">
            <input type="hidden" name="action" value="riff_booking_admin_cancel">
            <input type="hidden" name="booking_id" value="<?php echo (int) $b['id']; ?>">
            <?php wp_nonce_field('riff_booking_admin_' . $b['id']); ?>
            <p><button type="submit" class="button button-link-delete">予約をキャンセルする</button></p>
        </form>
        <?php
    }
    echo '<p class="description">お客様用の変更・キャンセル画面：<br><a href="' . esc_url(riff_booking_page_url(array('rb' => $b['token']))) . '" target="_blank">開く</a></p>';
}

function riff_booking_admin_guard() {
    $id = absint($_POST['booking_id'] ?? 0);
    if (!current_user_can('edit_post', $id)) {
        wp_die('権限がありません。');
    }
    check_admin_referer('riff_booking_admin_' . $id);
    $b = riff_booking_get($id);
    if (!$b) {
        wp_die('予約が見つかりません。');
    }
    return $b;
}

function riff_booking_admin_back($b, $notice) {
    wp_safe_redirect(add_query_arg('rb_notice', rawurlencode($notice), admin_url('post.php?post=' . $b['id'] . '&action=edit')));
    exit;
}

add_action('admin_post_riff_booking_admin_confirm', function () {
    $b = riff_booking_admin_guard();
    if ($b['status'] !== 'request') {
        riff_booking_admin_back($b, 'この予約はすでに確定またはキャンセルされています。');
    }
    $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', sanitize_text_field(wp_unslash($_POST['start'] ?? '')), wp_timezone());
    if (!$dt || $dt->getTimestamp() <= time()) {
        riff_booking_admin_back($b, '未来の日時を指定してください。');
    }
    $result = riff_booking_confirm_request($b, $dt->getTimestamp());
    riff_booking_admin_back($b, is_wp_error($result) ? '確定に失敗しました：' . $result->get_error_message() : '日時を確定し、お客様に確定メールを送りました。');
});

add_action('admin_post_riff_booking_admin_cancel', function () {
    $b = riff_booking_admin_guard();
    $result = riff_booking_cancel($b);
    riff_booking_admin_back($b, is_wp_error($result) ? 'キャンセルに失敗しました：' . $result->get_error_message() : '予約をキャンセルし、お客様にメールを送りました。');
});

// 予約を管理画面のゴミ箱に入れた場合も、Googleカレンダーの予定を残さない
add_action('wp_trash_post', function ($post_id) {
    $b = riff_booking_get($post_id);
    if ($b && $b['status'] === 'confirmed' && $b['start'] > time()) {
        riff_booking_delete_event($b);
        riff_booking_save_meta($post_id, array('status' => 'cancelled'));
    }
});
