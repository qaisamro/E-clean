/* ==========================================================================
   E-Clean — Unified Bluetooth/BLE print engine
   Used by both Laravel apps (ERP + Web_Admin). Vanilla JS, no dependencies.
   UMD export so it can be unit tested in Node as well as run in the browser.

   Layers:
     ENV        — environment detection (browser vs test)
     Errors     — Arabic error mapping
     Services   — known BLE GATT candidates (verified heuristics, not guesses)
     Profiles   — printer profiles (RPP320+TSPL, generic ESC/POS)
     Raster     — pixel buffer → mono → protocol-specific bitmap commands
     Connection — discover/connect/write/reconnect/forget
     Jobs       — serial job queue with abort + double-click protection
     Stickers   — pure helpers (quantity expansion, item labelling)
   ========================================================================== */
(function (root, factory) {
    'use strict';
    const api = factory();
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = api;
    }
    root.EcleanBT = api;
})(typeof window !== 'undefined' ? window : globalThis, function () {
    'use strict';

    const VERSION = '1.0.0';

    /* ================= ENV helpers ================= */
    function isBrowser() {
        return typeof navigator !== 'undefined' && typeof document !== 'undefined';
    }
    function isAndroid() {
        return isBrowser() && /Android/i.test(navigator.userAgent || '');
    }
    function isIos() {
        const ua = isBrowser() ? (navigator.userAgent || '') : '';
        return /iPad|iPhone|iPod/i.test(ua) || ((navigator.platform === 'MacIntel') && navigator.maxTouchPoints > 1);
    }
    function webusbSupported() {
        return isBrowser() && !!navigator.usb;
    }
    function sleep(ms) {
        return new Promise(function (resolve) { setTimeout(resolve, ms); });
    }

    /* ================= Errors (Arabic) ================= */
    function stringifyErrValue(value) {
        if (value === null || value === undefined) return '';
        const t = typeof value;
        if (t === 'string') return value;
        if (t === 'number' || t === 'boolean') return String(value);
        if (value instanceof Error) return value.message || value.name || String(value);
        if (typeof value === 'object') {
            if ('message' in value && value.message) {
                return String(value.message);
            }
            const keys = Object.keys(value);
            if (keys.length === 1 && keys[0] === 'name') {
                return ''; // name-only shell — let formatArabicError decide by name
            }
            try {
                const json = JSON.stringify(value);
                if (json && json !== '{}') return json;
            } catch (e) { /* fall through */ }
            return '';
        }
        return String(value);
    }

    function formatArabicError(err) {
        if (!err) return 'حدث خطأ غير معروف';
        const name = (err && err.name) ? String(err.name) : '';
        let msg = stringifyErrValue(err);
        if (!msg) {
            if (name === 'NotFoundError') return 'تم إلغاء اختيار الطابعة.';
            if (name === 'NotAllowedError' || name === 'SecurityError') {
                return 'انتهى الإذن أو رفضه المتصفح — اضغط الزر مباشرة وحاول مجدداً.';
            }
            return 'حدث خطأ غير متوقع من الطابعة أو المتصفح';
        }

        if (err === 'NO_BLUETOOTH' || (/Bluetooth/i.test(msg) && !(typeof navigator !== 'undefined' && navigator.bluetooth))) {
            if (isIos()) {
                return 'المتصفح الحالي لا يدعم Web Bluetooth على iOS — افتح هذه الصفحة في تطبيق متصفح Bluefy المجاني من App Store.';
            }
            return 'المتصفح لا يدعم Web Bluetooth — استخدم Chrome أو Edge على أندرويد عبر اتصال HTTPS آمن.';
        }
        if (name === 'NotFoundError' || /cancelled|cansell?ed/i.test(msg)) {
            return 'تم إلغاء اختيار الطابعة.';
        }
        if (name === 'SecurityError' || /HTTPS|localhost/i.test(msg)) {
            return 'يتطلب البلوتوث اتصالاً آمناً عبر HTTPS.';
        }
        if (name === 'NotAllowedError' || /permission|gesture|user activation/i.test(msg)) {
            return 'المتصفح رفض إذن الوصول للبلوتوث — اضغط زر الطباعة مباشرة من الصفحة وحاول مجدداً.';
        }
        if (name === 'NetworkError' || name === 'NotConnectedError' || /GATT/i.test(msg)) {
            return 'تعذر الاتصال بالطابعة — تأكد من تشغيلها وقربها وأنها غير متصلة بجهاز آخر.';
        }
        if (err === 'CHANNEL_FAIL' || /CHANNEL_FAIL/i.test(msg)) {
            return 'تم الاتصال بالجهاز لكن لم تُعثر على قناة كتابة متوافقة (بعض الطابعات تتطلب بروتوكولاً آخر).';
        }
        if (err === 'PRINTER_BUSY' || /busy|isBusy/i.test(msg)) {
            return 'جارٍ إرسال مهمة طباعة حالياً — انتظر حتى تنتهي.';
        }
        if (err === 'JOB_ABORTED') {
            return 'تم إلغاء مهمة الطباعة.';
        }
        return msg;
    }

    /* ================= GATT service/characteristic candidates ================= */
    /* Profiles reference these; not every device exposes them — they are
       *heuristics*, verified at runtime by probing a writable characteristic. */
    const KNOWN_SERVICES = [
        { name: 'Nordic UART (most common BLE label/thermal printers)', service: '6e400001-b5a3-f393-e0a9-e50e24dcca9e', write: ['6e400002-b5a3-f393-e0a9-e50e24dcca9e'] },
        { name: '0xFF00 (Ten-Jiang / common CN label printers)', service: 0xFF00, write: [0xFF02, 0xFF01, '0000ff02-0000-1000-8000-00805f9b34fb', '0000ff01-0000-1000-8000-00805f9b34fb'] },
        { name: '0xFF00 (128-bit UUID)', service: '0000ff00-0000-1000-8000-00805f9b34fb', write: ['0000ff02-0000-1000-8000-00805f9b34fb', '0000ff01-0000-1000-8000-00805f9b34fb', 0xFF02, 0xFF01] },
        { name: '0xFFE0 (ISSC/HM-10 variants)', service: 0xFFE0, write: [0xFFE1, '0000ffe1-0000-1000-8000-00805f9b34fb'] },
        { name: '0xFFE0 (128-bit UUID)', service: '0000ffe0-0000-1000-8000-00805f9b34fb', write: ['0000ffe1-0000-1000-8000-00805f9b34fb', 0xFFE1] },
        { name: '0xFFF0 (Rongta-style candidates)', service: 0xFFF0, write: [0xFFF1, 0xFFF2, 0xFFF3, '0000fff1-0000-1000-8000-00805f9b34fb', '0000fff2-0000-1000-8000-00805f9b34fb', '0000fff3-0000-1000-8000-00805f9b34fb'] },
        { name: '0xFFF0 (128-bit UUID)', service: '0000fff0-0000-1000-8000-00805f9b34fb', write: ['0000fff1-0000-1000-8000-00805f9b34fb', '0000fff2-0000-1000-8000-00805f9b34fb', '0000fff3-0000-1000-8000-00805f9b34fb', 0xFFF1, 0xFFF2, 0xFFF3] },
        { name: 'ISSC Transparent UART', service: '49535343-fe7d-4ae5-8fa9-9fafd205e455', write: ['49535343-8841-43f4-a8d4-ecbe34729bb3'] },
        { name: '0x18F0 (POS-STAR / common Chinese POS)', service: 0x18F0, write: [0x2AF0, 0x2AF1, '00002af0-0000-1000-8000-00805f9b34fb', '00002af1-0000-1000-8000-00805f9b34fb'] },
        { name: '0x18F0 (128-bit UUID)', service: '000018f0-0000-1000-8000-00805f9b34fb', write: ['00002af0-0000-1000-8000-00805f9b34fb', '00002af1-0000-1000-8000-00805f9b34fb'] },
        { name: '0xAE30', service: 0xAE30, write: [0xAE01, '0000ae01-0000-1000-8000-00805f9b34fb'] },
        { name: '0xAF30', service: 0xAF30, write: [0xAF01, '0000af01-0000-1000-8000-00805f9b34fb'] },
        { name: 'FEE7-FEC7 (CC2540 transparent)', service: 0xFEE7, write: [0xFEC7, 0xFEC8, 0xFED4, 0xFED5, 0xFED6, 0xFED7, 0xFED8] },
    ];

    const OPTIONAL_SERVICES = [
        '6e400001-b5a3-f393-e0a9-e50e24dcca9e',
        '0000ff00-0000-1000-8000-00805f9b34fb',
        '0000ffe0-0000-1000-8000-00805f9b34fb',
        '0000fff0-0000-1000-8000-00805f9b34fb',
        '49535343-fe7d-4ae5-8fa9-9fafd205e455',
        '000018f0-0000-1000-8000-00805f9b34fb',
        '0000ae30-0000-1000-8000-00805f9b34fb',
        '0000af30-0000-1000-8000-00805f9b34fb',
        '0000fee7-0000-1000-8000-00805f9b34fb',
        '0000ffe5-0000-1000-8000-00805f9b34fb',
        'e7810a71-73ae-499d-8c15-faa9aef0c3f',
        '0000180a-0000-1000-8000-00805f9b34fb',
    ];

    /* ================= Printer profiles ================= */
    const PROFILES = {
        'rpp320-tspl': {
            id: 'rpp320-tspl',
            label: 'Rongta RPP320 (ورق حراري 80مم — TSPL)',
            protocol: 'TSPL',
            dpi: 203,
            paper: { widthMm: 80, printableWidthPx: 576 /* 72mm printable @203dpi */ },
            label: { widthMm: 40, heightMm: 30, gapMm: 2 },
            continuousPaper: true,
            probe: null,           // TSPL has no reliable non-destructive status query here;
            cutCommand: null,      // RPP320 has no cutter — never send CUT
            chunkSize: 128,
        },
        'generic-escpos': {
            id: 'generic-escpos',
            label: 'ESC/POS عام (طابعات 80مم)',
            protocol: 'ESC/POS',
            dpi: 203,
            paper: { widthMm: 80, printableWidthPx: 576 },
            label: { widthMm: 40, heightMm: 30, gapMm: 2 },
            continuousPaper: true,
            probe: new Uint8Array([0x10, 0x04, 0x01]), // DLE EOT 1 (status)
            cutCommand: null,                         // never cut
            chunkSize: 128,
        },
    };
    function getProfile(id) {
        return PROFILES[id] || PROFILES['rpp320-tspl'];
    }

    /* ================= Byte helpers ================= */
    function concatArrays() {
        let total = 0;
        for (let i = 0; i < arguments.length; i++) {
            const a = arguments[i];
            if (a) total += a.length;
        }
        const out = new Uint8Array(total);
        let offset = 0;
        for (let i = 0; i < arguments.length; i++) {
            const a = arguments[i];
            if (!a) continue;
            out.set(a, offset);
            offset += a.length;
        }
        return out;
    }
    function ascii(str) {
        const out = new Uint8Array(str.length);
        for (let i = 0; i < str.length; i++) {
            out[i] = str.charCodeAt(i) & 0xff;
        }
        return out;
    }

    /* ================= Raster (mono bitmap) ================= */
    /* data: Uint8ClampedArray RGBA, threshold: 0..255 */
    function rgbaToMono(data, width, height, threshold) {
        const t = threshold === undefined ? 128 : threshold;
        const row = Math.ceil(width / 8);
        const mono = new Uint8Array(row * height);
        for (let y = 0; y < height; y++) {
            for (let x = 0; x < width; x++) {
                const i = (y * width + x) * 4;
                const alpha = data[i + 3];
                const lum = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
                if (alpha > 128 && lum < t) {
                    mono[y * row + (x >> 3)] |= 0x80 >> (x & 7);
                }
            }
        }
        return mono;
    }
    function canvasToMono(canvas) {
        const ctx = canvas.getContext('2d');
        const img = ctx.getImageData(0, 0, canvas.width, canvas.height);
        return rgbaToMono(img.data, canvas.width, canvas.height, 128);
    }

    /* ESC/POS raster: GS v 0 with 128-dot vertical bands */
    const ESC_POS_BAND = 128;
    function escPosRaster(mono, widthPx) {
        const GS = 0x1D;
        const row = Math.ceil(widthPx / 8);
        const h = Math.floor(mono.length / row);
        const parts = [];
        for (let y0 = 0; y0 < h; y0 += ESC_POS_BAND) {
            const bh = Math.min(ESC_POS_BAND, h - y0);
            const blk = new Uint8Array(8 + row * bh);
            blk.set([GS, 0x76, 0x30, 0x00, row & 0xff, (row >> 8) & 0xff, bh & 0xff, (bh >> 8) & 0xff], 0);
            blk.set(mono.subarray(y0 * row, (y0 + bh) * row), 8);
            parts.push(blk);
        }
        return concatArrays.apply(null, parts);
    }

    /* TSPL bitmap: BITMAP x,y,widthBytes,height,mode,data */
    function tsplBitmap(mono, widthPx, heightPx) {
        const widthBytes = Math.ceil(widthPx / 8);
        if (mono.length < widthBytes * heightPx) {
            throw new Error('BITMAP البيانات أصغر من المتوقع');
        }
        const header = ascii('BITMAP 0,0,' + widthBytes + ',' + heightPx + ',0,');
        return concatArrays(header, mono.subarray(0, widthBytes * heightPx), ascii('\r\n'));
    }

    function tsplJob(mono, widthPx, heightPx, opts) {
        const o = opts || {};
        const dpi = o.dpi || 203;
        const copies = Math.max(1, o.copies | 0 || 1);
        const heightMm = Math.round(((heightPx / dpi) * 25.4) * 10) / 10; // 0.1mm steps
        // SIZE refers to the physical roll width, not the raster width —
        // for continuous paper this is the printable window.
        const widthMm = o.widthMm !== undefined ? o.widthMm : Math.ceil(((widthPx / dpi) * 25.4) * 2) / 2;
        const density = Math.min(15, Math.max(0, o.density === undefined ? 8 : o.density));
        const parts = [
            ascii('SIZE ' + widthMm.toFixed(1) + ' mm,' + heightMm.toFixed(1) + ' mm\r\n'),
            ascii('GAP 0 mm,0 mm\r\n'),
            ascii('DENSITY ' + density + '\r\n'),
            ascii('DIRECTION 0\r\n'),
            ascii('CLS\r\n'),
            tsplBitmap(mono, widthPx, heightPx),
            ascii('PRINT ' + copies + ',1\r\n'),
        ];
        return concatArrays.apply(null, parts);
    }

    function escPosJob(mono, widthPx, heightPx, opts) {
        const ESC = 0x1B;
        const o = opts || {};
        const copies = Math.max(1, o.copies | 0 || 1);
        const one = concatArrays(
            new Uint8Array([ESC, 0x40]),        // initialize
            new Uint8Array([ESC, 0x61, 0x01]),  // center
            escPosRaster(mono, widthPx),
            new Uint8Array([ESC, 0x64, 0x03])   // feed 3 lines (no cut)
        );
        if (copies === 1) return one;
        const out = [];
        for (let i = 0; i < copies; i++) out.push(one);
        return concatArrays.apply(null, out);
    }

    function buildJob(mono, widthPx, heightPx, opts) {
        const o = opts || {};
        const profile = getProfile(o.profile);
        if (profile.protocol === 'TSPL') {
            return tsplJob(mono, widthPx, heightPx, Object.assign({ dpi: profile.dpi, widthMm: profile.paper.widthMm }, o));
        }
        return escPosJob(mono, widthPx, heightPx, o);
    }

    /* ================= Connection manager (injectable for tests) ================= */
    /* env = { bluetooth?, storage?, onStatus?, logger? } */
    function defaultEnv() {
        return {
            bluetooth: isBrowser() ? navigator.bluetooth : undefined,
            storage: isBrowser() ? window.localStorage : null,
            onStatus: null,
            logger: function () {},
        };
    }
    function storageKey(role) { return 'ecleanbt.' + role; }
    function savePref(env, role, data) {
        try {
            if (env.storage && typeof env.storage.setItem === 'function') {
                env.storage.setItem(storageKey(role), JSON.stringify(data));
            }
        } catch (e) { /* storage may be blocked — non-fatal */ }
    }
    function loadPref(env, role) {
        try {
            if (env.storage && typeof env.storage.getItem === 'function') {
                const raw = env.storage.getItem(storageKey(role));
                return raw ? JSON.parse(raw) : null;
            }
        } catch (e) { /* ignore */ }
        return null;
    }

    function isSupported(env) {
        return !!(env || defaultEnv()).bluetooth;
    }

    function requestDevice(env, role) {
        const e = env || defaultEnv();
        if (!e.bluetooth) return Promise.reject(new Error('NO_BLUETOOTH'));
        const filters = [
            { namePrefix: 'RPP' }, { namePrefix: 'RP' },
            { namePrefix: 'RONGTA' }, { namePrefix: 'PRINT' },
            { namePrefix: 'PRN' }, { namePrefix: 'MTP' },
            { namePrefix: 'MP' }, { namePrefix: 'BT' },
            { namePrefix: 'Thermal' }, { namePrefix: 'POS' },
            { namePrefix: 'XP' }, { namePrefix: 'GPRT' }, { namePrefix: 'HM' }, { namePrefix: 'BLU' },
        ];
        const req = { filters: filters, optionalServices: OPTIONAL_SERVICES };
        // Chrome Android shows one picker; acceptAllDevices enlarges the list.
        return e.bluetooth.requestDevice({ acceptAllDevices: true, optionalServices: OPTIONAL_SERVICES })
            .catch(function (errAll) {
                const msg = String((errAll && errAll.message) || errAll);
                if (errAll && errAll.name === 'NotFoundError') throw errAll;
                if (/cancell?ed|cansell?ed/i.test(msg)) throw errAll;
                // Fall back to filtered list if acceptAllDevices is rejected.
                if (/acceptAllDevices|filters|optional/i.test(msg)) {
                    return e.bluetooth.requestDevice(req);
                }
                throw errAll;
            });
    }

    async function testWriteProbe(ch, probe) {
        if (!ch || !ch.properties || !probe) return !!probe;
        const p = ch.properties;
        if (!p.write && !p.writeWithoutResponse) return false;
        try {
            if (p.write && typeof ch.writeValueWithResponse === 'function') {
                await ch.writeValueWithResponse(probe);
                return true;
            }
            if (p.writeWithoutResponse && typeof ch.writeValueWithoutResponse === 'function') {
                await ch.writeValueWithoutResponse(probe);
                return true;
            }
            if (typeof ch.writeValue === 'function') {
                await ch.writeValue(probe);
                return true;
            }
        } catch (e1) {
            try {
                if (p.writeWithoutResponse && typeof ch.writeValueWithoutResponse === 'function') {
                    await ch.writeValueWithoutResponse(probe);
                    return true;
                }
                if (typeof ch.writeValue === 'function') {
                    await ch.writeValue(probe);
                    return true;
                }
            } catch (e2) {
                return false;
            }
        }
        return false;
    }

    async function discoverWritableCharacteristic(server, probe) {
        // Phase 1 — prioritized candidates
        for (let i = 0; i < KNOWN_SERVICES.length; i++) {
            const cand = KNOWN_SERVICES[i];
            try {
                const service = await server.getPrimaryService(cand.service);
                if (!service) continue;
                const check = async function (wUuid) {
                    try {
                        const ch = await service.getCharacteristic(wUuid);
                        if (ch && (ch.properties.write || ch.properties.writeWithoutResponse)) {
                            if (!probe) return ch; // TSPL: no probe — trust writable channel
                            const verified = await testWriteProbe(ch, probe);
                            if (verified) return ch;
                        }
                    } catch (e) { /* next candidate */ }
                    return null;
                };
                for (let j = 0; j < cand.write.length; j++) {
                    const hit = await check(cand.write[j]);
                    if (hit) return hit;
                }
                // Some stacks reject getCharacteristic for unknown UUIDs but expose characteristics()
                try {
                    const chars = await service.getCharacteristics();
                    for (let k = 0; k < chars.length; k++) {
                        const ch = chars[k];
                        if (ch && (ch.properties.write || ch.properties.writeWithoutResponse)) {
                            if (!probe) return ch;
                            const verified = await testWriteProbe(ch, probe);
                            if (verified) return ch;
                        }
                    }
                } catch (e) { /* continue */ }
            } catch (e) { /* service not found */ }
        }
        // Phase 2 — full sweep of everything the device exposes
        try {
            const allServices = await server.getPrimaryServices();
            for (let sIdx = 0; sIdx < allServices.length; sIdx++) {
                try {
                    const chars = await allServices[sIdx].getCharacteristics();
                    for (let cIdx = 0; cIdx < chars.length; cIdx++) {
                        const ch = chars[cIdx];
                        if (ch && (ch.properties.write || ch.properties.writeWithoutResponse)) {
                            if (!probe) return ch;
                            const verified = await testWriteProbe(ch, probe);
                            if (verified) return ch;
                        }
                    }
                } catch (e) { /* continue */ }
            }
        } catch (e) { /* continue */ }
        return null;
    }

    /* Connection state machine: disconnected → connecting → connected/printing/error */
    function createConnection(envInject) {
        const env = envInject || defaultEnv();
        const state = {
            role: 'invoice',
            device: null,
            characteristic: null,
            status: 'disconnected',
            lastError: null,
            listeners: [],
        };
        function emit(status, extra) {
            state.status = status;
            const payload = Object.assign({ status: status, role: state.role, device: state.device ? (state.device.name || 'طابعة') : null }, extra || {});
            state.listeners.slice().forEach(function (fn) {
                try { fn(payload); } catch (e) { /* listener errors are non-fatal */ }
            });
            if (typeof env.onStatus === 'function') {
                try { env.onStatus(payload); } catch (e) { /* ignore */ }
            }
        }
        function on(event, fn) {
            if (event === 'status') state.listeners.push(fn);
        }
        function off(event, fn) {
            if (event !== 'status') return;
            state.listeners = state.listeners.filter(function (l) { return l !== fn; });
        }
        function handleDisconnected() {
            state.characteristic = null;
            emit('disconnected');
        }
        async function connect(role) {
            const e = env;
            if (!isSupported(e)) throw new Error('NO_BLUETOOTH');
            state.role = role || state.role || 'invoice';
            emit('connecting');
            const device = await requestDevice(e, state.role);
            try {
                device.addEventListener('gattserverdisconnected', handleDisconnected);
            } catch (e2) { /* older stacks */ }
            const server = await device.gatt.connect();
            const profile = getProfile(loadPref(e, state.role)?.profile || 'rpp320-tspl');
            const ch = await discoverWritableCharacteristic(server, profile.probe || null);
            if (!ch) {
                state.device = device;
                emit('error', { error: 'CHANNEL_FAIL' });
                throw new Error('CHANNEL_FAIL');
            }
            state.device = device;
            state.characteristic = ch;
            state.lastError = null;
            savePref(e, state.role, { profile: profile.id, deviceName: device.name || null });
            emit('connected', { device: device.name });
            return device;
        }
        async function reconnect() {
            if (!state.device) return false;
            try {
                emit('connecting');
                const server = await state.device.gatt.connect();
                const profile = getProfile(loadPref(env, state.role)?.profile || 'rpp320-tspl');
                const ch = await discoverWritableCharacteristic(server, profile.probe || null);
                if (!ch) throw new Error('CHANNEL_FAIL');
                state.characteristic = ch;
                emit('connected', { device: state.device.name, reconnected: true });
                return true;
            } catch (e) {
                state.lastError = e;
                emit('error', { error: e });
                return false;
            }
        }
        async function ensure() {
            if (state.device && state.device.gatt && state.device.gatt.connected && state.characteristic) {
                return true;
            }
            if (state.device && state.device.gatt && !state.device.gatt.connected) {
                return reconnect();
            }
            return false;
        }
        function forget() {
            try {
                if (state.storage && typeof state.storage.removeItem === 'function') {
                    state.storage.removeItem(storageKey(state.role));
                }
            } catch (e) { /* ignore */ }
            state.device = null;
            state.characteristic = null;
            emit('disconnected');
        }
        return {
            state: state,
            on: on,
            off: off,
            isSupported: function () { return isSupported(env); },
            connect: connect,
            reconnect: reconnect,
            ensure: ensure,
            forget: forget,
            requestDevice: function (role) { return requestDevice(env, role); },
            loadPref: function (role) { return loadPref(env, role); },
            savePref: function (role, data) { return savePref(env, role, data); },
        };
    }

    /* ================= Job queue (serial + abort) ================= */
    function createJobManager() {
        const state = { tail: Promise.resolve(), busy: false, aborted: false };
        function isBusy() { return state.busy; }
        function abort() {
            state.aborted = true;
        }
        function enqueue(task) {
            state.aborted = false;
            const run = async function () {
                state.busy = true;
                try {
                    return await task(state);
                } finally {
                    state.busy = false;
                }
            };
            state.tail = state.tail.then(run, run);
            return state.tail;
        }
        function isAborted() { return state.aborted; }
        return { enqueue: enqueue, isBusy: isBusy, abort: abort, isAborted: isAborted };
    }

    /* ================= Chunked writer with reconnect ================= */
    const WRITE = { chunkSize: 128, minChunkSize: 20, chunkDelayMs: 25, maxReconnectAttempts: 3 };
    const writeChains = new WeakMap();
    async function writeChunked(conn, bytes, opts) {
        /* Serialize jobs per characteristic — a second job waits for the first
         * to finish, it never interleaves chunks on the wire. */
        const key = conn && conn.state ? conn.state.characteristic || conn.state : null;
        const previous = key ? (writeChains.get(key) || Promise.resolve()) : Promise.resolve();
        const run = previous.then(function () { return writeChunkedInner(conn, bytes, opts); });
        if (key) {
            const tracked = run.catch(function () { /* keep chain alive after failure */ });
            writeChains.set(key, tracked);
        }
        return run;
    }
    async function writeChunkedInner(conn, bytes, opts) {
        const env = opts || {};
        const onProgress = env.onProgress || function () {};
        const onStatus = env.onStatus || function () {};
        const isAborted = env.isAborted || function () { return false; };
        const profile = getProfile(env.profile || 'rpp320-tspl');
        let chunkSize = profile.chunkSize || WRITE.chunkSize;
        let reconnects = 0;
        let offset = 0;

        if (onProgress) onProgress(0);
        while (offset < bytes.length) {
            if (isAborted()) {
                throw new Error('JOB_ABORTED');
            }
            const connected = await conn.ensure();
            if (!connected) {
                if (reconnects < WRITE.maxReconnectAttempts) {
                    reconnects++;
                    onStatus('انقطع الاتصال، جارٍ إعادة الاتصال التلقائي (محاولة ' + reconnects + '/' + WRITE.maxReconnectAttempts + ')...', 'warning');
                    await sleep(1000);
                    continue;
                }
                throw new Error('انقطع الاتصال بالطابعة أثناء الطباعة');
            }
            const chunk = bytes.slice(offset, offset + chunkSize);
            try {
                const p = conn.state.characteristic.properties || {};
                const withResponse = p.write && typeof conn.state.characteristic.writeValueWithResponse === 'function';
                if (withResponse) {
                    try {
                        await conn.state.characteristic.writeValueWithResponse(chunk);
                    } catch (we) {
                        if (p.writeWithoutResponse && typeof conn.state.characteristic.writeValueWithoutResponse === 'function') {
                            await conn.state.characteristic.writeValueWithoutResponse(chunk);
                        } else {
                            throw we;
                        }
                    }
                } else if (p.writeWithoutResponse && typeof conn.state.characteristic.writeValueWithoutResponse === 'function') {
                    await conn.state.characteristic.writeValueWithoutResponse(chunk);
                } else if (typeof conn.state.characteristic.writeValue === 'function') {
                    await conn.state.characteristic.writeValue(chunk);
                } else {
                    throw new Error('لا توجد وسيلة كتابة مدعومة لهذه الخاصية');
                }
                offset += chunk.length;
                reconnects = 0;
                if (onProgress) onProgress(Math.min(100, Math.floor((offset / bytes.length) * 100)));
                if (offset < bytes.length) await sleep(WRITE.chunkDelayMs);
            } catch (err) {
                if (!conn.state.device || !conn.state.device.gatt || !conn.state.device.gatt.connected) {
                    if (reconnects < WRITE.maxReconnectAttempts) {
                        reconnects++;
                        onStatus('انقطع الاتصال، جارٍ إعادة الاتصال التلقائي (محاولة ' + reconnects + '/' + WRITE.maxReconnectAttempts + ')...', 'warning');
                        await sleep(1000);
                        continue;
                    }
                    throw new Error('انقطع الاتصال بالطابعة أثناء الطباعة');
                }
                if (chunkSize > WRITE.minChunkSize) {
                    chunkSize = Math.max(WRITE.minChunkSize, Math.floor(chunkSize / 2));
                    onStatus('تعديل حجم الحزمة إلى ' + chunkSize + ' بايت للملاءمة...', 'info');
                    await sleep(35);
                    continue;
                }
                throw err;
            }
        }
        if (onProgress) onProgress(100);
    }

    /* ================= Stickers helpers (pure) ================= */
    function expandItems(items) {
        const out = [];
        const list = Array.isArray(items) ? items : [];
        for (let i = 0; i < list.length; i++) {
            const it = list[i];
            const qty = Math.max(0, Math.floor(Number(it.quantity) || 0));
            const copies = it.copies !== undefined ? Math.max(1, Math.floor(Number(it.copies) || 1)) : qty || 1;
            for (let c = 0; c < copies; c++) out.push(it);
        }
        return out;
    }
    function itemLabel(item) {
        const name = item && item.name ? String(item.name) : '';
        const service = item && item.service ? String(item.service) : '';
        return service ? name + ' — ' + service : name;
    }

    /* ================= Export ================= */
    return {
        version: VERSION,
        constants: {
            KNOWN_SERVICES: KNOWN_SERVICES,
            OPTIONAL_SERVICES: OPTIONAL_SERVICES,
            PROFILES: PROFILES,
            WRITE: WRITE,
        },
        util: {
            sleep: sleep,
            concatArrays: concatArrays,
            ascii: ascii,
            isBrowser: isBrowser,
            isAndroid: isAndroid,
            isIos: isIos,
            webusbSupported: webusbSupported,
            storageKey: storageKey,
            savePref: savePref,
            loadPref: loadPref,
        },
        errors: { formatArabicError: formatArabicError },
        profiles: { get: getProfile, list: function () { return Object.keys(PROFILES).map(function (k) { return PROFILES[k]; }); } },
        raster: {
            rgbaToMono: rgbaToMono,
            canvasToMono: canvasToMono,
            escPosRaster: escPosRaster,
            tsplBitmap: tsplBitmap,
            buildJob: buildJob,
            tsplJob: tsplJob,
            escPosJob: escPosJob,
        },
        connection: {
            create: createConnection,
            requestDevice: requestDevice,
            discoverWritableCharacteristic: discoverWritableCharacteristic,
            testWriteProbe: testWriteProbe,
            defaultEnv: defaultEnv,
            isSupported: isSupported,
            /* Gesture-safe connect path for click handlers:
             * fast path = nothing awaited before requestDevice (keeps user activation),
             * reconnect needs no gesture, prompt only as fallback. */
            ensureInteractive: async function (conn, role) {
                const st = conn && conn.state ? conn.state : {};
                const live = !!(st.characteristic && st.device && st.device.gatt && st.device.gatt.connected);
                if (live) return true;
                if (st.device) {
                    try {
                        const ok = await conn.reconnect();
                        if (ok) return true;
                    } catch (e) { /* fall through to the picker */ }
                }
                await conn.connect(role || 'invoice');
                return true;
            },
        },
        jobs: { create: createJobManager, writeChunked: writeChunked },
        stickers: { expandItems: expandItems, itemLabel: itemLabel },
    };
});
