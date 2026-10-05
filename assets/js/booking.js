/**
 * 日程予約ページ（inc/booking/booking-front.php）の画面制御
 * - 予約モード：日時選択 → お客様情報 → 完了
 * - 管理モード：メール内リンクからの日程変更・キャンセル
 */
(function () {
    'use strict';

    var config = window.RIFF_BOOKING || {};
    var root = document.querySelector('.riff-booking[data-rb-mode]');
    if (!root || !config.api) return;

    var mode = root.getAttribute('data-rb-mode');
    var WEEK = ['日', '月', '火', '水', '木', '金', '土'];

    function $(sel, scope) { return (scope || root).querySelector(sel); }
    function $all(sel, scope) { return Array.prototype.slice.call((scope || root).querySelectorAll(sel)); }

    function api(path, options) {
        options = options || {};
        var init = { method: options.method || 'GET', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin' };
        if (options.body) init.body = JSON.stringify(options.body);
        return fetch(config.api + path, init).then(function (res) {
            return res.json().catch(function () { return {}; }).then(function (data) {
                if (!res.ok || !data.ok) {
                    var err = new Error(data.message || '通信エラーが発生しました。時間をおいて再度お試しください。');
                    err.status = res.status;
                    throw err;
                }
                return data;
            });
        });
    }

    function showError(message) {
        var box = $('[data-rb-error]');
        if (!box) return;
        box.textContent = message || '';
        box.hidden = !message;
        if (message) box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function formatDate(dateStr) {
        var p = dateStr.split('-');
        var d = new Date(+p[0], +p[1] - 1, +p[2]);
        return (+p[1]) + '月' + (+p[2]) + '日（' + WEEK[d.getDay()] + '）';
    }

    function formatRange(dateStr, time) {
        var p = dateStr.split('-');
        var hm = time.split(':');
        var end = new Date(2000, 0, 1, +hm[0], +hm[1] + (config.durationMin || 60));
        var pad = function (n) { return (n < 10 ? '0' : '') + n; };
        return p[0] + '年' + formatDate(dateStr) + time + '〜' + pad(end.getHours()) + ':' + pad(end.getMinutes());
    }

    /* ------------------------------------------------------------
     * 日付カレンダー＋時間枠の選択パネル
     * ------------------------------------------------------------ */
    function Picker(el, onSelect) {
        this.el = el;
        this.onSelect = onSelect;
        this.days = {};
        this.selectedDate = null;
        this.month = null;
        var self = this;
        $('[data-rb-prev]', el).addEventListener('click', function () { self.shiftMonth(-1); });
        $('[data-rb-next]', el).addEventListener('click', function () { self.shiftMonth(1); });
    }

    Picker.prototype.load = function () {
        var self = this;
        var loading = $('[data-rb-loading]', this.el);
        loading.hidden = false;
        this.el.classList.add('is-loading');
        var query = config.token ? 'slots?token=' + encodeURIComponent(config.token) : 'slots';
        return api(query).then(function (data) {
            self.days = data.days || {};
            var keys = Object.keys(self.days);
            var firstOpen = keys.filter(function (k) { return self.days[k].length; })[0] || keys[0];
            if (!self.month && firstOpen) {
                var p = firstOpen.split('-');
                self.month = new Date(+p[0], +p[1] - 1, 1);
            }
            self.render();
            if (firstOpen && self.days[firstOpen].length && !self.selectedDate) self.selectDate(firstOpen);
            else if (self.selectedDate) self.selectDate(self.selectedDate);
        }).catch(function (err) {
            showError(err.message);
        }).then(function () {
            loading.hidden = true;
            self.el.classList.remove('is-loading');
        });
    };

    Picker.prototype.shiftMonth = function (delta) {
        this.month = new Date(this.month.getFullYear(), this.month.getMonth() + delta, 1);
        this.render();
    };

    Picker.prototype.monthKey = function (date) {
        return date.getFullYear() * 12 + date.getMonth();
    };

    Picker.prototype.render = function () {
        if (!this.month) return;
        var self = this;
        var keys = Object.keys(this.days);
        var y = this.month.getFullYear();
        var m = this.month.getMonth();
        $('[data-rb-month]', this.el).textContent = y + '年' + (m + 1) + '月';

        var first = keys[0].split('-');
        var last = keys[keys.length - 1].split('-');
        var cur = this.monthKey(this.month);
        $('[data-rb-prev]', this.el).disabled = cur <= this.monthKey(new Date(+first[0], +first[1] - 1, 1));
        $('[data-rb-next]', this.el).disabled = cur >= this.monthKey(new Date(+last[0], +last[1] - 1, 1));

        var grid = $('[data-rb-days]', this.el);
        grid.innerHTML = '';
        var offset = new Date(y, m, 1).getDay();
        for (var i = 0; i < offset; i++) {
            grid.appendChild(document.createElement('span'));
        }
        var count = new Date(y, m + 1, 0).getDate();
        for (var d = 1; d <= count; d++) {
            var key = y + '-' + String(m + 1).padStart(2, '0') + '-' + String(d).padStart(2, '0');
            var slots = this.days[key];
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = d;
            btn.className = 'rb-day';
            var dow = (offset + d - 1) % 7;
            if (dow === 0) btn.classList.add('is-sun');
            if (dow === 6) btn.classList.add('is-sat');
            if (slots && slots.length) {
                btn.classList.add('is-open');
                btn.setAttribute('aria-label', formatDate(key) + ' 予約可能');
                btn.addEventListener('click', (function (k) { return function () { self.selectDate(k); }; })(key));
            } else {
                btn.disabled = true;
            }
            if (key === this.selectedDate) btn.classList.add('is-selected');
            grid.appendChild(btn);
        }
    };

    Picker.prototype.selectDate = function (key) {
        var self = this;
        this.selectedDate = key;
        var p = key.split('-');
        this.month = new Date(+p[0], +p[1] - 1, 1);
        this.render();

        var slots = this.days[key] || [];
        $('[data-rb-times-title]', this.el).textContent = formatDate(key) + (slots.length ? 'の空き時間' : 'は空きがありません');
        var list = $('[data-rb-times]', this.el);
        list.innerHTML = '';
        slots.forEach(function (time) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'rb-time';
            btn.textContent = time;
            btn.addEventListener('click', function () {
                $all('.rb-time', list).forEach(function (b) { b.classList.remove('is-selected'); });
                btn.classList.add('is-selected');
                self.onSelect(key, time);
            });
            list.appendChild(btn);
        });
    };

    /* ------------------------------------------------------------
     * 予約モード
     * ------------------------------------------------------------ */
    function initBook() {
        var state = { date: null, time: null, request: false, turnstileId: null };
        var form = $('[data-rb-form]');
        var note = $('[data-rb-note]');

        function goStep(n) {
            $all('[data-rb-step]').forEach(function (s) { s.hidden = s.getAttribute('data-rb-step') !== String(n); });
            $all('[data-rb-step-label]').forEach(function (li) {
                var k = +li.getAttribute('data-rb-step-label');
                li.classList.toggle('is-active', k === n);
                li.classList.toggle('is-done', k < n);
            });
            showError('');
            root.scrollIntoView({ behavior: 'smooth', block: 'start' });
            if (n === 2) renderTurnstile();
        }

        function setRequestMode(on) {
            state.request = on;
            $('[data-rb-selected-label]').textContent = on ? 'ご予約方法' : 'ご予約日時';
            $('[data-rb-selected]').textContent = on ? '日時を調整してご連絡します（備考にご希望の日時をご記入ください）' : formatRange(state.date, state.time);
            $('[data-rb-note-required]').hidden = !on;
            note.required = on;
            note.placeholder = note.getAttribute(on ? 'data-placeholder-request' : 'data-placeholder-book');
            $('[data-rb-submit]').textContent = on ? 'この内容で日程調整を依頼する' : 'この内容で予約する';
        }

        function renderTurnstile() {
            var holder = $('[data-rb-turnstile]');
            if (!config.sitekey || state.turnstileId !== null) return;
            var tryRender = function () {
                if (window.turnstile) {
                    state.turnstileId = window.turnstile.render(holder, { sitekey: config.sitekey });
                } else {
                    setTimeout(tryRender, 300);
                }
            };
            tryRender();
        }

        var picker = new Picker($('[data-rb-picker]'), function (date, time) {
            state.date = date;
            state.time = time;
            setRequestMode(false);
            goStep(2);
        });
        picker.load();

        $('[data-rb-request]').addEventListener('click', function () {
            setRequestMode(true);
            goStep(2);
            note.focus();
        });
        $('[data-rb-back]').addEventListener('click', function () { goStep(1); });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!form.reportValidity()) return;

            var data = {};
            new FormData(form).forEach(function (v, k) { data[k] = v; });
            data.privacy = form.privacy.checked;
            data.request_mode = state.request;
            if (!state.request) {
                data.date = state.date;
                data.time = state.time;
            }
            if (config.sitekey && window.turnstile && state.turnstileId !== null) {
                data.turnstile = window.turnstile.getResponse(state.turnstileId) || '';
                if (!data.turnstile) {
                    showError('スパム対策の確認が完了するまでお待ちください。');
                    return;
                }
            }

            var submit = $('[data-rb-submit]');
            submit.disabled = true;
            submit.classList.add('is-busy');
            showError('');

            api('book', { method: 'POST', body: data }).then(function (res) {
                if (res.status === 'request') {
                    $('[data-rb-done-title]').textContent = '日程調整のご依頼を受け付けました';
                    $('[data-rb-done-datetime]').textContent = '';
                    $('[data-rb-done-text]').innerHTML = 'ご希望の日時を確認のうえ、担当者よりご連絡いたします。<br>受付確認メールをお送りしましたので、ご確認ください。';
                } else {
                    $('[data-rb-done-datetime]').textContent = res.datetime;
                }
                goStep(3);
                if (typeof gtag === 'function') {
                    gtag('event', 'booking_complete', {
                        booking_type: res.status === 'request' ? 'request' : 'slot',
                        inquiry_type: data.inquiry_type
                    });
                }
            }).catch(function (err) {
                showError(err.message);
                if (err.status === 409) {
                    // 直前に埋まった枠：最新の空き状況で選び直してもらう
                    goStep(1);
                    showError(err.message);
                    picker.load();
                }
                if (window.turnstile && state.turnstileId !== null) window.turnstile.reset(state.turnstileId);
            }).then(function () {
                submit.disabled = false;
                submit.classList.remove('is-busy');
            });
        });
    }

    /* ------------------------------------------------------------
     * 管理モード（日程変更・キャンセル）
     * ------------------------------------------------------------ */
    function initManage() {
        var summary = $('[data-rb-summary]');
        if (!summary || !config.token) return;
        var reschedulePanel = $('[data-rb-reschedule]');
        var cancelPanel = $('[data-rb-cancel]');
        var done = $('[data-rb-manage-done]');
        var choice = null;
        var picker = null;

        function show(panel) {
            summary.hidden = panel !== null;
            reschedulePanel.hidden = panel !== reschedulePanel;
            cancelPanel.hidden = panel !== cancelPanel;
            showError('');
        }

        function finish(title, text) {
            summary.hidden = reschedulePanel.hidden = cancelPanel.hidden = true;
            $('[data-rb-done-title]', done).textContent = title;
            $('[data-rb-done-text]', done).innerHTML = text;
            done.hidden = false;
            root.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        $('[data-rb-open-reschedule]').addEventListener('click', function () {
            show(reschedulePanel);
            if (!picker) {
                picker = new Picker($('[data-rb-picker]', reschedulePanel), function (date, time) {
                    choice = { date: date, time: time };
                    $('[data-rb-selected]', reschedulePanel).textContent = formatRange(date, time);
                    $('[data-rb-reschedule-confirm]').hidden = false;
                });
            }
            picker.load();
        });
        $('[data-rb-open-cancel]').addEventListener('click', function () { show(cancelPanel); });
        $all('[data-rb-close]').forEach(function (b) { b.addEventListener('click', function () { show(null); }); });

        $('[data-rb-do-reschedule]').addEventListener('click', function () {
            if (!choice) return;
            var btn = this;
            btn.disabled = true;
            api('reschedule', { method: 'POST', body: { token: config.token, date: choice.date, time: choice.time } }).then(function (res) {
                finish('日程を変更しました', '新しい日時：<strong>' + res.datetime + '</strong><br>変更内容を記載したメールをお送りしました。Google MeetのURLは変わりません。');
            }).catch(function (err) {
                showError(err.message);
                if (err.status === 409) picker.load();
            }).then(function () { btn.disabled = false; });
        });

        $('[data-rb-do-cancel]').addEventListener('click', function () {
            var btn = this;
            btn.disabled = true;
            api('cancel', { method: 'POST', body: { token: config.token } }).then(function () {
                finish('ご予約をキャンセルしました', 'キャンセル確認メールをお送りしました。<br>またのご予約をお待ちしております。');
            }).catch(function (err) {
                showError(err.message);
            }).then(function () { btn.disabled = false; });
        });
    }

    if (mode === 'book') initBook();
    if (mode === 'manage') initManage();
})();
