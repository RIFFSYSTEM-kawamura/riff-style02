<?php
/**
 * 日程予約：フロント画面から呼ぶREST API
 *
 * GET  /riff-booking/v1/slots             空き枠一覧（token 付きなら自分の予約枠も空きとして扱う）
 * POST /riff-booking/v1/book              予約／日時調整リクエスト
 * POST /riff-booking/v1/reschedule        日程変更（token）
 * POST /riff-booking/v1/cancel            キャンセル（token）
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {
    $ns = 'riff-booking/v1';
    register_rest_route($ns, '/slots', array(
        'methods'             => 'GET',
        'callback'            => 'riff_booking_rest_slots',
        'permission_callback' => '__return_true',
    ));
    register_rest_route($ns, '/book', array(
        'methods'             => 'POST',
        'callback'            => 'riff_booking_rest_book',
        'permission_callback' => '__return_true',
    ));
    register_rest_route($ns, '/reschedule', array(
        'methods'             => 'POST',
        'callback'            => 'riff_booking_rest_reschedule',
        'permission_callback' => '__return_true',
    ));
    register_rest_route($ns, '/cancel', array(
        'methods'             => 'POST',
        'callback'            => 'riff_booking_rest_cancel',
        'permission_callback' => '__return_true',
    ));
});

function riff_booking_rest_error($error, $status = 400) {
    return new WP_REST_Response(array('ok' => false, 'message' => $error instanceof WP_Error ? $error->get_error_message() : $error), $status);
}

/** 管理中（変更・キャンセル可能）の予約をトークンから取得する */
function riff_booking_rest_manageable($token) {
    $b = riff_booking_sync_from_calendar(riff_booking_find_by_token($token));
    if (!$b) {
        return new WP_Error('riff_booking_not_found', 'ご予約が見つかりませんでした。URLをご確認ください。');
    }
    if ($b['status'] !== 'confirmed') {
        return new WP_Error('riff_booking_closed', $b['status'] === 'cancelled' ? 'このご予約はキャンセル済みです。' : 'このご予約は日時調整中のため、担当者からのご連絡をお待ちください。');
    }
    if ($b['start'] <= time()) {
        return new WP_Error('riff_booking_past', '開始時刻を過ぎたご予約は変更・キャンセルできません。');
    }
    return $b;
}

function riff_booking_rest_slots(WP_REST_Request $request) {
    if (!riff_booking_is_ready()) {
        return riff_booking_rest_error('現在、日程予約を受け付けておりません。', 503);
    }
    $ignore = null;
    if ($request->get_param('token')) {
        $b = riff_booking_rest_manageable($request->get_param('token'));
        if (is_wp_error($b)) {
            return riff_booking_rest_error($b, 404);
        }
        $ignore = array($b['start'], $b['end']);
    }
    $days = riff_booking_available_slots($ignore);
    if (is_wp_error($days)) {
        return riff_booking_rest_error('空き状況を取得できませんでした。時間をおいて再度お試しください。', 503);
    }
    return new WP_REST_Response(array('ok' => true, 'days' => $days), 200);
}

/** 'Y-m-d' と 'H:i' から受付ルールに合う開始UNIX時刻を得る */
function riff_booking_parse_slot($date, $time) {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date) || !preg_match('/^\d{2}:\d{2}$/', (string) $time)) {
        return new WP_Error('riff_booking_slot', '日時を選択してください。');
    }
    $dt = DateTimeImmutable::createFromFormat('!Y-m-d H:i', $date . ' ' . $time, wp_timezone());
    if (!$dt || !riff_booking_is_valid_slot_start($dt->getTimestamp())) {
        return new WP_Error('riff_booking_slot', '選択された日時は予約できません。別の日時をお選びください。');
    }
    return $dt->getTimestamp();
}

function riff_booking_verify_turnstile($token) {
    // サイトキーとシークレットはお問い合わせフォーム（Contact Form 7）の設定を共用する
    $keys = class_exists('WPCF7') ? (array) WPCF7::get_option('turnstile') : array();
    $sitekey = array_key_first($keys);
    if (!$sitekey) {
        return true; // Turnstile未設定の環境では検証しない
    }
    $res = wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', array(
        'timeout' => 10,
        'body'    => array('secret' => $keys[$sitekey], 'response' => (string) $token, 'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''),
    ));
    $body = json_decode(wp_remote_retrieve_body($res), true);
    return !is_wp_error($res) && !empty($body['success']);
}

function riff_booking_rest_book(WP_REST_Request $request) {
    if (!riff_booking_is_ready()) {
        return riff_booking_rest_error('現在、日程予約を受け付けておりません。', 503);
    }
    $p = $request->get_json_params() ?: $request->get_body_params();

    $fields = array(
        'company'      => sanitize_text_field($p['company'] ?? ''),
        'name'         => sanitize_text_field($p['name'] ?? ''),
        'email'        => sanitize_email($p['email'] ?? ''),
        'phone'        => sanitize_text_field($p['phone'] ?? ''),
        'inquiry_type' => sanitize_text_field($p['inquiry_type'] ?? ''),
        'site_url'     => esc_url_raw($p['site_url'] ?? ''),
        'budget'       => sanitize_text_field($p['budget'] ?? ''),
        'timing'       => sanitize_text_field($p['timing'] ?? ''),
        'note'         => sanitize_textarea_field($p['note'] ?? ''),
    );
    $is_request = !empty($p['request_mode']);

    $errors = array();
    if ($fields['name'] === '') {
        $errors[] = '氏名を入力してください。';
    }
    if (!is_email($fields['email'])) {
        $errors[] = 'メールアドレスを正しく入力してください。';
    }
    if (!preg_match('/^[0-9０-９\-－+() ]{10,20}$/u', $fields['phone'])) {
        $errors[] = '電話番号を正しく入力してください。';
    }
    if (!in_array($fields['inquiry_type'], riff_booking_inquiry_types(), true)) {
        $errors[] = 'お問い合わせ種別を選択してください。';
    }
    if ($fields['budget'] !== '' && !in_array($fields['budget'], riff_booking_budget_options(), true)) {
        $fields['budget'] = '';
    }
    if ($fields['timing'] !== '' && !in_array($fields['timing'], riff_booking_timing_options(), true)) {
        $fields['timing'] = '';
    }
    if ($is_request && mb_strlen($fields['note']) < 5) {
        $errors[] = '備考にご希望の日時をご記入ください。';
    }
    if (empty($p['privacy'])) {
        $errors[] = 'プライバシーポリシーへの同意が必要です。';
    }
    if ($errors) {
        return riff_booking_rest_error(implode("\n", $errors));
    }
    if (!riff_booking_verify_turnstile($p['turnstile'] ?? '')) {
        return riff_booking_rest_error('スパム対策の確認に失敗しました。ページを再読み込みしてお試しください。');
    }

    $start = null;
    if (!$is_request) {
        $start = riff_booking_parse_slot($p['date'] ?? '', $p['time'] ?? '');
        if (is_wp_error($start)) {
            return riff_booking_rest_error($start);
        }
    }

    $booking = riff_booking_create($fields, $start);
    if (is_wp_error($booking)) {
        return riff_booking_rest_error($booking, $booking->get_error_code() === 'riff_booking_taken' ? 409 : 500);
    }
    return new WP_REST_Response(array(
        'ok'       => true,
        'status'   => $booking['status'],
        'datetime' => $booking['start'] ? riff_booking_format_range($booking['start'], $booking['end']) : '',
    ), 200);
}

function riff_booking_rest_reschedule(WP_REST_Request $request) {
    $p = $request->get_json_params() ?: $request->get_body_params();
    $b = riff_booking_rest_manageable($p['token'] ?? '');
    if (is_wp_error($b)) {
        return riff_booking_rest_error($b, 404);
    }
    $start = riff_booking_parse_slot($p['date'] ?? '', $p['time'] ?? '');
    if (is_wp_error($start)) {
        return riff_booking_rest_error($start);
    }
    if ($start === $b['start']) {
        return riff_booking_rest_error('現在のご予約と同じ日時です。別の日時をお選びください。');
    }
    $updated = riff_booking_reschedule($b, $start);
    if (is_wp_error($updated)) {
        return riff_booking_rest_error($updated, $updated->get_error_code() === 'riff_booking_taken' ? 409 : 500);
    }
    return new WP_REST_Response(array('ok' => true, 'datetime' => riff_booking_format_range($updated['start'], $updated['end'])), 200);
}

function riff_booking_rest_cancel(WP_REST_Request $request) {
    $p = $request->get_json_params() ?: $request->get_body_params();
    $b = riff_booking_rest_manageable($p['token'] ?? '');
    if (is_wp_error($b)) {
        return riff_booking_rest_error($b, 404);
    }
    $res = riff_booking_cancel($b);
    if (is_wp_error($res)) {
        return riff_booking_rest_error($res, 500);
    }
    return new WP_REST_Response(array('ok' => true), 200);
}
