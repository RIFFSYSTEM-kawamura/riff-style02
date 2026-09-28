<?php
/**
 * ヒーローセクション用テンプレートパーツ
 * 
 * @package YourThemeName
 */

// ページタイトルとサブタイトルを取得
$page_title = '';
$page_subtitle = '';
$background_image = '';

// カスタムフィールドからの値を取得（まずWordPress標準のメタフィールドをチェック）
$hero_background_image_id = get_post_meta(get_the_ID(), 'hero_background_image', true);
if ($hero_background_image_id) {
    $background_image = wp_get_attachment_image_url($hero_background_image_id, 'full');
}

// Advanced Custom Fieldsからの値を取得（ACFがある場合の追加サポート）
if (function_exists('get_field')) {
    $hero_title_custom = get_field('hero_title');
    $hero_subtitle_custom = get_field('hero_subtitle');
    $hero_background_image_acf = get_field('hero_background_image');
    
    if ($hero_title_custom) {
        $page_title = $hero_title_custom;
    }
    if ($hero_subtitle_custom) {
        $page_subtitle = $hero_subtitle_custom;
    }
    // ACFの画像が設定されていて、標準メタフィールドが設定されていない場合
    if ($hero_background_image_acf && is_array($hero_background_image_acf) && empty($background_image)) {
        $background_image = $hero_background_image_acf['url'];
    }
}

// WordPress標準のメタフィールドからの値を優先取得
$hero_title_meta = get_post_meta(get_the_ID(), 'hero_title', true);
$hero_subtitle_meta = get_post_meta(get_the_ID(), 'hero_subtitle', true);

if (!empty($hero_title_meta)) {
    $page_title = $hero_title_meta;
}
if (!empty($hero_subtitle_meta)) {
    $page_subtitle = $hero_subtitle_meta;
}

// カスタムフィールドが設定されていない場合のデフォルト値
if (empty($page_title)) {
    if (is_single()) {
        $page_title = get_the_title();
    } elseif (is_page()) {
        $page_title = get_the_title();
    } elseif (is_category()) {
        $page_title = single_cat_title('', false);
    } elseif (is_tag()) {
        $page_title = single_tag_title('', false);
    } elseif (is_archive()) {
        $page_title = get_the_archive_title();
    } elseif (is_search()) {
        $page_title = 'Search Results';
    } elseif (is_404()) {
        $page_title = 'Page Not Found';
    } else {
        $page_title = get_bloginfo('name');
    }
}

// サブタイトルのデフォルト値
if (empty($page_subtitle)) {
    if (is_single()) {
        $page_subtitle = get_the_excerpt() ? wp_trim_words(get_the_excerpt(), 20) : '';
    } elseif (is_page()) {
        $page_subtitle = get_the_excerpt() ? wp_trim_words(get_the_excerpt(), 20) : '';
    } elseif (is_category()) {
        $page_subtitle = category_description();
    } elseif (is_tag()) {
        $page_subtitle = tag_description();
    } elseif (is_archive()) {
        $page_subtitle = get_the_archive_description();
    } elseif (is_search()) {
        $page_subtitle = sprintf(__('検索結果: %s', 'textdomain'), get_search_query());
    } elseif (is_404()) {
        $page_subtitle = 'お探しのページが見つかりませんでした';
    }
}

// デフォルト背景画像（テーマディレクトリ内に配置）
if (empty($background_image)) {
    $background_image = get_template_directory_uri() . '/assets/images/hero-default-bg.jpg';
}

// HTMLのエスケープ処理
// 「｜」区切りのタイトルは、見やすさのため表示時のみ区切り位置で改行する
// （single.phpのpost-title側と同じロジック。$page_title自体は変更しないため
// meta title・構造化データ等には影響しない）
if ( mb_strpos( $page_title, '｜' ) !== false ) {
    list( $hero_title_main, $hero_title_sub ) = explode( '｜', $page_title, 2 );
    $page_title = esc_html( $hero_title_main ) . '｜<br class="hero-title-break">' . esc_html( $hero_title_sub );
} else {
    $page_title = esc_html($page_title);
}
$page_subtitle = wp_kses_post($page_subtitle);
$background_image = esc_url($background_image);

// 1ページに複数のh1が存在しないよう、本文側に既にh1があるかを判定する。
// single.php / archive.php / archive-news.php / archive-blog.php / category.php /
// search.php / 404.php / index.php はいずれも独自に<h1>を出力するため、
// ヒーロー側は常にh2にする。固定ページ（page.php）のみ、本文（ショートコード等）に
// h1が無い場合に限りヒーローをh1として出力する（0個になるのを避けるため）。
$hero_heading_tag = 'h2';
if (is_page() && !is_front_page()) {
    $rendered_body = do_shortcode(get_the_content());
    if (stripos($rendered_body, '<h1') === false) {
        $hero_heading_tag = 'h1';
    }
}

// 背景画像のスタイル設定
$bg_style = '';
if (!empty($background_image)) {
    $bg_style = sprintf('style="background-image: url(%s);"', $background_image);
}
?>

<section class="hero-section has-background-image" <?php echo $bg_style; ?>>
    <div class="hero-content">
        <?php if (!empty($page_title)) : ?>
            <<?php echo $hero_heading_tag; ?> class="hero-title"><?php echo $page_title; ?></<?php echo $hero_heading_tag; ?>>
        <?php endif; ?>
        
        <?php if (!empty($page_subtitle)) : ?>
            <p class="hero-subtitle"><?php echo $page_subtitle; ?></p>
        <?php endif; ?>
    </div>
</section>