<?php
/**
 * RIFF テーマ管理画面 - 統合タブナビゲーション + SEO設定ページ
 */

// ================================
// タブ定義
// ================================

function riff_get_admin_tabs() {
    return array(
        'riff-theme-settings'           => array( 'label' => '基本設定',       'group' => 'site' ),
        'riff-header-settings'          => array( 'label' => 'ヘッダー設定',   'group' => 'header' ),
        'riff-hero-settings'            => array( 'label' => 'ヒーロー設定',   'group' => 'content' ),
        'riff-front-sections'           => array( 'label' => 'フロントページ', 'group' => 'content' ),
        'riff-section-management'       => array( 'label' => 'セクション設定', 'group' => 'content' ),
        'riff-footer-up-settings'       => array( 'label' => 'CTA設定',        'group' => 'content' ),
        'riff-side-buttons-settings'    => array( 'label' => 'サイド追従ボタン', 'group' => 'content' ),
        'riff-footer-settings'          => array( 'label' => 'フッター設定',   'group' => 'footer' ),
        'riff-seo-settings'             => array( 'label' => 'SEO設定',        'group' => 'seo' ),
        'riff-faq-settings'             => array( 'label' => 'FAQ設定',        'group' => 'content' ),
        'riff-instagram-settings'       => array( 'label' => 'Instagram設定',  'group' => 'content' ),
        'riff-genbox-cta-settings'      => array( 'label' => 'GENBOX導線',     'group' => 'content' ),
        'riff-archive-hero-settings'    => array( 'label' => 'アーカイブ',     'group' => 'content' ),
        'riff-fade-animations-settings' => array( 'label' => 'アニメーション', 'group' => 'design' ),
        'riff-custom-css-settings'      => array( 'label' => 'カスタムCSS',   'group' => 'design' ),
    );
}

// ================================
// 管理画面タブナビゲーション出力
// ================================

function riff_admin_tab_nav() {
    if ( ! isset( $_GET['page'] ) ) {
        return;
    }
    $current = sanitize_key( $_GET['page'] );
    $tabs    = riff_get_admin_tabs();
    if ( ! array_key_exists( $current, $tabs ) ) {
        return;
    }
    ?>
    <div class="riff-tab-nav-wrap" id="riff-tab-nav-wrap">
        <div class="riff-tab-nav-header">
            <span class="riff-tab-nav-logo">RIFF</span>
            <span class="riff-tab-nav-title">テーマ設定</span>
        </div>
        <div class="riff-tab-list">
            <?php foreach ( $tabs as $slug => $info ) : ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $slug ) ); ?>"
               class="riff-tab riff-tab-group-<?php echo esc_attr( $info['group'] ); ?><?php echo ( $current === $slug ) ? ' riff-tab-active' : ''; ?>">
                <?php echo esc_html( $info['label'] ); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
    // タブナビを .wrap の先頭に移動し、重複 h1 を非表示にする
    document.addEventListener('DOMContentLoaded', function () {
        var nav  = document.getElementById('riff-tab-nav-wrap');
        var wrap = document.querySelector('#wpbody-content > .wrap');
        if ( nav && wrap ) {
            wrap.insertBefore( nav, wrap.firstChild );
            var h1 = wrap.querySelector('h1');
            if ( h1 ) { h1.style.display = 'none'; }
        }
    });
    </script>
    <?php
}
add_action( 'admin_notices', 'riff_admin_tab_nav' );

// body クラスに is-riff-admin を追加（CSS ターゲット用）
function riff_admin_body_class( $classes ) {
    if ( ! isset( $_GET['page'] ) ) {
        return $classes;
    }
    if ( array_key_exists( sanitize_key( $_GET['page'] ), riff_get_admin_tabs() ) ) {
        $classes .= ' is-riff-admin';
    }
    return $classes;
}
add_filter( 'admin_body_class', 'riff_admin_body_class' );

// ================================
// SEO設定ページ コンテンツ
// ================================

function riff_seo_settings_page_content() {

    // 保存処理
    if ( isset( $_POST['submit'] ) ) {
        if ( ! isset( $_POST['riff_seo_nonce'] ) || ! wp_verify_nonce( $_POST['riff_seo_nonce'], 'riff_seo_settings' ) ) {
            wp_die( 'セキュリティチェックに失敗しました。' );
        }
        update_option( 'riff_site_description',  sanitize_textarea_field( $_POST['site_description'] ) );
        update_option( 'riff_ogp_image',         esc_url_raw( $_POST['ogp_image'] ) );
        update_option( 'riff_ogp_image_alt',     sanitize_text_field( $_POST['ogp_image_alt'] ) );
        update_option( 'riff_twitter_card_type', sanitize_text_field( $_POST['twitter_card_type'] ) );
        echo '<div class="notice notice-success is-dismissible"><p>SEO設定を保存しました。</p></div>';
    }

    // 値の取得
    $site_description  = get_option( 'riff_site_description', get_bloginfo( 'description' ) );
    $ogp_image         = get_option( 'riff_ogp_image', '' );
    $ogp_image_alt     = get_option( 'riff_ogp_image_alt', get_option( 'riff_site_title', get_bloginfo( 'name' ) ) );
    $twitter_card_type = get_option( 'riff_twitter_card_type', 'summary_large_image' );
    ?>
    <div class="wrap">
        <h1>SEO設定</h1>

        <form method="post" action="">
            <?php wp_nonce_field( 'riff_seo_settings', 'riff_seo_nonce' ); ?>

            <!-- 基本SEO -->
            <h2 class="riff-section-heading">基本SEO設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        サイト説明文<br>
                        <small style="font-weight:normal;color:#666;">(meta description)</small>
                    </th>
                    <td>
                        <textarea name="site_description" rows="4" class="large-text"><?php echo esc_textarea( $site_description ); ?></textarea>
                        <p class="description">Google・SNSシェア時に表示されるサイトの説明文です。<strong>120〜160文字</strong>を目安に入力してください。</p>
                        <p class="description" id="riff-desc-count" style="color:#0073aa;"></p>
                    </td>
                </tr>
            </table>

            <!-- OGP画像 -->
            <h2 class="riff-section-heading">OGP画像設定（SNSシェア画像）</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        デフォルトOGP画像<br>
                        <small style="font-weight:normal;color:#666;">推奨サイズ: 1200×630px</small>
                    </th>
                    <td>
                        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                            <input type="url" name="ogp_image" id="riff_ogp_image"
                                   value="<?php echo esc_attr( $ogp_image ); ?>"
                                   class="regular-text" placeholder="https://..." />
                            <button type="button" class="button" id="riff_upload_ogp_btn">画像を選択</button>
                            <?php if ( $ogp_image ) : ?>
                            <button type="button" class="button" id="riff_remove_ogp_btn">削除</button>
                            <?php endif; ?>
                        </div>
                        <p class="description">Facebook・LINE・X（Twitter）等でシェアされた際に表示される画像です。個別投稿にアイキャッチ画像がある場合はそちらが優先されます。</p>
                        <div id="riff-ogp-preview" style="margin-top:12px;">
                            <?php if ( $ogp_image ) : ?>
                            <img src="<?php echo esc_url( $ogp_image ); ?>"
                                 style="max-width:400px;max-height:210px;border:1px solid #ddd;padding:6px;border-radius:4px;display:block;" />
                            <p style="margin:4px 0 0;font-size:12px;color:#888;">現在のOGP画像</p>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">OGP画像の代替テキスト</th>
                    <td>
                        <input type="text" name="ogp_image_alt"
                               value="<?php echo esc_attr( $ogp_image_alt ); ?>" class="regular-text" />
                        <p class="description">スクリーンリーダー向けの画像説明テキストです。</p>
                    </td>
                </tr>
            </table>

            <!-- Twitter Card -->
            <h2 class="riff-section-heading">X（Twitter）Card設定</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Cardタイプ</th>
                    <td>
                        <label style="display:flex;align-items:flex-start;gap:8px;margin-bottom:10px;">
                            <input type="radio" name="twitter_card_type" value="summary_large_image"
                                   <?php checked( $twitter_card_type, 'summary_large_image' ); ?> style="margin-top:3px;">
                            <span>
                                <strong>summary_large_image</strong>（推奨）<br>
                                <small style="color:#666;">大きな画像つきカードで表示されます</small>
                            </span>
                        </label>
                        <label style="display:flex;align-items:flex-start;gap:8px;">
                            <input type="radio" name="twitter_card_type" value="summary"
                                   <?php checked( $twitter_card_type, 'summary' ); ?> style="margin-top:3px;">
                            <span>
                                <strong>summary</strong><br>
                                <small style="color:#666;">小さなサムネイルつきカードで表示されます</small>
                            </span>
                        </label>
                    </td>
                </tr>
            </table>

            <div style="background:#e8f4fd;border-left:4px solid #2271b1;padding:14px 16px;border-radius:0 4px 4px 0;margin:20px 0;">
                <strong>💡 OGP設定のヒント</strong><br>
                <ul style="margin:8px 0 0 20px;color:#444;">
                    <li>個別の投稿・固定ページには「アイキャッチ画像」が自動的にOGP画像として使用されます</li>
                    <li>アイキャッチ未設定のページではここで設定したデフォルトOGP画像が使われます</li>
                    <li>画像変更後は SNS の OGP キャッシュを削除してから確認してください</li>
                </ul>
            </div>

            <?php submit_button( '設定を保存する' ); ?>
        </form>
    </div>

    <script>
    jQuery( document ).ready( function ( $ ) {

        // 文字数カウンター
        var $desc  = $( 'textarea[name="site_description"]' );
        var $count = $( '#riff-desc-count' );
        function updateCount() {
            var len = $desc.val().length;
            var color = len < 80 ? '#e57373' : ( len <= 160 ? '#2e7d32' : '#e57373' );
            $count.text( len + ' 文字' + ( len < 80 ? '（短すぎます）' : ( len > 160 ? '（長すぎます）' : '（適切）' ) ) ).css( 'color', color );
        }
        $desc.on( 'input', updateCount );
        updateCount();

        // メディアアップローダー（OGP画像）
        $( '#riff_upload_ogp_btn' ).on( 'click', function ( e ) {
            e.preventDefault();
            var uploader = wp.media( {
                title: 'OGP画像を選択',
                button: { text: 'この画像を使用' },
                multiple: false
            } );
            uploader.on( 'select', function () {
                var att = uploader.state().get( 'selection' ).first().toJSON();
                $( '#riff_ogp_image' ).val( att.url );
                $( '#riff-ogp-preview' ).html(
                    '<img src="' + att.url + '" style="max-width:400px;max-height:210px;border:1px solid #ddd;padding:6px;border-radius:4px;display:block;">' +
                    '<p style="margin:4px 0 0;font-size:12px;color:#888;">現在のOGP画像</p>'
                );
            } );
            uploader.open();
        } );

        // OGP画像削除
        $( '#riff_remove_ogp_btn' ).on( 'click', function () {
            $( '#riff_ogp_image' ).val( '' );
            $( '#riff-ogp-preview' ).empty();
            $( this ).remove();
        } );
    } );
    </script>
    <?php
}
