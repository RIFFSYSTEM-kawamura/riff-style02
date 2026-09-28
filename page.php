<?php
/**
 * 固定ページ用テンプレート
 * 
 * @package YourThemeName
 */

get_header(); ?>

<?php
// ヒーローセクションの表示（front-page以外で表示）
display_hero_section();
?>

<?php
// パンくずリストの表示
riff_display_breadcrumb();
?>

<div class="main-content">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="entry-content">
                    <?php
                    the_content();
                    
                    wp_link_pages(array(
                        'before' => '<div class="page-links">' . esc_html__('Pages:', 'textdomain'),
                        'after'  => '</div>',
                    ));
                    ?>
                </div>
                
                <?php if (comments_open() || get_comments_number()) : ?>
                    <div class="comments-section">
                        <?php comments_template(); ?>
                    </div>
                <?php endif; ?>
            </article>
        <?php endwhile; ?>
    </div>
</div>

<?php get_footer(); ?>