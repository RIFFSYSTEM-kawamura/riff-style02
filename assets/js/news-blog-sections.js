/**
 * NEWSセクション・BLOGセクション JavaScript
 * フィルター機能、アニメーション、インタラクション管理
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // 初期化
    initializeNewsSections();
    initializeBlogSections();
    initializeAnimations();
    initializeIntersectionObserver();
    
    /**
     * NEWSセクションの初期化
     */
    function initializeNewsSections() {
        const newsFilterButtons = document.querySelectorAll('.riff-news-filter-btn');
        const newsItems = document.querySelectorAll('.riff-news-item-new');
        
        if (newsFilterButtons.length === 0 || newsItems.length === 0) return;
        
        // フィルターボタンのイベントリスナー設定
        newsFilterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                filterNewsItems(filter, newsFilterButtons, newsItems);
            });
        });
        
        // ホバーエフェクトの設定
        setupHoverEffects(newsItems);
    }
    
    /**
     * BLOGセクションの初期化
     */
    function initializeBlogSections() {
        const blogItems = document.querySelectorAll('.riff-blog-item-new');
        
        if (blogItems.length === 0) return;
        
        // ホバーエフェクトの設定
        setupHoverEffects(blogItems);
    }
    
    /**
     * ニュース記事のフィルタリング
     */
    function filterNewsItems(filter, filterButtons, newsItems) {
        // ローディング状態を表示
        showLoadingState();
        
        // アクティブボタンの切り替え
        filterButtons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        // フィルタリング処理（アニメーション付き）
        setTimeout(() => {
            newsItems.forEach(item => {
                const itemCategories = item.getAttribute('data-category');
                const shouldShow = filter === 'all' || (itemCategories && itemCategories.includes(filter));
                
                if (shouldShow) {
                    item.classList.remove('filtered-out');
                    item.style.display = 'block';
                    // 段階的にアニメーション表示
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0) scale(1)';
                    }, 50);
                } else {
                    item.classList.add('filtered-out');
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(20px) scale(0.95)';
                    // 完全に非表示にする
                    setTimeout(() => {
                        if (item.classList.contains('filtered-out')) {
                            item.style.display = 'none';
                        }
                    }, 300);
                }
            });
            
            hideLoadingState();
        }, 200);
        
        // アナリティクス（必要に応じて）
        if (typeof gtag !== 'undefined') {
            gtag('event', 'filter_news', {
                'filter_category': filter
            });
        }
    }
    
    /**
     * ホバーエフェクトの設定
     */
    function setupHoverEffects(items) {
        items.forEach(item => {
            const image = item.querySelector('img');
            const title = item.querySelector('.riff-news-item-title-new, .riff-blog-item-title-new');
            
            if (!image || !title) return;
            
            // マウスオーバー時のパララックス効果
            item.addEventListener('mouseenter', function(e) {
                this.style.transform = 'translateY(-10px)';
                if (image) {
                    image.style.transform = 'scale(1.08)';
                }
                
                // タイトルの色変更アニメーション
                if (title) {
                    title.style.transition = 'color 0.3s ease';
                    title.style.color = 'var(--primary-color)';
                }
            });
            
            item.addEventListener('mouseleave', function(e) {
                this.style.transform = 'translateY(0)';
                if (image) {
                    image.style.transform = 'scale(1)';
                }
                
                // タイトル色をリセット
                if (title) {
                    title.style.color = 'var(--text-color)';
                }
            });
            
            // マウス移動時の微細なパララックス
            item.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const deltaX = (x - centerX) / centerX;
                const deltaY = (y - centerY) / centerY;
                
                const tiltX = deltaY * 5; // 最大5度の傾き
                const tiltY = deltaX * -5;
                
                this.style.transform = `translateY(-10px) perspective(1000px) rotateX(${tiltX}deg) rotateY(${tiltY}deg)`;
            });
        });
    }
    
    /**
     * アニメーションの初期化
     */
    function initializeAnimations() {
        // スクロール時のfade-inアニメーション
        const fadeElements = document.querySelectorAll('.fade-in');
        
        fadeElements.forEach(element => {
            const delay = element.getAttribute('data-delay') || '0s';
            element.style.animationDelay = delay;
        });
        
        // カウンターアニメーション（もし統計情報がある場合）
        initializeCounters();
    }
    
    /**
     * Intersection Observer の設定
     */
    function initializeIntersectionObserver() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    
                    // fade-inアニメーションの実行
                    if (element.classList.contains('fade-in')) {
                        element.style.opacity = '1';
                        element.style.transform = 'translateY(0)';
                    }
                    
                    // 段階的アニメーション（子要素がある場合）
                    const children = element.querySelectorAll('.riff-news-item-new, .riff-blog-item-new');
                    children.forEach((child, index) => {
                        setTimeout(() => {
                            child.style.opacity = '1';
                            child.style.transform = 'translateY(0)';
                        }, index * 100);
                    });
                }
            });
        }, observerOptions);
        
        // 監視対象の要素を登録
        const sectionsToObserve = document.querySelectorAll(
            '.riff-news-section-fullwidth, .riff-blog-section-fullwidth, .fade-in'
        );
        
        sectionsToObserve.forEach(section => {
            observer.observe(section);
        });
    }
    
    /**
     * カウンターアニメーション
     */
    function initializeCounters() {
        const counters = document.querySelectorAll('[data-counter]');
        
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-counter'));
            const duration = 2000; // 2秒でカウントアップ
            const step = target / (duration / 16); // 60FPS
            let current = 0;
            
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current).toLocaleString();
            }, 16);
        });
    }
    
    /**
     * ローディング状態の表示
     */
    function showLoadingState() {
        let loadingElement = document.querySelector('.loading-state');
        
        if (!loadingElement) {
            loadingElement = document.createElement('div');
            loadingElement.className = 'loading-state';
            loadingElement.innerHTML = `
                <div class="loading-spinner"></div>
                <p>記事を読み込み中...</p>
            `;
            
            const newsContainer = document.querySelector('.riff-news-grid-new');
            if (newsContainer) {
                newsContainer.parentNode.insertBefore(loadingElement, newsContainer.nextSibling);
            }
        }
        
        loadingElement.classList.add('active');
    }
    
    /**
     * ローディング状態の非表示
     */
    function hideLoadingState() {
        const loadingElement = document.querySelector('.loading-state');
        if (loadingElement) {
            loadingElement.classList.remove('active');
        }
    }
    
    /**
     * スムーススクロール機能
     */
    function initializeSmoothScroll() {
        const buttons = document.querySelectorAll('.riff-news-button-new, .riff-blog-button-new');
        
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // 内部リンクの場合のみスムーススクロール
                if (href && href.startsWith('#')) {
                    e.preventDefault();
                    
                    const targetElement = document.querySelector(href);
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    }
    
    /**
     * レスポンシブ対応の調整
     */
    function handleResponsiveAdjustments() {
        const mediaQuery = window.matchMedia('(max-width: 768px)');
        
        function handleMediaChange(e) {
            const newsGrids = document.querySelectorAll('.riff-news-grid-new, .riff-blog-grid-new');
            
            newsGrids.forEach(grid => {
                if (e.matches) {
                    // モバイル表示の調整
                    grid.style.gridTemplateColumns = '1fr';
                    grid.style.gap = '20px';
                } else {
                    // デスクトップ表示の復元
                    grid.style.gridTemplateColumns = '';
                    grid.style.gap = '';
                }
            });
        }
        
        mediaQuery.addEventListener('change', handleMediaChange);
        handleMediaChange(mediaQuery); // 初期実行
    }
    
    /**
     * アクセシビリティの向上
     */
    function improveAccessibility() {
        // キーボードナビゲーション対応
        const filterButtons = document.querySelectorAll('.riff-news-filter-btn, .riff-blog-filter-btn');
        
        filterButtons.forEach(button => {
            button.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
        
        // ARIA属性の設定
        const newsSection = document.querySelector('.riff-news-section-fullwidth');
        const blogSection = document.querySelector('.riff-blog-section-fullwidth');
        
        if (newsSection) {
            newsSection.setAttribute('role', 'region');
            newsSection.setAttribute('aria-label', 'ニュース記事一覧');
        }
        
        if (blogSection) {
            blogSection.setAttribute('role', 'region');
            blogSection.setAttribute('aria-label', 'ブログ記事一覧');
        }
    }
    
    /**
     * パフォーマンス最適化
     */
    function optimizePerformance() {
        // 画像の遅延読み込み
        const images = document.querySelectorAll('.riff-news-image-new img, .riff-blog-image-new img');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            imageObserver.unobserve(img);
                        }
                    }
                });
            });
            
            images.forEach(img => {
                if (img.dataset.src) {
                    imageObserver.observe(img);
                }
            });
        }
        
        // デバウンス処理
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        // スクロールイベントのデバウンス
        const debouncedScroll = debounce(() => {
            // スクロール時の処理があればここに追加
        }, 16);
        
        window.addEventListener('scroll', debouncedScroll);
    }
    
    // 初期化関数群の実行
    initializeSmoothScroll();
    handleResponsiveAdjustments();
    improveAccessibility();
    optimizePerformance();
    
    // エラーハンドリング
    window.addEventListener('error', function(e) {
        console.warn('News/Blog sections error:', e.error);
        // エラー時のフォールバック処理
        hideLoadingState();
    });
    
});

/**
 * ユーティリティ関数群
 */

// アニメーション完了を検知
function onAnimationEnd(element, callback) {
    element.addEventListener('animationend', callback, { once: true });
}

// CSS変数の取得
function getCSSVariable(name) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
}

// 要素が表示領域内にあるかチェック
function isElementInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// スムーズなスクロール（古いブラウザ対応）
function smoothScrollTo(target, duration = 800) {
    const targetElement = typeof target === 'string' ? document.querySelector(target) : target;
    if (!targetElement) return;
    
    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
    const startPosition = window.pageYOffset;
    const distance = targetPosition - startPosition;
    let startTime = null;
    
    function animation(currentTime) {
        if (startTime === null) startTime = currentTime;
        const timeElapsed = currentTime - startTime;
        const run = easeInOutQuad(timeElapsed, startPosition, distance, duration);
        window.scrollTo(0, run);
        if (timeElapsed < duration) requestAnimationFrame(animation);
    }
    
    function easeInOutQuad(t, b, c, d) {
        t /= d / 2;
        if (t < 1) return c / 2 * t * t + b;
        t--;
        return -c / 2 * (t * (t - 2) - 1) + b;
    }
    
    requestAnimationFrame(animation);
}