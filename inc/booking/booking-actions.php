<?php
/**
 * 日程予約：予約の作成・日程変更・キャンセル、メール通知、前日リマインド
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 予約を作成する。$start が null の場合は「希望日時がカレンダーにない」リクエストとして受け付け、
 * 管理画面で日時を確定するまで予定は作らない。
 */
function riff_booking_create($fields, $start) {
    $is_request = $start === null;

    $run = function () use ($fields, $start, $is_request) {
        if (!$is_request) {
            $free = riff_booking_recheck_slot($start);
            if (is_wp_error($free)) {
                return $free;
            }
            if (!$free) {
                return new WP_Error('riff_booking_taken', '申し訳ありません。選択された日時は直前に埋まってしまいました。別の日時をお選びください。');
            }
        }

        $post_id = wp_insert_post(array(
            'post_type'   => RIFF_BOOKING_POST_TYPE,
            'post_status' => 'publish',
            'post_title'  => ($is_request ? '【日時調整中】' : riff_booking_format_range($start, $start + RIFF_BOOKING_DURATION_MIN * 60) . ' ')
                . ($fields['company'] ? $fields['company'] . ' ' : '') . $fields['name'],
        ), true);
        if (is_wp_error($post_id)) {
            return $post_id;
        }
        riff_booking_save_meta($post_id, array_merge($fields, array(
            'status'   => $is_request ? 'request' : 'confirmed',
            'start'    => $is_request ? 0 : $start,
            'end'      => $is_request ? 0 : $start + RIFF_BOOKING_DURATION_MIN * 60,
            'token'    => bin2hex(random_bytes(20)),
            'reminded' => 0,
        )));

        if (!$is_request) {
            $event = riff_booking_create_event(riff_booking_get($post_id));
            if (is_wp_error($event)) {
                wp_delete_post($post_id, true);
                return new WP_Error('riff_booking_event', '予約の登録に失敗しました。お手数ですが、お問い合わせフォームからご連絡ください。');
            }
        }
        return $post_id;
    };

    $post_id = $is_request ? $run() : riff_booking_with_lock($run);
    if (is_wp_error($post_id)) {
        return $post_id;
    }

    $booking = riff_booking_get($post_id);
    if ($is_request) {
        riff_booking_mail_customer($booking, 'request');
        riff_booking_mail_admin($booking, 'request');
    } else {
        riff_booking_mail_customer($booking, 'confirmed');
        riff_booking_mail_admin($booking, 'confirmed');
    }
    return $booking;
}

/** 日程変更（お客様のリンクから） */
function riff_booking_reschedule($booking, $new_start) {
    $old = array($booking['start'], $booking['end']);
    $result = riff_booking_with_lock(function () use ($booking, $new_start, $old) {
        $free = riff_booking_recheck_slot($new_start, $old);
        if (is_wp_error($free)) {
            return $free;
        }
        if (!$free) {
            return new WP_Error('riff_booking_taken', '申し訳ありません。選択された日時は直前に埋まってしまいました。別の日時をお選びください。');
        }
        $booking['start'] = $new_start;
        $booking['end']   = $new_start + RIFF_BOOKING_DURATION_MIN * 60;
        $res = riff_booking_update_event_time($booking);
        if (is_wp_error($res)) {
            return new WP_Error('riff_booking_event', '日程の変更に失敗しました。お手数ですが、お問い合わせフォームからご連絡ください。');
        }
        riff_booking_save_meta($booking['id'], array('start' => $booking['start'], 'end' => $booking['end'], 'reminded' => 0));
        riff_booking_retitle($booking);
        return true;
    });
    if (is_wp_error($result)) {
        return $result;
    }
    $updated = riff_booking_get($booking['id']);
    $previous = riff_booking_format_range($old[0], $old[1]);
    riff_booking_mail_customer($updated, 'rescheduled', $previous);
    riff_booking_mail_admin($updated, 'rescheduled', $previous);
    return $updated;
}

/** キャンセル（お客様のリンク／管理画面から） */
function riff_booking_cancel($booking) {
    $res = riff_booking_delete_event($booking);
    if (is_wp_error($res)) {
        return new WP_Error('riff_booking_event', 'キャンセル処理に失敗しました。お手数ですが、お問い合わせフォームからご連絡ください。');
    }
    riff_booking_save_meta($booking['id'], array('status' => 'cancelled'));
    $booking['status'] = 'cancelled';
    riff_booking_retitle($booking);
    riff_booking_mail_customer($booking, 'cancelled');
    riff_booking_mail_admin($booking, 'cancelled');
    return $booking;
}

/** 日時調整中のリクエストを、管理画面で決めた日時で確定する（受付時間外も可） */
function riff_booking_confirm_request($booking, $start) {
    $booking['start'] = $start;
    $booking['end']   = $start + RIFF_BOOKING_DURATION_MIN * 60;
    riff_booking_save_meta($booking['id'], array('start' => $booking['start'], 'end' => $booking['end']));
    $event = riff_booking_create_event($booking);
    if (is_wp_error($event)) {
        riff_booking_save_meta($booking['id'], array('start' => 0, 'end' => 0));
        return $event;
    }
    riff_booking_save_meta($booking['id'], array('status' => 'confirmed', 'reminded' => 0));
    $booking = riff_booking_get($booking['id']);
    riff_booking_retitle($booking);
    riff_booking_mail_customer($booking, 'confirmed');
    return $booking;
}

/**
 * Googleカレンダー側で予定が削除・移動されていたら予約記録に反映する。
 * カレンダーから直接消した予約にリマインドを送ったり、変更リンクを使えたりしないようにするため。
 * 通信エラー時は何も変えずに元の予約を返す。
 */
function riff_booking_sync_from_calendar($booking) {
    if (!$booking || $booking['status'] !== 'confirmed' || !$booking['event_id']) {
        return $booking;
    }
    $event = riff_booking_google_request('GET', '/calendars/primary/events/' . rawurlencode($booking['event_id']));
    $gone = is_wp_error($event)
        ? in_array($event->get_error_data()['status'] ?? 0, array(404, 410), true)
        : ($event['status'] ?? '') === 'cancelled';
    if (is_wp_error($event) && !$gone) {
        return $booking;
    }

    if ($gone) {
        riff_booking_save_meta($booking['id'], array('status' => 'cancelled'));
        riff_booking_log("予約 {$booking['id']} はGoogleカレンダーで削除されたためキャンセル扱いにしました");
    } else {
        $start = isset($event['start']['dateTime']) ? strtotime($event['start']['dateTime']) : 0;
        $end   = isset($event['end']['dateTime']) ? strtotime($event['end']['dateTime']) : 0;
        if (!$start || !$end || ($start === $booking['start'] && $end === $booking['end'])) {
            return $booking;
        }
        riff_booking_save_meta($booking['id'], array('start' => $start, 'end' => $end, 'reminded' => 0));
    }
    $booking = riff_booking_get($booking['id']);
    riff_booking_retitle($booking);
    return $booking;
}

function riff_booking_retitle($booking) {
    $who = ($booking['company'] ? $booking['company'] . ' ' : '') . $booking['name'];
    if ($booking['status'] === 'request') {
        $title = '【日時調整中】' . $who;
    } else {
        $title = ($booking['status'] === 'cancelled' ? '【キャンセル】' : '') . riff_booking_format_range($booking['start'], $booking['end']) . ' ' . $who;
    }
    wp_update_post(array('ID' => $booking['id'], 'post_title' => $title));
}

/* ------------------------------------------------------------------
 * メール
 * ------------------------------------------------------------------ */

function riff_booking_mail_headers() {
    $notify = riff_booking_settings()['notify_email'];
    $headers = array('Content-Type: text/plain; charset=UTF-8', 'Reply-To: RIFF System <' . $notify . '>');
    // サイトと同じドメインのアドレスなら送信元にする（他ドメインを名乗ると迷惑メール判定されやすい）
    $site_host = wp_parse_url(home_url(), PHP_URL_HOST);
    if (substr(strrchr($notify, '@'), 1) === preg_replace('/^www\./', '', $site_host)) {
        $headers[] = 'From: RIFF System <' . $notify . '>';
    }
    return $headers;
}

function riff_booking_detail_block($b) {
    $lines = array(
        '■ 日時　　　　：' . ($b['start'] ? riff_booking_format_range($b['start'], $b['end']) : '調整中'),
        '■ 面談方法　　：オンライン（Google Meet）',
    );
    if ($b['meet_url']) {
        $lines[] = '■ Meet URL　　：' . $b['meet_url'];
    }
    return implode("\n", array_merge($lines, array(
        '■ 会社名　　　：' . ($b['company'] ?: '—'),
        '■ 氏名　　　　：' . $b['name'],
        '■ メール　　　：' . $b['email'],
        '■ 電話番号　　：' . $b['phone'],
        '■ お問い合わせ種別：' . $b['inquiry_type'],
        '■ 現在のホームページURL：' . ($b['site_url'] ?: '—'),
        '■ ご予算　　　：' . ($b['budget'] ?: '—'),
        '■ ご希望の時期：' . ($b['timing'] ?: '—'),
        '■ 備考',
        $b['note'] ?: '—',
    )));
}

function riff_booking_google_calendar_link($b) {
    return add_query_arg(array(
        'action'  => 'TEMPLATE',
        'text'    => rawurlencode('RIFF System オンライン面談'),
        'dates'   => gmdate('Ymd\THis\Z', $b['start']) . '/' . gmdate('Ymd\THis\Z', $b['end']),
        'details' => rawurlencode('Google Meet：' . $b['meet_url']),
    ), 'https://calendar.google.com/calendar/render');
}

function riff_booking_mail_customer($b, $type, $previous = '') {
    $manage = riff_booking_page_url(array('rb' => $b['token']));
    $sign = "\n\n――――――――――――――――――\nRIFF System\n東京都板橋区前野町2-19-14-206\n" . home_url('/') . "\n※このメールにご返信いただくと担当者に届きます。";

    switch ($type) {
        case 'confirmed':
            $subject = '【RIFF System】オンライン面談のご予約が確定しました';
            $body = "{$b['name']} 様\n\nこの度はRIFF Systemにご予約いただき、ありがとうございます。\n以下の内容でオンライン面談のご予約が確定しました。\n\n"
                . riff_booking_detail_block($b)
                . "\n\n当日は上記のGoogle Meet URLからご参加ください。\n（パソコンはブラウザのみ、スマートフォンはGoogle Meetアプリでご参加いただけます）\n\n"
                . "▼ ご自身のGoogleカレンダーに追加する\n" . riff_booking_google_calendar_link($b)
                . "\n\n▼ 日程の変更・キャンセルはこちら\n{$manage}";
            break;
        case 'request':
            $subject = '【RIFF System】日程調整のご依頼を受け付けました';
            $body = "{$b['name']} 様\n\nこの度はRIFF Systemにお問い合わせいただき、ありがとうございます。\n以下の内容で日程調整のご依頼を受け付けました。\n\n"
                . riff_booking_detail_block($b)
                . "\n\nご希望の日時を確認のうえ、担当者より改めてご連絡いたします。\n日時が確定しましたら、Google MeetのURLを記載した確定メールをお送りします。";
            break;
        case 'rescheduled':
            $subject = '【RIFF System】オンライン面談の日程を変更しました';
            $body = "{$b['name']} 様\n\nオンライン面談の日程を変更しました。\n\n変更前：{$previous}\n変更後：" . riff_booking_format_range($b['start'], $b['end'])
                . "\n\n" . riff_booking_detail_block($b)
                . "\n\n▼ ご自身のGoogleカレンダーに追加する\n" . riff_booking_google_calendar_link($b)
                . "\n\n▼ 日程の変更・キャンセルはこちら\n{$manage}";
            break;
        case 'cancelled':
            $subject = '【RIFF System】オンライン面談のご予約をキャンセルしました';
            $body = "{$b['name']} 様\n\n以下のご予約をキャンセルしました。\n\n■ 日時：" . ($b['start'] ? riff_booking_format_range($b['start'], $b['end']) : '調整中')
                . "\n\nまたのご予約をお待ちしております。\n" . riff_booking_page_url();
            break;
        case 'reminder':
            $subject = '【RIFF System】明日のオンライン面談のご案内';
            $body = "{$b['name']} 様\n\n明日のオンライン面談のご案内です。\n\n"
                . riff_booking_detail_block($b)
                . "\n\nお時間になりましたら、上記のGoogle Meet URLからご参加ください。\n\n▼ 日程の変更・キャンセルはこちら\n{$manage}";
            break;
        default:
            return false;
    }
    return wp_mail($b['email'], $subject, $body . $sign, riff_booking_mail_headers());
}

function riff_booking_mail_admin($b, $type, $previous = '') {
    $labels = array(
        'confirmed'   => '新しい日程予約が入りました',
        'request'     => '日程調整のリクエストが届きました（要対応）',
        'rescheduled' => 'お客様が日程を変更しました',
        'cancelled'   => 'お客様が予約をキャンセルしました',
    );
    $subject = '[日程予約] ' . $labels[$type] . '：' . ($b['company'] ? $b['company'] . ' ' : '') . $b['name'] . '様';
    $body = $labels[$type] . "\n\n";
    if ($type === 'request') {
        $body .= "カレンダーの空き枠以外の日時を希望されています。備考の希望日時を確認し、管理画面で日時を確定してください。\n日時を確定すると、Meet付きの予定作成と確定メールの送信が自動で行われます。\n\n";
    }
    if ($previous) {
        $body .= "変更前：{$previous}\n\n";
    }
    $body .= riff_booking_detail_block($b) . "\n\n管理画面：" . admin_url('post.php?post=' . $b['id'] . '&action=edit');
    $headers = array('Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $b['name'] . ' <' . $b['email'] . '>');
    return wp_mail(riff_booking_settings()['notify_email'], $subject, $body, $headers);
}

/* ------------------------------------------------------------------
 * 前日リマインド（1時間ごとに、開始24時間前を過ぎた予約へ送信）
 * ------------------------------------------------------------------ */

add_action('init', function () {
    if (!wp_next_scheduled('riff_booking_reminder_cron')) {
        wp_schedule_event(time() + 300, 'hourly', 'riff_booking_reminder_cron');
    }
});

add_action('riff_booking_reminder_cron', 'riff_booking_send_reminders');

function riff_booking_send_reminders() {
    $now = time();
    $ids = get_posts(array(
        'post_type'      => RIFF_BOOKING_POST_TYPE,
        'post_status'    => 'publish',
        'posts_per_page' => 50,
        'fields'         => 'ids',
        'meta_query'     => array(
            array('key' => '_rb_status', 'value' => 'confirmed'),
            array('key' => '_rb_reminded', 'value' => '1', 'compare' => '!='),
            array('key' => '_rb_start', 'value' => array($now, $now + DAY_IN_SECONDS), 'compare' => 'BETWEEN', 'type' => 'NUMERIC'),
        ),
    ));
    foreach ($ids as $id) {
        $b = riff_booking_sync_from_calendar(riff_booking_get($id));
        // カレンダーで削除された、または24時間より先へ移動された予約は今回は送らない
        if ($b['status'] !== 'confirmed' || $b['start'] > $now + DAY_IN_SECONDS || $b['start'] < $now) {
            continue;
        }
        // 予約してから24時間以内の面談は確定メールで足りるため、リマインドは送らない
        $booked_at = get_post_time('U', true, $id);
        if ($b['start'] - $booked_at > DAY_IN_SECONDS + HOUR_IN_SECONDS) {
            riff_booking_mail_customer($b, 'reminder');
        }
        riff_booking_save_meta($id, array('reminded' => 1));
    }
}
