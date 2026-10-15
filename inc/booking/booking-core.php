<?php
/**
 * 日程予約：設定・Googleカレンダー連携・空き枠計算・予約データ
 *
 * 予約は非公開のカスタム投稿タイプ riff_booking に保存し、Googleカレンダーには
 * Google Meet付きの予定として書き込む。空き判定は連携アカウントの複数カレンダーを
 * freeBusy APIでまとめて参照する。
 */

if (!defined('ABSPATH')) {
    exit;
}

// 受付ルール（年中無休 10:00〜22:00 / 60分枠 / 前後60分空け / 30日先まで）
const RIFF_BOOKING_OPEN_HOUR     = 10;
const RIFF_BOOKING_CLOSE_HOUR    = 22;
const RIFF_BOOKING_DURATION_MIN  = 60;
const RIFF_BOOKING_BUFFER_MIN    = 60;
const RIFF_BOOKING_STEP_MIN      = 30;
const RIFF_BOOKING_HORIZON_DAYS  = 30;
// 当日予約も受けるが、準備時間として開始の1時間前までに締め切る
const RIFF_BOOKING_LEAD_MIN      = 60;

const RIFF_BOOKING_OPTION        = 'riff_booking_settings';
const RIFF_BOOKING_POST_TYPE     = 'riff_booking';
const RIFF_BOOKING_GOOGLE_SCOPE  = 'https://www.googleapis.com/auth/calendar.readonly https://www.googleapis.com/auth/calendar.events';

function riff_booking_inquiry_types() {
    return array('Web制作について', 'LPについて', 'SEO・MEO・AIOなどの集客について', 'システム開発について', 'GENBOXについて', 'その他');
}

function riff_booking_budget_options() {
    return array('未定', '〜30万円', '30〜50万円', '50〜100万円', '100万円以上');
}

function riff_booking_timing_options() {
    return array('未定', 'すぐにでも', '1〜3ヶ月以内', '3〜6ヶ月以内', '6ヶ月以上先');
}

/* ------------------------------------------------------------------
 * 設定
 * ------------------------------------------------------------------ */

function riff_booking_settings() {
    $defaults = array(
        'client_id'          => '',
        'client_secret'      => '',
        'refresh_token'      => '',
        'connected_email'    => '',
        'busy_calendar_ids'  => array(),
        'notify_email'       => 'info@riff-system.com',
        'page_id'            => 0,
    );
    return wp_parse_args(get_option(RIFF_BOOKING_OPTION, array()), $defaults);
}

function riff_booking_update_settings($changes) {
    update_option(RIFF_BOOKING_OPTION, array_merge(riff_booking_settings(), $changes), false);
}

/** Google連携と予約ページが揃い、予約を受け付けられる状態か */
function riff_booking_is_ready() {
    $s = riff_booking_settings();
    return $s['refresh_token'] !== '' && $s['page_id'] && get_post_status($s['page_id']) === 'publish';
}

function riff_booking_page_url($args = array()) {
    $s = riff_booking_settings();
    $url = $s['page_id'] ? get_permalink($s['page_id']) : home_url('/reservation/');
    return $args ? add_query_arg($args, $url) : $url;
}

// リフレッシュトークンはDBに平文で残さない
function riff_booking_crypt_key() {
    return hash('sha256', AUTH_KEY . SECURE_AUTH_KEY, true);
}

function riff_booking_encrypt($plain) {
    $iv = random_bytes(16);
    $cipher = openssl_encrypt($plain, 'aes-256-cbc', riff_booking_crypt_key(), OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $cipher);
}

function riff_booking_decrypt($encoded) {
    $raw = base64_decode($encoded, true);
    if ($raw === false || strlen($raw) <= 16) {
        return '';
    }
    $plain = openssl_decrypt(substr($raw, 16), 'aes-256-cbc', riff_booking_crypt_key(), OPENSSL_RAW_DATA, substr($raw, 0, 16));
    return $plain === false ? '' : $plain;
}

/* ------------------------------------------------------------------
 * Google API
 * ------------------------------------------------------------------ */

function riff_booking_oauth_redirect_uri() {
    return admin_url('admin-post.php?action=riff_booking_oauth');
}

function riff_booking_oauth_auth_url($state) {
    $s = riff_booking_settings();
    return add_query_arg(array(
        'client_id'     => $s['client_id'],
        'redirect_uri'  => riff_booking_oauth_redirect_uri(),
        'response_type' => 'code',
        'scope'         => RIFF_BOOKING_GOOGLE_SCOPE,
        'access_type'   => 'offline',
        'prompt'        => 'consent',
        'login_hint'    => 'riffsystem01@gmail.com',
        'state'         => $state,
    ), 'https://accounts.google.com/o/oauth2/v2/auth');
}

/** 認可コードをリフレッシュトークンに交換して保存する */
function riff_booking_oauth_exchange_code($code) {
    $s = riff_booking_settings();
    $res = wp_remote_post('https://oauth2.googleapis.com/token', array(
        'timeout' => 15,
        'body'    => array(
            'code'          => $code,
            'client_id'     => $s['client_id'],
            'client_secret' => $s['client_secret'],
            'redirect_uri'  => riff_booking_oauth_redirect_uri(),
            'grant_type'    => 'authorization_code',
        ),
    ));
    $body = json_decode(wp_remote_retrieve_body($res), true);
    if (is_wp_error($res) || empty($body['refresh_token'])) {
        return new WP_Error('riff_booking_oauth', 'Googleの認証に失敗しました：' . (is_wp_error($res) ? $res->get_error_message() : ($body['error_description'] ?? $body['error'] ?? '不明なエラー')));
    }
    riff_booking_update_settings(array('refresh_token' => riff_booking_encrypt($body['refresh_token'])));
    set_transient('riff_booking_access_token', $body['access_token'], max(60, (int) $body['expires_in'] - 60));

    $primary = riff_booking_google_request('GET', '/calendars/primary');
    if (!is_wp_error($primary)) {
        riff_booking_update_settings(array('connected_email' => $primary['id']));
    }
    return true;
}

function riff_booking_access_token() {
    $cached = get_transient('riff_booking_access_token');
    if ($cached) {
        return $cached;
    }
    $s = riff_booking_settings();
    $refresh = riff_booking_decrypt($s['refresh_token']);
    if ($refresh === '') {
        return new WP_Error('riff_booking_not_connected', 'Googleカレンダーが連携されていません。');
    }
    $res = wp_remote_post('https://oauth2.googleapis.com/token', array(
        'timeout' => 15,
        'body'    => array(
            'client_id'     => $s['client_id'],
            'client_secret' => $s['client_secret'],
            'refresh_token' => $refresh,
            'grant_type'    => 'refresh_token',
        ),
    ));
    $body = json_decode(wp_remote_retrieve_body($res), true);
    if (is_wp_error($res) || empty($body['access_token'])) {
        $reason = is_wp_error($res) ? $res->get_error_message() : ($body['error'] ?? '不明なエラー');
        riff_booking_log('アクセストークンの更新に失敗: ' . $reason);
        if (($body['error'] ?? '') === 'invalid_grant') {
            riff_booking_alert_admin_once('Googleカレンダーの連携が切れました', "Googleカレンダーの連携が無効になったため、日程予約を受け付けられません。\n管理画面の「RIFFテーマ → 日程予約設定」から再連携してください。\n\n" . admin_url('admin.php?page=riff-booking-settings'));
        }
        return new WP_Error('riff_booking_token', 'Googleカレンダーに接続できませんでした。');
    }
    set_transient('riff_booking_access_token', $body['access_token'], max(60, (int) $body['expires_in'] - 60));
    return $body['access_token'];
}

/**
 * Calendar APIを呼ぶ。$path は /calendar/v3 以下のパス。
 * 成功時はデコード済み配列（DELETE時は空配列）を返す。
 */
function riff_booking_google_request($method, $path, $body = null, $query = array()) {
    $token = riff_booking_access_token();
    if (is_wp_error($token)) {
        return $token;
    }
    $url = 'https://www.googleapis.com/calendar/v3' . $path;
    if ($query) {
        $url = add_query_arg($query, $url);
    }
    $args = array(
        'method'  => $method,
        'timeout' => 15,
        'headers' => array('Authorization' => 'Bearer ' . $token, 'Content-Type' => 'application/json'),
    );
    if ($body !== null) {
        $args['body'] = wp_json_encode($body);
    }
    $res = wp_remote_request($url, $args);
    if (is_wp_error($res)) {
        riff_booking_log("{$method} {$path} 通信エラー: " . $res->get_error_message());
        return $res;
    }
    $code = wp_remote_retrieve_response_code($res);
    $data = json_decode(wp_remote_retrieve_body($res), true);
    if ($code >= 300) {
        $message = $data['error']['message'] ?? ('HTTP ' . $code);
        riff_booking_log("{$method} {$path} 失敗: {$message}");
        return new WP_Error('riff_booking_google', $message, array('status' => $code));
    }
    return is_array($data) ? $data : array();
}

/** 連携アカウントで参照できるカレンダー一覧 */
function riff_booking_list_calendars() {
    $data = riff_booking_google_request('GET', '/users/me/calendarList', null, array('maxResults' => 250));
    return is_wp_error($data) ? $data : ($data['items'] ?? array());
}

/** 空き判定に使うカレンダー。未設定ならメインカレンダーのみ */
function riff_booking_busy_calendar_ids() {
    $ids = riff_booking_settings()['busy_calendar_ids'];
    return $ids ? $ids : array('primary');
}

/**
 * 指定期間の予定あり区間を [開始UNIX, 終了UNIX] の配列で返す。
 * 空き枠の表示用には短時間キャッシュし、予約確定時は $fresh で必ず取り直す。
 */
function riff_booking_fetch_busy($from_ts, $to_ts, $fresh = false) {
    $cache_key = 'riff_booking_busy_' . md5($from_ts . '-' . $to_ts . '-' . implode(',', riff_booking_busy_calendar_ids()));
    if (!$fresh) {
        $cached = get_transient($cache_key);
        if (is_array($cached)) {
            return $cached;
        }
    }
    $items = array();
    foreach (riff_booking_busy_calendar_ids() as $id) {
        $items[] = array('id' => $id);
    }
    $data = riff_booking_google_request('POST', '/freeBusy', array(
        'timeMin'  => gmdate('c', $from_ts),
        'timeMax'  => gmdate('c', $to_ts),
        'timeZone' => 'Asia/Tokyo',
        'items'    => $items,
    ));
    if (is_wp_error($data)) {
        return $data;
    }
    $busy = array();
    foreach (($data['calendars'] ?? array()) as $id => $calendar) {
        // 参照権限のないカレンダーを空きと誤認しないよう、エラーは予約停止扱いにする
        if (!empty($calendar['errors'])) {
            riff_booking_log("freeBusy {$id} エラー: " . wp_json_encode($calendar['errors']));
            return new WP_Error('riff_booking_freebusy', 'カレンダーの空き状況を取得できませんでした。');
        }
        foreach (($calendar['busy'] ?? array()) as $b) {
            $busy[] = array(strtotime($b['start']), strtotime($b['end']));
        }
    }
    set_transient($cache_key, $busy, 60);
    return $busy;
}

function riff_booking_flush_busy_cache() {
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '\\_transient\\_riff\\_booking\\_busy\\_%' OR option_name LIKE '\\_transient\\_timeout\\_riff\\_booking\\_busy\\_%'");
}

/** 予定あり区間から [開始, 終了] を除く（日程変更時に自分の予約枠を空きとして扱うため） */
function riff_booking_subtract_interval($busy, $start, $end) {
    $result = array();
    foreach ($busy as $b) {
        if ($b[1] <= $start || $b[0] >= $end) {
            $result[] = $b;
            continue;
        }
        if ($b[0] < $start) {
            $result[] = array($b[0], $start);
        }
        if ($b[1] > $end) {
            $result[] = array($end, $b[1]);
        }
    }
    return $result;
}

/* ------------------------------------------------------------------
 * 空き枠
 * ------------------------------------------------------------------ */

/** 受付期間（今日〜30日後）の [開始UNIX, 終了UNIX] */
function riff_booking_window() {
    $tz = wp_timezone();
    $today = new DateTimeImmutable('today', $tz);
    return array($today->getTimestamp(), $today->modify('+' . (RIFF_BOOKING_HORIZON_DAYS + 1) . ' days')->getTimestamp());
}

/** その枠が受付ルール上の正規の開始時刻か（任意の時刻を送り込まれるのを防ぐ） */
function riff_booking_is_valid_slot_start($ts) {
    list($from, $to) = riff_booking_window();
    if ($ts < $from || $ts >= $to || $ts < time() + RIFF_BOOKING_LEAD_MIN * 60) {
        return false;
    }
    $dt = (new DateTimeImmutable('@' . $ts))->setTimezone(wp_timezone());
    $minutes = (int) $dt->format('G') * 60 + (int) $dt->format('i');
    return (int) $dt->format('s') === 0
        && $minutes % RIFF_BOOKING_STEP_MIN === 0
        && $minutes >= RIFF_BOOKING_OPEN_HOUR * 60
        && $minutes + RIFF_BOOKING_DURATION_MIN <= RIFF_BOOKING_CLOSE_HOUR * 60;
}

/** 前後の空け時間を含めて予定と重ならないか */
function riff_booking_slot_is_free($start, $busy) {
    $guard_start = $start - RIFF_BOOKING_BUFFER_MIN * 60;
    $guard_end   = $start + (RIFF_BOOKING_DURATION_MIN + RIFF_BOOKING_BUFFER_MIN) * 60;
    foreach ($busy as $b) {
        if ($b[0] < $guard_end && $b[1] > $guard_start) {
            return false;
        }
    }
    return true;
}

/**
 * 受付期間内の空き枠を日付ごとに返す。 array('2026-10-05' => array('10:00', ...), ...)
 * $ignore は日程変更時に除外する自分の予約枠 [開始, 終了]。
 */
function riff_booking_available_slots($ignore = null) {
    list($from, $to) = riff_booking_window();
    $busy = riff_booking_fetch_busy($from - RIFF_BOOKING_BUFFER_MIN * 60, $to + RIFF_BOOKING_BUFFER_MIN * 60);
    if (is_wp_error($busy)) {
        return $busy;
    }
    if ($ignore) {
        $busy = riff_booking_subtract_interval($busy, $ignore[0], $ignore[1]);
    }

    $tz = wp_timezone();
    $days = array();
    $day = new DateTimeImmutable('today', $tz);
    for ($i = 0; $i <= RIFF_BOOKING_HORIZON_DAYS; $i++, $day = $day->modify('+1 day')) {
        $slots = array();
        $start = $day->setTime(RIFF_BOOKING_OPEN_HOUR, 0);
        $last  = $day->setTime(RIFF_BOOKING_CLOSE_HOUR, 0)->modify('-' . RIFF_BOOKING_DURATION_MIN . ' minutes');
        for ($t = $start; $t <= $last; $t = $t->modify('+' . RIFF_BOOKING_STEP_MIN . ' minutes')) {
            $ts = $t->getTimestamp();
            if (riff_booking_is_valid_slot_start($ts) && riff_booking_slot_is_free($ts, $busy)) {
                $slots[] = $t->format('H:i');
            }
        }
        $days[$day->format('Y-m-d')] = $slots;
    }
    return $days;
}

/** 予約確定直前に、キャッシュを使わず最新の予定で空きを再確認する */
function riff_booking_recheck_slot($start, $ignore = null) {
    $busy = riff_booking_fetch_busy(
        $start - RIFF_BOOKING_BUFFER_MIN * 60,
        $start + (RIFF_BOOKING_DURATION_MIN + RIFF_BOOKING_BUFFER_MIN) * 60,
        true
    );
    if (is_wp_error($busy)) {
        return $busy;
    }
    if ($ignore) {
        $busy = riff_booking_subtract_interval($busy, $ignore[0], $ignore[1]);
    }
    return riff_booking_slot_is_free($start, $busy);
}

/* ------------------------------------------------------------------
 * 予約データ
 * ------------------------------------------------------------------ */

add_action('init', function () {
    register_post_type(RIFF_BOOKING_POST_TYPE, array(
        'labels'          => array(
            'name'          => '日程予約',
            'singular_name' => '日程予約',
            'edit_item'     => '予約の詳細',
            'all_items'     => '予約一覧',
            'search_items'  => '予約を検索',
            'not_found'     => '予約はまだありません',
        ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'menu_icon'       => 'dashicons-calendar-alt',
        'menu_position'   => 26,
        'supports'        => array('title'),
        'capability_type' => 'post',
        'capabilities'    => array('create_posts' => 'do_not_allow'),
        'map_meta_cap'    => true,
    ));
});

/** 予約の全情報を配列で取得する */
function riff_booking_get($post_id) {
    $post = get_post($post_id);
    if (!$post || $post->post_type !== RIFF_BOOKING_POST_TYPE) {
        return null;
    }
    $data = array('id' => $post->ID);
    foreach (riff_booking_meta_keys() as $key) {
        $data[$key] = get_post_meta($post->ID, '_rb_' . $key, true);
    }
    $data['start'] = (int) $data['start'];
    $data['end']   = (int) $data['end'];
    return $data;
}

function riff_booking_meta_keys() {
    return array(
        'status', 'company', 'name', 'email', 'phone', 'inquiry_type', 'site_url', 'budget', 'timing', 'note',
        'start', 'end', 'event_id', 'meet_url', 'token', 'reminded',
    );
}

function riff_booking_save_meta($post_id, $fields) {
    foreach ($fields as $key => $value) {
        update_post_meta($post_id, '_rb_' . $key, $value);
    }
}

function riff_booking_find_by_token($token) {
    if (!is_string($token) || !preg_match('/^[a-f0-9]{40}$/', $token)) {
        return null;
    }
    $ids = get_posts(array(
        'post_type'      => RIFF_BOOKING_POST_TYPE,
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_rb_token',
        'meta_value'     => $token,
    ));
    return $ids ? riff_booking_get($ids[0]) : null;
}

function riff_booking_status_label($status) {
    $labels = array('confirmed' => '予約確定', 'request' => '日時調整中', 'cancelled' => 'キャンセル');
    return $labels[$status] ?? $status;
}

/** 日時の表示用文字列（例：2026年10月5日（月）14:00〜15:00） */
function riff_booking_format_range($start, $end) {
    $week = array('日', '月', '火', '水', '木', '金', '土');
    $s = (new DateTimeImmutable('@' . $start))->setTimezone(wp_timezone());
    $e = (new DateTimeImmutable('@' . $end))->setTimezone(wp_timezone());
    return $s->format('Y年n月j日') . '（' . $week[(int) $s->format('w')] . '）' . $s->format('H:i') . '〜' . $e->format('H:i');
}

function riff_booking_event_payload($booking) {
    $lines = array(
        'RIFF System 日程予約（オンライン面談）',
        '',
        '会社名：' . ($booking['company'] ?: '—'),
        '氏名：' . $booking['name'],
        'メール：' . $booking['email'],
        '電話番号：' . $booking['phone'],
        'お問い合わせ種別：' . $booking['inquiry_type'],
        '現在のホームページURL：' . ($booking['site_url'] ?: '—'),
        'ご予算：' . ($booking['budget'] ?: '—'),
        'ご希望の時期：' . ($booking['timing'] ?: '—'),
        '',
        '備考：',
        $booking['note'] ?: '—',
        '',
        '予約管理：' . admin_url('post.php?post=' . $booking['id'] . '&action=edit'),
    );
    return array(
        'summary'     => '【日程予約】' . ($booking['company'] ? $booking['company'] . ' ' : '') . $booking['name'] . '様（' . $booking['inquiry_type'] . '）',
        'description' => implode("\n", $lines),
        'start'       => array('dateTime' => gmdate('c', $booking['start']), 'timeZone' => 'Asia/Tokyo'),
        'end'         => array('dateTime' => gmdate('c', $booking['end']), 'timeZone' => 'Asia/Tokyo'),
    );
}

/** Googleカレンダーに Meet付きの予定を作り、予定IDとMeetのURLを保存する */
function riff_booking_create_event($booking) {
    $payload = riff_booking_event_payload($booking);
    $payload['conferenceData'] = array(
        'createRequest' => array(
            'requestId'             => 'riff-booking-' . $booking['id'] . '-' . wp_generate_password(8, false),
            'conferenceSolutionKey' => array('type' => 'hangoutsMeet'),
        ),
    );
    $event = riff_booking_google_request('POST', '/calendars/primary/events', $payload, array('conferenceDataVersion' => 1, 'sendUpdates' => 'none'));
    if (is_wp_error($event)) {
        return $event;
    }
    $meet = $event['hangoutLink'] ?? '';
    riff_booking_save_meta($booking['id'], array('event_id' => $event['id'], 'meet_url' => $meet));
    riff_booking_flush_busy_cache();
    return array('event_id' => $event['id'], 'meet_url' => $meet);
}

function riff_booking_update_event_time($booking) {
    $payload = riff_booking_event_payload($booking);
    $res = riff_booking_google_request('PATCH', '/calendars/primary/events/' . rawurlencode($booking['event_id']), array(
        'start' => $payload['start'],
        'end'   => $payload['end'],
    ), array('sendUpdates' => 'none'));
    riff_booking_flush_busy_cache();
    return $res;
}

function riff_booking_delete_event($booking) {
    if (!$booking['event_id']) {
        return true;
    }
    $res = riff_booking_google_request('DELETE', '/calendars/primary/events/' . rawurlencode($booking['event_id']), null, array('sendUpdates' => 'none'));
    riff_booking_flush_busy_cache();
    // すでにカレンダー側で削除済みなら成功扱い
    if (is_wp_error($res) && in_array($res->get_error_data()['status'] ?? 0, array(404, 410), true)) {
        return true;
    }
    return $res;
}

/**
 * 同じ枠への同時予約を防ぐため、空き再確認〜予定作成をMySQLのロックで直列化する
 */
function riff_booking_with_lock($callback) {
    global $wpdb;
    $got = $wpdb->get_var("SELECT GET_LOCK('riff_booking', 15)");
    if ((string) $got !== '1') {
        return new WP_Error('riff_booking_busy', '混み合っています。少し時間をおいて再度お試しください。');
    }
    try {
        return $callback();
    } finally {
        $wpdb->query("SELECT RELEASE_LOCK('riff_booking')");
    }
}

/* ------------------------------------------------------------------
 * ログ・管理者への警告
 * ------------------------------------------------------------------ */

function riff_booking_log($message) {
    $log = get_option('riff_booking_log', array());
    array_unshift($log, wp_date('Y-m-d H:i:s') . ' ' . $message);
    update_option('riff_booking_log', array_slice($log, 0, 50), false);
}

/** 同じ警告メールを1日1回までに抑えて管理者に送る */
function riff_booking_alert_admin_once($subject, $body) {
    $key = 'riff_booking_alert_' . md5($subject);
    if (get_transient($key)) {
        return;
    }
    set_transient($key, 1, DAY_IN_SECONDS);
    wp_mail(riff_booking_settings()['notify_email'], '[RIFF System] ' . $subject, $body);
}
