/**
 * 実績詳細ページ設定メタボックス用：画像・動画アップロードJS
 * data-target属性で対象のhidden input（同名のプレビューdivは "{target}_preview"）と紐付ける、
 * 汎用的な複数フィールド対応スクリプト。data-media-type="video" が指定されたボタンは
 * メディアライブラリの選択を動画のみに絞り込み、プレビューも<video>タグで表示する。
 */
jQuery(document).ready(function ($) {
    'use strict';

    var frames = {};

    $('.riff-pf-upload-btn').on('click', function (e) {
        e.preventDefault();
        var target = $(this).data('target');
        var mediaType = $(this).data('media-type') || 'image';
        var $button = $(this);

        if (frames[target]) {
            frames[target].open();
            return;
        }

        var frame = wp.media({
            title: mediaType === 'video' ? '動画を選択' : '画像を選択',
            button: { text: mediaType === 'video' ? 'この動画を使用' : 'この画像を使用' },
            multiple: false,
            library: { type: mediaType === 'video' ? 'video' : 'image' }
        });

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#' + target).val(attachment.id);

            if (mediaType === 'video') {
                $('#' + target + '_preview').html(
                    '<video src="' + attachment.url + '" style="max-width:280px;max-height:200px;border:1px solid #ddd;display:block;" controls></video>'
                );
            } else {
                var previewUrl = (attachment.sizes && attachment.sizes.medium) ? attachment.sizes.medium.url : attachment.url;
                $('#' + target + '_preview').html(
                    '<img src="' + previewUrl + '" style="max-width:280px;height:auto;border:1px solid #ddd;display:block;" />'
                );
            }
            $button.siblings('.riff-pf-remove-btn').show();
        });

        frames[target] = frame;
        frame.open();
    });

    $('.riff-pf-remove-btn').on('click', function (e) {
        e.preventDefault();
        var target = $(this).data('target');
        $('#' + target).val('');
        $('#' + target + '_preview').empty();
        $(this).hide();
    });
});
