/**
 * RIFF Theme Admin Scripts
 * 管理画面用JavaScript
 */

jQuery(document).ready(function($) {
    
    // ================================
    // 管理画面の初期化
    // ================================
    
    // 管理画面にクラスを追加
    $('.wrap').addClass('riff-admin-page');
    
    // ================================
    // ヒーロー設定の表示/非表示制御
    // ================================
    function toggleHeroSettings() {
        const displayType = $('#hero_display_type').val();
        const imageRow = $('.hero-image-row');
        const videoRow = $('.hero-video-row');
        
        // すべて非表示
        imageRow.hide();
        videoRow.hide();
        
        // 選択されたタイプに応じて表示
        switch(displayType) {
            case 'image':
                imageRow.show();
                break;
            case 'video':
                videoRow.show();
                break;
            case 'gradient':
                // グラデーションの場合は何も表示しない
                break;
        }
    }
    
    // 初期表示
    if ($('#hero_display_type').length) {
        toggleHeroSettings();
        $('#hero_display_type').on('change', toggleHeroSettings);
    }
    
    // ================================
    // オーバーレイ透明度のリアルタイム更新
    // ================================
    $('input[name="hero_overlay_opacity"]').on('input', function() {
        const value = $(this).val();
        $('#opacity-value').text(value + '%');
        
        // プレビューがあれば更新
        $('.hero-content-preview').css({
            'background': `linear-gradient(rgba(0,0,0,${value/100}), rgba(0,0,0,${value/100})), linear-gradient(135deg, #007cba, #0096d7)`
        });
    });
    
	// ================================
// サービス透明度スライダーのリアルタイム更新
// ================================
$('input[name="service1_opacity"], input[name="service2_opacity"], input[name="service3_opacity"]').on('input', function() {
    const value = $(this).val();
    const displayElement = $(this).siblings('.opacity-display');
    displayElement.text(value + '%');
    
    // プレビュー更新（オプション）
    const serviceIndex = $(this).attr('name').match(/service(\d+)_opacity/)[1];
    const colorInput = $(`input[name="service${serviceIndex}_color"]`);
    const color = colorInput.val();
    
    if (color) {
        // リアルタイムプレビューを作成
        const previewId = `service${serviceIndex}-preview`;
        let preview = $(`#${previewId}`);
        
        if (preview.length === 0) {
            preview = $(`<div id="${previewId}" class="service-opacity-preview" style="margin-top: 10px; padding: 20px; border-radius: 6px; color: white; text-align: center; font-weight: bold;">プレビュー</div>`);
            $(this).closest('td').append(preview);
        }
        
        // 16進数をRGBAに変換
        function hexToRgba(hex, alpha) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }
        
        const alpha = value / 100;
        preview.css('background', hexToRgba(color, alpha));
    }
});

// カラーピッカーの変更もプレビューに反映
$('input[name="service1_color"], input[name="service2_color"], input[name="service3_color"]').on('change', function() {
    const serviceIndex = $(this).attr('name').match(/service(\d+)_color/)[1];
    const opacityInput = $(`input[name="service${serviceIndex}_opacity"]`);
    opacityInput.trigger('input');
});
	
    // ================================
    // メディアアップローダー機能
    // ================================
    
    // ロゴアップローダー
    $(document).on('click', '#upload_logo_button', function(e) {
        e.preventDefault();
        openMediaUploader('logo', 'image', $(this));
    });
    
    // ヒーロー画像アップローダー
    $(document).on('click', '#upload_hero_image_button', function(e) {
        e.preventDefault();
        openMediaUploader('hero_image', 'image', $(this));
    });
    
    // ヒーロー動画アップローダー
    $(document).on('click', '#upload_hero_video_button', function(e) {
        e.preventDefault();
        openMediaUploader('hero_video', 'video', $(this));
    });
    
    function openMediaUploader(type, mediaType, button) {
        const frame = wp.media({
            title: getMediaTitle(type),
            multiple: false,
            library: {
                type: mediaType
            }
        });
        
        frame.on('select', function() {
            const attachment = frame.state().get('selection').first().toJSON();
            const url = attachment.url;
            const inputField = getInputField(type);
            
            // URLを設定
            inputField.val(url);
            
            // プレビューを更新
            updateMediaPreview(type, url, attachment);
        });
        
        frame.open();
    }
    
    function getMediaTitle(type) {
        const titles = {
            'logo': 'ロゴ画像を選択',
            'hero_image': 'ヒーロー背景画像を選択',
            'hero_video': 'ヒーロー背景動画を選択'
        };
        return titles[type] || 'メディアを選択';
    }
    
    function getInputField(type) {
        const fields = {
            'logo': $('#logo_url'),
            'hero_image': $('input[name="hero_bg_image"]'),
            'hero_video': $('input[name="hero_bg_video"]')
        };
        return fields[type];
    }
    
    function updateMediaPreview(type, url, attachment) {
        const previewClass = type.replace('_', '-') + '-preview';
        
        // 既存のプレビューを削除
        $(`.${previewClass}`).remove();
        
        let previewHTML = '';
        
        if (type === 'logo' || type === 'hero_image') {
            previewHTML = `
                <div class="${previewClass}">
                    <img src="${url}" style="max-width: 300px; height: auto; border: 1px solid #ddd; border-radius: 4px;" alt="プレビュー" />
                    <p style="margin-top: 10px; font-size: 12px; color: #666;">
                        サイズ: ${attachment.width} × ${attachment.height}px | 
                        ファイルサイズ: ${formatFileSize(attachment.filesizeInBytes)}
                    </p>
                </div>
            `;
        } else if (type === 'hero_video') {
            previewHTML = `
                <div class="${previewClass}">
                    <video src="${url}" style="max-width: 300px; height: auto; border: 1px solid #ddd; border-radius: 4px;" controls></video>
                    <p style="margin-top: 10px; font-size: 12px; color: #666;">
                        ファイルサイズ: ${formatFileSize(attachment.filesizeInBytes)}
                    </p>
                </div>
            `;
        }
        
        // 対応するボタンの後に挿入
        $(`#upload_${type}_button`).after(previewHTML);
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // ================================
    // フォームバリデーション
    // ================================
    $('.riff-admin-page form').on('submit', function(e) {
        let hasError = false;
        
        // 必須フィールドのチェック
        $(this).find('input[required], textarea[required], select[required]').each(function() {
            const $field = $(this);
            const value = $field.val().trim();
            
            if (!value) {
                showFieldError($field, 'この項目は必須です。');
                hasError = true;
            } else {
                clearFieldError($field);
            }
        });
        
        // URLフィールドのバリデーション
        $(this).find('input[type="url"]').each(function() {
            const $field = $(this);
            const value = $field.val().trim();
            
            if (value && !isValidURL(value)) {
                showFieldError($field, '正しいURLを入力してください。');
                hasError = true;
            }
        });
        
        // エラーがある場合は送信を停止
        if (hasError) {
            e.preventDefault();
            
            // 最初のエラーフィールドにスクロール
            const firstError = $(this).find('.error').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
                firstError.focus();
            }
            
            // エラーメッセージを表示
            showNotice('入力内容に問題があります。エラーを修正してください。', 'error');
        }
    });
    
    function showFieldError($field, message) {
        clearFieldError($field);
        $field.addClass('error');
        $field.after(`<div class="field-error" style="color: #dc3545; font-size: 13px; margin-top: 5px;">${message}</div>`);
    }
    
    function clearFieldError($field) {
        $field.removeClass('error');
        $field.siblings('.field-error').remove();
    }
    
    function isValidURL(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }
    
    function showNotice(message, type = 'info') {
        const noticeClass = `notice-${type}`;
        const noticeHTML = `
            <div class="notice ${noticeClass} is-dismissible">
                <p>${message}</p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">この通知を閉じる</span>
                </button>
            </div>
        `;
        
        $('.riff-admin-page h1').after(noticeHTML);
        
        // 自動で消す
        setTimeout(() => {
            $('.notice').fadeOut(500, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // 通知の閉じるボタン
    $(document).on('click', '.notice-dismiss', function() {
        $(this).closest('.notice').fadeOut(300, function() {
            $(this).remove();
        });
    });
    
    // ================================
    // リアルタイムバリデーション
    // ================================
    $('.riff-admin-page input[type="url"]').on('blur', function() {
        const $field = $(this);
        const value = $field.val().trim();
        
        if (value && !isValidURL(value)) {
            showFieldError($field, '正しいURLを入力してください。');
        } else {
            clearFieldError($field);
        }
    });
    
    $('.riff-admin-page input[type="email"]').on('blur', function() {
        const $field = $(this);
        const value = $field.val().trim();
        
        if (value && !isValidEmail(value)) {
            showFieldError($field, '正しいメールアドレスを入力してください。');
        } else {
            clearFieldError($field);
        }
    });
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // ================================
    // タブ機能（将来の拡張用）
    // ================================
    $('.riff-admin-tabs a').on('click', function(e) {
        e.preventDefault();
        
        const $this = $(this);
        const targetTab = $this.attr('href');
        
        // アクティブ状態を切り替え
        $('.riff-admin-tabs a').removeClass('active');
        $this.addClass('active');
        
        // タブコンテンツを切り替え
        $('.tab-content').hide();
        $(targetTab).show();
    });
    
    // ================================
    // キーボードショートカット
    // ================================
    $(document).on('keydown', function(e) {
        // Ctrl+S で保存
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            $('.riff-admin-page form .button-primary').click();
        }
    });
    
    // ================================
    // ユーティリティ関数
    // ================================
    
    // デバウンス関数
    function debounce(func, wait, immediate) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                timeout = null;
                if (!immediate) func(...args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func(...args);
        };
    }
    
    // ローカルストレージへの設定保存（オートセーブ機能）
    function saveFormData() {
        const formData = {};
        $('.riff-admin-page form input, .riff-admin-page form textarea, .riff-admin-page form select').each(function() {
            const $field = $(this);
            const name = $field.attr('name');
            if (name && $field.attr('type') !== 'submit') {
                formData[name] = $field.val();
            }
        });
        
        localStorage.setItem('riff_admin_form_data', JSON.stringify(formData));
    }
    
    // ローカルストレージからの設定復元
    function loadFormData() {
        const savedData = localStorage.getItem('riff_admin_form_data');
        if (savedData) {
            try {
                const formData = JSON.parse(savedData);
                Object.keys(formData).forEach(name => {
                    const $field = $(`[name="${name}"]`);
                    if ($field.length && !$field.val()) {
                        $field.val(formData[name]);
                    }
                });
            } catch (e) {
                console.warn('Failed to load saved form data:', e);
            }
        }
    }
    
    // オートセーブ機能
    $('.riff-admin-page form input, .riff-admin-page form textarea, .riff-admin-page form select').on('input change', debounce(saveFormData, 2000));
    
    // ページ読み込み時にデータを復元
    // loadFormData(); // 必要に応じてコメントアウトを外す
    
    // フォーム送信成功時にローカルストレージをクリア
    $('.riff-admin-page form').on('submit', function() {
        setTimeout(() => {
            if ($('.notice-success').length) {
                localStorage.removeItem('riff_admin_form_data');
            }
        }, 1000);
    });
    
    // ================================
    // 管理画面の追加機能
    // ================================
    
    // ツールチップ機能
    $('[data-tooltip]').each(function() {
        const $element = $(this);
        const tooltipText = $element.data('tooltip');
        
        $element.on('mouseenter', function() {
            const tooltip = $(`<div class="admin-tooltip">${tooltipText}</div>`);
            $('body').append(tooltip);
            
            const offset = $element.offset();
            tooltip.css({
                position: 'absolute',
                top: offset.top - tooltip.outerHeight() - 5,
                left: offset.left + ($element.outerWidth() / 2) - (tooltip.outerWidth() / 2),
                background: 'rgba(0,0,0,0.8)',
                color: '#fff',
                padding: '8px 12px',
                borderRadius: '4px',
                fontSize: '12px',
                zIndex: 10000,
                whiteSpace: 'nowrap'
            });
        });
        
        $element.on('mouseleave', function() {
            $('.admin-tooltip').remove();
        });
    });
    
    // ================================
    // 初期化完了
    // ================================
    console.log('%cRIFF Theme Admin Loaded', 'color: #007cba; font-weight: bold;');
    
});

// ================================
    // メガメニュー管理機能
    // ================================
    
    // メガメニュー有効/無効の切り替え
    $('input[name="megamenu_enabled"]').on('change', function() {
        const isEnabled = $(this).is(':checked');
        $('.megamenu-dependent').toggle(isEnabled);
        
        if (isEnabled) {
            showNotice('メガメニュー機能を有効にしました。メニュー管理画面でメガメニュータイプを設定してください。', 'info');
        }
    });
    
    // 初期表示時の状態設定
    const megamenuEnabled = $('input[name="megamenu_enabled"]').is(':checked');
    $('.megamenu-dependent').toggle(megamenuEnabled);
    
    // メガメニュープレビュー機能
    function updateMegamenuPreview() {
        const postCount = $('input[name="megamenu_post_count"]').val() || 6;
        const serviceCount = $('input[name="megamenu_service_count"]').val() || 3;
        
        // プレビューエリアの更新
        $('.megamenu-preview').remove();
        
        if (megamenuEnabled) {
            const previewHTML = `
                <div class="megamenu-preview" style="margin-top: 20px; padding: 20px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 6px;">
                    <h4 style="margin: 0 0 15px 0; color: #007cba;">メガメニュープレビュー</h4>
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1; padding: 15px; background: white; border-radius: 4px;">
                            <strong>ニュースタイプ</strong><br>
                            <small>表示記事数: ${postCount}件</small>
                        </div>
                        <div style="flex: 1; padding: 15px; background: white; border-radius: 4px;">
                            <strong>サービスタイプ</strong><br>
                            <small>表示サービス数: ${serviceCount}件</small>
                        </div>
                        <div style="flex: 1; padding: 15px; background: white; border-radius: 4px;">
                            <strong>ページタイプ</strong><br>
                            <small>主要ページを表示</small>
                        </div>
                    </div>
                </div>
            `;
            $('.form-table').after(previewHTML);
        }
    }
    
    // プレビュー更新のイベントリスナー
    $('input[name="megamenu_post_count"], input[name="megamenu_service_count"]').on('input change', debounce(updateMegamenuPreview, 500));
    
    // 初期プレビュー表示
    if ($('input[name="megamenu_enabled"]').length) {
        updateMegamenuPreview();
    }
    
    // メニュー管理画面でのメガメニュー設定表示制御
    if (window.location.href.indexOf('nav-menus.php') !== -1) {
        // メニュー管理ページの場合
        $(document).ready(function() {
            // メガメニュー設定の表示/非表示制御
            function toggleMegamenuFields() {
                const megamenuEnabled = window.riffMegamenuEnabled || false;
                $('.field-megamenu-type').toggle(megamenuEnabled);
            }
            
            // ページ読み込み時に実行
            setTimeout(toggleMegamenuFields, 500);
            
            // メニューアイテム展開時にも実行
            $(document).on('click', '.item-edit', function() {
                setTimeout(toggleMegamenuFields, 100);
            });
        });
        
        // メガメニュータイプ変更時の説明表示
        $(document).on('change', '.edit-menu-item-megamenu-type', function() {
            const selectedType = $(this).val();
            const description = $(this).siblings('.description');
            
            let descriptionText = 'このメニューアイテムのメガメニュータイプを選択してください。';
            
            switch(selectedType) {
                case 'news':
                    descriptionText = '最新の投稿記事を一覧表示するメガメニューです。';
                    break;
                case 'services':
                    descriptionText = 'サービス内容をアイコン付きで表示するメガメニューです。';
                    break;
                case 'pages':
                    descriptionText = '主要なページを画像付きで表示するメガメニューです。';
                    break;
            }
            
            description.text(descriptionText);
        });
    }
    
    // メガメニュー設定のエクスポート/インポート機能
    function exportMegamenuSettings() {
        const settings = {
            enabled: $('input[name="megamenu_enabled"]').is(':checked'),
            postCount: $('input[name="megamenu_post_count"]').val(),
            serviceCount: $('input[name="megamenu_service_count"]').val()
        };
        
        const dataStr = JSON.stringify(settings, null, 2);
        const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
        
        const exportFileDefaultName = 'riff-megamenu-settings.json';
        
        const linkElement = document.createElement('a');
        linkElement.setAttribute('href', dataUri);
        linkElement.setAttribute('download', exportFileDefaultName);
        linkElement.click();
    }
    
    function importMegamenuSettings(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const settings = JSON.parse(e.target.result);
                
                if (settings.enabled !== undefined) {
                    $('input[name="megamenu_enabled"]').prop('checked', settings.enabled);
                }
                if (settings.postCount) {
                    $('input[name="megamenu_post_count"]').val(settings.postCount);
                }
                if (settings.serviceCount) {
                    $('input[name="megamenu_service_count"]').val(settings.serviceCount);
                }
                
                updateMegamenuPreview();
                showNotice('メガメニュー設定をインポートしました。', 'success');
            } catch (error) {
                showNotice('設定ファイルの形式が正しくありません。', 'error');
            }
        };
        reader.readAsText(file);
    }
    
    // エクスポート/インポートボタンの追加（必要に応じて）
    if ($('.riff-admin-page').length && $('input[name="megamenu_enabled"]').length) {
        const toolsHTML = `
            <div class="megamenu-tools" style="margin-top: 20px; padding: 15px; background: #f0f8ff; border-radius: 6px;">
                <h4 style="margin: 0 0 15px 0;">メガメニュー設定ツール</h4>
                <button type="button" class="button" id="export-megamenu-settings">設定をエクスポート</button>
                <input type="file" id="import-megamenu-settings" accept=".json" style="display: none;">
                <button type="button" class="button" id="import-megamenu-settings-btn">設定をインポート</button>
            </div>
        `;
        $('.form-table').after(toolsHTML);
        
        $('#export-megamenu-settings').on('click', exportMegamenuSettings);
        $('#import-megamenu-settings-btn').on('click', function() {
            $('#import-megamenu-settings').click();
        });
        $('#import-megamenu-settings').on('change', function(e) {
            if (e.target.files.length > 0) {
                importMegamenuSettings(e.target.files[0]);
            }
        });
    }
    
    // リアルタイムバリデーション（メガメニュー設定）
    $('input[name="megamenu_post_count"]').on('input', function() {
        const value = parseInt($(this).val());
        if (value < 3 || value > 12) {
            showFieldError($(this), '記事数は3から12の間で設定してください。');
        } else {
            clearFieldError($(this));
        }
    });
    
    $('input[name="megamenu_service_count"]').on('input', function() {
        const value = parseInt($(this).val());
        if (value < 2 || value > 6) {
            showFieldError($(this), 'サービス数は2から6の間で設定してください。');
        } else {
            clearFieldError($(this));
        }
    });
    
    // メガメニュー設定の保存確認
    $('.riff-admin-page form').on('submit', function(e) {
        const megamenuEnabled = $('input[name="megamenu_enabled"]').is(':checked');
        
        if (megamenuEnabled) {
            const postCount = parseInt($('input[name="megamenu_post_count"]').val());
            const serviceCount = parseInt($('input[name="megamenu_service_count"]').val());
            
            if (postCount < 3 || postCount > 12 || serviceCount < 2 || serviceCount > 6) {
                e.preventDefault();
                showNotice('メガメニューの設定値を正しい範囲で入力してください。', 'error');
                return false;
            }
        }
    });
    
    console.log('%cMegamenu Admin Functions Loaded', 'color: #28a745; font-weight: bold;');

// ================================
// セクション管理機能
// ================================

jQuery(document).ready(function($) {
    
    // セクション管理ページのみで実行
    if ($('#sortable-sections').length) {
        
        // jQuery UI Sortableを初期化
        $("#sortable-sections").sortable({
            handle: ".drag-handle",
            placeholder: "section-placeholder",
            cursor: "grabbing",
            opacity: 0.8,
            tolerance: "pointer",
            containment: "parent",
            axis: "y",
            start: function(event, ui) {
                ui.item.addClass('ui-sortable-helper-custom');
                ui.placeholder.html('<div class="placeholder-content">ここにドロップ</div>');
            },
            stop: function(event, ui) {
                ui.item.removeClass('ui-sortable-helper-custom');
                updateSectionOrder();
                markFormChanged();
            },
            update: function(event, ui) {
                updateSectionOrder();
                updateSectionPreview();
            }
        });
        
        // ソート可能にしたことを示すスタイルを追加
        $("#sortable-sections").addClass('sortable-enabled');
        
        // セクションの順序を更新
        function updateSectionOrder() {
            $("#sortable-sections .section-item").each(function(index) {
                var sectionId = $(this).data('id');
                var orderInput = $(this).find('.section-order-input');
                orderInput.val(index + 1);
                
                // ビジュアルフィードバック
                $(this).find('.section-order .section-order-input').trigger('change');
            });
        }
        
        // プレビューを更新
        function updateSectionPreview() {
            var previewList = $('.preview-list');
            previewList.empty();
            
            var order = 1;
            $("#sortable-sections .section-item").each(function() {
                var $checkbox = $(this).find('input[type="checkbox"]');
                if ($checkbox.is(':checked') || $checkbox.prop('disabled')) {
                    var sectionName = $(this).find('.section-details h4').text().trim();
                    var sectionDesc = $(this).find('.section-details p').text().trim();
                    
                    var previewItem = $(`
                        <div style="display: flex; align-items: center; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                            <span style="width: 30px; height: 30px; background: #007cba; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; margin-right: 15px;">
                                ${order}
                            </span>
                            <span style="font-weight: 600; color: #333;">${sectionName}</span>
                            <span style="margin-left: 10px; color: #666; font-size: 13px;">- ${sectionDesc}</span>
                        </div>
                    `);
                    
                    previewList.append(previewItem);
                    order++;
                }
            });
            
            if (order === 1) {
                previewList.html('<p style="color: #666; text-align: center; padding: 20px;">表示されるセクションがありません</p>');
            }
        }
        
        // チェックボックスの変更を監視
        $(document).on('change', '.section-item input[type="checkbox"]', function() {
            var $item = $(this).closest('.section-item');
            var $status = $item.find('.status-indicator');
            var $statusText = $item.find('.section-status span:last-child');
            
            if ($(this).is(':checked')) {
                $status.removeClass('disabled').addClass('enabled').css('background', '#28a745');
                $statusText.text('表示中');
                $item.removeClass('section-disabled').addClass('section-enabled');
                $item.css('opacity', '1');
            } else {
                $status.removeClass('enabled').addClass('disabled').css('background', '#dc3545');
                $statusText.text('非表示');
                $item.removeClass('section-enabled').addClass('section-disabled');
                $item.css('opacity', '0.6');
            }
            
            updateStats();
            updateSectionPreview();
            markFormChanged();
            
            // アニメーション効果
            $item.addClass('section-changed');
            setTimeout(function() {
                $item.removeClass('section-changed');
            }, 300);
        });
        
        // 統計を更新
        function updateStats() {
            var totalSections = $('.section-item').length;
            var enabledSections = $('.section-item input[type="checkbox"]:checked').length;
            var disabledRequired = $('.section-item input[type="checkbox"]:disabled:checked').length;
            
            $('.section-stats').html(`
                表示中: <strong>${enabledSections}</strong> / 
                総数: <strong>${totalSections}</strong>
                ${disabledRequired > 0 ? ` <span style="color: #666;">(必須: ${disabledRequired})</span>` : ''}
            `);
        }
        
        // フォームが変更されたことをマーク
        function markFormChanged() {
            $('#sections-form').addClass('form-changed');
        }
        
        // 順序入力の変更を監視
        $(document).on('input change', '.section-order-input', function() {
            markFormChanged();
            
            // 値の検証
            var value = parseInt($(this).val());
            var max = $('.section-item').length;
            
            if (value < 1) {
                $(this).val(1);
            } else if (value > max) {
                $(this).val(max);
            }
            
            // 重複チェック（簡易版）
            var currentValue = $(this).val();
            var $currentItem = $(this).closest('.section-item');
            var isDuplicate = false;
            
            $('.section-order-input').not(this).each(function() {
                if ($(this).val() === currentValue) {
                    isDuplicate = true;
                }
            });
            
            if (isDuplicate) {
                $(this).addClass('duplicate-order');
                showTempMessage($currentItem, '同じ順序の項目があります', 'warning');
            } else {
                $(this).removeClass('duplicate-order');
            }
        });
        
        // 一時的なメッセージを表示
        function showTempMessage($element, message, type = 'info') {
            var messageClass = 'temp-message-' + type;
            var $message = $(`<div class="temp-message ${messageClass}">${message}</div>`);
            
            $element.append($message);
            
            setTimeout(function() {
                $message.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 2000);
        }
        
        // ページ離脱時の警告
        $(window).on('beforeunload', function() {
            if ($('#sections-form').hasClass('form-changed')) {
                return '変更が保存されていません。ページを離れますか？';
            }
        });
        
        // フォーム送信時は警告を無効化
        $('#sections-form').on('submit', function() {
            $(window).off('beforeunload');
            
            // 送信前の最終検証
            var hasEnabledSections = $('.section-item input[type="checkbox"]:checked').length > 0;
            if (!hasEnabledSections) {
                alert('少なくとも1つのセクションを有効にしてください。');
                return false;
            }
            
            // ローディング表示
            var $submitBtn = $(this).find('button[type="submit"]');
            var originalText = $submitBtn.text();
            $submitBtn.prop('disabled', true).text('保存中...');
            
            // 元に戻す（エラー時用）
            setTimeout(function() {
                $submitBtn.prop('disabled', false).text(originalText);
            }, 5000);
        });
        
        // リセットボタンの確認強化
        $('button[name="reset_sections"]').on('click', function(e) {
            var confirmMsg = 'すべてのセクション設定がデフォルトに戻ります。\n' +
                           '・表示順序がリセットされます\n' +
                           '・表示/非表示設定がリセットされます\n\n' +
                           '本当に続行しますか？';
            
            if (!confirm(confirmMsg)) {
                e.preventDefault();
                return false;
            }
        });
        
        // キーボードショートカット
        $(document).on('keydown', function(e) {
            // Ctrl+S で保存
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                $('#sections-form button[name="save_sections"]').click();
            }
            
            // Escapeで変更をリセット（確認付き）
            if (e.key === 'Escape' && $('#sections-form').hasClass('form-changed')) {
                if (confirm('変更を破棄してページをリロードしますか？')) {
                    location.reload();
                }
            }
        });
        
        // ドラッグ&ドロップの追加スタイル
        $("#sortable-sections").on('sortstart', function(event, ui) {
            ui.item.addClass('sorting-active');
            $('.section-item').not(ui.item).addClass('sorting-inactive');
        });
        
        $("#sortable-sections").on('sortstop', function(event, ui) {
            $('.section-item').removeClass('sorting-active sorting-inactive');
        });
        
        // アニメーション効果の追加
        $('.section-item').each(function(index) {
            $(this).css('animation-delay', (index * 0.1) + 's');
        });
        
        // 初期化処理
        function initializeSectionManagement() {
            // 初期統計を更新
            updateStats();
            
            // 初期プレビューを更新
            updateSectionPreview();
            
            // 初期状態の設定
            $('.section-item').each(function() {
                var $checkbox = $(this).find('input[type="checkbox"]');
                if (!$checkbox.is(':checked') && !$checkbox.prop('disabled')) {
                    $(this).css('opacity', '0.6').addClass('section-disabled');
                } else {
                    $(this).addClass('section-enabled');
                }
            });
            
            // セクション管理が有効であることを表示
            console.log('%cSection Management Initialized', 'color: #28a745; font-weight: bold; font-size: 14px;');
            
            // 機能説明のツールチップを追加
            addSectionTooltips();
        }
        
        // ツールチップの追加
        function addSectionTooltips() {
            $('.drag-handle').attr('title', 'ドラッグして順序を変更');
            $('.section-order-input').attr('title', '表示順序を数値で指定');
            $('.status-indicator').attr('title', 'セクションの表示状態');
            
            // より詳細なツールチップ
            $('.section-item').each(function() {
                var sectionName = $(this).find('h4').text();
                var isRequired = $(this).find('input[type="checkbox"]').prop('disabled');
                
                if (isRequired) {
                    $(this).attr('title', sectionName + ' - このセクションは必須のため非表示にできません');
                } else {
                    $(this).attr('title', sectionName + ' - クリックで表示/非表示を切り替え、ドラッグで順序変更');
                }
            });
        }
        
        // バルクアクション機能
        function addBulkActions() {
            var bulkActionsHtml = `
                <div class="bulk-actions" style="margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                    <h4 style="margin: 0 0 10px 0;">一括操作</h4>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button type="button" class="button" id="enable-all-sections">すべて表示</button>
                        <button type="button" class="button" id="disable-all-optional">任意項目を非表示</button>
                        <button type="button" class="button" id="reset-order">順序をリセット</button>
                    </div>
                </div>
            `;
            
            $('.sections-container').after(bulkActionsHtml);
            
            // バルクアクションのイベントハンドラ
            $('#enable-all-sections').on('click', function() {
                $('.section-item input[type="checkbox"]:not(:disabled)').prop('checked', true).trigger('change');
            });
            
            $('#disable-all-optional').on('click', function() {
                $('.section-item input[type="checkbox"]:not(:disabled)').prop('checked', false).trigger('change');
            });
            
            $('#reset-order').on('click', function() {
                if (confirm('表示順序をデフォルトに戻しますか？')) {
                    $('.section-order-input').each(function(index) {
                        $(this).val(index + 1);
                    });
                    markFormChanged();
                }
            });
        }
        
        // 初期化実行
        initializeSectionManagement();
        addBulkActions();
        
        console.log('%cSection Management Features Loaded', 'color: #007cba; font-weight: bold;');
    }
    
});

// ================================
    // サービスカード透明度スライダー機能
    // ================================
    
    // 透明度スライダーのリアルタイム更新
    $('.service-opacity-slider').on('input', function() {
        const $slider = $(this);
        const value = $slider.val();
        const target = $slider.data('target');
        
        // 表示値を更新
        $(`#${target}-opacity-value`).text(value + '%');
        
        // プレビューカードを更新（存在する場合）
        updateServicePreviewCard(target, value);
        
        // 変更をマーク
        markFormChanged();
    });
    
    // サービスカードプレビューの生成・更新
    function updateServicePreviewCard(serviceId, opacity) {
        const colorInput = $(`input[name="${serviceId}_color"]`);
        const titleInput = $(`input[name="${serviceId}_title"]`);
        
        if (colorInput.length === 0 || titleInput.length === 0) {
            return;
        }
        
        const color = colorInput.val() || '#007cba';
        const title = titleInput.val() || 'サービスタイトル';
        const opacityValue = parseInt(opacity) / 100;
        
        // 既存のプレビューを削除
        $(`.service-preview-${serviceId}`).remove();
        
        // 新しいプレビューを作成
        const rgb = hexToRgb(color);
        if (rgb) {
            const gradientStyle = generatePreviewGradient(rgb, opacityValue);
            
            const previewHTML = `
                <div class="service-preview-${serviceId}" style="margin-top: 20px;">
                    <div style="
                        position: relative;
                        width: 300px;
                        height: 200px;
                        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    ">
                        <div style="
                            position: absolute;
                            bottom: 0;
                            left: 0;
                            right: 0;
                            height: 35%;
                            background: ${gradientStyle};
                            display: flex;
                            align-items: flex-end;
                            padding: 20px;
                        ">
                            <h4 style="
                                color: white;
                                margin: 0;
                                font-size: 18px;
                                font-weight: bold;
                                text-shadow: 0 1px 3px rgba(0,0,0,0.3);
                            ">${title}</h4>
                            <div style="
                                width: 40px;
                                height: 40px;
                                background: rgba(255,255,255,0.2);
                                border-radius: 50%;
                                margin-left: auto;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                <span style="color: white; font-size: 18px;">→</span>
                            </div>
                        </div>
                    </div>
                    <p style="margin-top: 10px; font-size: 12px; color: #666;">
                        プレビュー - カラー: ${color} | 透明度: ${opacity}%
                    </p>
                </div>
            `;
            
            // 対応するスライダーの後に挿入
            $(`input[name="${serviceId}_opacity"]`).closest('td').append(previewHTML);
        }
    }
    
    // 16進数カラーをRGBに変換（管理画面版）
    function hexToRgb(hex) {
        hex = hex.replace('#', '');
        
        if (hex.length === 3) {
            hex = hex.split('').map(char => char + char).join('');
        }
        
        const result = /^([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }
    
    // プレビュー用のグラデーション生成
    function generatePreviewGradient(rgb, opacity) {
        return `linear-gradient(
            to top,
            rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${opacity * 0.9}) 0%,
            rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${opacity * 0.7}) 20%,
            rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${opacity * 0.5}) 50%,
            rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${opacity * 0.3}) 80%,
            transparent 100%
        )`;
    }
    
    // カラーピッカーの変更も監視
    $(document).on('change', 'input[name$="_color"]', function() {
        const fieldName = $(this).attr('name');
        const serviceId = fieldName.replace('_color', '');
        const opacitySlider = $(`input[name="${serviceId}_opacity"]`);
        
        if (opacitySlider.length) {
            updateServicePreviewCard(serviceId, opacitySlider.val());
        }
    });
    
    // タイトルフィールドの変更も監視
    $(document).on('input', 'input[name$="_title"]', function() {
        const fieldName = $(this).attr('name');
        const serviceId = fieldName.replace('_title', '');
        const opacitySlider = $(`input[name="${serviceId}_opacity"]`);
        
        if (opacitySlider.length) {
            updateServicePreviewCard(serviceId, opacitySlider.val());
        }
    });
    
    // 初期プレビュー生成
    function initServicePreviews() {
        $('.service-opacity-slider').each(function() {
            const target = $(this).data('target');
            const opacity = $(this).val();
            updateServicePreviewCard(target, opacity);
        });
    }
    
    // ページ読み込み完了後に初期プレビュー生成
    $(document).ready(function() {
        setTimeout(initServicePreviews, 500);
    });
    
    // 一括透明度設定機能（オプション）
    if ($('.service-opacity-slider').length > 0) {
        const bulkControlsHTML = `
            <div class="service-bulk-controls" style="margin-top: 30px; padding: 20px; background: #f0f8ff; border-radius: 8px; border-left: 4px solid #007cba;">
                <h4 style="margin: 0 0 15px 0; color: #007cba;">一括透明度設定</h4>
                <p style="margin: 0 0 15px 0; color: #666; font-size: 13px;">
                    すべてのサービスカードの透明度を一括で設定できます。
                </p>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <label for="bulk-opacity-slider">透明度:</label>
                    <input type="range" id="bulk-opacity-slider" min="0" max="100" value="80" style="width: 200px;" />
                    <span id="bulk-opacity-value">80%</span>
                    <button type="button" class="button" id="apply-bulk-opacity">すべてに適用</button>
                </div>
            </div>
        `;
        
        // サービスカード設定の最後に追加
        $('h3:contains("サービスカード3")').parent().after(bulkControlsHTML);
        
        // 一括設定の機能実装
        $('#bulk-opacity-slider').on('input', function() {
            $('#bulk-opacity-value').text($(this).val() + '%');
        });
        
        $('#apply-bulk-opacity').on('click', function() {
            const bulkValue = $('#bulk-opacity-slider').val();
            
            $('.service-opacity-slider').each(function() {
                $(this).val(bulkValue).trigger('input');
            });
            
            showNotice(`すべてのサービスカードの透明度を ${bulkValue}% に設定しました。`, 'success');
        });
    }
    
    console.log('%cService Opacity Controls Loaded', 'color: #28a745; font-weight: bold;');

// ================================
// Footer-Up設定機能
// ================================

jQuery(document).ready(function($) {
    
    // Footer-Up設定ページのみで実行
    if ($('.riff-theme-settings_page_riff-footer-up-settings').length) {
        
        // 背景画像アップローダー
        $('#upload_footer_up_bg_button').click(function(e) {
            e.preventDefault();
            var image = wp.media({
                title: 'Footer-Up背景画像を選択',
                multiple: false,
                library: {
                    type: 'image'
                }
            }).open().on('select', function(e) {
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $('#footer_up_bg_image').val(image_url);
                
                // プレビューを更新
                $('.footer-up-bg-preview').remove();
                $('<div class="footer-up-bg-preview" style="margin-top: 10px;"><img src="' + image_url + '" style="max-width: 300px; height: auto; border: 1px solid #ddd; border-radius: 4px;" /></div>').insertAfter('#upload_footer_up_bg_button');
            });
        });
        
        // リアルタイムプレビュー機能
        function updateFooterUpPreview() {
            var title = $('input[name="footer_up_title"]').val() || 'サンプルタイトル';
            var subtitle = $('input[name="footer_up_subtitle"]').val() || 'Sample Subtitle';
            var description = $('textarea[name="footer_up_description"]').val() || 'サンプル説明文です。';
            var bgColor = $('input[name="footer_up_bg_color"]').val() || '#f8f9fa';
            var textColor = $('input[name="footer_up_text_color"]').val() || '#333333';
            var btn1Text = $('input[name="footer_up_btn1_text"]').val() || 'ボタン1';
            var btn1Color = $('input[name="footer_up_btn1_color"]').val() || '#ff6b35';
            var btn2Text = $('input[name="footer_up_btn2_text"]').val() || 'ボタン2';
            var btn2Color = $('input[name="footer_up_btn2_color"]').val() || '#007cba';
            
            // 既存のプレビューを削除
            $('.footer-up-preview').remove();
            
            // 新しいプレビューを作成
            var previewHTML = `
                <div class="footer-up-preview" style="
                    margin-top: 30px; 
                    padding: 60px 30px; 
                    background-color: ${bgColor}; 
                    color: ${textColor}; 
                    border-radius: 8px; 
                    text-align: center;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                ">
                    <p style="font-size: 14px; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 15px; opacity: 0.8;">${subtitle}</p>
                    <h3 style="font-size: 2.2rem; margin-bottom: 20px; font-weight: 700; line-height: 1.3;">${title}</h3>
                    <p style="font-size: 16px; opacity: 0.9; line-height: 1.7; margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto;">${description}</p>
                    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                        <a href="#" style="
                            display: inline-flex; 
                            align-items: center; 
                            gap: 8px; 
                            padding: 16px 32px; 
                            background: ${btn1Color}; 
                            color: white; 
                            text-decoration: none; 
                            border-radius: 50px; 
                            font-weight: 600;
                            transition: all 0.3s ease;
                        ">${btn1Text} →</a>
                        <a href="#" style="
                            display: inline-flex; 
                            align-items: center; 
                            gap: 8px; 
                            padding: 16px 32px; 
                            background: transparent; 
                            color: ${btn2Color}; 
                            text-decoration: none; 
                            border-radius: 50px; 
                            border: 2px solid ${btn2Color}; 
                            font-weight: 600;
                            transition: all 0.3s ease;
                        ">${btn2Text} →</a>
                    </div>
                </div>
            `;
            
            $('.form-table').last().after(previewHTML);
        }
        
        // プレビュー更新のイベントリスナー
        $('input[name="footer_up_title"], input[name="footer_up_subtitle"], textarea[name="footer_up_description"], input[name="footer_up_bg_color"], input[name="footer_up_text_color"], input[name="footer_up_btn1_text"], input[name="footer_up_btn1_color"], input[name="footer_up_btn2_text"], input[name="footer_up_btn2_color"]').on('input change', function() {
            debounce(updateFooterUpPreview, 500)();
        });
        
        // 初期プレビュー表示
        updateFooterUpPreview();
        
        // セクション有効/無効の切り替え
        $('input[name="footer_up_enabled"]').on('change', function() {
            var isEnabled = $(this).is(':checked');
            $('.footer-up-preview').toggle(isEnabled);
            
            if (isEnabled) {
                showNotice('Footer-Upセクションを有効にしました。', 'info');
            } else {
                showNotice('Footer-Upセクションを無効にしました。', 'warning');
            }
        });
        
        // カラーピッカーの改良
        $('input[type="color"]').each(function() {
            $(this).on('change', function() {
                var $preview = $(this).next('.color-preview');
                if ($preview.length === 0) {
                    $preview = $('<span class="color-preview" style="display: inline-block; width: 20px; height: 20px; border-radius: 50%; margin-left: 10px; border: 1px solid #ccc; vertical-align: middle;"></span>');
                    $(this).after($preview);
                }
                $preview.css('background-color', $(this).val());
            }).trigger('change');
        });
        
        // バリデーション
        $('.riff-admin-page form').on('submit', function(e) {
            var hasError = false;
            
            // URLフィールドのバリデーション
            $('input[type="url"]').each(function() {
                var url = $(this).val().trim();
                if (url && !isValidURL(url)) {
                    showFieldError($(this), '正しいURLを入力してください。');
                    hasError = true;
                } else {
                    clearFieldError($(this));
                }
            });
            
            if (hasError) {
                e.preventDefault();
                showNotice('入力内容を確認してください。', 'error');
            }
        });
        
        // 設定のエクスポート/インポート機能
        function exportFooterUpSettings() {
            var settings = {
                enabled: $('input[name="footer_up_enabled"]').is(':checked'),
                title: $('input[name="footer_up_title"]').val(),
                subtitle: $('input[name="footer_up_subtitle"]').val(),
                description: $('textarea[name="footer_up_description"]').val(),
                bgColor: $('input[name="footer_up_bg_color"]').val(),
                textColor: $('input[name="footer_up_text_color"]').val(),
                bgImage: $('input[name="footer_up_bg_image"]').val(),
                btn1: {
                    text: $('input[name="footer_up_btn1_text"]').val(),
                    url: $('input[name="footer_up_btn1_url"]').val(),
                    color: $('input[name="footer_up_btn1_color"]').val()
                },
                btn2: {
                    text: $('input[name="footer_up_btn2_text"]').val(),
                    url: $('input[name="footer_up_btn2_url"]').val(),
                    color: $('input[name="footer_up_btn2_color"]').val()
                }
            };
            
            var dataStr = JSON.stringify(settings, null, 2);
            var dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
            
            var exportFileDefaultName = 'footer-up-settings.json';
            
            var linkElement = document.createElement('a');
            linkElement.setAttribute('href', dataUri);
            linkElement.setAttribute('download', exportFileDefaultName);
            linkElement.click();
        }
        
        // エクスポート/インポートボタンの追加
        var toolsHTML = `
            <div class="footer-up-tools" style="margin-top: 30px; padding: 20px; background: #f0f8ff; border-radius: 6px; border: 1px solid #bee5eb;">
                <h4 style="margin: 0 0 15px 0; color: #0c5460;">Footer-Up設定ツール</h4>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="button" class="button" id="export-footer-up-settings">設定をエクスポート</button>
                    <input type="file" id="import-footer-up-settings" accept=".json" style="display: none;">
                    <button type="button" class="button" id="import-footer-up-settings-btn">設定をインポート</button>
                    <button type="button" class="button" id="reset-footer-up-settings">デフォルトに戻す</button>
                </div>
                <p style="margin: 15px 0 0 0; font-size: 13px; color: #666;">
                    設定をJSONファイルとしてエクスポートして、他のサイトにインポートできます。
                </p>
            </div>
        `;
        $('.submit').before(toolsHTML);
        
        // ツールボタンのイベントハンドラ
        $('#export-footer-up-settings').on('click', exportFooterUpSettings);
        
        $('#import-footer-up-settings-btn').on('click', function() {
            $('#import-footer-up-settings').click();
        });
        
        $('#import-footer-up-settings').on('change', function(e) {
            if (e.target.files.length > 0) {
                var file = e.target.files[0];
                var reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        var settings = JSON.parse(e.target.result);
                        
                        // 設定を適用
                        if (settings.enabled !== undefined) $('input[name="footer_up_enabled"]').prop('checked', settings.enabled);
                        if (settings.title) $('input[name="footer_up_title"]').val(settings.title);
                        if (settings.subtitle) $('input[name="footer_up_subtitle"]').val(settings.subtitle);
                        if (settings.description) $('textarea[name="footer_up_description"]').val(settings.description);
                        if (settings.bgColor) $('input[name="footer_up_bg_color"]').val(settings.bgColor);
                        if (settings.textColor) $('input[name="footer_up_text_color"]').val(settings.textColor);
                        if (settings.bgImage) $('input[name="footer_up_bg_image"]').val(settings.bgImage);
                        if (settings.btn1) {
                            if (settings.btn1.text) $('input[name="footer_up_btn1_text"]').val(settings.btn1.text);
                            if (settings.btn1.url) $('input[name="footer_up_btn1_url"]').val(settings.btn1.url);
                            if (settings.btn1.color) $('input[name="footer_up_btn1_color"]').val(settings.btn1.color);
                        }
                        if (settings.btn2) {
                            if (settings.btn2.text) $('input[name="footer_up_btn2_text"]').val(settings.btn2.text);
                            if (settings.btn2.url) $('input[name="footer_up_btn2_url"]').val(settings.btn2.url);
                            if (settings.btn2.color) $('input[name="footer_up_btn2_color"]').val(settings.btn2.color);
                        }
                        
                        updateFooterUpPreview();
                        showNotice('設定をインポートしました。', 'success');
                    } catch (error) {
                        showNotice('設定ファイルの形式が正しくありません。', 'error');
                    }
                };
                reader.readAsText(file);
            }
        });
        
        $('#reset-footer-up-settings').on('click', function() {
            if (confirm('すべての設定をデフォルト値に戻します。よろしいですか？')) {
                // デフォルト値を設定
                $('input[name="footer_up_enabled"]').prop('checked', true);
                $('input[name="footer_up_title"]').val('お気軽にお問い合わせください');
                $('input[name="footer_up_subtitle"]').val('Contact Us');
                $('textarea[name="footer_up_description"]').val('サービスに関するご質問やご相談がございましたら、お気軽にお問い合わせください。専門スタッフが丁寧にサポートいたします。');
                $('input[name="footer_up_bg_color"]').val('#f8f9fa');
                $('input[name="footer_up_text_color"]').val('#333333');
                $('input[name="footer_up_bg_image"]').val('');
                $('input[name="footer_up_btn1_text"]').val('お問い合わせ');
                $('input[name="footer_up_btn1_url"]').val('/contact/');
                $('input[name="footer_up_btn1_color"]').val('#ff6b35');
                $('input[name="footer_up_btn2_text"]').val('資料請求');
                $('input[name="footer_up_btn2_url"]').val('/document/');
                $('input[name="footer_up_btn2_color"]').val('#007cba');
                
                updateFooterUpPreview();
                showNotice('設定をデフォルト値に戻しました。', 'info');
            }
        });
        
        console.log('%cFooter-Up Admin Functions Loaded', 'color: #28a745; font-weight: bold;');
    }
    
});