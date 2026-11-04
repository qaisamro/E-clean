<x-app-layout :title="config('app.name') . ' | ملصقات طلب ' . $order->order_code">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row gap-3 mb-4 no-print">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">
                <a href="{{ route('orders.show', $order) }}">طلب {{ $order->order_code }}</a> /
            </span>
            ملصقات القطع
        </h4>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <button id="btnSaveAll" class="btn btn-primary">
                <i class='bx bx-download me-1'></i> حفظ كل الصور
            </button>
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class='bx bx-printer me-1'></i> طباعة ورقية
            </button>
            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
                <i class='bx bx-arrow-back me-1'></i> رجوع للطلب
            </a>
        </div>
    </div>

    {{-- حالة الاتصال وشريط التقدم --}}
    <div id="btStatusWrap" class="no-print mb-3" style="display:none">
        <div id="btLog" class="alert alert-info py-2 small mb-2 d-flex justify-content-between align-items-center">
            <span id="btLogText"></span>
            <button type="button" class="btn-close btn-sm" onclick="document.getElementById('btStatusWrap').style.display='none'"></button>
        </div>
        <div id="btProgressWrap" style="display:none !important;"></div>
    </div>

    {{-- تنبيه خاص بمتصفحات iOS غير الداعمة --}}
    <div id="btBrowserNotice" class="alert alert-warning no-print py-2 small" style="display:none"></div>

    @if ($order->orderDetails->isEmpty())
        <div class="alert alert-warning no-print">لا توجد قطع في هذا الطلب.</div>
    @endif

    <div class="stickers-sheet">
        @foreach ($order->orderDetails as $index => $detail)
            <div class="sticker-wrap">
                <div class="sticker">
                    <div class="sticker-customer">{{ $order->customer?->name ?? '—' }}</div>
                    <div class="sticker-order"># {{ $order->order_code }}</div>
                    <div class="sticker-item">{{ $detail->item?->name ?? '' }}{{ $detail->service?->name ? ' — ' . $detail->service->name : '' }}</div>
                </div>
                <div class="d-flex gap-1 justify-content-center no-print mt-1">
                    <button class="btn btn-sm btn-outline-info btn-share" data-index="{{ $index }}"
                        onclick="window.__shareSticker && window.__shareSticker(parseInt(this.dataset.index||'0',10))">
                        <i class='bx bx-share-alt me-1'></i> حفظ / مشاركة
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <style>
        .stickers-sheet {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .sticker-wrap {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .sticker {
            width: 72mm;
            min-height: 34mm;
            border: 1.5px dashed #333;
            border-radius: 4px;
            padding: 3mm 4mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 1mm;
            background: #fff;
            page-break-inside: avoid;
            direction: rtl;
            font-family: 'Tajawal', 'Cairo', Tahoma, Arial, sans-serif !important;
        }
        .sticker-customer {
            font-size: 20px;
            font-weight: 800;
            line-height: 1.2;
        }
        .sticker-order {
            font-size: 16px;
            font-weight: 700;
        }
        .sticker-item {
            font-size: 13px;
            color: #333;
        }
        .sticker-meta {
            font-size: 11px;
            color: #555;
        }
        @media print {
            @page {
                size: auto;
                margin: 4mm;
            }
            body * {
                visibility: hidden !important;
            }
            .stickers-sheet, .stickers-sheet * {
                visibility: visible !important;
            }
            .stickers-sheet {
                position: absolute;
                inset: 0;
                display: block;
            }
            .sticker {
                margin-bottom: 4mm;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <canvas id="btCanvas" width="384" height="220" style="display:none"></canvas>
    @php
        $btStickers = $order->orderDetails->map(function ($d) use ($order) {
            return [
                'customer' => $order->customer?->name ?? '',
                'order' => (string) $order->order_code,
                'item' => trim(($d->item?->name ?? '') . ($d->service?->name ? ' - ' . $d->service->name : '')),
            ];
        })->values();
    @endphp
    <script id="bt-data" type="application/json">@json($btStickers)</script>
    <script>
/* Inlined bt-label-print (RPP320 / Toprint / Bluefy iOS Compatible) */
'use strict';
(function () {
  const CONFIG = {
    printerWidthPx: 384,
    chunkSize: 128,
    chunkDelayMs: 25,
    minChunkSize: 20,
    maxReconnectAttempts: 3,
  };

  const CANDIDATES = [
    // 1) Nordic UART Service (most common for BLE label/thermal printers)
    {
      service: '6e400001-b5a3-f393-e0a9-e50e24dcca9e',
      write: ['6e400002-b5a3-f393-e0a9-e50e24dcca9e']
    },
    // 2) 0xFF00
    {
      service: 0xFF00,
      write: [0xFF02, 0xFF01, '0000ff02-0000-1000-8000-00805f9b34fb', '0000ff01-0000-1000-8000-00805f9b34fb']
    },
    {
      service: '0000ff00-0000-1000-8000-00805f9b34fb',
      write: ['0000ff02-0000-1000-8000-00805f9b34fb', '0000ff01-0000-1000-8000-00805f9b34fb', 0xFF02, 0xFF01]
    },
    // 3) 0xFFE0
    {
      service: 0xFFE0,
      write: [0xFFE1, '0000ffe1-0000-1000-8000-00805f9b34fb']
    },
    {
      service: '0000ffe0-0000-1000-8000-00805f9b34fb',
      write: ['0000ffe1-0000-1000-8000-00805f9b34fb', 0xFFE1]
    },
    // 4) 0xFFF0
    {
      service: 0xFFF0,
      write: [0xFFF1, 0xFFF2, 0xFFF3, '0000fff1-0000-1000-8000-00805f9b34fb', '0000fff2-0000-1000-8000-00805f9b34fb']
    },
    {
      service: '0000fff0-0000-1000-8000-00805f9b34fb',
      write: ['0000fff1-0000-1000-8000-00805f9b34fb', '0000fff2-0000-1000-8000-00805f9b34fb', 0xFFF1, 0xFFF2, 0xFFF3]
    },
    // 5) ISSC Transparent
    {
      service: '49535343-fe7d-4ae5-8fa9-9fafd205e455',
      write: ['49535343-8841-43f4-a8d4-ecbe34729bb3']
    },
    // 6) Other POS services
    {
      service: 0x18F0,
      write: [0x2AF0, 0x2AF1, '00002af0-0000-1000-8000-00805f9b34fb', '00002af1-0000-1000-8000-00805f9b34fb']
    },
    {
      service: '000018f0-0000-1000-8000-00805f9b34fb',
      write: ['00002af0-0000-1000-8000-00805f9b34fb', '00002af1-0000-1000-8000-00805f9b34fb']
    },
    {
      service: 0xAE30,
      write: [0xAE01, '0000ae01-0000-1000-8000-00805f9b34fb']
    },
    {
      service: 0xAF30,
      write: [0xAF01, '0000af01-0000-1000-8000-00805f9b34fb']
    },
    {
      service: 0xFEE7,
      write: [0xFEC7, 0xFEC8, 0xFED4, 0xFED5, 0xFED6, 0xFED7, 0xFED8]
    }
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
    '0000180a-0000-1000-8000-00805f9b34fb'
];

  let device = null;
  let writeChar = null;
  let isConnected = false;
  let isBusy = false;

  const ESC = 0x1B, GS = 0x1D;

  function $(id) {
    return document.getElementById(id);
  }

  function sleep(ms) {
    return new Promise((r) => setTimeout(r, ms));
  }

  function isIosDevice() {
    const ua = navigator.userAgent || '';
    return /iPad|iPhone|iPod/i.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
  }

  function isBluefyBrowser() {
    return /Bluefy/i.test(navigator.userAgent || '');
  }

  function setStatus(msg, type) {
    if (!type) type = 'info';
    const wrap = $('btStatusWrap');
    const logBox = $('btLog');
    const logText = $('btLogText');
    if (!logBox) return;

    if (wrap) wrap.style.display = 'block';
    logBox.style.display = 'flex';
    logBox.className = 'alert alert-' + type + ' py-2 small mb-2 d-flex justify-content-between align-items-center';

    if (logText) {
      logText.textContent = msg;
    } else {
      logBox.textContent = msg;
    }
  }

  function setProgress(pct) {
    const wrap = $('btProgressWrap');
    const bar = $('btProgressBar');
    if (!wrap || !bar) return;
    if (pct === null || pct < 0) {
      wrap.style.display = 'none';
      bar.style.width = '0%';
      bar.textContent = '0%';
      return;
    }
    wrap.style.display = 'block';
    bar.style.width = pct + '%';
    bar.textContent = pct + '%';
  }

  function updateButtons(busy) {
    const p = $('btnBtPrint');
    const c = $('btnBtConnect');
    if (p) p.disabled = !!busy;
    if (c) c.disabled = !!busy;
    const singles = document.querySelectorAll('.btn-print-single');
    singles.forEach(function (btn) {
      btn.disabled = !!busy;
    });
  }

  function formatArabicError(err) {
    if (!err) return 'حدث خطأ غير معروف';
    const name = (err && err.name) || '';
    const msg = (err && err.message) || String(err);

    if (err === 'NO_BLUETOOTH' || (/Bluetooth/i.test(msg) && !navigator.bluetooth)) {
      if (isIosDevice()) {
        return 'المتصفح الحالي لا يدعم Web Bluetooth على iOS — للطباعة المباشرة، افتح هذه الصفحة في تطبيق متصفح Bluefy المجاني من App Store.';
      }
      return 'المتصفح لا يدعم Web Bluetooth — استخدم Chrome أو Edge على أندرويد أو الكمبيوتر عبر اتصال HTTPS آمن.';
    }

    if (name === 'NotFoundError' || /cancelled/i.test(msg)) {
      return 'تم إلغاء اختيار الطابعة.';
    }

    if (name === 'SecurityError' || /HTTPS|localhost/i.test(msg)) {
      return 'يتطلب البلوتوث اتصالاً آمناً عبر HTTPS.';
    }

    if (name === 'NetworkError' || name === 'NotConnectedError') {
      return 'تعذر الاتصال بالطابعة — تأكد من تشغيلها وقربها وأنها غير متصلة بجهاز آخر.';
    }

    if (err === 'CHANNEL_FAIL' || /CHANNEL_FAIL/i.test(msg)) {
      return 'تم الاتصال بالجهاز ولكن لم يتم العثور على قناة كتابة متوافقة للطابعة (تأكد من اختيار طابعة ملصقات متوافقة).';
    }

    return msg;
  }

  function ensureBluetoothSupported() {
    if (navigator.bluetooth) return true;
    const isIos = isIosDevice();
    const noticeEl = $('btBrowserNotice');
    let msg = '';
    if (isIos) {
      msg = '⚠️ المتصفح الحالي لا يدعم Web Bluetooth على أجهزة آبل (سفاري/كروم iOS). للطباعة المباشرة بضغطة زر: افتح هذه الصفحة في متصفح Bluefy – Web BLE Browser المجاني من App Store، أو استخدم خيار الطباعة الورقية.';
    } else {
      msg = '⚠️ المتصفح لا يدعم Web Bluetooth — يرجى استخدام متصفح Chrome أو Edge على أندرويد أو الكمبيوتر مع التأكد من الاتصال الآمن عبر HTTPS.';
    }
    if (noticeEl) {
      noticeEl.textContent = msg;
      noticeEl.style.display = 'block';
    }
    setStatus(msg, 'warning');
    return false;
  }

  // Non-destructive probe test using standard ESC/POS DLE EOT status request [0x10, 0x04, 0x01]
  async function testWriteProbe(ch) {
    if (!ch || !ch.properties) return false;
    const p = ch.properties;
    if (!p.write && !p.writeWithoutResponse) return false;
    const probe = new Uint8Array([0x10, 0x04, 0x01]);
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
        } else if (typeof ch.writeValue === 'function') {
          await ch.writeValue(probe);
          return true;
        }
      } catch (e2) {
        return false;
      }
    }
    return false;
  }

  async function discoverWritableCharacteristic(server) {
    // Phase 1: Try prioritized candidates
    for (let i = 0; i < CANDIDATES.length; i++) {
      const cand = CANDIDATES[i];
      try {
        const service = await server.getPrimaryService(cand.service);
        if (!service) continue;

        if (cand.write && cand.write.length) {
          for (let j = 0; j < cand.write.length; j++) {
            const wUuid = cand.write[j];
            try {
              const ch = await service.getCharacteristic(wUuid);
              if (ch && (ch.properties.write || ch.properties.writeWithoutResponse)) {
                const verified = await testWriteProbe(ch);
                if (verified) return ch;
              }
            } catch (e) { /* continue checking next candidate */ }
          }
        }

        try {
          const chars = await service.getCharacteristics();
          for (let k = 0; k < chars.length; k++) {
            const ch = chars[k];
            if (ch && (ch.properties.write || ch.properties.writeWithoutResponse)) {
              const verified = await testWriteProbe(ch);
              if (verified) return ch;
            }
          }
        } catch (e) { /* continue */ }
      } catch (e) {
        // Service not found, try next candidate
      }
    }

    // Phase 2: Comprehensive Fallback - scan ALL services and characteristics
    try {
      const allServices = await server.getPrimaryServices();
      for (let sIdx = 0; sIdx < allServices.length; sIdx++) {
        const svc = allServices[sIdx];
        try {
          const chars = await svc.getCharacteristics();
          for (let cIdx = 0; cIdx < chars.length; cIdx++) {
            const ch = chars[cIdx];
            if (ch && (ch.properties.write || ch.properties.writeWithoutResponse)) {
              const verified = await testWriteProbe(ch);
              if (verified) return ch;
            }
          }
        } catch (e) { /* continue */ }
      }
    } catch (e) { /* continue */ }

    return null;
  }

  function onDisconnected() {
    isConnected = false;
    writeChar = null;
    updateButtons(false);
    setStatus('انقطع الاتصال بالطابعة — أعد المحاولة أو اضغط اتصال', 'warning');
  }

  async function reconnectGatt() {
    try {
      if (!device || !device.gatt) return false;
      const server = await device.gatt.connect();
      const ch = await discoverWritableCharacteristic(server);
      if (ch) {
        writeChar = ch;
        isConnected = true;
        return true;
      }
    } catch (e) {
      console.warn('[BT] Reconnect failed:', e);
    }
    return false;
  }

  // Must be called directly from user click handler so requestDevice succeeds synchronously
  async function connectDevice() {
    if (!navigator.bluetooth) {
      ensureBluetoothSupported();
      return false;
    }

    isBusy = true;
    updateButtons(true);
    const bluefy = isBluefyBrowser();
    setStatus(bluefy ? 'متصفح Bluefy — اختر الطابعة من القائمة...' : 'اختر طابعة الملصقات من القائمة...', 'info');

    let chosenDevice;
    try {
      chosenDevice = await btPickDevice();
    } catch (err) {
      isBusy = false;
      updateButtons(false);
      if (err.name === 'NotFoundError' || /cancelled/i.test(err.message)) {
        setStatus('تم إلغاء اختيار الطابعة.', 'secondary');
      } else {
        setStatus(formatArabicError(err), 'danger');
      }
      return false;
    }

    try {
      device = chosenDevice;
      device.addEventListener('gattserverdisconnected', onDisconnected);
      setStatus('تم اختيار الجهاز، جارٍ الاتصال بقناة GATT...', 'info');

      const server = await device.gatt.connect();
      setStatus('متصل GATT — جارٍ فحص قناة الكتابة للطابعة...', 'info');

      const ch = await discoverWritableCharacteristic(server);
      if (!ch) {
        throw new Error('CHANNEL_FAIL');
      }

      writeChar = ch;
      isConnected = true;
      isBusy = false;
      updateButtons(false);
      setStatus('متصل: ' + (device.name || 'طابعة الملصقات') + ' بنجاح', 'success');
      return true;
    } catch (err) {
      isConnected = false;
      writeChar = null;
      isBusy = false;
      updateButtons(false);
      setStatus(formatArabicError(err), 'danger');
      return false;
    }
  }

  async function ensureConnected() {
    if (isConnected && device && device.gatt && device.gatt.connected && writeChar) {
      return true;
    }
    if (device && device.gatt) {
      setStatus('جارٍ استعادة الاتصال بالطابعة...', 'info');
      const reconnected = await reconnectGatt();
      if (reconnected) return true;
    }
    return await connectDevice();
  }

  // Chunked transmission with confirmed write preference, auto-downsizing and auto-reconnect
  async function writeBytesWithChunks(bytes, onProgress, onStatus) {
    let offset = 0;
    let chunkSize = CONFIG.chunkSize;
    const delayMs = CONFIG.chunkDelayMs;
    let reconnectAttempts = 0;

    if (onProgress) onProgress(0);

    while (offset < bytes.length) {
      if (!device || !device.gatt || !device.gatt.connected || !writeChar) {
        if (reconnectAttempts < CONFIG.maxReconnectAttempts && device) {
          reconnectAttempts++;
          if (onStatus) onStatus('انقطع الاتصال، جارٍ إعادة الاتصال التلقائي واستكمال الطباعة (محاولة ' + reconnectAttempts + '/' + CONFIG.maxReconnectAttempts + ')...', 'warning');
          await sleep(1000);
          const ok = await reconnectGatt();
          if (ok) {
            if (onStatus) onStatus('تمت استعادة الاتصال بنجاح، استكمال الإرسال...', 'info');
            continue;
          }
        }
        throw new Error('انقطع الاتصال بالطابعة أثناء الطباعة');
      }

      const chunk = bytes.slice(offset, offset + chunkSize);
      try {
        const p = writeChar.properties || {};
        if (p.write && typeof writeChar.writeValueWithResponse === 'function') {
          try {
            await writeChar.writeValueWithResponse(chunk);
          } catch (we) {
            if (p.writeWithoutResponse && typeof writeChar.writeValueWithoutResponse === 'function') {
              await writeChar.writeValueWithoutResponse(chunk);
            } else {
              throw we;
            }
          }
        } else if (p.writeWithoutResponse && typeof writeChar.writeValueWithoutResponse === 'function') {
          await writeChar.writeValueWithoutResponse(chunk);
        } else if (typeof writeChar.writeValue === 'function') {
          await writeChar.writeValue(chunk);
        } else {
          throw new Error('لا توجد وسيلة كتابة مدعومة للخاصية');
        }

        offset += chunk.length;
        reconnectAttempts = 0;
        const pct = Math.min(100, Math.floor((offset / bytes.length) * 100));
        if (onProgress) onProgress(pct);

        if (delayMs > 0 && offset < bytes.length) {
          await sleep(delayMs);
        }
      } catch (err) {
        console.warn('[BT] Write chunk error at offset', offset, err);
        if (!device || !device.gatt || !device.gatt.connected) {
          if (reconnectAttempts < CONFIG.maxReconnectAttempts && device) {
            reconnectAttempts++;
            if (onStatus) onStatus('انقطع الاتصال، جارٍ إعادة الاتصال التلقائي (محاولة ' + reconnectAttempts + '/' + CONFIG.maxReconnectAttempts + ')...', 'warning');
            await sleep(1000);
            const ok = await reconnectGatt();
            if (ok) continue;
          }
          throw new Error('انقطع الاتصال بالطابعة أثناء الطباعة');
        }

        // Auto-downsize chunk size on failure
        if (chunkSize > CONFIG.minChunkSize) {
          chunkSize = Math.max(CONFIG.minChunkSize, Math.floor(chunkSize / 2));
          if (onStatus) onStatus('تعديل حجم الحزمة إلى ' + chunkSize + ' بايت للملاءمة...', 'info');
          await sleep(35);
          continue;
        } else {
          throw err;
        }
      }
    }

    if (onProgress) onProgress(100);
  }

  function concatArrays() {
    let total = 0;
    for (let i = 0; i < arguments.length; i++) {
      if (arguments[i]) total += arguments[i].length;
    }
    const out = new Uint8Array(total);
    let offset = 0;
    for (let i = 0; i < arguments.length; i++) {
      if (arguments[i]) {
        out.set(arguments[i], offset);
        offset += arguments[i].length;
      }
    }
    return out;
  }

  function rasterCmd(mono, widthPx) {
    const row = Math.ceil(widthPx / 8);
    const h = Math.floor(mono.length / row);
    const BAND = 128;
    const parts = [];
    for (let y0 = 0; y0 < h; y0 += BAND) {
      const bh = Math.min(BAND, h - y0);
      const blk = new Uint8Array(8 + row * bh);
      blk.set([GS, 0x76, 0x30, 0x00, row & 0xFF, (row >> 8) & 0xFF, bh & 0xFF, (bh >> 8) & 0xFF], 0);
      blk.set(mono.subarray(y0 * row, (y0 + bh) * row), 8);
      parts.push(blk);
    }
    return concatArrays.apply(null, parts);
  }

  function drawSticker(cv, s) {
    const ctx = cv.getContext('2d');
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, cv.width, cv.height);

    // Dashed border around sticker
    ctx.strokeStyle = '#000000';
    ctx.lineWidth = 3;
    ctx.setLineDash([8, 6]);
    ctx.strokeRect(6, 6, cv.width - 12, cv.height - 12);
    ctx.setLineDash([]);

    ctx.fillStyle = '#000000';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';

    // Customer Name (bold)
    ctx.font = 'bold 36px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    ctx.fillText(s.customer || 'عميل نقدي', cv.width / 2, 52);

    // Order Code
    ctx.font = 'bold 32px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    ctx.fillText('# ' + (s.order || ''), cv.width / 2, 106);

    // Item / Service description
    ctx.font = '24px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    const itemText = s.item || '';
    if (itemText.length > 28) {
      ctx.font = '20px Tajawal, Cairo, Tahoma, Arial, sans-serif';
    }
    ctx.fillText(itemText, cv.width / 2, 158);
  }

  function canvasToMono(cv) {
    const ctx = cv.getContext('2d');
    const img = ctx.getImageData(0, 0, cv.width, cv.height).data;
    const row = Math.ceil(cv.width / 8);
    const mono = new Uint8Array(row * cv.height);
    for (let y = 0; y < cv.height; y++) {
      for (let x = 0; x < cv.width; x++) {
        const i = (y * cv.width + x) * 4;
        const alpha = img[i + 3];
        const lum = 0.299 * img[i] + 0.587 * img[i + 1] + 0.114 * img[i + 2];
        if (alpha > 128 && lum < 128) {
          mono[y * row + (x >> 3)] |= 0x80 >> (x & 7);
        }
      }
    }
    return mono;
  }

  function stickerData() {
    try {
      const el = document.getElementById('bt-data');
      if (el) {
        const arr = JSON.parse(el.textContent || '[]');
        if (Array.isArray(arr)) return arr;
      }
    } catch (err) { /* fall through */ }
    return Array.isArray(window.BT_STICKERS) ? window.BT_STICKERS : [];
  }

  async function printAll() {
    const list = stickerData();
    if (!list.length) {
      setStatus('لا توجد ملصقات للطباعة', 'warning');
      return;
    }
    isBusy = true;
    updateButtons(true);
    setProgress(0);

    const cv = $('btCanvas') || document.createElement('canvas');
    cv.width = CONFIG.printerWidthPx;
    cv.height = 220;

    let successCount = 0;
    try {
      for (let i = 0; i < list.length; i++) {
        const s = list[i];
        setStatus('جارٍ إرسال الملصق (' + (i + 1) + ' من ' + list.length + ')...', 'info');
        drawSticker(cv, s);
        const mono = canvasToMono(cv);
        const job = concatArrays(
          new Uint8Array([ESC, 0x40]),
          new Uint8Array([ESC, 0x61, 0x01]),
          rasterCmd(mono, cv.width),
          new Uint8Array([ESC, 0x64, 0x03])
        );

        await writeBytesWithChunks(
          job,
          function (chunkPct) {
            const overall = Math.floor(((i + (chunkPct / 100)) / list.length) * 100);
            setProgress(overall);
          },
          function (m, t) { setStatus(m, t); }
        );
        successCount++;
        await sleep(150);
      }
      setProgress(100);
      setStatus('✅ تمت طباعة جميع الملصقات بنجاح (' + successCount + ' ملصق)', 'success');
    } catch (err) {
      setStatus('خطأ أثناء الطباعة (طُبع ' + successCount + '/' + list.length + '): ' + formatArabicError(err), 'danger');
    } finally {
      isBusy = false;
      updateButtons(false);
      setTimeout(function () { setProgress(null); }, 4000);
    }
  }

  async function printSingle(index) {
    const list = stickerData();
    const s = list[index];
    if (!s) {
      setStatus('الملصق غير موجود', 'warning');
      return;
    }
    isBusy = true;
    updateButtons(true);
    setProgress(0);

    const cv = $('btCanvas') || document.createElement('canvas');
    cv.width = CONFIG.printerWidthPx;
    cv.height = 220;

    try {
      setStatus('جارٍ إرسال الملصق: ' + (s.item || s.customer) + '...', 'info');
      drawSticker(cv, s);
      const mono = canvasToMono(cv);
      const job = concatArrays(
        new Uint8Array([ESC, 0x40]),
        new Uint8Array([ESC, 0x61, 0x01]),
        rasterCmd(mono, cv.width),
        new Uint8Array([ESC, 0x64, 0x03])
      );

      await writeBytesWithChunks(
        job,
        function (pct) { setProgress(pct); },
        function (m, t) { setStatus(m, t); }
      );
      setProgress(100);
      setStatus('✅ تمت طباعة الملصق بنجاح', 'success');
    } catch (err) {
      setStatus('خطأ أثناء الطباعة: ' + formatArabicError(err), 'danger');
    } finally {
      isBusy = false;
      updateButtons(false);
      setTimeout(function () { setProgress(null); }, 3000);
    }
  }

  // 1-Click Handlers (Synchronous click -> requestDevice if not connected)
  async function printAllOneClick() {
    if (isBusy) return;
    if (!ensureBluetoothSupported()) return;

    if (!isConnected || !device || !device.gatt || !device.gatt.connected || !writeChar) {
      const ok = await connectDevice();
      if (!ok) return; // Cancelled or error, status already displayed
    }

    await printAll();
  }

  async function btPickDevice() {
  const reqFiltered = {
    filters: [
      { namePrefix: 'RPP' }, { namePrefix: 'RP' },
      { namePrefix: 'RONGTA' }, { namePrefix: 'PRINT' },
      { namePrefix: 'PRN' }, { namePrefix: 'MTP' },
      { namePrefix: 'MP' }, { namePrefix: 'BT' },
      { namePrefix: 'Blue' }, { namePrefix: 'Thermal' },
      { namePrefix: 'POS' }, { namePrefix: 'XP' },
      { namePrefix: 'GPRT' }, { namePrefix: 'HM' }
    ],
    optionalServices: OPTIONAL_SERVICES
  };
  try {
    return await navigator.bluetooth.requestDevice({ acceptAllDevices: true, optionalServices: OPTIONAL_SERVICES });
  } catch (errAll) {
    const mAll = (errAll && errAll.message) || String(errAll);
    if (errAll && errAll.name === 'NotFoundError') throw errAll;
    if (/cancell?ed|cansell?ed/i.test(mAll)) throw errAll;
    if (!/pars|payload|option|service|filter|argument|invalid/i.test(mAll)) throw errAll;
    return await navigator.bluetooth.requestDevice(reqFiltered);
  }
}

async function printSingleOneClick(index) {
    if (isBusy) return;
    if (!ensureBluetoothSupported()) return;

    if (!isConnected || !device || !device.gatt || !device.gatt.connected || !writeChar) {
      const ok = await connectDevice();
      if (!ok) return; // Cancelled or error, status already displayed
    }

    await printSingle(index);
  }

  /* ===== Share & Print via Toprint app (Web Share API) ===== */
  function renderStickerSync(index) {
    const list = stickerData();
    const s = list[index];
    if (!s) throw new Error('لا يوجد ملصق');
    const cv = document.createElement('canvas');
    cv.width = CONFIG.printerWidthPx;
    cv.height = 220;
    drawSticker(cv, s);
    const dataUrl = cv.toDataURL('image/png');
    if (!dataUrl || dataUrl.length < 100) throw new Error('تعذر إنشاء الصورة');
    const parts = dataUrl.split(',');
    const bin = atob(parts[1]);
    const arr = new Uint8Array(bin.length);
    for (let i = 0; i < bin.length; i++) arr[i] = bin.charCodeAt(i);
    return { blob: new Blob([arr], { type: 'image/png' }), dataUrl: dataUrl };
  }

  function downloadSticker(dataUrl, fileName) {
    try {
      const a = document.createElement('a');
      a.href = dataUrl;
      a.download = fileName;
      document.body.appendChild(a);
      a.click();
      setTimeout(function () { a.remove(); }, 2000);
      setStatus('تم تنزيل الصورة — افتح تطبيق Toprint واطبعها من ألبوم الصور', 'info');
    } catch (err) {
      setStatus('خطأ التنزيل: ' + (err.message || err), 'danger');
    }
  }

  function shareSticker(index) {
    let rendered = null;
    try {
      rendered = renderStickerSync(index);
    } catch (err) {
      setStatus('خطأ: ' + (err.message || err), 'danger');
      return;
    }
    const fileName = 'label-' + (index + 1) + '.png';
    const blob = rendered.blob, dataUrl = rendered.dataUrl;

    if (navigator.canShare) {
      try {
        const file = new File([blob], fileName, { type: 'image/png' });
        if (navigator.canShare({ files: [file] })) {
          navigator.share({
            files: [file],
            title: 'ملصق المغسلة',
            text: 'اضغط للمشاركة والطباعة عبر تطبيق Toprint',
          }).then(function () {
            setStatus('تمت المشاركة بنجاح', 'success');
          }, function (err) {
            if (!(err && err.name === 'AbortError')) {
              downloadSticker(dataUrl, fileName);
            } else {
              setStatus('تم إلغاء المشاركة', 'secondary');
            }
          });
          return;
        }
      } catch (err) { /* fall through to direct download */ }
    }
    downloadSticker(dataUrl, fileName);
  }

  async function saveAllStickers() {
    try {
      const list = stickerData();
      if (!list.length) { setStatus('لا توجد ملصقات للحفظ', 'warning'); return; }
      setStatus('جارٍ حفظ ' + list.length + ' صور...', 'info');
      for (let i = 0; i < list.length; i++) {
        try {
          const r = renderStickerSync(i);
          downloadSticker(r.dataUrl, 'label-' + (i + 1) + '.png');
        } catch (err) { setStatus('خطأ في الملصق ' + (i + 1), 'danger'); }
        await sleep(700);
      }
      setStatus('تم حفظ كل الصور - اطبعها من تطبيق Toprint', 'success');
    } catch (err) { setStatus('خطأ: ' + (err.message || err), 'danger'); }
  }

  // Exposed globally
  window.__shareSticker = shareSticker;
  window.__printSingleSticker = printSingleOneClick;
  window.__printAllStickers = printAllOneClick;
  window.__btConnect = connectDevice;
  window.__btReady = true;

  document.addEventListener('DOMContentLoaded', function () {
    const c = $('btnBtConnect');
    const p = $('btnBtPrint');
    if (c) c.addEventListener('click', connectDevice);
    if (p) p.addEventListener('click', printAllOneClick);
    const s = $('btnSaveAll');
    if (s) s.addEventListener('click', saveAllStickers);
  });
})();
</script>

</x-app-layout>
