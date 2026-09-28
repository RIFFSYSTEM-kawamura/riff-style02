jQuery(document).ready(function($) {
    
    // メディアアップローダーの初期化
    var heroMediaUploader;
    
    // 画像選択ボタンクリック時の処理
    $('#hero_upload_image_button').click(function(e) {
        e.preventDefault();
        
        // メディアアップローダーが既に作成されている場合は開く
        if (heroMediaUploader) {
            heroMediaUploader.open();
            return;
        }
        
        // 新しいメディアアップローダーを作成
        heroMediaUploader = wp.media({
            title: 'ヒーロー背景画像を選択',
            button: {
                text: '画像を選択'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        // 画像が選択された時の処理
        heroMediaUploader.on('select', function() {
            var attachment = heroMediaUploader.state().get('selection').first().toJSON();
            
            // 隠しフィールドに画像IDを設定
            $('#hero_background_image').val(attachment.id);
            
            // プレビュー画像を表示
            var imageUrl = attachment.sizes && attachment.sizes.medium ? 
                          attachment.sizes.medium.url : attachment.url;
            var previewHtml = '<img src="' + imageUrl + '" style="max-width: 100%; height: auto; margin-top: 10px; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" />';
            $('#hero_image_preview').html(previewHtml);
            
            // ボタンテキストを変更
            $('#hero_upload_image_button').text('画像を変更');
            
            // 削除ボタンを表示
            $('#hero_remove_image_button').show();
        });
        
        // メディアアップローダーを開く
        heroMediaUploader.open();
    });
    
    // 画像削除ボタンクリック時の処理
    $('#hero_remove_image_button').click(function(e) {
        e.preventDefault();
        
        // 確認ダイアログ
        if (confirm('背景画像を削除しますか？')) {
            // 隠しフィールドをクリア
            $('#hero_background_image').val('');
            
            // プレビューをクリア
            $('#hero_image_preview').html('');
            
            // ボタンテキストを元に戻す
            $('#hero_upload_image_button').text('画像を選択');
            
            // 削除ボタンを非表示
            $('#hero_remove_image_button').hide();
        }
    });
    
    // 管理画面用CSSスタイルを追加
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            #hero_image_preview {
                border: 2px dashed #ddd;
                padding: 20px;
                text-align: center;
                margin-top: 10px;
                min-height: 40px;
                background-color: #fafafa;
            }
            
            #hero_image_preview img {
                border-radius: 4px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
            
            #hero_upload_image_button {
                margin-right: 10px;
            }
            
            #hero_remove_image_button {
                color: #d63638;
                border-color: #d63638;
            }
            
            #hero_remove_image_button:hover {
                background-color: #d63638;
                color: white;
                border-color: #d63638;
            }
            
            .form-table th {
                width: 150px;
                vertical-align: top;
                padding-top: 8px;
            }
            
            .form-table .description {
                font-style: italic;
                color: #666;
                margin-top: 5px;
                font-size: 13px;
            }
        `)
        .appendTo('head');
});