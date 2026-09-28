/**
 * BLOG セクション フィルタリング機能（参考サイト準拠）
 * Enhanced Blog Filter System
 */

(function() {
    'use strict';

    // 設定オブジェクト
    const config = {
        animationDuration: 600,
        staggerDelay: 100,
        easeFunction: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
        enableAnimations: !window.matchMedia('(prefers-reduced-motion: reduce)').matches
    };

    // DOM要素の参照
    let elements = {};

    // 現在のフィルター状態
    let currentFilter = 'all';
    let isFiltering = false;

    /**
     * 初期化
     */
    function init() {
        // DOM要素を取得
        cacheElements();

        // イベントリスナーを設定
        bindEvents();

        // 初期表示の設定
        setupInitialState();

        console.log('Blog Filter Enhanced: Initialized successfully');
    }

    /**
     * DOM要素をキャッシュ
     */
    function cacheElements() {
        elements = {
            section: document.querySelector('.riff-blog-section-enhanced'),
            container: document.querySelector('.riff-blog-container-enhanced'),
            filterButtons: document.querySelectorAll('.riff-blog-filter-btn-enhanced'),
            blogGrid: document.querySelector('.riff-blog-grid-enhanced'),
            blogItems: document.querySelectorAll('.riff-blog-item-enhanced'),
            noPostsMessage: document.querySelector('.no-blog-posts-enhanced')
        };
    }

    /**
     * イベントリスナーを設定
     */
    function bindEvents() {
        if (!elements.filterButtons.length) return;

        // フィルターボタンのクリックイベント
        elements.filterButtons.forEach(button => {
            button.addEventListener('click', handleFilterClick);
            
            // キーボードアクセシビリティ
            button.addEventListener('keydown', handleFilterKeydown);
        });

        // ウィンドウリサイズイベント
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(handleResize, 250);
        });

        // アクセシビリティ: 動きの削減設定の変更を監視
        if (window.matchMedia) {
            const reducedMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
            reducedMotionQuery.addListener(handleReducedMotionChange);
        }
    }

    /**
     * 初期状態の設定
     */
    function setupInitialState() {
        if (!elements.blogItems.length) return;

        // 全ての記事を表示状態に設定
        elements.blogItems.forEach((item, index) => {
            item.style.opacity = '1';
            item.style.transform = 'scale(1) translateY(0)';
            
            // アニメーション遅延の設定
            if (config.enableAnimations) {
                item.style.transitionDelay = `${index * 50}ms`;
            }
        });

        // アクティブなフィルターボタンを設定
        const activeButton = document.querySelector('.riff-blog-filter-btn-enhanced.active');
        if (activeButton) {
            currentFilter = activeButton.dataset.category || 'all';
        }
    }

    /**
     * フィルターボタンのクリックハンドラー
     */
    function handleFilterClick(event) {
        event.preventDefault();
        
        if (isFiltering) return;

        const button = event.currentTarget;
        const category = button.dataset.category || 'all';

        // 既に選択されているフィルターの場合は何もしない
        if (category === currentFilter) return;

        // ボタンの状態を更新
        updateActiveButton(button);

        // フィルタリングを実行
        filterBlogItems(category);

        // 現在のフィルターを更新
        currentFilter = category;

        // アナリティクス（オプション）
        trackFilterUsage(category);
    }

    /**
     * キーボードナビゲーション
     */
    function handleFilterKeydown(event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            handleFilterClick(event);
        }
    }

    /**
     * アクティブボタンの更新
     */
    function updateActiveButton(activeButton) {
        // 全てのボタンから active クラスを削除
        elements.filterButtons.forEach(button => {
            button.classList.remove('active');
            button.setAttribute('aria-pressed', 'false');
        });

        // クリックされたボタンに active クラスを追加
        activeButton.classList.add('active');
        activeButton.setAttribute('aria-pressed', 'true');

        // フォーカス管理
        activeButton.focus();
    }

    /**
     * ブログアイテムのフィルタリング
     */
    async function filterBlogItems(category) {
        if (!elements.blogGrid || !elements.blogItems.length) return;

        isFiltering = true;
        elements.blogGrid.classList.add('filtering');

        try {
            // アニメーション有効時
            if (config.enableAnimations) {
                await performAnimatedFiltering(category);
            } else {
                // アニメーション無効時（即座に切り替え）
                performInstantFiltering(category);
            }
        } catch (error) {
            console.error('Blog Filter Error:', error);
            // エラー時は即座に切り替え
            performInstantFiltering(category);
        } finally {
            isFiltering = false;
            elements.blogGrid.classList.remove('filtering');
        }
    }

    /**
     * アニメーション付きフィルタリング
     */
    async function performAnimatedFiltering(category) {
        // フェードアウト段階
        const hidePromises = Array.from(elements.blogItems).map((item, index) => {
            return new Promise(resolve => {
                setTimeout(() => {
                    if (shouldShowItem(item, category)) {
                        // 表示する記事: 一時的に非表示
                        item.style.opacity = '0';
                        item.style.transform = 'scale(0.8) translateY(20px)';
                    } else {
                        // 非表示にする記事: フェードアウト
                        item.classList.add('filtering-hide');
                        item.style.opacity = '0';
                        item.style.transform = 'scale(0.8) translateY(-20px)';
                    }
                    
                    setTimeout(resolve, config.animationDuration * 0.6);
                }, index * 50);
            });
        });

        await Promise.all(hidePromises);

        // レイアウト調整（非表示要素を完全に隠す）
        elements.blogItems.forEach(item => {
            if (!shouldShowItem(item, category)) {
                item.style.display = 'none';
                item.classList.add('filtering-hidden');
            } else {
                item.style.display = 'block';
                item.classList.remove('filtering-hidden');
            }
        });

        // 短い遅延
        await new Promise(resolve => setTimeout(resolve, 100));

        // フェードイン段階
        const showPromises = Array.from(elements.blogItems).map((item, index) => {
            return new Promise(resolve => {
                if (shouldShowItem(item, category)) {
                    setTimeout(() => {
                        item.classList.remove('filtering-hide');
                        item.classList.add('filtering-show');
                        item.style.opacity = '1';
                        item.style.transform = 'scale(1) translateY(0)';
                        
                        setTimeout(() => {
                            item.classList.remove('filtering-show');
                            resolve();
                        }, config.animationDuration);
                    }, index * 80);
                } else {
                    resolve();
                }
            });
        });

        await Promise.all(showPromises);

        // クリーンアップ
        elements.blogItems.forEach(item => {
            item.classList.remove('filtering-hide', 'filtering-show');
        });
    }

    /**
     * 即座に切り替え（アニメーション無効時）
     */
    function performInstantFiltering(category) {
        elements.blogItems.forEach(item => {
            if (shouldShowItem(item, category)) {
                item.style.display = 'block';
                item.style.opacity = '1';
                item.style.transform = 'scale(1) translateY(0)';
                item.classList.remove('filtering-hidden');
            } else {
                item.style.display = 'none';
                item.classList.add('filtering-hidden');
            }
        });
    }

    /**
     * アイテムを表示すべきかチェック
     */
    function shouldShowItem(item, category) {
        if (category === 'all') return true;

        const itemCategories = item.dataset.categories || '';
        return itemCategories.split(' ').includes(category);
    }

    /**
     * 動きの削減設定変更ハンドラー
     */
    function handleReducedMotionChange(query) {
        config.enableAnimations = !query.matches;
        
        // 設定変更時にスタイルをリセット
        if (!config.enableAnimations) {
            elements.blogItems.forEach(item => {
                item.style.transition = 'none';
                item.style.transitionDelay = '0s';
            });
        } else {
            elements.blogItems.forEach(item => {
                item.style.transition = '';
                item.style.transitionDelay = '';
            });
        }
    }

    /**
     * リサイズハンドラー
     */
    function handleResize() {
        // レスポンシブ対応: 必要に応じてレイアウトを調整
        if (window.innerWidth <= 768) {
            // モバイル表示時の調整
            elements.blogItems.forEach(item => {
                if (item.style.display === 'none') return;
                item.style.transform = 'scale(1) translateY(0)';
            });
        }
    }

    /**
     * フィルター使用状況の追跡（オプション）
     */
    function trackFilterUsage(category) {
        // Google Analytics や他のアナリティクスツールに送信
        if (typeof gtag !== 'undefined') {
            gtag('event', 'blog_filter_used', {
                'event_category': 'Blog Interaction',
                'event_label': category,
                'value': 1
            });
        }

        // カスタムイベントの発火
        const customEvent = new CustomEvent('blogFilterChanged', {
            detail: {
                category: category,
                timestamp: new Date().toISOString(),
                previousFilter: currentFilter
            }
        });
        document.dispatchEvent(customEvent);
    }

    /**
     * パブリックAPI
     */
    window.BlogFilterEnhanced = {
        // 外部からフィルターを設定
        setFilter: function(category) {
            const button = document.querySelector(`[data-category="${category}"]`);
            if (button && category !== currentFilter) {
                button.click();
            }
        },

        // 現在のフィルターを取得
        getCurrentFilter: function() {
            return currentFilter;
        },

        // 表示されているアイテム数を取得
        getVisibleItemsCount: function() {
            return Array.from(elements.blogItems).filter(item => 
                item.style.display !== 'none'
            ).length;
        },

        // フィルターをリセット
        resetFilter: function() {
            const allButton = document.querySelector('[data-category="all"]');
            if (allButton) {
                allButton.click();
            }
        },

        // 再初期化
        reinit: function() {
            init();
        }
    };

    /**
     * DOM読み込み完了後に初期化
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // ページ表示時（戻るボタンなど）の再初期化
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            setTimeout(init, 100);
        }
    });

})();

/**
 * オプション: Intersection Observer による遅延読み込み
 * 記事が多い場合のパフォーマンス向上
 */
(function() {
    'use strict';

    // Intersection Observer がサポートされている場合のみ実行
    if (!('IntersectionObserver' in window)) return;

    let observer;

    function initLazyLoading() {
        const blogImages = document.querySelectorAll('.riff-blog-image-enhanced img[data-src]');
        
        if (!blogImages.length) return;

        const imageObserverOptions = {
            rootMargin: '50px 0px',
            threshold: 0.1
        };

        observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const dataSrc = img.getAttribute('data-src');
                    
                    if (dataSrc) {
                        img.src = dataSrc;
                        img.removeAttribute('data-src');
                        img.classList.add('loaded');
                        observer.unobserve(img);
                    }
                }
            });
        }, imageObserverOptions);

        blogImages.forEach(img => observer.observe(img));
    }

    // DOM読み込み完了後に遅延読み込みを初期化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLazyLoading);
    } else {
        initLazyLoading();
    }

})();

/**
 * オプション: キーボードショートカット
 * アクセシビリティとUX向上のため
 */
(function() {
    'use strict';

    const shortcuts = {
        '1': 'all',
        '2': null, // 動的に設定
        '3': null, // 動的に設定
        '4': null, // 動的に設定
        '5': null  // 動的に設定
    };

    function initKeyboardShortcuts() {
        const filterButtons = document.querySelectorAll('.riff-blog-filter-btn-enhanced');
        
        if (!filterButtons.length) return;

        // ショートカットマッピングを動的に設定
        filterButtons.forEach((button, index) => {
            const key = (index + 1).toString();
            if (shortcuts.hasOwnProperty(key)) {
                shortcuts[key] = button.dataset.category;
                
                // ボタンにツールチップを追加（オプション）
                button.setAttribute('title', `キーボードショートカット: ${key}`);
            }
        });

        // キーボードイベントリスナー
        document.addEventListener('keydown', (event) => {
            // Ctrl/Cmd + 数字キーの場合のみ
            if ((event.ctrlKey || event.metaKey) && shortcuts[event.key]) {
                event.preventDefault();
                
                const category = shortcuts[event.key];
                if (window.BlogFilterEnhanced && category) {
                    window.BlogFilterEnhanced.setFilter(category);
                }
            }
        });
    }

    // DOM読み込み完了後にキーボードショートカットを初期化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initKeyboardShortcuts);
    } else {
        initKeyboardShortcuts();
    }

})();