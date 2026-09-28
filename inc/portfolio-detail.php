<?php
/**
 * 実績詳細ページ機能
 * 固定ページ編集画面にメタボックスを追加し、各項目を入力・画像アップロードできるようにする。
 * 表示は [riff_portfolio_detail] ショートコードで行い、既存の「固定ページ＋ショートコード」の
 * 構成はそのまま維持する。未入力・未アップロードの項目はセクションごと出力しない。
 *
 * @package RiffTheme
 */

// ================================
// メタボックス登録
// ================================
function riff_pf_add_meta_box() {
    add_meta_box(
        'riff_portfolio_detail',
        '実績詳細ページ設定',
        'riff_pf_meta_box_callback',
        'page',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'riff_pf_add_meta_box');

function riff_pf_image_field($field_key, $label) {
    $attachment_id = get_post_meta(get_the_ID(), $field_key, true);
    $preview_url   = $attachment_id ? wp_get_attachment_image_url($attachment_id, 'medium') : '';
    ?>
    <p>
        <label><strong><?php echo esc_html($label); ?></strong></label><br>
        <input type="hidden" id="<?php echo esc_attr($field_key); ?>" name="<?php echo esc_attr($field_key); ?>" value="<?php echo esc_attr($attachment_id); ?>" />
        <button type="button" class="button riff-pf-upload-btn" data-target="<?php echo esc_attr($field_key); ?>">画像を選択</button>
        <button type="button" class="button riff-pf-remove-btn" data-target="<?php echo esc_attr($field_key); ?>" style="<?php echo $attachment_id ? '' : 'display:none;'; ?>">削除</button>
        <div id="<?php echo esc_attr($field_key); ?>_preview" class="riff-pf-preview" style="margin-top:8px;">
            <?php if ($preview_url): ?>
                <img src="<?php echo esc_url($preview_url); ?>" style="max-width:280px;height:auto;border:1px solid #ddd;display:block;" />
            <?php endif; ?>
        </div>
    </p>
    <?php
}

function riff_pf_video_field($field_key, $label) {
    $attachment_id = get_post_meta(get_the_ID(), $field_key, true);
    $preview_url   = $attachment_id ? wp_get_attachment_url($attachment_id) : '';
    ?>
    <p>
        <label><strong><?php echo esc_html($label); ?></strong></label><br>
        <input type="hidden" id="<?php echo esc_attr($field_key); ?>" name="<?php echo esc_attr($field_key); ?>" value="<?php echo esc_attr($attachment_id); ?>" />
        <button type="button" class="button riff-pf-upload-btn" data-target="<?php echo esc_attr($field_key); ?>" data-media-type="video">動画を選択</button>
        <button type="button" class="button riff-pf-remove-btn" data-target="<?php echo esc_attr($field_key); ?>" style="<?php echo $attachment_id ? '' : 'display:none;'; ?>">削除</button>
        <div id="<?php echo esc_attr($field_key); ?>_preview" class="riff-pf-preview" style="margin-top:8px;">
            <?php if ($preview_url): ?>
                <video src="<?php echo esc_url($preview_url); ?>" style="max-width:280px;max-height:200px;border:1px solid #ddd;display:block;" controls></video>
            <?php endif; ?>
        </div>
    </p>
    <?php
}

function riff_pf_meta_box_callback($post) {
    wp_nonce_field('riff_pf_meta_box', 'riff_pf_nonce');

    $url         = get_post_meta($post->ID, 'riff_pf_url', true);
    $business    = get_post_meta($post->ID, 'riff_pf_business', true);
    $purpose     = get_post_meta($post->ID, 'riff_pf_purpose', true);
    $scope       = get_post_meta($post->ID, 'riff_pf_scope', true);
    $testimonial = get_post_meta($post->ID, 'riff_pf_testimonial', true);
    $prev_id     = get_post_meta($post->ID, 'riff_pf_prev_id', true);
    $next_id     = get_post_meta($post->ID, 'riff_pf_next_id', true);
    ?>
    <style>
        .riff-pf-box p { margin: 16px 0; }
        .riff-pf-box textarea, .riff-pf-box input[type="url"] { width: 100%; }
        .riff-pf-box hr { margin: 24px 0; border: none; border-top: 1px solid #ddd; }
        .riff-pf-box h4 { margin: 0 0 4px; }
        .riff-pf-box .riff-pf-hint { color: #666; font-size: 12px; margin: 2px 0 0; }
    </style>
    <div class="riff-pf-box">
        <p>
            <label><strong>サイトURL</strong></label><br>
            <input type="url" name="riff_pf_url" value="<?php echo esc_attr($url); ?>" placeholder="https://example.com/" />
            <span class="riff-pf-hint">空欄の場合「サイトを見る」ボタンは表示されません。</span>
        </p>

        <?php riff_pf_image_field('riff_pf_fv_pc_image', 'FVモックアップ画像（PC）'); ?>
        <p class="riff-pf-hint">推奨サイズ：1440×900に収まるサイズ。空欄の場合、この画像は表示されません。</p>
        <?php riff_pf_image_field('riff_pf_fv_mobile_image', 'FVモックアップ画像（スマホ）'); ?>
        <p class="riff-pf-hint">推奨サイズ：393×852に収まるサイズ。空欄の場合、この画像は表示されません。両方空欄の場合はブロックごと非表示になります。</p>

        <?php riff_pf_video_field('riff_pf_fv_video', '紹介動画（PC・スマホ画像の下に表示）'); ?>
        <p class="riff-pf-hint">空欄の場合、動画は表示されません。</p>

        <hr>

        <p>
            <label><strong>事業内容</strong></label><br>
            <textarea name="riff_pf_business" rows="3"><?php echo esc_textarea($business); ?></textarea>
            <span class="riff-pf-hint">空欄の場合「事業内容」セクションは表示されません。</span>
        </p>

        <p>
            <label><strong>サイトリニューアルの目的</strong></label><br>
            <textarea name="riff_pf_purpose" rows="3"><?php echo esc_textarea($purpose); ?></textarea>
            <span class="riff-pf-hint">空欄の場合、このセクションは表示されません。</span>
        </p>

        <p>
            <label><strong>制作内容（1行に1項目）</strong></label><br>
            <textarea name="riff_pf_scope" rows="4" placeholder="企画構成・ディレクション&#10;アートディレクション・デザイン&#10;Webサイト制作（フロントエンド）"><?php echo esc_textarea($scope); ?></textarea>
            <span class="riff-pf-hint">空欄の場合「制作内容」セクションは表示されません。</span>
        </p>

        <hr>
        <h4>制作のポイント（最大4項目・タイトルと本文がどちらも空の項目は表示されません）</h4>
        <?php for ($i = 1; $i <= 4; $i++):
            $ptitle = get_post_meta($post->ID, "riff_pf_point{$i}_title", true);
            $ptext  = get_post_meta($post->ID, "riff_pf_point{$i}_text", true);
        ?>
        <p>
            <label>ポイント<?php echo $i; ?> 見出し</label><br>
            <input type="text" name="riff_pf_point<?php echo $i; ?>_title" value="<?php echo esc_attr($ptitle); ?>" style="width:100%;" /><br>
            <label>ポイント<?php echo $i; ?> 本文</label><br>
            <textarea name="riff_pf_point<?php echo $i; ?>_text" rows="2" style="width:100%;"><?php echo esc_textarea($ptext); ?></textarea>
        </p>
        <?php endfor; ?>

        <hr>

        <p>
            <label><strong>お客様の声</strong></label><br>
            <textarea name="riff_pf_testimonial" rows="4"><?php echo esc_textarea($testimonial); ?></textarea>
            <span class="riff-pf-hint">空欄の場合「お客様の声」セクションは表示されません。</span>
        </p>

        <hr>

        <?php riff_pf_image_field('riff_pf_pc_shot', 'PC表示 一覧画像'); ?>
        <p class="riff-pf-hint">空欄の場合、この画像は表示されません。</p>
        <?php riff_pf_image_field('riff_pf_mobile_shot', 'スマホ表示 一覧画像'); ?>
        <p class="riff-pf-hint">空欄の場合、この画像は表示されません。</p>

        <hr>

        <p>
            <label><strong>前の実績</strong></label><br>
            <?php
            wp_dropdown_pages(array(
                'name'              => 'riff_pf_prev_id',
                'selected'          => $prev_id,
                'show_option_none'  => '（選択なし）',
                'option_none_value' => '0',
            ));
            ?>
        </p>
        <p>
            <label><strong>次の実績</strong></label><br>
            <?php
            wp_dropdown_pages(array(
                'name'              => 'riff_pf_next_id',
                'selected'          => $next_id,
                'show_option_none'  => '（選択なし）',
                'option_none_value' => '0',
            ));
            ?>
        </p>
    </div>
    <?php
}

// ================================
// 保存処理
// ================================
function riff_pf_save_meta_box($post_id) {
    if (!isset($_POST['riff_pf_nonce'])) return;
    if (!wp_verify_nonce($_POST['riff_pf_nonce'], 'riff_pf_meta_box')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['riff_pf_url'])) {
        update_post_meta($post_id, 'riff_pf_url', esc_url_raw($_POST['riff_pf_url']));
    }

    foreach (array('riff_pf_fv_pc_image', 'riff_pf_fv_mobile_image', 'riff_pf_fv_video', 'riff_pf_pc_shot', 'riff_pf_mobile_shot') as $image_field) {
        if (isset($_POST[$image_field])) {
            $attachment_id = intval($_POST[$image_field]);
            if ($attachment_id > 0) {
                update_post_meta($post_id, $image_field, $attachment_id);
            } else {
                delete_post_meta($post_id, $image_field);
            }
        }
    }

    foreach (array('riff_pf_business', 'riff_pf_purpose', 'riff_pf_scope', 'riff_pf_testimonial') as $textarea_field) {
        if (isset($_POST[$textarea_field])) {
            update_post_meta($post_id, $textarea_field, sanitize_textarea_field($_POST[$textarea_field]));
        }
    }

    for ($i = 1; $i <= 4; $i++) {
        if (isset($_POST["riff_pf_point{$i}_title"])) {
            update_post_meta($post_id, "riff_pf_point{$i}_title", sanitize_text_field($_POST["riff_pf_point{$i}_title"]));
        }
        if (isset($_POST["riff_pf_point{$i}_text"])) {
            update_post_meta($post_id, "riff_pf_point{$i}_text", sanitize_textarea_field($_POST["riff_pf_point{$i}_text"]));
        }
    }

    foreach (array('riff_pf_prev_id', 'riff_pf_next_id') as $nav_field) {
        if (isset($_POST[$nav_field])) {
            $page_id = intval($_POST[$nav_field]);
            if ($page_id > 0) {
                update_post_meta($post_id, $nav_field, $page_id);
            } else {
                delete_post_meta($post_id, $nav_field);
            }
        }
    }
}
add_action('save_post', 'riff_pf_save_meta_box');

// ================================
// 管理画面アセット
// ================================
function riff_pf_admin_assets($hook) {
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script(
        'riff-pf-admin',
        get_template_directory_uri() . '/assets/js/portfolio-detail-admin.js',
        array('jquery'),
        '1.0.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'riff_pf_admin_assets');

// ================================
// 表示（ショートコード）
// ================================
function riff_pf_scope_items($scope_raw) {
    $lines = preg_split('/\r\n|\r|\n/', trim($scope_raw));
    return array_values(array_filter(array_map('trim', $lines)));
}

function riff_pf_nav_card($page_id, $direction) {
    if (!$page_id) {
        return '';
    }
    $page = get_post($page_id);
    if (!$page || $page->post_status !== 'publish') {
        return '';
    }
    $thumb_id  = get_post_meta($page_id, 'riff_pf_fv_pc_image', true);
    $thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'medium') : '';
    $label     = ($direction === 'prev') ? '前の実績' : '次の実績';
    $arrow     = ($direction === 'prev') ? '←' : '→';

    ob_start();
    ?>
    <a href="<?php echo esc_url(get_permalink($page_id)); ?>" class="riff-pfd-navcard riff-pfd-navcard--<?php echo esc_attr($direction); ?>">
        <?php if ($direction === 'prev'): ?><span class="riff-pfd-navcard-arrow"><?php echo $arrow; ?></span><?php endif; ?>
        <?php if ($thumb_url): ?>
        <span class="riff-pfd-navcard-thumb"><img src="<?php echo esc_url($thumb_url); ?>" alt="" loading="lazy"></span>
        <?php endif; ?>
        <span class="riff-pfd-navcard-body">
            <span class="riff-pfd-navcard-label"><?php echo esc_html($label); ?></span>
            <span class="riff-pfd-navcard-title"><?php echo esc_html(get_the_title($page_id)); ?></span>
        </span>
        <?php if ($direction === 'next'): ?><span class="riff-pfd-navcard-arrow"><?php echo $arrow; ?></span><?php endif; ?>
    </a>
    <?php
    return ob_get_clean();
}

function riff_pf_render_detail() {
    $post_id = get_the_ID();

    $url         = get_post_meta($post_id, 'riff_pf_url', true);
    $fv_pc_id    = get_post_meta($post_id, 'riff_pf_fv_pc_image', true);
    $fv_mobile_id = get_post_meta($post_id, 'riff_pf_fv_mobile_image', true);
    $fv_video_id = get_post_meta($post_id, 'riff_pf_fv_video', true);
    $business    = get_post_meta($post_id, 'riff_pf_business', true);
    $purpose     = get_post_meta($post_id, 'riff_pf_purpose', true);
    $scope       = riff_pf_scope_items(get_post_meta($post_id, 'riff_pf_scope', true));
    $testimonial = get_post_meta($post_id, 'riff_pf_testimonial', true);
    $pc_shot_id  = get_post_meta($post_id, 'riff_pf_pc_shot', true);
    $mobile_shot_id = get_post_meta($post_id, 'riff_pf_mobile_shot', true);
    $prev_id     = get_post_meta($post_id, 'riff_pf_prev_id', true);
    $next_id     = get_post_meta($post_id, 'riff_pf_next_id', true);

    $points = array();
    for ($i = 1; $i <= 4; $i++) {
        $ptitle = get_post_meta($post_id, "riff_pf_point{$i}_title", true);
        $ptext  = get_post_meta($post_id, "riff_pf_point{$i}_text", true);
        if ($ptitle !== '' || $ptext !== '') {
            $points[] = array('title' => $ptitle, 'text' => $ptext);
        }
    }

    $fv_pc_url     = $fv_pc_id ? wp_get_attachment_image_url($fv_pc_id, 'full') : '';
    $fv_mobile_url = $fv_mobile_id ? wp_get_attachment_image_url($fv_mobile_id, 'full') : '';
    $has_fv        = ($fv_pc_url || $fv_mobile_url);
    $fv_video_url  = $fv_video_id ? wp_get_attachment_url($fv_video_id) : '';
    $pc_shot_url   = $pc_shot_id ? wp_get_attachment_image_url($pc_shot_id, 'full') : '';
    $mobile_shot_url = $mobile_shot_id ? wp_get_attachment_image_url($mobile_shot_id, 'full') : '';
    $has_pc_or_mobile = ($pc_shot_url || $mobile_shot_url);

    ob_start();
    ?>
    <style>
    .riff-pfd{max-width:960px;margin:0 auto;padding:20px 20px 0;}
    .riff-pfd-title{font-size:clamp(1.6rem,4vw,2.2rem);font-weight:700;color:#1a1a1a;margin:0 0 14px;line-height:1.4;}
    .riff-pfd-url-block{margin:0 0 36px;}
    .riff-pfd-url-text{font-size:.85rem;color:#777;margin:0 0 12px;word-break:break-all;}
    .riff-pfd-url-btn{display:inline-flex;align-items:center;gap:10px;padding:15px 30px;background:#007cba;color:#fff;font-weight:700;font-size:.98rem;text-decoration:none;border-radius:0;transition:opacity .2s ease;}
    .riff-pfd-url-btn:hover{opacity:.8;color:#fff;}
    .riff-pfd-url-btn-arrow{transition:transform .2s ease;}
    .riff-pfd-url-btn:hover .riff-pfd-url-btn-arrow{transform:translateX(4px);}
    .riff-pfd-fv{margin:0 0 24px;padding:0;border-radius:0;background:none;display:flex;align-items:flex-start;justify-content:flex-start;gap:24px;width:100%;}
    .riff-pfd-fv-pc{flex:1 1 auto;min-width:0;}
    .riff-pfd-fv-mobile{flex:0 0 200px;width:200px;}
    .riff-pfd-fv img{width:100%;height:auto;display:block;object-fit:contain;border:1px solid #dbe3ea;box-shadow:0 16px 32px rgba(20,40,70,.14);}
    .riff-pfd-fv-video{margin:0 0 56px;}
    .riff-pfd-fv-video video{width:100%;max-width:100%;height:auto;display:block;border:1px solid #dbe3ea;box-shadow:0 16px 32px rgba(20,40,70,.14);background:#000;}
    .riff-pfd-section{margin:0 0 48px;}
    .riff-pfd-section-title{font-size:1.3rem;font-weight:700;color:#1a1a1a;margin:0 0 18px;padding-bottom:10px;border-bottom:3px solid #007cba;display:inline-block;}
    .riff-pfd-text{font-size:.98rem;line-height:1.9;color:#3a3a3a;margin:0;white-space:pre-line;}
    .riff-pfd-scope{list-style:none;margin:0;padding:0;display:grid;gap:10px;}
    .riff-pfd-scope li{position:relative;padding-left:26px;font-size:.96rem;color:#3a3a3a;line-height:1.7;}
    .riff-pfd-scope li::before{content:"";position:absolute;left:0;top:8px;width:12px;height:12px;border-radius:3px;background:#007cba;}
    .riff-pfd-points{display:grid;gap:22px;}
    .riff-pfd-point{display:flex;gap:18px;padding:22px;background:#f7f9fb;border-radius:12px;}
    .riff-pfd-point-icon{flex:none;width:36px;height:36px;border-radius:50%;background:#007cba;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;}
    .riff-pfd-point-title{font-size:1.02rem;font-weight:700;color:#1a1a1a;margin:0 0 6px;}
    .riff-pfd-point-text{font-size:.92rem;color:#555;line-height:1.75;margin:0;}
    .riff-pfd-testimonial{position:relative;margin:0 0 48px;padding:32px 32px 32px 56px;background:#f7f9fb;border:1px solid #e2e8ee;border-left:4px solid #007cba;border-radius:10px;}
    .riff-pfd-testimonial::before{content:"\201C";position:absolute;top:8px;left:18px;font-size:3rem;line-height:1;color:#c9d8e4;font-family:Georgia,serif;}
    .riff-pfd-testimonial-title{font-size:.85rem;font-weight:700;letter-spacing:.08em;color:#007cba;text-transform:uppercase;margin:0 0 10px;}
    .riff-pfd-testimonial-text{font-size:.96rem;line-height:1.9;color:#3a3a3a;margin:0;white-space:pre-line;}
    .riff-pfd-shots{display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;}
    .riff-pfd-shots.riff-pfd-shots--single{grid-template-columns:1fr;}
    .riff-pfd-shot-label{font-size:.85rem;font-weight:700;color:#555;margin:0 0 10px;text-align:center;}
    .riff-pfd-shot-frame{border:1px solid #e2e8ee;border-radius:8px;overflow:hidden;background:#f7f9fb;max-height:640px;overflow-y:auto;}
    .riff-pfd-shot-frame img{width:100%;height:auto;display:block;}
    .riff-pfd-cta{margin:56px 0 48px;}
    .riff-pfd-cta-title{text-align:center;font-size:1.1rem;font-weight:700;color:#1a1a1a;margin:0 0 22px;}
    .riff-pfd-cta-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;}
    .riff-pfd-cta-card{display:block;padding:40px 28px;background:transparent;border:2px solid #1a1a1a;border-radius:0;text-decoration:none;text-align:center;transition:opacity .2s ease;}
    .riff-pfd-cta-card:hover{opacity:.65;}
    .riff-pfd-cta-card-title{display:flex;align-items:center;justify-content:center;gap:10px;font-size:1.1rem;font-weight:700;color:#1a1a1a;margin:0 0 16px;}
    .riff-pfd-cta-card-desc{font-size:.85rem;color:#444;margin:0;line-height:1.85;}
    .riff-pfd-navigation{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin:0 0 24px;}
    .riff-pfd-navcard{display:flex;align-items:center;gap:12px;padding:14px;border:1px solid #e2e8ee;border-radius:10px;text-decoration:none;color:inherit;transition:box-shadow .2s ease;}
    .riff-pfd-navcard:hover{box-shadow:0 6px 16px rgba(0,0,0,.08);}
    .riff-pfd-navcard--next{justify-content:flex-end;text-align:right;}
    .riff-pfd-navcard-thumb{flex:none;width:56px;height:56px;border-radius:6px;overflow:hidden;background:#eef2f5;}
    .riff-pfd-navcard-thumb img{width:100%;height:100%;object-fit:cover;display:block;}
    .riff-pfd-navcard-body{min-width:0;}
    .riff-pfd-navcard-label{display:block;font-size:.72rem;color:#999;}
    .riff-pfd-navcard-title{display:block;font-size:.88rem;font-weight:600;color:#1a1a1a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .riff-pfd-navcard-arrow{flex:none;font-size:1.2rem;color:#007cba;}
    .riff-pfd-back{display:block;text-align:center;padding:16px;background:#1a1a1a;color:#fff;text-decoration:none;border-radius:8px;font-weight:700;font-size:.95rem;margin-bottom:60px;transition:opacity .2s ease;}
    .riff-pfd-back:hover{opacity:.85;color:#fff;}
    @media (max-width:768px){
        .riff-pfd{padding:16px 16px 0;}
        .riff-pfd-fv{margin-bottom:40px;flex-direction:column;align-items:center;}
        .riff-pfd-fv-pc{width:100%;flex:0 0 auto;}
        .riff-pfd-fv-mobile{width:55%;max-width:220px;}
        .riff-pfd-section{margin-bottom:36px;}
        .riff-pfd-shots{grid-template-columns:1fr;}
        .riff-pfd-cta-grid{grid-template-columns:1fr;}
        .riff-pfd-navigation{grid-template-columns:1fr;}
        .riff-pfd-navcard--next{justify-content:flex-start;text-align:left;}
        .riff-pfd-point{flex-direction:column;}
    }
    </style>
    <div class="riff-pfd">
        <h1 class="riff-pfd-title"><?php echo esc_html(get_the_title()); ?></h1>

        <?php if ($url): ?>
        <div class="riff-pfd-url-block">
            <p class="riff-pfd-url-text"><?php echo esc_html($url); ?></p>
            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" class="riff-pfd-url-btn">
                実際のページを見てみる
                <span class="riff-pfd-url-btn-arrow">→</span>
            </a>
        </div>
        <?php endif; ?>

        <?php if ($has_fv): ?>
        <div class="riff-pfd-fv">
            <?php if ($fv_pc_url): ?>
            <div class="riff-pfd-fv-pc">
                <img src="<?php echo esc_url($fv_pc_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>（PC表示）">
            </div>
            <?php endif; ?>
            <?php if ($fv_mobile_url): ?>
            <div class="riff-pfd-fv-mobile">
                <img src="<?php echo esc_url($fv_mobile_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>（スマホ表示）">
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($fv_video_url): ?>
        <div class="riff-pfd-fv-video">
            <video src="<?php echo esc_url($fv_video_url); ?>" controls playsinline></video>
        </div>
        <?php endif; ?>

        <?php if ($business): ?>
        <section class="riff-pfd-section">
            <h2 class="riff-pfd-section-title">事業内容</h2>
            <p class="riff-pfd-text"><?php echo esc_html($business); ?></p>
        </section>
        <?php endif; ?>

        <?php if ($purpose): ?>
        <section class="riff-pfd-section">
            <h2 class="riff-pfd-section-title">サイトリニューアルの目的</h2>
            <p class="riff-pfd-text"><?php echo esc_html($purpose); ?></p>
        </section>
        <?php endif; ?>

        <?php if (!empty($scope)): ?>
        <section class="riff-pfd-section">
            <h2 class="riff-pfd-section-title">制作内容</h2>
            <ul class="riff-pfd-scope">
                <?php foreach ($scope as $item): ?>
                <li><?php echo esc_html($item); ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php endif; ?>

        <?php if (!empty($points)): ?>
        <section class="riff-pfd-section">
            <h2 class="riff-pfd-section-title">制作のポイント</h2>
            <div class="riff-pfd-points">
                <?php foreach ($points as $i => $point): ?>
                <div class="riff-pfd-point">
                    <div class="riff-pfd-point-icon">✓</div>
                    <div>
                        <?php if ($point['title']): ?><p class="riff-pfd-point-title"><?php echo esc_html($point['title']); ?></p><?php endif; ?>
                        <?php if ($point['text']): ?><p class="riff-pfd-point-text"><?php echo esc_html($point['text']); ?></p><?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <?php if ($testimonial): ?>
        <div class="riff-pfd-testimonial">
            <p class="riff-pfd-testimonial-title">お客様の声</p>
            <p class="riff-pfd-testimonial-text"><?php echo esc_html($testimonial); ?></p>
        </div>
        <?php endif; ?>

        <?php if ($has_pc_or_mobile): ?>
        <section class="riff-pfd-section">
            <div class="riff-pfd-shots <?php echo ($pc_shot_url && $mobile_shot_url) ? '' : 'riff-pfd-shots--single'; ?>">
                <?php if ($pc_shot_url): ?>
                <div>
                    <p class="riff-pfd-shot-label">PC表示</p>
                    <div class="riff-pfd-shot-frame"><img src="<?php echo esc_url($pc_shot_url); ?>" alt="PC表示" loading="lazy"></div>
                </div>
                <?php endif; ?>
                <?php if ($mobile_shot_url): ?>
                <div>
                    <p class="riff-pfd-shot-label">スマホ表示</p>
                    <div class="riff-pfd-shot-frame"><img src="<?php echo esc_url($mobile_shot_url); ?>" alt="スマホ表示" loading="lazy"></div>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        <?php $line_url = get_option('riff_footer_line_url', '') ?: home_url('/contact/'); ?>
        <div class="riff-pfd-cta">
            <p class="riff-pfd-cta-title">ホームページ制作に関するご相談はこちらから</p>
            <div class="riff-pfd-cta-grid">
                <a href="<?php echo esc_url($line_url); ?>" target="_blank" rel="noopener noreferrer" class="riff-pfd-cta-card">
                    <p class="riff-pfd-cta-card-title">LINEで無料相談<span>→</span></p>
                    <p class="riff-pfd-cta-card-desc">公式LINEから気軽にご相談いただけます。ご質問やお見積りのご相談も、お気軽にメッセージをお送りください。</p>
                </a>
                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="riff-pfd-cta-card">
                    <p class="riff-pfd-cta-card-title">ホームページ無料診断<span>→</span></p>
                    <p class="riff-pfd-cta-card-desc">目標達成のためには、現状把握が不可欠です。リニューアル前に把握すべき5つのポイントを分析し、結果の出るホームページへの改善案をご提案します。</p>
                </a>
            </div>
        </div>

        <?php
        $prev_card = riff_pf_nav_card($prev_id, 'prev');
        $next_card = riff_pf_nav_card($next_id, 'next');
        if ($prev_card || $next_card):
        ?>
        <div class="riff-pfd-navigation">
            <?php echo $prev_card ? $prev_card : '<span></span>'; ?>
            <?php echo $next_card ? $next_card : '<span></span>'; ?>
        </div>
        <?php endif; ?>

        <a href="<?php echo esc_url(home_url('/portfolio/')); ?>" class="riff-pfd-back">制作実績一覧に戻る</a>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('riff_portfolio_detail', 'riff_pf_render_detail');
