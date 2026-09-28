<?php
/**
 * アーカイブページ用テンプレート（改良版）
 * 投稿タイプフィルター機能付き
 * 
 * @package RiffTheme
 */

get_header(); ?>

<?php
// ヒーローセクションの表示
display_hero_section();
?>

<div class="main-content">
    <div class="container">
        
        <!-- アーカイブヘッダー -->
        <header class="archive-header text-center mb-5 fade-in">
            
            <!-- パンくずリスト -->
            <nav class="breadcrumb" style="margin-bottom: 30px; font-size: 14px; color: var(--text-light);">
                <a href="<?php echo esc_url(home_url('/')); ?>" style="color: var(--text-light); text-decoration: none;">ホーム</a>
                <span style="margin: 0 10px;">/</span>
                <span>記事一覧</span>
            </nav>
            
            <!-- アーカイブアイコン -->
            <div class="archive-icon" style="margin-bottom: 20px;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-color), #00a0d2); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(0, 124, 186, 0.3);">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="white">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
            </div>
            
            <!-- アーカイブタイトル -->
            <h1 style="font-size: 2.5rem; font-weight: 700; line-height: 1.2; margin-bottom: 20px; color: var(--text-color);">
                <span style="color: var(--primary-color); font-size: 1.2rem; font-weight: 400; display: block; margin-bottom: 10px;">記事一覧</span>
                <?php
                if (is_category()) {
                    single_cat_title();
                } elseif (is_tag()) {
                    single_tag_title();
                } elseif (is_author()) {
                    the_author();
                } elseif (is_date()) {
                    if (is_year()) {
                        echo get_the_date('Y年');
                    } elseif (is_month()) {
                        echo get_the_date('Y年n月');
                    } else {
                        echo get_the_date('Y年n月j日');
                    }
                } else {
                    echo 'すべての記事';
                }
                ?>
            </h1>
            
            <!-- アーカイブ説明 -->
            <?php if (is_category() && category_description()): ?>
            <div class="archive-description" style="max-width: 600px; margin: 0 auto; color: var(--text-light); font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
                <?php echo category_description(); ?>
            </div>
            <?php endif; ?>
            
            <!-- 投稿数 -->
            <div class="archive-meta" style="display: flex; justify-content: center; align-items: center; gap: 20px; margin-bottom: 30px; color: var(--text-light); font-size: 14px;">
                <span>
                    <strong><?php echo number_format($wp_query->found_posts); ?></strong>件の投稿
                </span>
                <?php if (is_category()): ?>
                <a href="<?php echo esc_url(get_category_feed_link(get_query_var('cat'))); ?>" 
                   style="color: var(--accent-color); text-decoration: none; display: flex; align-items: center; gap: 5px; font-weight: 500;"
                   title="このカテゴリーのRSSフィード">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6.503 20.752c0 1.794-1.456 3.248-3.251 3.248-1.796 0-3.252-1.454-3.252-3.248 0-1.794 1.456-3.248 3.252-3.248 1.795.001 3.251 1.454 3.251 3.248zm-6.503-12.572v4.811c6.05.062 10.96 4.966 11.022 11.009h4.818c-.062-8.71-7.118-15.758-15.84-15.82zm0-3.368c10.58.046 19.152 8.594 19.183 19.188h4.817c-.03-13.231-10.755-23.954-24-24v4.812z"/>
                    </svg>
                    RSS
                </a>
                <?php endif; ?>
            </div>
            
        </header>
        
        <!-- 投稿タイプフィルター -->
        <?php
        $current_filter = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : 'all';
        $base_url = strtok($_SERVER["REQUEST_URI"], '?');
        $query_params = $_GET;
        unset($query_params['post_type']); // post_typeパラメータを除去
        $base_url_with_params = $base_url . (!empty($query_params) ? '?' . http_build_query($query_params) : '');
        ?>
        <div class="archive-type-filters fade-in" style="background: linear-gradient(135deg, #f8f9fa, #ffffff); padding: 25px; border-radius: 16px; margin-bottom: 40px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05); border: 1px solid #e9ecef;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="margin: 0; color: var(--text-color); font-size: 1.2rem;">投稿タイプで絞り込み</h3>
                <p style="margin: 8px 0 0; color: var(--text-light); font-size: 14px;">読みたい記事のタイプを選択してください</p>
            </div>
            
            <div class="filter-buttons" style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                <a href="<?php echo esc_url($base_url_with_params); ?>" 
                   class="archive-filter-btn <?php echo $current_filter === 'all' ? 'active' : ''; ?>"
                   style="background: <?php echo $current_filter === 'all' ? 'var(--primary-color)' : '#ffffff'; ?>; 
                          color: <?php echo $current_filter === 'all' ? 'white' : 'var(--text-color)'; ?>; 
                          padding: 12px 24px; 
                          border: 2px solid var(--primary-color); 
                          border-radius: 25px; 
                          text-decoration: none; 
                          font-weight: 600; 
                          transition: all 0.3s ease;
                          display: flex;
                          align-items: center;
                          gap: 8px;
                          min-width: 120px;
                          justify-content: center;">
                    <span>ALL</span>
                </a>
                
                <a href="<?php echo esc_url(add_query_arg('post_type', 'news', $base_url_with_params)); ?>" 
                   class="archive-filter-btn <?php echo $current_filter === 'news' ? 'active' : ''; ?>"
                   style="background: <?php echo $current_filter === 'news' ? 'var(--primary-color)' : '#ffffff'; ?>; 
                          color: <?php echo $current_filter === 'news' ? 'white' : 'var(--text-color)'; ?>; 
                          padding: 12px 24px; 
                          border: 2px solid var(--primary-color); 
                          border-radius: 25px; 
                          text-decoration: none; 
                          font-weight: 600; 
                          transition: all 0.3s ease;
                          display: flex;
                          align-items: center;
                          gap: 8px;
                          min-width: 120px;
                          justify-content: center;">
                    <span>NEWS</span>
                </a>
                
                <a href="<?php echo esc_url(add_query_arg('post_type', 'blog', $base_url_with_params)); ?>" 
                   class="archive-filter-btn <?php echo $current_filter === 'blog' ? 'active' : ''; ?>"
                   style="background: <?php echo $current_filter === 'blog' ? 'var(--primary-color)' : '#ffffff'; ?>; 
                          color: <?php echo $current_filter === 'blog' ? 'white' : 'var(--text-color)'; ?>; 
                          padding: 12px 24px; 
                          border: 2px solid var(--primary-color); 
                          border-radius: 25px; 
                          text-decoration: none; 
                          font-weight: 600; 
                          transition: all 0.3s ease;
                          display: flex;
                          align-items: center;
                          gap: 8px;
                          min-width: 120px;
                          justify-content: center;">
                    <span>BLOG</span>
                </a>
            </div>
            
            <!-- 現在のフィルター状態表示 -->
            <?php if ($current_filter !== 'all'): ?>
            <div style="text-align: center; margin-top: 15px; padding: 10px; background: rgba(0, 124, 186, 0.1); border-radius: 8px;">
                <span style="color: var(--primary-color); font-weight: 600; font-size: 14px;">
                    <?php 
                    switch($current_filter) {
                        case 'news':
                            echo 'NEWS記事を表示中';
                            break;
                        case 'blog':
                            echo 'BLOG記事を表示中';
                            break;
                    }
                    ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- フィルター・ソート -->
        <div class="archive-filters fade-in" style="background: var(--gray-100); padding: 20px; border-radius: 10px; margin-bottom: 40px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                
                <!-- 表示形式 -->
                <div class="view-toggle">
                    <label style="font-size: 14px; font-weight: 600; color: var(--text-color); margin-right: 10px;">表示:</label>
                    <div style="display: flex; background: white; border-radius: 6px; overflow: hidden; border: 1px solid var(--gray-300);">
                        <button onclick="toggleView('grid')" id="grid-view" class="view-btn active" style="padding: 8px 12px; border: none; background: var(--primary-color); color: white; cursor: pointer; transition: var(--transition);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4 11h5V5H4v6zm0 7h5v-6H4v6zm6 0h5v-6h-5v6zm6 0h5v-6h-5v6zm-6-7h5V5h-5v6zm6-6v6h5V5h-5z"/>
                            </svg>
                        </button>
                        <button onclick="toggleView('list')" id="list-view" class="view-btn" style="padding: 8px 12px; border: none; background: white; color: var(--text-color); cursor: pointer; transition: var(--transition);">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4 10.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5zm0-6c-.83 0-1.5.67-1.5 1.5S3.17 7.5 4 7.5 5.5 6.83 5.5 6 4.83 4.5 4 4.5zm0 12c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5zM7 19h14v-2H7v2zm0-6h14v-2H7v2zm0-8v2h14V5H7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- 並び順 -->
                <div class="filter-sort">
                    <label style="font-size: 14px; font-weight: 600; color: var(--text-color); margin-right: 10px;">並び順:</label>
                    <select onchange="changeSortOrder(this.value)" style="padding: 8px 12px; border: 1px solid var(--gray-300); border-radius: 5px; font-size: 14px;">
                        <option value="date_desc" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'date' && isset($_GET['order']) && $_GET['order'] === 'desc'); ?>>新しい順</option>
                        <option value="date_asc" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'date' && isset($_GET['order']) && $_GET['order'] === 'asc'); ?>>古い順</option>
                        <option value="title_asc" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'title'); ?>>タイトル順</option>
                        <option value="comment_count" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'comment_count'); ?>>コメント数順</option>
                        <option value="rand" <?php selected(isset($_GET['orderby']) && $_GET['orderby'] === 'rand'); ?>>ランダム</option>
                    </select>
                </div>
                
                <!-- 検索 -->
                <form method="get" action="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" style="display: flex; gap: 5px;">
                    <?php if (isset($_GET['post_type'])): ?>
                    <input type="hidden" name="post_type" value="<?php echo esc_attr($_GET['post_type']); ?>">
                    <?php endif; ?>
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
        
        <div id="posts-container" class="posts-grid fade-in" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; margin-bottom: 60px;">
            
            <?php while (have_posts()): the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?> style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08); transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94); position: relative; transform: translateY(0); border: 1px solid #f0f0f0;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 20px 60px rgba(0, 0, 0, 0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 30px rgba(0, 0, 0, 0.08)'">
                
                <!-- アイキャッチ画像 -->
                <?php if (has_post_thumbnail()): ?>
                <div class="post-thumbnail" style="aspect-ratio: 16/9; overflow: hidden; position: relative; background: linear-gradient(135deg, #f5f7fa, #c3cfe2);">
                    <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                        <?php the_post_thumbnail('medium_large', array(
                            'style' => 'width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);',
                            'onmouseover' => 'this.style.transform="scale(1.08)"',
                            'onmouseout' => 'this.style.transform="scale(1)"'
                        )); ?>
                    </a>
                    
                    <!-- 投稿タイプバッジ -->
                    <?php 
                    $post_type_flag = get_post_meta(get_the_ID(), 'post_type_flag', true);
                    $post_type_flag = $post_type_flag ? $post_type_flag : 'news';
                    ?>
                    <span style="position: absolute; top: 12px; left: 12px; padding: 6px 12px; border-radius: 15px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: white; 
                        background: <?php 
                        switch($post_type_flag) {
                            case 'news': echo 'linear-gradient(135deg, #1976d2, #42a5f5)'; break;
                            case 'blog': echo 'linear-gradient(135deg, #7b1fa2, #ba68c8)'; break;
                            case 'both': echo 'linear-gradient(135deg, #388e3c, #66bb6a)'; break;
                            default: echo 'linear-gradient(135deg, #757575, #9e9e9e)';
                        }
                        ?>;">
                        <?php 
                        switch($post_type_flag) {
                            case 'news': echo 'NEWS'; break;
                            case 'blog': echo 'BLOG'; break;
                            case 'both': echo 'BOTH'; break;
                            default: echo 'POST';
                        }
                        ?>
                    </span>
                </div>
                <?php endif; ?>
                
                <!-- 投稿内容 -->
                <div class="post-content" style="padding: 25px;">
                    
                    <!-- タイトル -->
                    <h2 class="post-title" style="margin: 0 0 12px; font-size: 18px; line-height: 1.4; font-weight: 600;">
                        <a href="<?php the_permalink(); ?>" style="color: var(--text-color); text-decoration: none; transition: color 0.3s ease;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-color)'">
                            <?php the_title(); ?>
                        </a>
                    </h2>
                    
                    <!-- 抜粋 -->
                    <div class="post-excerpt" style="color: var(--text-light); font-size: 14px; line-height: 1.6; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                        <?php 
                        if (has_excerpt()) {
                            echo wp_trim_words(get_the_excerpt(), 25, '...');
                        } else {
                            echo wp_trim_words(get_the_content(), 25, '...');
                        }
                        ?>
                    </div>
                    
                    <!-- タグ -->
                    <?php 
                    $tags = get_the_tags();
                    if ($tags): 
                    ?>
                    <div class="post-tags" style="margin-bottom: 15px;">
                        <?php foreach (array_slice($tags, 0, 3) as $tag): ?>
                        <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" 
                           style="background: var(--gray-200); color: var(--text-light); padding: 2px 6px; border-radius: 8px; font-size: 10px; text-decoration: none; margin-right: 5px; display: inline-block; margin-bottom: 3px; transition: all 0.3s ease;"
                           onmouseover="this.style.background='var(--primary-color)'; this.style.color='white';"
                           onmouseout="this.style.background='var(--gray-200)'; this.style.color='var(--text-light)';">
                            #<?php echo esc_html($tag->name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
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
            $pagination_args = array(
                'prev_text' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z"/></svg> 前へ',
                'next_text' => '次へ <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z"/></svg>',
                'type' => 'list',
                'before_page_number' => '<span class="screen-reader-text">ページ </span>',
            );
            
            // URLパラメータを保持
            if (isset($_GET['post_type'])) {
                $pagination_args['add_args'] = array('post_type' => $_GET['post_type']);
            }
            
            echo paginate_links($pagination_args);
            ?>
        </nav>
        
        <?php else: ?>
        
        <!-- 投稿が見つからない場合 -->
        <div class="no-posts text-center fade-in" style="padding: 60px 20px; background: linear-gradient(135deg, #f8f9fa, #ffffff); border-radius: 16px; border: 1px solid #e9ecef;">
            
            <div style="margin-bottom: 30px;">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="var(--text-light)" style="opacity: 0.6;">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>
            
            <h2 style="font-size: 24px; margin-bottom: 15px; color: var(--text-color);">
                記事が見つかりませんでした
            </h2>
            
            <p style="color: var(--text-light); margin-bottom: 30px;">
                <?php if (isset($_GET['s']) && !empty($_GET['s'])): ?>
                    「<?php echo esc_html(get_search_query()); ?>」に関する記事は見つかりませんでした。<br>
                    別のキーワードで検索してみてください。
                <?php else: ?>
                    現在、この条件に一致する記事はありません。<br>
                    他の条件で検索してみてください。
                <?php endif; ?>
            </p>
            
            <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" style="background: var(--primary-color); color: white; padding: 14px 28px; border-radius: 25px; font-weight: 600; font-size: 15px; text-decoration: none; transition: all 0.3s ease;">
                    すべての記事を見る
                </a>
                <a href="<?php echo esc_url(home_url('/')); ?>" style="background: var(--secondary-color); color: white; padding: 14px 28px; border-radius: 25px; font-weight: 600; font-size: 15px; text-decoration: none; transition: all 0.3s ease;">
                    ホームに戻る
                </a>
            </div>
            
        </div>
        
        <?php endif; ?>
        
    </div>
</div>

<!-- 表示切り替え・ソート機能用JavaScript -->
<script>
function toggleView(viewType) {
    const container = document.getElementById('posts-container');
    const gridBtn = document.getElementById('grid-view');
    const listBtn = document.getElementById('list-view');
    
    if (viewType === 'list') {
        container.style.gridTemplateColumns = '1fr';
        container.style.gap = '20px';
        
        // リスト表示用スタイル調整
        const cards = container.querySelectorAll('.post-card');
        cards.forEach(card => {
            card.style.display = 'flex';
            card.style.alignItems = 'stretch';
            
            const thumbnail = card.querySelector('.post-thumbnail');
            if (thumbnail) {
                thumbnail.style.aspectRatio = 'auto';
                thumbnail.style.width = '200px';
                thumbnail.style.flexShrink = '0';
            }
        });
        
        gridBtn.style.background = 'white';
        gridBtn.style.color = 'var(--text-color)';
        listBtn.style.background = 'var(--primary-color)';
        listBtn.style.color = 'white';
    } else {
        container.style.gridTemplateColumns = 'repeat(auto-fit, minmax(350px, 1fr))';
        container.style.gap = '30px';
        
        // グリッド表示用スタイルリセット
        const cards = container.querySelectorAll('.post-card');
        cards.forEach(card => {
            card.style.display = 'block';
            
            const thumbnail = card.querySelector('.post-thumbnail');
            if (thumbnail) {
                thumbnail.style.aspectRatio = '16/9';
                thumbnail.style.width = '100%';
            }
        });
        
        gridBtn.style.background = 'var(--primary-color)';
        gridBtn.style.color = 'white';
        listBtn.style.background = 'white';
        listBtn.style.color = 'var(--text-color)';
    }
}

function changeSortOrder(value) {
    const url = new URL(window.location);
    const [orderby, order] = value.split('_');
    
    url.searchParams.set('orderby', orderby);
    url.searchParams.set('order', order);
    
    window.location.href = url.toString();
}

// フィルターボタンのホバーエフェクト
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.archive-filter-btn');
    
    filterButtons.forEach(button => {
        if (!button.classList.contains('active')) {
            button.addEventListener('mouseenter', function() {
                this.style.background = 'var(--primary-color)';
                this.style.color = 'white';
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 8px 25px rgba(0, 124, 186, 0.3)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.background = '#ffffff';
                this.style.color = 'var(--text-color)';
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '';
            });
        }
    });
});
</script>

<!-- スタイル -->
<style>
.pagination .page-numbers {
    display: inline-block;
    padding: 12px 16px;
    margin: 0 5px;
    background: white;
    border: 2px solid var(--gray-300);
    border-radius: 8px;
    color: var(--text-color);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.pagination .page-numbers:hover,
.pagination .page-numbers.current {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 124, 186, 0.3);
}

.pagination .page-numbers.dots {
    border: none;
    background: none;
    color: var(--text-light);
}

.pagination .page-numbers.dots:hover {
    background: none;
    transform: none;
    box-shadow: none;
}

@media (max-width: 768px) {
    .posts-grid {
        grid-template-columns: 1fr !important;
    }
    
    .archive-filters > div {
        flex-direction: column;
        align-items: stretch;
        gap: 20px;
    }
    
    .archive-filters label {
        margin-bottom: 5px;
        margin-right: 0;
    }
    
    .archive-filters select,
    .archive-filters input {
        width: 100%;
    }
    
    .view-toggle {
        order: -1;
    }
    
    .filter-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .archive-filter-btn {
        width: 100%;
        max-width: 200px;
    }
}
</style>

<?php get_footer(); ?>