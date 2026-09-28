/**
 * ローディング画面JavaScript（修正版）
 */

/**
 * Front-pageでのみローディング画面を実行する安全チェック
 */
(function() {
    'use strict';
    
    // Front-page以外では処理を停止
    if (!document.body.classList.contains('home') && 
        !document.body.classList.contains('front-page') &&
        !window.location.pathname.match(/^\/?$/) &&
        !window.location.pathname.match(/^\/\?.*$/)) {
        console.log('Loading screen: Not front-page, stopping execution');
        return;
    }
    
    // ローディング画面が有効でない場合は処理を停止
    if (typeof loadingSettings === 'undefined' || !loadingSettings.enable) {
        console.log('Loading screen: Settings not found or disabled');
        return;
    }
    
    // 管理画面やカスタマイザーでは実行しない
    if (document.body.classList.contains('admin-bar') && 
        (window.location.href.indexOf('/wp-admin/') !== -1 || 
         window.location.href.indexOf('customize.php') !== -1)) {
        console.log('Loading screen: Admin environment detected, stopping execution');
        return;
    }
    
    console.log('Loading screen: Safety checks passed, initializing...');

jQuery(document).ready(function($) {
    'use strict';

    // ローディング画面クラス（修正版）
    class LoadingScreen {
        constructor(settings) {
            this.settings = settings;
            this.container = $('#loading-screen-container');
            this.isLoading = false;
            this.timeouts = [];
            this.startTime = Date.now();
            
            // カスタマイザー設定の適用確認
            console.log('Loading screen settings received:', this.settings);
            
            this.init();
        }

        init() {
            if (!this.settings.enable) {
                console.log('Loading screen disabled');
                return;
            }

            console.log('Initializing loading screen with settings:', this.settings);

            // bodyにローディングクラスを追加
            $('body').addClass('loading-active');
            
            // ローディング画面を作成
            this.createLoadingScreens();
            
            // 確実に初期化を完了させてからアニメーション開始
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    console.log('Starting animation after initialization');
                    this.startAnimation();
                });
            });
        }

        createLoadingScreens() {
            // CSSカスタムプロパティを設定
            document.documentElement.style.setProperty('--loading-bg-color1', this.settings.bg_color1);
            document.documentElement.style.setProperty('--loading-bg-color2', this.settings.bg_color2);
            document.documentElement.style.setProperty('--loading-text-color', this.settings.text_color);
            
            // フォントファミリーを設定
            this.setFontFamily();

            // 第1フェーズ（黒背景 + 最初のテキスト）
            const phase1 = $('<div class="loading-screen phase-1"></div>');
            const firstText = $('<div class="loading-text ' + this.settings.text_animation + '">' + this.settings.first_text + '</div>');
            phase1.append(firstText);

            // 第2フェーズ（赤背景 + タイトル/ロゴ）- 最初は画面外に配置
            const phase2 = $('<div class="loading-screen phase-2"></div>');
            let titleElement;
            
            if (this.settings.display_type === 'logo' && this.settings.logo_image) {
                titleElement = $('<img class="loading-logo" src="' + this.settings.logo_image + '" alt="' + this.settings.title_text + '" width="200" height="200">');
            } else {
                titleElement = $('<div class="loading-title">' + this.settings.title_text + '</div>');
            }
            
            phase2.append(titleElement);

            // コンテナに追加
            this.container.append(phase1, phase2);
            
            // 確実に初期状態を設定
            setTimeout(() => {
                phase2.css({
                    'transform': 'translateY(-100%)',
                    'z-index': '1001'
                });
                phase1.css({
                    'z-index': '1000'
                });
            }, 50);
        }

        setFontFamily() {
            let fontFamily = '';
            
            switch(this.settings.font_family) {
                case 'noto-sans':
                    fontFamily = 'var(--font-noto-sans)';
                    break;
                case 'hiragino':
                    fontFamily = 'var(--font-hiragino)';
                    break;
                case 'yu-gothic':
                    fontFamily = 'var(--font-yu-gothic)';
                    break;
                case 'meiryo':
                    fontFamily = 'var(--font-meiryo)';
                    break;
                case 'roboto':
                    fontFamily = 'var(--font-roboto)';
                    break;
                case 'open-sans':
                    fontFamily = 'var(--font-open-sans)';
                    break;
                case 'lato':
                    fontFamily = 'var(--font-lato)';
                    break;
                case 'montserrat':
                    fontFamily = 'var(--font-montserrat)';
                    break;
                case 'poppins':
                    fontFamily = 'var(--font-poppins)';
                    break;
                default:
                    fontFamily = 'inherit';
            }
            
            if (fontFamily !== 'inherit') {
                document.documentElement.style.setProperty('--loading-font-family', fontFamily);
            }
        }

        startAnimation() {
            this.isLoading = true;
            
            // カスタマイザーの設定から表示時間を取得（秒 → ミリ秒に変換）
            // デフォルト値を確実に設定し、数値変換を行う
            const phase1Duration = Math.max(parseFloat(this.settings.phase1_duration) * 1000 || 3000, 2000); // 最低2秒
            const phase2Duration = Math.max(parseFloat(this.settings.phase2_duration) * 1000 || 2000, 1500); // 最低1.5秒
            
            // デバッグ情報をコンソールに出力
            console.log('Loading Screen Settings Applied:', {
                phase1_duration_setting: this.settings.phase1_duration,
                phase2_duration_setting: this.settings.phase2_duration,
                calculated_phase1_ms: phase1Duration,
                calculated_phase2_ms: phase2Duration,
                total_duration_ms: phase1Duration + phase2Duration,
                total_duration_seconds: (phase1Duration + phase2Duration) / 1000
            });

            // 第1フェーズを確実に表示
            console.log('Phase 1 started - showing black background and text');
            
            // 第1フェーズの終了タイミングで第2フェーズ開始
            this.addTimeout(() => {
                console.log('Starting phase 2 transition - preparing slide down');
                const phase2 = this.container.find('.phase-2');
                
                // 現在の状態を確認
                console.log('Phase 2 element found:', phase2.length > 0);
                console.log('Current transform:', phase2.css('transform'));
                
                // requestAnimationFrameで確実にアニメーション実行
                requestAnimationFrame(() => {
                    console.log('Executing slide down animation');
                    phase2.addClass('slide-down');
                    
                    // 念のため直接スタイルも設定
                    phase2.css('transform', 'translate3d(0, 0, 0)');
                    
                    // 実行確認
                    setTimeout(() => {
                        console.log('After slide down - transform:', phase2.css('transform'));
                        console.log('Has slide-down class:', phase2.hasClass('slide-down'));
                    }, 100);
                });
            }, phase1Duration);

            // 第2フェーズ表示後、フェードアウト開始
            this.addTimeout(() => {
                console.log('Starting fadeout - finishing loading animation');
                this.finishLoading();
            }, phase1Duration + phase2Duration);
        }

        // 修正：スムーズなフェードアウト処理
        finishLoading() {
            if (!this.isLoading) return;

            console.log('Finishing loading - starting smooth fadeout');
            this.isLoading = false;

            // 両画面を同時にフェードアウト
            const screens = this.container.find('.loading-screen');
            const container = this.container;
            
            requestAnimationFrame(() => {
                // 両画面に同時にフェードアウトクラスを追加
                screens.addClass('fade-out');
                // コンテナ自体もフェードアウト
                container.addClass('fade-out');
                console.log('Fadeout classes added to both screens and container');
            });

            // フェードアウト完了後に完全に非表示にする
            this.addTimeout(() => {
                console.log('Removing loading screen completely');
                $('body').removeClass('loading-active');
                this.container.remove();
                this.clearTimeouts();
                
                // カスタムイベントを発火
                $(document).trigger('loadingComplete');
                console.log('Loading screen removed - site content visible');
            }, 1200); // フェードアウト時間（1秒）+ バッファ（0.2秒）
        }

        addTimeout(callback, delay) {
            const timeoutId = setTimeout(callback, delay);
            this.timeouts.push(timeoutId);
            return timeoutId;
        }

        clearTimeouts() {
            this.timeouts.forEach(id => clearTimeout(id));
            this.timeouts = [];
        }

        // 手動でローディングを停止する方法
        stop() {
            if (this.isLoading) {
                this.clearTimeouts();
                this.finishLoading();
            }
        }
    }

    // ページ読み込み完了後の処理（フェイルセーフ・修正版）
    // 通常はphase1/phase2のタイマーで自動的に消えるが、
    // 万一window.loadが大幅に遅延した場合でも、初期表示からの絶対時間で
    // 必ずローディング画面を終了させる（window.load時点から再カウントしない）
    $(window).on('load', function() {
        if (window.loadingScreenInstance && typeof loadingSettings !== 'undefined') {
            const phase1Duration = Math.max(parseFloat(loadingSettings.phase1_duration) * 1000 || 3000, 2000);
            const phase2Duration = Math.max(parseFloat(loadingSettings.phase2_duration) * 1000 || 2000, 1500);
            const totalDuration = phase1Duration + phase2Duration + 1000; // +1秒のバッファ
            const elapsed = Date.now() - (window.loadingScreenInstance.startTime || Date.now());
            const remaining = Math.max(totalDuration - elapsed, 0);

            setTimeout(function() {
                if (window.loadingScreenInstance && window.loadingScreenInstance.isLoading) {
                    window.loadingScreenInstance.finishLoading();
                }
            }, remaining);
        }
    });

    // 初期化（カスタマイザー設定の確認を強化）
    if (typeof loadingSettings !== 'undefined' && loadingSettings.enable) {
        // 設定値をデバッグ出力
        console.log('Loading Settings Received and Validated:', {
            enable: loadingSettings.enable,
            phase1_duration: loadingSettings.phase1_duration,
            phase2_duration: loadingSettings.phase2_duration,
            display_type: loadingSettings.display_type,
            first_text: loadingSettings.first_text,
            title_text: loadingSettings.title_text,
            bg_color1: loadingSettings.bg_color1,
            bg_color2: loadingSettings.bg_color2,
            text_color: loadingSettings.text_color,
            font_family: loadingSettings.font_family,
            text_animation: loadingSettings.text_animation
        });
        
        window.loadingScreenInstance = new LoadingScreen(loadingSettings);
    } else {
        console.log('Loading settings not found or disabled');
    }

    // ユーティリティ関数：外部からローディングを制御
    window.LoadingScreenUtils = {
        stop: function() {
            if (window.loadingScreenInstance) {
                window.loadingScreenInstance.stop();
            }
        },
        
        isLoading: function() {
            return window.loadingScreenInstance ? window.loadingScreenInstance.isLoading : false;
        },
        
        // デバッグ用：現在の設定を確認
        getSettings: function() {
            return window.loadingScreenInstance ? window.loadingScreenInstance.settings : null;
        }
    };

    // プリローダー対応：画像等の読み込み完了を待つ
    function preloadResources() {
        const images = document.querySelectorAll('img');
        const videos = document.querySelectorAll('video');
        let loadedCount = 0;
        const totalResources = images.length + videos.length;

        if (totalResources === 0) {
            return Promise.resolve();
        }

        return new Promise((resolve) => {
            function checkComplete() {
                loadedCount++;
                if (loadedCount >= totalResources) {
                    resolve();
                }
            }

            // 画像の読み込み監視
            images.forEach(img => {
                if (img.complete) {
                    checkComplete();
                } else {
                    img.addEventListener('load', checkComplete);
                    img.addEventListener('error', checkComplete);
                }
            });

            // 動画の読み込み監視
            videos.forEach(video => {
                if (video.readyState >= 3) {
                    checkComplete();
                } else {
                    video.addEventListener('canplaythrough', checkComplete);
                    video.addEventListener('error', checkComplete);
                }
            });

            // タイムアウト設定（10秒）
            setTimeout(resolve, 10000);
        });
    }

    // リソース読み込み完了後の処理
    preloadResources().then(() => {
        // 追加の処理がある場合はここに記述
        console.log('All resources loaded');
    });

    // エラーハンドリング
    window.addEventListener('error', function(e) {
        console.warn('Loading screen error:', e);
        if (window.loadingScreenInstance && window.loadingScreenInstance.isLoading) {
            // エラーが発生した場合は強制的にローディングを終了
            setTimeout(() => {
                window.loadingScreenInstance.stop();
            }, 1000);
        }
    });

    // ページ離脱時のクリーンアップ
    $(window).on('beforeunload', function() {
        if (window.loadingScreenInstance) {
            window.loadingScreenInstance.clearTimeouts();
        }
    });
});

// AMD/CommonJS対応
if (typeof define === 'function' && define.amd) {
    define([], function() {
        return window.LoadingScreenUtils;
    });
} else if (typeof module !== 'undefined' && module.exports) {
    module.exports = window.LoadingScreenUtils;
}
    // ファイルの最後に追加
})(); // 安全チェック関数の終了