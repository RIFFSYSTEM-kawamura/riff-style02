<?php
/**
 * 個別投稿用テンプレート（改良版）
 * l.pngデザインに基づいた記事詳細ページ
 * 
 * @package RiffTheme
 */

get_header(); ?>

<?php
// ヒーローセクションの表示
display_hero_section();
?>

<?php
// パンくずリストの表示
riff_display_breadcrumb();
?>

<div class="single-post-container">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('single-post-article'); ?>>
            
            <!-- 記事ヘッダー -->
            <header class="post-header">
                <!-- メタ情報（上部） -->
                <div class="post-meta-top">
                    <time class="post-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                        <?php echo esc_html(get_the_date('Y年n月j日')); ?>
                    </time>
                    
                    <?php
                    $categories = get_the_category();
                    if (!empty($categories)) : ?>
                        <div class="post-categories">
                            <?php foreach ($categories as $category) : ?>
                                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" 
                                   class="post-category-badge">
                                    <?php echo esc_html($category->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- 記事タイトル -->
                <?php
                // 「｜」区切りのタイトルは、見やすさのため表示時のみ区切り位置で改行する
                // （the_title()自体は変更しないため、meta title・構造化データ・パンくず等には影響しません）
                $riff_post_title = get_the_title();
                if ( mb_strpos( $riff_post_title, '｜' ) !== false ) {
                    list( $riff_title_main, $riff_title_sub ) = explode( '｜', $riff_post_title, 2 );
                    $riff_post_title_html = esc_html( $riff_title_main ) . '｜<br class="post-title-break">' . esc_html( $riff_title_sub );
                } else {
                    $riff_post_title_html = esc_html( $riff_post_title );
                }
                ?>
                <h1 class="post-title"><?php echo $riff_post_title_html; ?></h1>
                
                <!-- 記事の抜粋（サブタイトルとして使用） -->
                <?php if (has_excerpt()) : ?>
                    <div class="post-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                <?php endif; ?>
            </header>
            
            <!-- アイキャッチ画像 -->
            <?php if (has_post_thumbnail()) : ?>
                <div class="post-featured-image">
                    <?php 
                    the_post_thumbnail('full', array(
                        'class' => 'featured-image',
                        'alt' => get_the_title()
                    )); 
                    ?>
                </div>
            <?php endif; ?>
            
            <!-- 記事本文 -->
            <div class="post-content">
                <?php
                the_content();
                
                wp_link_pages(array(
                    'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'riff-theme') . '</span>',
                    'after'  => '</div>',
                    'link_before' => '<span>',
                    'link_after' => '</span>',
                ));
                ?>
            </div>
            
            <!-- SNSシェアボタン -->
            <div class="post-share-buttons">
                <h3 class="post-share-title">この記事をシェアする</h3>
                <div class="share-buttons-list">
                    <a href="#" class="share-button twitter" data-platform="twitter">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                        X (Twitter)
                    </a>
                    
                    <a href="#" class="share-button facebook" data-platform="facebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </a>
                    
                    <a href="#" class="share-button line" data-platform="line">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771z"/>
                        </svg>
                        LINE
                    </a>
                    
                    <a href="#" class="share-button pocket" data-platform="pocket">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M21.901 4.204C21.642 3.484 20.956 3 20.196 3h-16.392c-.76 0-1.446.484-1.705 1.204C1.891 4.924 2.094 5.711 2.7 6.183L12 14.629l9.3-8.446c.606-.472.809-1.259.601-1.979z"/>
                        </svg>
                        Pocket
                    </a>
                    
                    <a href="#" class="share-button hatena" data-platform="hatena">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6.167 12c0 3.618 2.715 6.5 6.333 6.5S18.833 15.618 18.833 12c0-3.618-2.715-6.5-6.333-6.5S6.167 8.382 6.167 12zM12 8.75c1.243 0 2.25 1.007 2.25 2.25S13.243 13.25 12 13.25 9.75 12.243 9.75 11 10.757 8.75 12 8.75zm0-6.75C5.373 2 0 7.373 0 14s5.373 12 12 12 12-5.373 12-12S18.627 2 12 2z"/>
                        </svg>
                        はてブ
                    </a>
                </div>
            </div>
            
            <!-- タグ -->
            <?php
            $tags = get_the_tags();
            if ($tags) : ?>
                <div class="post-tags">
                    <h3 class="tags-title">タグ</h3>
                    <div class="tags-list">
                        <?php foreach ($tags as $tag) : ?>
                            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" 
                               class="tag-link">
                                <?php echo esc_html($tag->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- 著者情報 -->
            <?php 
            $author_id = get_the_author_meta('ID');
            $author_name = get_the_author();
            $author_bio = get_the_author_meta('description');
            $author_avatar = get_avatar($author_id, 80, '', $author_name, array('class' => 'author-avatar'));
            
            if ($author_bio) : ?>
                <div class="post-author-info">
                    <div class="author-header">
                        <?php echo $author_avatar; ?>
                        <div class="author-details">
                            <h4><?php echo esc_html($author_name); ?></h4>
                            <span class="author-title">記事執筆者</span>
                        </div>
                    </div>
                    <p class="author-bio"><?php echo esc_html($author_bio); ?></p>
                </div>
            <?php endif; ?>
        </article>
        
        <!-- 関連記事 -->
        <?php
        $categories = get_the_category();
        if (!empty($categories)) :
            $category_ids = array();
            foreach ($categories as $category) {
                $category_ids[] = $category->term_id;
            }
            
            $related_posts = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => 3,
                'post__not_in' => array(get_the_ID()),
                'category__in' => $category_ids,
                'orderby' => 'rand'
            ));
            
            if ($related_posts->have_posts()) : ?>
                <section class="related-posts">
                    <h2 class="related-posts-title">関連記事</h2>
                    <div class="related-posts-grid">
                        <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                            <article class="related-post-item">
                                <a href="<?php the_permalink(); ?>" class="related-post-link">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="related-post-image">
                                            <?php the_post_thumbnail('medium', array('class' => 'related-image')); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="related-post-content">
                                        <div class="related-post-meta">
                                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                <?php echo esc_html(get_the_date('Y.m.d')); ?>
                                            </time>
                                            <?php
                                            $post_categories = get_the_category();
                                            if (!empty($post_categories)) :
                                                $primary_category = $post_categories[0];
                                                ?>
                                                <span class="related-post-category">
                                                    <?php echo esc_html($primary_category->name); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <h3 class="related-post-title"><?php the_title(); ?></h3>
                                    </div>
                                </a>
                            </article>
                        <?php endwhile; ?>
                    </div>
                </section>
            <?php 
            endif;
            wp_reset_postdata();
        endif;
        ?>
        
        <!-- 前後記事ナビゲーション -->
        <?php
        $prev_post = get_previous_post();
        $next_post = get_next_post();
        
        if ($prev_post || $next_post) : ?>
            <nav class="post-navigation" aria-label="投稿ナビゲーション">
                <div class="nav-links">
                    <?php if ($prev_post) : ?>
                        <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" class="nav-previous" rel="prev">
                            <span class="nav-label">前の記事</span>
                            <span class="nav-title"><?php echo esc_html(get_the_title($prev_post->ID)); ?></span>
                        </a>
                    <?php else : ?>
                        <div class="nav-placeholder"></div>
                    <?php endif; ?>
                    
                    <?php if ($next_post) : ?>
                        <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" class="nav-next" rel="next">
                            <span class="nav-label">次の記事</span>
                            <span class="nav-title"><?php echo esc_html(get_the_title($next_post->ID)); ?></span>
                        </a>
                    <?php else : ?>
                        <div class="nav-placeholder"></div>
                    <?php endif; ?>
                </div>
            </nav>
        <?php endif; ?>
        
    <?php endwhile; ?>
</div>

<!-- 構造化データ（JSON-LD） -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": "<?php echo esc_js(get_the_title()); ?>",
    "image": <?php echo has_post_thumbnail() ? '"' . esc_url(get_the_post_thumbnail_url(get_the_ID(), 'full')) . '"' : 'null'; ?>,
    "author": {
        "@type": "Person",
        "name": "<?php echo esc_js(get_the_author()); ?>"
    },
    "publisher": {
        "@type": "Organization",
        "name": "<?php echo esc_js(get_bloginfo('name')); ?>",
        "logo": {
            "@type": "ImageObject",
            "url": "<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>"
        }
    },
    "datePublished": "<?php echo esc_js(get_the_date('c')); ?>",
    "dateModified": "<?php echo esc_js(get_the_modified_date('c')); ?>",
    "description": "<?php echo esc_js(has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 30, '...')); ?>",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "<?php echo esc_url(get_permalink()); ?>"
    }
}
</script>

<!-- パンくずナビ用構造化データ -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "ホーム",
            "item": "<?php echo esc_url(home_url('/')); ?>"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "ブログ",
            "item": "<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>"
        },
        {
            "@type": "ListItem",
            "position": 3,
            "name": "<?php echo esc_js(get_the_title()); ?>",
            "item": "<?php echo esc_url(get_permalink()); ?>"
        }
    ]
}
</script>

<?php get_footer(); ?>