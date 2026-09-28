/**
 * BLOG スライダー機能
 * Blog Slider Functionality
 */

(function() {
    'use strict';

    // スライダー設定
    const config = {
        slidesToShow: {
            desktop: 3,
            tablet: 2,
            mobile: 1
        },
        slidesToScroll: 1,
        animationDuration: 600,
        autoPlay: false,
        autoPlayInterval: 5000,
        pauseOnHover: true,
        enableSwipe: true,
        enableKeyboard: true,
        easeFunction: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
    };

    let sliders = [];

    /**
     * スライダークラス
     */
    class BlogSlider {
        constructor(container) {
            this.container = container;
            this.track = container.querySelector('.riff-blog-slider-track');
            this.slides = Array.from(container.querySelectorAll('.riff-blog-slide'));
            this.prevBtn = container.querySelector('.riff-blog-slider-prev');
            this.nextBtn = container.querySelector('.riff-blog-slider-next');
            
            // 既存のドットを取得（重複防止）
            this.dotsContainer = document.querySelector('.riff-blog-slider-dots');
            this.dots = this.dotsContainer ? Array.from(this.dotsContainer.querySelectorAll('.riff-blog-slider-dot')) : [];
            
            this.currentIndex = 0;
            this.slidesToShow = this.getSlidesToShow();
            this.totalSlides = this.slides.length;
            this.maxIndex = Math.max(0, this.totalSlides - this.slidesToShow);
            this.isAnimating = false;
            this.autoPlayTimer = null;
            this.startX = 0;
            this.currentX = 0;
            this.isDragging = false;
            this.hasDragged = false;
            this.linkMouseDown = false;
            this.linkStartX = 0;
            this.linkStartY = 0;

            this.init();
        }

        /**
         * 初期化
         */
        init() {
            if (this.totalSlides === 0) return;

            this.setupSlides();
            this.bindEvents();
            this.updateNavigation();
            this.updateDots();

            if (config.autoPlay) {
                this.startAutoPlay();
            }

            // ウィンドウリサイズ時の対応
            this.handleResize = this.debounce(() => {
                const oldSlidesToShow = this.slidesToShow;
                this.slidesToShow = this.getSlidesToShow();
                
                // 表示数が変わった場合のみ再計算
                if (oldSlidesToShow !== this.slidesToShow) {
                    this.maxIndex = Math.max(0, this.totalSlides - this.slidesToShow);
                    this.currentIndex = Math.min(this.currentIndex, this.maxIndex);
                    this.setupSlides();
                    this.updateSlider(false);
                    this.updateNavigation();
                    this.updateDots();
                }
            }, 250);

            window.addEventListener('resize', this.handleResize);

            // 初期表示のアニメーション
            setTimeout(() => {
                this.container.classList.add('slider-initialized');
                
                // ドットナビゲーションを確実に表示
                if (this.dotsContainer) {
                    this.dotsContainer.style.display = 'flex';
                    this.dotsContainer.style.visibility = 'visible';
                }
            }, 100);

            console.log(`Blog Slider initialized: ${this.totalSlides} slides, ${this.slidesToShow} visible, ${this.maxIndex + 1} pages`);
        }

        /**
         * スライドの設定
         */
        setupSlides() {
            const slideWidth = 100 / this.slidesToShow;
            
            this.slides.forEach((slide, index) => {
                slide.style.width = `${slideWidth}%`;
                slide.style.flex = `0 0 ${slideWidth}%`;
                slide.style.minWidth = `${slideWidth}%`;
                slide.style.maxWidth = `${slideWidth}%`;
                
                // アクセシビリティ属性
                slide.setAttribute('aria-hidden', index >= this.slidesToShow ? 'true' : 'false');
            });

            // トラックの幅を設定（総スライド数に基づく）
            const totalWidth = (this.totalSlides / this.slidesToShow) * 100;
            this.track.style.width = `${totalWidth}%`;
            
            // flexboxの設定を強制
            this.track.style.display = 'flex';
            this.track.style.flexWrap = 'nowrap';
            this.track.style.alignItems = 'stretch';
        }

        /**
         * 現在の画面サイズに応じた表示スライド数を取得
         */
        getSlidesToShow() {
            const width = window.innerWidth;
            
            if (width <= 767) {
                return config.slidesToShow.mobile;
            } else if (width <= 1024) {
                return config.slidesToShow.tablet;
            } else {
                return config.slidesToShow.desktop;
            }
        }

        /**
         * イベントリスナーの設定
         */
        bindEvents() {
            // ナビゲーションボタン
            if (this.prevBtn) {
                this.prevBtn.addEventListener('click', () => this.prevSlide());
            }
            
            if (this.nextBtn) {
                this.nextBtn.addEventListener('click', () => this.nextSlide());
            }

            // ドットナビゲーション
            this.dots.forEach((dot, index) => {
                dot.addEventListener('click', () => this.goToSlide(index));
            });

            // キーボードナビゲーション
            if (config.enableKeyboard) {
                this.container.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowLeft') {
                        e.preventDefault();
                        this.prevSlide();
                    } else if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        this.nextSlide();
                    }
                });
                
                // フォーカス可能にする
                this.container.setAttribute('tabindex', '0');
            }

            // 自動再生の一時停止/再開
            if (config.autoPlay && config.pauseOnHover) {
                this.container.addEventListener('mouseenter', () => this.stopAutoPlay());
                this.container.addEventListener('mouseleave', () => this.startAutoPlay());
            }

            // スワイプ機能（モバイル）
            if (config.enableSwipe) {
                this.bindSwipeEvents();
            }
        }

        /**
         * スワイプイベントの設定
         */
        bindSwipeEvents() {
            // タッチイベント
            this.track.addEventListener('touchstart', (e) => this.handleSwipeStart(e), { passive: true });
            this.track.addEventListener('touchmove', (e) => this.handleSwipeMove(e), { passive: false });
            this.track.addEventListener('touchend', (e) => this.handleSwipeEnd(e), { passive: true });

            // マウスイベント（デスクトップでのドラッグ対応）
            this.track.addEventListener('mousedown', (e) => this.handleSwipeStart(e));
            this.track.addEventListener('mousemove', (e) => this.handleSwipeMove(e));
            this.track.addEventListener('mouseup', (e) => this.handleSwipeEnd(e));
            this.track.addEventListener('mouseleave', (e) => this.handleSwipeEnd(e));
            
            // 記事リンクのクリック制御
            this.bindLinkControl();
        }
		
		/**
		 * 記事リンクのクリック制御（修正版）
		 */
		bindLinkControl() {
			const blogLinks = this.container.querySelectorAll('.riff-blog-title-link'); // タイトルリンクのみ対象

			blogLinks.forEach(link => {
				// タイトルリンクのクリック処理を最優先
				link.addEventListener('click', (e) => {
					// ドラッグ中またはドラッグした場合のみリンクを無効化
					if (this.isDragging || this.hasDragged) {
						e.preventDefault();
						e.stopPropagation();
						return false;
					}
					// それ以外は通常のリンク動作を許可
				}, true); // キャプチャフェーズで実行（最優先）

				// タイトルリンクでのマウスダウンは特別処理
				link.addEventListener('mousedown', (e) => {
					this.linkMouseDown = true;
					this.linkStartX = e.clientX;
					this.linkStartY = e.clientY;
					this.linkClickStartTime = Date.now(); // クリック開始時間を記録

					// タイトルリンク上ではドラッグ判定を厳しくする
					this.onTitleLink = true;
				});

				link.addEventListener('mousemove', (e) => {
					if (this.linkMouseDown && this.onTitleLink) {
						const deltaX = Math.abs(e.clientX - this.linkStartX);
						const deltaY = Math.abs(e.clientY - this.linkStartY);

						// タイトルリンク上では移動判定を厳しく（10px以上で初めてドラッグ）
						if (deltaX > 10 || deltaY > 10) {
							this.hasDragged = true;
							this.onTitleLink = false;
						}
					}
				});

				link.addEventListener('mouseup', (e) => {
					const clickDuration = Date.now() - (this.linkClickStartTime || 0);

					// クリック時間が短く、移動が少ない場合は通常のクリックとして処理
					if (clickDuration < 200 && !this.hasDragged) {
						// 通常のリンク動作を許可
						this.isDragging = false;
						this.hasDragged = false;
					}

					this.linkMouseDown = false;
					this.onTitleLink = false;

					setTimeout(() => {
						this.hasDragged = false;
					}, 100);
				});
			});
		}

		/**
		 * スワイプ開始（修正版）
		 */
		handleSwipeStart(e) {
			// タイトルリンク上でのクリックは優先処理
			if (e.target.closest('.riff-blog-title-link')) {
				this.onTitleLink = true;
				return; // タイトルリンククリックの場合はドラッグ処理を開始しない
			}

			this.isDragging = true;
			this.hasDragged = false;
			this.onTitleLink = false;
			this.startX = e.type === 'mousedown' ? e.clientX : e.touches[0].clientX;
			this.currentX = this.startX;

			// 自動再生を停止
			this.stopAutoPlay();

			// カーソルを変更
			this.track.style.cursor = 'grabbing';

			// リンクの無効化フラグ
			this.container.classList.add('dragging');
		}

		/**
		 * スワイプ中（修正版）
		 */
		handleSwipeMove(e) {
			// タイトルリンク上の場合はドラッグ処理をスキップ
			if (this.onTitleLink) {
				return;
			}

			if (!this.isDragging) return;

			this.currentX = e.type === 'mousemove' ? e.clientX : e.touches[0].clientX;
			const diffX = this.currentX - this.startX;

			// 移動判定の閾値を上げる（15px以上で初めてドラッグ）
			if (Math.abs(diffX) > 15) {
				this.hasDragged = true;
				e.preventDefault(); // デフォルトの動作を防ぐ
			}

			// 軽微な移動でアニメーションを更新
			if (Math.abs(diffX) > 20) {
				const movePercent = (diffX / this.container.clientWidth) * 100;
				const currentTransform = -(this.currentIndex * (100 / this.slidesToShow));
				this.track.style.transform = `translateX(${currentTransform + movePercent}%)`;
			}
		}

		/**
		 * スワイプ終了（修正版）
		 */
		handleSwipeEnd(e) {
			// タイトルリンク上の場合は特別処理
			if (this.onTitleLink) {
				this.onTitleLink = false;
				return;
			}

			if (!this.isDragging) return;

			this.isDragging = false;
			this.track.style.cursor = 'grab';

			// ドラッグクラスを削除（少し遅延させる）
			setTimeout(() => {
				this.container.classList.remove('dragging');
				this.hasDragged = false;
			}, 150); // 遅延を少し長めに

			const diffX = this.currentX - this.startX;
			const threshold = this.container.clientWidth * 0.15; // 閾値を15%に上げる

			if (Math.abs(diffX) > threshold) {
				if (diffX > 0) {
					this.prevSlide();
				} else {
					this.nextSlide();
				}
			} else {
				// 元の位置に戻す
				this.updateSlider(true);
			}

			// 自動再生を再開
			if (config.autoPlay) {
				this.startAutoPlay();
			}
		}

        /**
         * 前のスライドへ
         */
        prevSlide() {
            if (this.isAnimating) return;
            
            this.currentIndex = this.currentIndex > 0 ? this.currentIndex - 1 : this.maxIndex;
            this.updateSlider();
        }

        /**
         * 次のスライドへ
         */
        nextSlide() {
            if (this.isAnimating) return;
            
            this.currentIndex = this.currentIndex < this.maxIndex ? this.currentIndex + 1 : 0;
            this.updateSlider();
        }

        /**
         * 指定スライドへ移動
         */
        goToSlide(index) {
            if (this.isAnimating || index === this.currentIndex) return;
            
            this.currentIndex = Math.max(0, Math.min(index, this.maxIndex));
            this.updateSlider();
        }

        /**
         * スライダーの更新
         */
        updateSlider(animate = true) {
            if (animate) {
                this.isAnimating = true;
            }

            const translateX = -(this.currentIndex * (100 / this.slidesToShow));
            
            if (animate) {
                this.track.style.transition = `transform ${config.animationDuration}ms ${config.easeFunction}`;
            } else {
                this.track.style.transition = 'none';
            }
            
            this.track.style.transform = `translateX(${translateX}%)`;

            // アクセシビリティ属性の更新
            this.slides.forEach((slide, index) => {
                const isVisible = index >= this.currentIndex && index < this.currentIndex + this.slidesToShow;
                slide.setAttribute('aria-hidden', !isVisible ? 'true' : 'false');
            });

            if (animate) {
                setTimeout(() => {
                    this.isAnimating = false;
                    this.track.style.transition = '';
                }, config.animationDuration);
            }

            this.updateNavigation();
            this.updateDots();

            // カスタムイベントを発火
            this.container.dispatchEvent(new CustomEvent('slideChanged', {
                detail: {
                    currentIndex: this.currentIndex,
                    totalSlides: this.totalSlides,
                    slidesToShow: this.slidesToShow
                }
            }));
        }

        /**
         * ナビゲーションボタンの更新
         */
        updateNavigation() {
            if (this.prevBtn) {
                this.prevBtn.disabled = this.totalSlides <= this.slidesToShow;
                this.prevBtn.setAttribute('aria-label', 
                    this.currentIndex === 0 ? '前のスライド（最初です）' : '前のスライド'
                );
            }
            
            if (this.nextBtn) {
                this.nextBtn.disabled = this.totalSlides <= this.slidesToShow;
                this.nextBtn.setAttribute('aria-label', 
                    this.currentIndex === this.maxIndex ? '次のスライド（最後です）' : '次のスライド'
                );
            }
        }

        /**
         * バー型ナビゲーションの更新
         */
        updateDots() {
            if (!this.dots.length) return;
            
            this.dots.forEach((dot, index) => {
                const isActive = index === this.currentIndex;
                dot.classList.toggle('active', isActive);
                dot.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                dot.setAttribute('aria-label', `スライド ${index + 1} を表示`);
            });
        }

        /**
         * 自動再生開始
         */
        startAutoPlay() {
            if (!config.autoPlay || this.totalSlides <= this.slidesToShow) return;
            
            this.stopAutoPlay();
            this.autoPlayTimer = setInterval(() => {
                this.nextSlide();
            }, config.autoPlayInterval);
        }

        /**
         * 自動再生停止
         */
        stopAutoPlay() {
            if (this.autoPlayTimer) {
                clearInterval(this.autoPlayTimer);
                this.autoPlayTimer = null;
            }
        }

        /**
         * デバウンス関数
         */
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func.apply(this, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        /**
         * スライダーの破棄
         */
        destroy() {
            this.stopAutoPlay();
            window.removeEventListener('resize', this.handleResize);
            
            // スタイルをリセット
            this.track.style.transform = '';
            this.track.style.transition = '';
            this.track.style.width = '';
            
            this.slides.forEach(slide => {
                slide.style.width = '';
                slide.style.flex = '';
                slide.removeAttribute('aria-hidden');
            });
            
            // ドラッグクラスを削除
            this.container.classList.remove('slider-initialized', 'dragging');
            
            // 追加したプロパティをリセット
            this.hasDragged = false;
            this.linkMouseDown = false;
        }
    }

    /**
     * スライダーの初期化
     */
    function initBlogSliders() {
        const sliderContainers = document.querySelectorAll('.riff-blog-slider-container');
        
        sliderContainers.forEach(container => {
            const slider = new BlogSlider(container);
            sliders.push(slider);
        });

        console.log(`Blog Slider: ${sliders.length}個のスライダーを初期化しました`);
    }

    /**
     * 全スライダーの破棄
     */
    function destroyAllSliders() {
        sliders.forEach(slider => slider.destroy());
        sliders = [];
    }

    /**
     * パブリックAPI
     */
    window.RiffBlogSlider = {
        init: initBlogSliders,
        destroy: destroyAllSliders,
        config: config,
        sliders: sliders
    };

    /**
     * DOM読み込み完了後に初期化
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBlogSliders);
    } else {
        initBlogSliders();
    }

    // ページ表示時の再初期化
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            setTimeout(initBlogSliders, 100);
        }
    });

})();