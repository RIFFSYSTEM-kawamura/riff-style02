/**
 * RIFF Theme - Main JavaScript
 * モダンなWordPressテーマ
 * バージョン: 2.0 - 修正版
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ================================
    // 定数とグローバル変数（統一版）
    // ================================
    const header = document.getElementById('header');
    const hero = document.querySelector('.hero');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNav = document.querySelector('.mobile-nav');
    
    let lastScrollTop = 0;
    let ticking = false;
    let resizeTimer; // 統一されたリサイズタイマー
    let activeTimer = null;
    let activeMegamenu = null;

    // ================================
    // ヒーロー背景動画の遅延読み込み
    // 初期表示(LCP)をブロックしないよう、poster画像を先に見せてから
    // window の load 後に実データを読み込んで再生する
    // ================================
    (function initHeroVideo() {
        const heroVideo = document.querySelector('.hero-video[data-src]');
        if (!heroVideo) return;

        function startHeroVideo() {
            const src = heroVideo.getAttribute('data-src');
            if (!src) return;
            heroVideo.removeAttribute('data-src');
            heroVideo.setAttribute('src', src);
            heroVideo.setAttribute('autoplay', '');
            heroVideo.load();
            const playPromise = heroVideo.play();
            if (playPromise && typeof playPromise.catch === 'function') {
                playPromise.catch(function() {
                    // 自動再生がブロックされた場合はposter画像のまま表示を継続
                });
            }
        }

        if (document.readyState === 'complete') {
            startHeroVideo();
        } else {
            window.addEventListener('load', startHeroVideo, { once: true });
        }
    })();

    // ================================
    // ヘッダーのスクロール効果（修正版）
    // ================================
    
    function updateHeaderOnScroll() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const heroHeight = hero ? hero.offsetHeight : 0;

        if (!header) {
            return;
        }

        // メインのスクロール処理 - 50px以上スクロールしたら背景色を表示
        if (scrollTop > 50) {
            if (!header.classList.contains('scrolled')) {
                header.classList.add('scrolled');
                if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev') || window.location.hostname.includes('riff-style02')) {
                }
            }
        } else {
            if (header.classList.contains('scrolled')) {
                header.classList.remove('scrolled');
                if (window.location.hostname === 'localhost' || window.location.hostname.includes('dev') || window.location.hostname.includes('riff-style02')) {
                }
            }
        }

        // ヘッダーの表示/非表示（スマートハイド）
        if (scrollTop > lastScrollTop && scrollTop > 200) {
            if (!mobileNav || !mobileNav.classList.contains('active')) {
                header.style.transform = 'translateY(-100%)';
            }
        } else {
            header.style.transform = 'translateY(0)';
        }

        // ヒーローセクション通過後の追加クラス
        if (scrollTop > heroHeight - 100) {
            if (!header.classList.contains('past-hero')) {
                header.classList.add('past-hero');
            }
        } else {
            if (header.classList.contains('past-hero')) {
                header.classList.remove('past-hero');
            }
        }

        lastScrollTop = scrollTop;
        ticking = false;
    }

    // スクロールイベントのスロットリング
    function onScroll() {
        if (!ticking) {
            requestAnimationFrame(updateHeaderOnScroll);
            ticking = true;
        }
    }

    // イベントリスナーの登録
    if (window.addEventListener) {
        window.addEventListener('scroll', onScroll, { passive: true });
    } else {
        window.attachEvent('onscroll', onScroll);
    }

    // 初期チェック
    setTimeout(() => {
        updateHeaderOnScroll();
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
        if (currentScroll > 50 && header && !header.classList.contains('scrolled')) {
            header.classList.add('scrolled');
        }
    }, 100);

    // リサイズとロード時の処理
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            updateHeaderOnScroll();
        }, 100);
    });

    window.addEventListener('load', function() {
        setTimeout(() => {
            updateHeaderOnScroll();
            const finalScrollCheck = window.pageYOffset || document.documentElement.scrollTop;
            if (header) {
                if (finalScrollCheck > 50) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            }
        }, 200);
    });

    // デバッグ関数
    window.debugHeaderScroll = function() {
        if (!header) {
            console.error('❌ Header element not found');
            return;
        }
        
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const hasScrolledClass = header.classList.contains('scrolled');
        const computedStyle = window.getComputedStyle(header);
        
        
        const root = document.documentElement;
        const rootStyle = window.getComputedStyle(root);
    };

    window.checkCSSVariables = function() {
        const root = document.documentElement;
        const computedStyle = window.getComputedStyle(root);
        
    };
    
    // ================================
    // メガメニューの制御
    // ================================
    const megamenuItems = document.querySelectorAll('.has-megamenu');
    
    if (megamenuItems.length > 0) {
        megamenuItems.forEach(item => {
            const megamenuContainer = item.querySelector('.megamenu-container');
            
            if (!megamenuContainer) return;
            
            item.addEventListener('mouseenter', function() {
                clearTimeout(activeTimer);
                
                if (activeMegamenu && activeMegamenu !== item) {
                    hideMegamenu(activeMegamenu);
                }
                
                activeMegamenu = item;
                showMegamenu(item);
            });
            
            item.addEventListener('mouseleave', function() {
                activeTimer = setTimeout(() => {
                    hideMegamenu(item);
                    if (activeMegamenu === item) {
                        activeMegamenu = null;
                    }
                }, 100);
            });
            
            megamenuContainer.addEventListener('mouseenter', function() {
                clearTimeout(activeTimer);
            });
            
            megamenuContainer.addEventListener('mouseleave', function() {
                activeTimer = setTimeout(() => {
                    hideMegamenu(item);
                    if (activeMegamenu === item) {
                        activeMegamenu = null;
                    }
                }, 100);
            });
        });
    }
    
    function showMegamenu(item) {
        const container = item.querySelector('.megamenu-container');
        if (!container) return;
        
        container.style.opacity = '1';
        container.style.visibility = 'visible';
        container.style.transform = 'translateX(-50%) translateY(0)';
        
        adjustMegamenuPosition(container);
        
        item.setAttribute('aria-expanded', 'true');
        container.setAttribute('aria-hidden', 'false');
    }
    
    function hideMegamenu(item) {
        const container = item.querySelector('.megamenu-container');
        if (!container) return;
        
        container.style.opacity = '0';
        container.style.visibility = 'hidden';
        container.style.transform = 'translateX(-50%) translateY(10px)';
        
        item.setAttribute('aria-expanded', 'false');
        container.setAttribute('aria-hidden', 'true');
    }
    
    function adjustMegamenuPosition(container) {
        const rect = container.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        
        if (rect.right > viewportWidth - 20) {
            const offset = rect.right - viewportWidth + 20;
            container.style.transform = `translateX(calc(-50% - ${offset}px)) translateY(0)`;
        }
        
        if (rect.left < 20) {
            const offset = 20 - rect.left;
            container.style.transform = `translateX(calc(-50% + ${offset}px)) translateY(0)`;
        }
    }
    // ================================
    // モバイルメニューの動作（参考サイト準拠・黒デザイン版）
    // ================================
    if (mobileMenuToggle && mobileNav) {
        // メニューを開く関数
        function openMobileMenu() {
            mobileMenuToggle.classList.add('active');
            mobileNav.classList.add('active');
            mobileMenuToggle.setAttribute('aria-expanded', 'true');
            mobileNav.setAttribute('aria-hidden', 'false');
            document.body.classList.add('mobile-menu-open');
            document.body.style.overflow = 'hidden';

            // 開くたびにサブメニューを必ず閉じた状態にリセットする
            mobileNav.querySelectorAll('.mobile-submenu').forEach(sm => {
                sm.style.setProperty('display', 'none', 'important');
            });
            mobileNav.querySelectorAll('.mobile-submenu-toggle.active').forEach(tg => {
                tg.classList.remove('active');
            });
            
            // フォーカス管理
            setTimeout(() => {
                const firstLink = mobileNav.querySelector('.mobile-menu-link, .mobile-nav-close');
                if (firstLink) {
                    firstLink.focus();
                }
            }, 300);
        }
        
        // メニューを閉じる関数
        function closeMobileMenu() {
            mobileMenuToggle.classList.remove('active');
            mobileNav.classList.remove('active');
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
            mobileNav.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('mobile-menu-open');
            document.body.style.overflow = '';
            
            // すべてのサブメニューを閉じる
            mobileNav.querySelectorAll('.mobile-submenu').forEach(submenu => {
                submenu.style.setProperty('display', 'none', 'important');
            });
            
            const activeToggles = mobileNav.querySelectorAll('.mobile-submenu-toggle.active');
            activeToggles.forEach(toggle => {
                toggle.classList.remove('active');
            });
            
            // フォーカスをハンバーガーボタンに戻す
            mobileMenuToggle.focus();
        }
        
        // ハンバーガーボタンクリック
        mobileMenuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isActive = this.classList.contains('active');
            if (isActive) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
        
        // 閉じるボタンクリック
        const mobileNavClose = mobileNav.querySelector('.mobile-nav-close');
        if (mobileNavClose) {
            mobileNavClose.addEventListener('click', function(e) {
                e.preventDefault();
                closeMobileMenu();
            });
        }
        
        // オーバーレイクリック
        const mobileNavOverlay = mobileNav.querySelector('.mobile-nav-overlay');
        if (mobileNavOverlay) {
            mobileNavOverlay.addEventListener('click', function(e) {
                e.preventDefault();
                closeMobileMenu();
            });
        }
        
        // サブメニューの開閉（インラインstyleを唯一の判定基準にする。
        // 外部CSSの!importantカスケードに依存すると崩れやすいため直接操作する）
        function closeOtherSubmenus(exceptSubmenu) {
            mobileNav.querySelectorAll('.mobile-submenu').forEach(sm => {
                if (sm !== exceptSubmenu) {
                    sm.style.setProperty('display', 'none', 'important');
                    const otherToggle = sm.closest('li').querySelector('.mobile-submenu-toggle');
                    if (otherToggle) otherToggle.classList.remove('active');
                }
            });
        }

        function toggleSubmenu(submenu, toggle) {
            const isOpen = submenu.style.display === 'flex';
            closeOtherSubmenus(submenu);
            if (isOpen) {
                submenu.style.setProperty('display', 'none', 'important');
                if (toggle) toggle.classList.remove('active');
            } else {
                submenu.style.setProperty('display', 'flex', 'important');
                if (toggle) toggle.classList.add('active');
            }
        }

        // メニューリンククリック時
        const navLinks = mobileNav.querySelectorAll('.mobile-menu-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // サブメニューがある場合は展開制御
                const parentLi = this.closest('li');
                const submenu = parentLi.querySelector('.mobile-submenu');
                const toggle = parentLi.querySelector('.mobile-submenu-toggle');

                if (submenu && toggle) {
                    // サブメニューがある場合はリンクを無効化して展開制御
                    e.preventDefault();
                    toggleSubmenu(submenu, toggle);
                } else {
                    // サブメニューがない場合はメニューを閉じる
                    closeMobileMenu();
                }
            });
        });

        // サブメニュートグル機能
        const submenuToggles = mobileNav.querySelectorAll('.mobile-submenu-toggle');
        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const submenu = this.closest('li').querySelector('.mobile-submenu');
                if (submenu) {
                    toggleSubmenu(submenu, this);
                }
            });
        });
        
        // サブメニューのリンククリック時
        const submenuLinks = mobileNav.querySelectorAll('.mobile-submenu .mobile-menu-link');
        submenuLinks.forEach(link => {
            link.addEventListener('click', function() {
                closeMobileMenu();
            });
        });
        
        // キーボードナビゲーション
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileNav.classList.contains('active')) {
                closeMobileMenu();
            }
            
            // タブキーでのフォーカストラップ
            if (e.key === 'Tab' && mobileNav.classList.contains('active')) {
                const focusableElements = mobileNav.querySelectorAll(
                    'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
                );
                const firstElement = focusableElements[0];
                const lastElement = focusableElements[focusableElements.length - 1];
                
                if (e.shiftKey && document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                } else if (!e.shiftKey && document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        });
        
        // リサイズ時の処理
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && mobileNav.classList.contains('active')) {
                closeMobileMenu();
            }
        });
        
        // 外部関数として定義（他の場所で使用される場合）
        window.closeMobileMenu = closeMobileMenu;
    }
    
    // ================================
    // スムーススクロール
    // ================================
    const smoothScrollLinks = document.querySelectorAll('a[href^="#"]:not([href="#"])');
    
    smoothScrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                e.preventDefault();
                
                const headerHeight = header ? header.offsetHeight : 0;
                const targetPosition = targetElement.offsetTop - headerHeight - 20;
                
                smoothScrollTo(targetPosition, 1000);
                closeMobileMenu();
            }
        });
    });
    
    function smoothScrollTo(targetPosition, duration) {
        const startPosition = window.pageYOffset;
        const distance = targetPosition - startPosition;
        let startTime = null;
        
        function animation(currentTime) {
            if (startTime === null) startTime = currentTime;
            const timeElapsed = currentTime - startTime;
            const run = easeInOutQuart(timeElapsed, startPosition, distance, duration);
            window.scrollTo(0, run);
            if (timeElapsed < duration) requestAnimationFrame(animation);
        }
        
        function easeInOutQuart(t, b, c, d) {
            t /= d / 2;
            if (t < 1) return c / 2 * t * t * t * t + b;
            t -= 2;
            return -c / 2 * (t * t * t * t - 2) + b;
        }
        
        requestAnimationFrame(animation);
    }
    
    // ================================
    // IntersectionObserver による要素監視
    // ================================
    
    document.body.classList.add('js');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    if ('IntersectionObserver' in window) {
        const intersectionObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    intersectionObserver.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        const fadeInElements = document.querySelectorAll('.fade-in');
        fadeInElements.forEach(element => {
            if (!element.closest('.hero')) {
                intersectionObserver.observe(element);
            }
        });
        
        setTimeout(() => {
            const hiddenElements = document.querySelectorAll('.fade-in:not(.visible):not(.hero .fade-in)');
            hiddenElements.forEach(element => {
                element.classList.add('visible', 'fade-in-fallback');
            });
        }, 3000);
        
    } else {
        const fadeInElements = document.querySelectorAll('.fade-in');
        fadeInElements.forEach(element => {
            if (!element.closest('.hero')) {
                element.classList.add('visible', 'fade-in-fallback');
            }
        });
    }
    
    window.addEventListener('load', function() {
        setTimeout(() => {
            const stillHiddenElements = document.querySelectorAll('.fade-in:not(.visible):not(.hero .fade-in)');
            if (stillHiddenElements.length > 0) {
                stillHiddenElements.forEach(element => {
                    element.classList.add('visible', 'fade-in-fallback');
                });
            }
        }, 1000);
    });
    
    // ================================
    // ヒーローセクションの動画制御
    // ================================
    const heroVideo = document.querySelector('.hero-video');
    if (heroVideo) {
        heroVideo.muted = true;
        heroVideo.playsInline = true;
        
        heroVideo.addEventListener('loadeddata', function() {
            this.classList.add('loaded');
        });
        
        heroVideo.addEventListener('error', function() {
            const fallbackImage = this.nextElementSibling;
            if (fallbackImage && fallbackImage.classList.contains('hero-video-fallback')) {
                fallbackImage.style.display = 'block';
            }
        });
        
        if ('IntersectionObserver' in window) {
            const videoObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    const video = entry.target;
                    if (!video || typeof video.play !== 'function') return;
                    // data-srcが残っている間(＝遅延読み込み前)はまだ再生対象がないため何もしない
                    if (video.hasAttribute('data-src')) return;
                    if (entry.isIntersecting) {
                        const playPromise = video.play();
                        if (playPromise && typeof playPromise.catch === 'function') {
                            playPromise.catch(function() {});
                        }
                    } else if (typeof video.pause === 'function') {
                        video.pause();
                    }
                });
            }, { threshold: 0.1 });

            videoObserver.observe(heroVideo);
        }
    }
    
    // ================================
    // ヒーローセクションのパララックス効果
    // ================================
    const heroBg = document.querySelector('.hero-bg-image, .hero-video');
    
    if (hero && heroBg) {
        // heroの高さはスクロール中は変化しないため、スクロールイベントの度に
        // offsetHeightを読み取らず、表示開始時に一度だけキャッシュする（強制リフロー回避）
        let cachedHeroHeight = hero.offsetHeight;
        let parallaxTicking = false;

        function applyParallax() {
            const scrolled = window.pageYOffset;
            if (scrolled < cachedHeroHeight) {
                const speed = scrolled * 0.3;
                heroBg.style.transform = `translate3d(0, ${speed}px, 0)`;
            }
            parallaxTicking = false;
        }

        function updateParallax() {
            if (!parallaxTicking) {
                requestAnimationFrame(applyParallax);
                parallaxTicking = true;
            }
        }

        if ('IntersectionObserver' in window) {
            const heroObserver = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        cachedHeroHeight = hero.offsetHeight;
                        window.addEventListener('scroll', updateParallax, { passive: true });
                    } else {
                        window.removeEventListener('scroll', updateParallax);
                    }
                });
            }, { threshold: 0 });

            heroObserver.observe(hero);
        }

        window.addEventListener('resize', function() {
            cachedHeroHeight = hero.offsetHeight;
        }, { passive: true });
    }
    
    // ================================
    // カウンターアニメーション
    // ================================
    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-target')) || 0;
        const duration = parseInt(element.getAttribute('data-duration')) || 2000;
        const startValue = 0;
        const increment = target / (duration / 16);
        
        let current = startValue;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current).toLocaleString();
        }, 16);
    }
    
    const counterElements = document.querySelectorAll('.counter');
    if (counterElements.length > 0 && 'IntersectionObserver' in window) {
        const counterObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        counterElements.forEach(counter => {
            counterObserver.observe(counter);
        });
    }
    
    // ================================
    // フォームのバリデーション
    // ================================
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                const firstError = form.querySelector('.error');
                if (firstError) {
                    firstError.focus();
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
        
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required')) {
                    validateField(this);
                }
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    validateField(this);
                }
            });
        });
    });
    
    function validateField(field) {
        const value = field.value.trim();
        const type = field.type;
        let isValid = true;
        let errorMessage = '';
        
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'この項目は必須です。';
        }
        
        if (type === 'email' && value) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(value)) {
                isValid = false;
                errorMessage = '正しいメールアドレスを入力してください。';
            }
        }
        
        if (type === 'tel' && value) {
            const telPattern = /^[\d\-\(\)\+\s]+$/;
            if (!telPattern.test(value)) {
                isValid = false;
                errorMessage = '正しい電話番号を入力してください。';
            }
        }
        
        updateFieldValidation(field, isValid, errorMessage);
        return isValid;
    }
    
    function updateFieldValidation(field, isValid, errorMessage) {
        const errorClass = 'error';
        const errorMsgClass = 'error-message';
        
        if (isValid) {
            field.classList.remove(errorClass);
            const errorMsg = field.parentNode.querySelector(`.${errorMsgClass}`);
            if (errorMsg) {
                errorMsg.remove();
            }
        } else {
            field.classList.add(errorClass);
            
            let errorMsg = field.parentNode.querySelector(`.${errorMsgClass}`);
            if (!errorMsg) {
                errorMsg = document.createElement('span');
                errorMsg.className = errorMsgClass;
                errorMsg.style.cssText = `
                    color: var(--danger-color);
                    font-size: 14px;
                    display: block;
                    margin-top: 5px;
                `;
                field.parentNode.appendChild(errorMsg);
            }
            errorMsg.textContent = errorMessage;
        }
    }
    
    // ================================
    // 画像の遅延読み込み
    // ================================
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    if (lazyImages.length > 0 && 'IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    
                    img.src = img.dataset.src;
                    img.classList.add('loading');
                    
                    img.addEventListener('load', function() {
                        this.classList.remove('lazy', 'loading');
                        this.classList.add('loaded');
                    });
                    
                    img.addEventListener('error', function() {
                        this.classList.remove('lazy', 'loading');
                        this.classList.add('error');
                    });
                    
                    imageObserver.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px'
        });
        
        lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // ================================
    // ツールチップ機能
    // ================================
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        let tooltip = null;
        
        element.addEventListener('mouseenter', function() {
            showTooltip(this);
        });
        
        element.addEventListener('mouseleave', function() {
            hideTooltip();
        });
        
        element.addEventListener('focus', function() {
            showTooltip(this);
        });
        
        element.addEventListener('blur', function() {
            hideTooltip();
        });
        
        function showTooltip(el) {
            hideTooltip();
            
            tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = el.getAttribute('data-tooltip');
            tooltip.style.cssText = `
                position: absolute;
                background: rgba(0, 0, 0, 0.9);
                color: white;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 14px;
                white-space: nowrap;
                z-index: 10000;
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.2s ease;
            `;
            
            document.body.appendChild(tooltip);
            
            const rect = el.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();
            
            let left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
            let top = rect.top - tooltipRect.height - 8;
            
            if (left < 0) left = 8;
            if (left + tooltipRect.width > window.innerWidth) {
                left = window.innerWidth - tooltipRect.width - 8;
            }
            if (top < 0) {
                top = rect.bottom + 8;
            }
            
            tooltip.style.left = left + window.pageXOffset + 'px';
            tooltip.style.top = top + window.pageYOffset + 'px';
            
            requestAnimationFrame(() => {
                tooltip.style.opacity = '1';
            });
        }
        
        function hideTooltip() {
            if (tooltip) {
                tooltip.remove();
                tooltip = null;
            }
        }
    });
    
    // ================================
    // キーボードナビゲーション対応
    // ================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeMobileMenu();
            
            if (activeMegamenu) {
                hideMegamenu(activeMegamenu);
                activeMegamenu = null;
            }
            
            const tooltips = document.querySelectorAll('.tooltip');
            tooltips.forEach(tooltip => tooltip.remove());
        }
        
        if (e.key === 'Tab' && mobileNav && mobileNav.classList.contains('active')) {
            const focusableElements = mobileNav.querySelectorAll(
                'a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])'
            );
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            if (e.shiftKey && document.activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            } else if (!e.shiftKey && document.activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        }
    });
    
    // ================================
    // フロントページセクション機能
    // ================================
    
    // NEWSセクションのフィルター機能
    function initNewsFilter() {
        const filterButtons = document.querySelectorAll('.riff-news-filter-btn');
        const newsItems = document.querySelectorAll('.riff-news-item');
        
        if (filterButtons.length > 0 && newsItems.length > 0) {
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    newsItems.forEach((item, index) => {
                        const category = item.getAttribute('data-category');
                        
                        if (filter === 'all' || category === filter) {
                            item.style.display = 'block';
                            setTimeout(() => {
                                item.classList.add('visible');
                            }, index * 50);
                        } else {
                            item.style.display = 'none';
                            item.classList.remove('visible');
                        }
                    });
                });
            });
        }
    }
    
    // SERVICEカードのホバー効果
    function initServiceHover() {
        const serviceCards = document.querySelectorAll('.riff-service-card');
        
        serviceCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                const overlay = this.querySelector('.riff-service-overlay');
                if (overlay) {
                    overlay.style.transform = 'translateY(-10px)';
                }
            });
            
            card.addEventListener('mouseleave', function() {
                const overlay = this.querySelector('.riff-service-overlay');
                if (overlay) {
                    overlay.style.transform = 'translateY(0)';
                }
            });
        });
    }
    
    // ================================
	// サービスカードのグラデーションオーバーレイ（修正版）
	// ================================
	function initServiceCardOverlays() {

		const serviceCards = document.querySelectorAll('.riff-service-card-new');
		const gradientOverlays = document.querySelectorAll('.riff-service-gradient-overlay');


		// 基本的なホバー効果をまず適用
		serviceCards.forEach((card, index) => {

			// 基本的なホバー効果
			card.addEventListener('mouseenter', function() {
				const wrapper = this.querySelector('.riff-service-wrapper');
				const image = this.querySelector('.riff-service-image');
				const title = this.querySelector('.riff-service-card-title');
				const arrow = this.querySelector('.riff-service-arrow');

				if (wrapper) {
					wrapper.style.transform = 'translateY(-12px)';
					wrapper.style.boxShadow = '0 20px 60px rgba(0, 0, 0, 0.15)';
				}

				if (image) {
					image.style.transform = 'scale(1.08)';
				}

				if (title) {
					title.style.textShadow = '0 3px 12px rgba(0, 0, 0, 0.5)';
					title.style.transform = 'translateY(-5px)';
				}

				if (arrow) {
					arrow.style.transform = 'translateY(-5px) scale(1.1)';
				}
			});

			card.addEventListener('mouseleave', function() {
				const wrapper = this.querySelector('.riff-service-wrapper');
				const image = this.querySelector('.riff-service-image');
				const title = this.querySelector('.riff-service-card-title');
				const arrow = this.querySelector('.riff-service-arrow');

				if (wrapper) {
					wrapper.style.transform = 'translateY(0)';
					wrapper.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.12)';
				}

				if (image) {
					image.style.transform = 'scale(1)';
				}

				if (title) {
					title.style.textShadow = '0 2px 8px rgba(0, 0, 0, 0.3)';
					title.style.transform = 'translateY(0)';
				}

				if (arrow) {
					arrow.style.transform = 'translateY(0) scale(1)';
				}
			});
		});

		// グラデーションオーバーレイの設定
		gradientOverlays.forEach((overlay, index) => {
			const color = overlay.getAttribute('data-color');
			const opacity = overlay.getAttribute('data-opacity');


			if (color && opacity) {
				function hexToRgba(hex, alpha) {
					// # を除去
					hex = hex.replace('#', '');

					// 3文字の場合は6文字に展開
					if (hex.length === 3) {
						hex = hex.split('').map(char => char + char).join('');
					}

					const r = parseInt(hex.slice(0, 2), 16);
					const g = parseInt(hex.slice(2, 4), 16);
					const b = parseInt(hex.slice(4, 6), 16);
					return `rgba(${r}, ${g}, ${b}, ${alpha})`;
				}

				const opacityPercent = Math.max(0, Math.min(100, parseInt(opacity) || 80));

				// 通常時のグラデーション
				const alpha80 = opacityPercent / 100;
				const alpha60 = Math.max(0, (opacityPercent - 20) / 100);
				const alpha30 = Math.max(0, (opacityPercent - 50) / 100);

				// ホバー時のグラデーション
				const alpha90 = Math.min(1, (opacityPercent + 10) / 100);
				const alpha70 = Math.max(0, (opacityPercent - 10) / 100);
				const alpha40 = Math.max(0, (opacityPercent - 40) / 100);

				const normalGradient = `linear-gradient(
					to top,
					${hexToRgba(color, alpha80)} 0%,
					${hexToRgba(color, alpha60)} 30%,
					${hexToRgba(color, alpha30)} 70%,
					transparent 100%
				)`;

				const hoverGradient = `linear-gradient(
					to top,
					${hexToRgba(color, alpha90)} 0%,
					${hexToRgba(color, alpha70)} 30%,
					${hexToRgba(color, alpha40)} 70%,
					transparent 100%
				)`;

				// 初期設定
				overlay.style.background = normalGradient;
				overlay.classList.add('colored');

				// 親カードを取得
				const serviceCard = overlay.closest('.riff-service-card-new');
				if (serviceCard) {

					serviceCard.addEventListener('mouseenter', function() {
						overlay.style.background = hoverGradient;
						overlay.style.height = '45%';
						overlay.style.transition = 'all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
					});

					serviceCard.addEventListener('mouseleave', function() {
						overlay.style.background = normalGradient;
						overlay.style.height = '35%';
						overlay.style.transition = 'all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
					});
				} else {
				}
			} else {
			}
		});

	}
    
    // ================================
    // Footer-Upセクション
    // ================================
    function initFooterUpSection() {
        const footerUpSection = document.querySelector('.riff-footer-up-section');
        if (!footerUpSection) return;
        
        const footerUpButtons = footerUpSection.querySelectorAll('.riff-footer-up-cta-button');
        
        if (footerUpButtons.length === 0) return;
        
        footerUpButtons.forEach((button, index) => {
            // ホバー効果
            button.addEventListener('mouseenter', function() {
                this.style.transition = 'all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                this.setAttribute('aria-expanded', 'true');
            });

            button.addEventListener('mouseleave', function() {
                this.style.transition = 'all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                this.setAttribute('aria-expanded', 'false');
            });

            // クリック時のリップル効果
            button.addEventListener('click', function(e) {
                const existingRipple = this.querySelector('.ripple-effect');
                if (existingRipple) {
                    existingRipple.remove();
                }

                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height) * 0.6;
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.className = 'ripple-effect';
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255, 255, 255, 0.3);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple-animation 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                    pointer-events: none;
                    z-index: 10;
                `;

                this.appendChild(ripple);

                setTimeout(() => {
                    if (ripple.parentNode) {
                        ripple.remove();
                    }
                }, 800);
            });
            
            // キーボードナビゲーション
            button.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const clickEvent = new MouseEvent('click', {
                        bubbles: true,
                        cancelable: true,
                        clientX: this.getBoundingClientRect().left + this.offsetWidth / 2,
                        clientY: this.getBoundingClientRect().top + this.offsetHeight / 2
                    });
                    this.dispatchEvent(clickEvent);
                    
                    setTimeout(() => {
                        if (this.href) {
                            window.location.href = this.href;
                        }
                    }, 200);
                }

                if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    const nextButton = footerUpButtons[index + 1] || footerUpButtons[0];
                    nextButton.focus();
                }

                if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prevButton = footerUpButtons[index - 1] || footerUpButtons[footerUpButtons.length - 1];
                    prevButton.focus();
                }
            });
        });
        
        // アニメーション用のCSS追加
        if (!document.querySelector('#footer-up-animations')) {
            const style = document.createElement('style');
            style.id = 'footer-up-animations';
            style.textContent = `
                @keyframes ripple-animation {
                    to {
                        transform: scale(3);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        // IntersectionObserverによるアニメーション
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animation = 'fade-in 1.2s ease forwards';
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            observer.observe(footerUpSection);
        }
    }
    
    // ================================
    // モバイルオーバーレイナビゲーション
    // ================================
    function initMobileOverlay() {
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const mobileOverlay = document.getElementById('riff-mobile-overlay');
        const mobileClose = document.querySelector('.riff-mobile-close');
        
        if (!mobileToggle || !mobileOverlay) return;
        
        function openMobileMenu() {
            mobileOverlay.classList.add('active');
            mobileToggle.classList.add('active');
            mobileOverlay.setAttribute('aria-hidden', 'false');
            
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
            
            setTimeout(() => {
                const firstFocusable = mobileOverlay.querySelector('a, button');
                if (firstFocusable) {
                    firstFocusable.focus();
                }
            }, 300);
        }
        
        function closeMobileOverlay() {
            mobileOverlay.classList.remove('active');
            mobileToggle.classList.remove('active');
            mobileOverlay.setAttribute('aria-hidden', 'true');
            
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
            
            mobileToggle.focus();
        }
        
        mobileToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (mobileOverlay.classList.contains('active')) {
                closeMobileOverlay();
            } else {
                openMobileMenu();
            }
        });
        
        if (mobileClose) {
            mobileClose.addEventListener('click', function(e) {
                e.preventDefault();
                closeMobileOverlay();
            });
        }
        
        mobileOverlay.addEventListener('click', function(e) {
            if (e.target === mobileOverlay) {
                closeMobileOverlay();
            }
        });
        
        const mobileMenuLinks = mobileOverlay.querySelectorAll('.riff-mobile-menu a');
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', function() {
                closeMobileOverlay();
            });
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileOverlay.classList.contains('active')) {
                closeMobileOverlay();
            }
        });
        
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && mobileOverlay.classList.contains('active')) {
                closeMobileOverlay();
            }
        });
        
        mobileOverlay.setAttribute('aria-hidden', 'true');
    }
    
    // ================================
    // 統合リサイズハンドラー
    // ================================
    function handleResize() {
        updateHeaderOnScroll();
        
        if (activeMegamenu) {
            const container = activeMegamenu.querySelector('.megamenu-container');
            if (container && container.style.opacity === '1') {
                adjustMegamenuPosition(container);
            }
        }
        
        if (window.innerWidth > 768) {
            closeMobileMenu();
        }
        
        const heroVideo = document.querySelector('.hero-video');
        if (heroVideo) {
            adjustVideoAspectRatio(heroVideo);
        }
    }
    
    function adjustVideoAspectRatio(videoElement) {
        if (!videoElement) return;
        
        const container = videoElement.parentElement;
        if (!container) return;
        
        const containerAspect = container.offsetWidth / container.offsetHeight;
        const videoAspect = videoElement.videoWidth / videoElement.videoHeight;
        
        if (containerAspect > videoAspect) {
            videoElement.style.width = '100%';
            videoElement.style.height = 'auto';
        } else {
            videoElement.style.width = 'auto';
            videoElement.style.height = '100%';
        }
    }
    
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(handleResize, 250);
    });
    
    // ================================
	// 初期化関数の実行（修正版）
	// ================================
	function initAllFeatures() {

		// DOM要素の存在確認
		const serviceSection = document.querySelector('.riff-service-section-fullwidth');
		const serviceCards = document.querySelectorAll('.riff-service-card-new');


		// 各機能を順次初期化
		try {
			initNewsFilter();
		} catch (error) {
		}

		try {
			initServiceHover();
		} catch (error) {
		}

		try {
			initServiceCardOverlays();
		} catch (error) {
		}

		try {
			initFooterUpSection();
		} catch (error) {
		}

		try {
			initMobileOverlay();
		} catch (error) {
		}

		// デバッグ用：ホバーテスト機能を追加
		if (serviceCards.length > 0) {
			addDebugHoverTest();
		}

	}

	// デバッグ用：ホバーテスト機能
	function addDebugHoverTest() {
		const serviceCards = document.querySelectorAll('.riff-service-card-new');

		serviceCards.forEach((card, index) => {
			// プログラム的にホバー状態をテスト
			const testHover = () => {
				card.classList.add('hover');

				setTimeout(() => {
					card.classList.remove('hover');
				}, 2000);
			};

			// カードをダブルクリックでホバーテスト
			card.addEventListener('dblclick', function(e) {
				e.preventDefault();
				testHover();
			});

			// 追加のイベントリスナーでデバッグ
			card.addEventListener('mouseenter', function() {
			});

			card.addEventListener('mouseleave', function() {
			});
		});

		// グローバル関数として追加（コンソールから呼び出し可能）
		window.testServiceHover = function(cardIndex = 0) {
			const cards = document.querySelectorAll('.riff-service-card-new');
			if (cards[cardIndex]) {
				cards[cardIndex].classList.add('hover');

				setTimeout(() => {
					cards[cardIndex].classList.remove('hover');
				}, 3000);
			} else {
				console.error(`Card ${cardIndex + 1} not found`);
			}
		};

	}

	// より確実な初期化のために複数のタイミングで実行
	function ensureInitialization() {
		let initCount = 0;
		const maxAttempts = 3;

		function attemptInit() {
			initCount++;

			const serviceCards = document.querySelectorAll('.riff-service-card-new');

			if (serviceCards.length > 0 || initCount >= maxAttempts) {
				initAllFeatures();
			} else {
				setTimeout(attemptInit, 500);
			}
		}

		attemptInit();
	}

	// 初期化実行（修正版）
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', ensureInitialization);
	} else {
		ensureInitialization();
	}

	// 追加の初期化タイミング
	window.addEventListener('load', function() {

		setTimeout(() => {
			const serviceCards = document.querySelectorAll('.riff-service-card-new');
			if (serviceCards.length > 0) {
				initServiceCardOverlays();
			}
		}, 1000);
	});
    
    // ページ可視性が変わった時の処理
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            const visibleImages = document.querySelectorAll('.riff-service-image img, .riff-news-image img, .riff-blog-image img');
            visibleImages.forEach(img => {
                if (!img.complete) {
                    img.src = img.src;
                }
            });
        }
    });
    
    // ================================
    // パフォーマンス監視
    // ================================
    if ('performance' in window) {
        window.addEventListener('load', function() {
            setTimeout(() => {
                const perfData = performance.getEntriesByType('navigation')[0];
                if (perfData) {
                }
            }, 0);
        });
    }
    
    // ================================
    // 初期化完了
    // ================================
    document.body.classList.add('riff-loaded');
    
    
});

// ================================
// ユーティリティ関数（グローバル）
// ================================

function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

function debounce(func, wait, immediate = false) {
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

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

function preloadAnimations() {
    const style = document.createElement('style');
    style.textContent = `
        .fade-in { transition: all 0.8s ease; }
        .hero-button { transition: all 0.3s ease; }
        .service-item { transition: all 0.3s ease; }
    `;
    document.head.appendChild(style);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', preloadAnimations);
} else {
    preloadAnimations();
}