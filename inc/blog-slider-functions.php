<?php
/**
 * BLOG スライダー専用関数
 * Blog Slider Functions
 * 
 * @package riff-style02
 * @since 1.0.0
 */

// 直接アクセスを防ぐ
if (!defined('ABSPATH')) {
    exit;
}

/**
 * BLOGスライダー用アセットの読み込み
 */
function riff_enqueue_blog_slider_assets() {
    // フロントページでのみ読み込み
    if (!is_front_page()) {
        return;
    }

    $theme_uri = get_template_directory_uri();
    $theme_version = wp_get_theme()->get('Version') ?: '1.0.0';

    // BLOGスライダー専用CSSはriff-frontpage-bundle.cssに統合済み（riff_enqueue_frontpage_css_bundle参照）

    // BLOGスライダー専用JavaScript
    wp_enqueue_script(
        'riff-blog-slider',
        $theme_uri . '/assets/js/blog-slider.js',
        array(), // jQueryに依存せず、ヴァニラJSで実装
        $theme_version,
        true // フッターで読み込み
    );

    // JavaScriptに設定を渡す
    wp_localize_script(
        'riff-blog-slider',
        'riffBlogSliderConfig',
        array(
            'slidesToShow' => array(
                'desktop' => get_option('riff_blog_slider_desktop_slides', 3),
                'tablet' => get_option('riff_blog_slider_tablet_slides', 2),
                'mobile' => get_option('riff_blog_slider_mobile_slides', 1)
            ),
            'autoPlay' => get_option('riff_blog_slider_autoplay', false),
            'autoPlayInterval' => get_option('riff_blog_slider_interval', 5000),
            'animationDuration' => get_option('riff_blog_slider_duration', 600),
            'enableSwipe' => get_option('riff_blog_slider_swipe', true),
            'enableKeyboard' => get_option('riff_blog_slider_keyboard', true),
            'pauseOnHover' => get_option('riff_blog_slider_pause_hover', true),
            'strings' => array(
                'prevSlide' => __('前のスライド', 'riff-theme'),
                'nextSlide' => __('次のスライド', 'riff-theme'),
                'goToSlide' => __('スライド %d を表示', 'riff-theme'),
                'loading' => __('読み込み中...', 'riff-theme'),
                'error' => __('スライダーの読み込みに失敗しました', 'riff-theme')
            )
        )
    );
}
add_action('wp_enqueue_scripts', 'riff_enqueue_blog_slider_assets', 25);

/**
 * BLOGスライダーセクションの表示（既存関数を置き換え）
 */
function riff_display_blog_section_slider() {
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
    
    <section class="riff-blog-section-enhanced" id="blog">
        <div class="riff-blog-container-enhanced">
            <!-- ヘッダー -->
            <div class="riff-blog-header-enhanced fade-in">
                <h2 class="riff-blog-title-enhanced"><?php echo esc_html($blog_title); ?></h2>
                <p class="riff-blog-subtitle-enhanced"><?php echo esc_html($blog_subtitle); ?></p>
            </div>
            
            <?php if (!empty($categories)): ?>
            <!-- カテゴリフィルター -->
            <div class="riff-blog-filters-enhanced fade-in" data-delay="0.2s">
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
            <!-- ブログスライダーコンテナ -->
            <div class="riff-blog-slider-container fade-in" data-delay="0.4s">
                <!-- スライダートラック -->
                <div class="riff-blog-slider-track">
                    <?php $count = 0; while ($blog_posts->have_posts() && $count < 8): $blog_posts->the_post(); $count++; ?>
                    <?php 
                    $categories_list = get_the_category();
                    $category_slugs = array();
                    foreach ($categories_list as $cat) {
                        $category_slugs[] = $cat->slug;
                    }
                    $category_classes = implode(' ', $category_slugs);
                    ?>
					<div class="riff-blog-slide" data-categories="<?php echo esc_attr($category_classes); ?>" aria-hidden="false">
						<article class="riff-blog-item-enhanced">
							<!-- 画像部分（リンクなし） -->
							<?php if (has_post_thumbnail()): ?>
							<div class="riff-blog-image-enhanced">
								<?php the_post_thumbnail('medium_large', array(
									'class' => 'img-responsive',
									'loading' => 'lazy',
									'alt' => get_the_title()
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
					</div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
            
            <!-- バー型ナビゲーション（修正版） -->
            <?php
            $total_slides = $blog_posts->found_posts;
            $desktop_slides = get_option('riff_blog_slider_desktop_slides', 3);
            $total_dots = max(1, ceil($total_slides / $desktop_slides));
            
            if ($total_dots > 1): // スライドが複数ある場合のみ表示
            ?>
            <div class="riff-blog-slider-dots fade-in" data-delay="0.6s" role="tablist" aria-label="スライド選択">
                <?php for ($i = 0; $i < $total_dots; $i++): ?>
                <button type="button" 
                        class="riff-blog-slider-dot <?php echo $i === 0 ? 'active' : ''; ?>" 
                        data-slide="<?php echo $i; ?>"
                        role="tab"
                        aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                        aria-controls="blog-slide-<?php echo $i; ?>"
                        aria-label="スライド <?php echo $i + 1; ?> を表示">
                </button>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
            
            <!-- 「もっと見る」ボタン -->
            <div class="riff-blog-button-wrap-enhanced fade-in" data-delay="0.8s">
                <a href="<?php echo esc_url($blog_button_url); ?>" class="riff-blog-button-enhanced">
                    <?php echo esc_html($blog_button_text); ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                    </svg>
                </a>
            </div>
            <?php else: ?>
            <!-- BLOG記事がない場合の表示 -->
            <div class="no-blog-posts-enhanced fade-in">
                <div class="no-posts-icon-enhanced">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
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

/**
 * BLOGスライダー設定の管理画面メニューを追加
 */
function riff_blog_slider_settings_menu() {
    add_submenu_page(
        'riff-theme-settings',
        'BLOGスライダー設定',
        'BLOGスライダー設定',
        'manage_options',
        'riff-blog-slider-settings',
        'riff_blog_slider_settings_page'
    );
}
add_action('admin_menu', 'riff_blog_slider_settings_menu');

/**
 * BLOGスライダー設定ページ
 */
function riff_blog_slider_settings_page() {
    if (isset($_POST['submit'])) {
        // nonceチェック
        if (!isset($_POST['riff_blog_slider_nonce']) || !wp_verify_nonce($_POST['riff_blog_slider_nonce'], 'riff_blog_slider_settings')) {
            wp_die('セキュリティチェックに失敗しました。');
        }
        
        // 設定を保存
        update_option('riff_blog_slider_desktop_slides', intval($_POST['desktop_slides']));
        update_option('riff_blog_slider_tablet_slides', intval($_POST['tablet_slides']));
        update_option('riff_blog_slider_mobile_slides', intval($_POST['mobile_slides']));
        update_option('riff_blog_slider_autoplay', isset($_POST['autoplay']) ? 1 : 0);
        update_option('riff_blog_slider_interval', intval($_POST['autoplay_interval']));
        update_option('riff_blog_slider_duration', intval($_POST['animation_duration']));
        update_option('riff_blog_slider_swipe', isset($_POST['enable_swipe']) ? 1 : 0);
        update_option('riff_blog_slider_keyboard', isset($_POST['enable_keyboard']) ? 1 : 0);
        update_option('riff_blog_slider_pause_hover', isset($_POST['pause_on_hover']) ? 1 : 0);
        
        echo '<div class="notice notice-success"><p>BLOGスライダー設定を保存しました。</p></div>';
    }
    
    // 設定値を取得
    $desktop_slides = get_option('riff_blog_slider_desktop_slides', 3);
    $tablet_slides = get_option('riff_blog_slider_tablet_slides', 2);
    $mobile_slides = get_option('riff_blog_slider_mobile_slides', 1);
    $autoplay = get_option('riff_blog_slider_autoplay', 0);
    $interval = get_option('riff_blog_slider_interval', 5000);
    $duration = get_option('riff_blog_slider_duration', 600);
    $swipe = get_option('riff_blog_slider_swipe', 1);
    $keyboard = get_option('riff_blog_slider_keyboard', 1);
    $pause_hover = get_option('riff_blog_slider_pause_hover', 1);
    ?>
    
    <div class="wrap">
        <h1>BLOGスライダー設定</h1>
        
        <div class="riff-admin-header">
            <p>フロントページのBLOGセクションのスライダー機能を設定できます。</p>
        </div>
        
        <form method="post" action="">
            <?php wp_nonce_field('riff_blog_slider_settings', 'riff_blog_slider_nonce'); ?>
            
            <!-- 表示数設定 -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">📱 レスポンシブ表示設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">デスクトップ表示数</th>
                    <td>
                        <input type="number" name="desktop_slides" value="<?php echo esc_attr($desktop_slides); ?>" min="1" max="6" />
                        <p class="description">1025px以上の画面で表示するスライド数</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">タブレット表示数</th>
                    <td>
                        <input type="number" name="tablet_slides" value="<?php echo esc_attr($tablet_slides); ?>" min="1" max="4" />
                        <p class="description">768px〜1024pxの画面で表示するスライド数</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">モバイル表示数</th>
                    <td>
                        <input type="number" name="mobile_slides" value="<?php echo esc_attr($mobile_slides); ?>" min="1" max="2" />
                        <p class="description">767px以下の画面で表示するスライド数</p>
                    </td>
                </tr>
            </table>
            
            <!-- 自動再生設定 -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">⏰ 自動再生設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">自動再生</th>
                    <td>
                        <label>
                            <input type="checkbox" name="autoplay" value="1" <?php checked($autoplay, 1); ?> />
                            自動でスライドを切り替える
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">再生間隔</th>
                    <td>
                        <input type="number" name="autoplay_interval" value="<?php echo esc_attr($interval); ?>" min="1000" max="10000" step="500" />
                        <span>ミリ秒</span>
                        <p class="description">自動再生の間隔（推奨：3000〜5000ミリ秒）</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">ホバー時一時停止</th>
                    <td>
                        <label>
                            <input type="checkbox" name="pause_on_hover" value="1" <?php checked($pause_hover, 1); ?> />
                            マウスホバー時に自動再生を一時停止する
                        </label>
                    </td>
                </tr>
            </table>
            
            <!-- 操作設定 -->
            <h2 style="background: #f0f8ff; padding: 15px; margin: 30px 0 20px 0; border-radius: 6px;">🎮 操作設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">アニメーション時間</th>
                    <td>
                        <input type="number" name="animation_duration" value="<?php echo esc_attr($duration); ?>" min="200" max="1000" step="50" />
                        <span>ミリ秒</span>
                        <p class="description">スライド切り替えのアニメーション時間</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">スワイプ操作</th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_swipe" value="1" <?php checked($swipe, 1); ?> />
                            タッチ・ドラッグでのスライド操作を有効にする
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">キーボード操作</th>
                    <td>
                        <label>
                            <input type="checkbox" name="enable_keyboard" value="1" <?php checked($keyboard, 1); ?> />
                            左右矢印キーでのスライド操作を有効にする
                        </label>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('設定を保存'); ?>
        </form>
        
        <!-- プレビューセクション -->
        <div style="margin-top: 40px; padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 6px;">
            <h3 style="margin: 0 0 15px 0; color: #333;">現在の設定プレビュー</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 15px;">
                <div style="padding: 15px; background: #f8f9fa; border-radius: 4px;">
                    <h4 style="margin: 0 0 10px 0; color: #333;">📱 表示数</h4>
                    <p style="margin: 5px 0; font-size: 14px;">
                        <strong>デスクトップ:</strong> <?php echo $desktop_slides; ?>枚<br>
                        <strong>タブレット:</strong> <?php echo $tablet_slides; ?>枚<br>
                        <strong>モバイル:</strong> <?php echo $mobile_slides; ?>枚
                    </p>
                </div>
                <div style="padding: 15px; background: #f8f9fa; border-radius: 4px;">
                    <h4 style="margin: 0 0 10px 0; color: #333;">⏰ 自動再生</h4>
                    <p style="margin: 5px 0; font-size: 14px;">
                        <strong>状態:</strong> <?php echo $autoplay ? '有効' : '無効'; ?><br>
                        <strong>間隔:</strong> <?php echo $interval / 1000; ?>秒<br>
                        <strong>ホバー停止:</strong> <?php echo $pause_hover ? '有効' : '無効'; ?>
                    </p>
                </div>
                <div style="padding: 15px; background: #f8f9fa; border-radius: 4px;">
                    <h4 style="margin: 0 0 10px 0; color: #333;">🎮 操作</h4>
                    <p style="margin: 5px 0; font-size: 14px;">
                        <strong>アニメーション:</strong> <?php echo $duration; ?>ms<br>
                        <strong>スワイプ:</strong> <?php echo $swipe ? '有効' : '無効'; ?><br>
                        <strong>キーボード:</strong> <?php echo $keyboard ? '有効' : '無効'; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * BLOGスライダーのCSS変数を出力
 */
function riff_blog_slider_custom_css() {
    if (!is_front_page()) {
        return;
    }
    
    $desktop_slides = get_option('riff_blog_slider_desktop_slides', 3);
    $tablet_slides = get_option('riff_blog_slider_tablet_slides', 2);
    $mobile_slides = get_option('riff_blog_slider_mobile_slides', 1);
    ?>
    <style type="text/css" id="riff-blog-slider-vars">
        :root {
            --blog-slider-desktop-slides: <?php echo intval($desktop_slides); ?>;
            --blog-slider-tablet-slides: <?php echo intval($tablet_slides); ?>;
            --blog-slider-mobile-slides: <?php echo intval($mobile_slides); ?>;
        }
    </style>
    <?php
}
add_action('wp_head', 'riff_blog_slider_custom_css', 101);

/**
 * BLOGスライダー機能の有効化フラグを設定
 */
function riff_enable_blog_slider() {
    return apply_filters('riff_blog_slider_enabled', true);
}

/**
 * 既存のBLOGセクション表示関数をスライダー版に置き換える条件分岐
 */
function riff_maybe_use_slider_blog_section() {
    if (riff_enable_blog_slider()) {
        // 既存関数を無効化し、スライダー版を使用
        remove_action('riff_display_blog_section', 'riff_display_blog_section');
        
        // functions.phpで直接呼び出される場合は関数の置き換えで対応
        if (!function_exists('riff_display_blog_section_original')) {
            // 既存関数をバックアップ
            function riff_display_blog_section_original() {
                // 元の関数の内容（万が一の復元用）
            }
        }
    }
}
add_action('init', 'riff_maybe_use_slider_blog_section', 5);