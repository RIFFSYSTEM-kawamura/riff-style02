<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    
    <!-- FOUC防止用：重要なインラインCSS -->
    <style type="text/css">
    <?php if (is_front_page() && get_theme_mod('loading_screen_enable', true)): ?>
    /* ローディング画面有効時のみ：初期非表示（ローディング画面が覆う） */
    html {
        visibility: hidden;
        opacity: 0;
    }
    html.loading-screen-enabled {
        visibility: visible !important;
        opacity: 1 !important;
    }
    <?php endif; ?>
    
    /* 基本レイアウト - レンダリングブロッキング防止 */
    body {
        margin: 0;
        padding: 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans JP", sans-serif;
        line-height: 1.6;
        color: #333;
        background: #ffffff;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    /* ヘッダーの基本スタイル */
    .header {
        position: relative;
        z-index: 1000;
        background: #ffffff;
    }
    
    /* コンテナの基本スタイル */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    /* ローディング画面の基本スタイル */
    /* モバイルのアドレスバー等でビューポート高さが変動しても隙間ができないよう、
       width/height指定ではなくtop/right/bottom/leftで画面全体に固定する */
    #loading-screen-container {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 99999;
        background: #000000;
        display: none;
    }
    
    /* front-pageでローディング画面を有効化 */
    body.loading-screen-enabled #loading-screen-container {
        display: block;
    }
    
    /* ローディング中のbody */
    body.loading-active {
        overflow: hidden;
        height: 100vh;
    }
    </style>
    
    <!-- 重要フォントのプリロード -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Roboto:wght@300;400;500;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Roboto:wght@300;400;500;700&display=swap"></noscript>
    
    <!-- ローディング画面CSSはwp_enqueue_styleで通常読み込み（FOUC防止のため意図的にブロッキング読み込みとする。
         以前のpreload+swap方式は同ファイルの重複タグとなり効果がなかったため削除） -->

    <!-- 初期化スクリプト（ローディング画面用） -->
    <?php if (is_front_page() && get_theme_mod('loading_screen_enable', true)): ?>
    <script>
    (function() {
        // html を即座に可視化（コンテンツはローディング画面コンテナが覆う）
        document.documentElement.classList.add('loading-screen-enabled');
    })();
    </script>
    <?php endif; ?>
    
    <?php
    // title/description/OGP/Twitterカード/canonical/robotsは All in One SEO プラグインが出力するため、
    // テーマ側では重複出力しない（description等の二重出力によるSEO上の不整合を回避）。
    $riff_site_title = get_option('riff_site_title', get_bloginfo('name'));
    $riff_site_desc  = get_option('riff_site_description', get_bloginfo('description'));
    ?>

    <!-- 構造化データ（JSON-LD）Organization -->
    <!-- WebSite/WebPage/BreadcrumbListは All in One SEO が出力するため、ここではAIOSEOがカバーしない
         Organization（サービス一覧付き）のみをテーマ側の責務として出力する -->
    <?php
    $riff_home    = home_url();
    $riff_phone   = get_option('riff_header_phone');
    $riff_email   = get_option('riff_header_email');
    $riff_logo    = get_option('riff_logo_url');
    $riff_address = get_option('riff_footer_address');

    $riff_org = array(
        '@context'        => 'https://schema.org',
        '@type'           => array('Organization', 'LocalBusiness'),
        '@id'             => $riff_home . '/#organization',
        'name'            => $riff_site_title,
        'url'             => $riff_home,
        'description'     => $riff_site_desc,
        'sameAs'          => array(
            'https://www.instagram.com/riffsystem?igsh=d3Jta2xrZnk0eTZz',
            'https://lin.ee/TgeZ8qXl',
        ),
        'address'         => array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => '前野町2-19-14-206',
            'addressLocality' => '板橋区',
            'addressRegion'   => '東京都',
            'postalCode'      => '174-0063',
            'addressCountry'  => 'JP',
        ),
        'geo'             => array(
            '@type'     => 'GeoCoordinates',
            'latitude'  => '35.7648345',
            'longitude' => '139.6883257',
        ),
        // 拠点（板橋区前野町）から訪問対応しやすい近隣区を明示し、地域検索での関連性を高める
        'areaServed'      => array(
            array('@type' => 'City', 'name' => '板橋区'),
            array('@type' => 'City', 'name' => '練馬区'),
            array('@type' => 'City', 'name' => '北区'),
            array('@type' => 'City', 'name' => '豊島区'),
            array('@type' => 'AdministrativeArea', 'name' => '東京都'),
            array('@type' => 'Country', 'name' => '日本'),
        ),
        'knowsAbout'      => array(
            'ホームページ制作',
            'ホームページ作成',
            'LP制作',
            'ランディングページ制作',
            'コーポレートサイト制作',
            'WEB制作',
            'Webサイト制作',
            'WordPressサイト制作',
            '中小企業のホームページ制作',
            '建築業・工務店のホームページ制作',
            'システム開発',
            '業務システム開発',
            '業務効率化',
            'kintone導入・カスタマイズ',
            '中小企業のDX支援',
        ),
        'hasOfferCatalog' => array(
            '@type'           => 'OfferCatalog',
            'name'            => 'サービス一覧',
            'itemListElement' => array(
                array('@type' => 'Offer', 'itemOffered' => array('@type' => 'Service', 'name' => 'ホームページ制作',           'url' => $riff_home . '/web_service/',    'description' => '東京都板橋区を拠点に、中小企業・個人事業主向けのホームページ制作。目的に合わせたデザインと機能で、ビジネスの成長をサポートします。')),
                array('@type' => 'Offer', 'itemOffered' => array('@type' => 'Service', 'name' => 'LP制作（ランディングページ制作）', 'url' => $riff_home . '/web_service/',    'description' => '板橋区・東京の中小企業向けLP制作。コンバージョン率を高める構成で、集客から問い合わせまでを最適化します。')),
                array('@type' => 'Offer', 'itemOffered' => array('@type' => 'Service', 'name' => 'コーポレートサイト制作',     'url' => $riff_home . '/web_service/',    'description' => '企業ブランドの信頼性を高めるコーポレートサイトの制作。採用・広報など様々な目的に対応します。')),
                array('@type' => 'Offer', 'itemOffered' => array('@type' => 'Service', 'name' => '建築業・工務店のホームページ制作', 'url' => $riff_home . '/web_service/', 'description' => '工務店・建設業・一人親方向けのホームページ制作。施工実績の見せ方や元請け・施主からの問い合わせ獲得に強い構成で制作します。')),
                array('@type' => 'Offer', 'itemOffered' => array('@type' => 'Service', 'name' => '業務効率化システム開発',     'url' => $riff_home . '/efficiency/',     'description' => '中小企業向けの業務システム開発。見積書・請求書・報告書作成などExcel業務の自動化で、コスト削減と生産性向上を実現します。')),
                array('@type' => 'Offer', 'itemOffered' => array('@type' => 'Service', 'name' => 'kintone導入・カスタマイズ開発', 'url' => $riff_home . '/system_service/', 'description' => 'kintoneの導入支援・アプリ開発・データ移行・操作研修まで対応。東京都板橋区から全国オンラインでサポートします。')),
            ),
        ),
    );
    if ($riff_logo)    $riff_org['logo']      = array('@type' => 'ImageObject', 'url' => $riff_logo);
    if ($riff_phone)   $riff_org['telephone'] = $riff_phone;
    if ($riff_email)   $riff_org['email']     = $riff_email;

    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode($riff_org, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo "\n</script>\n";
    ?>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico" type="image/x-icon">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php 
$header_style = get_option('riff_header_style', 'transparent');
$header_classes = 'header header-' . $header_style;
?>

<header class="<?php echo esc_attr($header_classes); ?>" id="header">
    <!-- ヘッダー上部の連絡先情報 -->
    <?php 
    $header_phone = get_option('riff_header_phone');
    $header_email = get_option('riff_header_email');
    if (($header_phone || $header_email) && $header_style !== 'minimal'): 
    ?>
    <div class="header-top">
        <div class="container">
            <div class="header-contact">
                <?php if ($header_phone): ?>
                <a href="tel:<?php echo esc_attr(str_replace(array('-', ' ', '(', ')'), '', $header_phone)); ?>" aria-label="電話で連絡する" class="header-contact-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                    </svg>
                    <span><?php echo esc_html($header_phone); ?></span>
                </a>
                <?php endif; ?>
                
                <?php if ($header_email): ?>
                <a href="mailto:<?php echo esc_attr($header_email); ?>" aria-label="メールで連絡する" class="header-contact-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                    <span><?php echo esc_html($header_email); ?></span>
                </a>
                <?php endif; ?>
            </div>
            
            <div class="header-social">
                <!-- ソーシャルメディアリンクは将来の拡張用 -->
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- メインヘッダー -->
    <div class="header-main">
        <div class="container">
            <div class="header-content">
                <!-- ロゴ -->
                <div class="logo">
                    <a href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_option('riff_site_title', get_bloginfo('name'))); ?>のホームページへ" class="logo-link">
                        <?php 
                        $logo_url = get_option('riff_logo_url');
                        $logo_width = get_option('riff_logo_width', 200);
                        $logo_height = get_option('riff_logo_height', 50);
                        if ($logo_url): 
                        ?>
                            <img src="<?php echo esc_url($logo_url); ?>" 
                                 alt="<?php echo esc_attr(get_option('riff_site_title', get_bloginfo('name'))); ?>" 
                                 class="logo-image"
                                 width="<?php echo esc_attr($logo_width); ?>" 
                                 height="<?php echo esc_attr($logo_height); ?>">
                        <?php else: ?>
                            <span class="logo-text"><?php echo esc_html(get_option('riff_site_title', get_bloginfo('name'))); ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                
                <!-- ナビゲーションメニュー -->
                <nav class="navigation" role="navigation" aria-label="メインナビゲーション">
                    <div class="nav-wrapper">
                        <?php
                        // デスクトップ用ナビゲーションメニューを生成
                        if (has_nav_menu('primary')) {
                            wp_nav_menu(array(
                                'theme_location' => 'primary',
                                'menu_class' => 'nav nav-primary',
                                'container' => false,
                                'fallback_cb' => 'riff_fallback_menu',
                                'walker' => class_exists('Riff_Nav_Walker') ? new Riff_Nav_Walker() : ''
                            ));
                        } else {
                            // フォールバック関数が存在する場合のみ呼び出し
                            if (function_exists('riff_fallback_menu')) {
                                riff_fallback_menu();
                            }
                        }
                        ?>
                    </div>
                </nav>
                
                <!-- ヘッダー右側のアクション -->
                <div class="header-actions">
                    <!-- CTAボタン -->
                    <?php 
                    $cta_text = get_option('riff_header_cta_text', 'お問い合わせ');
                    $cta_url = get_option('riff_header_cta_url', '#contact');
                    if ($cta_text): 
                    ?>
                    <a href="<?php echo esc_url($cta_url); ?>" class="header-cta btn-primary" aria-label="<?php echo esc_attr($cta_text); ?>">
                        <span><?php echo esc_html($cta_text); ?></span>
                    </a>
                    <?php endif; ?>
                    
                    <!-- モバイルメニューボタン -->
                    <button class="mobile-menu-toggle" 
                            aria-label="メニューを開く" 
                            aria-expanded="false" 
                            aria-controls="mobile-nav"
                            type="button">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-text sr-only">メニュー</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- モバイルナビゲーション -->
    <div class="mobile-nav" id="mobile-nav" aria-hidden="true">
        <div class="mobile-nav-overlay"></div>
        <div class="mobile-nav-content">
            <div class="mobile-nav-header">
                <button class="mobile-nav-close" aria-label="メニューを閉じる">
                    <span class="close-icon">&times;</span>
                </button>
            </div>
            
            <div class="mobile-nav-menu">
                <?php
                // モバイル用ナビゲーションメニューを生成
                if (has_nav_menu('primary')) {
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class' => 'nav nav-mobile',
                        'container' => false,
                        'fallback_cb' => 'riff_fallback_menu_mobile',
                        'walker' => class_exists('Riff_Mobile_Nav_Walker') ? new Riff_Mobile_Nav_Walker() : '',
                        'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>'
                    ));
                } else {
                    // フォールバックメニュー
                    echo '<ul class="nav nav-mobile">';
                    echo '<li class="mobile-menu-item-wrapper">';
                    echo '<a href="' . esc_url(home_url('/')) . '" class="mobile-menu-link">HOME</a>';
                    echo '<button class="mobile-submenu-toggle" aria-label="サブメニューを開く"><span class="submenu-icon"></span></button>';
                    echo '</li>';
                    echo '<li class="mobile-menu-item-wrapper">';
                    echo '<a href="#" class="mobile-menu-link">ABOUT</a>';
                    echo '<button class="mobile-submenu-toggle" aria-label="サブメニューを開く"><span class="submenu-icon"></span></button>';
                    echo '</li>';
                    echo '<li class="mobile-menu-item-wrapper">';
                    echo '<a href="#" class="mobile-menu-link">NEWS</a>';
                    echo '</li>';
                    echo '<li class="mobile-menu-item-wrapper">';
                    echo '<a href="#" class="mobile-menu-link">SERVICE</a>';
                    echo '<button class="mobile-submenu-toggle" aria-label="サブメニューを開く"><span class="submenu-icon"></span></button>';
                    echo '</li>';
                    echo '<li class="mobile-menu-item-wrapper">';
                    echo '<a href="#" class="mobile-menu-link">COMPANY</a>';
                    echo '</li>';
                    echo '<li class="mobile-menu-item-wrapper">';
                    echo '<a href="#" class="mobile-menu-link">BLOG</a>';
                    echo '</li>';
                    echo '<li class="mobile-menu-item-wrapper">';
                    echo '<a href="#" class="mobile-menu-link">DOWNLOAD</a>';
                    echo '</li>';
                    echo '</ul>';
                }
                ?>
            </div>
            
            <!-- モバイル用連絡先 -->
            <?php if ($header_phone || $header_email): ?>
            <div class="mobile-contact">
                <h3 class="mobile-contact-title">Contact</h3>
                <?php if ($header_phone): ?>
                <a href="tel:<?php echo esc_attr(str_replace(array('-', ' ', '(', ')'), '', $header_phone)); ?>" class="mobile-contact-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                    </svg>
                    <span><?php echo esc_html($header_phone); ?></span>
                </a>
                <?php endif; ?>
                
                <?php if ($header_email): ?>
                <a href="mailto:<?php echo esc_attr($header_email); ?>" class="mobile-contact-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                    <span><?php echo esc_html($header_email); ?></span>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- モバイル用CTA -->
            <?php if ($cta_text): ?>
            <div class="mobile-cta">
                <a href="<?php echo esc_url($cta_url); ?>" class="btn-primary btn-block">
                    <?php echo esc_html($cta_text); ?>
                </a>
            </div>
            <?php endif; ?>
            
            <!-- モバイル用ソーシャルメディア（フッターと共通のSNS定義を参照し、設定済みのものだけ表示。
                 フッター設定でSNSを追加すると自動的にこちらにも反映される） -->
			<?php
			$mobile_social_platforms = riff_get_social_platforms();
			$has_mobile_social = false;
			foreach ($mobile_social_platforms as $info) {
				if (!empty(get_option($info['option'], ''))) { $has_mobile_social = true; break; }
			}
			if ($has_mobile_social):
			?>
			<div class="mobile-social">
				<div class="mobile-social-links">
					<?php foreach ($mobile_social_platforms as $platform => $info): ?>
						<?php $url = get_option($info['option'], ''); ?>
						<?php if (!empty($url)): ?>
						<a href="<?php echo esc_url($url); ?>" class="mobile-social-link" aria-label="<?php echo esc_attr($info['label']); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo riff_get_social_icon($platform); ?>
						</a>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>
        </div>
    </div>
</header>