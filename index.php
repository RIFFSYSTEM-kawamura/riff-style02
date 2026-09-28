<?php get_header(); ?>

<main class="main-content" role="main">
    <div class="section" style="padding-top: 120px;">
        <div class="container">
            
            <!-- ブログヘッダー -->
            <header class="blog-header text-center mb-5 fade-in">
                
                <!-- パンくずリスト -->
                <nav class="breadcrumb" style="margin-bottom: 30px; font-size: 14px; color: var(--text-light);">
                    <a href="<?php echo esc_url(home_url('/')); ?>" style="color: var(--text-light); text-decoration: none;">ホーム</a>
                    <span style="margin: 0 10px;">/</span>
                    <span>ブログ</span>
                </nav>
                
                <!-- ブログタイトル -->
                <h1 style="font-size: 2.5rem; font-weight: 700; line-height: 1.2; margin-bottom: 20px; color: var(--text-color);">
                    <span style="color: var(--primary-color); font-size: 1.2rem; font-weight: 400; display: block; margin-bottom: 10px;">Blog</span>
                    ブログ
                </h1>
                
                <!-- ブログ説明 -->
                <p style="max-width: 600px; margin: 0 auto; color: var(--text-light); font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                    最新の取り組みや実績、業界の動向について情報をお届けします。
                </p>
                
                <!-- 投稿数 -->
                <?php
                $published_posts = wp_count_posts();
                $total_posts = $published_posts->publish;
                ?>
                <div class="blog-count" style="margin-bottom: 30px; color: var(--text-light); font-size: 14px;">
                    <strong><?php echo number_format($total_posts); ?></strong>件の記事を公開中
                </div>
                
            </header>
            
            <!-- フィルター・ソート -->
            <div class="blog-filters fade-in" style="background: var(--gray-100); padding: 20px; border-radius: 10px; margin-bottom: 40px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    
                    <!-- カテゴリーフィルター -->
                    <div class="filter-categories">
                        <label style="font-size: 14px; font-weight: 600; color: var(--text-color); margin-right: 10px;">カテゴリー:</label>
                        <select onchange="filterByCategory(this.value)" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 5px; font-size: 14px;">
                            <option value="">すべて</option>
                            <?php
                            $categories = get_categories();
                            foreach ($categories as $cat):
                            ?>
                            <option value="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
                                <?php echo esc_html($cat->name); ?> (<?php echo $cat->count; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- 表示順 -->
                    <div class="filter-sort">
                        <label style="font-size: 14px; font-weight: 600; color: var(--text-color); margin-right: 10px;">並び順:</label>
                        <select onchange="changeSortOrder(this.value)" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 5px; font-size: 14px;">
                            <option value="date_desc">新しい順</option>
                            <option value="date_asc">古い順</option>
                            <option value="title_asc">タイトル順</option>
                            <option value="comment_count">コメント数順</option>
                        </select>
                    </div>
                    
                    <!-- 検索フォーム -->
                    <form method="get" action="<?php echo esc_url(home_url('/')); ?>" style="display: flex; gap: 5px;">
                        <input type="search" name="s" placeholder="記事を検索..." value="<?php echo get_search_query(); ?>" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 5px; font-size: 14px; min-width: 200px;">
                        <button type="submit" style="background: var(--primary-color); color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                            </svg>
                        </button>
                    </form>
                    
                </div>
            </div>
            
            <!-- 投稿一覧 -->
            <?php if (have_posts()): ?>
            
            <div class="posts-grid fade-in" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; margin-bottom: 60px;">
                
                <?php while (have_posts()): the_post(); ?>
                
                <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?> style="background: white; border-radius: 10px; overflow: hidden; box-shadow: var(--shadow); transition: var(--transition);" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                    
                    <!-- アイキャッチ画像 -->
                    <?php if (has_post_thumbnail()): ?>
                    <div class="post-thumbnail" style="aspect-ratio: 16/9; overflow: hidden;">
                        <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                            <?php the_post_thumbnail('medium_large', array(
                                'style' => 'width: 100%; height: 100%; object-fit: cover; transition: var(--transition);',
                                'onmouseover' => 'this.style.transform="scale(1.05)"',
                                'onmouseout' => 'this.style.transform="scale(1)"'
                            )); ?>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <!-- 投稿内容 -->
                    <div class="post-content" style="padding: 25px;">
                        
                        <!-- カテゴリー -->
                        <?php 
                        $categories = get_the_category();
                        if (!empty($categories)): 
                        ?>
                        <div class="post-categories" style="margin-bottom: 12px;">
                            <?php foreach (array_slice($categories, 0, 2) as $category): ?>
                            <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" 
                               style="background: var(--primary-color); color: white; padding: 4px 8px; border-radius: 10px; font-size: 11px; text-decoration: none; margin-right: 6px; display: inline-block;">
                                <?php echo esc_html($category->name); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- タイトル -->
                        <h2 class="post-title" style="margin: 0 0 12px; font-size: 18px; line-height: 1.4; font-weight: 600;">
                            <a href="<?php the_permalink(); ?>" style="color: var(--text-color); text-decoration: none;">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                        
                        <!-- 抜粋 -->
                        <div class="post-excerpt" style="color: var(--text-light); font-size: 14px; line-height: 1.6; margin-bottom: 15px;">
                            <?php 
                            if (has_excerpt()) {
                                echo wp_trim_words(get_the_excerpt(), 25, '...');
                            } else {
                                echo wp_trim_words(get_the_content(), 25, '...');
                            }
                            ?>
                        </div>
                        
                        <!-- メタ情報 -->
                        <div class="post-meta" style="display: flex; justify-content: space-between; align-items: center; color: var(--text-light); font-size: 12px; border-top: 1px solid var(--gray-200); padding-top: 15px;">
                            
                            <time datetime="<?php echo get_the_date('c'); ?>" style="display: flex; align-items: center; gap: 5px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                                </svg>
                                <?php echo get_the_date(); ?>
                            </time>
                            
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <!-- 著者 -->
                                <span style="display: flex; align-items: center; gap: 5px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                    <?php the_author(); ?>
                                </span>
                                
                                <!-- コメント数 -->
                                <?php if (comments_open() || get_comments_number()): ?>
                                <a href="<?php comments_link(); ?>" style="display: flex; align-items: center; gap: 5px; color: inherit; text-decoration: none;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M21.99 4c0-1.1-.89-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18zM18 14H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                                    </svg>
                                    <?php comments_number('0', '1', '%'); ?>
                                </a>
                                <?php endif; ?>
                            </div>
                            
                        </div>
                        
                    </div>
                    
                </article>
                
                <?php endwhile; ?>
                
            </div>
            
            <!-- ページネーション -->
            <nav class="pagination fade-in" style="text-align: center; margin-top: 40px;">
                <?php
                echo paginate_links(array(
                    'prev_text' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z"/></svg> 前へ',
                    'next_text' => '次へ <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>',
                    'type' => 'list',
                    'before_page_number' => '<span class="screen-reader-text">ページ </span>',
                ));
                ?>
            </nav>
            
            <?php else: ?>
            
            <!-- 投稿が見つからない場合 -->
            <div class="no-posts text-center fade-in" style="padding: 60px 20px; background: var(--gray-100); border-radius: 10px;">
                
                <div style="margin-bottom: 30px;">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="var(--text-light)" style="opacity: 0.6;">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                
                <h2 style="font-size: 24px; margin-bottom: 15px; color: var(--text-color);">
                    投稿が見つかりませんでした
                </h2>
                
                <p style="color: var(--text-light); margin-bottom: 30px;">
                    申し訳ございませんが、投稿が見つかりませんでした。<br>
                    最初の記事を作成してみてください。
                </p>
                
                <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                    <a href="<?php echo esc_url(admin_url('post-new.php')); ?>" class="hero-button">
                        新しい記事を作成
                    </a>
                    <a href="<?php echo esc_url(home_url('/')); ?>" style="background: var(--secondary-color); color: white; padding: 18px 40px; border-radius: 50px; font-weight: 600; font-size: 16px; text-decoration: none; transition: var(--transition);">
                        ホームに戻る
                    </a>
                </div>
                
            </div>
            
            <?php endif; ?>
            
        </div>
    </div>
</main>

<!-- フィルター機能用JavaScript -->
<script>
function filterByCategory(categoryUrl) {
    if (categoryUrl) {
        window.location.href = categoryUrl;
    } else {
        window.location.href = '<?php echo esc_url(home_url('/blog/')); ?>';
    }
}

function changeSortOrder(value) {
    const url = new URL(window.location);
    const [orderby, order] = value.split('_');
    
    url.searchParams.set('orderby', orderby);
    url.searchParams.set('order', order);
    
    window.location.href = url.toString();
}

// URLパラメータからソート値を復元
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const orderby = urlParams.get('orderby') || 'date';
    const order = urlParams.get('order') || 'desc';
    const sortValue = orderby + '_' + order;
    
    const sortSelect = document.querySelector('.filter-sort select');
    if (sortSelect) {
        sortSelect.value = sortValue;
    }
});
</script>

<!-- ページネーション用スタイル -->
<style>
.pagination .page-numbers {
    display: inline-block;
    padding: 12px 16px;
    margin: 0 5px;
    background: white;
    border: 2px solid var(--gray-300);
    border-radius: 6px;
    color: var(--text-color);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
}

.pagination .page-numbers:hover,
.pagination .page-numbers.current {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    transform: translateY(-2px);
}

.pagination .page-numbers.dots {
    border: none;
    background: none;
    color: var(--text-light);
}

.pagination .page-numbers.dots:hover {
    background: none;
    transform: none;
}

@media (max-width: 767px) {
    .posts-grid {
        grid-template-columns: 1fr;
    }
    
    .blog-filters > div {
        flex-direction: column;
        align-items: stretch;
    }
    
    .blog-filters label {
        margin-bottom: 5px;
    }
    
    .blog-filters select,
    .blog-filters input {
        width: 100%;
    }
}
</style>

<?php get_footer(); ?>