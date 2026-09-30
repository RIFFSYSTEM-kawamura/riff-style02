<?php
/**
 * SEO・AEO強化：Articleスキーマ・BreadcrumbListスキーマ出力
 */

// ブログ記事のArticleスキーマ
function riff_output_article_schema() {
    if ( ! is_single() ) {
        return;
    }

    $site_title = get_option( 'riff_site_title', get_bloginfo( 'name' ) );
    $home_url   = home_url();

    // 記事内容と関係のないキーワードを付けないよう、記事自身のカテゴリー・タグから生成する
    $keyword_terms = array_merge(
        (array) get_the_category(),
        (array) get_the_tags()
    );
    $keywords = array();
    foreach ( $keyword_terms as $term ) {
        if ( $term instanceof WP_Term && '未分類' !== $term->name ) {
            $keywords[] = $term->name;
        }
    }
    $keywords = array_values( array_unique( $keywords ) );
    if ( empty( $keywords ) ) {
        $keywords = array( 'ホームページ制作', 'LP制作', '業務効率化' );
    }

    $schema = array(
        '@context'          => 'https://schema.org',
        '@type'             => 'Article',
        '@id'               => get_permalink() . '/#article',
        'headline'          => get_the_title(),
        'datePublished'     => get_the_date( 'c' ),
        'dateModified'      => get_the_modified_date( 'c' ),
        'inLanguage'        => 'ja',
        'keywords'          => implode( ',', $keywords ),
        'author'            => array(
            '@type' => 'Organization',
            'name'  => $site_title,
            '@id'   => $home_url . '/#organization',
        ),
        'publisher'         => array(
            '@type' => 'Organization',
            'name'  => $site_title,
            '@id'   => $home_url . '/#organization',
        ),
        'mainEntityOfPage'  => array(
            '@type' => 'WebPage',
            '@id'   => get_permalink(),
        ),
        'isPartOf'          => array( '@id' => $home_url . '/#website' ),
    );

    $description = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 50, '' );
    if ( $description ) {
        $schema['description'] = $description;
    }

    if ( has_post_thumbnail() ) {
        $schema['image'] = array(
            '@type' => 'ImageObject',
            'url'   => get_the_post_thumbnail_url( null, 'large' ),
        );
    }

    $categories = get_the_category();
    if ( ! empty( $categories ) ) {
        $schema['articleSection'] = $categories[0]->name;
    }

    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
    echo "\n</script>\n";
}
add_action( 'wp_head', 'riff_output_article_schema' );

// BreadcrumbListスキーマ（トップページ以外）
function riff_output_breadcrumb_schema() {
    if ( is_front_page() ) {
        return;
    }

    $home_url = home_url();
    $items    = array(
        array( '@type' => 'ListItem', 'position' => 1, 'name' => 'ホーム', 'item' => $home_url ),
    );
    $position = 2;

    if ( is_singular() ) {
        $post_type = get_post_type();
        if ( $post_type === 'post' ) {
            $cats = get_the_category();
            if ( ! empty( $cats ) ) {
                $items[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position,
                    'name'     => $cats[0]->name,
                    'item'     => get_category_link( $cats[0]->term_id ),
                );
                $position++;
            }
        }
        if ( is_page() ) {
            $ancestors = get_post_ancestors( get_the_ID() );
            foreach ( array_reverse( $ancestors ) as $ancestor_id ) {
                $items[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position,
                    'name'     => get_the_title( $ancestor_id ),
                    'item'     => get_permalink( $ancestor_id ),
                );
                $position++;
            }
        }
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    } elseif ( is_category() ) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => single_cat_title( '', false ),
            'item'     => get_category_link( get_queried_object_id() ),
        );
    } elseif ( is_archive() || is_home() ) {
        $title   = is_home() ? get_option( 'riff_blog_title', 'BLOG' ) : get_the_archive_title();
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => $title,
            'item'     => get_pagenum_link( max( 1, get_query_var( 'paged' ) ), false ),
        );
    } elseif ( is_search() ) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => '検索結果: ' . get_search_query(),
            'item'     => get_search_link(),
        );
    }

    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    );

    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
    echo "\n</script>\n";
}
// All in One SEO が BreadcrumbList を出力するため、テーマ側の重複出力は無効化
// add_action( 'wp_head', 'riff_output_breadcrumb_schema' );
