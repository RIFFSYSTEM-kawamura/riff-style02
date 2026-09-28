<?php
/**
 * NEWSアーカイブページテンプレート
 * 
 * @package RiffTheme
 */

get_header(); ?>

<?php
// ヒーロー設定を取得
$news_hero_enabled = get_option('riff_news_archive_hero_enabled', 1);
$news_title_main = get_option('riff_news_archive_title_main', 'ニュース・お知らせ');
$news_title_sub = get_option('riff_news_archive_title_sub', 'NEWS');
$news_description = get_option('riff_news_archive_description', '最新のニュースやお知らせ、重要な情報をお届けします。');
$news_title_color = get_option('riff_news_archive_title_color', '#ffffff');
$news_text_color = get_option('riff_news_archive_text_color', '#ffffff');
?>

<?php if ($news_hero_enabled): ?>
<!-- ヒーローセクション -->
<section class="riff-news-archive-hero">
    <div class="riff-news-archive-hero-content fade-in">
        <div class="riff-archive-hero-subtitle" style="color: <?php echo esc_attr($news_text_color); ?>;">
            <?php echo esc_html($news_title_sub); ?>
        </div>
        <h1 class="riff-archive-hero-title" style="color: <?php echo esc_attr($news_title_color); ?>;">
            <?php echo esc_html($news_title_main); ?>
        </h1>
        <div class="riff-archive-hero-description" style="color: <?php echo esc_attr($news_text_color); ?>;">
            <?php echo esc_html($news_description); ?>
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
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #1976d2, #42a5f5); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(25, 118, 210, 0.3);">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="white">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                </div>
            </div>
            
            <!-- アーカイブタイトル -->
            <h1 style="font-size: 2.5rem; font-weight: 700; line-height: 1.2; margin-bottom: 20px; color: var(--text-color);">
                <span style="color: #1976d2; font-size: 1.2rem; font-weight: 400; display: block; margin-bottom: 10px;"><?php echo esc_html($news_title_sub); ?></span>
                <?php echo esc_html($news_title_main); ?>
            </h1>
            
            <!-- アーカイブ説明 -->
            <div class="archive-description" style="max-width: 600px; margin: 0 auto; color: var(--text-light); font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                <?php echo esc_html($news_description); ?>
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
                   class="archive-nav-btn active"
                   style="background: #1976d2; color: white; padding: 8px 20px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 14px; transition: all 0.3s ease;">
                    ニュース
                </a>
                
                <a href="<?php echo esc_url(home_url('/blog/')); ?>" 
                   class="archive-nav-btn"
                   style="background: transparent; color: #666; padding: 8px 20px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 14px; transition: all 0.3s ease;"
                   onmouseover="this.style.background='#e9ecef';"
                   onmouseout="this.style.background='transparent';">
                    ブログ
                </a>
            </div>
        </div>
        
        <!-- 検索バー -->
        <div class="search-bar fade-in" style="text-align: center; margin-bottom: 40px;">
            <form method="get" action="<?php echo esc_url(home_url('/news/')); ?>" style="max-width: 500px; margin: 0 auto;">
                <div style="position: relative; display: flex; background: white; border: 2px solid #e9ecef; border-radius: 50px; padding: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <input type="search" name="s" placeholder="ニュースを検索..." value="<?php echo get_search_query(); ?>" style="flex: 1; padding: 12px 20px; border: none; border-radius: 50px; font-size: 14px; outline: none; background: transparent;">
                    <button type="submit" style="background: #1976d2; color: white; border: none; padding: 12px 20px; border-radius: 50px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 500; transition: all 0.3s ease;" onmouseover="this.style.background='#1565c0';" onmouseout="this.style.background='#1976d2';">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        検索
                    </button>
                </div>
            </form>
        </div>
        
        <!-- 投稿一覧 -->
        <?php
        // NEWSタイプの投稿を取得
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
            )
        );
        
        // 検索クエリがある場合
        if (!empty($search_query)) {
            $args['s'] = $search_query;
        }
        
        $news_query = new WP_Query($args);
        global $wp_query;
        $temp_query = $wp_query;
        $wp_query = $news_query;
        ?>
        
        <?php if ($news_query->have_posts()): ?>
        
        <!-- 投稿数表示 -->
        <div class="archive-meta text-center fade-in" style="margin-bottom: 30px; color: var(--text-light); font-size: 14px;">
            <span><strong><?php echo number_format($news_query->found_posts); ?></strong>件のニュース記事があります</span>
            <?php if (!empty($search_query)): ?>
            <span style="margin-left: 20px;">「<?php echo esc_html($search_query); ?>」の検索結果</span>
            <?php endif; ?>
        </div>
        
        <div id="posts-container" class="posts-grid fade-in" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 60px; max-width: 1200px; margin-left: auto; margin-right: auto;">
            
            <?php while ($news_query->have_posts()): $news_query->the_post(); ?>
            
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
                'base' => home_url('/news/page/%#%/'),
                'format' => '',
                'current' => max(1, $paged),
                'total' => $news_query->max_num_pages,
                'prev_text' => '← 前へ',
                'next_text' => '次へ →',
                'type' => 'list',
                'before_page_number' => '<span class="screen-reader-text">ページ </span>',
            );
            
            echo paginate_links($pagination_args);
            ?>
        </nav>
        
        <?php else: ?>
        
        <!-- 投稿が見つからない場合 -->
        <div class="no-posts text-center fade-in" style="padding: 60px 20px; background: #f8f9fa; border-radius: 12px; max-width: 600px; margin: 0 auto;">
            
            <h2 style="font-size: 20px; margin-bottom: 15px; color: #333;">
                ニュース記事が見つかりませんでした
            </h2>
            
            <p style="color: #666; margin-bottom: 30px;">
                <?php if (!empty($search_query)): ?>
                    「<?php echo esc_html($search_query); ?>」に関するニュース記事は見つかりませんでした。
                <?php else: ?>
                    現在、ニュース記事はありません。
                <?php endif; ?>
            </p>
            
            <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                <a href="<?php echo esc_url(home_url('/blog/')); ?>" style="background: #7b1fa2; color: white; padding: 10px 20px; border-radius: 6px; font-weight: 500; text-decoration: none;">
                    ブログ記事を見る
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
    color: #1976d2;
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
}
</style>

<?php get_footer(); ?>