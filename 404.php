<?php
/**
 * 404エラーページテンプレート（改良版）
 * NEWS/BLOG記事への誘導機能付き
 * 
 * @package RiffTheme
 */

get_header(); ?>

<div class="error-404-container">
    <div class="container">
        
        <!-- 404エラーヘッダー -->
        <header class="error-404-header text-center fade-in">
            
            <!-- 404アニメーション -->
            <div class="error-404-animation" style="margin-bottom: 40px;">
                <div style="position: relative; display: inline-block;">
                    <!-- 404数字 -->
                    <div style="font-size: 8rem; font-weight: 900; color: var(--primary-color); line-height: 1; margin-bottom: 20px; text-shadow: 0 4px 8px rgba(0, 124, 186, 0.3); position: relative;">
                        4
                        <span style="color: var(--accent-color); animation: bounce 2s infinite;">0</span>
                        4
                    </div>
                    
                    <!-- 背景装飾 -->
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 300px; height: 300px; background: linear-gradient(45deg, var(--primary-color), var(--accent-color)); opacity: 0.1; border-radius: 50%; z-index: -1; animation: pulse 3s infinite;"></div>
                </div>
            </div>
            
            <h1 style="font-size: 2.5rem; font-weight: 700; color: var(--text-color); margin-bottom: 20px;">
                ページが見つかりません
            </h1>
            
            <p style="font-size: 1.2rem; color: var(--text-light); margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                お探しのページは削除されたか、URLが変更された可能性があります。<br>
                以下の方法で目的の情報を見つけてください。
            </p>
            
        </header>
        
        <!-- クイック検索 -->
        <div class="error-404-search fade-in" data-delay="0.2s" style="background: linear-gradient(135deg, #f8f9fa, #ffffff); padding: 40px; border-radius: 20px; margin-bottom: 60px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05); border: 1px solid #e9ecef;">
            <h2 style="text-align: center; margin-bottom: 30px; color: var(--text-color); font-size: 1.5rem;">お探しの記事を検索</h2>
            
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" style="display: flex; gap: 15px; max-width: 600px; margin: 0 auto; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <input type="search" 
                           name="s" 
                           placeholder="キーワードを入力してください..." 
                           style="width: 100%; padding: 18px 25px; border: 2px solid #e9ecef; border-radius: 50px; font-size: 16px; transition: all 0.3s ease;"
                           onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 3px rgba(0, 124, 186, 0.1)'"
                           onblur="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'">
                </div>
                <button type="submit" 
                        style="background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); color: white; border: none; padding: 18px 35px; border-radius: 50px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 10px;"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 30px rgba(0, 124, 186, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    検索
                </button>
            </form>
        </div>
        
        <!-- ナビゲーションオプション -->
        <div class="error-404-navigation fade-in" data-delay="0.4s" style="margin-bottom: 60px;">
            <h2 style="text-align: center; margin-bottom: 40px; color: var(--text-color); font-size: 1.5rem;">サイト内ナビゲーション</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                
                <!-- ホームページ -->
                <a href="<?php echo esc_url(home_url('/')); ?>" 
                   style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; border-radius: 16px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);"
                   onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 40px rgba(102, 126, 234, 0.4)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.3)'">
                    <div style="text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 15px;"></div>
                        <h3 style="margin: 0 0 10px; font-size: 1.3rem; font-weight: 700;">ホームページ</h3>
                        <p style="margin: 0; opacity: 0.9; font-size: 14px;">トップページに戻る</p>
                    </div>
                </a>
                
                <!-- NEWS記事一覧 -->
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) . '?post_type=news'); ?>" 
                   style="background: linear-gradient(135deg, #f093fb, #f5576c); color: white; padding: 30px; border-radius: 16px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(240, 147, 251, 0.3);"
                   onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 40px rgba(240, 147, 251, 0.4)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(240, 147, 251, 0.3)'">
                    <div style="text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 15px;"></div>
                        <h3 style="margin: 0 0 10px; font-size: 1.3rem; font-weight: 700;">NEWS記事</h3>
                        <p style="margin: 0; opacity: 0.9; font-size: 14px;">最新のお知らせ・ニュース</p>
                    </div>
                </a>
                
                <!-- BLOG記事一覧 -->
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) . '?post_type=blog'); ?>" 
                   style="background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; padding: 30px; border-radius: 16px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);"
                   onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 40px rgba(79, 172, 254, 0.4)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(79, 172, 254, 0.3)'">
                    <div style="text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 15px;"></div>
                        <h3 style="margin: 0 0 10px; font-size: 1.3rem; font-weight: 700;">BLOG記事</h3>
                        <p style="margin: 0; opacity: 0.9; font-size: 14px;">技術記事・コラム</p>
                    </div>
                </a>
                
                <!-- 全記事一覧 -->
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" 
                   style="background: linear-gradient(135deg, #fa709a, #fee140); color: white; padding: 30px; border-radius: 16px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(250, 112, 154, 0.3);"
                   onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 40px rgba(250, 112, 154, 0.4)'"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 25px rgba(250, 112, 154, 0.3)'">
                    <div style="text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 15px;"></div>
                        <h3 style="margin: 0 0 10px; font-size: 1.3rem; font-weight: 700;">すべての記事</h3>
                        <p style="margin: 0; opacity: 0.9; font-size: 14px;">全投稿記事を見る</p>
                    </div>
                </a>
                
            </div>
        </div>
        
        <!-- 人気記事セクション -->
        <?php
        $popular_posts = new WP_Query(array(
            'post_type' => 'post',
            'posts_per_page' => 6,
            'post_status' => 'publish',
            'orderby' => 'comment_count',
            'order' => 'DESC'
        ));
        
        if ($popular_posts->have_posts()) :
        ?>
        <div class="error-404-popular-posts fade-in" data-delay="0.6s">
            <h2 style="text-align: center; margin-bottom: 40px; color: var(--text-color); font-size: 1.5rem;">人気記事</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px;">
                
                <?php $count = 0; while ($popular_posts->have_posts() && $count < 6) : $popular_posts->the_post(); $count++; ?>
                
                <article style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; border: 1px solid #f0f0f0;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 35px rgba(0, 0, 0, 0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(0, 0, 0, 0.08)'">
                    
                    <a href="<?php the_permalink(); ?>" style="display: block; text-decoration: none; color: inherit;">
                        
                        <?php if (has_post_thumbnail()) : ?>
                        <div style="aspect-ratio: 16/9; overflow: hidden; background: linear-gradient(135deg, #f5f7fa, #c3cfe2);">
                            <?php the_post_thumbnail('medium', array(
                                'style' => 'width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;',
                                'onmouseover' => 'this.style.transform="scale(1.05)"',
                                'onmouseout' => 'this.style.transform="scale(1)"'
                            )); ?>
                        </div>
                        <?php endif; ?>
                        
                        <div style="padding: 20px;">
                            
                            <!-- 投稿タイプバッジ -->
                            <?php 
                            $post_type_flag = get_post_meta(get_the_ID(), 'post_type_flag', true);
                            $post_type_flag = $post_type_flag ? $post_type_flag : 'news';
                            ?>
                            <div style="margin-bottom: 12px;">
                                <span style="padding: 4px 10px; border-radius: 12px; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: white; 
                                    background: <?php 
                                    switch($post_type_flag) {
                                        case 'news': echo 'linear-gradient(135deg, #1976d2, #42a5f5)'; break;
                                        case 'blog': echo 'linear-gradient(135deg, #7b1fa2, #ba68c8)'; break;
                                        case 'both': echo 'linear-gradient(135deg, #388e3c, #66bb6a)'; break;
                                        default: echo 'linear-gradient(135deg, #757575, #9e9e9e)';
                                    }
                                    ?>;">
                                    <?php 
                                    switch($post_type_flag) {
                                        case 'news': echo 'NEWS'; break;
                                        case 'blog': echo 'BLOG'; break;
                                        case 'both': echo 'BOTH'; break;
                                        default: echo 'POST';
                                    }
                                    ?>
                                </span>
                            </div>
                            
                            <h3 style="margin: 0 0 10px; font-size: 16px; line-height: 1.4; font-weight: 600; color: var(--text-color); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php the_title(); ?>
                            </h3>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; color: var(--text-light); font-size: 12px;">
                                
                                <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('Y.m.d'); ?></time>
                                
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <!-- コメント数 -->
                                    <span style="display: flex; align-items: center; gap: 3px;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M21.99 4c0-1.1-.89-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18zM18 14H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                                        </svg>
                                        <?php comments_number('0', '1', '%'); ?>
                                    </span>
                                    
                                    <!-- 人気度インジケーター -->
                                    <span style="display: flex; align-items: center; gap: 3px; color: var(--accent-color); font-weight: 600;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                        人気
                                    </span>
                                </div>
                                
                            </div>
                            
                        </div>
                    </a>
                    
                </article>
                
                <?php endwhile; wp_reset_postdata(); ?>
                
            </div>
        </div>
        <?php endif; ?>
        
        <!-- カテゴリー一覧 -->
        <?php
        $categories = get_categories(array(
            'hide_empty' => true,
            'number' => 8
        ));
        
        if (!empty($categories)) :
        ?>
        <div class="error-404-categories fade-in" data-delay="0.8s" style="margin-top: 60px;">
            <h2 style="text-align: center; margin-bottom: 40px; color: var(--text-color); font-size: 1.5rem;">カテゴリーから探す</h2>
            
            <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                <?php foreach ($categories as $category) : ?>
                <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" 
                   style="background: white; color: var(--text-color); padding: 12px 20px; border: 2px solid #e9ecef; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px;"
                   onmouseover="this.style.background='var(--primary-color)'; this.style.color='white'; this.style.borderColor='var(--primary-color)'; this.style.transform='translateY(-2px)'"
                   onmouseout="this.style.background='white'; this.style.color='var(--text-color)'; this.style.borderColor='#e9ecef'; this.style.transform='translateY(0)'">
                    <span><?php echo esc_html($category->name); ?></span>
                    <span style="background: var(--gray-200); color: var(--text-light); padding: 2px 6px; border-radius: 10px; font-size: 11px; font-weight: 700;">
                        <?php echo $category->count; ?>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- お問い合わせCTA -->
        <div class="error-404-contact fade-in" data-delay="1.0s" style="margin-top: 80px; padding: 50px 30px; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); border-radius: 20px; text-align: center; color: white;">
            
            <h2 style="margin: 0 0 20px; font-size: 1.8rem; font-weight: 700;">それでも見つからない場合は</h2>
            <p style="margin: 0 0 30px; font-size: 1.1rem; opacity: 0.9; max-width: 500px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                お探しの情報が見つからない場合は、<br>お気軽にお問い合わせください。
            </p>
            
            <a href="<?php echo esc_url(home_url('/contact/')); ?>" 
               style="display: inline-flex; align-items: center; gap: 10px; background: white; color: var(--primary-color); padding: 18px 35px; border-radius: 50px; font-weight: 700; font-size: 16px; text-decoration: none; transition: all 0.3s ease;"
               onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 15px 40px rgba(0, 0, 0, 0.2)'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
                お問い合わせ
            </a>
            
        </div>
        
    </div>
</div>

<!-- 404ページ用アニメーション -->
<style>
@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translate3d(0,0,0);
    }
    40%, 43% {
        transform: translate3d(0, -30px, 0);
    }
    70% {
        transform: translate3d(0, -15px, 0);
    }
    90% {
        transform: translate3d(0, -4px, 0);
    }
}

@keyframes pulse {
    0% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.1;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.1);
        opacity: 0.2;
    }
    100% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.1;
    }
}

.error-404-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    min-height: 80vh;
    padding: 80px 0;
}

.fade-in {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.8s ease forwards;
}

.fade-in[data-delay="0.2s"] { animation-delay: 0.2s; }
.fade-in[data-delay="0.4s"] { animation-delay: 0.4s; }
.fade-in[data-delay="0.6s"] { animation-delay: 0.6s; }
.fade-in[data-delay="0.8s"] { animation-delay: 0.8s; }
.fade-in[data-delay="1.0s"] { animation-delay: 1.0s; }

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .error-404-animation div {
        font-size: 5rem !important;
    }
    
    .error-404-header h1 {
        font-size: 2rem !important;
    }
    
    .error-404-header p {
        font-size: 1rem !important;
    }
    
    .error-404-search form {
        flex-direction: column;
    }
    
    .error-404-search input {
        min-width: auto !important;
    }
    
    .error-404-navigation > div {
        grid-template-columns: 1fr !important;
        gap: 15px;
    }
    
    .error-404-popular-posts > div {
        grid-template-columns: 1fr !important;
        gap: 20px;
    }
    
    .error-404-contact {
        padding: 40px 20px !important;
    }
    
    .error-404-contact h2 {
        font-size: 1.5rem !important;
    }
    
    .error-404-contact p {
        font-size: 1rem !important;
    }
}
</style>

<!-- 404ページ用JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // アニメーション開始
    const fadeElements = document.querySelectorAll('.fade-in');
    fadeElements.forEach(element => {
        const delay = element.getAttribute('data-delay') || '0s';
        element.style.animationDelay = delay;
    });
    
    // 404エラーの統計を記録（必要に応じて）
    if (typeof gtag !== 'undefined') {
        gtag('event', 'page_view', {
            'page_title': '404 Error',
            'page_location': window.location.href,
            'page_path': window.location.pathname
        });
    }
    
    // 検索フォームの拡張機能
    const searchInput = document.querySelector('input[name="s"]');
    if (searchInput) {
        // 検索候補機能（必要に応じて実装）
        searchInput.addEventListener('input', function() {
            // 検索候補のロジックをここに追加
        });
    }
});
</script>

<?php get_footer(); ?>