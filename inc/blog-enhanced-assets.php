<?php
/**
 * BLOG セクション拡張用アセット読み込み
 * Enhanced Blog Section Assets Loader
 * 
 * @package riff-style02
 * @since 1.0.0
 */

// 直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * BLOG セクション拡張用CSS・JSの読み込み
 */
function riff_enqueue_blog_enhanced_assets() {
    // フロントページでのみ読み込み
    if (!is_front_page()) {
        return;
    }

    // テーマディレクトリのURIを取得
    $theme_uri = get_template_directory_uri();
    $theme_version = wp_get_theme()->get('Version') ?: '1.0.0';

    // BLOG セクション拡張用CSSはriff-frontpage-bundle.cssに統合済み（riff_enqueue_frontpage_css_bundle参照）

    // BLOG セクション拡張用JavaScript
    wp_enqueue_script(
        'riff-blog-filter-enhanced',
        $theme_uri . '/assets/js/blog-filter-enhanced.js',
        array(), // jQueryに依存せず、ヴァニラJSで実装
        $theme_version,
        true // フッターで読み込み
    );

    // JavaScriptに設定を渡す
    wp_localize_script(
        'riff-blog-filter-enhanced',
        'riffBlogConfig',
        array(
            'ajaxUrl' => admin_url('ajax.php'),
            'nonce' => wp_create_nonce('riff_blog_filter_nonce'),
            'strings' => array(
                'loading' => __('読み込み中...', 'riff-theme'),
                'error' => __('エラーが発生しました。', 'riff-theme'),
                'noResults' => __('該当する記事が見つかりませんでした。', 'riff-theme'),
                'allCategories' => __('すべて', 'riff-theme'),
            ),
            'settings' => array(
                'animationDuration' => 600,
                'enableAnalytics' => false, // Google Analytics追跡を有効にする場合はtrue
                'enableKeyboardShortcuts' => true,
                'enableLazyLoading' => true,
            )
        )
    );
}

// wp_enqueue_scripts フックに登録
add_action('wp_enqueue_scripts', 'riff_enqueue_blog_enhanced_assets', 20);

/**
 * BLOG セクション用のプリロードヒント（旧実装）
 * バージョンクエリなしのURLをpreloadしていたため、wp_enqueue_style/scriptが出力する
 * バージョン付きURLと別リソース扱いとなり、同じCSS/JSを二重に取得してしまっていた。
 * wp_enqueue_style/scriptは通常のブロッキング読み込みのままのため、このpreloadは
 * パフォーマンス向上に寄与しておらず、無駄な二重リクエストの原因だったため削除。
 */
// add_action('wp_head', 'riff_add_blog_enhanced_preload_hints', 5);

/**
 * BLOG セクション拡張用のbody_classの追加
 * 条件付きスタイリングのため
 */
function riff_add_blog_enhanced_body_class($classes) {
    if (is_front_page()) {
        $classes[] = 'has-blog-enhanced';
        
        // JavaScript有効/無効の判定クラス
        $classes[] = 'js-enabled'; // JavaScriptで 'no-js' を削除
    }
    
    return $classes;
}

// body_class フックに登録
add_filter('body_class', 'riff_add_blog_enhanced_body_class');

/**
 * no-jsクラスをjsクラスに変更するスクリプト
 * アクセシビリティとプログレッシブエンハンスメントのため
 */
function riff_add_js_detection_script() {
    if (!is_front_page()) {
        return;
    }
    ?>
    <script>
    (function() {
        document.documentElement.className = document.documentElement.className.replace(/\bno-js\b/, 'js');
    })();
    </script>
    <?php
}

// wp_head フックに登録（早期実行）
add_action('wp_head', 'riff_add_js_detection_script', 1);

/**
 * CSS変数のカスタマイズサポート
 * WordPress カスタマイザーとの統合
 */
function riff_blog_enhanced_custom_css() {
    if (!is_front_page()) {
        return;
    }

    // カスタマイザーからの設定値を取得
    $primary_color = get_theme_mod('riff_primary_color', '#007cba');
    $accent_color = get_theme_mod('riff_accent_color', '#ff6b35');
    $animation_duration = get_theme_mod('riff_blog_animation_duration', 600);
    $enable_animations = get_theme_mod('riff_blog_enable_animations', true);

    // カスタムCSS変数を出力
    ?>
    <style id="riff-blog-enhanced-custom-vars">
    :root {
        --riff-blog-primary-color: <?php echo esc_html($primary_color); ?>;
        --riff-blog-accent-color: <?php echo esc_html($accent_color); ?>;
        --riff-blog-animation-duration: <?php echo intval($animation_duration); ?>ms;
    }
    
    <?php if (!$enable_animations): ?>
    .riff-blog-section-enhanced *,
    .riff-blog-section-enhanced *::before,
    .riff-blog-section-enhanced *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
    <?php endif; ?>
    </style>
    <?php
}

// wp_head フックに登録
add_action('wp_head', 'riff_blog_enhanced_custom_css', 100);

/**
 * 管理バー表示時のスタイル調整
 */
function riff_blog_enhanced_admin_bar_css() {
    if (!is_front_page() || !is_admin_bar_showing()) {
        return;
    }
    ?>
    <style id="riff-blog-enhanced-admin-bar">
    @media screen and (min-width: 783px) {
        .has-blog-enhanced.admin-bar .riff-blog-section-enhanced {
            padding-top: calc(120px + 32px);
        }
    }
    
    @media screen and (max-width: 782px) {
        .has-blog-enhanced.admin-bar .riff-blog-section-enhanced {
            padding-top: calc(80px + 46px);
        }
    }
    </style>
    <?php
}

// wp_head フックに登録
add_action('wp_head', 'riff_blog_enhanced_admin_bar_css', 101);

/**
 * パフォーマンス最適化: Critical CSS の埋め込み
 * 最初に表示される部分のスタイルを埋め込み
 */
function riff_blog_enhanced_critical_css() {
    if (!is_front_page()) {
        return;
    }
    ?>
    <style id="riff-blog-enhanced-critical">
    /* Above-the-fold styles for BLOG section */
    .riff-blog-section-enhanced {
        padding: 120px 0;
        background: #ffffff;
        width: 100%;
        position: relative;
        z-index: 1;
    }
    
    .riff-blog-container-enhanced {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 60px;
        width: 100%;
        position: relative;
        z-index: 1;
    }
    
    .riff-blog-header-enhanced {
        text-align: center;
        margin-bottom: 80px;
    }
    
    .riff-blog-title-enhanced {
        font-size: 4rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        font-family: "Roboto", sans-serif;
        letter-spacing: -2px;
        line-height: 1.1;
    }
    
    @media (max-width: 768px) {
        .riff-blog-section-enhanced {
            padding: 80px 0;
        }
        
        .riff-blog-container-enhanced {
            padding: 0 20px;
        }
        
        .riff-blog-title-enhanced {
            font-size: 2.5rem;
        }
    }
    </style>
    <?php
}

// wp_head フックに登録（Critical CSS は早期に）
add_action('wp_head', 'riff_blog_enhanced_critical_css', 10);

/**
 * セキュリティ: nonce の検証関数
 * AJAX リクエスト時のセキュリティチェック用
 */
function riff_verify_blog_filter_nonce($nonce) {
    return wp_verify_nonce($nonce, 'riff_blog_filter_nonce');
}

/**
 * デバッグ用: 開発環境でのみアセット情報を出力
 */
function riff_blog_enhanced_debug_info() {
    if (!WP_DEBUG || !is_front_page()) {
        return;
    }
    
    $theme_uri = get_template_directory_uri();
    
    echo "<!-- RIFF Blog Enhanced Debug Info -->\n";
    echo "<!-- CSS: {$theme_uri}/assets/css/blog-section-enhanced.css -->\n";
    echo "<!-- JS: {$theme_uri}/assets/js/blog-filter-enhanced.js -->\n";
    echo "<!-- Theme Version: " . wp_get_theme()->get('Version') . " -->\n";
    echo "<!-- End Debug Info -->\n";
}

// wp_head フックに登録（デバッグ時のみ）
add_action('wp_head', 'riff_blog_enhanced_debug_info', 999);

/**
 * アンインストール時のクリーンアップ
 * オプション: 将来的な機能
 */
function riff_blog_enhanced_cleanup() {
    // カスタマイザー設定のクリーンアップ
    remove_theme_mod('riff_blog_animation_duration');
    remove_theme_mod('riff_blog_enable_animations');
    
    // 一時的なオプションのクリーンアップ
    delete_option('riff_blog_enhanced_version');
    delete_transient('riff_blog_enhanced_cache');
}

// テーマ切り替え時のクリーンアップ
add_action('switch_theme', 'riff_blog_enhanced_cleanup');

/**
 * 初期化フラグの設定
 * アセット読み込みの成功を追跡
 */
function riff_blog_enhanced_init_flag() {
    if (is_front_page()) {
        update_option('riff_blog_enhanced_initialized', current_time('mysql'));
    }
}

// init フックに登録
add_action('init', 'riff_blog_enhanced_init_flag');