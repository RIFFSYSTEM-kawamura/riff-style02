/**
 * 記事詳細ページ JavaScript
 * SNSシェア、プログレスバー、読了時間、アニメーション等の機能
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // 初期化
    initializeSinglePost();
    
    /**
     * 記事詳細ページの初期化
     */
    function initializeSinglePost() {
        initializeProgressBar();
        initializeReadingTime();
        initializeSocialShare();
        initializeTableOfContents();
        initializeImageZoom();
        initializeScrollAnimations();
        initializeCopyLink();
        // 「先頭に戻る」ボタンはfooter.php側の共通実装（#back-to-top）を使用するため、
        // ここでの独自生成は行わない（重複表示の原因だったため削除）
    }
    
    /**
     * 読了プログレスバーの初期化
     */
    function initializeProgressBar() {
        // プログレスバーのHTML要素を作成
        const progressBar = document.createElement('div');
        progressBar.className = 'reading-progress-bar';
        progressBar.innerHTML = '<div class="reading-progress-fill"></div>';
        
        // スタイルを追加
        const style = document.createElement('style');
        style.textContent = `
            .reading-progress-bar {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 4px;
                background: rgba(0, 0, 0, 0.1);
                z-index: 9999;
                backdrop-filter: blur(10px);
            }
            .reading-progress-fill {
                height: 100%;
                background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
                width: 0%;
                transition: width 0.1s ease;
                box-shadow: 0 0 10px rgba(0, 124, 186, 0.3);
            }
        `;
        document.head.appendChild(style);
        document.body.appendChild(progressBar);
        
        const progressFill = progressBar.querySelector('.reading-progress-fill');
        const postContent = document.querySelector('.post-content');
        
        if (!postContent) return;
        
        function updateProgress() {
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight - windowHeight;
            const scrollTop = window.pageYOffset;
            const progress = (scrollTop / documentHeight) * 100;
            
            progressFill.style.width = Math.min(progress, 100) + '%';
        }
        
        window.addEventListener('scroll', updateProgress);
        updateProgress(); // 初期実行
    }
    
    /**
     * 読了時間の計算と表示
     */
    function initializeReadingTime() {
        const postContent = document.querySelector('.post-content');
        if (!postContent) return;
        
        const text = postContent.textContent || postContent.innerText;
        const wordsPerMinute = 400; // 日本語の場合は文字数ベース
        const words = text.length;
        const minutes = Math.ceil(words / wordsPerMinute);
        
        // 読了時間表示要素を作成
        const readingTime = document.createElement('div');
        readingTime.className = 'reading-time';
        readingTime.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
            </svg>
            読了時間：約${minutes}分
        `;
        
        // スタイルを追加
        const style = document.createElement('style');
        style.textContent = `
            .reading-time {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 14px;
                color: var(--text-light);
                font-weight: 500;
            }
        `;
        document.head.appendChild(style);
        
        // メタ情報に追加
        const postMeta = document.querySelector('.post-meta-top');
        if (postMeta) {
            postMeta.appendChild(readingTime);
        }
    }
    
    /**
     * SNSシェア機能の初期化
     */
    function initializeSocialShare() {
        const shareButtons = document.querySelectorAll('.share-button');
        const currentUrl = encodeURIComponent(window.location.href);
        const currentTitle = encodeURIComponent(document.title);
        
        shareButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const platform = this.classList[1]; // share-button twitter → twitter
                let shareUrl = '';
                
                switch(platform) {
                    case 'twitter':
                        shareUrl = `https://twitter.com/intent/tweet?url=${currentUrl}&text=${currentTitle}`;
                        break;
                    case 'facebook':
                        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${currentUrl}`;
                        break;
                    case 'line':
                        shareUrl = `https://social-plugins.line.me/lineit/share?url=${currentUrl}`;
                        break;
                    case 'pocket':
                        shareUrl = `https://getpocket.com/edit?url=${currentUrl}&title=${currentTitle}`;
                        break;
                    case 'hatena':
                        shareUrl = `https://b.hatena.ne.jp/add?url=${currentUrl}&title=${currentTitle}`;
                        break;
                }
                
                if (shareUrl) {
                    window.open(shareUrl, 'share', 'width=600,height=400,scrollbars=yes,resizable=yes');
                    
                    // アナリティクス
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'share', {
                            'method': platform,
                            'content_type': 'article',
                            'content_id': window.location.pathname
                        });
                    }
                }
            });
        });
    }
    
    /**
     * 目次の自動生成
     */
    function initializeTableOfContents() {
        const headings = document.querySelectorAll('.post-content h2, .post-content h3');
        if (headings.length < 3) return; // 見出しが少ない場合は目次を作らない
        
        const toc = document.createElement('div');
        toc.className = 'table-of-contents';
        toc.innerHTML = '<h3>目次</h3><ul class="toc-list"></ul>';
        
        const tocList = toc.querySelector('.toc-list');
        
        headings.forEach((heading, index) => {
            // 見出しにIDを付与
            const id = `heading-${index}`;
            heading.id = id;
            
            // 目次アイテムを作成
            const listItem = document.createElement('li');
            listItem.className = `toc-item toc-${heading.tagName.toLowerCase()}`;
            
            const link = document.createElement('a');
            link.href = `#${id}`;
            link.textContent = heading.textContent;
            link.addEventListener('click', function(e) {
                e.preventDefault();
                smoothScrollToElement(heading);
            });
            
            listItem.appendChild(link);
            tocList.appendChild(listItem);
        });
        
        // スタイルを追加
        const style = document.createElement('style');
        style.textContent = `
            .table-of-contents {
                background: linear-gradient(135deg, #f8f9fa, #ffffff);
                border: 1px solid #e9ecef;
                border-radius: 12px;
                padding: 25px;
                margin: 30px auto;
                max-width: 600px;
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
            }
            .table-of-contents h3 {
                margin: 0 0 15px 0;
                font-size: 1.2rem;
                color: var(--text-color);
                text-align: center;
            }
            .toc-list {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            .toc-item {
                margin-bottom: 8px;
            }
            .toc-item a {
                display: block;
                padding: 8px 12px;
                color: var(--text-color);
                text-decoration: none;
                border-radius: 6px;
                transition: all 0.3s ease;
                border-left: 3px solid transparent;
            }
            .toc-item a:hover {
                background: var(--primary-color);
                color: white;
                border-left-color: var(--accent-color);
            }
            .toc-h3 a {
                padding-left: 24px;
                font-size: 0.95rem;
                color: var(--text-light);
            }
            @media (max-width: 768px) {
                .table-of-contents {
                    padding: 16px 14px;
                    margin: 20px auto;
                    border-radius: 10px;
                }
                .toc-item a {
                    padding: 8px 6px;
                }
                .toc-h3 a {
                    padding-left: 14px;
                }
            }
            @media (max-width: 480px) {
                .table-of-contents {
                    padding: 14px 10px;
                }
                .toc-item a {
                    padding: 7px 4px;
                    font-size: 0.95rem;
                }
                .toc-h3 a {
                    padding-left: 10px;
                }
            }
        `;
        document.head.appendChild(style);
        
        // 最初の段落の前に挿入
        const firstParagraph = document.querySelector('.post-content p');
        if (firstParagraph) {
            firstParagraph.parentNode.insertBefore(toc, firstParagraph);
        }
    }
    
    /**
     * 画像のズーム機能
     */
    function initializeImageZoom() {
        const images = document.querySelectorAll('.post-content img');
        
        images.forEach(img => {
            img.style.cursor = 'zoom-in';
            
            img.addEventListener('click', function() {
                createImageModal(this);
            });
        });
        
        function createImageModal(img) {
            const modal = document.createElement('div');
            modal.className = 'image-modal';
            modal.innerHTML = `
                <div class="image-modal-backdrop"></div>
                <div class="image-modal-content">
                    <img src="${img.src}" alt="${img.alt}">
                    <button class="image-modal-close" aria-label="閉じる">×</button>
                </div>
            `;
            
            // スタイルを追加
            const style = document.createElement('style');
            style.textContent = `
                .image-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 10000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    animation: fadeIn 0.3s ease;
                }
                .image-modal-backdrop {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.9);
                    backdrop-filter: blur(5px);
                }
                .image-modal-content {
                    position: relative;
                    max-width: 90%;
                    max-height: 90%;
                }
                .image-modal img {
                    max-width: 100%;
                    max-height: 100%;
                    border-radius: 8px;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                }
                .image-modal-close {
                    position: absolute;
                    top: -50px;
                    right: 0;
                    background: white;
                    border: none;
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    font-size: 24px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }
                .image-modal-close:hover {
                    background: var(--primary-color);
                    color: white;
                }
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
            `;
            document.head.appendChild(style);
            
            document.body.appendChild(modal);
            document.body.style.overflow = 'hidden';
            
            // 閉じる機能
            const closeModal = () => {
                modal.remove();
                document.body.style.overflow = '';
            };
            
            modal.querySelector('.image-modal-close').addEventListener('click', closeModal);
            modal.querySelector('.image-modal-backdrop').addEventListener('click', closeModal);
            
            // ESCキーで閉じる
            const escHandler = (e) => {
                if (e.key === 'Escape') {
                    closeModal();
                    document.removeEventListener('keydown', escHandler);
                }
            };
            document.addEventListener('keydown', escHandler);
        }
    }
    
    /**
     * スクロールアニメーションの初期化
     */
    function initializeScrollAnimations() {
        const animatedElements = document.querySelectorAll('.post-content > *');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1,
            // 要素が画面下端に到達する前(150px手前)からフェードを開始し、
            // 「スクロールしても何も表示されない時間」を短くする
            rootMargin: '0px 0px 150px 0px'
        });

        animatedElements.forEach((element, index) => {
            // 遅延を要素番号にそのまま比例させると、記事が長いほど後半の要素の
            // 表示が数秒単位で遅れてしまうため、遅延は最大0.2秒までに制限する
            const delay = Math.min(index * 0.04, 0.2);
            element.style.opacity = '0';
            element.style.transform = 'translateY(16px)';
            element.style.transition = `opacity 0.35s ease ${delay}s, transform 0.35s ease ${delay}s`;
            observer.observe(element);
        });
    }
    
    /**
     * URLコピー機能
     */
    function initializeCopyLink() {
        // コピーリンクボタンを追加
        const copyButton = document.createElement('button');
        copyButton.className = 'copy-link-button';
        copyButton.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
            </svg>
            URLをコピー
        `;
        
        // スタイルを追加
        const style = document.createElement('style');
        style.textContent = `
            .copy-link-button {
                display: flex;
                align-items: center;
                gap: 8px;
                background: #f8f9fa;
                border: 1px solid #e9ecef;
                color: var(--text-color);
                padding: 12px 20px;
                border-radius: 50px;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.3s ease;
                margin: 0 auto;
            }
            .copy-link-button:hover {
                background: var(--primary-color);
                color: white;
                border-color: var(--primary-color);
            }
            .copy-link-button.copied {
                background: #28a745;
                color: white;
                border-color: #28a745;
            }
        `;
        document.head.appendChild(style);
        
        // シェアボタンの後に追加
        const shareSection = document.querySelector('.post-share-buttons .share-buttons-list');
        if (shareSection) {
            const copyWrapper = document.createElement('div');
            copyWrapper.style.marginTop = '20px';
            copyWrapper.appendChild(copyButton);
            shareSection.parentNode.appendChild(copyWrapper);
        }
        
        copyButton.addEventListener('click', function() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                this.textContent = 'コピーしました！';
                this.classList.add('copied');
                
                setTimeout(() => {
                    this.innerHTML = `
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                        </svg>
                        URLをコピー
                    `;
                    this.classList.remove('copied');
                }, 2000);
            });
        });
    }
    
    /**
     * スムーススクロール関数
     */
    function smoothScrollToElement(element) {
        const targetPosition = element.getBoundingClientRect().top + window.pageYOffset - 80;
        
        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });
    }
    
    function smoothScrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
    
    /**
     * パフォーマンス最適化
     */
    function optimizePerformance() {
        // 画像の遅延読み込み
        const images = document.querySelectorAll('img[data-src]');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            images.forEach(img => imageObserver.observe(img));
        }
    }
    
    // パフォーマンス最適化の実行
    optimizePerformance();
    
});

/**
 * ユーティリティ関数
 */

// デバウンス関数
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

// スロットル関数
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
    };
}