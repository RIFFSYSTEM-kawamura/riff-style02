<?php
/**
 * フッターテンプレート
 * RIFF Theme - 新しいデザイン（フルワイド対応・ロゴ選択機能付き・FOUC対策版）
 */
?>

<footer class="riff-new-footer" id="footer">
    <div class="riff-new-footer-container">
        <!-- 会社情報・SNSセクション -->
        <div class="riff-new-footer-info">
            <!-- ロゴ・会社名 -->
            <div class="riff-new-footer-brand">
                <?php 
                $footer_display_type = get_option('riff_footer_display_type', 'title'); // 'logo' または 'title'
                $footer_logo_url = get_option('riff_footer_logo_url', '');
                $site_title = get_option('riff_site_title', get_bloginfo('name'));
                
                if ($footer_display_type === 'logo' && !empty($footer_logo_url)): 
                ?>
                    <img src="<?php echo esc_url($footer_logo_url); ?>" 
                         alt="<?php echo esc_attr($site_title); ?>" 
                         class="riff-new-footer-logo"
                         loading="lazy">
                <?php else: ?>
                    <h3 class="riff-new-footer-title"><?php echo esc_html($site_title); ?></h3>
                <?php endif; ?>
            </div>
            
            <!-- 会社名・住所 -->
            <div class="riff-new-footer-company">
                <p class="riff-new-footer-company-name">
                    <?php echo esc_html(get_option('riff_footer_company_name', 'RIFF System')); ?>
                </p>
                <p class="riff-new-footer-address">
                    <?php echo esc_html(get_option('riff_footer_address', '')); ?>
                </p>
            </div>
            
            <!-- SNSアイコン -->
            <div class="riff-new-footer-social">
                <?php
                foreach (riff_get_social_platforms() as $platform => $info) :
                    $url = get_option($info['option'], '');
                    if (!empty($url)) :
                ?>
                    <a href="<?php echo esc_url($url); ?>"
                       class="riff-new-footer-social-link riff-social-<?php echo esc_attr($platform); ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="<?php echo esc_attr($info['label']); ?>">
                        <?php echo riff_get_social_icon($platform); ?>
                    </a>
                <?php
                    endif;
                endforeach;
                ?>
            </div>
        </div>
        
        <!-- ナビゲーションメニュー -->
        <nav class="riff-new-footer-nav" aria-label="フッターナビゲーション">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'footer',
                'menu_class' => 'riff-new-footer-menu',
                'container' => false,
                'fallback_cb' => 'riff_new_footer_fallback_menu',
                'depth' => 0
            ));
            ?>
        </nav>

        <style>
        .riff-new-footer-menu li.menu-item-has-children { position: relative; }
        .riff-new-footer-menu li.menu-item-has-children > a {
            cursor: pointer;
        }
        .riff-new-footer-menu li.menu-item-has-children > a::after {
            content: '';
            display: inline-block;
            width: 6px;
            height: 6px;
            margin-left: 6px;
            vertical-align: middle;
            border-right: 2px solid currentColor;
            border-bottom: 2px solid currentColor;
            transform: rotate(45deg);
            transition: transform 0.25s ease;
        }
        .riff-new-footer-menu li.menu-item-has-children.footer-submenu-open > a::after {
            transform: rotate(-135deg);
        }
        .riff-new-footer-menu .sub-menu {
            list-style: none;
            margin: 0;
            padding: 0 0 0 16px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        .riff-new-footer-menu li.menu-item-has-children.footer-submenu-open > .sub-menu {
            max-height: 300px;
        }
        .riff-new-footer-menu .sub-menu li { margin-top: 10px; }
        .riff-new-footer-menu .sub-menu a { font-size: 13px; opacity: 0.85; }
        </style>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var footerParents = document.querySelectorAll('.riff-new-footer-menu li.menu-item-has-children > a');
            footerParents.forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    var parentLi = this.parentElement;
                    parentLi.classList.toggle('footer-submenu-open');
                });
            });
        });
        </script>
        
        <!-- 法的リンク -->
        <div class="riff-new-footer-legal-links" style="text-align:center; margin-bottom:12px; font-size:13px;">
            <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" style="color:inherit; opacity:0.85; text-decoration:none; margin:0 10px;">プライバシーポリシー</a>
            <a href="<?php echo esc_url(home_url('/tokushoho/')); ?>" style="color:inherit; opacity:0.85; text-decoration:none; margin:0 10px;">特定商取引法に基づく表記</a>
        </div>

        <!-- コピーライト -->
        <div class="riff-new-footer-copyright">
            <p><?php echo wp_kses_post(get_option('riff_footer_copyright_text', '&copy; ' . date('Y') . ' ' . get_option('riff_footer_company_name', get_option('riff_site_title', get_bloginfo('name'))) . '. All rights reserved.')); ?></p>
        </div>
    </div>
    
    <!-- ページトップへ戻るボタン（既存のものを保持） -->
    <div class="back-to-top" id="back-to-top">
        <button type="button" aria-label="ページトップへ戻る" class="back-to-top-button">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/>
            </svg>
        </button>
    </div>

    <!-- サイド追従ボタン（公式LINE・お問い合わせ・日程予約） -->
    <?php
    $side_line_enabled = get_option('riff_side_buttons_line_enabled', 1);
    $side_line_label = get_option('riff_side_buttons_line_label', '公式LINE');
    $side_line_url = get_option('riff_side_buttons_line_url', get_option('riff_footer_line_url', ''));

    $side_contact_enabled = get_option('riff_side_buttons_contact_enabled', 1);
    $side_contact_label = get_option('riff_side_buttons_contact_label', 'お問い合わせ');
    $side_contact_url = get_option('riff_side_buttons_contact_url', get_option('riff_footer_up_contact_url', '/contact/'));
    ?>
    <div class="riff-side-fixed-buttons" id="riff-side-fixed-buttons">
        <?php if ($side_line_enabled && !empty($side_line_url)): ?>
        <a href="<?php echo esc_url($side_line_url); ?>"
           class="riff-side-fixed-btn riff-side-fixed-btn-line"
           target="_blank"
           rel="noopener noreferrer"
           aria-label="<?php echo esc_attr($side_line_label); ?>はこちら">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12.017.572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/>
            </svg>
            <span class="riff-side-fixed-btn-label"><?php echo esc_html($side_line_label); ?></span>
        </a>
        <?php endif; ?>

        <?php if ($side_contact_enabled && !empty($side_contact_url)): ?>
        <a href="<?php echo esc_url($side_contact_url); ?>"
           class="riff-side-fixed-btn riff-side-fixed-btn-contact"
           aria-label="<?php echo esc_attr($side_contact_label); ?>はこちら">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
            </svg>
            <span class="riff-side-fixed-btn-label"><?php echo esc_html($side_contact_label); ?></span>
        </a>
        <?php endif; ?>

        <?php if (function_exists('riff_booking_is_ready') && riff_booking_is_ready()): ?>
        <a href="<?php echo esc_url(riff_booking_page_url()); ?>"
           class="riff-side-fixed-btn riff-side-fixed-btn-booking"
           aria-label="日程を予約するページへ">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zm-7 5h5v5h-5z"/>
            </svg>
            <span class="riff-side-fixed-btn-label">日程を予約する</span>
        </a>
        <?php endif; ?>
    </div>

    <!-- 公式LINE・日程予約ボタンのクリックをGA4イベント(line_click / booking_click)として計測する -->
    <script>
    (function () {
        document.addEventListener('click', function (e) {
            if (typeof gtag === 'undefined') return;
            var el = e.target.closest('.riff-side-fixed-btn-line, .riff-side-fixed-btn-booking');
            if (!el) return;
            gtag('event', el.classList.contains('riff-side-fixed-btn-line') ? 'line_click' : 'booking_click', {
                cta_location: 'side_fixed',
                link_url: el.href
            });
        });
    })();
    </script>
</footer>

<?php
// フォールバックメニュー関数
function riff_new_footer_fallback_menu() {
    echo '<ul class="riff-new-footer-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">ホーム</a></li>';
    echo '<li><a href="' . esc_url(home_url('/about/')) . '">運営者情報</a></li>';
    echo '<li><a href="' . esc_url(home_url('/service/')) . '">サービス</a></li>';
    echo '<li><a href="' . esc_url(home_url('/news/')) . '">ニュース</a></li>';
    echo '<li><a href="' . esc_url(home_url('/blog/')) . '">ブログ</a></li>';
    echo '<li><a href="' . esc_url(home_url('/company/')) . '">企業情報</a></li>';
    echo '<li><a href="' . esc_url(home_url('/download/')) . '">ダウンロード</a></li>';
    echo '</ul>';
}
// riff_get_social_icon() は functions.php に共通化（フッター・モバイルメニューで共有）
?>

<!-- FOUC防止とページ読み込み完了処理 -->
<script type="text/javascript">
// ページトップへ戻るボタンの動作（既存機能を保持）
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // ページトップボタンの初期化
    initializeBackToTop();
    
    // FOUC防止の最終チェック
    ensurePageVisible();
    
    // ページ読み込み完了の検知
    detectPageLoadComplete();
    
    /**
     * ページトップへ戻るボタンの初期化
     */
    function initializeBackToTop() {
        var backToTop = document.getElementById('back-to-top');
        var backToTopButton = backToTop && backToTop.querySelector('.back-to-top-button');
        
        if (backToTop && backToTopButton) {
            // スクロール監視
            var scrollHandler = function() {
                if (window.pageYOffset > 300) {
                    backToTop.classList.add('visible');
                } else {
                    backToTop.classList.remove('visible');
                }
            };
            
            // パフォーマンス最適化: スクロールイベントのスロットリング
            var ticking = false;
            window.addEventListener('scroll', function() {
                if (!ticking) {
                    requestAnimationFrame(function() {
                        scrollHandler();
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });
            
            // クリックイベント
            backToTopButton.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    }
    
    /**
     * FOUC防止の最終チェック
     */
    function ensurePageVisible() {
        // ローディング画面が無効な場合のみ実行
        var isLoadingEnabled = <?php echo (is_front_page() && get_theme_mod('loading_screen_enable', true)) ? 'true' : 'false'; ?>;
        
        if (!isLoadingEnabled) {
            // 最終的にページを表示
            setTimeout(function() {
                var html = document.documentElement;
                if (!html.classList.contains('styles-loaded')) {
                    html.classList.add('styles-loaded');
                    
                    // デバッグログ
                    if (typeof console !== 'undefined' && console.log) {
                        console.log('FOUC prevention: Final fallback triggered');
                    }
                }
            }, 100);
        }
    }
    
    /**
     * ページ読み込み完了の検知
     */
    function detectPageLoadComplete() {
        var pageLoadManager = {
            isComplete: false,
            
            init: function() {
                this.setupCompletionChecks();
                this.trackPerformance();
            },
            
            setupCompletionChecks: function() {
                var self = this;
                
                // 複数の方法で読み込み完了を検知
                this.checkMethods = [
                    this.checkDOMState.bind(this),
                    this.checkImages.bind(this),
                    this.checkStyleSheets.bind(this)
                ];
                
                var checkInterval = setInterval(function() {
                    if (self.isPageComplete()) {
                        clearInterval(checkInterval);
                        self.onPageComplete();
                    }
                }, 100);
                
                // 5秒でタイムアウト
                setTimeout(function() {
                    clearInterval(checkInterval);
                    if (!self.isComplete) {
                        self.onPageComplete();
                    }
                }, 5000);
            },
            
            checkDOMState: function() {
                return document.readyState === 'complete';
            },
            
            checkImages: function() {
                var images = document.images;
                for (var i = 0; i < images.length; i++) {
                    if (!images[i].complete) {
                        return false;
                    }
                }
                return true;
            },
            
            checkStyleSheets: function() {
                try {
                    var sheets = document.styleSheets;
                    for (var i = 0; i < sheets.length; i++) {
                        var sheet = sheets[i];
                        if (sheet.href && sheet.href.indexOf('style.css') > -1) {
                            // メインCSSが読み込まれているかチェック
                            var rules = sheet.cssRules || sheet.rules;
                            return rules && rules.length > 0;
                        }
                    }
                    return true; // メインCSSが見つからない場合はtrueとする
                } catch (e) {
                    return true; // CORS制限などでアクセスできない場合
                }
            },
            
            isPageComplete: function() {
                var results = this.checkMethods.map(function(method) {
                    return method();
                });
                
                // すべてのチェックがtrueの場合、または2つ以上がtrueの場合
                var trueCount = results.filter(function(result) {
                    return result === true;
                }).length;
                
                return trueCount >= 2;
            },
            
            onPageComplete: function() {
                if (this.isComplete) return;
                this.isComplete = true;
                
                // ページ読み込み完了の処理
                document.body.classList.add('page-loaded');
                
                // カスタムイベントの発火
                if (typeof CustomEvent !== 'undefined') {
                    var event = new CustomEvent('riffPageLoadComplete', {
                        detail: { 
                            timestamp: Date.now(),
                            method: 'footer-detection'
                        }
                    });
                    document.dispatchEvent(event);
                }
                
                // デバッグログ
                if (typeof console !== 'undefined' && console.log) {
                    console.log('RIFF Theme: Page load complete detected');
                }
            },
            
            trackPerformance: function() {
                // パフォーマンス情報の記録（開発時のみ）
                if (<?php echo WP_DEBUG ? 'true' : 'false'; ?> && typeof performance !== 'undefined') {
                    setTimeout(function() {
                        if (performance.timing) {
                            var timings = {
                                domReady: performance.timing.domContentLoadedEventEnd - performance.timing.navigationStart,
                                loadComplete: performance.timing.loadEventEnd - performance.timing.navigationStart,
                                firstPaint: performance.getEntriesByType ? 
                                    (performance.getEntriesByType('paint')[0] ? 
                                        performance.getEntriesByType('paint')[0].startTime : 0) : 0
                            };
                            console.log('RIFF Performance:', timings);
                        }
                    }, 1000);
                }
            }
        };
        
        pageLoadManager.init();
    }
});
</script>

<!-- 遅延読み込み画像の最適化 -->
<script type="text/javascript">
// 画像の遅延読み込み対応（modern browsers）
if ('loading' in HTMLImageElement.prototype) {
    // ネイティブlazy loading対応
    var lazyImages = document.querySelectorAll('img[loading="lazy"]');
    lazyImages.forEach(function(img) {
        img.addEventListener('load', function() {
            img.style.opacity = '1';
        });
    });
} else {
    // フォールバック: Intersection Observer
    if ('IntersectionObserver' in window) {
        var imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.style.opacity = '1';
                    observer.unobserve(img);
                }
            });
        });
        
        var lazyImages = document.querySelectorAll('img[loading="lazy"]');
        lazyImages.forEach(function(img) {
            imageObserver.observe(img);
        });
    }
}
</script>

<?php wp_footer(); ?>

<!-- FOUC防止の最終確認 -->
<script type="text/javascript">
// 最終的なFOUC防止チェック
(function() {
    'use strict';
    
    // ページ読み込み後の最終処理
    window.addEventListener('load', function() {
        // 確実にページを表示
        setTimeout(function() {
            var html = document.documentElement;
            html.classList.add('styles-loaded');
            html.style.visibility = 'visible';
            html.style.opacity = '1';
            
            // 読み込み状態のクリーンアップ
            document.body.classList.remove('loading-active');
            
            // 不要なクラスの削除
            setTimeout(function() {
                html.classList.remove('loading-screen-enabled');
            }, 100);
            
        }, 50);
    });
    
    // 念のための最終フォールバック（3秒後）
    setTimeout(function() {
        var html = document.documentElement;
        if (!html.classList.contains('styles-loaded')) {
            html.classList.add('styles-loaded');
            html.style.visibility = 'visible';
            html.style.opacity = '1';
            
            if (typeof console !== 'undefined' && console.warn) {
                console.warn('RIFF Theme: Final FOUC prevention fallback executed');
            }
        }
    }, 3000);
})();
</script>
</body>
</html>