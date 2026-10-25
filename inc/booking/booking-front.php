<?php
/**
 * 日程予約：予約ページ（ショートコード [riff_booking]）
 *
 * 通常は「日時選択 → お客様情報 → 完了」の予約画面を、メール内リンク（?rb=トークン）から
 * 来た場合は日程変更・キャンセルの画面を表示する。画面の動きは assets/js/booking.js。
 */

if (!defined('ABSPATH')) {
    exit;
}

function riff_booking_turnstile_sitekey() {
    $keys = class_exists('WPCF7') ? (array) WPCF7::get_option('turnstile') : array();
    return (string) array_key_first($keys);
}

add_shortcode('riff_booking', 'riff_booking_shortcode');

function riff_booking_shortcode() {
    $contact_url = home_url('/contact/');
    if (!riff_booking_is_ready()) {
        return '<div class="riff-booking"><p class="riff-booking-notice">現在、オンラインでの日程予約は準備中です。お手数ですが<a href="' . esc_url($contact_url) . '">お問い合わせフォーム</a>からご連絡ください。</p></div>';
    }

    $theme_uri = get_template_directory_uri();
    $theme_dir = get_template_directory();
    wp_enqueue_style('riff-booking', $theme_uri . '/assets/css/booking.css', array(), filemtime($theme_dir . '/assets/css/booking.css'));
    wp_enqueue_script('riff-booking', $theme_uri . '/assets/js/booking.js', array(), filemtime($theme_dir . '/assets/js/booking.js'), true);

    $token = isset($_GET['rb']) ? sanitize_text_field(wp_unslash($_GET['rb'])) : '';
    $config = array(
        'api'         => esc_url_raw(rest_url('riff-booking/v1/')),
        'token'       => '',
        'sitekey'     => riff_booking_turnstile_sitekey(),
        'durationMin' => RIFF_BOOKING_DURATION_MIN,
    );

    ob_start();
    if ($token !== '') {
        $booking = riff_booking_sync_from_calendar(riff_booking_find_by_token($token));
        $config['token'] = $booking ? $token : '';
        riff_booking_render_manage($booking, $contact_url);
    } else {
        if ($config['sitekey']) {
            wp_enqueue_script('cf-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit', array(), null, true);
        }
        riff_booking_render_form($contact_url);
    }
    wp_add_inline_script('riff-booking', 'window.RIFF_BOOKING = ' . wp_json_encode($config) . ';', 'before');
    return ob_get_clean();
}

/** 日付カレンダーと時間枠の選択パネル（予約・日程変更で共用） */
function riff_booking_render_picker() {
    ?>
    <div class="rb-picker" data-rb-picker>
        <div class="rb-calendar">
            <div class="rb-calendar-head">
                <button type="button" class="rb-month-nav" data-rb-prev aria-label="前の月">&#8249;</button>
                <span class="rb-month-label" data-rb-month></span>
                <button type="button" class="rb-month-nav" data-rb-next aria-label="次の月">&#8250;</button>
            </div>
            <div class="rb-weekdays" aria-hidden="true">
                <span class="is-sun">日</span><span>月</span><span>火</span><span>水</span><span>木</span><span>金</span><span class="is-sat">土</span>
            </div>
            <div class="rb-days" data-rb-days role="grid"></div>
            <p class="rb-legend"><span class="rb-legend-dot"></span>予約可能な日</p>
        </div>
        <div class="rb-times">
            <p class="rb-times-title" data-rb-times-title>日付を選択してください</p>
            <div class="rb-time-list" data-rb-times></div>
        </div>
        <p class="rb-loading" data-rb-loading>空き状況を確認しています…</p>
    </div>
    <?php
}

function riff_booking_render_form($contact_url) {
    $privacy_url = get_privacy_policy_url() ?: home_url('/privacy-policy/');
    ?>
    <div class="riff-booking" data-rb-mode="book">
        <div class="form-header">
            <h2 class="form-title">オンライン面談のご予約</h2>
            <p class="form-description">
                ご都合のよい日時を選んでご予約ください。Google Meetを使った無料のオンライン相談です。<br>
                「ちょっと聞いてみたい」「まずは状況だけ見てほしい」といったご相談も、お気軽にどうぞ。<br>
                受付時間：年中無休 <?php echo (int) RIFF_BOOKING_OPEN_HOUR; ?>:00〜<?php echo (int) RIFF_BOOKING_CLOSE_HOUR; ?>:00
            </p>
            <p class="rb-alt-link">日程調整が不要なご相談は<a href="<?php echo esc_url($contact_url); ?>">お問い合わせフォーム</a>からどうぞ。</p>
        </div>

        <ol class="rb-steps" aria-label="予約の手順">
            <li class="is-active" data-rb-step-label="1"><span>1</span>日時を選ぶ</li>
            <li data-rb-step-label="2"><span>2</span>お客様情報</li>
            <li data-rb-step-label="3"><span>3</span>予約完了</li>
        </ol>

        <p class="rb-error" data-rb-error role="alert" hidden></p>

        <section class="rb-step" data-rb-step="1">
            <?php riff_booking_render_picker(); ?>
            <div class="rb-request-box">
                <p>ご希望の日時が見つからない場合（受付時間外・<?php echo (int) RIFF_BOOKING_HORIZON_DAYS; ?>日より先など）は、日時を指定せずにご依頼ください。担当者が調整してご連絡します。</p>
                <button type="button" class="rb-btn-outline" data-rb-request>希望の日時がない場合はこちら</button>
            </div>
        </section>

        <section class="rb-step" data-rb-step="2" hidden>
            <div class="rb-selected">
                <span class="rb-selected-label" data-rb-selected-label>ご予約日時</span>
                <strong data-rb-selected></strong>
                <button type="button" class="rb-link-btn" data-rb-back>日時を選び直す</button>
            </div>

            <form class="rb-form" data-rb-form novalidate>
                <div class="form-row">
                    <label class="form-label" for="rb-company">会社名</label>
                    <input class="form-input" id="rb-company" name="company" type="text" autocomplete="organization">
                </div>
                <div class="form-row required">
                    <label class="form-label" for="rb-name">氏名<span class="required-mark">*</span></label>
                    <input class="form-input" id="rb-name" name="name" type="text" autocomplete="name" required>
                </div>
                <div class="form-row required">
                    <label class="form-label" for="rb-email">メールアドレス<span class="required-mark">*</span></label>
                    <input class="form-input" id="rb-email" name="email" type="email" autocomplete="email" required>
                    <p class="rb-help">予約確認メールとGoogle MeetのURLをお送りします。</p>
                </div>
                <div class="form-row required">
                    <label class="form-label" for="rb-phone">電話番号<span class="required-mark">*</span></label>
                    <input class="form-input" id="rb-phone" name="phone" type="tel" autocomplete="tel" inputmode="tel" required>
                </div>
                <div class="form-row required">
                    <label class="form-label" for="rb-inquiry">お問い合わせ種別<span class="required-mark">*</span></label>
                    <select class="form-select" id="rb-inquiry" name="inquiry_type" required>
                        <option value="">選択してください</option>
                        <?php foreach (riff_booking_inquiry_types() as $type): ?>
                        <option value="<?php echo esc_attr($type); ?>"><?php echo esc_html($type); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <label class="form-label" for="rb-site-url">現在のホームページURL</label>
                    <input class="form-input" id="rb-site-url" name="site_url" type="url" placeholder="https://" inputmode="url">
                </div>
                <div class="rb-form-cols">
                    <div class="form-row">
                        <label class="form-label" for="rb-budget">ご予算</label>
                        <select class="form-select" id="rb-budget" name="budget">
                            <option value="">選択してください</option>
                            <?php foreach (riff_booking_budget_options() as $o): ?>
                            <option value="<?php echo esc_attr($o); ?>"><?php echo esc_html($o); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="form-label" for="rb-timing">ご希望の時期</label>
                        <select class="form-select" id="rb-timing" name="timing">
                            <option value="">選択してください</option>
                            <?php foreach (riff_booking_timing_options() as $o): ?>
                            <option value="<?php echo esc_attr($o); ?>"><?php echo esc_html($o); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row" data-rb-note-row>
                    <label class="form-label" for="rb-note">備考<span class="required-mark" data-rb-note-required hidden>*</span></label>
                    <textarea class="form-textarea" id="rb-note" name="note" data-rb-note
                              data-placeholder-book="ご相談内容やご質問があればご記入ください"
                              data-placeholder-request="ご希望の日時を複数ご記入ください（例：10/10 21時以降、10/12 午前中など）&#10;ご相談内容もあわせてご記入ください"
                              placeholder="ご相談内容やご質問があればご記入ください"></textarea>
                </div>
                <div class="form-row required rb-privacy">
                    <label>
                        <input type="checkbox" name="privacy" value="1" required>
                        <a href="<?php echo esc_url($privacy_url); ?>" target="_blank" rel="noopener">プライバシーポリシー</a>に同意する<span class="required-mark">*</span>
                    </label>
                </div>
                <div class="turnstile-row" data-rb-turnstile></div>
                <div class="submit-row">
                    <button type="submit" class="submit-btn" data-rb-submit>この内容で予約する</button>
                </div>
            </form>
        </section>

        <section class="rb-step rb-done" data-rb-step="3" hidden>
            <div class="rb-done-icon" aria-hidden="true">&#10003;</div>
            <h3 class="rb-done-title" data-rb-done-title>ご予約が確定しました</h3>
            <p class="rb-done-datetime" data-rb-done-datetime></p>
            <p class="rb-done-text" data-rb-done-text>ご入力のメールアドレスに、Google MeetのURLを記載した確認メールをお送りしました。<br>メールが届かない場合は、迷惑メールフォルダもご確認ください。</p>
        </section>
    </div>
    <?php
}

function riff_booking_render_manage($booking, $contact_url) {
    ?>
    <div class="riff-booking" data-rb-mode="manage">
        <div class="form-header">
            <h2 class="form-title">ご予約の確認・変更</h2>
        </div>
        <?php if (!$booking): ?>
            <p class="riff-booking-notice">ご予約が見つかりませんでした。メールに記載のURLをご確認いただくか、<a href="<?php echo esc_url($contact_url); ?>">お問い合わせフォーム</a>からご連絡ください。</p>
        <?php elseif ($booking['status'] !== 'confirmed' || $booking['start'] <= time()): ?>
            <p class="riff-booking-notice">
                <?php
                if ($booking['status'] === 'cancelled') {
                    echo 'このご予約はキャンセル済みです。';
                } elseif ($booking['status'] === 'request') {
                    echo 'このご予約は日時調整中です。担当者からのご連絡をお待ちください。';
                } else {
                    echo '開始時刻を過ぎたご予約は変更・キャンセルできません。';
                }
                ?>
                <br><a href="<?php echo esc_url(riff_booking_page_url()); ?>">新しく予約する</a>
            </p>
        <?php else: ?>
            <p class="rb-error" data-rb-error role="alert" hidden></p>
            <div class="rb-summary" data-rb-summary>
                <dl>
                    <dt>日時</dt><dd data-rb-current><?php echo esc_html(riff_booking_format_range($booking['start'], $booking['end'])); ?></dd>
                    <dt>面談方法</dt><dd>オンライン（Google Meet）</dd>
                    <?php if ($booking['meet_url']): ?>
                    <dt>Meet URL</dt><dd><a href="<?php echo esc_url($booking['meet_url']); ?>" target="_blank" rel="noopener"><?php echo esc_html($booking['meet_url']); ?></a></dd>
                    <?php endif; ?>
                    <dt>お名前</dt><dd><?php echo esc_html(($booking['company'] ? $booking['company'] . ' ' : '') . $booking['name']); ?> 様</dd>
                </dl>
                <div class="rb-manage-actions">
                    <button type="button" class="submit-btn" data-rb-open-reschedule>日程を変更する</button>
                    <button type="button" class="rb-btn-outline rb-btn-danger" data-rb-open-cancel>予約をキャンセルする</button>
                </div>
            </div>

            <section class="rb-panel" data-rb-reschedule hidden>
                <h3 class="rb-panel-title">新しい日時を選択してください</h3>
                <?php riff_booking_render_picker(); ?>
                <div class="rb-confirm-row" data-rb-reschedule-confirm hidden>
                    <p>変更後：<strong data-rb-selected></strong></p>
                    <button type="button" class="submit-btn" data-rb-do-reschedule>この日時に変更する</button>
                </div>
                <button type="button" class="rb-link-btn" data-rb-close>戻る</button>
            </section>

            <section class="rb-panel" data-rb-cancel hidden>
                <h3 class="rb-panel-title">ご予約をキャンセルしますか？</h3>
                <p>キャンセルすると元に戻せません。日程の変更をご希望の場合は「日程を変更する」をご利用ください。</p>
                <div class="rb-manage-actions">
                    <button type="button" class="rb-btn-outline rb-btn-danger" data-rb-do-cancel>キャンセルを確定する</button>
                    <button type="button" class="rb-link-btn" data-rb-close>戻る</button>
                </div>
            </section>

            <section class="rb-done" data-rb-manage-done hidden>
                <div class="rb-done-icon" aria-hidden="true">&#10003;</div>
                <h3 class="rb-done-title" data-rb-done-title></h3>
                <p class="rb-done-text" data-rb-done-text></p>
            </section>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * お問い合わせページなどに置く予約ページへの案内（[riff_booking_banner]）。
 * 予約を受け付けていない間は何も表示しない。
 */
add_shortcode('riff_booking_banner', function () {
    if (!riff_booking_is_ready()) {
        return '';
    }
    wp_enqueue_style('riff-booking', get_template_directory_uri() . '/assets/css/booking.css', array(), filemtime(get_template_directory() . '/assets/css/booking.css'));
    return '<div class="rb-banner"><p class="rb-banner-text"><strong>オンライン面談（無料・約' . (int) RIFF_BOOKING_DURATION_MIN . '分）</strong>をご希望の方は、カレンダーから空いている日時をそのままご予約いただけます。</p>'
        . '<a class="rb-banner-btn" href="' . esc_url(riff_booking_page_url()) . '">オンライン面談を予約する</a></div>';
});

// 予約ページはお客様ごとの内容（トークン）を扱うため、検索結果に出さずキャッシュもさせない
add_action('template_redirect', function () {
    $s = riff_booking_settings();
    if ($s['page_id'] && is_page($s['page_id']) && isset($_GET['rb'])) {
        nocache_headers();
        add_filter('wp_robots', 'wp_robots_no_robots');
    }
});
