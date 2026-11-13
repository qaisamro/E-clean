/* ==========================================================================
   E-Clean — Bluetooth panel UI (connection state + actions), shared HTML/CSS.
   Depends on eclean-bt-core.js. Mount onto a container element:

     const conn = EcleanBT.connection.create();           // one per page
     EcleanBTUI.mount(document.getElementById('bt'), {
         role: 'invoice',                                  // or 'label'
         connection: conn,
         onTestPrint: function (conn) { ... },             // optional
     });
   ========================================================================== */
(function (root, factory) {
    'use strict';
    const api = factory(typeof root.EcleanBT !== 'undefined'
        ? root.EcleanBT
        : (typeof require === 'function' ? require('./eclean-bt-core.js') : undefined));
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = api;
    }
    root.EcleanBTUI = api;
})(typeof window !== 'undefined' ? window : globalThis, function (EcleanBT) {
    'use strict';

    const STYLE = ''
        + '.ecleanbt-panel{display:flex;flex-direction:column;gap:.45rem;padding:.55rem .7rem;border:1px dashed var(--ec-line, #d4d4d4);border-radius:.7rem;background:#fbfefd;font-size:.82rem;}'
        + '.ecleanbt-row{display:flex;align-items:center;gap:.45rem;flex-wrap:wrap;}'
        + '.ecleanbt-label{font-weight:700;color:var(--ec-body, #404040);white-space:nowrap;}'
        + '.ecleanbt-select{max-width:250px;padding:.3rem .5rem;border:1px solid var(--ec-line, #d4d4d4);border-radius:.5rem;font-family:inherit;font-size:.8rem;background:#fff;}'
        + '.ecleanbt-status{min-width:150px;color:var(--ec-muted, #6b7280);font-size:.78rem;}'
        + '.ecleanbt-status[data-state="connected"]{color:#15803d;font-weight:700;}'
        + '.ecleanbt-status[data-state="error"]{color:#b91c1c;font-weight:700;}'
        + '.ecleanbt-status[data-state="printing"]{color:#b45309;font-weight:700;}'
        + '.ecleanbt-log{max-height:110px;overflow-y:auto;background:#0f172a08;border-radius:.5rem;padding:.35rem .5rem;font-size:.68rem;white-space:pre-wrap;color:#334155;display:none;}'
        + '.ecleanbt-log.visible{display:block;}';

    function injectStyle() {
        if (typeof document === 'undefined') return;
        if (document.getElementById('ecleanbt-style')) return;
        const style = document.createElement('style');
        style.id = 'ecleanbt-style';
        style.textContent = STYLE;
        document.head.appendChild(style);
    }

    function mount(container, opts) {
        if (typeof document === 'undefined' || !container) return null;
        injectStyle();

        const o = opts || {};
        const role = o.role || 'invoice';
        const conn = o.connection || EcleanBT.connection.create({ onStatus: o.onStatus });
        conn.state.role = role;

        const pref = (conn.loadPref ? conn.loadPref(role) : null) || {};
        const profileId = pref.profile || (role === 'invoice' ? 'rpp320-tspl' : 'generic-escpos');
        const deviceName = typeof pref.deviceName === 'string' && pref.deviceName ? pref.deviceName : null;

        container.innerHTML = ''
            + '<div class="ecleanbt-panel" data-ecleanbt-role="' + role + '">'
            + '  <div class="ecleanbt-row">'
            + '    <span class="ecleanbt-label">' + (role === 'invoice' ? 'طابعة الفواتير' : 'طابعة الملصقات') + '</span>'
            + '    <select class="ecleanbt-select" data-field="profile">'
            +           EcleanBT.profiles.list().map(function (p) {
                          const sel = p.id === profileId ? ' selected' : '';
                          return '<option value="' + p.id + '"' + sel + '>' + p.label + '</option>';
                      }).join('')
            + '    </select>'
            + '  </div>'
            + '  <div class="ecleanbt-row">'
            + '    <button type="button" class="btn btn-sm btn-outline-primary" data-action="connect"><i class="bx bx-bluetooth"></i> اتصال</button>'
            + '    <button type="button" class="btn btn-sm btn-outline-secondary" data-action="test">طباعة تجريبية</button>'
            + '    <button type="button" class="btn btn-sm btn-outline-secondary" data-action="diag">تشخيص</button>'
            + '    <button type="button" class="btn btn-sm btn-link text-muted" data-action="forget">نسيان الطابعة</button>'
            + '    <span class="ecleanbt-status" data-state="disconnected">غير متصل' + (deviceName ? ' — آخر: ' + deviceName : '') + '</span>'
            + '  </div>'
            + '  <div class="ecleanbt-log" role="log"></div>'
            + '</div>';

        const statusEl = container.querySelector('.ecleanbt-status');
        const logEl = container.querySelector('.ecleanbt-log');

        function log(msg) {
            const safe = (typeof msg === 'string') ? msg
                : (msg && msg.message ? String(msg.message) : JSON.stringify(msg));
            const line = '[' + new Date().toLocaleTimeString('ar') + '] ' + safe;
            logEl.textContent = (logEl.textContent ? logEl.textContent + '\n' : '') + line;
            logEl.classList.add('visible');
            logEl.scrollTop = logEl.scrollHeight;
        }
        function setStatus(state, text) {
            statusEl.dataset.state = state;
            statusEl.textContent = (typeof text === 'string') ? text
                : (text && text.message ? String(text.message) : String(text));
        }

        conn.on('status', function (payload) {
            const s = payload.status;
            if (s === 'connected') {
                setStatus('connected', 'متصل: ' + (payload.device || 'الطابعة'));
                if (payload.reconnected) log('تمت استعادة الاتصال');
            } else if (s === 'connecting') {
                setStatus('printing', 'جارٍ الاتصال...');
            } else if (s === 'disconnected') {
                setStatus('disconnected', 'غير متصل');
            } else if (s === 'error') {
                const msg = payload.error ? EcleanBT.errors.formatArabicError(payload.error) : 'خطأ';
                setStatus('error', msg);
                log('خطأ: ' + msg);
            } else if (s === 'printing') {
                setStatus('printing', 'جارٍ الإرسال...');
            }
        });

        container.querySelector('[data-field="profile"]').addEventListener('change', function (e) {
            const id = e.target.value;
            const prefNow = (conn.loadPref ? conn.loadPref(role) : null) || {};
            prefNow.profile = id;
            conn.savePref(role, prefNow);
            log('تم حفظ ملف الطابعة: ' + id);
        });

        container.querySelector('[data-action="connect"]').addEventListener('click', function () {
            conn.connect(role).catch(function (err) {
                const msg = EcleanBT.errors.formatArabicError(err);
                setStatus('error', msg);
                log('فشل الاتصال: ' + msg);
            });
        });
        container.querySelector('[data-action="forget"]').addEventListener('click', function () {
            conn.forget();
            setStatus('disconnected', 'غير متصل — نسيت الطابعة');
            log('تم نسيان الطابعة');
        });
        container.querySelector('[data-action="test"]').addEventListener('click', function () {
            if (typeof o.onTestPrint === 'function') {
                o.onTestPrint(conn);
            }
        });
        container.querySelector('[data-action="diag"]').addEventListener('click', function () {
            if (typeof o.onDiagnostic === 'function') {
                o.onDiagnostic(conn, log);
            }
        });

        return {
            connection: conn,
            log: log,
            setStatus: setStatus,
            profileSelect: container.querySelector('[data-field="profile"]'),
        };
    }

    return { mount: mount, STYLE: STYLE };
});
