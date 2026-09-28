/**
 * ヘッダーカスタマイザーのリアルタイムプレビュー
 */
(function($) {
    'use strict';

    // カスタマイザーでの変更をリアルタイムで反映
    wp.customize('riff_header_link_hover_color', function(value) {
        value.bind(function(to) {
            // CSS変数を動的に更新
            updateCSS('--riff-header-link-hover-color', to);
            
            // アンダーラインの色も更新
            $('<style id="riff-header-hover-preview">' +
                '.main-navigation a:hover, ' +
                '.main-navigation a:focus, ' +
                '.main-navigation .current-menu-item > a, ' +
                '.main-navigation .current_page_item > a { ' +
                'color: ' + to + ' !important; } ' +
                '.main-navigation a::after, ' +
                '.main-navigation .current-menu-item > a::after, ' +
                '.main-navigation .current_page_item > a::after { ' +
                'background-color: ' + to + ' !important; }' +
                '</style>').appendTo('head');
                
            $('#riff-header-hover-preview').remove();
        });
    });

    // その他のヘッダー関連設定も同様にリアルタイム更新
    wp.customize('riff_header_text_color', function(value) {
        value.bind(function(to) {
            updateCSS('--riff-header-text-color', to);
            $('.main-navigation a').css('color', to);
        });
    });

    wp.customize('riff_header_bg_color', function(value) {
        value.bind(function(to) {
            updateCSS('--riff-header-bg-color', to);
            $('.site-header').css('background-color', to);
        });
    });

    // CSS変数を更新するヘルパー関数
    function updateCSS(property, value) {
        var root = document.documentElement;
        root.style.setProperty(property, value);
    }

})(jQuery);