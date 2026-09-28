/**
 * メディアアップローダー機能
 * ロゴ画像と背景画像/動画の選択用
 */
jQuery(document).ready(function($) {
    
    // ロゴアップローダー
    if ($('#upload_logo_button').length) {
        $('#upload_logo_button').click(function(e) {
            e.preventDefault();
            
            var image = wp.media({
                title: 'ロゴ画像を選択',
                multiple: false,
                library: {
                    type: 'image'
                }
            }).open().on('select', function(e) {
                var uploaded_image = image.state().get('selection').first();
                var image_data = uploaded_image.toJSON();
                var image_url = image_data.url;
                
                $('#logo_url').val(image_url);
                
                // プレビューを更新
                $('.logo-preview').remove();
                $('<div class="logo-preview" style="margin-top: 10px;"><img src="' + image_url + '" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 10px;" /></div>').insertAfter('#upload_logo_button');
            });
        });
    }
    
    // 背景画像アップローダー
    if ($('#upload_hero_image_button').length) {
        $('#upload_hero_image_button').click(function(e) {
            e.preventDefault();
            
            var image = wp.media({
                title: 'ヒーロー背景画像を選択',
                multiple: false,
                library: {
                    type: 'image'
                }
            }).open().on('select', function(e) {
                var uploaded_image = image.state().get('selection').first();
                var image_data = uploaded_image.toJSON();
                var image_url = image_data.url;
                
                $('input[name="hero_bg_image"]').val(image_url);
                
                // プレビューを更新
                $('.hero-image-preview').remove();
                $('<div class="hero-image-preview" style="margin-top: 10px;"><img src="' + image_url + '" style="max-width: 300px; height: auto; border: 1px solid #ddd;" /></div>').insertAfter('#upload_hero_image_button');
            });
        });
    }
    
    // 動画アップローダー
    if ($('#upload_hero_video_button').length) {
        $('#upload_hero_video_button').click(function(e) {
            e.preventDefault();
            
            var video = wp.media({
                title: 'ヒーロー背景動画を選択',
                multiple: false,
                library: {
                    type: 'video'
                }
            }).open().on('select', function(e) {
                var uploaded_video = video.state().get('selection').first();
                var video_data = uploaded_video.toJSON();
                var video_url = video_data.url;
                
                $('input[name="hero_bg_video"]').val(video_url);
                
                // プレビューを更新
                $('.hero-video-preview').remove();
                $('<div class="hero-video-preview" style="margin-top: 10px;"><video src="' + video_url + '" style="max-width: 300px; height: auto; border: 1px solid #ddd;" controls></video></div>').insertAfter('#upload_hero_video_button');
            });
        });
    }
    
});