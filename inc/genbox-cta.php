<?php
/**
 * GENBOX導線機能
 * 「建築業の基礎知識」「業務効率化」カテゴリの記事に、
 * GENBOXへ誘導するバナーを表示する。
 * ショートコード [genbox_cta] で本文中の好きな位置に挿入可能。
 * 挿入されていない場合は記事末尾に自動で表示する。
 *
 * @package RiffTheme
 */

// このCTAを表示する対象カテゴリのterm_id
// 11: 建築業の基礎知識 / 8: 業務効率化
define('RIFF_GENBOX_CTA_CATEGORY_IDS', array(11, 8));

// ================================
// バナー描画
// ================================
function riff_genbox_cta_render() {
    if (!get_option('riff_genbox_cta_enabled', 1)) {
        return '';
    }

    $title       = get_option('riff_genbox_cta_title', '建築業のDXなら「GENBOX」');
    $description = get_option('riff_genbox_cta_description', '見積作成から工程管理まで、建築業の日々の業務をまるごと効率化。まずは資料でGENBOXの機能をご確認ください。');
    $button_text = get_option('riff_genbox_cta_button_text', 'GENBOXの詳細を見る');
    $url         = get_option('riff_genbox_cta_url', 'https://genbox-lp.riff-system.com/');

    if (empty($url)) {
        return '';
    }

    ob_start();
    ?>
    <style>
    .riff-genbox-cta{display:flex;align-items:center;gap:24px;flex-wrap:wrap;margin:40px 0;padding:28px 32px;border-radius:14px;background:linear-gradient(135deg,#0f2942,#1c4c72);color:#fff;}
    .riff-genbox-cta-body{flex:1;min-width:240px;}
    .riff-genbox-cta-eyebrow{display:inline-block;font-size:.75rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#7dd3fc;margin:0 0 8px;}
    .riff-genbox-cta-title{font-size:1.25rem;font-weight:700;margin:0 0 8px;line-height:1.4;}
    .riff-genbox-cta-desc{font-size:.92rem;line-height:1.7;margin:0;color:rgba(255,255,255,.85);}
    .riff-genbox-cta-btn{flex:none;display:inline-flex;align-items:center;gap:8px;padding:14px 28px;border-radius:999px;background:#fff;color:#0f2942;font-weight:700;font-size:.92rem;text-decoration:none;white-space:nowrap;transition:opacity .2s ease;}
    .riff-genbox-cta-btn:hover{opacity:.85;color:#0f2942;}
    @media (max-width:600px){.riff-genbox-cta{padding:22px 20px;}.riff-genbox-cta-btn{width:100%;justify-content:center;}}
    </style>
    <div class="riff-genbox-cta">
        <div class="riff-genbox-cta-body">
            <span class="riff-genbox-cta-eyebrow">PR</span>
            <p class="riff-genbox-cta-title"><?php echo esc_html($title); ?></p>
            <p class="riff-genbox-cta-desc"><?php echo esc_html($description); ?></p>
        </div>
        <a href="<?php echo esc_url($url); ?>" class="riff-genbox-cta-btn" target="_blank" rel="noopener noreferrer">
            <?php echo esc_html($button_text); ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg>
        </a>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('genbox_cta', 'riff_genbox_cta_render');

// ================================
// 対象カテゴリの記事で、ショートコードが未挿入の場合は末尾に自動追加
// ================================
function riff_genbox_cta_auto_append($content) {
    if (!is_singular('post') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (!get_option('riff_genbox_cta_enabled', 1)) {
        return $content;
    }

    if (has_shortcode($content, 'genbox_cta')) {
        return $content; // 本文中に手動で挿入済み
    }

    if (!has_category(RIFF_GENBOX_CTA_CATEGORY_IDS, get_the_ID())) {
        return $content;
    }

    return $content . do_shortcode('[genbox_cta]');
}
add_filter('the_content', 'riff_genbox_cta_auto_append', 20);

// ================================
// 管理画面
// ================================
function riff_genbox_cta_admin_page() {
    add_submenu_page(
        'riff-theme-settings',
        'GENBOX導線設定',
        'GENBOX導線',
        'manage_options',
        'riff-genbox-cta-settings',
        'riff_genbox_cta_admin_page_content'
    );
}
add_action('admin_menu', 'riff_genbox_cta_admin_page');

function riff_genbox_cta_admin_page_content() {
    if (isset($_POST['submit'])) {
        if (!isset($_POST['riff_genbox_cta_nonce']) || !wp_verify_nonce($_POST['riff_genbox_cta_nonce'], 'riff_genbox_cta_admin_page')) {
            wp_die('セキュリティチェックに失敗しました。');
        }

        update_option('riff_genbox_cta_enabled', isset($_POST['genbox_cta_enabled']) ? 1 : 0);
        update_option('riff_genbox_cta_title', sanitize_text_field($_POST['genbox_cta_title']));
        update_option('riff_genbox_cta_description', sanitize_textarea_field($_POST['genbox_cta_description']));
        update_option('riff_genbox_cta_button_text', sanitize_text_field($_POST['genbox_cta_button_text']));
        update_option('riff_genbox_cta_url', esc_url_raw($_POST['genbox_cta_url']));

        echo '<div class="notice notice-success"><p>GENBOX導線設定を保存しました。</p></div>';
    }

    $enabled     = get_option('riff_genbox_cta_enabled', 1);
    $title       = get_option('riff_genbox_cta_title', '建築業のDXなら「GENBOX」');
    $description = get_option('riff_genbox_cta_description', '見積作成から工程管理まで、建築業の日々の業務をまるごと効率化。まずは資料でGENBOXの機能をご確認ください。');
    $button_text = get_option('riff_genbox_cta_button_text', 'GENBOXの詳細を見る');
    $url         = get_option('riff_genbox_cta_url', 'https://genbox-lp.riff-system.com/');

    $target_names = array();
    foreach (RIFF_GENBOX_CTA_CATEGORY_IDS as $cat_id) {
        $term = get_term($cat_id, 'category');
        if ($term && !is_wp_error($term)) {
            $target_names[] = $term->name;
        }
    }
    ?>
    <div class="wrap">
        <h1>GENBOX導線設定</h1>

        <div class="riff-admin-header">
            <h1>GENBOX導線設定</h1>
            <p>「<?php echo esc_html(implode('」「', $target_names)); ?>」カテゴリの記事に表示される、GENBOXへの誘導バナーの文言・リンク先を設定できます。</p>
        </div>

        <form method="post" action="">
            <?php wp_nonce_field('riff_genbox_cta_admin_page', 'riff_genbox_cta_nonce'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">表示</th>
                    <td>
                        <label>
                            <input type="checkbox" name="genbox_cta_enabled" value="1" <?php checked($enabled, 1); ?> />
                            対象カテゴリの記事にGENBOXバナーを表示する
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">タイトル</th>
                    <td><input type="text" name="genbox_cta_title" value="<?php echo esc_attr($title); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">説明文</th>
                    <td><textarea name="genbox_cta_description" rows="3" class="large-text"><?php echo esc_textarea($description); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">ボタン文言</th>
                    <td><input type="text" name="genbox_cta_button_text" value="<?php echo esc_attr($button_text); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">リンクURL</th>
                    <td><input type="url" name="genbox_cta_url" value="<?php echo esc_attr($url); ?>" class="regular-text" /></td>
                </tr>
            </table>

            <div style="background:#e8f4fd;border-left:4px solid #2271b1;padding:14px 16px;border-radius:0 4px 4px 0;margin:20px 0;">
                <strong>💡 記事本文中の好きな位置に挿入したい場合</strong><br>
                <p style="margin:8px 0 0;">記事編集画面の本文中に <code>[genbox_cta]</code> と入力すると、その位置にバナーが表示されます（中盤に入れたい場合など）。挿入し忘れた場合は、対象カテゴリの記事であれば自動的に本文末尾に表示されます。</p>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
