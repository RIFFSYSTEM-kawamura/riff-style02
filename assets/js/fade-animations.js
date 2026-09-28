/**
 * RIFF テーマ フェードアニメーション JavaScript
 * バージョン: 1.0.0
 * 既存のJavaScriptと競合しないよう独自の名前空間を使用
 */

(function() {
    'use strict';

    // 名前空間を作成（既存のコードと競合回避）
    window.RiffFadeAnimations = window.RiffFadeAnimations || {};

    /**
     * フェードアニメーション管理クラス
     */
    class RiffFadeManager {
        constructor() {
            this.elements = [];
            this.observer = null;
            this.isInitialized = false;
            this.config = {
                threshold: 0.1,           // 要素の10%が見えたらトリガー
                rootMargin: '0px 0px -50px 0px', // 少し早めにトリガー
                once: true                // 一度だけアニメーション実行
            };
            
            // デバッグモード（開発時に有効にする）
            this.debugMode = false;
        }

        /**
         * 初期化メソッド
         */
        init() {
            if (this.isInitialized) {
                return;
            }

            // Intersection Observer API のサポートチェック
            if (!('IntersectionObserver' in window)) {
                console.warn('RiffFadeAnimations: IntersectionObserver is not supported. Fallback to immediate display.');
                this.fallbackDisplay();
                return;
            }

            // モーション設定を無効にしているユーザーをチェック
            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                console.info('RiffFadeAnimations: Reduced motion is preferred. Animations disabled.');
                this.fallbackDisplay();
                return;
            }

            this.setupObserver();
            this.findElements();
            this.observeElements();
            
            this.isInitialized = true;
            
            if (this.debugMode) {
                console.log('RiffFadeAnimations: Initialized with', this.elements.length, 'elements');
                document.body.classList.add('riff-fade-debug');
            }
        }

        /**
         * Intersection Observer のセットアップ
         */
        setupObserver() {
            const options = {
                root: null,
                rootMargin: this.config.rootMargin,
                threshold: this.config.threshold
            };

            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.animateElement(entry.target);
                        
                        // 一度だけ実行する場合は監視を停止
                        if (this.config.once) {
                            this.observer.unobserve(entry.target);
                        }
                    }
                });
            }, options);
        }

        /**
         * フェード対象要素を検索
         */
        findElements() {
            const selectors = [
                '.fadeUpTrigger',
                '.fadeDownTrigger',
                '.fadeLeftTrigger',
                '.fadeRightTrigger'
            ];

            this.elements = [];
            
            selectors.forEach(selector => {
                const elements = document.querySelectorAll(selector);
                elements.forEach(element => {
                    if (!element.classList.contains('riff-fade-processed')) {
                        this.elements.push(element);
                        element.classList.add('riff-fade-processed');
                    }
                });
            });
        }

        /**
         * 要素の監視を開始
         */
        observeElements() {
            this.elements.forEach(element => {
                this.observer.observe(element);
            });
        }

        /**
         * 要素のアニメーション実行
         */
        animateElement(element) {
            // アニメーションの前処理
            element.style.willChange = 'transform, opacity';
            
            // 少し遅延を入れてからアニメーション開始（ブラウザ描画の最適化）
            requestAnimationFrame(() => {
                element.classList.add('riff-fade-visible');
                
                // アニメーション完了後にwill-changeをリセット
                const transitionDuration = parseFloat(getComputedStyle(element).transitionDuration) * 1000;
                const delay = parseFloat(getComputedStyle(element).transitionDelay) * 1000;
                
                setTimeout(() => {
                    element.style.willChange = 'auto';
                }, transitionDuration + delay + 100);
                
                if (this.debugMode) {
                    console.log('RiffFadeAnimations: Animated element', element);
                }
            });
        }

        /**
         * フォールバック表示（Intersection Observer 非対応時）
         */
        fallbackDisplay() {
            const selectors = [
                '.fadeUpTrigger',
                '.fadeDownTrigger',
                '.fadeLeftTrigger',
                '.fadeRightTrigger'
            ];

            selectors.forEach(selector => {
                const elements = document.querySelectorAll(selector);
                elements.forEach(element => {
                    element.style.opacity = '1';
                    element.style.transform = 'none';
                });
            });
        }

        /**
         * 新しい要素を追加（動的コンテンツ対応）
         */
        refresh() {
            if (!this.isInitialized) {
                return;
            }

            this.findElements();
            
            // 新しく見つかった要素のみを監視対象に追加
            this.elements.forEach(element => {
                if (!element.classList.contains('riff-fade-observed')) {
                    this.observer.observe(element);
                    element.classList.add('riff-fade-observed');
                }
            });
            
            if (this.debugMode) {
                console.log('RiffFadeAnimations: Refreshed. Total elements:', this.elements.length);
            }
        }

        /**
         * アニメーションを強制実行（すべての要素）
         */
        showAll() {
            const elements = document.querySelectorAll('.fadeUpTrigger, .fadeDownTrigger, .fadeLeftTrigger, .fadeRightTrigger');
            elements.forEach(element => {
                this.animateElement(element);
            });
        }

        /**
         * デバッグモードの切り替え
         */
        toggleDebug() {
            this.debugMode = !this.debugMode;
            if (this.debugMode) {
                document.body.classList.add('riff-fade-debug');
                console.log('RiffFadeAnimations: Debug mode enabled');
            } else {
                document.body.classList.remove('riff-fade-debug');
                console.log('RiffFadeAnimations: Debug mode disabled');
            }
        }

        /**
         * 破棄メソッド
         */
        destroy() {
            if (this.observer) {
                this.observer.disconnect();
                this.observer = null;
            }
            
            this.elements = [];
            this.isInitialized = false;
            
            if (this.debugMode) {
                document.body.classList.remove('riff-fade-debug');
            }
        }
    }

    // インスタンスを作成してグローバルに公開
    const fadeManager = new RiffFadeManager();
    window.RiffFadeAnimations.manager = fadeManager;

    /**
     * DOMの読み込み完了後に初期化
     */
    function initializeOnReady() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                fadeManager.init();
            });
        } else {
            // すでにDOMが読み込まれている場合
            fadeManager.init();
        }
    }

    /**
     * ページが完全に読み込まれた後に再チェック（動的コンテンツ対応）
     */
    function initializeOnLoad() {
        window.addEventListener('load', () => {
            setTimeout(() => {
                fadeManager.refresh();
            }, 100);
        });
    }

    // 初期化実行
    initializeOnReady();
    initializeOnLoad();

    /**
     * 外部から利用可能な関数を公開
     */
    window.RiffFadeAnimations.init = () => fadeManager.init();
    window.RiffFadeAnimations.refresh = () => fadeManager.refresh();
    window.RiffFadeAnimations.showAll = () => fadeManager.showAll();
    window.RiffFadeAnimations.toggleDebug = () => fadeManager.toggleDebug();
    window.RiffFadeAnimations.destroy = () => fadeManager.destroy();

    // レスポンシブ対応（画面リサイズ時の再計算）
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            fadeManager.refresh();
        }, 250);
    });

    // 開発者向けのコンソール出力
    if (window.console && typeof window.console.info === 'function') {
        console.info('RiffFadeAnimations: Loaded successfully. Use RiffFadeAnimations.toggleDebug() to enable debug mode.');
    }

})();