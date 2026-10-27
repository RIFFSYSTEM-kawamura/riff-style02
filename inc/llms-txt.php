<?php
/**
 * llms.txt（AI向けのサイト案内）の新着記事欄の自動更新
 *
 * llms.txt本体は手作業で整えた内容を残し、「## 新着記事（自動更新）」の欄だけを
 * 記事の公開・更新・非公開のたびに書き換える。欄がなければ「## 運営者・お問い合わせ」の前に追加する。
 */

if (!defined('ABSPATH')) {
    exit;
}

// 新着記事欄に載せる記事の数
const RIFF_LLMS_LATEST_COUNT = 10;
const RIFF_LLMS_LATEST_HEADING = '## 新着記事（自動更新）';

function riff_llms_latest_section() {
    global $wpdb;
    $posts = get_posts(array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => RIFF_LLMS_LATEST_COUNT * 3,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'has_password'   => false,
    ));

    $table = $wpdb->prefix . 'aioseo_posts';
    $has_aioseo = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table)) === $table;

    $lines = array(RIFF_LLMS_LATEST_HEADING, '');
    $count = 0;
    foreach ($posts as $post) {
        $row = $has_aioseo ? $wpdb->get_row($wpdb->prepare("SELECT title, description, robots_noindex FROM {$table} WHERE post_id = %d", $post->ID)) : null;
        // 自動生成の「ブログを更新しました」などnoindexの投稿は載せない
        if ($row && $row->robots_noindex) {
            continue;
        }
        $title = wp_strip_all_tags(get_the_title($post));
        $desc = ($row && $row->description) ? $row->description : get_the_excerpt($post);
        $desc = trim(preg_replace('/\s+/u', ' ', wp_strip_all_tags($desc)));
        // AIOSEOのタグ（#post_title など）のままの説明文は載せない
        if (preg_match('/#[a-z_]+/', $desc)) {
            $desc = '';
        }
        $desc = mb_strimwidth($desc, 0, 160, '…');
        $lines[] = '- [' . str_replace(array('[', ']'), array('［', '］'), $title) . '](' . get_permalink($post) . ')' . ($desc !== '' ? ': ' . $desc : '');
        if (++$count >= RIFF_LLMS_LATEST_COUNT) {
            break;
        }
    }
    return implode("\n", $lines) . "\n";
}

function riff_llms_update_latest() {
    $file = ABSPATH . 'llms.txt';
    if (!is_readable($file) || !is_writable($file)) {
        return;
    }
    $text = file_get_contents($file);
    $section = riff_llms_latest_section();

    // 既存の新着記事欄を、次の見出しの手前まで置き換える
    $pattern = '/^' . preg_quote(RIFF_LLMS_LATEST_HEADING, '/') . '\n.*?(?=^## |\z)/msu';
    if (preg_match($pattern, $text)) {
        $new = preg_replace($pattern, $section . "\n", $text, 1);
    } elseif (strpos($text, "\n## 運営者・お問い合わせ") !== false) {
        $new = str_replace("\n## 運営者・お問い合わせ", "\n" . $section . "\n## 運営者・お問い合わせ", $text);
    } else {
        $new = rtrim($text) . "\n\n" . $section;
    }
    if ($new !== $text) {
        file_put_contents($file, $new, LOCK_EX);
    }
}

// 記事の公開・公開中の更新・非公開化のときに更新する（AIOSEOの保存後に反映するため、処理の最後に行う）
add_action('transition_post_status', function ($new_status, $old_status, $post) {
    if ($post->post_type !== 'post' || wp_is_post_revision($post)) {
        return;
    }
    if ($new_status !== 'publish' && $old_status !== 'publish') {
        return;
    }
    if (!has_action('shutdown', 'riff_llms_update_latest')) {
        add_action('shutdown', 'riff_llms_update_latest');
    }
}, 10, 3);
