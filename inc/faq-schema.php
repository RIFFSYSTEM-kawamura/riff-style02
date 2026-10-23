<?php
/**
 * 記事ごとのFAQ（よくある質問）機能
 * 投稿編集画面でQ&Aを最大8組登録すると、本文末尾にFAQセクションを自動表示し、
 * あわせてFAQPageの構造化データ（JSON-LD）をwp_headに出力する。
 */

// 1記事に登録できるQ&Aの数
const RIFF_FAQ_MAX = 8;

function riff_faq_meta_box_callback($post) {
    wp_nonce_field('riff_faq_save', 'riff_faq_nonce');
    for ($i = 1; $i <= RIFF_FAQ_MAX; $i++) {
        $q = get_post_meta($post->ID, "riff_faq_q{$i}", true);
        $a = get_post_meta($post->ID, "riff_faq_a{$i}", true);
        echo '<div style="margin-bottom:16px; padding-bottom:16px; border-bottom:1px solid #eee;">';
        echo '<p style="margin:0 0 4px;"><strong>質問' . $i . '</strong></p>';
        echo '<input type="text" name="riff_faq_q' . $i . '" value="' . esc_attr($q) . '" class="widefat" style="margin-bottom:8px;" placeholder="質問文" />';
        echo '<textarea name="riff_faq_a' . $i . '" rows="3" class="widefat" placeholder="回答文">' . esc_textarea($a) . '</textarea>';
        echo '</div>';
    }
}

function riff_faq_add_meta_box() {
    add_meta_box(
        'riff_faq_box',
        'よくある質問（FAQ）',
        'riff_faq_meta_box_callback',
        'post',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'riff_faq_add_meta_box');

function riff_faq_save_meta_box($post_id) {
    if (!isset($_POST['riff_faq_nonce']) || !wp_verify_nonce($_POST['riff_faq_nonce'], 'riff_faq_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    for ($i = 1; $i <= RIFF_FAQ_MAX; $i++) {
        $q = isset($_POST["riff_faq_q{$i}"]) ? sanitize_text_field(wp_unslash($_POST["riff_faq_q{$i}"])) : '';
        $a = isset($_POST["riff_faq_a{$i}"]) ? sanitize_textarea_field(wp_unslash($_POST["riff_faq_a{$i}"])) : '';
        update_post_meta($post_id, "riff_faq_q{$i}", $q);
        update_post_meta($post_id, "riff_faq_a{$i}", $a);
    }
}
add_action('save_post', 'riff_faq_save_meta_box');

function riff_faq_get_items($post_id) {
    $items = [];
    for ($i = 1; $i <= RIFF_FAQ_MAX; $i++) {
        $q = get_post_meta($post_id, "riff_faq_q{$i}", true);
        $a = get_post_meta($post_id, "riff_faq_a{$i}", true);
        if ($q && $a) {
            $items[] = ['q' => $q, 'a' => $a];
        }
    }
    return $items;
}

function riff_faq_append_to_content($content) {
    if (!is_single() || get_post_type() !== 'post' || !in_the_loop() || !is_main_query()) {
        return $content;
    }
    $items = riff_faq_get_items(get_the_ID());
    if (empty($items)) {
        return $content;
    }

    $html = '<div class="riff-faq-block" style="margin-top:32px; padding-top:24px; border-top:1px solid #eee;">';
    $html .= '<h2>よくある質問</h2>';
    foreach ($items as $item) {
        $html .= '<div style="margin-bottom:20px;">';
        $html .= '<p style="font-weight:700; margin:0 0 6px;">Q. ' . esc_html($item['q']) . '</p>';
        $html .= '<p style="margin:0; color:#444;">A. ' . nl2br(esc_html($item['a'])) . '</p>';
        $html .= '</div>';
    }
    $html .= '</div>';

    return $content . $html;
}
add_filter('the_content', 'riff_faq_append_to_content', 20);

function riff_faq_output_schema() {
    if (!is_single() || get_post_type() !== 'post') {
        return;
    }
    $items = riff_faq_get_items(get_the_ID());
    if (empty($items)) {
        return;
    }

    $schema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array_map(function ($item) {
            return [
                '@type' => 'Question',
                'name'  => $item['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $item['a'],
                ],
            ];
        }, $items),
    ];

    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo "\n</script>\n";
}
add_action('wp_head', 'riff_faq_output_schema');
