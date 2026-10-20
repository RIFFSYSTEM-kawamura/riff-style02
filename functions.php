<?php
/**
 * RIFF Theme Functions
 * WordPressテーマ
 * Version: 2.0
 */

// テーマのセットアップ
function riff_theme_setup() {
    // テーマサポートを追加
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('custom-logo');
    add_theme_support('menus');
    
    // ナビゲーションメニューを登録
    register_nav_menus(array(
        'primary' => 'プライマリーメニュー',
        'footer' => 'フッターメニュー',
    ));
    
    // 画像サイズを追加
    add_image_size('hero-slide', 1920, 800, true);
    add_image_size('service-thumb', 400, 300, true);
    add_image_size('blog-thumb', 600, 400, true);
}
add_action('after_setup_theme', 'riff_theme_setup');

/**
 * SNSリンクの共通定義。
 * フッターとハンバーガーメニュー（モバイルナビ）の両方がここを参照するため、
 * フッター設定でSNSを追加・変更すると自動的に両方に反映される。
 * 新しいSNSを追加する場合はここに1件追加するだけでよい。
 */
function riff_get_social_platforms() {
    return array(
        'instagram' => array('option' => 'riff_footer_instagram_url', 'label' => 'Instagram'),
        'twitter'   => array('option' => 'riff_footer_twitter_url',   'label' => 'X (Twitter)'),
        'facebook'  => array('option' => 'riff_footer_facebook_url',  'label' => 'Facebook'),
        'youtube'   => array('option' => 'riff_footer_youtube_url',   'label' => 'YouTube'),
        'tiktok'    => array('option' => 'riff_footer_tiktok_url',    'label' => 'TikTok'),
        'line'      => array('option' => 'riff_footer_line_url',      'label' => 'LINE'),
    );
}

// SNSアイコン取得関数（フッター・モバイルメニュー共通）
function riff_get_social_icon($platform) {
    $icons = array(
        'instagram' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
        'twitter' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        'facebook' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        'youtube' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
        'tiktok' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
        'line' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12.017.572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/></svg>'
    );

    return isset($icons[$platform]) ? $icons[$platform] : '';
}

/**
 * カスタマイザー保存のURL（生の画像URL）を、可能ならメディアライブラリの添付ファイルIDに解決し、
 * wp_get_attachment_image()でsrcset/sizes・width/height付きの<img>を出力する。
 * 添付IDが見つからない場合は通常の<img>タグにフォールバックする。
 */
function riff_option_image($url, $args = array()) {
    if (empty($url)) return '';

    $defaults = array(
        'size'          => 'large',
        'class'         => '',
        'alt'           => '',
        'loading'       => 'lazy',
        'fetchpriority' => '',
    );
    $args = wp_parse_args($args, $defaults);

    $attachment_id = attachment_url_to_postid($url);

    if ($attachment_id) {
        $extra_attr = array(
            'class'   => $args['class'],
            'loading' => $args['loading'],
        );
        if ($args['alt'] !== '') {
            $extra_attr['alt'] = $args['alt'];
        }
        if ($args['fetchpriority']) {
            $extra_attr['fetchpriority'] = $args['fetchpriority'];
        }
        $html = wp_get_attachment_image($attachment_id, $args['size'], false, $extra_attr);
        if ($html) {
            return $html;
        }
    }

    // フォールバック（添付IDが見つからない場合）
    $attrs = sprintf(
        'src="%s" alt="%s" class="%s" loading="%s" decoding="async"',
        esc_url($url),
        esc_attr($args['alt']),
        esc_attr($args['class']),
        esc_attr($args['loading'])
    );
    if ($args['fetchpriority']) {
        $attrs .= ' fetchpriority="' . esc_attr($args['fetchpriority']) . '"';
    }
    return '<img ' . $attrs . '>';
}

/**
 * Contact Form 7（およびreCAPTCHA）は、フォームを実際に含まないページでも
 * プラグインの初期設定によりサイト全体で読み込まれてしまう。
 * reCAPTCHAスクリプトはメインスレッドを1秒以上占有する重い処理のため、
 * 実際に[contact-form-7]が使われているページ以外では読み込みを停止する。
 */
function riff_conditionally_dequeue_cf7_assets() {
    if (is_admin()) return;

    $post = get_post();
    $has_cf7 = false;
    if ($post instanceof WP_Post) {
        if (has_shortcode($post->post_content, 'contact-form-7')) {
            $has_cf7 = true;
        } elseif (function_exists('has_block') && has_block('contact-form-7/contact-form-selector', $post)) {
            $has_cf7 = true;
        }
    }

    if ($has_cf7) {
        return;
    }

    wp_dequeue_script('google-recaptcha');
    wp_dequeue_script('wpcf7-recaptcha');
    wp_dequeue_script('contact-form-7');
    wp_dequeue_script('swv');
    wp_dequeue_style('contact-form-7');
}
add_action('wp_enqueue_scripts', 'riff_conditionally_dequeue_cf7_assets', 20);

// SEO・AEO強化：構造化データ追加
$_riff_seo_schema_path = get_template_directory() . '/inc/seo-schema.php';
if ( file_exists( $_riff_seo_schema_path ) ) {
    require_once $_riff_seo_schema_path;
}
unset( $_riff_seo_schema_path );

// 管理画面：統合タブナビゲーション + SEO設定ページ
$_riff_admin_tab_path = get_template_directory() . '/inc/admin-tab-nav.php';
if ( file_exists( $_riff_admin_tab_path ) ) {
    require_once $_riff_admin_tab_path;
}
unset( $_riff_admin_tab_path );

// テーマ切り替え時にリライトルールを再生成
add_action( 'after_switch_theme', 'flush_rewrite_rules' );

// テーマ更新後の1回限りリライトルール再生成（パーマリンク問題の自動修正）
// 優先度9999で実行し、すべてのルール登録後に正確なルールセットを保存する
add_action( 'init', function() {
    if ( get_option( 'riff_flush_version' ) !== '2.2' ) {
        flush_rewrite_rules();
        update_option( 'riff_flush_version', '2.2' );
    }
}, 9999 );

// スタイルとスクリプトの読み込み
function riff_scripts() {
    wp_enqueue_style('riff-style', get_stylesheet_uri(), array(), '2.0.29');
    // Google Fontsはheader.phpでpreload+swap方式で読み込み済みのため、ここでの重複読み込みは行わない

    wp_enqueue_script('riff-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '2.0.8', true);

    // Swiper.js: テーマ内に.swiper要素の使用箇所がないため読み込みを停止（未使用CDNリクエストの解消）
}
add_action('wp_enqueue_scripts', 'riff_scripts');

/**
 * 全ページ共通で読み込まれる小さなCSS（responsive-breaks.css / news-blog-sections.css /
 * fade-animations.css）は、それぞれ個別にリクエストするとモバイル回線でのHTTPオーバーヘッドが
 * 積み重なるため、1ファイルに結合して配信する（内容・読み込み順序は変更していない）。
 */
function riff_enqueue_common_css_bundle() {
    wp_enqueue_style(
        'riff-common-bundle',
        get_template_directory_uri() . '/assets/css/riff-common-bundle.css',
        array('riff-style'),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'riff_enqueue_common_css_bundle');

/**
 * フロントページ限定のCSS（blog-section-enhanced.css / blog-slider.css）も
 * 同様に1ファイルへ統合して配信する（内容・読み込み順序は変更していない）。
 */
function riff_enqueue_frontpage_css_bundle() {
    if (!is_front_page()) {
        return;
    }
    wp_enqueue_style(
        'riff-frontpage-bundle',
        get_template_directory_uri() . '/assets/css/riff-frontpage-bundle.css',
        array('riff-style'),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'riff_enqueue_frontpage_css_bundle');

// jQuery（WordPressコアが<head>で同期読み込みするデフォルト設定）をフッターへ移動し、
// レンダリングブロッキングを解消する
function riff_move_jquery_to_footer() {
    if (is_admin()) return;
    wp_scripts()->add_data('jquery', 'group', 1);
    wp_scripts()->add_data('jquery-core', 'group', 1);
    wp_scripts()->add_data('jquery-migrate', 'group', 1);
}
add_action('wp_enqueue_scripts', 'riff_move_jquery_to_footer', 100);

/**
 * jquery-migrateの除去（フロントエンドのみ）
 *
 * 事前調査済み: テーマ側で読み込まれるフロントエンドJS
 * （main.js, loading-screen.js, blog-slider.js, blog-filter-enhanced.js,
 *   news-blog-sections.js, single-post.js, fade-animations.js）および
 * フロントエンドで現在読み込まれているプラグイン由来のJS（Google Site Kitの
 * イベント連携スクリプト、gtag.js）はいずれも jquery-migrate が補完する
 * 非推奨jQuery API（.live/.die/.bind/.unbind/.delegate/.undelegate/.size()/
 * $.browser/.andSelf 等）を使用していないことを確認済み。
 * 管理画面（admin-bar含む）には影響しないよう is_admin() のみ除外。
 */
function riff_remove_jquery_migrate($scripts) {
    if (is_admin() || empty($scripts->registered['jquery'])) {
        return;
    }
    $jquery = $scripts->registered['jquery'];
    $jquery->deps = array_diff($jquery->deps, array('jquery-migrate'));
}
add_action('wp_default_scripts', 'riff_remove_jquery_migrate');


// 管理画面用スタイル
function riff_admin_scripts() {
    wp_enqueue_style('riff-admin-style', get_template_directory_uri() . '/admin/admin-style.css', array(), '2.0.0');
    wp_enqueue_script('riff-admin-script', get_template_directory_uri() . '/admin/admin-script.js', array('jquery'), '2.0.0', true);
}
add_action('admin_enqueue_scripts', 'riff_admin_scripts');

// メディアアップローダー機能を追加
function riff_media_uploader_enqueue() {
    wp_enqueue_media();
    wp_enqueue_script('riff-media-uploader', get_template_directory_uri() . '/admin/media-uploader.js', array('jquery'), '2.0.0', true);
}
add_action('admin_enqueue_scripts', 'riff_media_uploader_enqueue');

// RIFFテーマ管理画面を追加
function riff_admin_menu() {
    add_menu_page(
        'RIFFテーマ設定',
        'RIFFテーマ',
        'manage_options',
        'riff-theme-settings',
        'riff_admin_page',
        'dashicons-admin-customizer',
        30
    );
    
    add_submenu_page(
        'riff-theme-settings',
        'ヘッダー設定',
        'ヘッダー設定',
        'manage_options',
        'riff-header-settings',
        'riff_header_admin_page'
    );
    
    add_submenu_page(
        'riff-theme-settings',
        'ヒーロー設定',
        'ヒーロー設定',
        'manage_options',
        'riff-hero-settings',
        'riff_hero_admin_page'
    );

    add_submenu_page(
        'riff-theme-settings',
        'SEO設定',
        'SEO設定',
        'manage_options',
        'riff-seo-settings',
        'riff_seo_settings_page_content'
    );
}
add_action('admin_menu', 'riff_admin_menu');

// メイン設定ページ
function riff_admin_page() {
    if (isset($_POST['submit'])) {
        // nonceチェック
        if (!isset($_POST['riff_nonce']) || !wp_verify_nonce($_POST['riff_nonce'], 'riff_admin_page')) {
            wp_die('セキュリティチェックに失敗しました。');
        }
        
        update_option('riff_site_title', sanitize_text_field($_POST['site_title']));
        update_option('riff_site_description', sanitize_textarea_field($_POST['site_description']));
        update_option('riff_primary_color', sanitize_hex_color($_POST['primary_color']));
        update_option('riff_secondary_color', sanitize_hex_color($_POST['secondary_color']));
        echo '<div class="notice notice-success"><p>設定を保存しました。</p></div>';
    }
    
    $site_title = get_option('riff_site_title', get_bloginfo('name'));
    $site_description = get_option('riff_site_description', get_bloginfo('description'));
    $primary_color = get_option('riff_primary_color', '#007cba');
    $secondary_color = get_option('riff_secondary_color', '#50575e');
    ?>
    <div class="wrap">
        <h1>RIFFテーマ 基本設定</h1>
        <form method="post" action="">
            <?php wp_nonce_field('riff_admin_page', 'riff_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">サイトタイトル</th>
                    <td><input type="text" name="site_title" value="<?php echo esc_attr($site_title); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">サイト説明</th>
                    <td><textarea name="site_description" rows="3" cols="50"><?php echo esc_textarea($site_description); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">プライマリーカラー</th>
                    <td><input type="color" name="primary_color" value="<?php echo esc_attr($primary_color); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">セカンダリーカラー</th>
                    <td><input type="color" name="secondary_color" value="<?php echo esc_attr($secondary_color); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// ヘッダー設定ページ（改良版・統合版・メガメニュー対応）
function riff_header_admin_page() {
    if (isset($_POST['submit'])) {
        // nonceチェック
        if (!isset($_POST['riff_header_nonce']) || !wp_verify_nonce($_POST['riff_header_nonce'], 'riff_header_admin_page')) {
            wp_die('セキュリティチェックに失敗しました。');
        }
        
        // 既存の設定
        update_option('riff_logo_url', esc_url_raw($_POST['logo_url']));
        update_option('riff_logo_width', intval($_POST['logo_width']));
        update_option('riff_logo_height', intval($_POST['logo_height']));
        update_option('riff_header_phone', sanitize_text_field($_POST['header_phone']));
        update_option('riff_header_email', sanitize_email($_POST['header_email']));
        update_option('riff_header_cta_text', sanitize_text_field($_POST['header_cta_text']));
        update_option('riff_header_cta_url', esc_url_raw($_POST['header_cta_url']));
        update_option('riff_header_style', sanitize_text_field($_POST['header_style']));
        update_option('riff_header_bg_color', sanitize_hex_color($_POST['header_bg_color']));
        update_option('riff_header_text_color', sanitize_hex_color($_POST['header_text_color']));
		update_option('riff_header_hover_color', sanitize_hex_color($_POST['header_hover_color']));
		// 新しいヘッダー色設定を追加
		update_option('riff_header_link_hover_color', sanitize_hex_color($_POST['header_link_hover_color']));
		update_option('riff_header_scrolled_bg_color', sanitize_hex_color($_POST['header_scrolled_bg_color']));
		update_option('riff_header_scrolled_text_color', sanitize_hex_color($_POST['header_scrolled_text_color']));
		update_option('riff_header_hover_color', sanitize_hex_color($_POST['header_hover_color']));
        
        // メガメニュー設定
        update_option('riff_megamenu_enabled', isset($_POST['megamenu_enabled']) ? 1 : 0);
        update_option('riff_megamenu_post_count', intval($_POST['megamenu_post_count']));
        update_option('riff_megamenu_service_count', intval($_POST['megamenu_service_count']));
        
        echo '<div class="notice notice-success"><p>ヘッダー設定を保存しました。</p></div>';
    }
    
    // 既存の変数取得
    $logo_url = get_option('riff_logo_url', '');
    $logo_width = get_option('riff_logo_width', 200);
    $logo_height = get_option('riff_logo_height', 50);
	// Footer-Up設定を取得
    $footer_up_bg_color = get_option('riff_footer_up_bg_color', '#f8f9fa');
    $footer_up_text_color = get_option('riff_footer_up_text_color', '#333333');
    $footer_up_btn1_color = get_option('riff_footer_up_btn1_color', '#ff6b35');
    $footer_up_btn2_color = get_option('riff_footer_up_btn2_color', '#007cba');
    $header_phone = get_option('riff_header_phone', '');
    $header_email = get_option('riff_header_email', '');
    $header_cta_text = get_option('riff_header_cta_text', 'お問い合わせ');
    $header_cta_url = get_option('riff_header_cta_url', '');
    $header_style = get_option('riff_header_style', 'transparent');
    $header_bg_color = get_option('riff_header_bg_color', '#ffffff');
    $header_text_color = get_option('riff_header_text_color', '#333333');
	$header_hover_color = get_option('riff_header_hover_color', '#ff6b35');
	// 新しいヘッダー色設定の変数を追加
	$header_link_hover_color = get_option('riff_header_link_hover_color', '#ff6b35');
	$header_scrolled_bg_color = get_option('riff_header_scrolled_bg_color', '#ffffff');
	$header_scrolled_text_color = get_option('riff_header_scrolled_text_color', '#333333');
	$header_hover_color = get_option('riff_header_hover_color', '#ff6b35');
    
    // メガメニュー設定
    $megamenu_enabled = get_option('riff_megamenu_enabled', 0);
    $megamenu_post_count = get_option('riff_megamenu_post_count', 6);
    $megamenu_service_count = get_option('riff_megamenu_service_count', 3);
    ?>
    <div class="wrap">
        <h1>ヘッダー設定</h1>
        <form method="post" action="">
            <?php wp_nonce_field('riff_header_admin_page', 'riff_header_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">ヘッダースタイル</th>
                    <td>
                        <select name="header_style">
                            <option value="transparent" <?php selected($header_style, 'transparent'); ?>>透明（スクロール時に背景色）</option>
                            <option value="solid" <?php selected($header_style, 'solid'); ?>>常に背景色</option>
                            <option value="minimal" <?php selected($header_style, 'minimal'); ?>>ミニマル</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">ヘッダー背景色</th>
                    <td><input type="color" name="header_bg_color" value="<?php echo esc_attr($header_bg_color); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">ヘッダーテキスト色</th>
                    <td><input type="color" name="header_text_color" value="<?php echo esc_attr($header_text_color); ?>" /></td>
                </tr>
				<tr>
                    <th scope="row">ヘッダーホバー時テキスト色</th>
                    <td><input type="color" name="header_hover_color" value="<?php echo esc_attr($header_hover_color); ?>" /></td>
                </tr>
				<tr>
					<th scope="row">リンクホバー色</th>
					<td><input type="color" name="header_link_hover_color" value="<?php echo esc_attr($header_link_hover_color); ?>" /></td>
				</tr>
				<tr>
					<th scope="row">スクロール時背景色</th>
					<td><input type="color" name="header_scrolled_bg_color" value="<?php echo esc_attr($header_scrolled_bg_color); ?>" /></td>
				</tr>
				<tr>
					<th scope="row">スクロール時テキスト色</th>
					<td><input type="color" name="header_scrolled_text_color" value="<?php echo esc_attr($header_scrolled_text_color); ?>" /></td>
				</tr>
				<tr>
                    <th scope="row">ヘッダーホバー時テキスト色</th>
                    <td><input type="color" name="header_hover_color" value="<?php echo esc_attr($header_hover_color); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">ロゴ画像URL</th>
                    <td>
                        <input type="url" name="logo_url" id="logo_url" value="<?php echo esc_attr($logo_url); ?>" class="regular-text" />
                        <button type="button" class="button" id="upload_logo_button">画像を選択</button>
                        <?php if ($logo_url): ?>
                            <div class="logo-preview" style="margin-top: 10px;">
                                <img src="<?php echo esc_url($logo_url); ?>" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 10px;" />
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">ロゴ幅（px）</th>
                    <td><input type="number" name="logo_width" value="<?php echo esc_attr($logo_width); ?>" min="50" max="500" /></td>
                </tr>
                <tr>
                    <th scope="row">ロゴ高さ（px）</th>
                    <td><input type="number" name="logo_height" value="<?php echo esc_attr($logo_height); ?>" min="30" max="200" /></td>
                </tr>
                <tr>
                    <th scope="row">電話番号</th>
                    <td><input type="text" name="header_phone" value="<?php echo esc_attr($header_phone); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">メールアドレス</th>
                    <td><input type="email" name="header_email" value="<?php echo esc_attr($header_email); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">CTAボタンテキスト</th>
                    <td><input type="text" name="header_cta_text" value="<?php echo esc_attr($header_cta_text); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">CTAボタンURL</th>
                    <td><input type="url" name="header_cta_url" value="<?php echo esc_attr($header_cta_url); ?>" class="regular-text" /></td>
                </tr>
                
                <!-- メガメニュー設定セクション -->
                <tr>
                    <th colspan="2" style="background: #f0f8ff; padding: 15px; font-size: 16px; color: #007cba;">
                        <strong>メガメニュー設定</strong>
                    </th>
                </tr>
                <tr>
                    <th scope="row">メガメニュー機能</th>
                    <td>
                        <label>
                            <input type="checkbox" name="megamenu_enabled" value="1" <?php checked($megamenu_enabled, 1); ?> />
                            メガメニューを有効にする
                        </label>
                        <p class="description">チェックを入れると、メニューアイテムにメガメニューオプションが表示されます。</p>
                    </td>
                </tr>
                <tr class="megamenu-dependent" style="<?php echo $megamenu_enabled ? '' : 'display:none;'; ?>">
                    <th scope="row">表示記事数（ニュースタイプ）</th>
                    <td>
                        <input type="number" name="megamenu_post_count" value="<?php echo esc_attr($megamenu_post_count); ?>" min="3" max="12" />
                        <p class="description">ニュース/ブログタイプのメガメニューで表示する記事数を設定します。</p>
                    </td>
                </tr>
                <tr class="megamenu-dependent" style="<?php echo $megamenu_enabled ? '' : 'display:none;'; ?>">
                    <th scope="row">表示サービス数</th>
                    <td>
                        <input type="number" name="megamenu_service_count" value="<?php echo esc_attr($megamenu_service_count); ?>" min="2" max="6" />
                        <p class="description">サービスタイプのメガメニューで表示するアイテム数を設定します。</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <script>
    jQuery(document).ready(function($) {
        // ロゴアップローダー
        $('#upload_logo_button').click(function(e) {
            e.preventDefault();
            var image = wp.media({
                title: 'ロゴ画像を選択',
                multiple: false,
                library: {
                    type: 'image'
                }
            }).open().on('select', function(e) {
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#logo_url').val(image_url);
                
                // プレビューを更新
                $('.logo-preview').remove();
                $('<div class="logo-preview" style="margin-top: 10px;"><img src="' + image_url + '" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 10px;" /></div>').insertAfter('#upload_logo_button');
            });
        });
        
        // メガメニュー設定の表示/非表示
        $('input[name="megamenu_enabled"]').on('change', function() {
            $('.megamenu-dependent').toggle($(this).is(':checked'));
        });
    });
    </script>
    <?php
}

// ヒーロー設定ページ（改良版）
function riff_hero_admin_page() {
    if (isset($_POST['submit'])) {
        // nonceチェック
        if (!isset($_POST['riff_hero_nonce']) || !wp_verify_nonce($_POST['riff_hero_nonce'], 'riff_hero_admin_page')) {
            wp_die('セキュリティチェックに失敗しました。');
        }
        
        update_option('riff_hero_title', riff_sanitize_textarea_with_br($_POST['hero_title']));
        update_option('riff_hero_subtitle', riff_sanitize_textarea_with_br($_POST['hero_subtitle']));
        update_option('riff_hero_description', riff_sanitize_textarea_with_br($_POST['hero_description']));
        update_option('riff_hero_button_text', sanitize_text_field($_POST['hero_button_text']));
        update_option('riff_hero_button_url', esc_url_raw($_POST['hero_button_url']));
        update_option('riff_hero_bg_image', esc_url_raw($_POST['hero_bg_image']));
        update_option('riff_hero_bg_video', esc_url_raw($_POST['hero_bg_video']));
        update_option('riff_hero_display_type', sanitize_text_field($_POST['hero_display_type']));
        update_option('riff_hero_overlay_opacity', intval($_POST['hero_overlay_opacity']));
        update_option('riff_hero_text_color', sanitize_hex_color($_POST['hero_text_color']));
        update_option('riff_hero_height', sanitize_text_field($_POST['hero_height']));
        echo '<div class="notice notice-success"><p>ヒーロー設定を保存しました。</p></div>';
    }
    
    $hero_title = get_option('riff_hero_title', 'MAKE THE WORLD');
    $hero_subtitle = get_option('riff_hero_subtitle', '人と自然が共生できる新しい世界をつくる');
    $hero_description = get_option('riff_hero_description', '私たちは持続可能な未来のために、革新的なソリューションを提供します。環境と調和した技術で、次世代に美しい地球を残していきます。');
    $hero_button_text = get_option('riff_hero_button_text', '詳しく見る');
    $hero_button_url = get_option('riff_hero_button_url', '#about');
    $hero_bg_image = get_option('riff_hero_bg_image', '');
    $hero_bg_video = get_option('riff_hero_bg_video', '');
    $hero_display_type = get_option('riff_hero_display_type', 'image');
    $hero_overlay_opacity = get_option('riff_hero_overlay_opacity', 50);
    $hero_text_color = get_option('riff_hero_text_color', '#ffffff');
    $hero_height = get_option('riff_hero_height', 'fullscreen');
    ?>
    <div class="wrap">
        <h1>ヒーローセクション設定</h1>
        <form method="post" action="">
            <?php wp_nonce_field('riff_hero_admin_page', 'riff_hero_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">表示タイプ</th>
                    <td>
                        <select name="hero_display_type" id="hero_display_type">
                            <option value="image" <?php selected($hero_display_type, 'image'); ?>>画像</option>
                            <option value="video" <?php selected($hero_display_type, 'video'); ?>>動画</option>
                            <option value="gradient" <?php selected($hero_display_type, 'gradient'); ?>>グラデーション</option>
                        </select>
                    </td>
                </tr>
                <tr class="hero-image-row" style="<?php echo $hero_display_type !== 'image' ? 'display:none;' : ''; ?>">
                    <th scope="row">背景画像URL</th>
                    <td>
                        <input type="url" name="hero_bg_image" value="<?php echo esc_attr($hero_bg_image); ?>" class="regular-text" />
                        <button type="button" class="button" id="upload_hero_image_button">画像を選択</button>
                        <p class="description">推奨サイズ: 1920×1080px</p>
                        <?php if ($hero_bg_image): ?>
                            <div class="hero-image-preview" style="margin-top: 10px;">
                                <img src="<?php echo esc_url($hero_bg_image); ?>" style="max-width: 300px; height: auto; border: 1px solid #ddd;" />
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr class="hero-video-row" style="<?php echo $hero_display_type !== 'video' ? 'display:none;' : ''; ?>">
                    <th scope="row">背景動画URL</th>
                    <td>
                        <input type="url" name="hero_bg_video" value="<?php echo esc_attr($hero_bg_video); ?>" class="regular-text" />
                        <button type="button" class="button" id="upload_hero_video_button">動画を選択</button>
                        <p class="description">MP4形式の動画URLを入力してください</p>
                        <?php if ($hero_bg_video): ?>
                            <div class="hero-video-preview" style="margin-top: 10px;">
                                <video src="<?php echo esc_url($hero_bg_video); ?>" style="max-width: 300px; height: auto; border: 1px solid #ddd;" controls></video>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">ヒーローの高さ</th>
                    <td>
                        <select name="hero_height">
                            <option value="fullscreen" <?php selected($hero_height, 'fullscreen'); ?>>フルスクリーン（100vh）</option>
                            <option value="large" <?php selected($hero_height, 'large'); ?>>大（80vh）</option>
                            <option value="medium" <?php selected($hero_height, 'medium'); ?>>中（60vh）</option>
                            <option value="small" <?php selected($hero_height, 'small'); ?>>小（40vh）</option>
                        </select>
                    </td>
                </tr>
                <tr>
					<th scope="row">メインタイトル</th>
					<td>
						<textarea name="hero_title" rows="2" cols="50" class="large-text"><?php echo esc_textarea($hero_title); ?></textarea>
						<p class="description">改行する場合は &lt;br&gt; タグを入力してください。PC専用改行: &lt;br class="pc"&gt;、SP専用改行: &lt;br class="sp"&gt;</p>
					</td>
				</tr>
				<tr>
					<th scope="row">サブタイトル</th>
					<td>
						<textarea name="hero_subtitle" rows="2" cols="50" class="large-text"><?php echo esc_textarea($hero_subtitle); ?></textarea>
						<p class="description">改行する場合は &lt;br&gt; タグを入力してください。PC専用改行: &lt;br class="pc"&gt;、SP専用改行: &lt;br class="sp"&gt;</p>
					</td>
				</tr>
                <tr>
                    <th scope="row">説明文</th>
                    <td><textarea name="hero_description" rows="4" cols="50" class="large-text"><?php echo esc_textarea($hero_description); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">ボタンテキスト</th>
                    <td><input type="text" name="hero_button_text" value="<?php echo esc_attr($hero_button_text); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">ボタンURL</th>
                    <td><input type="url" name="hero_button_url" value="<?php echo esc_attr($hero_button_url); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">テキストカラー</th>
                    <td><input type="color" name="hero_text_color" value="<?php echo esc_attr($hero_text_color); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">オーバーレイ透明度</th>
                    <td>
                        <input type="range" name="hero_overlay_opacity" value="<?php echo esc_attr($hero_overlay_opacity); ?>" min="0" max="100" />
                        <span id="opacity-value"><?php echo $hero_overlay_opacity; ?>%</span>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <script>
    document.querySelector('input[name="hero_overlay_opacity"]').addEventListener('input', function() {
        document.getElementById('opacity-value').textContent = this.value + '%';
    });
    
    document.getElementById('hero_display_type').addEventListener('change', function() {
        const imageRow = document.querySelector('.hero-image-row');
        const videoRow = document.querySelector('.hero-video-row');
        
        if (this.value === 'image') {
            imageRow.style.display = 'table-row';
            videoRow.style.display = 'none';
        } else if (this.value === 'video') {
            imageRow.style.display = 'none';
            videoRow.style.display = 'table-row';
        } else {
            imageRow.style.display = 'none';
            videoRow.style.display = 'none';
        }
    });
    </script>
    <?php
}

// カスタムCSSを動的に生成（改良版・スクロールヘッダー対応強化）
function riff_custom_css() {
    $primary_color = get_option('riff_primary_color', '#007cba');
    $secondary_color = get_option('riff_secondary_color', '#50575e');
    $hero_overlay_opacity = get_option('riff_hero_overlay_opacity', 50) / 100;
    $hero_text_color = get_option('riff_hero_text_color', '#ffffff');
    $hero_height = get_option('riff_hero_height', 'fullscreen');
    $header_bg_color = get_option('riff_header_bg_color', '#ffffff');
    $header_text_color = get_option('riff_header_text_color', '#333333');
    $header_hover_color = get_option('riff_header_hover_color', '#ff6b35');
    $header_style = get_option('riff_header_style', 'transparent');
    $logo_width = get_option('riff_logo_width', 200);
    $logo_height = get_option('riff_logo_height', 50);
    
    // 新しいヘッダー色設定を取得（デフォルト値を明確に設定）
    $header_link_hover_color = get_option('riff_header_link_hover_color', '#ff6b35');
    $header_scrolled_bg_color = get_option('riff_header_scrolled_bg_color', '#ffffff');
    $header_scrolled_text_color = get_option('riff_header_scrolled_text_color', '#333333');
    
    // ヒーローの高さを計算
    $height_map = [
        'fullscreen' => '100vh',
        'large' => '80vh',
        'medium' => '60vh',
        'small' => '40vh'
    ];
    $hero_height_value = isset($height_map[$hero_height]) ? $height_map[$hero_height] : '100vh';
    
    ?>
    <style type="text/css">
        :root {
            --primary-color: <?php echo esc_attr($primary_color); ?>;
            --secondary-color: <?php echo esc_attr($secondary_color); ?>;
            --hero-overlay-opacity: <?php echo esc_attr($hero_overlay_opacity); ?>;
            --hero-text-color: <?php echo esc_attr($hero_text_color); ?>;
            --hero-height: <?php echo esc_attr($hero_height_value); ?>;
            --header-bg-color: <?php echo esc_attr($header_bg_color); ?>;
            --header-text-color: <?php echo esc_attr($header_text_color); ?>;
            --header-hover-color: <?php echo esc_attr($header_hover_color); ?>;
            --logo-width: <?php echo esc_attr($logo_width); ?>px;
            --logo-height: <?php echo esc_attr($logo_height); ?>px;
			--footer-up-bg-color: <?php echo esc_attr($footer_up_bg_color); ?>;
            --footer-up-text-color: <?php echo esc_attr($footer_up_text_color); ?>;
            --footer-up-btn-primary-color: <?php echo esc_attr($footer_up_btn1_color); ?>;
            --footer-up-btn-secondary-color: <?php echo esc_attr($footer_up_btn2_color); ?>;
            /* スクロール時の色設定を明確に定義 */
            --header-link-hover-color: <?php echo esc_attr($header_link_hover_color); ?>;
            --header-scrolled-bg-color: <?php echo esc_attr($header_scrolled_bg_color); ?>;
            --header-scrolled-text-color: <?php echo esc_attr($header_scrolled_text_color); ?>;
        }
        
        /* ヘッダーのスクロール時スタイル強化 */
        .header {
            transition: all 0.3s ease;
        }

        /* 透明ヘッダーのデフォルト状態 */
        .header-transparent {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* 透明ヘッダーのスクロール時 */
        .header-transparent.scrolled {
            background: var(--header-scrolled-bg-color) !important;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* 透明ヘッダーのテキスト色（デフォルト状態） */
        .header-transparent .nav a,
        .header-transparent .logo-text {
            color: var(--hero-text-color) !important;
            transition: color 0.3s ease;
        }

        /* 透明ヘッダーのテキスト色（スクロール時） */
        .header-transparent.scrolled .nav a,
        .header-transparent.scrolled .logo-text {
            color: var(--header-scrolled-text-color) !important;
        }

        /* ホバー時の色（全ヘッダータイプ共通） */
        .header .nav a:hover,
        .header.scrolled .nav a:hover {
            color: var(--header-link-hover-color) !important;
        }

        /* ソリッドヘッダーの場合 */
        .header-solid {
            background: var(--header-bg-color) !important;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
        }

        .header-solid .nav a,
        .header-solid .logo-text {
            color: var(--header-text-color) !important;
        }

        /* ミニマルヘッダーの場合 */
        .header-minimal {
            background: transparent !important;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
        }

        .header-minimal.scrolled {
            background: var(--header-scrolled-bg-color) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-minimal .nav a,
        .header-minimal .logo-text {
            color: var(--hero-text-color) !important;
            transition: color 0.3s ease;
        }

        .header-minimal.scrolled .nav a,
        .header-minimal.scrolled .logo-text {
            color: var(--header-scrolled-text-color) !important;
        }

        /* スクロール時のハンバーガーメニュー色変更 */
        .header-transparent .hamburger-line,
        .header-minimal .hamburger-line {
            background: var(--hero-text-color) !important;
            transition: background 0.3s ease;
        }

        .header-transparent.scrolled .hamburger-line,
        .header-minimal.scrolled .hamburger-line {
            background: var(--header-scrolled-text-color) !important;
        }

        .header-solid .hamburger-line {
            background: var(--header-text-color) !important;
        }

        /* ロゴ画像のスタイル */
        .logo img,
        .logo-image {
            width: var(--logo-width);
            height: var(--logo-height);
            object-fit: contain;
        }
        
        .hero {
            height: var(--hero-height);
            min-height: 500px;
        }
        
        .hero-content {
            color: var(--hero-text-color);
        }

        /* デバッグ用（本番では削除） */
        .header.debug-scrolled {
            background: red !important;
        }
    </style>
    <?php
}
add_action('wp_head', 'riff_custom_css');

// SEO用のメタタグを追加
// viewport/charsetはheader.phpの<head>先頭で既に出力済み、descriptionはAll in One SEOが出力するため、
// ここでの重複出力は行わない（wp_head内での二重出力を回避）
function riff_seo_meta() {
}
// add_action('wp_head', 'riff_seo_meta', 1);

// ウィジェットエリアを登録
function riff_widgets_init() {
    register_sidebar(array(
        'name'          => 'フッターウィジェット',
        'id'            => 'footer-widget',
        'description'   => 'フッターに表示されるウィジェットエリアです。',
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'riff_widgets_init');

// テーマのカスタマイザー設定を無効化
function riff_remove_customizer() {
    global $wp_customize;
    if (isset($wp_customize)) {
        $wp_customize->remove_section('title_tagline');
        $wp_customize->remove_section('colors');
        $wp_customize->remove_section('header_image');
        $wp_customize->remove_section('background_image');
    }
}
add_action('customize_register', 'riff_remove_customizer', 20);

// セキュリティ強化
function riff_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
    }
}
add_action('send_headers', 'riff_security_headers');

// パフォーマンス最適化
function riff_optimize_performance() {
    // 絵文字スクリプトを削除
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    
    // 不要なジェネレーターメタタグを削除
    remove_action('wp_head', 'wp_generator');
    
    // Really Simple Discovery リンクを削除
    remove_action('wp_head', 'rsd_link');
    
    // Windows Live Writer 用マニフェストリンクを削除
    remove_action('wp_head', 'wlwmanifest_link');
}
add_action('init', 'riff_optimize_performance');

// 管理バーのカスタマイズ
function riff_customize_admin_bar() {
    if (is_admin_bar_showing()) {
        echo '<style>
        body.admin-bar .header {
            top: 32px;
        }
        @media screen and (max-width: 782px) {
            body.admin-bar .header {
                top: 46px;
            }
        }
        </style>';
    }
}
add_action('wp_head', 'riff_customize_admin_bar');

// ファイルアップロード制限の追加
function riff_upload_mimes($mimes) {
    // 動画ファイルを許可
    $mimes['mp4'] = 'video/mp4';
    $mimes['webm'] = 'video/webm';
    
    // SVGファイルを許可（セキュリティに注意）
    $mimes['svg'] = 'image/svg+xml';
    
    return $mimes;
}
add_filter('upload_mimes', 'riff_upload_mimes');

// エラーログ用のデバッグ関数
function riff_debug_log($message) {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
}

// テーマのアクティベーション時の処理
function riff_theme_activation() {
    // デフォルト値を設定
    if (!get_option('riff_hero_title')) {
        update_option('riff_hero_title', 'MAKE THE WORLD');
    }
    if (!get_option('riff_hero_subtitle')) {
        update_option('riff_hero_subtitle', '人と自然が共生できる新しい世界をつくる');
    }
    if (!get_option('riff_primary_color')) {
        update_option('riff_primary_color', '#007cba');
    }
    
    // 基本的なページを作成（必要に応じて）
    $pages_to_create = array(
        'about' => array(
            'title' => '会社概要',
            'content' => '会社概要ページの内容をここに入力してください。'
        ),
        'services' => array(
            'title' => 'サービス',
            'content' => 'サービスページの内容をここに入力してください。'
        ),
        'contact' => array(
            'title' => 'お問い合わせ',
            'content' => 'お問い合わせページの内容をここに入力してください。'
        )
    );
    
    foreach ($pages_to_create as $slug => $page_data) {
        $page = get_page_by_path($slug);
        if (!$page) {
            wp_insert_post(array(
                'post_title' => $page_data['title'],
                'post_content' => $page_data['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => $slug
            ));
        }
    }
    
    // パーマリンク構造を更新
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'riff_theme_activation');

// ================================
// メガメニュー機能の追加（最終安全版）
// ================================

// 管理画面でのカスタムウォーカーは遅延読み込み
function riff_load_megamenu_admin_walker() {
    if (class_exists('Walker_Nav_Menu_Edit')) {
        class Riff_Megamenu_Walker extends Walker_Nav_Menu_Edit {
            function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
                $indent = ($depth) ? str_repeat("\t", $depth) : '';
                
                ob_start();
                $item_id = esc_attr($item->ID);
                $item_url = esc_attr($item->url);
                $item_title = esc_attr($item->title);
                $item_target = esc_attr($item->target);
                $item_attr_title = esc_attr($item->attr_title);
                $item_classes = esc_attr(implode(' ', $item->classes));
                $item_xfn = esc_attr($item->xfn);
                
                // メガメニュータイプの取得
                $megamenu_type = get_post_meta($item_id, '_riff_megamenu_type', true);
                ?>
                <li id="menu-item-<?php echo $item_id; ?>" class="menu-item-depth-<?php echo $depth; ?>">
                    <div class="menu-item-bar">
                        <div class="menu-item-handle">
                            <span class="item-title"><span class="menu-item-title"><?php echo $item_title; ?></span></span>
                            <span class="item-controls">
                                <span class="item-type"><?php echo esc_html($item->type_label); ?></span>
                                <a class="item-edit" id="edit-<?php echo $item_id; ?>" href="#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Edit Menu Item'); ?></a>
                            </span>
                        </div>
                    </div>

                    <div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
                        <p class="field-url description description-wide">
                            <label for="edit-menu-item-url-<?php echo $item_id; ?>">URL</label>
                            <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo $item_url; ?>" />
                        </p>
                        <p class="field-title description description-wide">
                            <label for="edit-menu-item-title-<?php echo $item_id; ?>">ナビゲーションラベル</label>
                            <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo $item_title; ?>" />
                        </p>
                        <p class="field-attr-title description description-wide">
                            <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">タイトル属性</label>
                            <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo $item_attr_title; ?>" />
                        </p>
                        <p class="field-link-target description">
                            <label for="edit-menu-item-target-<?php echo $item_id; ?>">
                                <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" class="edit-menu-item-target" name="menu-item-target[<?php echo $item_id; ?>]" value="_blank" <?php checked($item_target, '_blank'); ?> />
                                リンクを新しいタブ/ウィンドウで開く
                            </label>
                        </p>
                        <p class="field-css-classes description description-thin">
                            <label for="edit-menu-item-classes-<?php echo $item_id; ?>">CSSクラス (オプション)</label>
                            <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo $item_classes; ?>" />
                        </p>
                        <p class="field-xfn description description-thin">
                            <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">リンク関係 (XFN)</label>
                            <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo $item_xfn; ?>" />
                        </p>
                        
                        <!-- メガメニューカスタムフィールド -->
                        <?php if (get_option('riff_megamenu_enabled', 0) && $depth === 0): ?>
                        <p class="field-megamenu-type description description-wide" style="border-top: 1px solid #ddd; padding-top: 15px; margin-top: 15px;">
                            <label for="edit-menu-item-megamenu-type-<?php echo $item_id; ?>" style="font-weight: bold; color: #007cba;">メガメニュータイプ</label>
                            <select id="edit-menu-item-megamenu-type-<?php echo $item_id; ?>" class="widefat edit-menu-item-megamenu-type" name="menu-item-megamenu-type[<?php echo $item_id; ?>]">
                                <option value="" <?php selected($megamenu_type, ''); ?>>メガメニューを使用しない</option>
                                <option value="news" <?php selected($megamenu_type, 'news'); ?>>ニュース/ブログ表示</option>
                                <option value="services" <?php selected($megamenu_type, 'services'); ?>>サービス一覧表示</option>
                                <option value="pages" <?php selected($megamenu_type, 'pages'); ?>>ページ一覧表示</option>
                            </select>
                            <span class="description">このメニューアイテムのメガメニュータイプを選択してください。</span>
                        </p>
                        <?php endif; ?>
                        
                        <div class="menu-item-actions description-wide submitbox">
                            <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="#">削除</a>
                            <span class="meta-sep hide-if-no-js"> | </span>
                            <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>" href="#menu-item-settings-<?php echo $item_id; ?>">キャンセル</a>
                        </div>

                        <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
                        <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->object_id); ?>" />
                        <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->object); ?>" />
                        <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->menu_item_parent); ?>" />
                        <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->menu_order); ?>" />
                        <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr($item->type); ?>" />
                    </div>
                </li>
                <?php
                $output .= ob_get_clean();
            }
        }
    }
}

// メニュー管理画面でカスタムウォーカーを使用
function riff_megamenu_edit_walker($walker, $menu_id) {
    // クラスが定義されているかチェック
    if (class_exists('Riff_Megamenu_Walker')) {
        return 'Riff_Megamenu_Walker';
    }
    return $walker;
}

// メガメニュー機能の初期化
function riff_init_megamenu() {
    if (get_option('riff_megamenu_enabled', 0)) {
        // 管理画面でのみウォーカーを読み込み
        if (is_admin()) {
            riff_load_megamenu_admin_walker();
            add_filter('wp_edit_nav_menu_walker', 'riff_megamenu_edit_walker', 10, 2);
        }
        add_action('wp_update_nav_menu_item', 'riff_update_nav_menu_item_megamenu', 10, 3);
    }
}
add_action('admin_init', 'riff_init_megamenu');

// メニューアイテム保存時にカスタムフィールドを保存
function riff_update_nav_menu_item_megamenu($menu_id, $menu_item_db_id, $args) {
    if (isset($_POST['menu-item-megamenu-type'][$menu_item_db_id])) {
        $megamenu_type = sanitize_text_field($_POST['menu-item-megamenu-type'][$menu_item_db_id]);
        update_post_meta($menu_item_db_id, '_riff_megamenu_type', $megamenu_type);
    }
}

// メガメニューコンテンツを取得する関数
function riff_get_megamenu_content($type) {
    if (!get_option('riff_megamenu_enabled', 0)) {
        return '';
    }
    
    ob_start();
    
    switch ($type) {
        case 'news':
            riff_render_news_megamenu();
            break;
        case 'services':
            riff_render_services_megamenu();
            break;
        case 'pages':
            riff_render_pages_megamenu();
            break;
    }
    
    return ob_get_clean();
}

// ニュース/ブログタイプのメガメニュー
function riff_render_news_megamenu() {
    $post_count = get_option('riff_megamenu_post_count', 6);
    $posts = get_posts(array(
        'numberposts' => $post_count,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    ?>
    <div class="megamenu-news">
        <div class="megamenu-header">
            <h3>最新ニュース</h3>
            <a href="<?php echo esc_url(home_url('/news/')); ?>" class="megamenu-view-all">すべて見る</a>
        </div>
        <div class="megamenu-posts">
            <?php foreach ($posts as $post): setup_postdata($post); ?>
            <article class="megamenu-post-item">
                <?php if (has_post_thumbnail($post->ID)): ?>
                <div class="megamenu-post-image">
                    <a href="<?php echo get_permalink($post->ID); ?>">
                        <?php echo get_the_post_thumbnail($post->ID, 'thumbnail'); ?>
                    </a>
                </div>
                <?php endif; ?>
                <div class="megamenu-post-content">
                    <h4 class="megamenu-post-title">
                        <a href="<?php echo get_permalink($post->ID); ?>"><?php echo get_the_title($post->ID); ?></a>
                    </h4>
                    <time class="megamenu-post-date"><?php echo get_the_date('Y.m.d', $post->ID); ?></time>
                    <p class="megamenu-post-excerpt"><?php echo wp_trim_words(get_the_excerpt($post->ID), 15, '...'); ?></p>
                </div>
            </article>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
    </div>
    <?php
}

// サービスタイプのメガメニュー
function riff_render_services_megamenu() {
    $service_count = get_option('riff_megamenu_service_count', 3);
    ?>
    <div class="megamenu-services">
        <div class="megamenu-header">
            <h3>サービス一覧</h3>
            <a href="<?php echo esc_url(home_url('/services/')); ?>" class="megamenu-view-all">詳しく見る</a>
        </div>
        <div class="megamenu-service-grid">
            <div class="megamenu-service-item">
                <div class="megamenu-service-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
                <h4>再生可能エネルギー</h4>
                <p>太陽光、風力、地熱などの再生可能エネルギーを活用したシステム設計・導入を行います。</p>
                <a href="<?php echo esc_url(home_url('/services/renewable-energy/')); ?>" class="megamenu-service-link">詳細を見る</a>
            </div>
            <div class="megamenu-service-item">
                <div class="megamenu-service-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66c.84-2.43 2.47-6.11 6.29-9l1.09 1.21C7.63 17.27 6.3 20.61 5.67 22.56l1.89.66c.84-2.43 2.47-6.11 6.29-9L12 12z"/>
                    </svg>
                </div>
                <h4>環境コンサルティング</h4>
                <p>企業の環境負荷削減と持続可能な経営戦略の策定をサポートします。</p>
                <a href="<?php echo esc_url(home_url('/services/consulting/')); ?>" class="megamenu-service-link">詳細を見る</a>
            </div>
            <div class="megamenu-service-item">
                <div class="megamenu-service-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <h4>循環型システム</h4>
                <p>廃棄物ゼロを目指した循環型社会システムの構築を提案・実装します。</p>
                <a href="<?php echo esc_url(home_url('/services/circular-system/')); ?>" class="megamenu-service-link">詳細を見る</a>
            </div>
        </div>
    </div>
    <?php
}

// ページタイプのメガメニュー
function riff_render_pages_megamenu() {
    $pages = get_pages(array(
        'sort_column' => 'menu_order',
        'sort_order' => 'ASC',
        'number' => 6,
        'parent' => 0
    ));
    ?>
    <div class="megamenu-pages">
        <div class="megamenu-header">
            <h3>主要ページ</h3>
        </div>
        <div class="megamenu-page-grid">
            <?php foreach ($pages as $page): ?>
            <div class="megamenu-page-item">
                <?php if (has_post_thumbnail($page->ID)): ?>
                <div class="megamenu-page-image">
                    <a href="<?php echo get_permalink($page->ID); ?>">
                        <?php echo get_the_post_thumbnail($page->ID, 'medium'); ?>
                    </a>
                </div>
                <?php endif; ?>
                <div class="megamenu-page-content">
                    <h4>
                        <a href="<?php echo get_permalink($page->ID); ?>"><?php echo get_the_title($page->ID); ?></a>
                    </h4>
                    <p><?php echo wp_trim_words(get_the_excerpt($page->ID), 20, '...'); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

// ================================
// メガメニュー対応ナビゲーションウォーカー（フロントエンド用）
// ================================

class Riff_Megamenu_Nav_Walker extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"dropdown-menu\">\n";
    }

    function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        // メガメニュータイプを取得
        $megamenu_type = get_post_meta($item->ID, '_riff_megamenu_type', true);
        $has_megamenu = !empty($megamenu_type) && get_option('riff_megamenu_enabled', 0);
        
        // ドロップダウンメニューがあるかチェック
        $has_children = in_array('menu-item-has-children', $classes);
        
        if ($has_children) {
            $classes[] = 'has-dropdown';
        }
        
        if ($has_megamenu && $depth === 0) {
            $classes[] = 'has-megamenu';
            $classes[] = 'megamenu-type-' . $megamenu_type;
        }
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names .'>';
        
        $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target) ? ' target="' . esc_attr($item->target) .'"' : '';
        $attributes .= ! empty($item->xfn) ? ' rel="'    . esc_attr($item->xfn) .'"' : '';
        $attributes .= ! empty($item->url) ? ' href="'   . esc_attr($item->url) .'"' : '';
        
        $item_output = isset($args->before) ? $args->before : '';
        $item_output .= '<a' . $attributes . '>';
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
        
        // ドロップダウンアイコンを追加
        if (($has_children || $has_megamenu) && $depth === 0) {
            $item_output .= ' <svg class="dropdown-icon" width="8" height="8" viewBox="0 0 12 12" fill="currentColor"><path d="M6 8L2 4h8z"/></svg>';
        }
        
        $item_output .= '</a>';
        $item_output .= isset($args->after) ? $args->after : '';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
        
        // メガメニューコンテンツを追加（トップレベルのみ）
        if ($has_megamenu && $depth === 0) {
            $output .= '<div class="megamenu-container">';
            $output .= '<div class="megamenu-content">';
            $output .= riff_get_megamenu_content($megamenu_type);
            $output .= '</div>';
            $output .= '</div>';
        }
    }

    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}

// ================================
// メガメニュー管理画面の修正
// ================================

// メニュー管理画面にJavaScriptとCSSを追加
function riff_megamenu_admin_assets($hook) {
    if ($hook !== 'nav-menus.php') {
        return;
    }
    
    // メガメニューが有効かチェック
    $megamenu_enabled = get_option('riff_megamenu_enabled', 0);
    
    ?>
    <script type="text/javascript">
        window.riffMegamenuEnabled = <?php echo $megamenu_enabled ? 'true' : 'false'; ?>;
    </script>
    <style>
        .field-megamenu-type {
            <?php echo $megamenu_enabled ? '' : 'display: none !important;'; ?>
        }
        .field-megamenu-type label {
            font-weight: bold;
            color: #007cba;
        }
        .field-megamenu-type select {
            width: 100%;
            margin-top: 5px;
        }
        .field-megamenu-type .description {
            font-style: italic;
            color: #666;
        }
    </style>
    <?php
}
add_action('admin_enqueue_scripts', 'riff_megamenu_admin_assets');

// メニューアイテムにカスタムフィールドを追加（改良版）
function riff_add_megamenu_fields($item_id, $item, $depth, $args) {
    if (get_option('riff_megamenu_enabled', 0) && $depth === 0) {
        $megamenu_type = get_post_meta($item_id, '_riff_megamenu_type', true);
        ?>
        <p class="field-megamenu-type description description-wide">
            <label for="edit-menu-item-megamenu-type-<?php echo $item_id; ?>">
                <strong>メガメニュータイプ</strong><br>
                <select id="edit-menu-item-megamenu-type-<?php echo $item_id; ?>" 
                        class="widefat edit-menu-item-megamenu-type" 
                        name="menu-item-megamenu-type[<?php echo $item_id; ?>]">
                    <option value="" <?php selected($megamenu_type, ''); ?>>メガメニューを使用しない</option>
                    <option value="news" <?php selected($megamenu_type, 'news'); ?>>ニュース/ブログ表示</option>
                    <option value="services" <?php selected($megamenu_type, 'services'); ?>>サービス一覧表示</option>
                    <option value="pages" <?php selected($megamenu_type, 'pages'); ?>>ページ一覧表示</option>
                </select>
            </label>
            <span class="description">
                メガメニューを表示する場合は、タイプを選択してください。<br>
                ニュース：最新記事を表示、サービス：サービス一覧を表示、ページ：主要ページを表示
            </span>
        </p>
        <?php
    }
}
add_action('wp_nav_menu_item_custom_fields', 'riff_add_megamenu_fields', 10, 4);

// カスタムフィールドの保存
function riff_save_megamenu_custom_fields($menu_id, $menu_item_db_id) {
    if (isset($_POST['menu-item-megamenu-type'][$menu_item_db_id])) {
        $megamenu_type = sanitize_text_field($_POST['menu-item-megamenu-type'][$menu_item_db_id]);
        update_post_meta($menu_item_db_id, '_riff_megamenu_type', $megamenu_type);
    } else {
        delete_post_meta($menu_item_db_id, '_riff_megamenu_type');
    }
}
add_action('wp_update_nav_menu_item', 'riff_save_megamenu_custom_fields', 10, 2);

// メニュー管理画面でのJavaScript追加
function riff_megamenu_admin_js() {
    if (get_current_screen()->id === 'nav-menus') {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // メガメニュー設定の表示/非表示
            function toggleMegamenuFields() {
                var enabled = window.riffMegamenuEnabled || false;
                $('.field-megamenu-type').toggle(enabled);
                
                if (!enabled) {
                    $('.edit-menu-item-megamenu-type').val('');
                }
            }
            
            // 初期表示
            toggleMegamenuFields();
            
            // メニューアイテム追加時
            $(document).on('click', '.item-edit', function() {
                setTimeout(toggleMegamenuFields, 100);
            });
            
            // メガメニュータイプ変更時の説明更新
            $(document).on('change', '.edit-menu-item-megamenu-type', function() {
                var $this = $(this);
                var value = $this.val();
                var $description = $this.closest('p').find('.description');
                
                var descriptions = {
                    '': 'メガメニューを表示する場合は、タイプを選択してください。',
                    'news': '最新の投稿記事を表示するメガメニューです。設定された記事数が表示されます。',
                    'services': 'サービス内容をアイコン付きで表示するメガメニューです。',
                    'pages': '主要なページを画像付きで表示するメガメニューです。'
                };
                
                $description.html(descriptions[value] || descriptions['']);
            });
            
            // メニュー保存時の確認
            $('#update-nav-menu').on('click', function() {
                var hasMegamenu = $('.edit-menu-item-megamenu-type').filter(function() {
                    return $(this).val() !== '';
                }).length;
                
                if (hasMegamenu > 0) {
                    console.log('メガメニューが設定されたメニューアイテムが ' + hasMegamenu + '個あります。');
                }
            });
        });
        </script>
        <?php
    }
}
add_action('admin_footer', 'riff_megamenu_admin_js');

// ホームページ判定のヘルパー関数
function riff_is_front_page() {
    return is_front_page() || (is_home() && get_option('page_on_front'));
}

// ブログページ判定のヘルパー関数
function riff_is_blog_page() {
    return (is_home() && !get_option('page_on_front')) || is_category() || is_tag() || is_author() || is_date() || (is_single() && get_post_type() === 'post');
}

// body_classにカスタムクラスを追加
function riff_custom_body_classes($classes) {
    if (riff_is_front_page()) {
        $classes[] = 'front-page';
    }
    
    if (riff_is_blog_page()) {
        $classes[] = 'blog-page';
    }
    
    if (is_page()) {
        $classes[] = 'single-page';
    }
    
    return $classes;
}
add_filter('body_class', 'riff_custom_body_classes');

// ブログページ用のメニューナビゲーション
function riff_add_blog_menu_location() {
    register_nav_menus(array(
        'blog' => 'ブログメニュー',
    ));
}
add_action('after_setup_theme', 'riff_add_blog_menu_location');

// ページネーション用のクエリ調整
function riff_modify_main_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_home() && !get_option('page_on_front')) {
            // ブログホームページの場合
            $query->set('posts_per_page', get_option('posts_per_page', 10));
        }
    }
}
add_action('pre_get_posts', 'riff_modify_main_query');

// カスタム投稿ページ用のテンプレート判定
function riff_template_include($template) {
    // ブログページの場合のテンプレート優先順位を調整
    if (is_home() && !get_option('page_on_front')) {
        // 「投稿ページ」が設定されていない場合はindex.phpを使用
        $new_template = locate_template(array('home.php', 'index.php'));
        if ($new_template) {
            return $new_template;
        }
    }
    
    return $template;
}
add_filter('template_include', 'riff_template_include');

// フロントページ用のカスタマイザー設定（オプション）
function riff_front_page_customizer($wp_customize) {
    // フロントページセクション
    $wp_customize->add_section('riff_front_page_settings', array(
        'title' => 'フロントページ設定',
        'priority' => 35,
    ));
    
    // 会社概要セクション表示/非表示
    $wp_customize->add_setting('riff_show_about_section', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('riff_show_about_section', array(
        'label' => '会社概要セクションを表示',
        'section' => 'riff_front_page_settings',
        'type' => 'checkbox',
    ));
    
    // サービスセクション表示/非表示
    $wp_customize->add_setting('riff_show_services_section', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('riff_show_services_section', array(
        'label' => 'サービスセクションを表示',
        'section' => 'riff_front_page_settings',
        'type' => 'checkbox',
    ));
    
    // ニュースセクション表示/非表示
    $wp_customize->add_setting('riff_show_news_section', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('riff_show_news_section', array(
        'label' => 'ニュースセクションを表示',
        'section' => 'riff_front_page_settings',
        'type' => 'checkbox',
    ));
}
add_action('customize_register', 'riff_front_page_customizer');

// ================================
// フロントページセクション管理機能
// ================================

// フロントページセクション設定ページを追加
function riff_front_sections_admin_page() {
    add_submenu_page(
        'riff-theme-settings',
        'フロントページセクション設定',
        'フロントページセクション',
        'manage_options',
        'riff-front-sections',
        'riff_front_sections_admin_page_content'
    );
}
add_action('admin_menu', 'riff_front_sections_admin_page');

// フロントページセクション設定ページのコンテンツ
function riff_front_sections_admin_page_content() {
    if (isset($_POST['submit'])) {
        // nonceチェック
        if (!isset($_POST['riff_sections_nonce']) || !wp_verify_nonce($_POST['riff_sections_nonce'], 'riff_front_sections_admin_page')) {
            wp_die('セキュリティチェックに失敗しました。');
        }
        
        // WHO WE ARE セクション
        update_option('riff_who_title', sanitize_text_field($_POST['who_title']));
        update_option('riff_who_subtitle', sanitize_text_field($_POST['who_subtitle']));
        update_option('riff_who_description', sanitize_textarea_field($_POST['who_description']));
        update_option('riff_who_detail_text', riff_sanitize_textarea_with_br($_POST['who_detail_text']));
        update_option('riff_who_button_text', sanitize_text_field($_POST['who_button_text']));
        update_option('riff_who_button_url', esc_url_raw($_POST['who_button_url']));
        
        // SERVICE セクション
        update_option('riff_service_title', sanitize_text_field($_POST['service_title']));
        update_option('riff_service_subtitle', sanitize_text_field($_POST['service_subtitle']));
        
        // サービスカード1
        update_option('riff_service1_title', sanitize_text_field($_POST['service1_title']));
        update_option('riff_service1_image', esc_url_raw($_POST['service1_image']));
        update_option('riff_service1_url', esc_url_raw($_POST['service1_url']));
        update_option('riff_service1_color', sanitize_hex_color($_POST['service1_color']));
		update_option('riff_service1_opacity', intval($_POST['service1_opacity']));
        
        // サービスカード2
        update_option('riff_service2_title', sanitize_text_field($_POST['service2_title']));
        update_option('riff_service2_image', esc_url_raw($_POST['service2_image']));
        update_option('riff_service2_url', esc_url_raw($_POST['service2_url']));
        update_option('riff_service2_color', sanitize_hex_color($_POST['service2_color']));
		update_option('riff_service2_opacity', intval($_POST['service2_opacity']));
        
        // サービスカード3
        update_option('riff_service3_title', sanitize_text_field($_POST['service3_title']));
        update_option('riff_service3_image', esc_url_raw($_POST['service3_image']));
        update_option('riff_service3_url', esc_url_raw($_POST['service3_url']));
        update_option('riff_service3_color', sanitize_hex_color($_POST['service3_color']));
		update_option('riff_service3_opacity', intval($_POST['service3_opacity']));
        
        // NEWS セクション
        update_option('riff_news_title', sanitize_text_field($_POST['news_title']));
        update_option('riff_news_subtitle', sanitize_text_field($_POST['news_subtitle']));
        update_option('riff_news_button_text', sanitize_text_field($_POST['news_button_text']));
        update_option('riff_news_button_url', esc_url_raw($_POST['news_button_url']));
        
        // COMPANY セクション
        update_option('riff_company_title', sanitize_text_field($_POST['company_title']));
        update_option('riff_company_subtitle', sanitize_text_field($_POST['company_subtitle']));
        update_option('riff_company_description', riff_sanitize_textarea_with_br($_POST['company_description']));
        update_option('riff_company_button_text', sanitize_text_field($_POST['company_button_text']));
        update_option('riff_company_button_url', esc_url_raw($_POST['company_button_url']));
        update_option('riff_company_image1', esc_url_raw($_POST['company_image1']));
        update_option('riff_company_image2', esc_url_raw($_POST['company_image2']));
        
        // BLOG セクション
        update_option('riff_blog_title', sanitize_text_field($_POST['blog_title']));
        update_option('riff_blog_subtitle', sanitize_text_field($_POST['blog_subtitle']));
        update_option('riff_blog_button_text', sanitize_text_field($_POST['blog_button_text']));
        update_option('riff_blog_button_url', esc_url_raw($_POST['blog_button_url']));
        
        echo '<div class="notice notice-success"><p>フロントページセクション設定を保存しました。</p></div>';
    }
    
    // 設定値を取得
    $who_title = get_option('riff_who_title', 'WHO WE ARE');
    $who_subtitle = get_option('riff_who_subtitle', '日本の高水準を誇る');
    $who_description = get_option('riff_who_description', '未来の情報基盤社会を目指す');
    $who_detail_text = get_option('riff_who_detail_text', '私たちは最先端の技術と豊富な経験を活かし、お客様のニーズに応える最適なソリューションを提供します。');
    $who_button_text = get_option('riff_who_button_text', '詳しく見る');
    $who_button_url = get_option('riff_who_button_url', '#about');
    
    $service_title = get_option('riff_service_title', 'SERVICE');
    $service_subtitle = get_option('riff_service_subtitle', '私たちのサービス');
    
    $service1_title = get_option('riff_service1_title', 'ENERGY');
    $service1_image = get_option('riff_service1_image', '');
    $service1_url = get_option('riff_service1_url', '');
    $service1_color = get_option('riff_service1_color', '#c62d2d');
	$service1_opacity = get_option('riff_service1_opacity', 80);
    
    $service2_title = get_option('riff_service2_title', 'SCIENCE');
    $service2_image = get_option('riff_service2_image', '');
    $service2_url = get_option('riff_service2_url', '');
    $service2_color = get_option('riff_service2_color', '#2d7d32');
	$service2_opacity = get_option('riff_service2_opacity', 80);
    
    $service3_title = get_option('riff_service3_title', 'SOCIETY');
    $service3_image = get_option('riff_service3_image', '');
    $service3_url = get_option('riff_service3_url', '');
    $service3_color = get_option('riff_service3_color', '#1976d2');
	$service3_opacity = get_option('riff_service3_opacity', 80);
    
    $news_title = get_option('riff_news_title', 'NEWS');
    $news_subtitle = get_option('riff_news_subtitle', '最新情報');
    $news_button_text = get_option('riff_news_button_text', '一覧を見る');
    $news_button_url = get_option('riff_news_button_url', '/news/');
    
    $company_title = get_option('riff_company_title', 'COMPANY');
    $company_subtitle = get_option('riff_company_subtitle', '私たちについて');
    $company_description = get_option('riff_company_description', '環境に配慮した持続可能な経営を実践し、社会に貢献する企業を目指しています。');
    $company_button_text = get_option('riff_company_button_text', '詳しく見る');
    $company_button_url = get_option('riff_company_button_url', '/company/');
    $company_image1 = get_option('riff_company_image1', '');
    $company_image2 = get_option('riff_company_image2', '');
    
    $blog_title = get_option('riff_blog_title', 'BLOG');
    $blog_subtitle = get_option('riff_blog_subtitle', '最新記事をお届け');
    $blog_button_text = get_option('riff_blog_button_text', 'ブログ一覧');
    $blog_button_url = get_option('riff_blog_button_url', '/blog/');
    ?>
    
    <div class="wrap">
        <h1>フロントページセクション設定</h1>
        <form method="post" action="">
            <?php wp_nonce_field('riff_front_sections_admin_page', 'riff_sections_nonce'); ?>
            
            <!-- WHO WE ARE セクション -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">WHO WE ARE セクション</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">セクションタイトル</th>
                    <td><input type="text" name="who_title" value="<?php echo esc_attr($who_title); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">サブタイトル</th>
                    <td><input type="text" name="who_subtitle" value="<?php echo esc_attr($who_subtitle); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">メイン説明</th>
                    <td><input type="text" name="who_description" value="<?php echo esc_attr($who_description); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">詳細説明</th>
                    <td><textarea name="who_detail_text" rows="3" cols="50"><?php echo esc_textarea($who_detail_text); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">ボタンテキスト</th>
                    <td><input type="text" name="who_button_text" value="<?php echo esc_attr($who_button_text); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">ボタンURL</th>
                    <td><input type="url" name="who_button_url" value="<?php echo esc_attr($who_button_url); ?>" class="regular-text" /></td>
                </tr>
            </table>
            
            <!-- SERVICE セクション -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">SERVICE セクション</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">セクションタイトル</th>
                    <td><input type="text" name="service_title" value="<?php echo esc_attr($service_title); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">サブタイトル</th>
                    <td><input type="text" name="service_subtitle" value="<?php echo esc_attr($service_subtitle); ?>" class="regular-text" /></td>
                </tr>
            </table>
            
            <!-- サービスカード設定 -->
            <h3>サービスカード1</h3>
            <table class="form-table">
                <tr>
                    <th scope="row">タイトル</th>
                    <td><input type="text" name="service1_title" value="<?php echo esc_attr($service1_title); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">画像URL</th>
                    <td>
                        <input type="url" name="service1_image" id="service1_image" value="<?php echo esc_attr($service1_image); ?>" class="regular-text" />
                        <button type="button" class="button" id="upload_service1_image_button">画像を選択</button>
                    </td>
                </tr>
                <tr>
                    <th scope="row">リンクURL</th>
                    <td><input type="url" name="service1_url" value="<?php echo esc_attr($service1_url); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">テーマカラー</th>
                    <td><input type="color" name="service1_color" value="<?php echo esc_attr($service1_color); ?>" /></td>
                </tr>
				<tr>
					<th scope="row">オーバーレイ透明度</th>
					<td>
						<input type="range" name="service1_opacity" class="service-opacity-slider" 
							   value="<?php echo esc_attr(get_option('riff_service1_opacity', 80)); ?>" 
							   min="10" max="100" step="5" />
						<span class="opacity-display"><?php echo get_option('riff_service1_opacity', 80); ?>%</span>
					</td>
				</tr>
            </table>
            
            <h3>サービスカード2</h3>
            <table class="form-table">
                <tr>
                    <th scope="row">タイトル</th>
                    <td><input type="text" name="service2_title" value="<?php echo esc_attr($service2_title); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">画像URL</th>
                    <td>
                        <input type="url" name="service2_image" id="service2_image" value="<?php echo esc_attr($service2_image); ?>" class="regular-text" />
                        <button type="button" class="button" id="upload_service2_image_button">画像を選択</button>
                    </td>
                </tr>
                <tr>
                    <th scope="row">リンクURL</th>
                    <td><input type="url" name="service2_url" value="<?php echo esc_attr($service2_url); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">テーマカラー</th>
                    <td><input type="color" name="service2_color" value="<?php echo esc_attr($service2_color); ?>" /></td>
                </tr>
				<tr>
                    <th scope="row">オーバーレイ透明度</th>
                    <td>
						<input type="range" name="service2_opacity" class="service-opacity-slider" 
							   value="<?php echo esc_attr(get_option('riff_service2_opacity', 80)); ?>" 
							   min="10" max="100" step="5" />
						<span class="opacity-display"><?php echo get_option('riff_service2_opacity', 80); ?>%</span>
					</td>
                </tr>
            </table>
            
            <h3>サービスカード3</h3>
            <table class="form-table">
                <tr>
                    <th scope="row">タイトル</th>
                    <td><input type="text" name="service3_title" value="<?php echo esc_attr($service3_title); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">画像URL</th>
                    <td>
                        <input type="url" name="service3_image" id="service3_image" value="<?php echo esc_attr($service3_image); ?>" class="regular-text" />
                        <button type="button" class="button" id="upload_service3_image_button">画像を選択</button>
                    </td>
                </tr>
                <tr>
                    <th scope="row">リンクURL</th>
                    <td><input type="url" name="service3_url" value="<?php echo esc_attr($service3_url); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">テーマカラー</th>
                    <td><input type="color" name="service3_color" value="<?php echo esc_attr($service3_color); ?>" /></td>
                </tr>
				<tr>
                    <th scope="row">オーバーレイ透明度</th>
                     <td>
						<input type="range" name="service3_opacity" class="service-opacity-slider" 
							   value="<?php echo esc_attr(get_option('riff_service3_opacity', 80)); ?>" 
							   min="10" max="100" step="5" />
						<span class="opacity-display"><?php echo get_option('riff_service3_opacity', 80); ?>%</span>
					</td>
                </tr>
            </table>
            
            <!-- NEWS セクション -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">NEWS セクション</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">セクションタイトル</th>
                    <td><input type="text" name="news_title" value="<?php echo esc_attr($news_title); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">サブタイトル</th>
                    <td><input type="text" name="news_subtitle" value="<?php echo esc_attr($news_subtitle); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">ボタンテキスト</th>
                    <td><input type="text" name="news_button_text" value="<?php echo esc_attr($news_button_text); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">ボタンURL</th>
                    <td><input type="url" name="news_button_url" value="<?php echo esc_attr($news_button_url); ?>" class="regular-text" /></td>
                </tr>
            </table>
            
            <!-- COMPANY セクション -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">COMPANY セクション</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">セクションタイトル</th>
                    <td><input type="text" name="company_title" value="<?php echo esc_attr($company_title); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">サブタイトル</th>
                    <td><input type="text" name="company_subtitle" value="<?php echo esc_attr($company_subtitle); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">説明文</th>
                    <td><textarea name="company_description" rows="3" cols="50"><?php echo esc_textarea($company_description); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">左側画像URL</th>
                    <td>
                        <input type="url" name="company_image1" id="company_image1" value="<?php echo esc_attr($company_image1); ?>" class="regular-text" />
                        <button type="button" class="button" id="upload_company_image1_button">画像を選択</button>
                    </td>
                </tr>
                <tr>
                    <th scope="row">右側画像URL</th>
                    <td>
                        <input type="url" name="company_image2" id="company_image2" value="<?php echo esc_attr($company_image2); ?>" class="regular-text" />
                        <button type="button" class="button" id="upload_company_image2_button">画像を選択</button>
                    </td>
                </tr>
                <tr>
                    <th scope="row">ボタンテキスト</th>
                    <td><input type="text" name="company_button_text" value="<?php echo esc_attr($company_button_text); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">ボタンURL</th>
                    <td><input type="url" name="company_button_url" value="<?php echo esc_attr($company_button_url); ?>" class="regular-text" /></td>
                </tr>
            </table>
            
            <!-- BLOG セクション -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">BLOG セクション</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">セクションタイトル</th>
                    <td><input type="text" name="blog_title" value="<?php echo esc_attr($blog_title); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">サブタイトル</th>
                    <td><input type="text" name="blog_subtitle" value="<?php echo esc_attr($blog_subtitle); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">ボタンテキスト</th>
                    <td><input type="text" name="blog_button_text" value="<?php echo esc_attr($blog_button_text); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">ボタンURL</th>
                    <td><input type="url" name="blog_button_url" value="<?php echo esc_attr($blog_button_url); ?>" class="regular-text" /></td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // 画像アップローダー
        function setupImageUploader(inputId, buttonId) {
            $('#' + buttonId).click(function(e) {
                e.preventDefault();
                var image = wp.media({
                    title: '画像を選択',
                    multiple: false,
                    library: { type: 'image' }
                }).open().on('select', function(e) {
                    var uploaded_image = image.state().get('selection').first();
                    var image_url = uploaded_image.toJSON().url;
                    $('#' + inputId).val(image_url);
                });
            });
        }
        
        setupImageUploader('service1_image', 'upload_service1_image_button');
        setupImageUploader('service2_image', 'upload_service2_image_button');
        setupImageUploader('service3_image', 'upload_service3_image_button');
        setupImageUploader('company_image1', 'upload_company_image1_button');
        setupImageUploader('company_image2', 'upload_company_image2_button');
    });
    </script>
    <?php
}

// ================================
// セクション管理機能を安全に追加
// ================================

// jQuery UIを読み込む関数
function riff_load_section_management_scripts($hook) {
    // セクション管理ページでのみ読み込み
    if ($hook === 'riff-theme-settings_page_riff-section-management') {
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_style('wp-jquery-ui-dialog');
        
        // セクション管理用の設定
        wp_localize_script('riff-admin-script', 'riffSectionManagement', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('riff_section_management'),
            'messages' => array(
                'saved' => 'セクション設定を保存しました。',
                'error' => 'エラーが発生しました。再度お試しください。',
                'confirm_reset' => 'すべての設定をデフォルトに戻します。よろしいですか？',
                'unsaved_changes' => '変更が保存されていません。ページを離れますか？'
            )
        ));
    }
}
add_action('admin_enqueue_scripts', 'riff_load_section_management_scripts');

// デフォルトセクション設定
// デフォルトセクション設定
function riff_get_default_sections() {
    return array(
        'hero' => array(
            'id' => 'hero',
            'name' => 'ヒーローセクション',
            'description' => 'トップページのメインビジュアル',
            'enabled' => true,
            'order' => 1,
            'required' => true
        ),
        'who' => array(
            'id' => 'who',
            'name' => 'WHO WE ARE',
            'description' => '会社概要セクション',
            'enabled' => true,
            'order' => 2,
            'required' => false
        ),
        'service' => array(
            'id' => 'service',
            'name' => 'SERVICE',
            'description' => 'サービス紹介セクション',
            'enabled' => true,
            'order' => 3,
            'required' => false
        ),
        'news' => array(
            'id' => 'news',
            'name' => 'NEWS',
            'description' => 'ニュース・お知らせセクション',
            'enabled' => true,
            'order' => 4,
            'required' => false
        ),
        'company' => array(
            'id' => 'company',
            'name' => 'COMPANY',
            'description' => '企業情報セクション',
            'enabled' => true,
            'order' => 5,
            'required' => false
        ),
        'faq' => array(
            'id' => 'faq',
            'name' => 'FAQ',
            'description' => 'よくある質問セクション',
            'enabled' => true,
            'order' => 6,
            'required' => false
        ),
        'instagram' => array(
            'id' => 'instagram',
            'name' => 'Instagram',
            'description' => 'Instagram最新投稿セクション',
            'enabled' => true,
            'order' => 7,
            'required' => false
        ),
        'blog' => array(
            'id' => 'blog',
            'name' => 'BLOG',
            'description' => 'ブログ記事セクション',
            'enabled' => true,
            'order' => 8,
            'required' => false
        ),
        'footer_up' => array(
            'id' => 'footer_up',
            'name' => 'FOOTER UP CTA',
            'description' => 'フッター上部コールトゥアクションセクション',
            'enabled' => true,
            'order' => 9,
            'required' => false
        )
    );
}

// セクション設定を取得
function riff_get_sections() {
    $saved_sections = get_option('riff_front_sections', array());
    $default_sections = riff_get_default_sections();
    
    // 保存されたデータがない場合はデフォルトを使用
    if (empty($saved_sections)) {
        update_option('riff_front_sections', $default_sections);
        return $default_sections;
    }
    
    // 新しいセクションが追加された場合のマージ処理
    foreach ($default_sections as $id => $default_section) {
        if (!isset($saved_sections[$id])) {
            $saved_sections[$id] = $default_section;
        }
    }
    
    return $saved_sections;
}

// 有効なセクションを順番通りに取得
function riff_get_enabled_sections_ordered() {
    $sections = riff_get_sections();
    
    // 有効なセクションのみをフィルタリング
    $enabled_sections = array_filter($sections, function($section) {
        return !empty($section['enabled']);
    });
    
    // 順番でソート
    uasort($enabled_sections, function($a, $b) {
        return $a['order'] - $b['order'];
    });
    
    return $enabled_sections;
}

// セクション管理ページを追加
function riff_add_section_management_menu() {
    add_submenu_page(
        'riff-theme-settings',
        'セクション管理',
        'セクション管理',
        'manage_options',
        'riff-section-management',
        'riff_section_management_page_content'
    );
}
add_action('admin_menu', 'riff_add_section_management_menu');

// セクション管理ページのコンテンツ
function riff_section_management_page_content() {
    // 設定保存処理
    if (isset($_POST['save_sections'])) {
        // nonceチェック
        if (!isset($_POST['riff_sections_nonce']) || !wp_verify_nonce($_POST['riff_sections_nonce'], 'riff_sections_management')) {
            wp_die('セキュリティチェックに失敗しました。');
        }
        
        $sections = riff_get_sections();
        
        // セクションの有効/無効設定を更新
        foreach ($sections as $id => $section) {
            $sections[$id]['enabled'] = isset($_POST['section_enabled'][$id]) ? true : false;
        }
        
        // 順番を更新
        if (isset($_POST['section_order']) && is_array($_POST['section_order'])) {
            foreach ($_POST['section_order'] as $id => $order) {
                if (isset($sections[$id])) {
                    $sections[$id]['order'] = intval($order);
                }
            }
        }
        
        update_option('riff_front_sections', $sections);
        echo '<div class="notice notice-success"><p>セクション設定を保存しました。</p></div>';
    }
    
    // リセット処理
    if (isset($_POST['reset_sections'])) {
        if (!isset($_POST['riff_sections_nonce']) || !wp_verify_nonce($_POST['riff_sections_nonce'], 'riff_sections_management')) {
            wp_die('セキュリティチェックに失敗しました。');
        }
        
        $default_sections = riff_get_default_sections();
        update_option('riff_front_sections', $default_sections);
        echo '<div class="notice notice-success"><p>セクション設定をリセットしました。</p></div>';
    }
    
    $sections = riff_get_sections();
    
    // 順番でソート
    uasort($sections, function($a, $b) {
        return $a['order'] - $b['order'];
    });
    ?>
    
    <div class="wrap">
        <h1>フロントページ セクション管理</h1>
        
        <div class="riff-admin-header">
            <h1>セクション管理</h1>
            <p>フロントページに表示されるセクションの表示/非表示と表示順序を管理できます。ドラッグ&ドロップで順番を変更できます。</p>
        </div>
        
        <form method="post" action="" id="sections-form">
            <?php wp_nonce_field('riff_sections_management', 'riff_sections_nonce'); ?>
            
            <div class="sections-container">
                <div class="sections-header" style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #dee2e6;">
                    <h3 style="margin: 0 0 10px 0; color: #333;">セクション一覧</h3>
                    <p style="margin: 0; color: #666; font-size: 14px;">
                        ✓ チェックボックスで表示/非表示を切り替え<br>
                        🔄 ドラッグ&ドロップで表示順序を変更<br>
                        ⚠️ ヒーローセクションは必須のため非表示にできません
                    </p>
                </div>
                
                <div id="sortable-sections" class="sortable-sections">
                    <?php foreach ($sections as $id => $section): ?>
                    <div class="section-item" data-id="<?php echo esc_attr($id); ?>" style="background: #fff; border: 1px solid #dee2e6; border-radius: 6px; margin-bottom: 10px; padding: 20px; cursor: move; transition: all 0.3s ease;">
                        <div class="section-header" style="display: flex; align-items: center; justify-content: space-between;">
                            <div class="section-info" style="display: flex; align-items: center; flex: 1;">
                                <span class="drag-handle" style="margin-right: 15px; color: #999; font-size: 18px; cursor: grab;">⋮⋮</span>
                                
                                <div class="section-checkbox" style="margin-right: 15px;">
                                    <input type="checkbox" 
                                           name="section_enabled[<?php echo esc_attr($id); ?>]" 
                                           id="section_<?php echo esc_attr($id); ?>" 
                                           value="1" 
                                           <?php checked($section['enabled']); ?>
                                           <?php echo $section['required'] ? 'disabled' : ''; ?>
                                           style="transform: scale(1.2);">
                                </div>
                                
                                <div class="section-details">
                                    <h4 style="margin: 0 0 5px 0; font-size: 16px; color: #333;">
                                        <?php echo esc_html($section['name']); ?>
                                        <?php if ($section['required']): ?>
                                            <span style="color: #dc3545; font-size: 12px; margin-left: 8px;">(必須)</span>
                                        <?php endif; ?>
                                    </h4>
                                    <p style="margin: 0; color: #666; font-size: 13px;"><?php echo esc_html($section['description']); ?></p>
                                </div>
                            </div>
                            
                            <div class="section-order" style="display: flex; align-items: center;">
                                <label style="font-size: 13px; color: #666; margin-right: 8px;">順序:</label>
                                <input type="number" 
                                       name="section_order[<?php echo esc_attr($id); ?>]" 
                                       value="<?php echo esc_attr($section['order']); ?>" 
                                       min="1" 
                                       max="10" 
                                       class="section-order-input"
                                       style="width: 60px; padding: 5px; border: 1px solid #ccc; border-radius: 4px; text-align: center;">
                            </div>
                            
                            <div class="section-status" style="margin-left: 20px;">
                                <span class="status-indicator <?php echo $section['enabled'] ? 'enabled' : 'disabled'; ?>" 
                                      style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: <?php echo $section['enabled'] ? '#28a745' : '#dc3545'; ?>;"></span>
                                <span style="margin-left: 5px; font-size: 12px; color: #666;">
                                    <?php echo $section['enabled'] ? '表示中' : '非表示'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="section-actions" style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 6px; display: flex; gap: 15px; align-items: center;">
                <button type="submit" name="save_sections" class="button button-primary" style="padding: 10px 20px; font-size: 14px;">
                    設定を保存
                </button>
                
                <button type="submit" name="reset_sections" class="button button-secondary" 
                        onclick="return confirm('すべての設定をデフォルトに戻します。よろしいですか？');"
                        style="padding: 10px 20px; font-size: 14px;">
                    デフォルトに戻す
                </button>
                
                <div class="section-stats" style="margin-left: auto; font-size: 13px; color: #666;">
                    表示中: <strong><?php echo count(array_filter($sections, function($s) { return $s['enabled']; })); ?></strong> / 
                    総数: <strong><?php echo count($sections); ?></strong>
                </div>
            </div>
        </form>
        
        <div class="section-preview" style="margin-top: 30px; padding: 20px; background: #fff; border: 1px solid #dee2e6; border-radius: 6px;">
            <h3 style="margin: 0 0 15px 0; color: #333;">現在の表示順序プレビュー</h3>
            <div class="preview-list">
                <?php 
                $enabled_sections = riff_get_enabled_sections_ordered();
                $order = 1;
                foreach ($enabled_sections as $section): ?>
                    <div style="display: flex; align-items: center; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                        <span style="width: 30px; height: 30px; background: #007cba; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; margin-right: 15px;">
                            <?php echo $order++; ?>
                        </span>
                        <span style="font-weight: 600; color: #333;"><?php echo esc_html($section['name']); ?></span>
                        <span style="margin-left: 10px; color: #666; font-size: 13px;">- <?php echo esc_html($section['description']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
}

// テーマアクティベーション時にデフォルトセクションを設定
function riff_set_default_sections() {
    if (!get_option('riff_front_sections')) {
        $default_sections = riff_get_default_sections();
        update_option('riff_front_sections', $default_sections);
    }
}
add_action('after_switch_theme', 'riff_set_default_sections');

// ================================
// FOOTER-UPセクション機能の追加
// ================================

// FOOTER-UP セクション設定ページを追加
function riff_footer_up_admin_page() {
    add_submenu_page(
        'riff-theme-settings',
        'Footer-Up設定',
        'Footer-Up設定',
        'manage_options',
        'riff-footer-up-settings',
        'riff_footer_up_admin_page_content'
    );
}
add_action('admin_menu', 'riff_footer_up_admin_page');

// Footer-Up設定ページのコンテンツ
function riff_footer_up_admin_page_content() {
    if (isset($_POST['submit'])) {
        // nonceチェック
        if (!isset($_POST['riff_footer_up_nonce']) || !wp_verify_nonce($_POST['riff_footer_up_nonce'], 'riff_footer_up_admin_page')) {
            wp_die('セキュリティチェックに失敗しました。');
        }
        
        // 設定値を保存
        update_option('riff_footer_up_enabled', isset($_POST['footer_up_enabled']) ? 1 : 0);
        update_option('riff_footer_up_bg_color', sanitize_hex_color($_POST['footer_up_bg_color']));
        update_option('riff_footer_up_text_color', sanitize_hex_color($_POST['footer_up_text_color']));
        update_option('riff_footer_up_bg_image', esc_url_raw($_POST['footer_up_bg_image']));
        
        // CONTACT ボタン設定
        update_option('riff_footer_up_contact_title', sanitize_text_field($_POST['footer_up_contact_title']));
        update_option('riff_footer_up_contact_subtitle', sanitize_text_field($_POST['footer_up_contact_subtitle']));
        update_option('riff_footer_up_contact_url', esc_url_raw($_POST['footer_up_contact_url']));
        update_option('riff_footer_up_contact_color', sanitize_hex_color($_POST['footer_up_contact_color']));
        update_option('riff_footer_up_contact_bg_image', esc_url_raw($_POST['footer_up_contact_bg_image']));
        
        // RECRUIT ボタン設定
        update_option('riff_footer_up_recruit_title', sanitize_text_field($_POST['footer_up_recruit_title']));
        update_option('riff_footer_up_recruit_subtitle', sanitize_text_field($_POST['footer_up_recruit_subtitle']));
        update_option('riff_footer_up_recruit_url', esc_url_raw($_POST['footer_up_recruit_url']));
        update_option('riff_footer_up_recruit_color', sanitize_hex_color($_POST['footer_up_recruit_color']));
        update_option('riff_footer_up_recruit_bg_image', esc_url_raw($_POST['footer_up_recruit_bg_image']));
        
        echo '<div class="notice notice-success"><p>Footer-Up設定を保存しました。</p></div>';
    }
    
    // 設定値を取得（参考サイト準拠のデフォルト値）
    $footer_up_enabled = get_option('riff_footer_up_enabled', 1);
    $footer_up_bg_color = get_option('riff_footer_up_bg_color', '#f8f9fa');
    $footer_up_text_color = get_option('riff_footer_up_text_color', '#333333');
    $footer_up_bg_image = get_option('riff_footer_up_bg_image', '');

    $contact_title = get_option('riff_footer_up_contact_title', 'CONTACT');
    $contact_subtitle = get_option('riff_footer_up_contact_subtitle', '各種お問い合わせ');
    $contact_url = get_option('riff_footer_up_contact_url', '/contact/');
    $contact_color = get_option('riff_footer_up_contact_color', '#ff6b35');
    $contact_bg_image = get_option('riff_footer_up_contact_bg_image', '');

    $recruit_title = get_option('riff_footer_up_recruit_title', 'RECRUIT');
    $recruit_subtitle = get_option('riff_footer_up_recruit_subtitle', '求人採用情報');
    $recruit_url = get_option('riff_footer_up_recruit_url', '/recruit/');
    $recruit_color = get_option('riff_footer_up_recruit_color', '#007cba');
    $recruit_bg_image = get_option('riff_footer_up_recruit_bg_image', '');
    ?>
    
    <div class="wrap">
        <h1>Footer-Up CTA セクション設定</h1>
        
        <div class="riff-admin-header">
            <h1>Footer-Up CTA セクション設定</h1>
            <p>フッター上部に表示される2つのCTAボタンの設定を管理できます。ホバー時に背景画像が表示されます。</p>
        </div>
        
        <form method="post" action="">
            <?php wp_nonce_field('riff_footer_up_admin_page', 'riff_footer_up_nonce'); ?>
            
            <!-- 基本設定 -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">基本設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">セクション表示</th>
                    <td>
                        <label>
                            <input type="checkbox" name="footer_up_enabled" value="1" <?php checked($footer_up_enabled, 1); ?> />
                            Footer-Upセクションを表示する
                        </label>
                        <p class="description">チェックを外すとセクション全体が非表示になります。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">背景色（通常時）</th>
                    <td>
                        <input type="color" name="footer_up_bg_color" value="<?php echo esc_attr($footer_up_bg_color); ?>" />
                        <p class="description">ボタンの通常時の背景色です。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">文字色（通常時）</th>
                    <td>
                        <input type="color" name="footer_up_text_color" value="<?php echo esc_attr($footer_up_text_color); ?>" />
                        <p class="description">テキストの通常時の色です。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">全体背景画像URL</th>
                    <td>
                        <input type="url" name="footer_up_bg_image" id="footer_up_bg_image" value="<?php echo esc_attr($footer_up_bg_image); ?>" class="regular-text" />
                        <button type="button" class="button" id="upload_footer_up_bg_button">画像を選択</button>
                        <p class="description">セクション全体の背景画像を設定する場合は画像URLを入力してください（オプション）。</p>
                        <?php if ($footer_up_bg_image): ?>
                            <div class="footer-up-bg-preview" style="margin-top: 10px;">
                                <img src="<?php echo esc_url($footer_up_bg_image); ?>" style="max-width: 300px; height: auto; border: 1px solid #ddd;" />
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            
            <!-- CONTACTボタン設定 -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">CONTACTボタン設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">タイトル</th>
                    <td><input type="text" name="footer_up_contact_title" value="<?php echo esc_attr($contact_title); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">サブタイトル</th>
                    <td><input type="text" name="footer_up_contact_subtitle" value="<?php echo esc_attr($contact_subtitle); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">リンクURL</th>
                    <td><input type="url" name="footer_up_contact_url" value="<?php echo esc_attr($contact_url); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">ボタン色</th>
                    <td><input type="color" name="footer_up_contact_color" value="<?php echo esc_attr($contact_color); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">背景画像URL（ホバー時表示）</th>
                    <td>
                        <input type="url" name="footer_up_contact_bg_image" id="footer_up_contact_bg_image" value="<?php echo esc_attr($contact_bg_image); ?>" class="regular-text" />
                        <button type="button" class="button" id="upload_contact_bg_button">画像を選択</button>
                        <p class="description">ホバー時に表示される背景画像を設定してください。</p>
                        <?php if ($contact_bg_image): ?>
                            <div class="contact-bg-preview" style="margin-top: 10px;">
                                <img src="<?php echo esc_url($contact_bg_image); ?>" style="max-width: 300px; height: auto; border: 1px solid #ddd;" />
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            
            <!-- RECRUITボタン設定 -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">RECRUITボタン設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">タイトル</th>
                    <td><input type="text" name="footer_up_recruit_title" value="<?php echo esc_attr($recruit_title); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">サブタイトル</th>
                    <td><input type="text" name="footer_up_recruit_subtitle" value="<?php echo esc_attr($recruit_subtitle); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">リンクURL</th>
                    <td><input type="url" name="footer_up_recruit_url" value="<?php echo esc_attr($recruit_url); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">ボタン色</th>
                    <td><input type="color" name="footer_up_recruit_color" value="<?php echo esc_attr($recruit_color); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">背景画像URL（ホバー時表示）</th>
                    <td>
                        <input type="url" name="footer_up_recruit_bg_image" id="footer_up_recruit_bg_image" value="<?php echo esc_attr($recruit_bg_image); ?>" class="regular-text" />
                        <button type="button" class="button" id="upload_recruit_bg_button">画像を選択</button>
                        <p class="description">ホバー時に表示される背景画像を設定してください。</p>
                        <?php if ($recruit_bg_image): ?>
                            <div class="recruit-bg-preview" style="margin-top: 10px;">
                                <img src="<?php echo esc_url($recruit_bg_image); ?>" style="max-width: 300px; height: auto; border: 1px solid #ddd;" />
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // 全体背景画像アップローダー
        $('#upload_footer_up_bg_button').click(function(e) {
            e.preventDefault();
            var image = wp.media({
                title: '背景画像を選択',
                multiple: false,
                library: { type: 'image' }
            }).open().on('select', function(e) {
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#footer_up_bg_image').val(image_url);
                
                // プレビューを更新
                $('.footer-up-bg-preview').remove();
                $('<div class="footer-up-bg-preview" style="margin-top: 10px;"><img src="' + image_url + '" style="max-width: 300px; height: auto; border: 1px solid #ddd;" /></div>').insertAfter('#upload_footer_up_bg_button');
            });
        });
        
        // CONTACTボタン背景画像アップローダー
        $('#upload_contact_bg_button').click(function(e) {
            e.preventDefault();
            var image = wp.media({
                title: 'CONTACT背景画像を選択',
                multiple: false,
                library: { type: 'image' }
            }).open().on('select', function(e) {
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#footer_up_contact_bg_image').val(image_url);
                
                // プレビューを更新
                $('.contact-bg-preview').remove();
                $('<div class="contact-bg-preview" style="margin-top: 10px;"><img src="' + image_url + '" style="max-width: 300px; height: auto; border: 1px solid #ddd;" /></div>').insertAfter('#upload_contact_bg_button');
            });
        });
        
        // RECRUITボタン背景画像アップローダー
        $('#upload_recruit_bg_button').click(function(e) {
            e.preventDefault();
            var image = wp.media({
                title: 'RECRUIT背景画像を選択',
                multiple: false,
                library: { type: 'image' }
            }).open().on('select', function(e) {
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#footer_up_recruit_bg_image').val(image_url);
                
                // プレビューを更新
                $('.recruit-bg-preview').remove();
                $('<div class="recruit-bg-preview" style="margin-top: 10px;"><img src="' + image_url + '" style="max-width: 300px; height: auto; border: 1px solid #ddd;" /></div>').insertAfter('#upload_recruit_bg_button');
            });
        });
    });
    </script>
    <?php
}

// サイド追従ボタン（公式LINE・お問い合わせ）設定ページを追加
function riff_side_buttons_admin_page() {
    add_submenu_page(
        'riff-theme-settings',
        'サイド追従ボタン設定',
        'サイド追従ボタン',
        'manage_options',
        'riff-side-buttons-settings',
        'riff_side_buttons_admin_page_content'
    );
}
add_action('admin_menu', 'riff_side_buttons_admin_page');

// サイド追従ボタン設定ページのコンテンツ
function riff_side_buttons_admin_page_content() {
    if (isset($_POST['submit'])) {
        // nonceチェック
        if (!isset($_POST['riff_side_buttons_nonce']) || !wp_verify_nonce($_POST['riff_side_buttons_nonce'], 'riff_side_buttons_admin_page')) {
            wp_die('セキュリティチェックに失敗しました。');
        }

        update_option('riff_side_buttons_line_enabled', isset($_POST['side_buttons_line_enabled']) ? 1 : 0);
        update_option('riff_side_buttons_line_label', sanitize_text_field($_POST['side_buttons_line_label']));
        update_option('riff_side_buttons_line_url', esc_url_raw($_POST['side_buttons_line_url']));

        update_option('riff_side_buttons_contact_enabled', isset($_POST['side_buttons_contact_enabled']) ? 1 : 0);
        update_option('riff_side_buttons_contact_label', sanitize_text_field($_POST['side_buttons_contact_label']));
        update_option('riff_side_buttons_contact_url', esc_url_raw($_POST['side_buttons_contact_url']));

        echo '<div class="notice notice-success"><p>サイド追従ボタン設定を保存しました。</p></div>';
    }

    // 設定値を取得（未設定時は関連する既存設定値をフォールバックとして使用）
    $line_enabled = get_option('riff_side_buttons_line_enabled', 1);
    $line_label = get_option('riff_side_buttons_line_label', '公式LINE');
    $line_url = get_option('riff_side_buttons_line_url', get_option('riff_footer_line_url', ''));

    $contact_enabled = get_option('riff_side_buttons_contact_enabled', 1);
    $contact_label = get_option('riff_side_buttons_contact_label', 'お問い合わせ');
    $contact_url = get_option('riff_side_buttons_contact_url', get_option('riff_footer_up_contact_url', '/contact/'));
    ?>

    <div class="wrap">
        <h1>サイド追従ボタン設定</h1>

        <div class="riff-admin-header">
            <h1>サイド追従ボタン設定</h1>
            <p>全ページ右側（スマホ表示では画面下部）に固定表示される「公式LINE」「お問い合わせ」ボタンの表示ON/OFF・文言・リンク先を設定できます。</p>
        </div>

        <form method="post" action="">
            <?php wp_nonce_field('riff_side_buttons_admin_page', 'riff_side_buttons_nonce'); ?>

            <!-- 公式LINEボタン設定 -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">公式LINEボタン設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">表示</th>
                    <td>
                        <label>
                            <input type="checkbox" name="side_buttons_line_enabled" value="1" <?php checked($line_enabled, 1); ?> />
                            公式LINEボタンを表示する
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">ボタン文言</th>
                    <td><input type="text" name="side_buttons_line_label" value="<?php echo esc_attr($line_label); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">リンクURL</th>
                    <td>
                        <input type="url" name="side_buttons_line_url" value="<?php echo esc_attr($line_url); ?>" class="regular-text" placeholder="https://lin.ee/xxxxxxx" />
                        <p class="description">LINE公式アカウントの友だち追加URLなどを入力してください。空欄の場合、ボタンは表示されません。</p>
                    </td>
                </tr>
            </table>

            <!-- お問い合わせボタン設定 -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">お問い合わせボタン設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">表示</th>
                    <td>
                        <label>
                            <input type="checkbox" name="side_buttons_contact_enabled" value="1" <?php checked($contact_enabled, 1); ?> />
                            お問い合わせボタンを表示する
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">ボタン文言</th>
                    <td><input type="text" name="side_buttons_contact_label" value="<?php echo esc_attr($contact_label); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">リンクURL</th>
                    <td>
                        <input type="url" name="side_buttons_contact_url" value="<?php echo esc_attr($contact_url); ?>" class="regular-text" placeholder="/contact/" />
                        <p class="description">空欄の場合、ボタンは表示されません。</p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Footer-Upセクション表示関数
function riff_display_footer_up_section() {
    // 設定が無効の場合も非表示
    $footer_up_enabled = get_option('riff_footer_up_enabled', 1);
    if (!$footer_up_enabled) {
        return;
    }
    
    // 設定値を取得
    $bg_color = get_option('riff_footer_up_bg_color', '#f8f9fa');
    $text_color = get_option('riff_footer_up_text_color', '#333333');
    $bg_image = get_option('riff_footer_up_bg_image', '');
    
    // CONTACT ボタン設定
    $contact_title = get_option('riff_footer_up_contact_title', 'CONTACT');
    $contact_subtitle = get_option('riff_footer_up_contact_subtitle', '各種お問い合わせ');
    $contact_url = get_option('riff_footer_up_contact_url', '/contact/');
    $contact_color = get_option('riff_footer_up_contact_color', '#ff6b35');
    $contact_bg_image = get_option('riff_footer_up_contact_bg_image', '');
    
    // RECRUIT ボタン設定
    $recruit_title = get_option('riff_footer_up_recruit_title', 'RECRUIT');
    $recruit_subtitle = get_option('riff_footer_up_recruit_subtitle', '求人採用情報');
    $recruit_url = get_option('riff_footer_up_recruit_url', '/recruit/');
    $recruit_color = get_option('riff_footer_up_recruit_color', '#007cba');
    $recruit_bg_image = get_option('riff_footer_up_recruit_bg_image', '');
    ?>
    
    <style>
        :root {
            --footer-up-bg-color: <?php echo esc_attr($bg_color); ?>;
            --footer-up-text-color: <?php echo esc_attr($text_color); ?>;
            <?php if ($contact_bg_image): ?>
            --contact-bg-image: url('<?php echo esc_url($contact_bg_image); ?>');
            <?php endif; ?>
            <?php if ($recruit_bg_image): ?>
            --recruit-bg-image: url('<?php echo esc_url($recruit_bg_image); ?>');
            <?php endif; ?>
        }
    </style>
    
    <section class="riff-footer-up-section" id="footer-up" style="background-color: <?php echo esc_attr($bg_color); ?>; color: <?php echo esc_attr($text_color); ?>;">
        <?php if ($bg_image): ?>
        <div class="riff-footer-up-bg" style="background-image: url(<?php echo esc_url($bg_image); ?>);"></div>
        <div class="riff-footer-up-overlay"></div>
        <?php endif; ?>
        
        <div class="riff-footer-up-container">
            <div class="riff-footer-up-cta-buttons fade-in">
                <!-- CONTACTボタン -->
                <?php if ($contact_title && $contact_url): ?>
                <a href="<?php echo esc_url($contact_url); ?>" 
                   class="riff-footer-up-cta-button riff-footer-up-contact"
                   <?php if ($contact_bg_image): ?>
                   style="background-image: url('<?php echo esc_url($contact_bg_image); ?>');"
                   <?php endif; ?>>
                    <div class="riff-footer-up-cta-content">
                        <h3 class="riff-footer-up-cta-title"><?php echo esc_html($contact_title); ?></h3>
                        <?php if ($contact_subtitle): ?>
                        <p class="riff-footer-up-cta-subtitle"><?php echo esc_html($contact_subtitle); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="riff-footer-up-cta-arrow">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                        </svg>
                    </div>
                </a>
                <?php endif; ?>
                
                <!-- RECRUITボタン -->
                <?php if ($recruit_title && $recruit_url): ?>
                <a href="<?php echo esc_url($recruit_url); ?>" 
                   class="riff-footer-up-cta-button riff-footer-up-recruit"
                   <?php if ($recruit_bg_image): ?>
                   style="background-image: url('<?php echo esc_url($recruit_bg_image); ?>');"
                   <?php endif; ?>>
                    <div class="riff-footer-up-cta-content">
                        <h3 class="riff-footer-up-cta-title"><?php echo esc_html($recruit_title); ?></h3>
                        <?php if ($recruit_subtitle): ?>
                        <p class="riff-footer-up-cta-subtitle"><?php echo esc_html($recruit_subtitle); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="riff-footer-up-cta-arrow">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                        </svg>
                    </div>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
}

// ================================
// 新しいフッター設定機能（ロゴ選択・フルワイド対応）
// ================================

// フッター設定ページを追加
function riff_footer_settings_admin_page() {
    add_submenu_page(
        'riff-theme-settings',
        'フッター設定',
        'フッター設定',
        'manage_options',
        'riff-footer-settings',
        'riff_footer_settings_admin_page_content'
    );
}
add_action('admin_menu', 'riff_footer_settings_admin_page');

// フッター設定ページのコンテンツ（ロゴ選択機能付き）
function riff_footer_settings_admin_page_content() {
    if (isset($_POST['submit'])) {
        // nonceチェック
        if (!isset($_POST['riff_footer_nonce']) || !wp_verify_nonce($_POST['riff_footer_nonce'], 'riff_footer_settings')) {
            wp_die('セキュリティチェックに失敗しました。');
        }
        
        // ロゴ・タイトル表示設定
        update_option('riff_footer_display_type', sanitize_text_field($_POST['footer_display_type']));
        update_option('riff_footer_logo_url', esc_url_raw($_POST['footer_logo_url']));
        update_option('riff_footer_logo_width', intval($_POST['footer_logo_width']));
        update_option('riff_footer_logo_height', intval($_POST['footer_logo_height']));
        
        // 会社情報設定
        update_option('riff_footer_company_name', sanitize_text_field($_POST['footer_company_name']));
        update_option('riff_footer_address', sanitize_textarea_field($_POST['footer_address']));
        
        // コピーライト設定（新規追加）
        update_option('riff_footer_copyright_text', sanitize_text_field($_POST['footer_copyright_text']));
        
        // SNS設定
        update_option('riff_footer_instagram_url', esc_url_raw($_POST['footer_instagram_url']));
        update_option('riff_footer_twitter_url', esc_url_raw($_POST['footer_twitter_url']));
        update_option('riff_footer_facebook_url', esc_url_raw($_POST['footer_facebook_url']));
        update_option('riff_footer_youtube_url', esc_url_raw($_POST['footer_youtube_url']));
        update_option('riff_footer_tiktok_url', esc_url_raw($_POST['footer_tiktok_url']));
		update_option('riff_footer_line_url', esc_url_raw($_POST['footer_line_url']));
        
        // デザイン設定
        update_option('riff_footer_bg_color', sanitize_hex_color($_POST['footer_bg_color']));
        update_option('riff_footer_text_color', sanitize_hex_color($_POST['footer_text_color']));
        update_option('riff_footer_link_color', sanitize_hex_color($_POST['footer_link_color']));
        update_option('riff_footer_link_hover_color', sanitize_hex_color($_POST['footer_link_hover_color']));
        
        // コピーライト専用デザイン設定（新規追加）
        update_option('riff_footer_copyright_bg_color', sanitize_hex_color($_POST['footer_copyright_bg_color']));
        update_option('riff_footer_copyright_text_color', sanitize_hex_color($_POST['footer_copyright_text_color']));
        
        update_option('riff_footer_fullwidth', isset($_POST['footer_fullwidth']) ? 1 : 0);
        
        echo '<div class="notice notice-success"><p>フッター設定を保存しました。</p></div>';
    }
    
    // 設定値を取得
    $display_type = get_option('riff_footer_display_type', 'title');
    $footer_logo_url = get_option('riff_footer_logo_url', '');
    $footer_logo_width = get_option('riff_footer_logo_width', 180);
    $footer_logo_height = get_option('riff_footer_logo_height', 50);
    
    $company_name = get_option('riff_footer_company_name', 'RIFF SYSTEM');
    $address = get_option('riff_footer_address', '');
    
    // コピーライト設定の取得（新規追加）
    $copyright_text = get_option('riff_footer_copyright_text', '&copy; ' . date('Y') . ' ' . get_option('riff_footer_company_name', get_option('riff_site_title', get_bloginfo('name'))) . '. All rights reserved.');
    
    $instagram_url = get_option('riff_footer_instagram_url', '');
    $twitter_url = get_option('riff_footer_twitter_url', '');
    $facebook_url = get_option('riff_footer_facebook_url', '');
    $youtube_url = get_option('riff_footer_youtube_url', '');
    $tiktok_url = get_option('riff_footer_tiktok_url', '');
	$line_url = get_option('riff_footer_line_url', '');

    
    $bg_color = get_option('riff_footer_bg_color', '#2c3e50');
    $text_color = get_option('riff_footer_text_color', '#ffffff');
    $link_color = get_option('riff_footer_link_color', '#ecf0f1');
    $link_hover_color = get_option('riff_footer_link_hover_color', '#ff6b35');
    
    // コピーライト専用設定の取得（新規追加）
    $copyright_bg_color = get_option('riff_footer_copyright_bg_color', '#1a252f');
    $copyright_text_color = get_option('riff_footer_copyright_text_color', '#ffffff');
    
    $fullwidth = get_option('riff_footer_fullwidth', 1);
    ?>
    
    <div class="wrap">
        <h1>フッター設定</h1>
        
        <div class="riff-admin-header">
            <h1>フッター設定</h1>
            <p>フッターに表示される会社情報、SNSリンク、デザインを設定できます。</p>
        </div>
        
        <form method="post" action="">
            <?php wp_nonce_field('riff_footer_settings', 'riff_footer_nonce'); ?>
            
            <!-- ロゴ・タイトル表示設定 -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">ロゴ・タイトル表示設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">表示タイプ</th>
                    <td>
                        <label style="margin-right: 20px;">
                            <input type="radio" name="footer_display_type" value="title" <?php checked($display_type, 'title'); ?> />
                            サイトタイトルを表示
                        </label>
                        <label>
                            <input type="radio" name="footer_display_type" value="logo" <?php checked($display_type, 'logo'); ?> />
                            ロゴ画像を表示
                        </label>
                        <p class="description">フッターに表示するタイプを選択してください。</p>
                    </td>
                </tr>
                <tr class="footer-logo-settings" style="<?php echo $display_type !== 'logo' ? 'display:none;' : ''; ?>">
                    <th scope="row">フッターロゴ画像URL</th>
                    <td>
                        <input type="url" name="footer_logo_url" id="footer_logo_url" value="<?php echo esc_attr($footer_logo_url); ?>" class="regular-text" />
                        <button type="button" class="button" id="upload_footer_logo_button">画像を選択</button>
                        <p class="description">フッター専用のロゴ画像を設定してください。</p>
                        <?php if ($footer_logo_url): ?>
                            <div class="footer-logo-preview" style="margin-top: 10px;">
                                <img src="<?php echo esc_url($footer_logo_url); ?>" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 10px; background: <?php echo esc_attr($bg_color); ?>;" />
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr class="footer-logo-settings" style="<?php echo $display_type !== 'logo' ? 'display:none;' : ''; ?>">
                    <th scope="row">ロゴ幅（px）</th>
                    <td>
                        <input type="number" name="footer_logo_width" value="<?php echo esc_attr($footer_logo_width); ?>" min="50" max="500" />
                        <p class="description">フッターロゴの表示幅を設定してください。</p>
                    </td>
                </tr>
                <tr class="footer-logo-settings" style="<?php echo $display_type !== 'logo' ? 'display:none;' : ''; ?>">
                    <th scope="row">ロゴ高さ（px）</th>
                    <td>
                        <input type="number" name="footer_logo_height" value="<?php echo esc_attr($footer_logo_height); ?>" min="30" max="200" />
                        <p class="description">フッターロゴの表示高さを設定してください。</p>
                    </td>
                </tr>
            </table>
            
            <!-- 会社情報設定 -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">会社情報設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">会社名</th>
                    <td>
                        <input type="text" name="footer_company_name" value="<?php echo esc_attr($company_name); ?>" class="regular-text" />
                        <p class="description">フッターに表示される会社名を設定してください。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">住所</th>
                    <td>
                        <textarea name="footer_address" rows="3" cols="50" class="large-text"><?php echo esc_textarea($address); ?></textarea>
                        <p class="description">会社の住所を設定してください。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row" style="color: #d63638; font-weight: bold;">コピーライト</th>
                    <td>
                        <input type="text" name="footer_copyright_text" value="<?php echo esc_attr($copyright_text); ?>" class="large-text" />
                        <p class="description" style="color: #d63638;">コピーライトの文言を設定してください。HTMLエンティティ（&copy;など）も使用可能です。</p>
                    </td>
                </tr>
            </table>
            
            <!-- SNS設定 -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">SNSリンク設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Instagram URL</th>
                    <td>
                        <input type="url" name="footer_instagram_url" value="<?php echo esc_attr($instagram_url); ?>" class="regular-text" />
                        <p class="description">InstagramのプロフィールURLを入力してください。空欄の場合は表示されません。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">X (Twitter) URL</th>
                    <td>
                        <input type="url" name="footer_twitter_url" value="<?php echo esc_attr($twitter_url); ?>" class="regular-text" />
                        <p class="description">X (Twitter)のプロフィールURLを入力してください。空欄の場合は表示されません。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Facebook URL</th>
                    <td>
                        <input type="url" name="footer_facebook_url" value="<?php echo esc_attr($facebook_url); ?>" class="regular-text" />
                        <p class="description">FacebookのページURLを入力してください。空欄の場合は表示されません。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">YouTube URL</th>
                    <td>
                        <input type="url" name="footer_youtube_url" value="<?php echo esc_attr($youtube_url); ?>" class="regular-text" />
                        <p class="description">YouTubeのチャンネルURLを入力してください。空欄の場合は表示されません。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">TikTok URL</th>
                    <td>
                        <input type="url" name="footer_tiktok_url" value="<?php echo esc_attr($tiktok_url); ?>" class="regular-text" />
                        <p class="description">TikTokのプロフィールURLを入力してください。空欄の場合は表示されません。</p>
                    </td>
                </tr>
				<tr>
                    <th scope="row">LINE URL</th>
                    <td>
                        <input type="url" name="footer_line_url" value="<?php echo esc_attr($line_url); ?>" class="regular-text" />
                        <p class="description">LINE公式アカウントのURLを入力してください。空欄の場合は表示されません。</p>
                    </td>
                </tr>
            </table>
            
            <!-- レイアウト設定 -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">レイアウト設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">フッター横幅</th>
                    <td>
                        <label>
                            <input type="checkbox" name="footer_fullwidth" value="1" <?php checked($fullwidth, 1); ?> />
                            フッターを横幅いっぱいに表示する
                        </label>
                        <p class="description">チェックを入れると、フッターが画面幅いっぱいに表示されます。</p>
                    </td>
                </tr>
            </table>
            
            <!-- デザイン設定 -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">フッターデザイン設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">フッター背景色</th>
                    <td>
                        <input type="color" name="footer_bg_color" value="<?php echo esc_attr($bg_color); ?>" />
                        <p class="description">フッターメイン部分の背景色を設定してください。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">文字色</th>
                    <td>
                        <input type="color" name="footer_text_color" value="<?php echo esc_attr($text_color); ?>" />
                        <p class="description">フッターの基本文字色を設定してください。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">リンク色</th>
                    <td>
                        <input type="color" name="footer_link_color" value="<?php echo esc_attr($link_color); ?>" />
                        <p class="description">フッターのリンク色を設定してください。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">リンクホバー色</th>
                    <td>
                        <input type="color" name="footer_link_hover_color" value="<?php echo esc_attr($link_hover_color); ?>" />
                        <p class="description">リンクにマウスを乗せた時の色を設定してください。</p>
                    </td>
                </tr>
            </table>
            
            <!-- コピーライト専用デザイン設定 -->
            <h2 style="background: #ffe6e6; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px; border: 2px solid #d63638;">コピーライト専用デザイン設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row" style="color: #d63638; font-weight: bold;">コピーライト背景色</th>
                    <td>
                        <input type="color" name="footer_copyright_bg_color" value="<?php echo esc_attr($copyright_bg_color); ?>" />
                        <p class="description" style="color: #d63638; font-weight: bold;">コピーライト部分の背景色を設定してください。フッター背景色とは独立して設定できます。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row" style="color: #d63638; font-weight: bold;">コピーライト文字色</th>
                    <td>
                        <input type="color" name="footer_copyright_text_color" value="<?php echo esc_attr($copyright_text_color); ?>" />
                        <p class="description" style="color: #d63638; font-weight: bold;">コピーライト部分の文字色を設定してください。背景色に合わせて調整してください。</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('設定を保存', 'primary', 'submit', false, array('style' => 'font-size: 16px; padding: 10px 20px;')); ?>
        </form>
        
        <!-- プレビューセクション -->
        <div style="margin-top: 40px; padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 6px;">
            <h3>プレビュー</h3>
            
            <!-- フッターメイン部分 -->
            <div style="background: <?php echo esc_attr($bg_color); ?>; color: <?php echo esc_attr($text_color); ?>; padding: 20px; border-radius: 4px 4px 0 0;">
                <div style="margin-bottom: 15px;">
                    <?php if ($display_type === 'logo' && !empty($footer_logo_url)): ?>
                        <img src="<?php echo esc_url($footer_logo_url); ?>" style="width: <?php echo esc_attr($footer_logo_width); ?>px; height: <?php echo esc_attr($footer_logo_height); ?>px; object-fit: contain; filter: brightness(0) invert(1);" alt="Footer Logo">
                    <?php else: ?>
                        <strong style="font-size: 24px;"><?php echo esc_html(get_option('riff_site_title', get_bloginfo('name'))); ?></strong>
                    <?php endif; ?>
                    <br>
                    <strong style="font-size: 18px;"><?php echo esc_html($company_name); ?></strong><br>
                    <span style="font-size: 14px;"><?php echo nl2br(esc_html($address)); ?></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <?php if (!empty($instagram_url)): ?><span style="margin-right: 10px; background: rgba(255,255,255,0.1); padding: 5px; border-radius: 3px;">📷</span><?php endif; ?>
                    <?php if (!empty($twitter_url)): ?><span style="margin-right: 10px; background: rgba(255,255,255,0.1); padding: 5px; border-radius: 3px;">🐦</span><?php endif; ?>
                    <?php if (!empty($facebook_url)): ?><span style="margin-right: 10px; background: rgba(255,255,255,0.1); padding: 5px; border-radius: 3px;">📘</span><?php endif; ?>
                    <?php if (!empty($youtube_url)): ?><span style="margin-right: 10px; background: rgba(255,255,255,0.1); padding: 5px; border-radius: 3px;">📺</span><?php endif; ?>
                    <?php if (!empty($tiktok_url)): ?><span style="margin-right: 10px; background: rgba(255,255,255,0.1); padding: 5px; border-radius: 3px;">🎵</span><?php endif; ?>
                </div>
                <div style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 15px;">
                    <a href="#" style="color: <?php echo esc_attr($link_color); ?>; margin-right: 20px; text-decoration: none; padding: 5px 10px; border: 1px solid rgba(255,255,255,0.1); border-radius: 3px;">ホーム</a>
                    <a href="#" style="color: <?php echo esc_attr($link_color); ?>; margin-right: 20px; text-decoration: none; padding: 5px 10px; border: 1px solid rgba(255,255,255,0.1); border-radius: 3px;">会社概要</a>
                    <a href="#" style="color: <?php echo esc_attr($link_color); ?>; margin-right: 20px; text-decoration: none; padding: 5px 10px; border: 1px solid rgba(255,255,255,0.1); border-radius: 3px;">サービス</a>
                </div>
            </div>
            
            <!-- コピーライト部分 -->
            <div style="background: <?php echo esc_attr($copyright_bg_color); ?>; color: <?php echo esc_attr($copyright_text_color); ?>; padding: 20px; border-radius: 0 0 4px 4px; font-size: 14px; text-align: left;">
                <strong>コピーライト部分:</strong> <?php echo esc_html($copyright_text); ?>
            </div>
            
            <div style="margin-top: 15px; padding: 15px; background: #f9f9f9; border-radius: 4px;">
                <h4 style="margin: 0 0 10px 0; color: #333;">設定状況</h4>
                <p style="margin: 5px 0; color: #666;">
                    <strong>表示タイプ:</strong> <?php echo $display_type === 'logo' ? 'ロゴ画像' : 'サイトタイトル'; ?><br>
                    <strong>横幅設定:</strong> <?php echo $fullwidth ? 'フルワイド' : 'コンテナ幅'; ?><br>
                    <strong>フッター背景色:</strong> <span style="display: inline-block; width: 20px; height: 20px; background: <?php echo esc_attr($bg_color); ?>; border: 1px solid #ccc; vertical-align: middle;"></span> <?php echo esc_html($bg_color); ?><br>
                    <strong>コピーライト背景色:</strong> <span style="display: inline-block; width: 20px; height: 20px; background: <?php echo esc_attr($copyright_bg_color); ?>; border: 1px solid #ccc; vertical-align: middle;"></span> <?php echo esc_html($copyright_bg_color); ?><br>
                    <strong>コピーライト文字色:</strong> <span style="display: inline-block; width: 20px; height: 20px; background: <?php echo esc_attr($copyright_text_color); ?>; border: 1px solid #ccc; vertical-align: middle;"></span> <?php echo esc_html($copyright_text_color); ?>
                </p>
            </div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // ロゴアップローダー
        $('#upload_footer_logo_button').click(function(e) {
            e.preventDefault();
            var image = wp.media({
                title: 'フッターロゴ画像を選択',
                multiple: false,
                library: { type: 'image' }
            }).open().on('select', function(e) {
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#footer_logo_url').val(image_url);
                
                // プレビューを更新
                $('.footer-logo-preview').remove();
                $('<div class="footer-logo-preview" style="margin-top: 10px;"><img src="' + image_url + '" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 10px; background: <?php echo esc_attr($bg_color); ?>;" /></div>').insertAfter('#upload_footer_logo_button');
            });
        });
        
        // 表示タイプの切り替え
        $('input[name="footer_display_type"]').on('change', function() {
            if ($(this).val() === 'logo') {
                $('.footer-logo-settings').show();
            } else {
                $('.footer-logo-settings').hide();
            }
        });
    });
    </script>
    <?php
}

// フッター用カスタムCSSを動的生成（コピーライト背景色対応）
function riff_footer_custom_css() {
    $bg_color = get_option('riff_footer_bg_color', '#2c3e50');
    $text_color = get_option('riff_footer_text_color', '#ffffff');
    $link_color = get_option('riff_footer_link_color', '#ecf0f1');
    $link_hover_color = get_option('riff_footer_link_hover_color', '#ff6b35');
    $copyright_bg_color = get_option('riff_footer_copyright_bg_color', '#1a252f');
    $copyright_text_color = get_option('riff_footer_copyright_text_color', '#ffffff');
    $fullwidth = get_option('riff_footer_fullwidth', 1);
    $footer_logo_width = get_option('riff_footer_logo_width', 180);
    $footer_logo_height = get_option('riff_footer_logo_height', 50);
    ?>
    <style type="text/css">
        :root {
            --riff-footer-bg-color: <?php echo esc_attr($bg_color); ?>;
            --riff-footer-text-color: <?php echo esc_attr($text_color); ?>;
            --riff-footer-link-color: <?php echo esc_attr($link_color); ?>;
            --riff-footer-link-hover-color: <?php echo esc_attr($link_hover_color); ?>;
            --riff-footer-copyright-bg-color: <?php echo esc_attr($copyright_bg_color); ?>;
            --riff-footer-copyright-text-color: <?php echo esc_attr($copyright_text_color); ?>;
            --riff-footer-logo-width: <?php echo esc_attr($footer_logo_width); ?>px;
            --riff-footer-logo-height: <?php echo esc_attr($footer_logo_height); ?>px;
            --riff-footer-fullwidth: <?php echo $fullwidth ? '100%' : '1200px'; ?>;
        }
        
        <?php if ($fullwidth): ?>
        .riff-new-footer-container {
            max-width: 100% !important;
            padding: 0 40px !important;
        }
        
        @media (max-width: 768px) {
            .riff-new-footer-container {
                padding: 0 20px !important;
            }
        }
        
        @media (max-width: 480px) {
            .riff-new-footer-container {
                padding: 0 15px !important;
            }
        }
        <?php endif; ?>
    </style>
    <?php
}
add_action('wp_head', 'riff_footer_custom_css');

/**
 * ナビゲーションメニューの登録
 */
function riff_register_menus() {
    register_nav_menus(array(
        'primary' => 'メインナビゲーション',
        'footer' => 'フッターナビゲーション',
        'mobile' => 'モバイルナビゲーション（オプション）'
    ));
}
add_action('after_setup_theme', 'riff_register_menus');

/**
 * デフォルトメニューのフォールバック（デスクトップ用）
 */
function riff_fallback_menu() {
    echo '<ul class="nav nav-primary">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">ホーム</a></li>';
    echo '<li><a href="' . esc_url(home_url('/about/')) . '">会社概要</a></li>';
    echo '<li><a href="' . esc_url(home_url('/services/')) . '">サービス</a></li>';
    echo '<li><a href="' . esc_url(home_url('/news/')) . '">ニュース</a></li>';
    echo '<li><a href="' . esc_url(home_url('/contact/')) . '">お問い合わせ</a></li>';
    echo '</ul>';
}

/**
 * モバイル用デフォルトメニューのフォールバック
 */
function riff_fallback_menu_mobile() {
    echo '<ul class="nav nav-mobile">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">ホーム</a></li>';
    echo '<li><a href="' . esc_url(home_url('/about/')) . '">会社概要</a></li>';
    echo '<li><a href="' . esc_url(home_url('/services/')) . '">サービス</a></li>';
    echo '<li><a href="' . esc_url(home_url('/news/')) . '">ニュース</a></li>';
    echo '<li><a href="' . esc_url(home_url('/contact/')) . '">お問い合わせ</a></li>';
    echo '</ul>';
}

/**
 * デスクトップ用カスタムナビゲーションウォーカー
 */
class Riff_Nav_Walker extends Walker_Nav_Menu {
    
    /**
     * サブメニューの開始
     */
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"dropdown-menu\" data-depth=\"$depth\">\n";
    }

    /**
     * サブメニューの終了
     */
    function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    /**
     * メニューアイテムの開始
     */
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        // 現在のページかチェック
        if (in_array('current-menu-item', $classes) || in_array('current-menu-ancestor', $classes)) {
            $classes[] = 'active';
        }
        
        // ドロップダウンメニューがあるかチェック
        $has_children = in_array('menu-item-has-children', $classes);
        if ($has_children) {
            $classes[] = 'has-dropdown';
        }
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names .'>';
        
        // リンク属性の設定
        $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target) ? ' target="' . esc_attr($item->target) .'"' : '';
        $attributes .= ! empty($item->xfn) ? ' rel="'    . esc_attr($item->xfn) .'"' : '';
        $attributes .= ! empty($item->url) ? ' href="'   . esc_attr($item->url) .'"' : '';
        
        // アクセシビリティの向上
        if ($has_children && $depth === 0) {
            $attributes .= ' aria-haspopup="true" aria-expanded="false"';
        }
        
        $item_output = isset($args->before) ? $args->before : '';
        $item_output .= '<a' . $attributes . '>';
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
        
        // ドロップダウンアイコンを追加
        if ($has_children && $depth === 0) {
            $item_output .= ' <svg class="dropdown-icon" width="8" height="8" viewBox="0 0 12 12" fill="currentColor" aria-hidden="true"><path d="M6 8L2 4h8z"/></svg>';
        }
        
        $item_output .= '</a>';
        $item_output .= isset($args->after) ? $args->after : '';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    /**
     * メニューアイテムの終了
     */
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}

/**
 * モバイル用カスタムナビゲーションウォーカー
 */
class Riff_Mobile_Nav_Walker extends Walker_Nav_Menu {
    
    /**
     * サブメニューの開始
     */
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"mobile-submenu\" data-depth=\"$depth\" style=\"display:none !important;\">\n";
    }

    /**
     * サブメニューの終了
     */
    function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    /**
     * メニューアイテムの開始
     */
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        // 現在のページかチェック
        if (in_array('current-menu-item', $classes) || in_array('current-menu-ancestor', $classes)) {
            $classes[] = 'active';
        }
        
        // ドロップダウンメニューがあるかチェック
        $has_children = in_array('menu-item-has-children', $classes);
        if ($has_children) {
            $classes[] = 'has-submenu';
        }
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names .'>';
        
        // リンク属性の設定
        $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target) ? ' target="' . esc_attr($item->target) .'"' : '';
        $attributes .= ! empty($item->xfn) ? ' rel="'    . esc_attr($item->xfn) .'"' : '';
        $attributes .= ! empty($item->url) ? ' href="'   . esc_attr($item->url) .'"' : '';
        
        $item_output = isset($args->before) ? $args->before : '';
        
        // サブメニューがある場合の処理
        if ($has_children) {
            $item_output .= '<div class="mobile-menu-item-wrapper">';
            $item_output .= '<a' . $attributes . ' class="mobile-menu-link">';
            $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
            $item_output .= '</a>';
            $item_output .= '<button class="mobile-submenu-toggle" aria-label="' . esc_attr($item->title) . 'のサブメニューを開く" type="button">';
            $item_output .= '<svg class="submenu-icon" width="12" height="12" viewBox="0 0 12 12" fill="currentColor" aria-hidden="true"><path d="M6 8L2 4h8z"/></svg>';
            $item_output .= '</button>';
            $item_output .= '</div>';
        } else {
            $item_output .= '<a' . $attributes . ' class="mobile-menu-link">';
            $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
            $item_output .= '</a>';
        }
        
        $item_output .= isset($args->after) ? $args->after : '';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    /**
     * メニューアイテムの終了
     */
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}

// ================================
// カスタムCSS機能の追加
// ================================

// 1. 固定ページ編集画面にカスタムCSSメタボックスを追加
function riff_add_custom_css_metabox() {
    add_meta_box(
        'riff_custom_css_metabox',
        'ページ固有のカスタムCSS',
        'riff_custom_css_metabox_callback',
        'page',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'riff_add_custom_css_metabox');

// 2. メタボックスの表示内容
function riff_custom_css_metabox_callback($post) {
    // nonceフィールドを追加
    wp_nonce_field('riff_custom_css_nonce', 'riff_custom_css_nonce');
    
    // 既存のカスタムCSSを取得
    $custom_css = get_post_meta($post->ID, '_riff_custom_css', true);
    $css_enabled = get_post_meta($post->ID, '_riff_custom_css_enabled', true);
    ?>
    
    <div class="riff-custom-css-container">
        <div class="riff-css-header">
            <label for="riff_custom_css_enabled" style="display: flex; align-items: center; margin-bottom: 15px;">
                <input type="checkbox" id="riff_custom_css_enabled" name="riff_custom_css_enabled" value="1" <?php checked($css_enabled, '1'); ?> style="margin-right: 8px;">
                <strong>このページでカスタムCSSを有効にする</strong>
            </label>
            
            <div class="riff-css-info" style="background: #f0f8ff; padding: 15px; margin-bottom: 20px; border-radius: 6px; border-left: 4px solid #007cba;">
                <h4 style="margin: 0 0 10px 0; color: #007cba;">💡 カスタムCSS使用ガイド</h4>
                <ul style="margin: 0; padding-left: 20px; color: #666;">
                    <li><strong>ページ固有CSS：</strong> このページでのみ適用されます</li>
                    <li><strong>レスポンシブ対応：</strong> メディアクエリを使用してください</li>
                    <li><strong>セレクタ例：</strong> .page-id-<?php echo $post->ID; ?> で特定可能</li>
                    <li><strong>プレビュー：</strong> 「プレビュー」ボタンで確認できます</li>
                </ul>
            </div>
        </div>
        
        <div class="riff-css-editor-wrapper">
            <div class="riff-css-toolbar" style="background: #f8f9fa; padding: 10px 15px; border: 1px solid #ddd; border-bottom: none; display: flex; justify-content: space-between; align-items: center;">
                <div class="riff-css-actions">
                    <button type="button" class="button button-small" id="riff-css-format">コード整形</button>
                    <button type="button" class="button button-small" id="riff-css-clear">クリア</button>
                    <button type="button" class="button button-small" id="riff-css-template">テンプレート挿入</button>
                </div>
                <div class="riff-css-info-text" style="font-size: 12px; color: #666;">
                    Ctrl+S で保存 | Tab でインデント
                </div>
            </div>
            
            <textarea 
                id="riff_custom_css" 
                name="riff_custom_css" 
                rows="20" 
                style="width: 100%; font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace; font-size: 14px; line-height: 1.5; border: 1px solid #ddd; border-top: none; padding: 15px; resize: vertical; min-height: 300px;"
                placeholder="/* このページ専用のカスタムCSSを入力してください */

/* 基本スタイル例 */
.page-id-<?php echo $post->ID; ?> .custom-section {
    padding: 40px 0;
    background: #f8f9fa;
}

/* レスポンシブ対応例 */
@media (max-width: 768px) {
    .page-id-<?php echo $post->ID; ?> .custom-section {
        padding: 20px 0;
    }
}

/* タブレット対応例 */
@media (min-width: 769px) and (max-width: 1024px) {
    .page-id-<?php echo $post->ID; ?> .custom-section {
        padding: 30px 0;
    }
}"><?php echo esc_textarea($custom_css); ?></textarea>
        </div>
        
        <div class="riff-css-footer" style="background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 6px 6px;">
            <div class="riff-responsive-info">
                <h4 style="margin: 0 0 10px 0; color: #333;">📱 レスポンシブブレイクポイント</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; font-size: 13px;">
                    <div style="padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
                        <strong>📱 モバイル</strong><br>
                        <code>@media (max-width: 767px)</code>
                    </div>
                    <div style="padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
                        <strong>📟 タブレット</strong><br>
                        <code>@media (768px - 1024px)</code>
                    </div>
                    <div style="padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
                        <strong>💻 デスクトップ</strong><br>
                        <code>@media (min-width: 1025px)</code>
                    </div>
                    <div style="padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
                        <strong>🖥️ 大画面</strong><br>
                        <code>@media (min-width: 1400px)</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        const textarea = $('#riff_custom_css');
        const enabledCheckbox = $('#riff_custom_css_enabled');
        
        // チェックボックスの状態に応じて表示/非表示
        function toggleCSSEditor() {
            if (enabledCheckbox.is(':checked')) {
                $('.riff-css-editor-wrapper, .riff-css-footer').show();
            } else {
                $('.riff-css-editor-wrapper, .riff-css-footer').hide();
            }
        }
        
        enabledCheckbox.on('change', toggleCSSEditor);
        toggleCSSEditor(); // 初期状態を設定
        
        // コード整形ボタン
        $('#riff-css-format').on('click', function() {
            let css = textarea.val();
            // 簡単なCSS整形
            css = css.replace(/\s*{\s*/g, ' {\n    ');
            css = css.replace(/;\s*/g, ';\n    ');
            css = css.replace(/\s*}\s*/g, '\n}\n\n');
            css = css.replace(/\n\s*\n\s*\n/g, '\n\n');
            textarea.val(css);
        });
        
        // クリアボタン
        $('#riff-css-clear').on('click', function() {
            if (confirm('カスタムCSSをすべてクリアしますか？')) {
                textarea.val('');
            }
        });
        
        // テンプレート挿入ボタン
        $('#riff-css-template').on('click', function() {
            const template = `/* <?php echo get_the_title($post->ID); ?> ページ専用CSS */

/* メインコンテンツエリア */
.page-id-<?php echo $post->ID; ?> .main-content {
    /* スタイルを追加 */
}

/* レスポンシブ対応 */
@media (max-width: 767px) {
    .page-id-<?php echo $post->ID; ?> .main-content {
        /* モバイル用スタイル */
    }
}

@media (min-width: 768px) and (max-width: 1024px) {
    .page-id-<?php echo $post->ID; ?> .main-content {
        /* タブレット用スタイル */
    }
}

@media (min-width: 1025px) {
    .page-id-<?php echo $post->ID; ?> .main-content {
        /* デスクトップ用スタイル */
    }
}`;
            textarea.val(template);
        });
        
        // Ctrl+S での保存
        textarea.on('keydown', function(e) {
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                $('#publish, #save-post').click();
            }
        });
        
        // タブキーでのインデント
        textarea.on('keydown', function(e) {
            if (e.key === 'Tab') {
                e.preventDefault();
                const start = this.selectionStart;
                const end = this.selectionEnd;
                const value = this.value;
                
                this.value = value.substring(0, start) + '    ' + value.substring(end);
                this.selectionStart = this.selectionEnd = start + 4;
            }
        });
    });
    </script>
    
    <style>
    .riff-custom-css-container {
        border: 1px solid #ddd;
        border-radius: 6px;
        overflow: hidden;
    }
    
    .riff-css-header {
        padding: 20px;
        background: #fff;
    }
    
    #riff_custom_css {
        background: #f8f8f8;
        border-radius: 0;
        tab-size: 4;
    }
    
    #riff_custom_css:focus {
        outline: none;
        border-color: #007cba;
        box-shadow: 0 0 0 1px #007cba;
    }
    
    .riff-css-toolbar .button {
        margin-right: 5px;
    }
    </style>
    <?php
}

// 3. カスタムCSSの保存
function riff_save_custom_css($post_id) {
    // 自動保存やリビジョンをスキップ
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;
    
    // nonceチェック
    if (!isset($_POST['riff_custom_css_nonce']) || !wp_verify_nonce($_POST['riff_custom_css_nonce'], 'riff_custom_css_nonce')) {
        return;
    }
    
    // 権限チェック
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // 投稿タイプチェック
    if (get_post_type($post_id) !== 'page') {
        return;
    }
    
    // カスタムCSSの保存
    if (isset($_POST['riff_custom_css'])) {
        $custom_css = wp_unslash($_POST['riff_custom_css']);
        // CSSの基本的なサニタイズ
        $custom_css = wp_strip_all_tags($custom_css);
        update_post_meta($post_id, '_riff_custom_css', $custom_css);
    }
    
    // 有効/無効の保存
    $css_enabled = isset($_POST['riff_custom_css_enabled']) ? '1' : '0';
    update_post_meta($post_id, '_riff_custom_css_enabled', $css_enabled);
}
add_action('save_post', 'riff_save_custom_css');

// 4. フロントエンドでカスタムCSSを出力
function riff_output_custom_css() {
    // 固定ページの場合のみ
    if (is_page()) {
        global $post;
        $css_enabled = get_post_meta($post->ID, '_riff_custom_css_enabled', true);
        
        if ($css_enabled === '1') {
            $custom_css = get_post_meta($post->ID, '_riff_custom_css', true);
            
            if (!empty($custom_css)) {
                echo "\n<!-- Page Custom CSS (Page ID: {$post->ID}) -->\n";
                echo '<style type="text/css" id="riff-page-custom-css">' . "\n";
                echo $custom_css;
                echo "\n</style>\n";
                echo "<!-- End Page Custom CSS -->\n\n";
            }
        }
    }
    
    // グローバルカスタムCSS
    $global_css = get_option('riff_global_custom_css', '');
    if (!empty($global_css)) {
        echo "\n<!-- Global Custom CSS -->\n";
        echo '<style type="text/css" id="riff-global-custom-css">' . "\n";
        echo $global_css;
        echo "\n</style>\n";
        echo "<!-- End Global Custom CSS -->\n\n";
    }
}
add_action('wp_head', 'riff_output_custom_css', 100);

// 5. グローバルカスタムCSS設定ページを追加
function riff_global_css_admin_page() {
    add_submenu_page(
        'riff-theme-settings',
        'カスタムCSS設定',
        'カスタムCSS設定',
        'manage_options',
        'riff-custom-css-settings',
        'riff_global_css_admin_page_content'
    );
}
add_action('admin_menu', 'riff_global_css_admin_page');

// 6. グローバルカスタムCSS設定ページのコンテンツ
function riff_global_css_admin_page_content() {
    if (isset($_POST['submit'])) {
        // nonceチェック
        if (!isset($_POST['riff_global_css_nonce']) || !wp_verify_nonce($_POST['riff_global_css_nonce'], 'riff_global_css_settings')) {
            wp_die('セキュリティチェックに失敗しました。');
        }
        
        $global_css = wp_unslash($_POST['riff_global_custom_css']);
        $global_css = wp_strip_all_tags($global_css);
        update_option('riff_global_custom_css', $global_css);
        
        $css_minify = isset($_POST['riff_css_minify']) ? 1 : 0;
        update_option('riff_css_minify', $css_minify);
        
        echo '<div class="notice notice-success"><p>グローバルカスタムCSS設定を保存しました。</p></div>';
    }
    
    $global_css = get_option('riff_global_custom_css', '');
    $css_minify = get_option('riff_css_minify', 0);
    ?>
    
    <div class="wrap">
        <h1>グローバルカスタムCSS設定</h1>
        
        <div class="riff-admin-header">
            <h1>グローバルカスタムCSS設定</h1>
            <p>サイト全体に適用されるカスタムCSSを設定できます。すべてのページで読み込まれます。</p>
        </div>
        
        <form method="post" action="">
            <?php wp_nonce_field('riff_global_css_settings', 'riff_global_css_nonce'); ?>
            
            <!-- 設定オプション -->
            <div style="background: #f0f8ff; padding: 20px; margin-bottom: 20px; border-radius: 6px; border-left: 4px solid #007cba;">
                <h3 style="margin: 0 0 15px 0; color: #007cba;">⚙️ CSS設定オプション</h3>
                <label style="display: flex; align-items: center;">
                    <input type="checkbox" name="riff_css_minify" value="1" <?php checked($css_minify, 1); ?> style="margin-right: 8px;">
                    <span>CSSを圧縮する（本番環境推奨）</span>
                </label>
                <p style="margin: 10px 0 0 0; font-size: 13px; color: #666;">
                    ※ CSSを圧縮してファイルサイズを削減し、サイトの読み込み速度を向上させます。
                </p>
            </div>
            
            <!-- CSSエディター -->
            <div class="riff-global-css-container" style="border: 1px solid #ddd; border-radius: 6px; overflow: hidden;">
                <div class="riff-css-toolbar" style="background: #f8f9fa; padding: 15px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                    <div class="riff-css-actions">
                        <button type="button" class="button button-small" id="riff-global-css-format">コード整形</button>
                        <button type="button" class="button button-small" id="riff-global-css-clear">クリア</button>
                        <button type="button" class="button button-small" id="riff-global-css-template">基本テンプレート</button>
                        <button type="button" class="button button-small" id="riff-global-css-responsive">レスポンシブテンプレート</button>
                    </div>
                    <div class="riff-css-info-text" style="font-size: 12px; color: #666;">
                        Ctrl+S で保存 | Tab でインデント | 全ページに適用されます
                    </div>
                </div>
                
                <textarea 
                    id="riff_global_custom_css" 
                    name="riff_global_custom_css" 
                    rows="25" 
                    style="width: 100%; font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace; font-size: 14px; line-height: 1.5; border: none; padding: 20px; resize: vertical; min-height: 400px; background: #f8f8f8;"
                    placeholder="/* サイト全体に適用されるカスタムCSSを入力してください */

/* 基本スタイル例 */
.custom-global-style {
    /* 全ページで有効なスタイル */
}

/* レスポンシブ対応例 */
@media (max-width: 767px) {
    /* モバイル専用スタイル */
}

@media (min-width: 768px) and (max-width: 1024px) {
    /* タブレット専用スタイル */
}

@media (min-width: 1025px) {
    /* デスクトップ専用スタイル */
}"><?php echo esc_textarea($global_css); ?></textarea>
            </div>
            
            <!-- レスポンシブガイド -->
            <div style="background: #f8f9fa; padding: 20px; margin-top: 20px; border-radius: 6px; border: 1px solid #ddd;">
                <h3 style="margin: 0 0 15px 0; color: #333;">📱 レスポンシブデザインガイド</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div style="padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
                        <h4 style="margin: 0 0 10px 0; color: #d63638;">📱 モバイル (〜767px)</h4>
                        <code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-size: 12px;">@media (max-width: 767px) { }</code>
                        <p style="margin: 10px 0 0 0; font-size: 13px; color: #666;">スマートフォン向けスタイル</p>
                    </div>
                    <div style="padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
                        <h4 style="margin: 0 0 10px 0; color: #ff9800;">📟 タブレット (768px〜1024px)</h4>
                        <code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-size: 12px;">@media (768px - 1024px) { }</code>
                        <p style="margin: 10px 0 0 0; font-size: 13px; color: #666;">タブレット向けスタイル</p>
                    </div>
                    <div style="padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
                        <h4 style="margin: 0 0 10px 0; color: #4caf50;">💻 デスクトップ (1025px〜)</h4>
                        <code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-size: 12px;">@media (min-width: 1025px) { }</code>
                        <p style="margin: 10px 0 0 0; font-size: 13px; color: #666;">デスクトップ向けスタイル</p>
                    </div>
                    <div style="padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
                        <h4 style="margin: 0 0 10px 0; color: #2196f3;">🖥️ 大画面 (1400px〜)</h4>
                        <code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-size: 12px;">@media (min-width: 1400px) { }</code>
                        <p style="margin: 10px 0 0 0; font-size: 13px; color: #666;">大画面ディスプレイ向け</p>
                    </div>
                </div>
            </div>
            
            <?php submit_button('グローバルCSS設定を保存', 'primary', 'submit', false, array('style' => 'font-size: 16px; padding: 12px 24px; margin-top: 20px;')); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        const textarea = $('#riff_global_custom_css');
        
        // コード整形ボタン
        $('#riff-global-css-format').on('click', function() {
            let css = textarea.val();
            css = css.replace(/\s*{\s*/g, ' {\n    ');
            css = css.replace(/;\s*/g, ';\n    ');
            css = css.replace(/\s*}\s*/g, '\n}\n\n');
            css = css.replace(/\n\s*\n\s*\n/g, '\n\n');
            textarea.val(css);
        });
        
        // クリアボタン
        $('#riff-global-css-clear').on('click', function() {
            if (confirm('グローバルカスタムCSSをすべてクリアしますか？')) {
                textarea.val('');
            }
        });
        
        // 基本テンプレート
        $('#riff-global-css-template').on('click', function() {
            const template = `/* <?php echo get_option('riff_site_title', get_bloginfo('name')); ?> - グローバルカスタムCSS */

/* 全体的なスタイル調整 */
body {
    /* サイト全体に適用するスタイル */
}

/* ヘッダーカスタマイズ */
.header {
    /* ヘッダーのカスタマイズ */
}

/* フッターカスタマイズ */
.footer {
    /* フッターのカスタマイズ */
}

/* コンテンツエリア */
.main-content {
    /* メインコンテンツのカスタマイズ */
}`;
            textarea.val(template);
        });
        
        // レスポンシブテンプレート
        $('#riff-global-css-responsive').on('click', function() {
            const template = `/* レスポンシブデザインテンプレート */

/* ベーススタイル（全デバイス共通） */
.responsive-element {
    /* 共通スタイル */
}

/* モバイル専用 (〜767px) */
@media (max-width: 767px) {
    .responsive-element {
        /* モバイル用スタイル */
        font-size: 14px;
        padding: 10px;
    }
    
    /* モバイルでの非表示例 */
    .hide-mobile {
        display: none;
    }
}

/* タブレット専用 (768px〜1024px) */
@media (min-width: 768px) and (max-width: 1024px) {
    .responsive-element {
        /* タブレット用スタイル */
        font-size: 16px;
        padding: 15px;
    }
}

/* デスクトップ専用 (1025px〜) */
@media (min-width: 1025px) {
    .responsive-element {
        /* デスクトップ用スタイル */
        font-size: 18px;
        padding: 20px;
    }
    
    /* デスクトップでの非表示例 */
    .hide-desktop {
        display: none;
    }
}

/* 大画面専用 (1400px〜) */
@media (min-width: 1400px) {
    .responsive-element {
        /* 大画面用スタイル */
        font-size: 20px;
        padding: 25px;
    }
}

/* 高解像度ディスプレイ対応 */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    /* Retinaディスプレイ用スタイル */
}`;
            textarea.val(template);
        });
        
        // Ctrl+S での保存
        textarea.on('keydown', function(e) {
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                $('input[type="submit"]').click();
            }
        });
        
        // タブキーでのインデント
        textarea.on('keydown', function(e) {
            if (e.key === 'Tab') {
                e.preventDefault();
                const start = this.selectionStart;
                const end = this.selectionEnd;
                const value = this.value;
                
                this.value = value.substring(0, start) + '    ' + value.substring(end);
                this.selectionStart = this.selectionEnd = start + 4;
            }
        });
    });
    </script>
    
    <style>
    #riff_global_custom_css:focus {
        outline: none;
        box-shadow: inset 0 0 0 2px #007cba;
    }
    
    .riff-css-toolbar .button {
        margin-right: 8px;
    }
    
    .riff-global-css-container {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    </style>
    <?php
}

// 7. 管理画面のページ一覧にカスタムCSS使用状況を表示
function riff_add_custom_css_column($columns) {
    $columns['riff_custom_css'] = 'カスタムCSS';
    return $columns;
}
add_filter('manage_pages_columns', 'riff_add_custom_css_column');

function riff_show_custom_css_column($column, $post_id) {
    if ($column === 'riff_custom_css') {
        $css_enabled = get_post_meta($post_id, '_riff_custom_css_enabled', true);
        if ($css_enabled === '1') {
            echo '<span style="color: #46b450; font-weight: bold;">✓ 有効</span>';
        } else {
            echo '<span style="color: #ccc;">無効</span>';
        }
    }
}
add_action('manage_pages_custom_column', 'riff_show_custom_css_column', 10, 2);

// 8. CSSの圧縮機能
function riff_minify_css($css) {
    if (!get_option('riff_css_minify', 0)) {
        return $css;
    }
    
    // CSSの基本的な圧縮
    $css = preg_replace('/\/\*.*?\*\//s', '', $css); // コメント削除
    $css = preg_replace('/\s+/', ' ', $css); // 余分なスペース削除
    $css = str_replace(array('; ', ' {', '{ ', ' }', '} ', ': '), array(';', '{', '{', '}', '}', ':'), $css);
    $css = trim($css);
    
    return $css;
}


/**
 * ヒーローセクション関連の関数とスタイル読み込み
 */

// ヒーローセクション用CSSとJSの読み込み
function enqueue_hero_section_assets() {
    // front-pageではヒーローセクション用CSSを読み込まない
    if (!is_front_page()) {
        wp_enqueue_style(
            'hero-section-style',
            get_template_directory_uri() . '/assets/css/hero-section.css',
            array(),
            '1.0.1'
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_hero_section_assets');

// 管理画面でメディアアップローダー用JSとCSSを読み込み
function enqueue_hero_admin_assets($hook) {
    if ('post.php' !== $hook && 'post-new.php' !== $hook) {
        return;
    }
    
    wp_enqueue_media();
    wp_enqueue_script(
        'hero-admin-js',
        get_template_directory_uri() . '/assets/js/hero-admin.js',
        array('jquery'),
        '1.0.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'enqueue_hero_admin_assets');

/**
 * ヒーローセクションが必要なページかどうかを判定
 * 
 * @return bool
 */
function should_display_hero_section() {
    // front-pageでは表示しない
    if (is_front_page()) {
        return false;
    }
    
    // 管理画面では表示しない
    if (is_admin()) {
        return false;
    }
    
    // カスタマイザーでヒーローセクションを無効にできるオプション
    $show_hero = get_theme_mod('show_hero_section', true);
    if (!$show_hero) {
        return false;
    }
    
    // 個別のページ・投稿でヒーローセクションを無効にするオプション
    if (is_singular()) {
        $hide_hero = get_post_meta(get_the_ID(), 'hide_hero_section', true);
        if ($hide_hero) {
            return false;
        }
    }
    
    return true;
}

/**
 * ヒーローセクションの表示
 */
function display_hero_section() {
    if (should_display_hero_section()) {
        get_template_part('template-parts/hero-section');
    }
}

/**
 * カスタマイザーにヒーローセクション設定を追加
 */
function hero_section_customizer_settings($wp_customize) {
    // ヒーローセクション設定セクションを追加
    $wp_customize->add_section('hero_section_settings', array(
        'title'    => 'ヒーローセクション設定',
        'priority' => 30,
    ));
    
    // ヒーローセクション表示/非表示設定
    $wp_customize->add_setting('show_hero_section', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('show_hero_section', array(
        'label'   => 'ヒーローセクションを表示する',
        'section' => 'hero_section_settings',
        'type'    => 'checkbox',
    ));
    
    // デフォルト背景画像設定
    $wp_customize->add_setting('hero_default_background', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'hero_default_background', array(
        'label'   => 'デフォルト背景画像',
        'section' => 'hero_section_settings',
    )));
}
add_action('customize_register', 'hero_section_customizer_settings');

/**
 * 投稿・ページ編集画面にヒーローセクション設定メタボックスを追加
 */
function add_hero_section_meta_boxes() {
    add_meta_box(
        'hero-section-settings',
        'ヒーローセクション設定',
        'hero_section_meta_box_callback',
        array('post', 'page'),
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_hero_section_meta_boxes');

/**
 * メタボックスのコールバック関数
 */
function hero_section_meta_box_callback($post) {
    wp_nonce_field('hero_section_meta_box', 'hero_section_nonce');
    
    $hide_hero = get_post_meta($post->ID, 'hide_hero_section', true);
    $hero_title = get_post_meta($post->ID, 'hero_title', true);
    $hero_subtitle = get_post_meta($post->ID, 'hero_subtitle', true);
    $hero_background_image = get_post_meta($post->ID, 'hero_background_image', true);
    
    // 背景画像のプレビュー用
    $image_preview = '';
    $image_text = '画像を選択';
    
    if ($hero_background_image) {
        $image_url = wp_get_attachment_image_url($hero_background_image, 'medium');
        if ($image_url) {
            $image_preview = '<img src="' . esc_url($image_url) . '" style="max-width: 100%; height: auto; margin-top: 10px;" />';
            $image_text = '画像を変更';
        }
    }
    ?>
    <table class="form-table">
        <tr>
            <th><label for="hide_hero_section">ヒーローセクション</label></th>
            <td>
                <input type="checkbox" id="hide_hero_section" name="hide_hero_section" value="1" <?php checked($hide_hero, 1); ?> />
                <label for="hide_hero_section">ヒーローセクションを非表示にする</label>
            </td>
        </tr>
        <tr>
            <th><label for="hero_background_image">背景画像</label></th>
            <td>
                <input type="hidden" id="hero_background_image" name="hero_background_image" value="<?php echo esc_attr($hero_background_image); ?>" />
                <button type="button" class="button" id="hero_upload_image_button"><?php echo esc_html($image_text); ?></button>
                <button type="button" class="button" id="hero_remove_image_button" style="<?php echo $hero_background_image ? '' : 'display:none;'; ?>">画像を削除</button>
                <div id="hero_image_preview"><?php echo $image_preview; ?></div>
                <p class="description">推奨サイズ: 1920×1080px以上</p>
            </td>
        </tr>
        <tr>
            <th><label for="hero_title">カスタムタイトル</label></th>
            <td>
                <input type="text" id="hero_title" name="hero_title" value="<?php echo esc_attr($hero_title); ?>" style="width: 100%;" />
                <p class="description">空白の場合はページタイトルが使用されます</p>
            </td>
        </tr>
        <tr>
            <th><label for="hero_subtitle">サブタイトル</label></th>
            <td>
                <textarea id="hero_subtitle" name="hero_subtitle" rows="3" style="width: 100%;"><?php echo esc_textarea($hero_subtitle); ?></textarea>
                <p class="description">空白の場合は抜粋が使用されます</p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * メタボックスの保存処理
 */
function save_hero_section_meta_box($post_id) {
    if (!isset($_POST['hero_section_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['hero_section_nonce'], 'hero_section_meta_box')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // メタデータの保存
    $hide_hero = isset($_POST['hide_hero_section']) ? 1 : 0;
    update_post_meta($post_id, 'hide_hero_section', $hide_hero);
    
    if (isset($_POST['hero_background_image'])) {
        $background_image_id = intval($_POST['hero_background_image']);
        if ($background_image_id > 0) {
            update_post_meta($post_id, 'hero_background_image', $background_image_id);
        } else {
            delete_post_meta($post_id, 'hero_background_image');
        }
    }
    
    if (isset($_POST['hero_title'])) {
        update_post_meta($post_id, 'hero_title', sanitize_text_field($_POST['hero_title']));
    }
    
    if (isset($_POST['hero_subtitle'])) {
        update_post_meta($post_id, 'hero_subtitle', sanitize_textarea_field($_POST['hero_subtitle']));
    }
}
add_action('save_post', 'save_hero_section_meta_box');

// ================================
// 新しいNEWS・BLOGセクション用のアセット読み込み
// ================================

// NEWSとBLOGセクション用のスタイルとスクリプトを読み込み
function riff_enqueue_news_blog_assets() {
    // NEWSとBLOGセクション用CSSはriff-common-bundle.cssに統合済み

    // NEWSとBLOGセクション用JavaScript
    wp_enqueue_script(
        'riff-news-blog-sections',
        get_template_directory_uri() . '/assets/js/news-blog-sections.js',
        array('jquery'),
        '1.0.0',
        true
    );
    
    // 記事詳細ページ用アセット
    if (is_single()) {
        wp_enqueue_style(
            'riff-single-post',
            get_template_directory_uri() . '/assets/css/single-post.css',
            array('riff-style'),
            '1.0.1'
        );
        
        wp_enqueue_script(
            'riff-single-post',
            get_template_directory_uri() . '/assets/js/single-post.js',
            array('jquery'),
            '1.0.3',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'riff_enqueue_news_blog_assets');

// ================================
// 投稿タイプ区別機能（NEWS / BLOG）
// ================================

// 投稿編集画面にNEWS/BLOG区別メタボックスを追加
function riff_add_post_type_metabox() {
    add_meta_box(
        'riff_post_type_flag',
        '投稿タイプ設定',
        'riff_post_type_metabox_callback',
        'post',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'riff_add_post_type_metabox');

// メタボックスのコールバック関数
function riff_post_type_metabox_callback($post) {
    wp_nonce_field('riff_post_type_metabox', 'riff_post_type_nonce');
    
    $post_type_flag = get_post_meta($post->ID, 'post_type_flag', true);
    $post_type_flag = $post_type_flag ? $post_type_flag : 'news'; // デフォルトはNEWS
    ?>
    <div class="riff-post-type-selection">
        <p><strong>この投稿の表示先を選択してください：</strong></p>
        
        <label style="display: block; margin-bottom: 10px;">
            <input type="radio" name="post_type_flag" value="news" <?php checked($post_type_flag, 'news'); ?>>
            📰 NEWSセクション
        </label>
        
        <label style="display: block; margin-bottom: 10px;">
            <input type="radio" name="post_type_flag" value="blog" <?php checked($post_type_flag, 'blog'); ?>>
            ✍️ BLOGセクション
        </label>
        
        <label style="display: block; margin-bottom: 10px;">
            <input type="radio" name="post_type_flag" value="both" <?php checked($post_type_flag, 'both'); ?>>
            🔄 両方のセクション
        </label>
        
        <div style="background: #f0f8ff; padding: 12px; border-radius: 6px; border-left: 4px solid #007cba; margin-top: 15px;">
            <p style="margin: 0; font-size: 13px; color: #666;">
                <strong>💡 設定について：</strong><br>
                • <strong>NEWS:</strong> 会社のお知らせ、プレスリリース等<br>
                • <strong>BLOG:</strong> 技術記事、コラム、ノウハウ等<br>
                • <strong>両方:</strong> 重要な記事を両セクションに表示
            </p>
        </div>
    </div>
    
    <style>
    .riff-post-type-selection input[type="radio"] {
        margin-right: 8px;
    }
    .riff-post-type-selection label {
        cursor: pointer;
        padding: 8px;
        border-radius: 4px;
        transition: background-color 0.2s ease;
    }
    .riff-post-type-selection label:hover {
        background-color: #f8f9fa;
    }
    </style>
    <?php
}

// メタボックスの保存処理
function riff_save_post_type_metabox($post_id) {
    if (!isset($_POST['riff_post_type_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['riff_post_type_nonce'], 'riff_post_type_metabox')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['post_type_flag'])) {
        $post_type_flag = sanitize_text_field($_POST['post_type_flag']);
        update_post_meta($post_id, 'post_type_flag', $post_type_flag);
    }
}
add_action('save_post', 'riff_save_post_type_metabox');

// ================================
// BLOG記事公開時にNEWSお知らせを自動生成
// ================================

// BLOG記事が公開された際、「ブログを更新しました」というNEWS投稿を自動作成する
// （手動公開・予約投稿のcronによる自動公開のどちらでも動作する）
function riff_auto_create_news_from_blog_post($new_status, $old_status, $post) {
    // 投稿(post)がpublishに「新しく」遷移した場合のみ対象（既にpublishの記事の再保存では発火しない）
    if ($post->post_type !== 'post' || $new_status !== 'publish' || $old_status === 'publish') {
        return;
    }

    // BLOG記事のみ対象（'both'は既にNEWS欄にも表示されるため対象外、'news'は対象外で無限生成を防止）
    $flag = get_post_meta($post->ID, 'post_type_flag', true);
    if ($flag !== 'blog') {
        return;
    }

    // 「業務効率化」カテゴリー（term_id=8）の記事のみお知らせ化する
    if (!has_category(8, $post)) {
        return;
    }

    // 既にこの記事からお知らせを生成済みなら重複生成しない
    $already = get_posts(array(
        'post_type'      => 'post',
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_auto_news_source_id',
        'meta_value'     => $post->ID,
    ));
    if (!empty($already)) {
        return;
    }

    $categories = wp_get_post_categories($post->ID);

    $news_title   = 'ブログを更新しました｜' . $post->post_title;
    $news_excerpt = 'ブログに新しい記事を公開しました。';
    $news_content = '<p>' . esc_html($news_excerpt) . '</p>'
        . '<p><a href="' . esc_url(get_permalink($post->ID)) . '">' . esc_html($post->post_title) . '</a></p>';

    $news_id = wp_insert_post(array(
        'post_title'   => $news_title,
        'post_excerpt' => $news_excerpt,
        'post_content' => $news_content,
        'post_type'    => 'post',
        'post_status'  => 'publish',
        'post_category'=> $categories,
        'meta_input'   => array(
            'post_type_flag'        => 'news',
            '_auto_news_source_id'  => $post->ID,
        ),
    ), true);

    if (is_wp_error($news_id)) {
        return;
    }

    // アイキャッチ画像を元記事から引き継ぐ
    $thumb_id = get_post_thumbnail_id($post->ID);
    if ($thumb_id) {
        update_post_meta($news_id, '_thumbnail_id', $thumb_id);
        update_post_meta($news_id, 'hero_background_image', $thumb_id);
    }

    // AIOSEOのタイトル・ディスクリプションも簡易設定
    global $wpdb;
    $wpdb->insert(
        "{$wpdb->prefix}aioseo_posts",
        array(
            'post_id'     => $news_id,
            'title'       => $news_title,
            'description' => $news_excerpt . '「' . $post->post_title . '」を公開しました。',
            'created'     => current_time('mysql'),
            'updated'     => current_time('mysql'),
        )
    );
}
add_action('transition_post_status', 'riff_auto_create_news_from_blog_post', 10, 3);

// 管理画面の投稿一覧に投稿タイプを表示
function riff_add_post_type_column($columns) {
    $columns['post_type_flag'] = '投稿タイプ';
    return $columns;
}
add_filter('manage_posts_columns', 'riff_add_post_type_column');

function riff_show_post_type_column($column, $post_id) {
    if ($column === 'post_type_flag') {
        $post_type_flag = get_post_meta($post_id, 'post_type_flag', true);
        $post_type_flag = $post_type_flag ? $post_type_flag : 'news';
        
        switch ($post_type_flag) {
            case 'news':
                echo '<span style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">📰 NEWS</span>';
                break;
            case 'blog':
                echo '<span style="background: #f3e5f5; color: #7b1fa2; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">✍️ BLOG</span>';
                break;
            case 'both':
                echo '<span style="background: #e8f5e8; color: #388e3c; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">🔄 両方</span>';
                break;
            default:
                echo '<span style="background: #fafafa; color: #757575; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">未設定</span>';
        }
    }
}
add_action('manage_posts_custom_column', 'riff_show_post_type_column', 10, 2);

// ================================
// フロントページのセクション機能改良
// ================================

// front-page.phpのNEWSセクション表示関数を改良
function riff_display_news_section_improved() {
    $news_title = get_option('riff_news_title', 'NEWS');
    $news_subtitle = get_option('riff_news_subtitle', '最新情報');
    $news_button_text = get_option('riff_news_button_text', '一覧を見る');
    
    // 投稿アーカイブページのURLを取得
    $posts_page_id = get_option('page_for_posts');
    if ($posts_page_id) {
        $default_archive_url = get_permalink($posts_page_id);
    } else {
        $default_archive_url = home_url('/');
        if (get_post_type_archive_link('post')) {
            $default_archive_url = get_post_type_archive_link('post');
        }
    }
    
    $news_button_url = get_option('riff_news_button_url', $default_archive_url);
    
    // NEWS記事を取得（post_type_flagがnewsまたはbothの記事）
    $recent_news = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => 6,
        'post_status' => 'publish',
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'post_type_flag',
                'value' => 'news',
                'compare' => '='
            ),
            array(
                'key' => 'post_type_flag',
                'value' => 'both',
                'compare' => '='
            ),
            array(
                'key' => 'post_type_flag',
                'compare' => 'NOT EXISTS'  // 未設定の場合はNEWSとして扱う
            )
        )
    ));
    ?>
    
    <section class="riff-news-section-fullwidth" id="news">
        <div class="riff-news-container">
            <div class="riff-news-header fade-in">
                <h2 class="riff-news-title-new"><?php echo esc_html($news_title); ?></h2>
                <p class="riff-news-subtitle-new"><?php echo esc_html($news_subtitle); ?></p>
            </div>
            
            <div class="riff-news-filters-new fade-in" data-delay="0.1s">
                <div class="riff-news-filter-buttons">
                    <button class="riff-news-filter-btn active" data-filter="all">ALL</button>
                    <?php
                    // 実際にNEWS記事で使用されているカテゴリーを取得
                    $used_categories = get_categories(array(
                        'hide_empty' => true,
                        'number' => 5,
                        'orderby' => 'count',
                        'order' => 'DESC'
                    ));

                    foreach ($used_categories as $category) {
                        echo '<button class="riff-news-filter-btn" data-filter="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</button>';
                    }
                    ?>
                </div>
            </div>
            
            <?php if ($recent_news->have_posts()): ?>
            <div class="riff-news-grid-new">
                <?php $count = 0; while ($recent_news->have_posts() && $count < 6): $recent_news->the_post(); $count++; ?>
                <?php 
                // 記事のカテゴリーを取得してフィルター用の属性を設定
                $post_categories = get_the_category();
                $category_slugs = array();
                if (!empty($post_categories)) {
                    foreach ($post_categories as $cat) {
                        $category_slugs[] = $cat->slug;
                    }
                }
                $category_data = !empty($category_slugs) ? implode(' ', $category_slugs) : 'uncategorized';
                ?>
                <article class="riff-news-item-new fade-in" data-delay="<?php echo 0.1 + ($count * 0.1); ?>s" data-category="<?php echo esc_attr($category_data); ?>">
                    <a href="<?php the_permalink(); ?>" class="riff-news-link">
                        <?php if (has_post_thumbnail()): ?>
                        <div class="riff-news-image-new">
                            <?php the_post_thumbnail('medium', array('class' => 'img-responsive')); ?>
                        </div>
                        <?php endif; ?>
                        <div class="riff-news-content-new">
                            <div class="riff-news-meta-new">
                                <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('Y.m.d'); ?></time>
                                <?php 
                                // 実際の投稿のカテゴリーを表示
                                $categories = get_the_category();
                                if (!empty($categories)) {
                                    $primary_category = $categories[0];
                                    echo '<span class="riff-news-category-new">' . esc_html($primary_category->name) . '</span>';
                                } else {
                                    echo '<span class="riff-news-category-new">NEWS</span>';
                                }
                                ?>
                            </div>
                            <h3 class="riff-news-item-title-new"><?php the_title(); ?></h3>
                        </div>
                    </a>
                </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            
            <div class="riff-news-button-wrap-new fade-in" data-delay="0.8s">
                <a href="<?php echo esc_url($news_button_url); ?>" class="riff-news-button-new">
                    <?php echo esc_html($news_button_text); ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                    </svg>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

// BLOGセクション表示関数を改良
function riff_display_blog_section_improved() {
    $blog_title = get_option('riff_blog_title', 'BLOG');
    $blog_subtitle = get_option('riff_blog_subtitle', '最新記事をお届け');
    $blog_button_text = get_option('riff_blog_button_text', 'ブログ一覧');
    
    // 投稿アーカイブページのURLを取得
    $posts_page_id = get_option('page_for_posts');
    if ($posts_page_id) {
        $default_archive_url = get_permalink($posts_page_id);
    } else {
        $default_archive_url = home_url('/');
        if (get_post_type_archive_link('post')) {
            $default_archive_url = get_post_type_archive_link('post');
        }
    }
    
    $blog_button_url = get_option('riff_blog_button_url', $default_archive_url);
    
    // BLOG記事を取得（post_type_flagがblogまたはbothの記事）
    $blog_posts = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => 4,
        'post_status' => 'publish',
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'post_type_flag',
                'value' => 'blog',
                'compare' => '='
            ),
            array(
                'key' => 'post_type_flag',
                'value' => 'both',
                'compare' => '='
            )
        )
    ));
    ?>
    
    <section class="riff-blog-section-fullwidth" id="blog">
        <div class="riff-blog-container">
            <div class="riff-blog-header fade-in">
                <h2 class="riff-blog-title-new"><?php echo esc_html($blog_title); ?></h2>
                <p class="riff-blog-subtitle-new"><?php echo esc_html($blog_subtitle); ?></p>
            </div>
            
            <?php if ($blog_posts->have_posts()): ?>
            <div class="riff-blog-grid-new">
                <?php $count = 0; while ($blog_posts->have_posts() && $count < 4): $blog_posts->the_post(); $count++; ?>
                <article class="riff-blog-item-new fade-in" data-delay="<?php echo 0.1 + ($count * 0.1); ?>s">
                    <a href="<?php the_permalink(); ?>" class="riff-blog-link">
                        <?php if (has_post_thumbnail()): ?>
                        <div class="riff-blog-image-new">
                            <?php the_post_thumbnail('medium', array('class' => 'img-responsive')); ?>
                        </div>
                        <?php endif; ?>
                        <div class="riff-blog-content-new">
                            <div class="riff-blog-meta-new">
                                <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('Y.m.d'); ?></time>
                            </div>
                            <h3 class="riff-blog-item-title-new"><?php the_title(); ?></h3>
                            <p class="riff-blog-excerpt-new"><?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?></p>
                        </div>
                    </a>
                </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            
            <div class="riff-blog-button-wrap-new fade-in" data-delay="0.6s">
                <a href="<?php echo esc_url($blog_button_url); ?>" class="riff-blog-button-new">
                    <?php echo esc_html($blog_button_text); ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                    </svg>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
}


// ================================
// CSS変数の更新（新しいセクション対応）
// ================================

// 既存のCSS出力に新しいセクション用の変数を追加
function riff_output_news_blog_css_vars() {
    ?>
    <style type="text/css">
        :root {
            /* 既存の変数に加えて新しいセクション用変数を追加 */
            --news-section-bg: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            --blog-section-bg: #ffffff;
            --card-hover-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            --card-border-radius: 16px;
            --button-gradient: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            --filter-button-active-shadow: 0 6px 20px rgba(0, 124, 186, 0.4);
        }
        
        /* ローディング状態のスタイル */
        .loading-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-light);
        }
        
        .loading-state .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <?php
}
add_action('wp_head', 'riff_output_news_blog_css_vars', 99);


// ================================
// 投稿タイプによるアーカイブページフィルター機能
// ================================

// URLパラメータで投稿タイプをフィルター
function riff_filter_posts_by_type($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_home() || is_archive()) {
            // URLパラメータでpost_typeが指定されている場合
            if (isset($_GET['post_type']) && in_array($_GET['post_type'], ['news', 'blog', 'both'])) {
                $post_type = sanitize_text_field($_GET['post_type']);
                
                $meta_query = array();
                if ($post_type === 'news') {
                    $meta_query = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'post_type_flag',
                            'value' => 'news',
                            'compare' => '='
                        ),
                        array(
                            'key' => 'post_type_flag',
                            'value' => 'both',
                            'compare' => '='
                        ),
                        array(
                            'key' => 'post_type_flag',
                            'compare' => 'NOT EXISTS'
                        )
                    );
                } elseif ($post_type === 'blog') {
                    $meta_query = array(
                        'relation' => 'OR',
                        array(
                            'key' => 'post_type_flag',
                            'value' => 'blog',
                            'compare' => '='
                        ),
                        array(
                            'key' => 'post_type_flag',
                            'value' => 'both',
                            'compare' => '='
                        )
                    );
                }
                
                if (!empty($meta_query)) {
                    $query->set('meta_query', $meta_query);
                }
            }
        }
    }
}
add_action('pre_get_posts', 'riff_filter_posts_by_type');

// ================================
// アーカイブページでのフィルターボタン表示
// ================================

// アーカイブページにフィルターボタンを表示する関数
function riff_display_archive_filters() {
    if (!is_home() && !is_archive()) {
        return;
    }
    
    $current_filter = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : 'all';
    $base_url = remove_query_arg('post_type');
    ?>
    
    <div class="archive-type-filters" style="text-align: center; margin: 30px 0; padding: 20px; background: #f8f9fa; border-radius: 12px;">
        <h3 style="margin-bottom: 20px; color: var(--text-color);">投稿タイプで絞り込み</h3>
        <div class="filter-buttons" style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
            <a href="<?php echo esc_url($base_url); ?>" 
               class="archive-filter-btn <?php echo $current_filter === 'all' ? 'active' : ''; ?>"
               style="background: <?php echo $current_filter === 'all' ? 'var(--primary-color)' : '#ffffff'; ?>; color: <?php echo $current_filter === 'all' ? 'white' : 'var(--text-color)'; ?>; padding: 12px 24px; border: 2px solid var(--primary-color); border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                📋 ALL
            </a>
            <a href="<?php echo esc_url(add_query_arg('post_type', 'news', $base_url)); ?>" 
               class="archive-filter-btn <?php echo $current_filter === 'news' ? 'active' : ''; ?>"
               style="background: <?php echo $current_filter === 'news' ? 'var(--primary-color)' : '#ffffff'; ?>; color: <?php echo $current_filter === 'news' ? 'white' : 'var(--text-color)'; ?>; padding: 12px 24px; border: 2px solid var(--primary-color); border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                📰 NEWS
            </a>
            <a href="<?php echo esc_url(add_query_arg('post_type', 'blog', $base_url)); ?>" 
               class="archive-filter-btn <?php echo $current_filter === 'blog' ? 'active' : ''; ?>"
               style="background: <?php echo $current_filter === 'blog' ? 'var(--primary-color)' : '#ffffff'; ?>; color: <?php echo $current_filter === 'blog' ? 'white' : 'var(--text-color)'; ?>; padding: 12px 24px; border: 2px solid var(--primary-color); border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                ✍️ BLOG
            </a>
        </div>
    </div>
    
    <style>
    .archive-filter-btn:hover {
        background: var(--primary-color) !important;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 124, 186, 0.3);
    }
    </style>
    <?php
}

/**
 * BLOG セクション拡張機能の読み込み
 */
require_once get_template_directory() . '/inc/blog-enhanced-assets.php';

/**
 * Instagram連携機能の読み込み
 */
require_once get_template_directory() . '/inc/instagram-integration.php';

/**
 * GENBOX導線機能の読み込み
 */
require_once get_template_directory() . '/inc/genbox-cta.php';

/**
 * 実績詳細ページ機能の読み込み
 */
require_once get_template_directory() . '/inc/portfolio-detail.php';

/**
 * 記事ごとのFAQ機能の読み込み
 */
require_once get_template_directory() . '/inc/faq-schema.php';

/**
 * 日程予約（Googleカレンダー連携のオンライン面談予約）の読み込み
 */
require_once get_template_directory() . '/inc/booking/booking-core.php';
require_once get_template_directory() . '/inc/booking/booking-actions.php';
require_once get_template_directory() . '/inc/booking/booking-rest.php';
require_once get_template_directory() . '/inc/booking/booking-front.php';
require_once get_template_directory() . '/inc/booking/booking-admin.php';

// ================================
// BLOGスライダー機能の追加（修正版）
// ================================

/**
 * BLOGスライダー専用関数の読み込み
 */
require_once get_template_directory() . '/inc/blog-slider-functions.php';

/**
 * BLOGセクション表示の条件分岐
 * スライダーを有効にするかどうかの設定
 */
function riff_is_blog_slider_enabled() {
    // 管理画面で設定できるオプション
    $slider_enabled = get_option('riff_blog_use_slider', true);
    
    // フィルターで外部から制御可能
    return apply_filters('riff_blog_slider_enabled', $slider_enabled);
}


/**
 * カスタマイザーにBLOGスライダー設定を追加
 */
function riff_blog_slider_customizer_settings($wp_customize) {
    // BLOGスライダーセクション
    $wp_customize->add_section('riff_blog_slider_section', array(
        'title'    => 'BLOGスライダー設定',
        'priority' => 40,
    ));
    
    // スライダー有効/無効
    $wp_customize->add_setting('riff_blog_use_slider', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('riff_blog_use_slider', array(
        'label'   => 'BLOGセクションでスライダーを使用する',
        'section' => 'riff_blog_slider_section',
        'type'    => 'checkbox',
    ));
    
    // デスクトップ表示数
    $wp_customize->add_setting('riff_blog_slider_desktop_slides', array(
        'default'           => 3,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('riff_blog_slider_desktop_slides', array(
        'label'   => 'デスクトップ表示数',
        'section' => 'riff_blog_slider_section',
        'type'    => 'range',
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 6,
            'step' => 1,
        ),
    ));
    
    // 自動再生
    $wp_customize->add_setting('riff_blog_slider_autoplay', array(
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('riff_blog_slider_autoplay', array(
        'label'   => '自動再生を有効にする',
        'section' => 'riff_blog_slider_section',
        'type'    => 'checkbox',
    ));
}
add_action('customize_register', 'riff_blog_slider_customizer_settings');

/**
 * BLOGスライダーの初期設定
 * テーマアクティベーション時に実行
 */
function riff_blog_slider_activation_setup() {
    // デフォルト設定
    if (get_option('riff_blog_use_slider') === false) {
        update_option('riff_blog_use_slider', true);
    }
    if (!get_option('riff_blog_slider_desktop_slides')) {
        update_option('riff_blog_slider_desktop_slides', 3);
    }
    if (!get_option('riff_blog_slider_tablet_slides')) {
        update_option('riff_blog_slider_tablet_slides', 2);
    }
    if (!get_option('riff_blog_slider_mobile_slides')) {
        update_option('riff_blog_slider_mobile_slides', 1);
    }
}
add_action('after_switch_theme', 'riff_blog_slider_activation_setup');


// ================================
// NEWS/BLOGアーカイブページ機能
// ================================

/**
 * カスタムリライトルールの追加
 * /news/ と /blog/ でそれぞれのアーカイブページを表示
 */
function riff_add_news_blog_rewrite_rules() {
    // /news/ アーカイブページ
    add_rewrite_rule(
        '^news/?$',
        'index.php?riff_archive_type=news',
        'top'
    );
    
    // /news/ページネーション
    add_rewrite_rule(
        '^news/page/([0-9]+)/?$',
        'index.php?riff_archive_type=news&paged=$matches[1]',
        'top'
    );
    
    // /blog/ アーカイブページ
    add_rewrite_rule(
        '^blog/?$',
        'index.php?riff_archive_type=blog',
        'top'
    );
    
    // /blog/ページネーション
    add_rewrite_rule(
        '^blog/page/([0-9]+)/?$',
        'index.php?riff_archive_type=blog&paged=$matches[1]',
        'top'
    );
}
add_action('init', 'riff_add_news_blog_rewrite_rules');

/**
 * クエリ変数を追加
 */
function riff_add_query_vars($vars) {
    $vars[] = 'riff_archive_type';
    return $vars;
}
add_filter('query_vars', 'riff_add_query_vars');

/**
 * テンプレートリダイレクト
 */
function riff_template_redirect_news_blog() {
    $archive_type = get_query_var('riff_archive_type');
    
    if ($archive_type === 'news' || $archive_type === 'blog') {
        // パーマリンクリフレッシュが必要な場合の対応
        global $wp_query;
        $wp_query->is_404 = false;
        status_header(200);
        
        // テンプレート選択
        if ($archive_type === 'news') {
            load_template(get_template_directory() . '/archive-news.php');
        } else {
            load_template(get_template_directory() . '/archive-blog.php');
        }
        exit;
    }
}
add_action('template_redirect', 'riff_template_redirect_news_blog');

/**
 * 投稿の詳細ページURLを調整
 * NEWSとBLOGで異なるパーマリンク構造を使用
 */
function riff_custom_post_permalink($permalink, $post) {
    if ($post->post_type !== 'post') {
        return $permalink;
    }
    
    $post_type_flag = get_post_meta($post->ID, 'post_type_flag', true);
    $post_type_flag = $post_type_flag ? $post_type_flag : 'news'; // デフォルトはNEWS
    
    // 投稿名のスラッグを取得
    $post_name = $post->post_name;
    
    if ($post_type_flag === 'news' || $post_type_flag === 'both') {
        return home_url("/news/{$post_name}/");
    } elseif ($post_type_flag === 'blog') {
        return home_url("/blog/{$post_name}/");
    }
    
    return $permalink;
}
add_filter('post_link', 'riff_custom_post_permalink', 10, 2);

/**
 * 投稿詳細ページのリライトルール
 */
function riff_add_post_detail_rewrite_rules() {
    // /news/投稿名/ の詳細ページ
    add_rewrite_rule(
        '^news/([^/]+)/?$',
        'index.php?name=$matches[1]&riff_post_type=news',
        'top'
    );
    
    // /blog/投稿名/ の詳細ページ
    add_rewrite_rule(
        '^blog/([^/]+)/?$',
        'index.php?name=$matches[1]&riff_post_type=blog',
        'top'
    );
}
add_action('init', 'riff_add_post_detail_rewrite_rules');

/**
 * 投稿詳細ページのクエリ調整
 */
function riff_parse_request_post_detail($wp) {
    if (isset($wp->query_vars['name']) && isset($wp->query_vars['riff_post_type'])) {
        $post_name = $wp->query_vars['name'];
        $expected_type = $wp->query_vars['riff_post_type'];
        
        // 投稿を取得して投稿タイプフラグをチェック
        $post = get_page_by_path($post_name, OBJECT, 'post');
        if ($post) {
            $post_type_flag = get_post_meta($post->ID, 'post_type_flag', true);
            $post_type_flag = $post_type_flag ? $post_type_flag : 'news';
            
            // URLと投稿タイプが一致しない場合は404またはリダイレクト
            if ($expected_type === 'news' && !in_array($post_type_flag, ['news', 'both'])) {
                // 正しいURLにリダイレクト
                wp_redirect(home_url("/blog/{$post_name}/"), 301);
                exit;
            } elseif ($expected_type === 'blog' && !in_array($post_type_flag, ['blog', 'both'])) {
                // 正しいURLにリダイレクト
                wp_redirect(home_url("/news/{$post_name}/"), 301);
                exit;
            }
        }
    }
}
add_action('parse_request', 'riff_parse_request_post_detail');

/**
 * パーマリンクフラッシュ
 * テーマアクティベーション時に実行
 */
function riff_flush_rewrite_rules_on_activation() {
    riff_add_news_blog_rewrite_rules();
    riff_add_post_detail_rewrite_rules();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'riff_flush_rewrite_rules_on_activation');


/**
 * SEO対策: メタタグの調整（<title>はWordPressに任せる）
 */
function riff_news_blog_seo_head() {
    $archive_type = get_query_var('riff_archive_type');

    if ($archive_type === 'news') {
        echo '<meta name="description" content="' . esc_attr( get_bloginfo('name') ) . 'の最新ニュースとお知らせを掲載しています。重要な情報をいち早くお届けします。">' . "\n";
        echo '<meta property="og:title" content="ニュース・お知らせ一覧 | ' . esc_attr( get_bloginfo('name') ) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr( get_bloginfo('name') ) . 'の最新ニュースとお知らせを掲載しています。">' . "\n";
        echo '<meta property="og:url" content="' . esc_url( home_url('/news/') ) . '">' . "\n";
        echo '<link rel="canonical" href="' . esc_url( home_url('/news/') ) . '">' . "\n";
    } elseif ($archive_type === 'blog') {
        echo '<meta name="description" content="' . esc_attr( get_bloginfo('name') ) . 'のブログ記事一覧です。技術情報やノウハウ、コラムなど有益な情報をお届けします。">' . "\n";
        echo '<meta property="og:title" content="ブログ記事一覧 | ' . esc_attr( get_bloginfo('name') ) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr( get_bloginfo('name') ) . 'のブログ記事一覧です。技術情報やノウハウ、コラムなど有益な情報をお届けします。">' . "\n";
        echo '<meta property="og:url" content="' . esc_url( home_url('/blog/') ) . '">' . "\n";
        echo '<link rel="canonical" href="' . esc_url( home_url('/blog/') ) . '">' . "\n";
    }
}
add_action('wp_head', 'riff_news_blog_seo_head');

function riff_news_blog_document_title( $title ) {
    $archive_type = get_query_var('riff_archive_type');
    if ($archive_type === 'news') {
        $title['title'] = 'ニュース・お知らせ一覧';
    } elseif ($archive_type === 'blog') {
        $title['title'] = 'ブログ記事一覧';
    }
    return $title;
}
add_filter('document_title_parts', 'riff_news_blog_document_title');

/**
 * 構造化データの追加
 */
function riff_add_structured_data() {
    $archive_type = get_query_var('riff_archive_type');
    
    if ($archive_type === 'news' || $archive_type === 'blog') {
        $site_name = get_bloginfo('name');
        $site_url = home_url();
        $archive_url = home_url("/{$archive_type}/");
        $title = $archive_type === 'news' ? 'ニュース・お知らせ' : 'ブログ記事';
        
        $structured_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $title . '一覧',
            'description' => $title . 'の一覧ページです。',
            'url' => $archive_url,
            'isPartOf' => array(
                '@type' => 'WebSite',
                'name' => $site_name,
                'url' => $site_url
            )
        );
        
        echo '<script type="application/ld+json">' . wp_json_encode($structured_data) . '</script>' . "\n";
    }
}
add_action('wp_head', 'riff_add_structured_data');

// ================================
// アーカイブページヒーロー設定機能
// ================================

// アーカイブページヒーロー設定ページを追加
function riff_archive_hero_admin_page() {
    add_submenu_page(
        'riff-theme-settings',
        'アーカイブページヒーロー設定',
        'アーカイブページヒーロー',
        'manage_options',
        'riff-archive-hero-settings',
        'riff_archive_hero_admin_page_content'
    );
}
add_action('admin_menu', 'riff_archive_hero_admin_page');

// アーカイブページヒーロー設定ページのコンテンツ
function riff_archive_hero_admin_page_content() {
    if (isset($_POST['submit'])) {
        // nonceチェック
        if (!isset($_POST['riff_archive_hero_nonce']) || !wp_verify_nonce($_POST['riff_archive_hero_nonce'], 'riff_archive_hero_settings')) {
            wp_die('セキュリティチェックに失敗しました。');
        }
        
        // NEWS アーカイブページ設定
        update_option('riff_news_archive_hero_enabled', isset($_POST['news_hero_enabled']) ? 1 : 0);
        update_option('riff_news_archive_hero_bg_image', esc_url_raw($_POST['news_hero_bg_image']));
        update_option('riff_news_archive_hero_overlay_opacity', intval($_POST['news_hero_overlay_opacity']));
        update_option('riff_news_archive_title_main', sanitize_text_field($_POST['news_title_main']));
        update_option('riff_news_archive_title_sub', sanitize_text_field($_POST['news_title_sub']));
        update_option('riff_news_archive_description', sanitize_textarea_field($_POST['news_description']));
        update_option('riff_news_archive_title_color', sanitize_hex_color($_POST['news_title_color']));
        update_option('riff_news_archive_text_color', sanitize_hex_color($_POST['news_text_color']));
        
        // BLOG アーカイブページ設定
        update_option('riff_blog_archive_hero_enabled', isset($_POST['blog_hero_enabled']) ? 1 : 0);
        update_option('riff_blog_archive_hero_bg_image', esc_url_raw($_POST['blog_hero_bg_image']));
        update_option('riff_blog_archive_hero_overlay_opacity', intval($_POST['blog_hero_overlay_opacity']));
        update_option('riff_blog_archive_title_main', sanitize_text_field($_POST['blog_title_main']));
        update_option('riff_blog_archive_title_sub', sanitize_text_field($_POST['blog_title_sub']));
        update_option('riff_blog_archive_description', sanitize_textarea_field($_POST['blog_description']));
        update_option('riff_blog_archive_title_color', sanitize_hex_color($_POST['blog_title_color']));
        update_option('riff_blog_archive_text_color', sanitize_hex_color($_POST['blog_text_color']));
        
        echo '<div class="notice notice-success"><p>アーカイブページヒーロー設定を保存しました。</p></div>';
    }
    
    // NEWS設定値を取得
    $news_hero_enabled = get_option('riff_news_archive_hero_enabled', 1);
    $news_hero_bg_image = get_option('riff_news_archive_hero_bg_image', '');
    $news_hero_overlay_opacity = get_option('riff_news_archive_hero_overlay_opacity', 30);
    $news_title_main = get_option('riff_news_archive_title_main', 'ニュース・お知らせ');
    $news_title_sub = get_option('riff_news_archive_title_sub', 'NEWS');
    $news_description = get_option('riff_news_archive_description', '最新のニュースやお知らせ、重要な情報をお届けします。');
    $news_title_color = get_option('riff_news_archive_title_color', '#ffffff');
    $news_text_color = get_option('riff_news_archive_text_color', '#ffffff');
    
    // BLOG設定値を取得
    $blog_hero_enabled = get_option('riff_blog_archive_hero_enabled', 1);
    $blog_hero_bg_image = get_option('riff_blog_archive_hero_bg_image', '');
    $blog_hero_overlay_opacity = get_option('riff_blog_archive_hero_overlay_opacity', 30);
    $blog_title_main = get_option('riff_blog_archive_title_main', 'ブログ記事');
    $blog_title_sub = get_option('riff_blog_archive_title_sub', 'BLOG');
    $blog_description = get_option('riff_blog_archive_description', '技術情報やノウハウ、コラムなど有益な情報をお届けします。');
    $blog_title_color = get_option('riff_blog_archive_title_color', '#ffffff');
    $blog_text_color = get_option('riff_blog_archive_text_color', '#ffffff');
    ?>
    
    <div class="wrap">
        <h1>アーカイブページヒーロー設定</h1>
        
        <div class="riff-admin-header">
            <h1>アーカイブページヒーロー設定</h1>
            <p>NEWSページとBLOGページのヒーロー画像とテキストを設定できます。背景画像を設定すると、よりインパクトのあるアーカイブページになります。</p>
        </div>
        
        <form method="post" action="">
            <?php wp_nonce_field('riff_archive_hero_settings', 'riff_archive_hero_nonce'); ?>
            
            <!-- NEWS アーカイブページ設定 -->
            <h2 style="background: #e3f2fd; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px; color: #1976d2;">📰 NEWSアーカイブページ設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">ヒーロー機能</th>
                    <td>
                        <label>
                            <input type="checkbox" name="news_hero_enabled" value="1" <?php checked($news_hero_enabled, 1); ?> />
                            NEWSページでヒーロー背景画像を有効にする
                        </label>
                        <p class="description">チェックを外すと従来のシンプルなヘッダーになります。</p>
                    </td>
                </tr>
                <tr class="news-hero-settings" style="<?php echo $news_hero_enabled ? '' : 'display:none;'; ?>">
                    <th scope="row">背景画像</th>
                    <td>
                        <input type="url" name="news_hero_bg_image" id="news_hero_bg_image" value="<?php echo esc_attr($news_hero_bg_image); ?>" class="regular-text" />
                        <button type="button" class="button" id="upload_news_hero_bg_button">画像を選択</button>
                        <button type="button" class="button" id="remove_news_hero_bg_button" style="<?php echo $news_hero_bg_image ? '' : 'display:none;'; ?>">画像を削除</button>
                        <p class="description">推奨サイズ: 1920×800px以上。ニュースに関連する画像がおすすめです。</p>
                        <?php if ($news_hero_bg_image): ?>
                            <div class="news-hero-bg-preview" style="margin-top: 10px;">
                                <img src="<?php echo esc_url($news_hero_bg_image); ?>" style="max-width: 300px; height: auto; border: 1px solid #ddd;" />
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr class="news-hero-settings" style="<?php echo $news_hero_enabled ? '' : 'display:none;'; ?>">
                    <th scope="row">オーバーレイ透明度</th>
                    <td>
                        <input type="range" name="news_hero_overlay_opacity" class="news-opacity-slider" 
                               value="<?php echo esc_attr($news_hero_overlay_opacity); ?>" 
                               min="0" max="80" step="5" />
                        <span class="opacity-display"><?php echo $news_hero_overlay_opacity; ?>%</span>
                        <p class="description">背景画像の上に重ねる暗いオーバーレイの透明度です。文字を読みやすくするために調整してください。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">メインタイトル</th>
                    <td>
                        <input type="text" name="news_title_main" value="<?php echo esc_attr($news_title_main); ?>" class="regular-text" />
                        <p class="description">ページの大きなタイトルです。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">サブタイトル</th>
                    <td>
                        <input type="text" name="news_title_sub" value="<?php echo esc_attr($news_title_sub); ?>" class="regular-text" />
                        <p class="description">メインタイトルの上に表示される小さなテキストです。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">説明文</th>
                    <td>
                        <textarea name="news_description" rows="3" cols="50" class="large-text"><?php echo esc_textarea($news_description); ?></textarea>
                        <p class="description">タイトルの下に表示される説明文です。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">タイトル色</th>
                    <td>
                        <input type="color" name="news_title_color" value="<?php echo esc_attr($news_title_color); ?>" />
                        <p class="description">メインタイトルとサブタイトルの色です。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">説明文の色</th>
                    <td>
                        <input type="color" name="news_text_color" value="<?php echo esc_attr($news_text_color); ?>" />
                        <p class="description">説明文とパンくずリストの色です。</p>
                    </td>
                </tr>
            </table>
            
            <!-- BLOG アーカイブページ設定 -->
            <h2 style="background: #f3e5f5; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px; color: #7b1fa2;">✍️ BLOGアーカイブページ設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">ヒーロー機能</th>
                    <td>
                        <label>
                            <input type="checkbox" name="blog_hero_enabled" value="1" <?php checked($blog_hero_enabled, 1); ?> />
                            BLOGページでヒーロー背景画像を有効にする
                        </label>
                        <p class="description">チェックを外すと従来のシンプルなヘッダーになります。</p>
                    </td>
                </tr>
                <tr class="blog-hero-settings" style="<?php echo $blog_hero_enabled ? '' : 'display:none;'; ?>">
                    <th scope="row">背景画像</th>
                    <td>
                        <input type="url" name="blog_hero_bg_image" id="blog_hero_bg_image" value="<?php echo esc_attr($blog_hero_bg_image); ?>" class="regular-text" />
                        <button type="button" class="button" id="upload_blog_hero_bg_button">画像を選択</button>
                        <button type="button" class="button" id="remove_blog_hero_bg_button" style="<?php echo $blog_hero_bg_image ? '' : 'display:none;'; ?>">画像を削除</button>
                        <p class="description">推奨サイズ: 1920×800px以上。技術やブログに関連する画像がおすすめです。</p>
                        <?php if ($blog_hero_bg_image): ?>
                            <div class="blog-hero-bg-preview" style="margin-top: 10px;">
                                <img src="<?php echo esc_url($blog_hero_bg_image); ?>" style="max-width: 300px; height: auto; border: 1px solid #ddd;" />
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr class="blog-hero-settings" style="<?php echo $blog_hero_enabled ? '' : 'display:none;'; ?>">
                    <th scope="row">オーバーレイ透明度</th>
                    <td>
                        <input type="range" name="blog_hero_overlay_opacity" class="blog-opacity-slider" 
                               value="<?php echo esc_attr($blog_hero_overlay_opacity); ?>" 
                               min="0" max="80" step="5" />
                        <span class="opacity-display"><?php echo $blog_hero_overlay_opacity; ?>%</span>
                        <p class="description">背景画像の上に重ねる暗いオーバーレイの透明度です。文字を読みやすくするために調整してください。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">メインタイトル</th>
                    <td>
                        <input type="text" name="blog_title_main" value="<?php echo esc_attr($blog_title_main); ?>" class="regular-text" />
                        <p class="description">ページの大きなタイトルです。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">サブタイトル</th>
                    <td>
                        <input type="text" name="blog_title_sub" value="<?php echo esc_attr($blog_title_sub); ?>" class="regular-text" />
                        <p class="description">メインタイトルの上に表示される小さなテキストです。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">説明文</th>
                    <td>
                        <textarea name="blog_description" rows="3" cols="50" class="large-text"><?php echo esc_textarea($blog_description); ?></textarea>
                        <p class="description">タイトルの下に表示される説明文です。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">タイトル色</th>
                    <td>
                        <input type="color" name="blog_title_color" value="<?php echo esc_attr($blog_title_color); ?>" />
                        <p class="description">メインタイトルとサブタイトルの色です。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">説明文の色</th>
                    <td>
                        <input type="color" name="blog_text_color" value="<?php echo esc_attr($blog_text_color); ?>" />
                        <p class="description">説明文とパンくずリストの色です。</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('設定を保存', 'primary', 'submit', false, array('style' => 'font-size: 16px; padding: 10px 20px; margin-top: 20px;')); ?>
        </form>
        
        <!-- プレビューセクション -->
        <div style="margin-top: 40px; padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 6px;">
            <h3>設定プレビュー</h3>
            
            <!-- NEWSプレビュー -->
            <div style="margin-bottom: 30px;">
                <h4 style="color: #1976d2;">📰 NEWSページプレビュー</h4>
                <div style="position: relative; height: 200px; background: linear-gradient(135deg, #1976d2, #42a5f5); <?php echo $news_hero_bg_image ? 'background-image: url(' . esc_url($news_hero_bg_image) . '); background-size: cover; background-position: center;' : ''; ?> border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <?php if ($news_hero_enabled && $news_hero_bg_image): ?>
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,<?php echo $news_hero_overlay_opacity / 100; ?>);"></div>
                    <?php endif; ?>
                    <div style="position: relative; z-index: 2; text-align: center; color: <?php echo esc_attr($news_title_color); ?>;">
                        <div style="font-size: 14px; margin-bottom: 5px; color: <?php echo esc_attr($news_text_color); ?>;">📰 <?php echo esc_html($news_title_sub); ?></div>
                        <h2 style="font-size: 28px; margin: 0 0 10px 0; color: <?php echo esc_attr($news_title_color); ?>;"><?php echo esc_html($news_title_main); ?></h2>
                        <p style="font-size: 14px; margin: 0; color: <?php echo esc_attr($news_text_color); ?>; max-width: 400px;"><?php echo esc_html($news_description); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- BLOGプレビュー -->
            <div>
                <h4 style="color: #7b1fa2;">✍️ BLOGページプレビュー</h4>
                <div style="position: relative; height: 200px; background: linear-gradient(135deg, #7b1fa2, #ba68c8); <?php echo $blog_hero_bg_image ? 'background-image: url(' . esc_url($blog_hero_bg_image) . '); background-size: cover; background-position: center;' : ''; ?> border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    <?php if ($blog_hero_enabled && $blog_hero_bg_image): ?>
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,<?php echo $blog_hero_overlay_opacity / 100; ?>);"></div>
                    <?php endif; ?>
                    <div style="position: relative; z-index: 2; text-align: center; color: <?php echo esc_attr($blog_title_color); ?>;">
                        <div style="font-size: 14px; margin-bottom: 5px; color: <?php echo esc_attr($blog_text_color); ?>;">✍️ <?php echo esc_html($blog_title_sub); ?></div>
                        <h2 style="font-size: 28px; margin: 0 0 10px 0; color: <?php echo esc_attr($blog_title_color); ?>;"><?php echo esc_html($blog_title_main); ?></h2>
                        <p style="font-size: 14px; margin: 0; color: <?php echo esc_attr($blog_text_color); ?>; max-width: 400px;"><?php echo esc_html($blog_description); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // 画像アップローダー（NEWS）
        $('#upload_news_hero_bg_button').click(function(e) {
            e.preventDefault();
            var image = wp.media({
                title: 'NEWS背景画像を選択',
                multiple: false,
                library: { type: 'image' }
            }).open().on('select', function(e) {
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#news_hero_bg_image').val(image_url);
                
                // プレビューを更新
                $('.news-hero-bg-preview').remove();
                $('<div class="news-hero-bg-preview" style="margin-top: 10px;"><img src="' + image_url + '" style="max-width: 300px; height: auto; border: 1px solid #ddd;" /></div>').insertAfter('#upload_news_hero_bg_button');
                $('#remove_news_hero_bg_button').show();
            });
        });
        
        // 画像削除（NEWS）
        $('#remove_news_hero_bg_button').click(function(e) {
            e.preventDefault();
            if (confirm('NEWS背景画像を削除しますか？')) {
                $('#news_hero_bg_image').val('');
                $('.news-hero-bg-preview').remove();
                $(this).hide();
            }
        });
        
        // 画像アップローダー（BLOG）
        $('#upload_blog_hero_bg_button').click(function(e) {
            e.preventDefault();
            var image = wp.media({
                title: 'BLOG背景画像を選択',
                multiple: false,
                library: { type: 'image' }
            }).open().on('select', function(e) {
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#blog_hero_bg_image').val(image_url);
                
                // プレビューを更新
                $('.blog-hero-bg-preview').remove();
                $('<div class="blog-hero-bg-preview" style="margin-top: 10px;"><img src="' + image_url + '" style="max-width: 300px; height: auto; border: 1px solid #ddd;" /></div>').insertAfter('#upload_blog_hero_bg_button');
                $('#remove_blog_hero_bg_button').show();
            });
        });
        
        // 画像削除（BLOG）
        $('#remove_blog_hero_bg_button').click(function(e) {
            e.preventDefault();
            if (confirm('BLOG背景画像を削除しますか？')) {
                $('#blog_hero_bg_image').val('');
                $('.blog-hero-bg-preview').remove();
                $(this).hide();
            }
        });
        
        // チェックボックスの状態に応じて表示/非表示（NEWS）
        $('input[name="news_hero_enabled"]').on('change', function() {
            if ($(this).is(':checked')) {
                $('.news-hero-settings').show();
            } else {
                $('.news-hero-settings').hide();
            }
        });
        
        // チェックボックスの状態に応じて表示/非表示（BLOG）
        $('input[name="blog_hero_enabled"]').on('change', function() {
            if ($(this).is(':checked')) {
                $('.blog-hero-settings').show();
            } else {
                $('.blog-hero-settings').hide();
            }
        });
        
        // 透明度スライダー（NEWS）
        $('.news-opacity-slider').on('input', function() {
            $(this).next('.opacity-display').text($(this).val() + '%');
        });
        
        // 透明度スライダー（BLOG）
        $('.blog-opacity-slider').on('input', function() {
            $(this).next('.opacity-display').text($(this).val() + '%');
        });
    });
    </script>
    <?php
}

// アーカイブページヒーロー用CSSを動的に生成
function riff_archive_hero_custom_css() {
    // NEWS設定
    $news_hero_enabled = get_option('riff_news_archive_hero_enabled', 1);
    $news_hero_bg_image = get_option('riff_news_archive_hero_bg_image', '');
    $news_hero_overlay_opacity = get_option('riff_news_archive_hero_overlay_opacity', 30) / 100;
    $news_title_color = get_option('riff_news_archive_title_color', '#ffffff');
    $news_text_color = get_option('riff_news_archive_text_color', '#ffffff');
    
    // BLOG設定
    $blog_hero_enabled = get_option('riff_blog_archive_hero_enabled', 1);
    $blog_hero_bg_image = get_option('riff_blog_archive_hero_bg_image', '');
    $blog_hero_overlay_opacity = get_option('riff_blog_archive_hero_overlay_opacity', 30) / 100;
    $blog_title_color = get_option('riff_blog_archive_title_color', '#ffffff');
    $blog_text_color = get_option('riff_blog_archive_text_color', '#ffffff');
    ?>
    <style type="text/css">
        :root {
            /* NEWS アーカイブヒーロー変数 */
            --news-hero-enabled: <?php echo $news_hero_enabled ? '1' : '0'; ?>;
            --news-hero-bg-image: <?php echo $news_hero_bg_image ? 'url(' . esc_attr($news_hero_bg_image) . ')' : 'none'; ?>;
            --news-hero-overlay-opacity: <?php echo esc_attr($news_hero_overlay_opacity); ?>;
            --news-hero-title-color: <?php echo esc_attr($news_title_color); ?>;
            --news-hero-text-color: <?php echo esc_attr($news_text_color); ?>;
            
            /* BLOG アーカイブヒーロー変数 */
            --blog-hero-enabled: <?php echo $blog_hero_enabled ? '1' : '0'; ?>;
            --blog-hero-bg-image: <?php echo $blog_hero_bg_image ? 'url(' . esc_attr($blog_hero_bg_image) . ')' : 'none'; ?>;
            --blog-hero-overlay-opacity: <?php echo esc_attr($blog_hero_overlay_opacity); ?>;
            --blog-hero-title-color: <?php echo esc_attr($blog_title_color); ?>;
            --blog-hero-text-color: <?php echo esc_attr($blog_text_color); ?>;
        }
        
        /* NEWSアーカイブページのヒーロースタイル */
        .riff-news-archive-hero {
            position: relative;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1976d2, #42a5f5);
            <?php if ($news_hero_enabled && $news_hero_bg_image): ?>
            background-image: var(--news-hero-bg-image);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            <?php endif; ?>
            color: var(--news-hero-title-color);
            overflow: hidden;
        }
        
        .riff-news-archive-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            <?php if ($news_hero_enabled && $news_hero_bg_image): ?>
            background: rgba(0, 0, 0, var(--news-hero-overlay-opacity));
            <?php else: ?>
            background: transparent;
            <?php endif; ?>
            z-index: 1;
        }
        
        .riff-news-archive-hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 800px;
            padding: 0 20px;
        }
        
        /* BLOGアーカイブページのヒーロースタイル */
        .riff-blog-archive-hero {
            position: relative;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #7b1fa2, #ba68c8);
            <?php if ($blog_hero_enabled && $blog_hero_bg_image): ?>
            background-image: var(--blog-hero-bg-image);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            <?php endif; ?>
            color: var(--blog-hero-title-color);
            overflow: hidden;
        }
        
        .riff-blog-archive-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            <?php if ($blog_hero_enabled && $blog_hero_bg_image): ?>
            background: rgba(0, 0, 0, var(--blog-hero-overlay-opacity));
            <?php else: ?>
            background: transparent;
            <?php endif; ?>
            z-index: 1;
        }
        
        .riff-blog-archive-hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 800px;
            padding: 0 20px;
        }
        
        /* 共通のヒーロースタイル */
        .riff-archive-hero-subtitle {
            font-size: 1.2rem;
            font-weight: 400;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        .riff-archive-hero-title {
            font-size: 3rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
        }
        
        .riff-archive-hero-description {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .riff-archive-hero-breadcrumb {
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            font-size: 14px;
        }
        
        .riff-archive-hero-breadcrumb a {
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        .riff-archive-hero-breadcrumb a:hover {
            opacity: 1;
        }
        
        /* レスポンシブ対応 */
        @media (max-width: 768px) {
            .riff-news-archive-hero,
            .riff-blog-archive-hero {
                min-height: 300px;
                padding: 60px 0;
            }
            
            .riff-archive-hero-title {
                font-size: 2.2rem;
            }
            
            .riff-archive-hero-subtitle {
                font-size: 1rem;
            }
            
            .riff-archive-hero-description {
                font-size: 1rem;
            }
            
            .riff-archive-hero-breadcrumb {
                top: 20px;
                position: relative;
                transform: none;
                left: auto;
                margin-bottom: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .riff-news-archive-hero,
            .riff-blog-archive-hero {
                min-height: 250px;
                padding: 40px 0;
            }
            
            .riff-archive-hero-title {
                font-size: 1.8rem;
            }
            
            .riff-archive-hero-content {
                padding: 0 15px;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'riff_archive_hero_custom_css', 101);

// ================================
// フェードアニメーション機能の追加
// ================================

/**
 * フェードアニメーション用CSSとJavaScriptの読み込み
 * 既存のテーマファイルと競合しないよう独立して実装
 */
function riff_enqueue_fade_animations() {
    // フェードアニメーション用CSSはriff-common-bundle.cssに統合済み

    // フェードアニメーション用JavaScript
    wp_enqueue_script(
        'riff-fade-animations',
        get_template_directory_uri() . '/assets/js/fade-animations.js',
        array(), // jQueryに依存しない（独立して動作）
        '1.0.0',
        true // フッターで読み込み
    );
    
    // デバッグ情報をJavaScriptに渡す（開発時のみ）
    if (defined('WP_DEBUG') && WP_DEBUG) {
        wp_localize_script('riff-fade-animations', 'riffFadeConfig', array(
            'debug' => true,
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('riff_fade_nonce'),
            'version' => '1.0.0'
        ));
    }
}
add_action('wp_enqueue_scripts', 'riff_enqueue_fade_animations');

/**
 * 管理画面でフェードアニメーション設定ページを追加
 * 既存のRIFFテーマ設定に統合
 */
function riff_fade_animations_admin_page() {
    add_submenu_page(
        'riff-theme-settings',
        'フェードアニメーション設定',
        'フェードアニメーション',
        'manage_options',
        'riff-fade-animations-settings',
        'riff_fade_animations_admin_page_content'
    );
}
add_action('admin_menu', 'riff_fade_animations_admin_page');

/**
 * フェードアニメーション設定ページのコンテンツ
 */
function riff_fade_animations_admin_page_content() {
    if (isset($_POST['submit'])) {
        // nonceチェック
        if (!isset($_POST['riff_fade_nonce']) || !wp_verify_nonce($_POST['riff_fade_nonce'], 'riff_fade_animations_settings')) {
            wp_die('セキュリティチェックに失敗しました。');
        }
        
        // 設定の保存
        update_option('riff_fade_animations_enabled', isset($_POST['fade_animations_enabled']) ? 1 : 0);
        update_option('riff_fade_animations_speed', sanitize_text_field($_POST['fade_animations_speed']));
        update_option('riff_fade_animations_threshold', floatval($_POST['fade_animations_threshold']));
        update_option('riff_fade_animations_mobile_enabled', isset($_POST['fade_animations_mobile_enabled']) ? 1 : 0);
        
        echo '<div class="notice notice-success"><p>フェードアニメーション設定を保存しました。</p></div>';
    }
    
    // 設定値を取得
    $fade_enabled = get_option('riff_fade_animations_enabled', 1);
    $fade_speed = get_option('riff_fade_animations_speed', 'normal');
    $fade_threshold = get_option('riff_fade_animations_threshold', 0.1);
    $fade_mobile_enabled = get_option('riff_fade_animations_mobile_enabled', 1);
    ?>
    
    <div class="wrap">
        <h1>フェードアニメーション設定</h1>
        
        <div class="riff-admin-header">
            <h1>フェードアニメーション設定</h1>
            <p>ページ内の要素にフェードイン効果を適用する設定を管理できます。</p>
        </div>
        
        <form method="post" action="">
            <?php wp_nonce_field('riff_fade_animations_settings', 'riff_fade_nonce'); ?>
            
            <!-- 基本設定 -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">基本設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">フェードアニメーション</th>
                    <td>
                        <label>
                            <input type="checkbox" name="fade_animations_enabled" value="1" <?php checked($fade_enabled, 1); ?> />
                            フェードアニメーションを有効にする
                        </label>
                        <p class="description">チェックを外すとすべてのフェードアニメーションが無効になります。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">アニメーション速度</th>
                    <td>
                        <select name="fade_animations_speed">
                            <option value="fast" <?php selected($fade_speed, 'fast'); ?>>高速（0.4秒）</option>
                            <option value="normal" <?php selected($fade_speed, 'normal'); ?>>標準（0.8秒）</option>
                            <option value="slow" <?php selected($fade_speed, 'slow'); ?>>低速（1.2秒）</option>
                        </select>
                        <p class="description">フェードアニメーションの実行速度を設定します。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">トリガー感度</th>
                    <td>
                        <input type="range" name="fade_animations_threshold" 
                               value="<?php echo esc_attr($fade_threshold); ?>" 
                               min="0.1" max="1.0" step="0.1" 
                               oninput="this.nextElementSibling.textContent = (this.value * 100) + '%'" />
                        <span><?php echo ($fade_threshold * 100); ?>%</span>
                        <p class="description">要素の何%が画面に表示されたらアニメーションを開始するかを設定します。</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">モバイル対応</th>
                    <td>
                        <label>
                            <input type="checkbox" name="fade_animations_mobile_enabled" value="1" <?php checked($fade_mobile_enabled, 1); ?> />
                            モバイルデバイスでもアニメーションを有効にする
                        </label>
                        <p class="description">チェックを外すとモバイルでは即座に表示されます（パフォーマンス向上）。</p>
                    </td>
                </tr>
            </table>
            
            <!-- 使用方法 -->
            <h2 style="background: #e8f5e8; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">使用方法</h2>
            <div style="background: #ffffff; padding: 20px; border: 1px solid #ddd; border-radius: 6px;">
                <h3>HTMLに追加するクラス名</h3>
                <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 12px; border: 1px solid #ddd; text-align: left;">クラス名</th>
                            <th style="padding: 12px; border: 1px solid #ddd; text-align: left;">効果</th>
                            <th style="padding: 12px; border: 1px solid #ddd; text-align: left;">使用例</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 12px; border: 1px solid #ddd;"><code>fadeUpTrigger</code></td>
                            <td style="padding: 12px; border: 1px solid #ddd;">上からフェードイン</td>
                            <td style="padding: 12px; border: 1px solid #ddd;"><code>&lt;div class="fadeUpTrigger"&gt;内容&lt;/div&gt;</code></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border: 1px solid #ddd;"><code>fadeDownTrigger</code></td>
                            <td style="padding: 12px; border: 1px solid #ddd;">下からフェードイン</td>
                            <td style="padding: 12px; border: 1px solid #ddd;"><code>&lt;div class="fadeDownTrigger"&gt;内容&lt;/div&gt;</code></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border: 1px solid #ddd;"><code>fadeLeftTrigger</code></td>
                            <td style="padding: 12px; border: 1px solid #ddd;">左からフェードイン</td>
                            <td style="padding: 12px; border: 1px solid #ddd;"><code>&lt;div class="fadeLeftTrigger"&gt;内容&lt;/div&gt;</code></td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border: 1px solid #ddd;"><code>fadeRightTrigger</code></td>
                            <td style="padding: 12px; border: 1px solid #ddd;">右からフェードイン</td>
                            <td style="padding: 12px; border: 1px solid #ddd;"><code>&lt;div class="fadeRightTrigger"&gt;内容&lt;/div&gt;</code></td>
                        </tr>
                    </tbody>
                </table>
                
                <h3 style="margin-top: 25px;">遅延アニメーション（オプション）</h3>
                <p>複数の要素を順番にアニメーションさせたい場合は、以下のクラスを追加してください：</p>
                <ul style="margin-left: 20px;">
                    <li><code>riff-fade-delay-100</code> - 0.1秒遅延</li>
                    <li><code>riff-fade-delay-200</code> - 0.2秒遅延</li>
                    <li><code>riff-fade-delay-300</code> - 0.3秒遅延</li>
                    <li><code>riff-fade-delay-400</code> - 0.4秒遅延</li>
                    <li><code>riff-fade-delay-500</code> - 0.5秒遅延</li>
                </ul>
                
                <h3 style="margin-top: 25px;">使用例</h3>
                <pre style="background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto;"><code>&lt;!-- 基本的な使用方法 --&gt;
&lt;div class="fadeUpTrigger"&gt;
    &lt;h2&gt;見出しタイトル&lt;/h2&gt;
    &lt;p&gt;この内容は上からフェードインします。&lt;/p&gt;
&lt;/div&gt;

&lt;!-- 遅延アニメーション --&gt;
&lt;div class="fadeLeftTrigger riff-fade-delay-100"&gt;
    &lt;p&gt;0.1秒遅延して左からフェードイン&lt;/p&gt;
&lt;/div&gt;

&lt;div class="fadeRightTrigger riff-fade-delay-200"&gt;
    &lt;p&gt;0.2秒遅延して右からフェードイン&lt;/p&gt;
&lt;/div&gt;</code></pre>
            </div>
            
            <!-- 開発者向け情報 -->
            <h2 style="background: #fff3cd; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px; border: 1px solid #ffeaa7;">開発者向け情報</h2>
            <div style="background: #ffffff; padding: 20px; border: 1px solid #ddd; border-radius: 6px;">
                <h3>JavaScript API</h3>
                <p>コンソールで以下のコマンドを実行して、アニメーションを制御できます：</p>
                <ul style="margin-left: 20px;">
                    <li><code>RiffFadeAnimations.toggleDebug()</code> - デバッグモードの切り替え</li>
                    <li><code>RiffFadeAnimations.refresh()</code> - 新しく追加された要素を検出</li>
                    <li><code>RiffFadeAnimations.showAll()</code> - すべての要素を強制表示</li>
                </ul>
                
                <h3>アクセシビリティ</h3>
                <p>「モーション設定を減らす」を選択しているユーザーには自動的にアニメーションが無効化されます。</p>
                
                <h3>パフォーマンス</h3>
                <p>Intersection Observer APIを使用しており、軽量で高パフォーマンスです。非対応ブラウザでは自動的にフォールバック表示されます。</p>
            </div>
            
            <?php submit_button('設定を保存', 'primary', 'submit', false, array('style' => 'font-size: 16px; padding: 10px 20px; margin-top: 20px;')); ?>
        </form>
    </div>
    <?php
}

/**
 * フェードアニメーション設定に基づくCSS変数を出力
 */
function riff_output_fade_animations_css_vars() {
    $fade_enabled = get_option('riff_fade_animations_enabled', 1);
    $fade_speed = get_option('riff_fade_animations_speed', 'normal');
    $fade_mobile_enabled = get_option('riff_fade_animations_mobile_enabled', 1);
    
    // アニメーション速度を秒数に変換
    $speed_map = array(
        'fast' => '0.4s',
        'normal' => '0.8s',
        'slow' => '1.2s'
    );
    $duration = isset($speed_map[$fade_speed]) ? $speed_map[$fade_speed] : '0.8s';
    
    if (!$fade_enabled) {
        // アニメーションが無効の場合は即座に表示
        ?>
        <style type="text/css">
        .fadeUpTrigger,
        .fadeDownTrigger,
        .fadeLeftTrigger,
        .fadeRightTrigger {
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }
        </style>
        <?php
        return;
    }
    ?>
    <style type="text/css">
    :root {
        --riff-fade-duration: <?php echo esc_attr($duration); ?>;
        --riff-fade-mobile-enabled: <?php echo $fade_mobile_enabled ? '1' : '0'; ?>;
    }
    
    .fadeUpTrigger,
    .fadeDownTrigger,
    .fadeLeftTrigger,
    .fadeRightTrigger {
        transition-duration: var(--riff-fade-duration);
    }
    
    <?php if (!$fade_mobile_enabled): ?>
    @media (max-width: 768px) {
        .fadeUpTrigger,
        .fadeDownTrigger,
        .fadeLeftTrigger,
        .fadeRightTrigger {
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }
    }
    <?php endif; ?>
    </style>
    <?php
}
add_action('wp_head', 'riff_output_fade_animations_css_vars', 102);

/**
 * フェードアニメーション用JavaScript設定を出力
 */
function riff_output_fade_animations_js_config() {
    $fade_enabled = get_option('riff_fade_animations_enabled', 1);
    $fade_threshold = get_option('riff_fade_animations_threshold', 0.1);
    
    if (!$fade_enabled) {
        return;
    }
    ?>
    <script type="text/javascript">
    if (typeof RiffFadeAnimations !== 'undefined' && RiffFadeAnimations.manager) {
        RiffFadeAnimations.manager.config.threshold = <?php echo floatval($fade_threshold); ?>;
    }
    </script>
    <?php
}
add_action('wp_footer', 'riff_output_fade_animations_js_config');

/**
 * テーマアクティベーション時にデフォルト設定を保存
 */
function riff_fade_animations_activation() {
    // デフォルト設定
    if (get_option('riff_fade_animations_enabled') === false) {
        update_option('riff_fade_animations_enabled', 1);
    }
    if (!get_option('riff_fade_animations_speed')) {
        update_option('riff_fade_animations_speed', 'normal');
    }
    if (get_option('riff_fade_animations_threshold') === false) {
        update_option('riff_fade_animations_threshold', 0.1);
    }
    if (get_option('riff_fade_animations_mobile_enabled') === false) {
        update_option('riff_fade_animations_mobile_enabled', 1);
    }
}
add_action('after_switch_theme', 'riff_fade_animations_activation');

// ================================
// パンくずリスト機能
// ================================

/**
 * パンくずリスト用CSS・JSの読み込み
 */
function riff_enqueue_breadcrumb_assets() {
    // front-pageでは読み込まない
    if (is_front_page()) {
        return;
    }
    
    // パンくずリスト用CSS
    wp_enqueue_style(
        'riff-breadcrumb',
        get_template_directory_uri() . '/assets/css/breadcrumb.css',
        array('riff-style'), // メインスタイルの後に読み込み
        '1.0.2'
    );
}
add_action('wp_enqueue_scripts', 'riff_enqueue_breadcrumb_assets');

/**
 * パンくずリストを表示する関数
 */
function riff_display_breadcrumb() {
    // front-pageでは表示しない
    if (is_front_page()) {
        return;
    }
    
    echo '<nav class="riff-breadcrumb-container" aria-label="パンくずナビゲーション">';
    echo '<div class="container">';
    echo '<div class="riff-breadcrumb">';
    
    // ホームリンク
    echo '<a href="' . esc_url(home_url('/')) . '" class="breadcrumb-home">ホーム</a>';
    echo '<span class="breadcrumb-separator" aria-hidden="true">/</span>';
    
    if (is_single()) {
        // 個別投稿ページ
        riff_breadcrumb_single();
    } elseif (is_page()) {
        // 固定ページ
        riff_breadcrumb_page();
    } elseif (is_category()) {
        // カテゴリーアーカイブ
        riff_breadcrumb_category();
    } elseif (is_tag()) {
        // タグアーカイブ
        riff_breadcrumb_tag();
    } elseif (is_author()) {
        // 著者アーカイブ
        riff_breadcrumb_author();
    } elseif (is_date()) {
        // 日付アーカイブ
        riff_breadcrumb_date();
    } elseif (is_search()) {
        // 検索結果ページ
        riff_breadcrumb_search();
    } elseif (is_404()) {
        // 404ページ
        echo '<span class="breadcrumb-current">ページが見つかりません</span>';
    } elseif (get_query_var('riff_archive_type') === 'news') {
        // NEWSアーカイブページ
        echo '<span class="breadcrumb-current">ニュース・お知らせ</span>';
    } elseif (get_query_var('riff_archive_type') === 'blog') {
        // BLOGアーカイブページ
        echo '<span class="breadcrumb-current">ブログ記事</span>';
    } else {
        // その他のアーカイブページ
        $title = wp_title('', false);
        if (!empty($title)) {
            echo '<span class="breadcrumb-current">' . esc_html($title) . '</span>';
        } else {
            echo '<span class="breadcrumb-current">' . esc_html(get_the_archive_title()) . '</span>';
        }
    }
    
    echo '</div>';
    echo '</div>';
    echo '</nav>';
}

/**
 * 個別投稿ページのパンくずリスト
 */
function riff_breadcrumb_single() {
    global $post;
    
    // 投稿タイプフラグを取得
    $post_type_flag = get_post_meta($post->ID, 'post_type_flag', true);
    $post_type_flag = $post_type_flag ? $post_type_flag : 'news';
    
    // アーカイブページへのリンク
    if ($post_type_flag === 'blog' || ($post_type_flag === 'both' && strpos($_SERVER['REQUEST_URI'], '/blog/') !== false)) {
        echo '<a href="' . esc_url(home_url('/blog/')) . '" class="breadcrumb-archive">ブログ記事</a>';
    } else {
        echo '<a href="' . esc_url(home_url('/news/')) . '" class="breadcrumb-archive">ニュース・お知らせ</a>';
    }
    echo '<span class="breadcrumb-separator" aria-hidden="true">/</span>';
    
    // カテゴリーがある場合
    $categories = get_the_category();
    if (!empty($categories)) {
        $primary_category = $categories[0];
        echo '<a href="' . esc_url(get_category_link($primary_category->term_id)) . '" class="breadcrumb-category">' . esc_html($primary_category->name) . '</a>';
        echo '<span class="breadcrumb-separator" aria-hidden="true">/</span>';
    }
    
    // 現在の投稿
    echo '<span class="breadcrumb-current">' . esc_html(get_the_title()) . '</span>';
}

// ================================
// パンくずリスト：カスタム親ページ メタボックス
// ================================

function riff_breadcrumb_meta_box_register() {
    add_meta_box(
        'riff_breadcrumb_settings',
        'パンくずリスト設定',
        'riff_breadcrumb_meta_box_render',
        'page',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'riff_breadcrumb_meta_box_register' );

function riff_breadcrumb_meta_box_render( $post ) {
    wp_nonce_field( 'riff_breadcrumb_meta', 'riff_breadcrumb_nonce' );
    $parent_id = get_post_meta( $post->ID, '_riff_breadcrumb_parent', true );
    $pages     = get_pages( array( 'exclude' => array( $post->ID ), 'sort_column' => 'menu_order', 'sort_order' => 'ASC' ) );
    ?>
    <p style="margin-top:0;font-size:12px;color:#666;">WordPressの「親ページ」とは独立したパンくず専用の親ページです。URLには影響しません。</p>
    <label for="riff_breadcrumb_parent" style="font-weight:600;display:block;margin-bottom:4px;">パンくず親ページ</label>
    <select name="riff_breadcrumb_parent" id="riff_breadcrumb_parent" style="width:100%;">
        <option value="">（なし）</option>
        <?php foreach ( $pages as $page ) : ?>
        <option value="<?php echo esc_attr( $page->ID ); ?>"<?php selected( $parent_id, $page->ID ); ?>><?php echo esc_html( $page->post_title ); ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}

function riff_breadcrumb_meta_box_save( $post_id ) {
    if ( ! isset( $_POST['riff_breadcrumb_nonce'] ) || ! wp_verify_nonce( $_POST['riff_breadcrumb_nonce'], 'riff_breadcrumb_meta' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( isset( $_POST['riff_breadcrumb_parent'] ) ) {
        $parent_id = absint( $_POST['riff_breadcrumb_parent'] );
        if ( $parent_id ) {
            update_post_meta( $post_id, '_riff_breadcrumb_parent', $parent_id );
        } else {
            delete_post_meta( $post_id, '_riff_breadcrumb_parent' );
        }
    }
}
add_action( 'save_post', 'riff_breadcrumb_meta_box_save' );

/**
 * 固定ページのパンくずリスト
 */
/**
 * 実績詳細ページ（portfolio_XX / protfolio_XX / portpolio_XX / business_efficiency_appN
 * など、これまでの実績追加で使われてきたスラッグ表記ゆれをすべて含む）かどうかをスラッグから判定し、
 * 該当する場合は「実績」一覧ページのIDを返す。パンくず親が未設定の新規追加ページに自動適用される。
 */
function riff_maybe_get_portfolio_parent_id( $post_id ) {
    $slug = get_post_field( 'post_name', $post_id );
    if ( $slug === 'portfolio' ) {
        return 0; // 実績一覧ページ自身は除外（自己参照防止）
    }
    if ( ! preg_match( '/^(portfolio|protfolio|portpolio|business_efficiency_app)/i', $slug ) ) {
        return 0;
    }
    $archive = get_page_by_path( 'portfolio' );
    return $archive ? $archive->ID : 0;
}

function riff_breadcrumb_page() {
    global $post;

    // カスタム親ページメタ → WordPress post_parent の順で祖先チェーンを構築
    $chain      = array();
    $current_id = $post->ID;
    $visited    = array( $current_id ); // 無限ループ防止

    while ( true ) {
        $custom_parent = get_post_meta( $current_id, '_riff_breadcrumb_parent', true );
        if ( $custom_parent ) {
            $parent_id = intval( $custom_parent );
        } else {
            $page      = get_post( $current_id );
            $parent_id = ( $page && $page->post_parent ) ? $page->post_parent : 0;

            // 実績詳細ページ（portfolio_XX等）はパンくず親が未設定でも
            // 自動的に「実績」一覧ページを親とする（今後追加するページにも適用される）
            if ( ! $parent_id && $current_id === $post->ID ) {
                $parent_id = riff_maybe_get_portfolio_parent_id( $current_id );
            }
        }

        if ( ! $parent_id || in_array( $parent_id, $visited ) ) {
            break;
        }

        $visited[]  = $parent_id;
        $chain[]    = $parent_id;
        $current_id = $parent_id;
    }

    // ルートから順に出力
    $chain = array_reverse( $chain );
    foreach ( $chain as $ancestor_id ) {
        echo '<a href="' . esc_url( get_permalink( $ancestor_id ) ) . '" class="breadcrumb-parent">' . esc_html( get_the_title( $ancestor_id ) ) . '</a>';
        echo '<span class="breadcrumb-separator" aria-hidden="true">/</span>';
    }

    // 現在のページ
    echo '<span class="breadcrumb-current">' . esc_html( get_the_title() ) . '</span>';
}

/**
 * カテゴリーアーカイブのパンくずリスト
 */
function riff_breadcrumb_category() {
    $category = get_queried_object();
    
    // まずブログアーカイブへのリンクを追加
    echo '<a href="' . esc_url(home_url('/blog/')) . '" class="breadcrumb-archive">ブログ記事</a>';
    echo '<span class="breadcrumb-separator" aria-hidden="true">/</span>';
    
    // 親カテゴリーがある場合
    if ($category->parent) {
        $ancestors = get_ancestors($category->term_id, 'category');
        $ancestors = array_reverse($ancestors);
        
        foreach ($ancestors as $ancestor) {
            $ancestor_category = get_category($ancestor);
            echo '<a href="' . esc_url(get_category_link($ancestor)) . '" class="breadcrumb-parent-category">' . esc_html($ancestor_category->name) . '</a>';
            echo '<span class="breadcrumb-separator" aria-hidden="true">/</span>';
        }
    }
    
    // 現在のカテゴリー
    echo '<span class="breadcrumb-current">' . esc_html(single_cat_title('', false)) . '</span>';
}

/**
 * タグアーカイブのパンくずリスト
 */
function riff_breadcrumb_tag() {
    echo '<a href="' . esc_url(home_url('/blog/')) . '" class="breadcrumb-archive">ブログ記事</a>';
    echo '<span class="breadcrumb-separator" aria-hidden="true">/</span>';
    echo '<span class="breadcrumb-current">タグ: ' . esc_html(single_tag_title('', false)) . '</span>';
}

/**
 * 著者アーカイブのパンくずリスト
 */
function riff_breadcrumb_author() {
    echo '<a href="' . esc_url(home_url('/blog/')) . '" class="breadcrumb-archive">ブログ記事</a>';
    echo '<span class="breadcrumb-separator" aria-hidden="true">/</span>';
    echo '<span class="breadcrumb-current">著者: ' . esc_html(get_the_author()) . '</span>';
}

/**
 * 日付アーカイブのパンくずリスト
 */
function riff_breadcrumb_date() {
    echo '<a href="' . esc_url(home_url('/blog/')) . '" class="breadcrumb-archive">ブログ記事</a>';
    echo '<span class="breadcrumb-separator" aria-hidden="true">/</span>';
    
    if (is_year()) {
        echo '<span class="breadcrumb-current">' . esc_html(get_the_date('Y年')) . '</span>';
    } elseif (is_month()) {
        echo '<a href="' . esc_url(get_year_link(get_the_date('Y'))) . '" class="breadcrumb-year">' . esc_html(get_the_date('Y年')) . '</a>';
        echo '<span class="breadcrumb-separator" aria-hidden="true">/</span>';
        echo '<span class="breadcrumb-current">' . esc_html(get_the_date('n月')) . '</span>';
    } elseif (is_day()) {
        echo '<a href="' . esc_url(get_year_link(get_the_date('Y'))) . '" class="breadcrumb-year">' . esc_html(get_the_date('Y年')) . '</a>';
        echo '<span class="breadcrumb-separator" aria-hidden="true">/</span>';
        echo '<a href="' . esc_url(get_month_link(get_the_date('Y'), get_the_date('n'))) . '" class="breadcrumb-month">' . esc_html(get_the_date('n月')) . '</a>';
        echo '<span class="breadcrumb-separator" aria-hidden="true">/</span>';
        echo '<span class="breadcrumb-current">' . esc_html(get_the_date('j日')) . '</span>';
    }
}

/**
 * 検索結果ページのパンくずリスト
 */
function riff_breadcrumb_search() {
    echo '<span class="breadcrumb-current">検索結果: ' . esc_html(get_search_query()) . '</span>';
}

/**
 * パンくずリスト用の構造化データを出力
 */
function riff_breadcrumb_structured_data() {
    if (is_front_page()) {
        return;
    }
    
    $breadcrumbs = array();
    $position = 1;
    
    // ホーム
    $breadcrumbs[] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => 'ホーム',
        'item' => home_url('/')
    );
    
    if (is_single()) {
        global $post;
        $post_type_flag = get_post_meta($post->ID, 'post_type_flag', true);
        $post_type_flag = $post_type_flag ? $post_type_flag : 'news';
        
        // アーカイブページ
        if ($post_type_flag === 'blog' || ($post_type_flag === 'both' && strpos($_SERVER['REQUEST_URI'], '/blog/') !== false)) {
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => 'ブログ記事',
                'item' => home_url('/blog/')
            );
        } else {
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => 'ニュース・お知らせ',
                'item' => home_url('/news/')
            );
        }
        
        // カテゴリー
        $categories = get_the_category();
        if (!empty($categories)) {
            $primary_category = $categories[0];
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $primary_category->name,
                'item' => get_category_link($primary_category->term_id)
            );
        }
        
        // 現在のページ
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title(),
            'item' => get_permalink()
        );
        
    } elseif (is_page()) {
        global $post;
        
        // 親ページ
        if ($post->post_parent) {
            $ancestors = get_post_ancestors($post->ID);
            $ancestors = array_reverse($ancestors);
            
            foreach ($ancestors as $ancestor) {
                $breadcrumbs[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => get_the_title($ancestor),
                    'item' => get_permalink($ancestor)
                );
            }
        }
        
        // 現在のページ
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_the_title(),
            'item' => get_permalink()
        );
    }
    
    if (count($breadcrumbs) > 1) {
        $structured_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs
        );
        
        echo '<script type="application/ld+json">' . wp_json_encode($structured_data, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
add_action('wp_head', 'riff_breadcrumb_structured_data');

// ================================
// テキストエリア用改行タグ許可機能（PC/SP対応版）
// ================================

/**
 * 安全なHTMLタグ（改行）を許可するサニタイズ関数
 * <br>、<br class="pc">、<br class="sp">を許可
 */
function riff_sanitize_textarea_with_br($input) {
    // 許可するHTMLタグ（brタグとclass属性）
    $allowed_html = array(
        'br' => array(
            'class' => array(),
        ),
    );
    
    // HTMLタグをサニタイズしつつ、改行タグは保持
    $sanitized = wp_kses($input, $allowed_html);
    
    // 改行文字を通常の<br>タグに変換（ユーザーが改行で入力した場合）
    $sanitized = nl2br($sanitized);
    
    // 重複する<br>タグをクリーンアップ
    $sanitized = preg_replace('/<br\s*\/?>\s*<br\s*\/?>/i', '<br>', $sanitized);
    
    return trim($sanitized);
}

/**
 * テキストエリアの内容を安全に出力する関数
 * 改行タグを正しく表示（PC/SP対応）
 */
function riff_output_textarea_with_br($content) {
    // 空の場合は空文字を返す
    if (empty($content)) {
        return '';
    }
    
    // 既にbrタグが含まれている場合はそのまま出力
    if (strpos($content, '<br') !== false) {
        return $content;
    }
    
    // プレーンテキストの場合は改行を<br>タグに変換
    return nl2br(esc_html($content));
}

/**
 * カスタムローディング画面機能
 * functions.phpの最後に追加してください
 */

// ローディング画面のカスタマイザー設定を追加
function loading_screen_customizer($wp_customize) {
    // ローディング画面セクションを追加
    $wp_customize->add_section('loading_screen_section', array(
        'title' => 'ローディング画面設定',
        'description' => 'ローディング画面の表示設定をカスタマイズできます',
        'priority' => 30,
    ));

    // ローディング画面の表示/非表示設定
    $wp_customize->add_setting('loading_screen_enable', array(
        'default' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('loading_screen_enable', array(
        'label' => 'ローディング画面を表示する',
        'section' => 'loading_screen_section',
        'type' => 'checkbox',
        'priority' => 10,
    ));

    // ロゴ/タイトル選択設定
    $wp_customize->add_setting('loading_display_type', array(
        'default' => 'title',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('loading_display_type', array(
        'label' => '表示タイプ',
        'section' => 'loading_screen_section',
        'type' => 'select',
        'choices' => array(
            'title' => 'タイトル',
            'logo' => 'ロゴ画像',
        ),
        'priority' => 20,
    ));

    // ロゴ画像設定
    $wp_customize->add_setting('loading_logo_image', array(
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'loading_logo_image', array(
        'label' => 'ロゴ画像',
        'section' => 'loading_screen_section',
        'priority' => 30,
    )));

    // タイトルテキスト設定
    $wp_customize->add_setting('loading_title_text', array(
        'default' => get_bloginfo('name'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('loading_title_text', array(
        'label' => 'タイトルテキスト',
        'section' => 'loading_screen_section',
        'type' => 'text',
        'priority' => 40,
    ));

    // 最初のテキスト設定
    $wp_customize->add_setting('loading_first_text', array(
        'default' => 'Loading...',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('loading_first_text', array(
        'label' => '最初のテキスト',
        'section' => 'loading_screen_section',
        'type' => 'text',
        'priority' => 50,
    ));

    // 背景色1（最初の黒背景）
    $wp_customize->add_setting('loading_bg_color1', array(
        'default' => '#000000',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'loading_bg_color1', array(
        'label' => '最初の背景色',
        'section' => 'loading_screen_section',
        'priority' => 60,
    )));

    // 背景色2（赤背景）
    $wp_customize->add_setting('loading_bg_color2', array(
        'default' => '#e74c3c',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'loading_bg_color2', array(
        'label' => 'セカンド背景色',
        'section' => 'loading_screen_section',
        'priority' => 70,
    )));

    // テキスト色
    $wp_customize->add_setting('loading_text_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'loading_text_color', array(
        'label' => 'テキスト色',
        'section' => 'loading_screen_section',
        'priority' => 80,
    )));

    // フォント選択設定
    $wp_customize->add_setting('loading_font_family', array(
        'default' => 'default',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('loading_font_family', array(
        'label' => 'フォント',
        'section' => 'loading_screen_section',
        'type' => 'select',
        'choices' => array(
            'default' => 'テーマデフォルト',
            'noto-sans' => 'Noto Sans JP',
            'hiragino' => 'ヒラギノ角ゴシック',
            'yu-gothic' => '游ゴシック',
            'meiryo' => 'メイリオ',
            'roboto' => 'Roboto',
            'open-sans' => 'Open Sans',
            'lato' => 'Lato',
            'montserrat' => 'Montserrat',
            'poppins' => 'Poppins',
        ),
        'priority' => 90,
    ));

    // 第1フェーズ表示時間設定
    $wp_customize->add_setting('loading_phase1_duration', array(
        'default' => 3,
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('loading_phase1_duration', array(
        'label' => '最初の画面表示時間（秒）',
        'description' => '黒背景とテキストの表示時間を設定します',
        'section' => 'loading_screen_section',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 10,
            'step' => 0.5,
        ),
        'priority' => 100,
    ));

    // 第2フェーズ表示時間設定
    $wp_customize->add_setting('loading_phase2_duration', array(
        'default' => 2,
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('loading_phase2_duration', array(
        'label' => 'タイトル画面表示時間（秒）',
        'description' => '赤背景とタイトル/ロゴの表示時間を設定します',
        'section' => 'loading_screen_section',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 10,
            'step' => 0.5,
        ),
        'priority' => 110,
    ));

    // テキストアニメーション設定
    $wp_customize->add_setting('loading_text_animation', array(
        'default' => 'soft-fade',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('loading_text_animation', array(
        'label' => 'テキストアニメーション',
        'section' => 'loading_screen_section',
        'type' => 'select',
        'choices' => array(
            'soft-fade' => 'ふわっと表示（ソフト）',
            'gentle-fade' => 'ゆっくりふわっと表示',
            'bounce-fade' => 'バウンスふわっと表示',
            'scale-fade' => 'スケールふわっと表示',
            'slide-fade' => 'スライドふわっと表示',
        ),
        'priority' => 120,
    ));
}
add_action('customize_register', 'loading_screen_customizer');

// ================================
// 修正されたローディング画面機能（front-pageのみ対応）
// ================================

// CSS・JSファイルを読み込み（front-pageのみ）
function loading_screen_enqueue_scripts() {
    // front-pageでローディング画面が有効な場合のみ読み込み
    if (is_front_page() && get_theme_mod('loading_screen_enable', true)) {
        wp_enqueue_style('loading-screen-css', get_template_directory_uri() . '/loading-screen/loading-screen.css', array(), '1.0.1');
        wp_enqueue_script('loading-screen-js', get_template_directory_uri() . '/loading-screen/loading-screen.js', array('jquery'), '1.0.1', true);
        
        // カスタマイザーの設定をJavaScriptに渡す
        $loading_settings = array(
            'enable' => get_theme_mod('loading_screen_enable', true),
            'display_type' => get_theme_mod('loading_display_type', 'title'),
            'logo_image' => get_theme_mod('loading_logo_image', ''),
            'title_text' => get_theme_mod('loading_title_text', get_bloginfo('name')),
            'first_text' => get_theme_mod('loading_first_text', 'Loading...'),
            'bg_color1' => get_theme_mod('loading_bg_color1', '#000000'),
            'bg_color2' => get_theme_mod('loading_bg_color2', '#e74c3c'),
            'text_color' => get_theme_mod('loading_text_color', '#ffffff'),
            'font_family' => get_theme_mod('loading_font_family', 'default'),
            'phase1_duration' => (float) get_theme_mod('loading_phase1_duration', 3),
			'phase2_duration' => (float) get_theme_mod('loading_phase2_duration', 2),
            'text_animation' => get_theme_mod('loading_text_animation', 'soft-fade'),
        );
        wp_localize_script('loading-screen-js', 'loadingSettings', $loading_settings);
        
        // デバッグ情報（開発時のみ）
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Loading screen assets enqueued for front-page');
        }
    }
}
add_action('wp_enqueue_scripts', 'loading_screen_enqueue_scripts');

// ローディング画面HTMLをフッターに追加（front-pageのみ）
function loading_screen_html() {
    // front-pageでローディング画面が有効な場合のみHTML出力
    if (is_front_page() && get_theme_mod('loading_screen_enable', true)) {
        echo '<div id="loading-screen-container"></div>';
        
        // デバッグ情報（開発時のみ）
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Loading screen HTML output for front-page');
        }
    }
}
add_action('wp_body_open', 'loading_screen_html');

// ローディング画面用のbody classを追加（front-pageのみ）
function loading_screen_body_class($classes) {
    // front-pageでローディング画面が有効な場合のみbody classを追加
    if (is_front_page() && get_theme_mod('loading_screen_enable', true)) {
        $classes[] = 'loading-screen-enabled';
        $classes[] = 'loading-active';
    }
    return $classes;
}
add_filter('body_class', 'loading_screen_body_class');

// ローディング画面の無効化を確実にする（他ページ用）
function ensure_loading_screen_disabled_on_other_pages() {
    // front-page以外では確実にローディング関連のスタイルを無効化
    if (!is_front_page()) {
        ?>
        <style type="text/css">
        /* front-page以外では確実にローディング画面を無効化 */
        #loading-screen-container,
        .loading-screen,
        .loading-screen-container {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
        
        /* body要素のローディングクラスも無効化 */
        body.loading-active,
        body.loading-screen-enabled {
            overflow: auto !important;
            height: auto !important;
        }
        
        /* 他ページでのパフォーマンス最適化 */
        body {
            visibility: visible !important;
            opacity: 1 !important;
        }
        </style>
        
        <script type="text/javascript">
        // JavaScript側でもローディング画面を無効化
        document.addEventListener('DOMContentLoaded', function() {
            // ローディング関連の要素を確実に削除
            var loadingContainers = document.querySelectorAll('#loading-screen-container, .loading-screen, .loading-screen-container');
            loadingContainers.forEach(function(container) {
                if (container) {
                    container.remove();
                }
            });
            
            // ローディング関連のbodyクラスを削除
            document.body.classList.remove('loading-active', 'loading-screen-enabled');
            
            // bodyのスタイルをリセット
            document.body.style.overflow = '';
            document.body.style.height = '';
            document.body.style.visibility = '';
            document.body.style.opacity = '';
            
            // グローバル変数のクリーンアップ
            if (typeof loadingSettings !== 'undefined') {
                window.loadingSettings = null;
            }
            if (typeof window.loadingScreenInstance !== 'undefined') {
                window.loadingScreenInstance = null;
            }
        });
        </script>
        <?php
    }
}
add_action('wp_head', 'ensure_loading_screen_disabled_on_other_pages', 1);

// ページ遷移時のクリーンアップ
function loading_screen_cleanup_on_page_transition() {
    if (!is_front_page()) {
        ?>
        <script type="text/javascript">
        // ページ遷移時のクリーンアップ処理
        (function() {
            'use strict';
            
            // ページ読み込み完了後すぐに実行
            function cleanupLoadingScreen() {
                // ローディング画面関連の要素を完全に削除
                var loadingElements = document.querySelectorAll('#loading-screen-container, .loading-screen, [class*="loading-"]');
                loadingElements.forEach(function(element) {
                    if (element && element.parentNode) {
                        element.parentNode.removeChild(element);
                    }
                });
                
                // ローディング関連のスタイルタグを削除
                var loadingStyles = document.querySelectorAll('style[id*="loading"], link[href*="loading-screen"]');
                loadingStyles.forEach(function(style) {
                    if (style && style.parentNode) {
                        style.parentNode.removeChild(style);
                    }
                });
                
                // bodyのローディングクラスを確実に削除
                var bodyClassList = document.body.classList;
                var loadingClasses = ['loading-active', 'loading-screen-enabled'];
                loadingClasses.forEach(function(className) {
                    bodyClassList.remove(className);
                });
                
                // bodyのスタイルをリセット
                document.body.style.overflow = '';
                document.body.style.height = '';
                document.body.style.visibility = 'visible';
                document.body.style.opacity = '1';
                
                if (typeof console !== 'undefined' && console.log) {
                    console.log('Loading screen cleanup completed for non-front-page');
                }
            }
            
            // DOM準備完了時とページ読み込み完了時の両方で実行
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', cleanupLoadingScreen);
            } else {
                cleanupLoadingScreen();
            }
            
            window.addEventListener('load', cleanupLoadingScreen);
            
            // 念のため遅延実行も追加
            setTimeout(cleanupLoadingScreen, 100);
        })();
        </script>
        <?php
    }
}
add_action('wp_footer', 'loading_screen_cleanup_on_page_transition', 1);

// 管理画面でのローディング画面無効化
function disable_loading_screen_in_admin() {
    if (is_admin() || is_customize_preview()) {
        ?>
        <style type="text/css">
        #loading-screen-container,
        .loading-screen {
            display: none !important;
        }
        </style>
        <?php
    }
}
add_action('admin_head', 'disable_loading_screen_in_admin');
add_action('wp_head', 'disable_loading_screen_in_admin');

// ================================
// FAQ セクション機能
// ================================

/**
 * FAQデフォルトデータ
 */
function riff_get_default_faq_items() {
    return array(
        array( 'id' => 'web_1',    'category' => 'web',    'question' => 'WEB制作の料金はいくらですか？',                        'answer' => '制作費用はサイトの規模・機能・デザインの複雑さなどによって大きく異なります。まずはお気軽に公式LINEにてご相談ください。お見積りは無料です。',                                                                                                          'order' => 1, 'enabled' => true ),
        array( 'id' => 'web_2',    'category' => 'web',    'question' => '制作期間はどのくらいかかりますか？',                    'answer' => 'コーポレートサイトの場合、ヒアリングから公開まで通常1〜3ヶ月程度です。ページ数や機能の複雑さによって変わります。お急ぎの場合はご相談ください。',                                                                                               'order' => 2, 'enabled' => true ),
        array( 'id' => 'web_3',    'category' => 'web',    'question' => 'どのようなWEBサイトを制作できますか？',                  'answer' => 'コーポレートサイト、LP（ランディングページ）、採用サイト、ECサイト、ポータルサイトなど幅広く対応しております。まずはご要望をお聞かせください。',                                                                                                  'order' => 3, 'enabled' => true ),
        array( 'id' => 'web_4',    'category' => 'web',    'question' => 'スマートフォン対応（レスポンシブ）は含まれますか？',     'answer' => 'はい、すべての制作物はスマートフォン・タブレット・PCに対応したレスポンシブデザインで制作しています。追加費用はかかりません。',                                                                                                              'order' => 4, 'enabled' => true ),
        array( 'id' => 'web_5',    'category' => 'web',    'question' => '公開後のサポート・保守は対応していますか？',             'answer' => '公開後のコンテンツ更新・不具合対応・セキュリティアップデートなど、継続的なサポートプランをご用意しています。詳細は公式LINEにてお問い合わせください。',                                                                                        'order' => 5, 'enabled' => true ),
        array( 'id' => 'web_6',    'category' => 'web',    'question' => 'WordPressでの制作は可能ですか？',                       'answer' => 'はい、WordPressでの制作に対応しています。お客様自身でコンテンツを更新・管理できるよう、使いやすい管理画面をカスタマイズしてご提供します。',                                                                                                  'order' => 6, 'enabled' => true ),
        array( 'id' => 'system_1', 'category' => 'system', 'question' => 'システム開発の費用はいくらですか？',                    'answer' => '開発費用はシステムの規模・機能・要件によって大きく異なります。まずは要件をお聞きし、お見積りをご提示します。公式LINEにてお気軽にご相談ください。',                                                                                          'order' => 1, 'enabled' => true ),
        array( 'id' => 'system_2', 'category' => 'system', 'question' => 'どのような業務システムを開発できますか？',              'answer' => '受発注管理・在庫管理・予約システム・社内ポータル・顧客管理（CRM）・各種業務効率化ツールなど、幅広いシステム開発に対応しています。',                                                                                                          'order' => 2, 'enabled' => true ),
        array( 'id' => 'system_3', 'category' => 'system', 'question' => '既存システムとの連携・カスタマイズは可能ですか？',       'answer' => 'はい、既存システムとのAPI連携やデータ移行、機能追加・カスタマイズにも対応しています。まずは現在の状況をお聞かせください。',                                                                                                                    'order' => 3, 'enabled' => true ),
        array( 'id' => 'system_4', 'category' => 'system', 'question' => 'スマートフォンアプリの開発も対応していますか？',         'answer' => 'はい、iOS・Androidアプリの開発にも対応しています。WEBアプリとの連携も含め、最適な開発方法をご提案します。詳細は公式LINEにてご相談ください。',                                                                                               'order' => 4, 'enabled' => true ),
        array( 'id' => 'system_5', 'category' => 'system', 'question' => '開発後の保守・運用サポートはありますか？',              'answer' => '公開後も継続的なサポートプランをご用意しています。バグ修正・機能追加・セキュリティ対応など、安定した運用をお手伝いします。公式LINEにてお問い合わせください。',                                                                              'order' => 5, 'enabled' => true ),
        array( 'id' => 'system_6', 'category' => 'system', 'question' => 'セキュリティ対策はどのようになっていますか？',          'answer' => '開発段階からセキュリティを考慮した設計・実装を行っています。SSL/TLS対応・不正アクセス対策・定期的な脆弱性チェックなども適切に実施します。',                                                                                                 'order' => 6, 'enabled' => true ),
    );
}

/**
 * FAQ アイテム取得（保存済み or デフォルト）
 */
function riff_get_faq_items() {
    $items = get_option( 'riff_faq_items', array() );
    if ( empty( $items ) ) {
        $items = riff_get_default_faq_items();
        update_option( 'riff_faq_items', $items );
    }
    return $items;
}

/**
 * 管理メニュー登録
 */
function riff_faq_admin_page_register() {
    add_submenu_page(
        'riff-theme-settings',
        'FAQ設定',
        'FAQ設定',
        'manage_options',
        'riff-faq-settings',
        'riff_faq_admin_page_content'
    );
}
add_action( 'admin_menu', 'riff_faq_admin_page_register' );

/**
 * FAQ 管理画面コンテンツ
 */
function riff_faq_admin_page_content() {

    // 保存処理
    if ( isset( $_POST['riff_faq_submit'] ) ) {
        if ( ! isset( $_POST['riff_faq_nonce'] ) || ! wp_verify_nonce( $_POST['riff_faq_nonce'], 'riff_faq_settings' ) ) {
            wp_die( 'セキュリティチェックに失敗しました。' );
        }

        $new_items = array();
        $categories = array( 'web', 'system' );
        foreach ( $categories as $cat ) {
            if ( ! isset( $_POST['faq'][ $cat ] ) || ! is_array( $_POST['faq'][ $cat ] ) ) {
                continue;
            }
            $order = 1;
            foreach ( $_POST['faq'][ $cat ] as $raw ) {
                $q = sanitize_textarea_field( $raw['question'] ?? '' );
                $a = sanitize_textarea_field( $raw['answer']   ?? '' );
                if ( $q === '' && $a === '' ) {
                    continue;
                }
                $new_items[] = array(
                    'id'       => $cat . '_' . $order,
                    'category' => $cat,
                    'question' => $q,
                    'answer'   => $a,
                    'order'    => $order,
                    'enabled'  => isset( $raw['enabled'] ) ? true : false,
                );
                $order++;
            }
        }
        // LINE URL
        update_option( 'riff_faq_line_url',     esc_url_raw( $_POST['faq_line_url'] ?? '' ) );
        update_option( 'riff_faq_section_title', sanitize_text_field( $_POST['faq_section_title'] ?? 'FAQ' ) );
        update_option( 'riff_faq_section_subtitle', sanitize_text_field( $_POST['faq_section_subtitle'] ?? 'よくあるご質問' ) );
        update_option( 'riff_faq_items', $new_items );

        echo '<div class="notice notice-success is-dismissible"><p>FAQ設定を保存しました。</p></div>';
    }

    $items         = riff_get_faq_items();
    $line_url      = get_option( 'riff_faq_line_url', '' );
    $sec_title     = get_option( 'riff_faq_section_title', 'FAQ' );
    $sec_subtitle  = get_option( 'riff_faq_section_subtitle', 'よくあるご質問' );

    // カテゴリーごとに振り分け
    $web_items    = array_values( array_filter( $items, function( $i ) { return $i['category'] === 'web'; } ) );
    $system_items = array_values( array_filter( $items, function( $i ) { return $i['category'] === 'system'; } ) );
    ?>
    <div class="wrap">
        <h1>FAQ設定</h1>

        <form method="post" action="" id="riff-faq-form">
            <?php wp_nonce_field( 'riff_faq_settings', 'riff_faq_nonce' ); ?>

            <!-- セクション基本設定 -->
            <h2 class="riff-section-heading">セクション基本設定</h2>
            <table class="form-table">
                <tr>
                    <th>セクションタイトル</th>
                    <td><input type="text" name="faq_section_title" value="<?php echo esc_attr( $sec_title ); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th>サブタイトル</th>
                    <td><input type="text" name="faq_section_subtitle" value="<?php echo esc_attr( $sec_subtitle ); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th>公式LINE URL</th>
                    <td>
                        <input type="url" name="faq_line_url" value="<?php echo esc_attr( $line_url ); ?>" class="regular-text" placeholder="https://lin.ee/xxxxxxx" />
                        <p class="description">料金に関する質問の回答末尾に「公式LINEにてお問い合わせください」ボタンを表示します。</p>
                    </td>
                </tr>
            </table>

            <!-- WEB制作 FAQ -->
            <h2 class="riff-section-heading" style="background:#e3f2fd;padding:12px 16px;border-radius:4px;color:#1565c0;">WEB制作 FAQ</h2>
            <div id="faq-web-container">
                <?php foreach ( $web_items as $idx => $item ) : ?>
                <div class="faq-row" style="background:#fff;border:1px solid #ddd;border-radius:6px;padding:16px;margin-bottom:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <strong style="color:#1565c0;">Q<?php echo $idx + 1; ?></strong>
                        <label style="font-size:13px;display:flex;align-items:center;gap:6px;">
                            <input type="checkbox" name="faq[web][<?php echo $idx; ?>][enabled]" value="1" <?php checked( $item['enabled'] ); ?>> 表示する
                        </label>
                    </div>
                    <table class="form-table" style="margin:0;">
                        <tr>
                            <th style="width:80px;padding:6px 0;">質問</th>
                            <td style="padding:6px 0;"><input type="text" name="faq[web][<?php echo $idx; ?>][question]" value="<?php echo esc_attr( $item['question'] ); ?>" class="widefat" /></td>
                        </tr>
                        <tr>
                            <th style="padding:6px 0;">回答</th>
                            <td style="padding:6px 0;"><textarea name="faq[web][<?php echo $idx; ?>][answer]" rows="3" class="widefat"><?php echo esc_textarea( $item['answer'] ); ?></textarea></td>
                        </tr>
                    </table>
                    <button type="button" class="button faq-remove-btn" style="margin-top:8px;color:#c00;">この項目を削除</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button button-secondary" id="add-web-faq" style="margin-bottom:30px;">＋ WEB制作FAQを追加</button>

            <!-- システム開発 FAQ -->
            <h2 class="riff-section-heading" style="background:#f3e5f5;padding:12px 16px;border-radius:4px;color:#6a1b9a;">システム開発 FAQ</h2>
            <div id="faq-system-container">
                <?php foreach ( $system_items as $idx => $item ) : ?>
                <div class="faq-row" style="background:#fff;border:1px solid #ddd;border-radius:6px;padding:16px;margin-bottom:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <strong style="color:#6a1b9a;">Q<?php echo $idx + 1; ?></strong>
                        <label style="font-size:13px;display:flex;align-items:center;gap:6px;">
                            <input type="checkbox" name="faq[system][<?php echo $idx; ?>][enabled]" value="1" <?php checked( $item['enabled'] ); ?>> 表示する
                        </label>
                    </div>
                    <table class="form-table" style="margin:0;">
                        <tr>
                            <th style="width:80px;padding:6px 0;">質問</th>
                            <td style="padding:6px 0;"><input type="text" name="faq[system][<?php echo $idx; ?>][question]" value="<?php echo esc_attr( $item['question'] ); ?>" class="widefat" /></td>
                        </tr>
                        <tr>
                            <th style="padding:6px 0;">回答</th>
                            <td style="padding:6px 0;"><textarea name="faq[system][<?php echo $idx; ?>][answer]" rows="3" class="widefat"><?php echo esc_textarea( $item['answer'] ); ?></textarea></td>
                        </tr>
                    </table>
                    <button type="button" class="button faq-remove-btn" style="margin-top:8px;color:#c00;">この項目を削除</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button button-secondary" id="add-system-faq" style="margin-bottom:30px;">＋ システム開発FAQを追加</button>

            <?php submit_button( '設定を保存する', 'primary', 'riff_faq_submit' ); ?>
        </form>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // 行追加テンプレート
        function newRow(cat, idx) {
            return `<div class="faq-row" style="background:#fff;border:1px solid #ddd;border-radius:6px;padding:16px;margin-bottom:12px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <strong>Q${idx + 1}</strong>
                    <label style="font-size:13px;display:flex;align-items:center;gap:6px;">
                        <input type="checkbox" name="faq[${cat}][${idx}][enabled]" value="1" checked> 表示する
                    </label>
                </div>
                <table class="form-table" style="margin:0;">
                    <tr>
                        <th style="width:80px;padding:6px 0;">質問</th>
                        <td style="padding:6px 0;"><input type="text" name="faq[${cat}][${idx}][question]" value="" class="widefat" /></td>
                    </tr>
                    <tr>
                        <th style="padding:6px 0;">回答</th>
                        <td style="padding:6px 0;"><textarea name="faq[${cat}][${idx}][answer]" rows="3" class="widefat"></textarea></td>
                    </tr>
                </table>
                <button type="button" class="button faq-remove-btn" style="margin-top:8px;color:#c00;">この項目を削除</button>
            </div>`;
        }

        $('#add-web-faq').on('click', function() {
            var count = $('#faq-web-container .faq-row').length;
            $('#faq-web-container').append(newRow('web', count));
        });

        $('#add-system-faq').on('click', function() {
            var count = $('#faq-system-container .faq-row').length;
            $('#faq-system-container').append(newRow('system', count));
        });

        $(document).on('click', '.faq-remove-btn', function() {
            if (confirm('この項目を削除しますか？')) {
                $(this).closest('.faq-row').remove();
            }
        });
    });
    </script>
    <?php
}

/**
 * FAQセクションをフロントエンドに表示する関数
 */
function riff_display_faq_section() {
    $items        = riff_get_faq_items();
    $line_url     = get_option( 'riff_faq_line_url', '' );
    $sec_title    = get_option( 'riff_faq_section_title', 'FAQ' );
    $sec_subtitle = get_option( 'riff_faq_section_subtitle', 'よくあるご質問' );

    $web_items    = array_values( array_filter( $items, function( $i ) { return $i['category'] === 'web'    && $i['enabled']; } ) );
    $system_items = array_values( array_filter( $items, function( $i ) { return $i['category'] === 'system' && $i['enabled']; } ) );

    if ( empty( $web_items ) && empty( $system_items ) ) {
        return;
    }
    ?>
    <section class="riff-faq-section fadeDownTrigger" id="faq">
        <div class="riff-faq-inner">
            <!-- ヘッダー -->
            <div class="riff-faq-header fade-in">
                <p class="riff-faq-subtitle"><?php echo esc_html( $sec_subtitle ); ?></p>
                <h2 class="riff-faq-title"><?php echo esc_html( $sec_title ); ?></h2>
            </div>

            <!-- タブ -->
            <div class="riff-faq-tabs fade-in" data-delay="0.1s">
                <?php if ( ! empty( $web_items ) ) : ?>
                <button class="riff-faq-tab active" data-target="faq-web">WEB制作</button>
                <?php endif; ?>
                <?php if ( ! empty( $system_items ) ) : ?>
                <button class="riff-faq-tab<?php echo empty( $web_items ) ? ' active' : ''; ?>" data-target="faq-system">システム開発</button>
                <?php endif; ?>
            </div>

            <!-- WEB制作 パネル -->
            <?php if ( ! empty( $web_items ) ) : ?>
            <div class="riff-faq-panel<?php echo ! empty( $web_items ) ? ' active' : ''; ?>" id="faq-web">
                <?php foreach ( $web_items as $idx => $item ) : ?>
                <div class="riff-faq-item fade-in" data-delay="<?php echo 0.1 + $idx * 0.05; ?>s">
                    <button class="riff-faq-question" aria-expanded="false">
                        <span class="riff-faq-q-label">Q</span>
                        <span class="riff-faq-q-text"><?php echo esc_html( $item['question'] ); ?></span>
                        <span class="riff-faq-icon" aria-hidden="true"></span>
                    </button>
                    <div class="riff-faq-answer">
                        <span class="riff-faq-a-label">A</span>
                        <div class="riff-faq-a-text">
                            <?php echo nl2br( esc_html( $item['answer'] ) ); ?>
                            <?php if ( $line_url && mb_strpos( $item['answer'], '公式LINE' ) !== false ) : ?>
                            <a href="<?php echo esc_url( $line_url ); ?>" class="riff-faq-line-btn" target="_blank" rel="noopener">公式LINEでお問い合わせ</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- システム開発 パネル -->
            <?php if ( ! empty( $system_items ) ) : ?>
            <div class="riff-faq-panel<?php echo empty( $web_items ) ? ' active' : ''; ?>" id="faq-system">
                <?php foreach ( $system_items as $idx => $item ) : ?>
                <div class="riff-faq-item fade-in" data-delay="<?php echo 0.1 + $idx * 0.05; ?>s">
                    <button class="riff-faq-question" aria-expanded="false">
                        <span class="riff-faq-q-label">Q</span>
                        <span class="riff-faq-q-text"><?php echo esc_html( $item['question'] ); ?></span>
                        <span class="riff-faq-icon" aria-hidden="true"></span>
                    </button>
                    <div class="riff-faq-answer">
                        <span class="riff-faq-a-label">A</span>
                        <div class="riff-faq-a-text">
                            <?php echo nl2br( esc_html( $item['answer'] ) ); ?>
                            <?php if ( $line_url && mb_strpos( $item['answer'], '公式LINE' ) !== false ) : ?>
                            <a href="<?php echo esc_url( $line_url ); ?>" class="riff-faq-line-btn" target="_blank" rel="noopener">公式LINEでお問い合わせ</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

/**
 * FAQ CSS をインラインで出力
 */
function riff_faq_inline_css() {
    if ( is_admin() ) return;
    ?>
    <style id="riff-faq-css">
    /* ===== FAQ Section ===== */
    .riff-faq-section {
        --primary-color: #b45309;
        padding: 80px 20px;
    }
    .riff-faq-inner {
        max-width: 860px;
        margin: 0 auto;
    }
    .riff-faq-header {
        text-align: center;
        margin-bottom: 48px;
    }
    .riff-faq-subtitle {
        font-size: 0.85rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: var(--primary-color, #007cba);
        margin: 0 0 8px;
    }
    .riff-faq-title {
        font-size: clamp(1.8rem, 4vw, 2.6rem);
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
        letter-spacing: 0.05em;
    }

    /* タブ */
    .riff-faq-tabs {
        display: flex;
        gap: 0;
        margin-bottom: 32px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid var(--primary-color, #007cba);
    }
    .riff-faq-tab {
        flex: 1;
        padding: 14px 20px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        background: #fff;
        color: var(--primary-color, #007cba);
        transition: background 0.25s, color 0.25s;
    }
    .riff-faq-tab.active {
        background: var(--primary-color, #007cba);
        color: #fff;
    }
    .riff-faq-tab:hover:not(.active) {
        background: rgba(251,146,60,0.08);
    }

    /* パネル */
    .riff-faq-panel { display: none; }
    .riff-faq-panel.active { display: block; }

    /* アコーディオン */
    .riff-faq-item {
        border-bottom: 1px solid #e0e0e0;
    }
    .riff-faq-item:first-child {
        border-top: 1px solid #e0e0e0;
    }
    .riff-faq-question {
        width: 100%;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 20px 0;
        background: none;
        border: none;
        cursor: pointer;
        text-align: left;
        font-size: 1rem;
        font-weight: 600;
        color: #1a1a1a;
        line-height: 1.5;
    }
    .riff-faq-question:hover .riff-faq-q-text {
        color: var(--primary-color, #007cba);
    }
    .riff-faq-q-label {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        background: var(--primary-color, #007cba);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 700;
        margin-top: 1px;
    }
    .riff-faq-q-text {
        flex: 1;
        transition: color 0.2s;
    }
    .riff-faq-icon {
        flex-shrink: 0;
        width: 20px;
        height: 20px;
        position: relative;
        margin-top: 6px;
        margin-left: auto;
    }
    .riff-faq-icon::before,
    .riff-faq-icon::after {
        content: '';
        position: absolute;
        background: var(--primary-color, #007cba);
        border-radius: 2px;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
    .riff-faq-icon::before { width: 20px; height: 2px; top: 9px; left: 0; }
    .riff-faq-icon::after  { width: 2px; height: 20px; top: 0; left: 9px; }
    .riff-faq-question[aria-expanded="true"] .riff-faq-icon::after {
        transform: rotate(90deg);
        opacity: 0;
    }
    .riff-faq-answer {
        display: none;
        gap: 14px;
        padding: 0 0 20px;
        overflow: hidden;
    }
    .riff-faq-answer.is-open {
        display: flex;
        animation: riffFaqOpen 0.3s ease;
    }
    @keyframes riffFaqOpen {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .riff-faq-a-label {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        background: #f0f0f0;
        color: #555;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 700;
        margin-top: 1px;
    }
    .riff-faq-a-text {
        flex: 1;
        font-size: 0.95rem;
        color: #444;
        line-height: 1.7;
    }

    /* LINE ボタン */
    .riff-faq-line-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 14px;
        padding: 10px 22px;
        background: #06c755;
        color: #fff;
        font-size: 0.9rem;
        font-weight: 600;
        border-radius: 24px;
        text-decoration: none;
        transition: opacity 0.2s;
    }
    .riff-faq-line-btn:hover { opacity: 0.85; color: #fff; }
    .riff-faq-line-btn::before {
        content: '';
        display: inline-block;
        width: 18px;
        height: 18px;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M12 2C6.48 2 2 6.03 2 11c0 2.7 1.26 5.12 3.28 6.82L4.5 22l4.36-2.28C9.88 19.89 10.93 20 12 20c5.52 0 10-4.03 10-9S17.52 2 12 2z'/%3E%3C/svg%3E") center/contain no-repeat;
    }

    /* レスポンシブ */
    @media (max-width: 600px) {
        .riff-faq-section { padding: 56px 16px; }
        .riff-faq-question { font-size: 0.92rem; padding: 16px 0; }
        .riff-faq-tab { padding: 12px 10px; font-size: 0.85rem; }
        .riff-faq-q-label, .riff-faq-a-label { width: 28px; height: 28px; font-size: 0.78rem; }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // タブ切り替え
        document.querySelectorAll('.riff-faq-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                var target = this.dataset.target;
                document.querySelectorAll('.riff-faq-tab').forEach(function(t) { t.classList.remove('active'); });
                document.querySelectorAll('.riff-faq-panel').forEach(function(p) { p.classList.remove('active'); });
                this.classList.add('active');
                var panel = document.getElementById(target);
                if (panel) panel.classList.add('active');
            });
        });

        // アコーディオン
        document.querySelectorAll('.riff-faq-question').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var answer  = this.nextElementSibling;
                var isOpen  = this.getAttribute('aria-expanded') === 'true';
                // 同一パネル内を全て閉じる
                var panel   = this.closest('.riff-faq-panel');
                if (panel) {
                    panel.querySelectorAll('.riff-faq-question').forEach(function(q) {
                        q.setAttribute('aria-expanded', 'false');
                        var a = q.nextElementSibling;
                        if (a) a.classList.remove('is-open');
                    });
                }
                if (!isOpen) {
                    this.setAttribute('aria-expanded', 'true');
                    answer.classList.add('is-open');
                }
            });
        });
    });
    </script>
    <?php
}
add_action( 'wp_head', 'riff_faq_inline_css', 103 );

/**
 * テーマ有効化時にFAQデフォルトデータを登録
 */
function riff_faq_activation_setup() {
    if ( ! get_option( 'riff_faq_items' ) ) {
        update_option( 'riff_faq_items', riff_get_default_faq_items() );
    }
}
add_action( 'after_switch_theme', 'riff_faq_activation_setup' );

/**
 * 管理画面サイドバーの「RIFFテーマ」サブメニューを非表示
 * タブナビゲーションのみで管理するため
 */
function riff_hide_admin_submenu() {
    ?>
    <style>
    #toplevel_page_riff-theme-settings .wp-submenu {
        display: none !important;
    }
    </style>
    <?php
}
add_action( 'admin_head', 'riff_hide_admin_submenu' );

/**
 * 固定ページ一覧に「複製」リンクを追加
 */
function riff_duplicate_page_link( $actions, $post ) {
    if ( $post->post_type !== 'page' ) {
        return $actions;
    }
    if ( ! current_user_can( 'edit_pages' ) ) {
        return $actions;
    }

    $nonce = wp_create_nonce( 'riff_duplicate_page_' . $post->ID );
    $url   = admin_url( 'admin.php?action=riff_duplicate_page&post_id=' . $post->ID . '&nonce=' . $nonce );

    $actions['duplicate'] = '<a href="' . esc_url( $url ) . '">複製</a>';

    return $actions;
}
add_filter( 'page_row_actions', 'riff_duplicate_page_link', 10, 2 );

/**
 * 複製処理
 */
function riff_do_duplicate_page() {
    if (
        ! isset( $_GET['action'] ) || $_GET['action'] !== 'riff_duplicate_page' ||
        ! isset( $_GET['post_id'] ) ||
        ! isset( $_GET['nonce'] )
    ) {
        return;
    }

    $post_id = intval( $_GET['post_id'] );

    if ( ! wp_verify_nonce( $_GET['nonce'], 'riff_duplicate_page_' . $post_id ) ) {
        wp_die( '不正なリクエストです。' );
    }

    if ( ! current_user_can( 'edit_pages' ) ) {
        wp_die( '権限がありません。' );
    }

    $original = get_post( $post_id );
    if ( ! $original ) {
        wp_die( 'ページが見つかりません。' );
    }

    // 複製ポストを作成（下書きとして保存）
    $new_post_id = wp_insert_post( array(
        'post_title'   => $original->post_title . ' のコピー',
        'post_content' => $original->post_content,
        'post_excerpt' => $original->post_excerpt,
        'post_status'  => 'draft',
        'post_type'    => $original->post_type,
        'post_author'  => get_current_user_id(),
        'post_parent'  => $original->post_parent,
        'menu_order'   => $original->menu_order,
        'comment_status' => $original->comment_status,
    ) );

    if ( is_wp_error( $new_post_id ) ) {
        wp_die( '複製に失敗しました。' );
    }

    // カスタムフィールド（メタデータ）をコピー
    $meta_data = get_post_meta( $post_id );
    if ( $meta_data ) {
        foreach ( $meta_data as $meta_key => $meta_values ) {
            // WordPressの内部メタキーはスキップ
            if ( in_array( $meta_key, array( '_edit_lock', '_edit_last' ), true ) ) {
                continue;
            }
            foreach ( $meta_values as $meta_value ) {
                add_post_meta( $new_post_id, $meta_key, maybe_unserialize( $meta_value ) );
            }
        }
    }

    // 複製後は固定ページ一覧にリダイレクト
    wp_redirect( admin_url( 'edit.php?post_type=page&duplicated=1' ) );
    exit;
}
add_action( 'admin_action_riff_duplicate_page', 'riff_do_duplicate_page' );

/**
 * 複製成功メッセージを表示
 */
function riff_duplicate_page_notice() {
    $screen = get_current_screen();
    if ( $screen && $screen->id === 'edit-page' && isset( $_GET['duplicated'] ) ) {
        echo '<div class="notice notice-success is-dismissible"><p>ページを複製しました（下書きとして保存されました）。</p></div>';
    }
}
add_action( 'admin_notices', 'riff_duplicate_page_notice' );

?>
