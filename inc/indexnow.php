<?php
/**
 * IndexNow（Bingなどへの更新通知）
 *
 * 記事・ページを公開・更新したときに、そのURLをIndexNowに送信して
 * Bing（ChatGPT検索・Copilotの元データ）などに早く読み込んでもらう。
 * 認証用のキーはオプションに保存し、/{キー}.txt で応答する。
 */

if (!defined('ABSPATH')) {
    exit;
}

// 同じURLを続けて送らない間隔（秒）
const RIFF_INDEXNOW_INTERVAL = 600;

function riff_indexnow_key() {
    $key = get_option('riff_indexnow_key');
    if (!$key) {
        $key = strtolower(wp_generate_password(32, false, false));
        update_option('riff_indexnow_key', $key, false);
    }
    return $key;
}

// /{キー}.txt にアクセスされたらキーを返す（IndexNowの所有確認用）
add_action('init', function () {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if ($path === '/' . riff_indexnow_key() . '.txt') {
        header('Content-Type: text/plain; charset=utf-8');
        echo riff_indexnow_key();
        exit;
    }
}, 0);

// AIOSEOでnoindexにしている投稿は送らない
function riff_indexnow_is_noindex($post_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'aioseo_posts';
    if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table)) !== $table) {
        return false;
    }
    return (bool) $wpdb->get_var($wpdb->prepare("SELECT robots_noindex FROM {$table} WHERE post_id = %d", $post_id));
}

function riff_indexnow_submit($url) {
    $transient = 'riff_indexnow_' . md5($url);
    if (get_transient($transient)) {
        return;
    }
    set_transient($transient, 1, RIFF_INDEXNOW_INTERVAL);

    $key = riff_indexnow_key();
    wp_remote_post('https://api.indexnow.org/indexnow', array(
        'blocking' => false,
        'timeout'  => 5,
        'headers'  => array('Content-Type' => 'application/json; charset=utf-8'),
        'body'     => wp_json_encode(array(
            'host'        => wp_parse_url(home_url(), PHP_URL_HOST),
            'key'         => $key,
            'keyLocation' => home_url('/' . $key . '.txt'),
            'urlList'     => array($url),
        )),
    ));
}

// 公開・公開中の更新のときに送信する（noindexの判定はAIOSEOの保存後に行うため、少し遅らせる）
add_action('transition_post_status', function ($new_status, $old_status, $post) {
    if ($new_status !== 'publish' || wp_is_post_revision($post) || wp_is_post_autosave($post)) {
        return;
    }
    if (!is_post_type_viewable($post->post_type) || $post->post_password !== '') {
        return;
    }
    $post_id = $post->ID;
    add_action('shutdown', function () use ($post_id) {
        if (!riff_indexnow_is_noindex($post_id)) {
            riff_indexnow_submit(get_permalink($post_id));
        }
    });
}, 10, 3);
