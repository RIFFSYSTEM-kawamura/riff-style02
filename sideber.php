<?php
/**
 * RIFF Theme - Sidebar Template
 * サイドバーテンプレート（オプション）
 */

// サイドバーウィジェットが有効でない場合は表示しない
if (!is_active_sidebar('sidebar-widget-area')) {
    return;
}
?>

<aside id="secondary" class="sidebar widget-area" role="complementary" style="background: var(--gray-100); padding: 30px; border-radius: 10px; margin-top: 40px;">
    
    <!-- サイドバータイトル -->
    <div class="sidebar-header" style="text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid var(--gray-300);">
        <h2 style="font-size: 20px; color: var(--text-color); margin: 0; font-weight: 600;">
            サイドバー
        </h2>
    </div>
    
    <!-- ウィジェットエリア -->
    <div class="sidebar-widgets">
        <?php dynamic_sidebar('sidebar-widget-area'); ?>
    </div>
    
    <!-- デフォルトコンテンツ（ウィジェットがない場合） -->
    <?php if (!dynamic_sidebar('sidebar-widget-area')): ?>
    
    <!-- 検索ウィジェット -->
    <div class="widget widget-search" style="background: white; padding: 25px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3 class="widget-title" style="font-size: 16px; margin-bottom: 15px; color: var(--text-color); font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--primary-color)">
                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
            </svg>
            サイト内検索
        </h3>
        <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" style="display: flex; gap: 5px;">
            <input type="search" name="s" placeholder="キーワードを入力..." value="<?php echo get_search_query(); ?>" style="flex: 1; padding: 10px 12px; border: 1px solid var(--gray-300); border-radius: 6px; font-size: 14px;" required>
            <button type="submit" style="background: var(--primary-color); color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
            </button>
        </form>
    </div>
    
    <!-- 最新記事ウィジェット -->
    <div class="widget widget-recent-posts" style="background: white; padding: 25px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3 class="widget-title" style="font-size: 16px; margin-bottom: 15px; color: var(--text-color); font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--primary-color)">
                <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
            </svg>
            最新記事
        </h3>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <?php
            $recent_posts = wp_get_recent_posts(array(
                'numberposts' => 5,
                'post_status' => 'publish'
            ));
            
            foreach ($recent_posts as $post):
            ?>
            <li style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid var(--gray-200); last-child:border-bottom: none;">
                <a href="<?php echo get_permalink($post['ID']); ?>" style="text-decoration: none; color: inherit; display: block;">
                    <h4 style="font-size: 14px; margin: 0 0 5px; color: var(--text-color); line-height: 1.4; font-weight: 500;">
                        <?php echo wp_trim_words($post['post_title'], 8, '...'); ?>
                    </h4>
                    <time style="font-size: 12px; color: var(--text-light);">
                        <?php echo date('Y.m.d', strtotime($post['post_date'])); ?>
                    </time>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <div style="text-align: center; margin-top: 15px;">
            <a href="<?php echo esc_url(home_url('/blog/')); ?>" style="color: var(--primary-color); font-size: 14px; text-decoration: none; font-weight: 500;">
                すべての記事を見る →
            </a>
        </div>
    </div>
    
    <!-- カテゴリーウィジェット -->
    <div class="widget widget-categories" style="background: white; padding: 25px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3 class="widget-title" style="font-size: 16px; margin-bottom: 15px; color: var(--text-color); font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--primary-color)">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
            カテゴリー
        </h3>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <?php
            $categories = get_categories(array('number' => 8));
            foreach ($categories as $category):
            ?>
            <li style="margin-bottom: 8px;">
                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" 
                   style="color: var(--text-color); text-decoration: none; font-size: 14px; display: flex; justify-content: space-between; align-items: center; padding: 5px 0; transition: var(--transition);"
                   onmouseover="this.style.color='var(--primary-color)'"
                   onmouseout="this.style.color='var(--text-color)'">
                    <span><?php echo esc_html($category->name); ?></span>
                    <span style="background: var(--gray-200); color: var(--text-light); padding: 2px 6px; border-radius: 10px; font-size: 11px;">
                        <?php echo $category->count; ?>
                    </span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    
    <!-- タグクラウドウィジェット -->
    <div class="widget widget-tag-cloud" style="background: white; padding: 25px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3 class="widget-title" style="font-size: 16px; margin-bottom: 15px; color: var(--text-color); font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--primary-color)">
                <path d="M17.63 5.84C17.27 5.33 16.67 5 16 5L5 5.01C3.9 5.01 3 5.9 3 7v10c0 1.1.9 1.99 2 1.99L16 19c.67 0 1.27-.33 1.63-.84L22 12l-4.37-6.16z"/>
            </svg>
            タグ
        </h3>
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            <?php
            $tags = get_tags(array('orderby' => 'count', 'order' => 'DESC', 'number' => 15));
            foreach ($tags as $tag):
            ?>
            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" 
               style="background: var(--gray-200); color: var(--text-color); padding: 4px 8px; border-radius: 12px; text-decoration: none; font-size: 11px; transition: var(--transition);"
               onmouseover="this.style.background='var(--primary-color)'; this.style.color='white'"
               onmouseout="this.style.background='var(--gray-200)'; this.style.color='var(--text-color)'">
                #<?php echo esc_html($tag->name); ?> (<?php echo $tag->count; ?>)
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- アーカイブウィジェット -->
    <div class="widget widget-archives" style="background: white; padding: 25px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3 class="widget-title" style="font-size: 16px; margin-bottom: 15px; color: var(--text-color); font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--primary-color)">
                <path d="M20.54 5.23l-1.39-1.68C18.88 3.21 18.47 3 18 3H6c-.47 0-.88.21-1.16.55L3.46 5.23C3.17 5.57 3 6.02 3 6.5V19c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6.5c0-.48-.17-.93-.46-1.27zM12 17.5L6.5 12H10v-2h4v2h3.5L12 17.5zM5.12 5l.81-1h12l.94 1H5.12z"/>
            </svg>
            アーカイブ
        </h3>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <?php
            wp_get_archives(array(
                'type' => 'monthly',
                'limit' => 12,
                'show_post_count' => true,
                'echo' => false
            ));
            
            // 手動でアーカイブリンクを生成
            global $wpdb;
            $archives = $wpdb->get_results("
                SELECT YEAR(post_date) AS year, MONTH(post_date) AS month, COUNT(*) AS count 
                FROM {$wpdb->posts} 
                WHERE post_status = 'publish' 
                AND post_type = 'post' 
                GROUP BY YEAR(post_date), MONTH(post_date) 
                ORDER BY post_date DESC 
                LIMIT 12
            ");
            
            foreach ($archives as $archive):
                $month_name = date('n月', mktime(0, 0, 0, $archive->month, 1));
            ?>
            <li style="margin-bottom: 8px;">
                <a href="<?php echo get_month_link($archive->year, $archive->month); ?>" 
                   style="color: var(--text-color); text-decoration: none; font-size: 14px; display: flex; justify-content: space-between; align-items: center; padding: 5px 0; transition: var(--transition);"
                   onmouseover="this.style.color='var(--primary-color)'"
                   onmouseout="this.style.color='var(--text-color)'">
                    <span><?php echo $archive->year; ?>年<?php echo $month_name; ?></span>
                    <span style="background: var(--gray-200); color: var(--text-light); padding: 2px 6px; border-radius: 10px; font-size: 11px;">
                        <?php echo $archive->count; ?>
                    </span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    
    <!-- お問い合わせウィジェット -->
    <div class="widget widget-contact" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; padding: 25px; border-radius: 10px; margin-bottom: 30px; text-align: center;">
        <h3 class="widget-title" style="font-size: 16px; margin-bottom: 15px; color: white; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
            </svg>
            お問い合わせ
        </h3>
        <p style="margin: 0 0 20px; font-size: 14px; line-height: 1.6; opacity: 0.9;">
            ご質問やご相談がございましたら、お気軽にお問い合わせください。
        </p>
        <a href="<?php echo esc_url(home_url('/contact/')); ?>" 
           style="background: white; color: var(--primary-color); padding: 12px 20px; border-radius: 25px; text-decoration: none; font-weight: 600; font-size: 14px; display: inline-block; transition: var(--transition);"
           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.2)'"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
            お問い合わせフォーム
        </a>
    </div>
    
    <?php endif; ?>
    
</aside>

<!-- サイドバー用スタイル -->
<style>
.sidebar .widget {
    margin-bottom: 30px;
}

.sidebar .widget:last-child {
    margin-bottom: 0;
}

.sidebar .widget-title {
    position: relative;
    padding-bottom: 10px;
}

.sidebar .widget-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 30px;
    height: 2px;
    background: var(--primary-color);
}

.sidebar a:hover {
    transform: translateX(5px);
}

@media (max-width: 768px) {
    .sidebar {
        margin-top: 60px !important;
        padding: 20px !important;
    }
    
    .sidebar .widget {
        margin-bottom: 25px;
    }
}
</style>