<?php
/**
 * 検索結果ページテンプレート（改良版）
 * NEWS/BLOGフィルター機能付き検索結果
 * 
 * @package RiffTheme
 */

get_header(); ?>

<?php
// ヒーローセクションの表示
display_hero_section();
?>

<div class="search-results-container">
    <div class="container">
        
        <!-- 検索結果ヘッダー -->
        <header class="search-header text-center mb-5 fade-in">
            
            <!-- パンくずリスト -->
            <nav class="breadcrumb" style="margin-bottom: 30px; font-size: 14px; color: var(--text-light);">
                <a href="<?php echo esc_url(home_url('/')); ?>" style="color: var(--text-light); text-decoration: none;">ホーム</a>
                <span style="margin: 0 10px;">/</span>
                <span>検索結果</span>
            </nav>
            
            <!-- 検索アイコン -->
            <div class="search-icon" style="margin-bottom: 20px;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary-color), #00a0d2); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(0, 124, 186, 0.3);">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="white">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                </div>
            </div>
            
            <!-- 検索結果タイトル -->
            <h1 style="font-size: 2.5rem; font-weight: 700; line-height: 1.2; margin-bottom: 20px; color: var(--text-color);">
                <span style="color: var(--primary-color); font-size: 1.2rem; font-weight: 400; display: block; margin-bottom: 10px;">検索結果</span>
                「<?php echo esc_html(get_search_query()); ?>」
            </h1>
            
            <!-- 検索結果数 -->
            <div class="search-meta" style="display: flex; justify-content: center; align-items: center; gap: 20px; margin-bottom: 30px; color: var(--text-light); font-size: 14px;">
                <span>
                    <strong><?php echo number_format($wp_query->found_posts); ?></strong>件の記事が見つかりました
                </span>
                <span style="color: var(--primary-color);">
                    検索時間: <?php timer_stop(); ?>秒
                </span>
            </div>
            
        </header>
        
        <!-- 検索フォーム（再検索用） -->
        <div class="search-form-container fade-in" style="background: linear-gradient(135deg, #f8f9fa, #ffffff); padding: 25px; border-radius: 16px; margin-bottom: 40px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05); border: 1px solid #e9ecef;">
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" style="display: flex; gap: 15px; max-width: 600px; margin: 0 auto; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <input type="search" 
                           name="s" 
                           value="<?php echo esc_attr(get_search_query()); ?>" 
                           placeholder="記事を検索..." 
                           required
                           style="width: 100%; padding: 15px 20px; border: 2px solid #e9ecef; border-radius: 50px; font-size: 16px; transition: all 0.3s ease;"
                           onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 3px rgba(0, 124, 186, 0.1)'"
                           onblur="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'">
                </div>
                <button type="submit" 
                        style="background: var(--primary-color); color: white; border: none; padding: 15px 30px; border-radius: 50px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px;"
                        onmouseover="this.style.background='var(--accent-color)'; this.style.transform='translateY(-2px)'"
                        onmouseout="this.style.background='var(--primary-color)'; this.style.transform='translateY(0)'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    再検索
                </button>
            </form>
        </div>
        
        <!-- 投稿タイプフィルター -->
        <?php if (have_posts()): ?>
        <?php
        $current_filter = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : 'all';
        $search_query = get_search_query();
        $base_url = add_query_arg('s', urlencode($search_query), home_url('/'));
        ?>
        <div class="search-type-filters fade-in" style="background: linear-gradient(135deg, #f8f9fa, #ffffff); padding: 25px; border-radius: 16px; margin-bottom: 40px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05); border: 1px solid #e9ecef;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="margin: 0; color: var(--text-color); font-size: 1.2rem;">投稿タイプで絞り込み</h3>
                <p style="margin: 8px 0 0; color: var(--text-light); font-size: 14px;">検索結果をカテゴリー別に表示</p>
            </div>
            
            <div class="filter-buttons" style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                <a href="<?php echo esc_url($base_url); ?>" 
                   class="search-filter-btn <?php echo $current_filter === 'all' ? 'active' : ''; ?>"
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
                    ALL
                </a>
                
                <a href="<?php echo esc_url(add_query_arg('post_type', 'news', $base_url)); ?>" 
                   class="search-filter-btn <?php echo $current_filter === 'news' ? 'active' : ''; ?>"
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
                    NEWS
                </a>
                
                <a href="<?php echo esc_url(add_query_arg('post_type', 'blog', $base_url)); ?>" 
                   class="search-filter-btn <?php echo $current_filter === 'blog' ? 'active' : ''; ?>"
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
                    BLOG
                </a>
            </div>
            
            <!-- 検索統計 -->
            <div style="text-align: center; margin-top: 20px; padding: 15px; background: rgba(0, 124, 186, 0.05); border-radius: 10px;">
                <div style="display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; font-size: 14px; color: var(--text-color);">
                    <?php
                    // 各タイプの記事数を取得
                    $news_count = new WP_Query(array(
                        's' => get_search_query(),
                        'post_type' => 'post',
                        'posts_per_page' => -1,
                        'meta_query' => array(
                            'relation' => 'OR',
                            array('key' => 'post_type_flag', 'value' => 'news', 'compare' => '='),
                            array('key' => 'post_type_flag', 'value' => 'both', 'compare' => '='),
                            array('key' => 'post_type_flag', 'compare' => 'NOT EXISTS')
                        )
                    ));
                    
                    $blog_count = new WP_Query(array(
                        's' => get_search_query(),
                        'post_type' => 'post',
                        'posts_per_page' => -1,
                        'meta_query' => array(
                            'relation' => 'OR',
                            array('key' => 'post_type_flag', 'value' => 'blog', 'compare' => '='),
                            array('key' => 'post_type_flag', 'value' => 'both', 'compare' => '=')
                        )
                    ));
                    ?>
                    <span><strong>NEWS:</strong> <?php echo $news_count->found_posts; ?>件</span>
                    <span><strong>BLOG:</strong> <?php echo $blog_count->found_posts; ?>件</span>
                    <span><strong>合計:</strong> <?php echo $wp_query->found_posts; ?>件</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- 検索結果一覧 -->
        <?php if (have_posts()): ?>
        
        <div class="search-results-grid fade-in" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; margin-bottom: 60px;">
            
            <?php while (have_posts()): the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class('search-result-card'); ?> style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08); transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94); position: relative; transform: translateY(0); border: 1px solid #f0f0f0;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 20px 60px rgba(0, 0, 0, 0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 30px rgba(0, 0, 0, 0.08)'">
                
                <!-- 検索マッチ度インジケーター -->
                <?php
                $search_query = get_search_query();
                if ( $search_query !== '' ) {
                    $title_matches   = substr_count( strtolower( get_the_title() ),   strtolower( $search_query ) );
                    $content_matches = substr_count( strtolower( get_the_content() ), strtolower( $search_query ) );
                    $total_matches   = $title_matches + $content_matches;
                } else {
                    $total_matches = 0;
                }
                $match_percentage = min( 100, ( $total_matches * 20 ) );
                ?>
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #f0f0f0; z-index: 2;">
                    <div style="height: 100%; background: linear-gradient(90deg, var(--accent-color), var(--primary-color)); width: <?php echo $match_percentage; ?>%; transition: width 0.5s ease;"></div>
                </div>
                
                <!-- アイキャッチ画像 -->
                <?php if (has_post_thumbnail()): ?>
                <div class="search-result-thumbnail" style="aspect-ratio: 16/9; overflow: hidden; position: relative; background: linear-gradient(135deg, #f5f7fa, #c3cfe2);">
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
                    
                    <!-- マッチ度表示 -->
                    <?php if ($total_matches > 0): ?>
                    <span style="position: absolute; top: 12px; right: 12px; background: rgba(0, 0, 0, 0.7); color: white; padding: 4px 8px; border-radius: 10px; font-size: 10px; font-weight: 600;">
                        <?php echo $total_matches; ?>個マッチ
                    </span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <!-- 記事内容 -->
                <div class="search-result-content" style="padding: 25px;">
                    
                    <!-- タイトル（検索語句をハイライト） -->
                    <h2 class="search-result-title" style="margin: 0 0 12px; font-size: 18px; line-height: 1.4; font-weight: 600;">
                        <a href="<?php the_permalink(); ?>" style="color: var(--text-color); text-decoration: none; transition: color 0.3s ease;" onmouseover="this.style.color='var(--primary-color)'" onmouseout="this.style.color='var(--text-color)'">
                            <?php 
                            $title = get_the_title();
                            if ($search_query) {
                                $highlighted_title = str_ireplace($search_query, '<mark style="background: linear-gradient(135deg, #fff3cd, #ffeaa7); padding: 2px 4px; border-radius: 3px; font-weight: 700;">' . $search_query . '</mark>', $title);
                                echo $highlighted_title;
                            } else {
                                echo $title;
                            }
                            ?>
                        </a>
                    </h2>
                    
                    <!-- 検索スニペット（検索語句をハイライト） -->
                    <div class="search-result-excerpt" style="color: var(--text-light); font-size: 14px; line-height: 1.6; margin-bottom: 15px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                        <?php 
                        $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30, '...');
                        if ($search_query) {
                            $highlighted_excerpt = str_ireplace($search_query, '<mark style="background: linear-gradient(135deg, #fff3cd, #ffeaa7); padding: 2px 4px; border-radius: 3px; font-weight: 600;">' . $search_query . '</mark>', $excerpt);
                            echo $highlighted_excerpt;
                        } else {
                            echo $excerpt;
                        }
                        ?>
                    </div>
                    
                    <!-- カテゴリー -->
                    <?php 
                    $categories = get_the_category();
                    if (!empty($categories)): 
                    ?>
                    <div class="search-result-categories" style="margin-bottom: 15px;">
                        <?php foreach (array_slice($categories, 0, 2) as $category): ?>
                        <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" 
                           style="background: var(--gray-200); color: var(--text-light); padding: 4px 8px; border-radius: 12px; font-size: 11px; text-decoration: none; margin-right: 8px; display: inline-block; margin-bottom: 3px; transition: all 0.3s ease;"
                           onmouseover="this.style.background='var(--primary-color)'; this.style.color='white';"
                           onmouseout="this.style.background='var(--gray-200)'; this.style.color='var(--text-light)';">
                            <?php echo esc_html($category->name); ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- メタ情報 -->
                    <div class="search-result-meta" style="display: flex; justify-content: space-between; align-items: center; color: var(--text-light); font-size: 12px; border-top: 1px solid var(--gray-200); padding-top: 15px;">
                        
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
                            
                            <!-- 関連度スコア -->
                            <span style="display: flex; align-items: center; gap: 5px; color: var(--primary-color); font-weight: 600;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                <?php echo min(100, $match_percentage); ?>%
                            </span>
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
            $add_args = array('s' => get_search_query());
            if (isset($_GET['post_type'])) {
                $add_args['post_type'] = $_GET['post_type'];
            }
            $pagination_args['add_args'] = $add_args;
            
            echo paginate_links($pagination_args);
            ?>
        </nav>
        
        <?php else: ?>
        
        <!-- 検索結果が見つからない場合 -->
        <div class="no-search-results text-center fade-in" style="padding: 80px 20px; background: linear-gradient(135deg, #f8f9fa, #ffffff); border-radius: 16px; border: 1px solid #e9ecef;">
            
            <div style="margin-bottom: 40px;">
                <svg width="120" height="120" viewBox="0 0 24 24" fill="var(--text-light)" style="opacity: 0.4;">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
            </div>
            
            <h2 style="font-size: 28px; margin-bottom: 20px; color: var(--text-color);">
                「<?php echo esc_html(get_search_query()); ?>」に関する記事は見つかりませんでした
            </h2>
            
            <p style="color: var(--text-light); margin-bottom: 40px; font-size: 16px; line-height: 1.6; max-width: 600px; margin-left: auto; margin-right: auto;">
                検索条件を変更して再度お試しください。<br>
                より一般的なキーワードや、類義語での検索をお勧めします。
            </p>
            
            <!-- 検索のヒント -->
            <div style="background: rgba(0, 124, 186, 0.05); padding: 30px; border-radius: 12px; margin: 40px auto; max-width: 600px; text-align: left;">
                <h3 style="margin: 0 0 15px; color: var(--primary-color); font-size: 18px;">検索のコツ</h3>
                <ul style="margin: 0; padding-left: 20px; color: var(--text-color); line-height: 1.6;">
                    <li>より短いキーワードで検索してみる</li>
                    <li>類義語や関連する言葉で検索する</li>
                    <li>ひらがな・カタカナ・漢字を変えてみる</li>
                    <li>部分的なキーワードで検索する</li>
                </ul>
            </div>
            
            <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" style="background: var(--primary-color); color: white; padding: 16px 32px; border-radius: 25px; font-weight: 600; font-size: 16px; text-decoration: none; transition: all 0.3s ease;">
                    すべての記事を見る
                </a>
                <a href="<?php echo esc_url(home_url('/')); ?>" style="background: var(--secondary-color); color: white; padding: 16px 32px; border-radius: 25px; font-weight: 600; font-size: 16px; text-decoration: none; transition: all 0.3s ease;">
                    ホームに戻る
                </a>
            </div>
            
        </div>
        
        <?php endif; ?>
        
    </div>
</div>

<!-- 検索フィルター用JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // フィルターボタンのホバーエフェクト
    const filterButtons = document.querySelectorAll('.search-filter-btn');
    
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
    
    // 検索語句のハイライト改良
    const searchQuery = '<?php echo esc_js(get_search_query()); ?>';
    if (searchQuery) {
        // 追加のハイライト処理があればここに追加
    }
});
</script>

<!-- スタイル -->
<style>
.search-results-container {
    background: #ffffff;
    min-height: 60vh;
    padding: 40px 0;
}

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

/* 検索結果ハイライト */
mark {
    background: linear-gradient(135deg, #fff3cd, #ffeaa7) !important;
    padding: 2px 4px !important;
    border-radius: 3px !important;
    font-weight: 600 !important;
}

@media (max-width: 768px) {
    .search-results-grid {
        grid-template-columns: 1fr !important;
        gap: 20px;
    }
    
    .search-form-container form {
        flex-direction: column;
    }
    
    .search-form-container input {
        min-width: auto;
    }
    
    .filter-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .search-filter-btn {
        width: 100%;
        max-width: 200px;
    }
}
</style>

<?php get_footer(); ?>