<?php
/**
 * BLOGアーカイブページテンプレート
 * 
 * @package RiffTheme
 */

get_header(); ?>

<?php
// ヒーロー設定を取得
$blog_hero_enabled = get_option('riff_blog_archive_hero_enabled', 1);
$blog_title_main = get_option('riff_blog_archive_title_main', 'ブログ記事');
$blog_title_sub = get_option('riff_blog_archive_title_sub', 'BLOG');
$blog_description = get_option('riff_blog_archive_description', '技術情報やノウハウ、コラムなど有益な情報をお届けします。');
$blog_title_color = get_option('riff_blog_archive_title_color', '#ffffff');
$blog_text_color = get_option('riff_blog_archive_text_color', '#ffffff');
?>

<?php if ($blog_hero_enabled): ?>
<!-- ヒーローセクション -->
<section class="riff-blog-archive-hero">
    <div class="riff-blog-archive-hero-content fade-in">
        <div class="riff-archive-hero-subtitle" style="color: <?php echo esc_attr($blog_text_color); ?>;">
            <?php echo esc_html($blog_title_sub); ?>
        </div>
        <h1 class="riff-archive-hero-title" style="color: <?php echo esc_attr($blog_title_color); ?>;">
            <?php echo esc_html($blog_title_main); ?>
        </h1>
        <div class="riff-archive-hero-description" style="color: <?php echo esc_attr($blog_text_color); ?>;">
            <?php echo esc_html($blog_description); ?>
        </div>
    </div>
</section>
<?php else: ?>
<!-- 従来のシンプルヘッダー -->
<div style="padding: 40px 0; background: #f8f9fa;">
    <div class="container">
        <!-- アーカイブヘッダー -->
        <header class="archive-header text-center mb-5 fade-in">
            <!-- アーカイブアイコン -->
            <div class="archive-icon" style="margin-bottom: 20px;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #7b1fa2, #ba68c8); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(123, 31, 162, 0.3);">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="white">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h8c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                    </svg>
                </div>
            </div>
            
            <!-- アーカイブタイトル -->
            <h1 style="font-size: 2.5rem; font-weight: 700; line-height: 1.2; margin-bottom: 20px; color: var(--text-color);">
                <span style="color: #7b1fa2; font-size: 1.2rem; font-weight: 400; display: block; margin-bottom: 10px;"><?php echo esc_html($blog_title_sub); ?></span>
                <?php echo esc_html($blog_title_main); ?>
            </h1>
            
            <!-- アーカイブ説明 -->
            <div class="archive-description" style="max-width: 600px; margin: 0 auto; color: var(--text-light); font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                <?php echo esc_html($blog_description); ?>
            </div>
        </header>
    </div>
</div>
<?php endif; ?>

<?php
// パンくずリストの表示
riff_display_breadcrumb();
?>

<div class="main-content">
    <div class="container">
        
        <!-- アーカイブタイプ切り替え -->
        <div class="archive-type-navigation fade-in" style="text-align: center; margin-top: 30px; margin-bottom: 30px;">
            <div style="display: inline-flex; gap: 0; background: #f8f9fa; border-radius: 8px; padding: 4px; border: 1px solid #e9ecef;">
                <a href="<?php echo esc_url(home_url('/news/')); ?>" 
                   class="archive-nav-btn"
                   style="background: transparent; color: #666; padding: 8px 20px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 14px; transition: all 0.3s ease;"
                   onmouseover="this.style.background='#e9ecef';"
                   onmouseout="this.style.background='transparent';">
                    ニュース
                </a>
                
                <a href="<?php echo esc_url(home_url('/blog/')); ?>" 
                   class="archive-nav-btn active"
                   style="background: #7b1fa2; color: white; padding: 8px 20px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 14px; transition: all 0.3s ease;">
                    ブログ
                </a>
            </div>
        </div>
        
        <!-- 検索バー -->
        <div class="search-bar fade-in" style="text-align: center; margin-bottom: 40px;">
            <form method="get" action="<?php echo esc_url(home_url('/blog/')); ?>" style="max-width: 500px; margin: 0 auto;">
                <div style="position: relative; display: flex; background: white; border: 2px solid #e9ecef; border-radius: 50px; padding: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <?php if (!empty($current_category)): ?>
                    <input type="hidden" name="category" value="<?php echo esc_attr($current_category); ?>">
                    <?php endif; ?>
                    <input type="search" name="s" placeholder="ブログ記事を検索..." value="<?php echo get_search_query(); ?>" style="flex: 1; padding: 12px 20px; border: none; border-radius: 50px; font-size: 14px; outline: none; background: transparent;">
                    <button type="submit" style="background: #7b1fa2; color: white; border: none; padding: 12px 20px; border-radius: 50px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 500; transition: all 0.3s ease;" onmouseover="this.style.background='#6a1b9a';" onmouseout="this.style.background='#7b1fa2';">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        検索
                    </button>
                </div>
            </form>
        </div>
        
        <!-- カテゴリーフィルター -->
        <?php
        // ブログ記事（post_type_flag=blog）で実際に使用されているカテゴリーのみを取得
        // ※ニュース記事など他のpost_type_flagで使われているカテゴリーは含めない
        $blog_post_ids_for_filter = get_posts(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_key' => 'post_type_flag',
            'meta_value' => 'blog',
        ));

        $blog_categories = array();
        if (!empty($blog_post_ids_for_filter)) {
            $blog_categories = wp_get_object_terms($blog_post_ids_for_filter, 'category', array(
                'orderby' => 'count',
                'order' => 'DESC',
            ));
            $blog_categories = array_slice($blog_categories, 0, 8);
        }

        $current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
        ?>
        <?php if (!empty($blog_categories)): ?>
        <div class="category-filters fade-in" style="text-align: center; margin-bottom: 30px;">
            <div style="display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; max-width: 800px; margin: 0 auto;">
                <a href="<?php echo esc_url(home_url('/blog/')); ?>" 
                   class="category-filter-btn <?php echo empty($current_category) ? 'active' : ''; ?>"
                   style="background: <?php echo empty($current_category) ? '#7b1fa2' : '#f8f9fa'; ?>; color: <?php echo empty($current_category) ? 'white' : '#666'; ?>; padding: 6px 12px; border: 1px solid #e9ecef; border-radius: 20px; text-decoration: none; font-size: 12px; font-weight: 500; transition: all 0.3s ease;">
                    すべて
                </a>
                <?php foreach (array_slice($blog_categories, 0, 6) as $category): ?>
                <a href="<?php echo esc_url(add_query_arg('category', $category->slug, home_url('/blog/'))); ?>" 
                   class="category-filter-btn <?php echo $current_category === $category->slug ? 'active' : ''; ?>"
                   style="background: <?php echo $current_category === $category->slug ? '#7b1fa2' : '#f8f9fa'; ?>; color: <?php echo $current_category === $category->slug ? 'white' : '#666'; ?>; padding: 6px 12px; border: 1px solid #e9ecef; border-radius: 20px; text-decoration: none; font-size: 12px; font-weight: 500; transition: all 0.3s ease;"
                   onmouseover="if(this.style.background !== 'rgb(123, 31, 162)') { this.style.background='#e9ecef'; }"
                   onmouseout="if(this.classList.contains('active') === false) { this.style.background='#f8f9fa'; }">
                    <?php echo esc_html($category->name); ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- 投稿一覧 -->
        <?php
        // BLOGタイプの投稿を取得
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $search_query = get_search_query();
        
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => 12,
            'paged' => $paged,
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
        );
        
        // カテゴリーフィルター
        if (!empty($current_category)) {
            $args['category_name'] = $current_category;
        }
        
        // 検索クエリがある場合
        if (!empty($search_query)) {
            $args['s'] = $search_query;
        }
        
        $blog_query = new WP_Query($args);
        global $wp_query;
        $temp_query = $wp_query;
        $wp_query = $blog_query;
        ?>
        
        <?php if ($blog_query->have_posts()): ?>
        
        <!-- 投稿数表示 -->
        <div class="archive-meta text-center fade-in" style="margin-bottom: 30px; color: #666; font-size: 14px;">
            <span><strong><?php echo number_format($blog_query->found_posts); ?></strong>件のブログ記事があります</span>
            <?php if (!empty($search_query)): ?>
            <span style="margin-left: 20px;">「<?php echo esc_html($search_query); ?>」の検索結果</span>
            <?php endif; ?>
            <?php if (!empty($current_category)): ?>
                <?php 
                $cat_obj = get_category_by_slug($current_category);
                if ($cat_obj): ?>
                <span style="margin-left: 20px;">カテゴリー「<?php echo esc_html($cat_obj->name); ?>」</span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <div id="posts-container" class="posts-grid fade-in" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 60px; max-width: 1200px; margin-left: auto; margin-right: auto;">
            
            <?php while ($blog_query->have_posts()): $blog_query->the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?> style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; border: 1px solid #f0f0f0;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 25px rgba(0, 0, 0, 0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0, 0, 0, 0.08)'">
                
                <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                    <!-- アイキャッチ画像 -->
                    <?php if (has_post_thumbnail()): ?>
                    <div class="post-thumbnail" style="aspect-ratio: 16/9; overflow: hidden; position: relative;">
                        <?php the_post_thumbnail('medium_large', array(
                            'style' => 'width: 100%; height: 100%; object-fit: cover;'
                        )); ?>
                        
                        <!-- 日付バッジ -->
                        <div style="position: absolute; top: 10px; left: 10px; background: rgba(255,255,255,0.95); padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; color: #333;">
                            <?php echo get_the_date('Y.m.d'); ?>
                        </div>
                        
                        <!-- カテゴリーバッジ -->
                        <?php 
                        $categories = get_the_category();
                        if ($categories): 
                            $primary_category = $categories[0];
                        ?>
                        <div style="position: absolute; top: 10px; right: 10px; background: rgba(123, 31, 162, 0.9); color: white; padding: 4px 8px; border-radius: 6px; font-size: 10px; font-weight: 600;">
                            <?php echo esc_html($primary_category->name); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- 投稿内容 -->
                    <div class="post-content" style="padding: 20px;">
                        
                        <!-- タイトル -->
                        <h2 class="post-title" style="margin: 0; font-size: 16px; line-height: 1.4; font-weight: 600; color: #333; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php the_title(); ?>
                        </h2>
                        
                    </div>
                </a>
                
            </article>
            
            <?php endwhile; ?>
            
        </div>
        
        <!-- ページネーション -->
        <nav class="pagination fade-in" style="text-align: center; margin-top: 40px;">
            <?php
            $pagination_args = array(
                'base' => home_url('/blog/page/%#%/'),
                'format' => '',
                'current' => max(1, $paged),
                'total' => $blog_query->max_num_pages,
                'prev_text' => '← 前へ',
                'next_text' => '次へ →',
                'type' => 'list',
                'before_page_number' => '<span class="screen-reader-text">ページ </span>',
            );
            
            // カテゴリーやサーチクエリがある場合のURL調整
            $add_args = array();
            if (!empty($current_category)) {
                $add_args['category'] = $current_category;
            }
            if (!empty($search_query)) {
                $add_args['s'] = $search_query;
            }
            if (!empty($add_args)) {
                $pagination_args['add_args'] = $add_args;
            }
            
            echo paginate_links($pagination_args);
            ?>
        </nav>
        
        <?php else: ?>
        
        <!-- 投稿が見つからない場合 -->
        <div class="no-posts text-center fade-in" style="padding: 60px 20px; background: #f8f9fa; border-radius: 12px; max-width: 600px; margin: 0 auto;">
            
            <h2 style="font-size: 20px; margin-bottom: 15px; color: #333;">
                ブログ記事が見つかりませんでした
            </h2>
            
            <p style="color: #666; margin-bottom: 30px;">
                <?php if (!empty($search_query)): ?>
                    「<?php echo esc_html($search_query); ?>」に関するブログ記事は見つかりませんでした。
                <?php elseif (!empty($current_category)): ?>
                    <?php
                    $cat_obj = get_category_by_slug($current_category);
                    if ($cat_obj): ?>
                    「<?php echo esc_html($cat_obj->name); ?>」カテゴリーのブログ記事は見つかりませんでした。
                    <?php endif; ?>
                <?php else: ?>
                    現在、ブログ記事はありません。
                <?php endif; ?>
            </p>
            
            <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                <a href="<?php echo esc_url(home_url('/news/')); ?>" style="background: #1976d2; color: white; padding: 10px 20px; border-radius: 6px; font-weight: 500; text-decoration: none;">
                    ニュース記事を見る
                </a>
                <a href="<?php echo esc_url(home_url('/')); ?>" style="background: #6c757d; color: white; padding: 10px 20px; border-radius: 6px; font-weight: 500; text-decoration: none;">
                    ホームに戻る
                </a>
            </div>
            
        </div>
        
        <?php endif; ?>
        
        <?php 
        // クエリをリセット
        wp_reset_postdata();
        $wp_query = $temp_query;
        ?>
        
    </div>
</div>

<!-- スタイル -->
<style>
.pagination ul.page-numbers {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 4px;
}

.pagination ul.page-numbers li {
    display: inline-block;
    list-style: none;
}

.pagination .page-numbers {
    display: inline-block;
    padding: 10px 15px;
    margin: 0 2px;
    color: #333;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.pagination .page-numbers:hover,
.pagination .page-numbers.current {
    color: #7b1fa2;
}

.pagination .page-numbers.dots {
    color: #999;
}

.pagination .page-numbers.dots:hover {
    color: #999;
}

@media (max-width: 768px) {
    .posts-grid {
        grid-template-columns: 1fr !important;
        gap: 15px !important;
    }
    
    .archive-type-navigation > div {
        gap: 4px;
    }

    .archive-type-navigation .archive-nav-btn {
        padding: 8px 14px !important;
        font-size: 13px !important;
    }

    .search-bar form > div {
        border-radius: 50px;
        padding: 4px;
    }

    .search-bar input {
        min-width: 0;
        padding: 10px 14px !important;
        font-size: 13px !important;
    }

    .search-bar button {
        border-radius: 50px !important;
        padding: 10px 16px !important;
        flex-shrink: 0;
        white-space: nowrap;
    }

    .category-filters > div {
        justify-content: center;
    }
}
</style>

<?php get_footer(); ?>