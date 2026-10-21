<?php
/**
 * 著者のプロフィール画像
 *
 * WordPress標準のアバターは外部サービス（Gravatar）から取得するため、
 * メディアにアップロードした画像を著者画像として使えるようにする。
 * 記事下の「記事執筆者」欄や、AIOSEOの構造化データ（Person）の画像に反映される。
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('pre_get_avatar_data', function ($args, $id_or_email) {
    $user_id = 0;
    if (is_numeric($id_or_email)) {
        $user_id = (int) $id_or_email;
    } elseif ($id_or_email instanceof WP_User) {
        $user_id = $id_or_email->ID;
    } elseif ($id_or_email instanceof WP_Post) {
        $user_id = (int) $id_or_email->post_author;
    } elseif ($id_or_email instanceof WP_Comment) {
        $user_id = (int) $id_or_email->user_id;
    } elseif (is_string($id_or_email) && is_email($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
        $user_id = $user ? $user->ID : 0;
    }

    $url = $user_id ? get_user_meta($user_id, 'riff_avatar_url', true) : '';
    if ($url) {
        $args['url'] = $url;
        $args['found_avatar'] = true;
    }
    return $args;
}, 10, 2);

// ユーザーのプロフィール画面に画像URLの入力欄を追加する
function riff_author_avatar_field($user) {
    $url = get_user_meta($user->ID, 'riff_avatar_url', true);
    ?>
    <h2>プロフィール画像（記事執筆者）</h2>
    <table class="form-table" role="presentation">
        <tr>
            <th><label for="riff_avatar_url">画像URL</label></th>
            <td>
                <?php if ($url): ?>
                    <img src="<?php echo esc_url($url); ?>" alt="" style="width:80px; height:80px; border-radius:50%; object-fit:cover; display:block; margin-bottom:8px;">
                <?php endif; ?>
                <input type="url" name="riff_avatar_url" id="riff_avatar_url" value="<?php echo esc_attr($url); ?>" class="regular-text">
                <p class="description">メディアにアップロードした正方形の画像のURLを入力してください。記事下の「記事執筆者」欄に表示されます。空欄にするとGravatarの画像に戻ります。</p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'riff_author_avatar_field');
add_action('edit_user_profile', 'riff_author_avatar_field');

function riff_author_avatar_save($user_id) {
    if (!current_user_can('edit_user', $user_id) || !isset($_POST['riff_avatar_url'])) {
        return;
    }
    update_user_meta($user_id, 'riff_avatar_url', esc_url_raw(wp_unslash($_POST['riff_avatar_url'])));
}
add_action('personal_options_update', 'riff_author_avatar_save');
add_action('edit_user_profile_update', 'riff_author_avatar_save');
