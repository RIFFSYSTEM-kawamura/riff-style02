<?php get_header(); ?>

<?php
// セクション設定を取得
$enabled_sections = riff_get_enabled_sections_ordered();
?>

<!-- ヒーローセクション（常に表示） -->
<?php riff_display_hero_section(); ?>

<!-- メインコンテンツ -->
<main class="main-content" id="content" style="position: relative; z-index: 2; background: #ffffff;">
    
    <?php
    // ヒーロー以外のセクションを動的に表示
    foreach ($enabled_sections as $section_id => $section_data) {
        // ヒーローセクションは既に表示済みのためスキップ
        if ($section_id === 'hero') {
            continue;
        }
        
        switch ($section_id) {
            case 'who':
                riff_display_who_section();
                break;
            case 'service':
                riff_display_service_section();
                break;
            case 'news':
                riff_display_news_section();
                break;
            case 'company':
                riff_display_company_section();
                break;
            case 'blog':
                riff_display_blog_section();
                break;
			case 'faq':
                riff_display_faq_section();
                break;
			case 'instagram':
                riff_display_instagram_section();
                break;
			case 'footer_up':
                riff_display_footer_up_section();
                break;
        }
    }
    ?>
    
</main>

<?php
// ================================
// セクション表示関数群
// ================================

// ヒーローセクション
function riff_display_hero_section() {
    $hero_title = get_option('riff_hero_title', 'MAKE THE WORLD');
    $hero_subtitle = get_option('riff_hero_subtitle', '人と自然が共生できる新しい世界をつくる');
    $hero_description = get_option('riff_hero_description', '私たちは持続可能な未来のために、革新的なソリューションを提供します。環境と調和した技術で、次世代に美しい地球を残していきます。');
    $hero_button_text = get_option('riff_hero_button_text', '詳しく見る');
    $hero_button_url = get_option('riff_hero_button_url', '#about');
    $hero_bg_image = get_option('riff_hero_bg_image', '');
    $hero_bg_video = get_option('riff_hero_bg_video', '');
    $hero_display_type = get_option('riff_hero_display_type', 'image');
    $hero_overlay_opacity = get_option('riff_hero_overlay_opacity', 50);
    ?>
    
    <section class="hero hero-<?php echo esc_attr($hero_display_type); ?>" id="hero" style="display: block !important; position: relative; z-index: 1; height: 100vh; height: 100dvh; min-height: 600px;">
        <!-- 背景要素 -->
        <div class="hero-background">
            <?php if ($hero_display_type === 'video' && $hero_bg_video):
                $hero_bg_poster = get_option('riff_hero_bg_poster', $hero_bg_image);
                // スマホは画面が小さく通信量も気になるため、軽量版の動画があればそちらを使う（main.js で切り替え）
                $hero_bg_video_mobile = get_option('riff_hero_bg_video_mobile', '');
            ?>
                <video class="hero-video" muted loop playsinline preload="none"
                       width="1280" height="720"
                       <?php if ($hero_bg_poster): ?>poster="<?php echo esc_url($hero_bg_poster); ?>"<?php endif; ?>
                       <?php if ($hero_bg_video_mobile): ?>data-src-mobile="<?php echo esc_url($hero_bg_video_mobile); ?>"<?php endif; ?>
                       data-src="<?php echo esc_url($hero_bg_video); ?>">
                    <?php if ($hero_bg_image): ?>
                    <img src="<?php echo esc_url($hero_bg_image); ?>" alt="" class="hero-video-fallback" width="1920" height="1080" loading="lazy">
                    <?php endif; ?>
                </video>
            <?php elseif ($hero_display_type === 'image' && $hero_bg_image): ?>
                <img src="<?php echo esc_url($hero_bg_image); ?>" alt="" class="hero-bg-image" width="1920" height="1080" fetchpriority="high" loading="eager">
            <?php elseif ($hero_display_type === 'gradient'): ?>
                <div class="hero-gradient"></div>
            <?php endif; ?>
            
            <!-- オーバーレイ -->
            <div class="hero-overlay" style="opacity: <?php echo esc_attr($hero_overlay_opacity / 100); ?>"></div>
        </div>
        
        <!-- コンテンツ -->
        <div class="hero-content">
            <div class="hero-inner">
                <?php if ($hero_subtitle): ?>
                <p class="hero-subtitle fade-in" data-delay="0.2s">
                    <?php echo riff_output_textarea_with_br($hero_subtitle); ?>
                </p>
                <?php endif; ?>

                <?php if ($hero_title): ?>
                <h1 class="hero-title fade-in" data-delay="0.4s">
                    <?php echo riff_output_textarea_with_br($hero_title); ?>
                </h1>
                <?php endif; ?>

                <?php if ($hero_description): ?>
                <p class="hero-description fade-in" data-delay="0.6s">
                    <?php echo riff_output_textarea_with_br($hero_description); ?>
                </p>
                <?php endif; ?>

                <?php if ($hero_button_text): ?>
                <div class="hero-actions fade-in" data-delay="0.8s">
                    <a href="<?php echo esc_url($hero_button_url); ?>" class="hero-button btn-primary">
                        <span><?php echo esc_html($hero_button_text); ?></span>
                        <svg class="button-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                        </svg>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
    </section>
    <?php
}

// WHO WE AREセクション
function riff_display_who_section() {
    $who_title = get_option('riff_who_title', 'WHO WE ARE');
    $who_subtitle = get_option('riff_who_subtitle', '日本の高水準を誇る');
    $who_description = get_option('riff_who_description', '未来の情報基盤社会を目指す');
    $who_detail_text = get_option('riff_who_detail_text', '私たちは最先端の技術と豊富な経験を活かし、お客様のニーズに応える最適なソリューションを提供します。');
    $who_button_text = get_option('riff_who_button_text', '詳しく見る');
    $who_button_url = get_option('riff_who_button_url', '#about');
    ?>
    
    <!-- 固定ページのコンテンツがある場合は表示 -->
    <?php
    $front_page_id = get_option('page_on_front');
    if ($front_page_id) {
        $front_page = get_post($front_page_id);
        if ($front_page && !empty(trim($front_page->post_content))) {
            ?>
            <section class="section page-content fade-in" style="position: relative; z-index: 1;">
                <div class="container">
                    <div class="page-content-inner" style="max-width: 800px; margin: 0 auto;">
                        <?php
                        // 固定ページのコンテンツを表示
                        echo apply_filters('the_content', $front_page->post_content);
                        ?>
                    </div>
                </div>
            </section>
            <?php
        }
    }
    ?>
    
    <section class="riff-who-section-fullwidth fadeLeftTrigger" id="about">
        <div class="riff-who-container">
            <div class="riff-who-content-wrapper">
                <div class="riff-who-left">
                    <h2 class="riff-who-title-new"><?php echo esc_html($who_title); ?></h2>
                    <p class="riff-who-subtitle-new"><?php echo esc_html($who_subtitle); ?></p>
                    <p class="riff-who-description-new"><?php echo esc_html($who_description); ?></p>
                </div>
                <div class="riff-who-right-new" data-delay="0.2s">
                    <p class="riff-who-detail-new"><?php echo riff_output_textarea_with_br($who_detail_text); ?></p>
                    <?php if ($who_button_text): ?>
                    <div class="riff-who-button-wrap-new">
                        <a href="<?php echo esc_url($who_button_url); ?>" class="riff-who-button-new">
                            <?php echo esc_html($who_button_text); ?>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                            </svg>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php
}

// SERVICEセクション
function riff_display_service_section() {
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
    ?>
    
    <section class="riff-service-section-fullwidth bg-light fadeRightTrigger" id="services">
        <div class="riff-service-container">
            <div class="riff-service-header">
                <h2 class="riff-service-title-new"><?php echo esc_html($service_title); ?></h2>
                <p class="riff-service-subtitle-new"><?php echo esc_html($service_subtitle); ?></p>
            </div>
            
            <div class="riff-service-grid-new">
                <!-- サービスカード1 -->
                <div class="riff-service-card-new fade-in" data-delay="0.1s">
                    <div class="riff-service-wrapper">
                        <a href="<?php echo esc_url($service1_url); ?>" class="riff-service-link">
                            <div class="riff-service-image-container">
                                <?php if ($service1_image): ?>
                                    <?php echo riff_option_image($service1_image, array('size' => 'service-thumb', 'class' => 'riff-service-image', 'alt' => $service1_title)); ?>
                                <?php else: ?>
                                    <div class="riff-service-no-image" style="background-color: <?php echo esc_attr($service1_color); ?>;">
                                        <span class="riff-service-placeholder">画像を追加</span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- グラデーションオーバーレイ -->
                                <div class="riff-service-gradient-overlay" 
                                     data-color="<?php echo esc_attr($service1_color); ?>" 
                                     data-opacity="<?php echo esc_attr($service1_opacity); ?>"></div>
                                
                                <!-- テキストコンテンツ（常に表示） -->
                                <div class="riff-service-content">
                                    <h3 class="riff-service-card-title"><?php echo esc_html($service1_title); ?></h3>
                                    <div class="riff-service-arrow">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- サービスカード2 -->
                <div class="riff-service-card-new fade-in" data-delay="0.2s">
                    <div class="riff-service-wrapper">
                        <a href="<?php echo esc_url($service2_url); ?>" class="riff-service-link">
                            <div class="riff-service-image-container">
                                <?php if ($service2_image): ?>
                                    <?php echo riff_option_image($service2_image, array('size' => 'service-thumb', 'class' => 'riff-service-image', 'alt' => $service2_title)); ?>
                                <?php else: ?>
                                    <div class="riff-service-no-image" style="background-color: <?php echo esc_attr($service2_color); ?>;">
                                        <span class="riff-service-placeholder">画像を追加</span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- グラデーションオーバーレイ -->
                                <div class="riff-service-gradient-overlay" 
                                     data-color="<?php echo esc_attr($service2_color); ?>" 
                                     data-opacity="<?php echo esc_attr($service2_opacity); ?>"></div>
                                
                                <!-- テキストコンテンツ（常に表示） -->
                                <div class="riff-service-content">
                                    <h3 class="riff-service-card-title"><?php echo esc_html($service2_title); ?></h3>
                                    <div class="riff-service-arrow">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- サービスカード3 -->
                <div class="riff-service-card-new fade-in" data-delay="0.3s">
                    <div class="riff-service-wrapper">
                        <a href="<?php echo esc_url($service3_url); ?>" class="riff-service-link">
                            <div class="riff-service-image-container">
                                <?php if ($service3_image): ?>
                                    <?php echo riff_option_image($service3_image, array('size' => 'service-thumb', 'class' => 'riff-service-image', 'alt' => $service3_title)); ?>
                                <?php else: ?>
                                    <div class="riff-service-no-image" style="background-color: <?php echo esc_attr($service3_color); ?>;">
                                        <span class="riff-service-placeholder">画像を追加</span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- グラデーションオーバーレイ -->
                                <div class="riff-service-gradient-overlay" 
                                     data-color="<?php echo esc_attr($service3_color); ?>" 
                                     data-opacity="<?php echo esc_attr($service3_opacity); ?>"></div>
                                
                                <!-- テキストコンテンツ（常に表示） -->
                                <div class="riff-service-content">
                                    <h3 class="riff-service-card-title"><?php echo esc_html($service3_title); ?></h3>
                                    <div class="riff-service-arrow">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
}

// NEWSセクション（シンプル版）
function riff_display_news_section() {
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
    
    $recent_posts = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => 5,
        'post_status' => 'publish'
    ));
    ?>
    
    <section class="riff-news-section-simple fadeRightTrigger" id="news">
        <div class="riff-news-container-simple">
            <div class="riff-news-header-simple">
                <h2 class="riff-news-title-simple"><?php echo esc_html($news_title); ?></h2>
                <?php if ($news_subtitle): ?>
                <p class="riff-news-subtitle-simple"><?php echo esc_html($news_subtitle); ?></p>
                <?php endif; ?>
            </div>
            
            <?php if ($recent_posts->have_posts()): ?>
            <div class="riff-news-list-simple">
                <?php $count = 0; while ($recent_posts->have_posts() && $count < 5): $recent_posts->the_post(); $count++; ?>
                <article class="riff-news-item-simple fade-in" data-delay="<?php echo 0.1 + ($count * 0.1); ?>s">
                    <a href="<?php the_permalink(); ?>" class="riff-news-link-simple">
                        <div class="riff-news-content-simple">
                            <div class="riff-news-meta-simple">
                                <time class="riff-news-date-simple" datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date('Y.m.d'); ?>
                                </time>
                                <?php 
                                $categories = get_the_category();
                                if (!empty($categories)) {
                                    $primary_category = $categories[0];
                                    echo '<span class="riff-news-category-simple">' . esc_html($primary_category->name) . '</span>';
                                }
                                ?>
                            </div>
                            <h3 class="riff-news-title-item-simple"><?php the_title(); ?></h3>
                        </div>
                        <div class="riff-news-arrow-simple">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                            </svg>
                        </div>
                    </a>
                </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            
            <?php if ($news_button_text): ?>
            <div class="riff-news-button-wrap-simple fade-in" data-delay="0.8s">
                <a href="<?php echo esc_url($news_button_url); ?>" class="riff-news-button-simple">
                    <?php echo esc_html($news_button_text); ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                    </svg>
                </a>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

// COMPANYセクション
function riff_display_company_section() {
    $company_title = get_option('riff_company_title', 'COMPANY');
    $company_subtitle = get_option('riff_company_subtitle', '私たちについて');
    $company_description = get_option('riff_company_description', '環境に配慮した持続可能な経営を実践し、社会に貢献する企業を目指しています。');
    $company_button_text = get_option('riff_company_button_text', '詳しく見る');
    $company_button_url = get_option('riff_company_button_url', '/company/');
    $company_image1 = get_option('riff_company_image1', '');
    $company_image2 = get_option('riff_company_image2', '');
    ?>
    
    <section class="riff-company-section-fullwidth bg-light fadeLeftTrigger" id="company">
        <div class="riff-company-container">
            <div class="riff-company-header">
                <h2 class="riff-company-title-new"><?php echo esc_html($company_title); ?></h2>
                <p class="riff-company-subtitle-new"><?php echo esc_html($company_subtitle); ?></p>
            </div>
            
            <div class="riff-company-content-new">
                <div class="riff-company-images-new fade-in" data-delay="0.2s">
                    <div class="riff-company-image-left-new">
                        <?php if ($company_image1): ?>
                            <?php echo riff_option_image($company_image1, array('size' => 'large', 'class' => 'img-responsive', 'alt' => $company_subtitle . 'の写真1')); ?>
                        <?php endif; ?>
                    </div>
                    <div class="riff-company-image-right-new">
                        <?php if ($company_image2): ?>
                            <?php echo riff_option_image($company_image2, array('size' => 'large', 'class' => 'img-responsive', 'alt' => $company_subtitle . 'の写真2')); ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="riff-company-text-new fade-in" data-delay="0.4s">
                    <p class="riff-company-description-new"><?php echo riff_output_textarea_with_br($company_description); ?></p>
                    <?php if ($company_button_text): ?>
                    <div class="riff-company-button-wrap-new">
                        <a href="<?php echo esc_url($company_button_url); ?>" class="riff-company-button-new">
                            <?php echo esc_html($company_button_text); ?>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                            </svg>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php
}

// 改良版BLOGセクション表示関数（front-page.phpの該当部分を置き換え）
function riff_display_blog_section() {
    // スライダー機能が有効かチェック
    if (function_exists('riff_is_blog_slider_enabled') && riff_is_blog_slider_enabled()) {
        // スライダー版を使用
        if (function_exists('riff_display_blog_section_slider')) {
            riff_display_blog_section_slider();
            return;
        }
    }
    
    // 元のグリッド版を実行（既存のコード）
    $blog_title = get_option('riff_blog_title', 'BLOG');
    $blog_subtitle = get_option('riff_blog_subtitle', '最新記事をお届け');
    $blog_button_text = get_option('riff_blog_button_text', 'ブログ一覧');
    
    // 投稿アーカイブページのURLを取得
    $posts_page_id = get_option('page_for_posts');
    if ($posts_page_id) {
        $default_archive_url = get_permalink($posts_page_id) . '?post_type=blog';
    } else {
        $default_archive_url = home_url('/?post_type=blog');
        if (get_post_type_archive_link('post')) {
            $default_archive_url = get_post_type_archive_link('post') . '?post_type=blog';
        }
    }
    
    $blog_button_url = get_option('riff_blog_button_url', $default_archive_url);
    
    // カテゴリを取得
    $categories = get_categories(array(
        'hide_empty' => true,
        'number' => 10
    ));
    
    // BLOG記事を取得（post_type_flagがblogまたはbothの記事）
    $blog_posts = new WP_Query(array(
        'post_type' => 'post',
        'posts_per_page' => 8,
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
    
    <section class="riff-blog-section-enhanced fadeLeftTrigger" id="blog">
        <div class="riff-blog-container-enhanced">
            <!-- ヘッダー -->
            <div class="riff-blog-header-enhanced">
                <h2 class="riff-blog-title-enhanced"><?php echo esc_html($blog_title); ?></h2>
                <p class="riff-blog-subtitle-enhanced"><?php echo esc_html($blog_subtitle); ?></p>
            </div>
            
            <?php if (!empty($categories)): ?>
            <!-- カテゴリフィルター -->
            <div class="riff-blog-filters-enhanced" data-delay="0.2s">
                <button class="riff-blog-filter-btn-enhanced active" data-category="all">
                    すべて
                </button>
                <?php foreach ($categories as $category): ?>
                <button class="riff-blog-filter-btn-enhanced" data-category="<?php echo esc_attr($category->slug); ?>">
                    <?php echo esc_html($category->name); ?>
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($blog_posts->have_posts()): ?>
            <!-- ブログ記事グリッド -->
            <div class="riff-blog-grid-enhanced" id="blog-posts-container">
                <?php $count = 0; while ($blog_posts->have_posts() && $count < 8): $blog_posts->the_post(); $count++; ?>
                <?php 
                $categories_list = get_the_category();
                $category_slugs = array();
                foreach ($categories_list as $cat) {
                    $category_slugs[] = $cat->slug;
                }
                $category_classes = implode(' ', $category_slugs);
                ?>
                <article class="riff-blog-item-enhanced fade-in" 
					 data-delay="<?php echo 0.1 + ($count * 0.1); ?>s"
					 data-categories="<?php echo esc_attr($category_classes); ?>">
				<!-- 画像部分（リンクなし） -->
				<?php if (has_post_thumbnail()): ?>
				<div class="riff-blog-image-enhanced">
					<?php the_post_thumbnail('medium_large', array(
						'class' => 'img-responsive',
						'loading' => 'lazy'
					)); ?>
				</div>
				<?php endif; ?>

				<!-- コンテンツ部分 -->
				<div class="riff-blog-content-enhanced">
					<div class="riff-blog-meta-enhanced">
						<time datetime="<?php echo get_the_date('c'); ?>" class="riff-blog-date-enhanced">
							<?php echo get_the_date('Y.m.d'); ?>
						</time>
						<?php if (!empty($categories_list)): ?>
						<span class="riff-blog-category-enhanced">
							<?php echo esc_html($categories_list[0]->name); ?>
						</span>
						<?php endif; ?>
					</div>

					<!-- タイトルのみリンク -->
					<h3 class="riff-blog-item-title-enhanced">
						<a href="<?php the_permalink(); ?>" class="riff-blog-title-link">
							<?php the_title(); ?>
						</a>
					</h3>

					<p class="riff-blog-excerpt-enhanced">
						<?php echo wp_trim_words(get_the_excerpt() ?: get_the_content(), 20, '...'); ?>
					</p>
				</div>
			</article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            
            <!-- 「もっと見る」ボタン -->
            <div class="riff-blog-button-wrap-enhanced fade-in" data-delay="0.8s">
                <a href="<?php echo esc_url($blog_button_url); ?>" class="riff-blog-button-enhanced">
                    <?php echo esc_html($blog_button_text); ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                    </svg>
                </a>
            </div>
            <?php else: ?>
            <!-- BLOG記事がない場合の表示 -->
            <div class="no-blog-posts-enhanced fade-in">
                <div class="no-posts-icon-enhanced">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h8c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                    </svg>
                </div>
                <h3>ブログ記事はまだありません</h3>
                <p>ブログ記事として設定された投稿がまだありません。<br>管理画面で投稿の「投稿タイプ設定」を「BLOG」に設定してください。</p>
                <?php if (current_user_can('edit_posts')): ?>
                <a href="<?php echo admin_url('edit.php'); ?>" class="riff-blog-button-enhanced">
                    投稿管理画面へ
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

// AEO（回答エンジン最適化）用FAQ構造化データ
$riff_faq_schema = array(
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => array(
        array(
            '@type'          => 'Question',
            'name'           => 'ホームページ制作の費用はどのくらいですか？',
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => 'ホームページ制作の費用はサイトの規模や機能によって異なります。シンプルな企業サイトから大規模なコーポレートサイトまで、お客様のご予算とニーズに合わせた最適なプランをご提案します。まずはお気軽にお問い合わせください。',
            ),
        ),
        array(
            '@type'          => 'Question',
            'name'           => 'LP（ランディングページ）制作も対応していますか？',
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => 'はい、LP制作（ランディングページ制作）にも対応しています。広告やSNSからの流入に最適化された、コンバージョン率の高いランディングページを制作します。商品・サービスの特性に合わせたデザインと構成でお客様の成果を最大化します。',
            ),
        ),
        array(
            '@type'          => 'Question',
            'name'           => 'コーポレートサイトの制作はできますか？',
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => 'コーポレートサイト制作も承っています。企業ブランドの信頼性を高めるデザインで、採用・IR・広報など様々な目的に対応したコーポレートサイトを制作します。',
            ),
        ),
        array(
            '@type'          => 'Question',
            'name'           => 'WEB制作の納期はどのくらいですか？',
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => 'WEB制作の納期はプロジェクトの規模によって異なりますが、一般的なホームページで約1〜2ヶ月、大規模なコーポレートサイトで約2〜4ヶ月が目安です。お急ぎの場合はご相談ください。',
            ),
        ),
        array(
            '@type'          => 'Question',
            'name'           => '業務効率化の支援も行っていますか？',
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => 'はい、ITツールを活用した業務効率化の支援も行っています。業務プロセスの分析から最適なシステム導入・運用サポートまで、コスト削減と生産性向上をトータルでサポートします。',
            ),
        ),
    ),
);
echo '<script type="application/ld+json">' . "\n";
echo wp_json_encode($riff_faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
echo "\n</script>\n";

get_footer(); ?>