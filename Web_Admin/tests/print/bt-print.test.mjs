/* E-Clean — automated tests for the unified Bluetooth print engine.
 * Run:  node --test tests/print/bt-print.test.mjs
 * Tests the shared engine that is byte-identical between the two apps. */
import { test, describe } from 'node:test';
import { strict as assert } from 'node:assert';
import { createRequire } from 'node:module';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const require = createRequire(import.meta.url);
const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..', '..');
const CORE = require(path.join(ROOT, 'public/web/js/eclean-bt-core.js'));
const INVOICE = require(path.join(ROOT, 'public/web/js/eclean-bt-invoice.js'));
const STICKERS = require(path.join(ROOT, 'public/web/js/eclean-bt-stickers.js'));
const UI = require(path.join(ROOT, 'public/web/js/eclean-bt-ui.js'));

const ESC = 0x1B, GS = 0x1D;

describe('byte helpers', () => {
    test('ascii encodes CRLF', () => {
        assert.deepEqual([...CORE.util.ascii('A\r\n')], [0x41, 0x0D, 0x0A]);
    });
    test('concatArrays skips nulls and preserves order', () => {
        const out = CORE.util.concatArrays(new Uint8Array([1, 2]), null, new Uint8Array([3]), undefined, new Uint8Array([4, 5]));
        assert.deepEqual([...out], [1, 2, 3, 4, 5]);
    });
    test('storageKey naming', () => {
        assert.equal(CORE.util.storageKey('invoice'), 'ecleanbt.invoice');
    });
});

describe('raster: rgbaToMono', () => {
    function px(r, g, b, a) { return [r, g, b, a]; }
    test('threshold + bit order (x=0 -> 0x80)', () => {
        // 8 pixels: first black, rest white
        const data = new Uint8ClampedArray([0, 0, 0, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255, 255]);
        const mono = CORE.raster.rgbaToMono(data, 8, 1, 128);
        assert.equal(mono[0], 0x80);
    });
    test('width not a multiple of 8 uses full row bytes', () => {
        const data = new Uint8ClampedArray(20 * 1 * 4).fill(0); // all transparent -> all set? alpha=0 < 128 -> zero bits
        const mono = CORE.raster.rgbaToMono(data, 20, 1, 128);
        assert.equal(mono.length, 3);
        assert.equal(mono[0], 0x00);
    });
    test('fully opaque dark pixels set bits across bytes', () => {
        const data = new Uint8ClampedArray(16 * 1 * 4).fill(0);
        for (let i = 0; i < 16; i++) { data[i * 4 + 3] = 255; }
        const mono = CORE.raster.rgbaToMono(data, 16, 1, 128);
        assert.deepEqual([...mono], [0xff, 0xff]);
    });
    test('height preserved across rows', () => {
        const data = new Uint8ClampedArray(8 * 3 * 4).fill(255); // all white opaque
        const mono = CORE.raster.rgbaToMono(data, 8, 3, 128);
        assert.equal(mono.length, 3);
        assert.deepEqual([...mono], [0, 0, 0]);
    });
});

describe('raster: ESC/POS band raster (GS v 0)', () => {
    test('single band for <=128 rows', () => {
        const width = 384, rows = 100;
        const mono = new Uint8Array(Math.ceil(width / 8) * rows).fill(0);
        const out = CORE.raster.escPosRaster(mono, width);
        const rowBytes = 48;
        assert.deepEqual([...out.slice(0, 8)], [GS, 0x76, 0x30, 0x00, rowBytes, 0x00, rows, 0x00]);
        assert.equal(out.length, 8 + rowBytes * rows);
    });
    test('two bands for 220 rows (128 + 92)', () => {
        const width = 384, rows = 220;
        const rowBytes = 48;
        const mono = new Uint8Array(rowBytes * rows);
        const out = CORE.raster.escPosRaster(mono, width);
        // first band header
        assert.deepEqual([...out.slice(0, 8)], [GS, 0x76, 0x30, 0x00, 0x30, 0x00, 0x80, 0x00]);
        // second band offset = 8 + 48*128
        const off = 8 + rowBytes * 128;
        assert.deepEqual([...out.slice(off, off + 8)], [GS, 0x76, 0x30, 0x00, 0x30, 0x00, 0x5C, 0x00]);
        assert.equal(out.length, off + 8 + rowBytes * 92);
    });
});

describe('raster: TSPL BITMAP', () => {
    test('header format BITMAP 0,0,bytesPerRow,height,0,', () => {
        const width = 384, height = 100;
        const rowBytes = 48;
        const mono = new Uint8Array(rowBytes * height);
        const out = CORE.raster.tsplBitmap(mono, width, height);
        const header = Buffer.from(out.slice(0, 20)).toString('latin1');
        assert.match(header, /^BITMAP 0,0,48,100,0,\r?\n?$/);
        assert.equal(out.length, header.length + rowBytes * height + 2);
    });
    test('rejects undersized input', () => {
        assert.throws(() => CORE.raster.tsplBitmap(new Uint8Array(10), 384, 100));
    });
});

describe('job builders', () => {
    function monoTest(width, height) {
        return new Uint8Array(Math.ceil(width / 8) * height);
    }
    test('TSPL job: SIZE/GAP/DENSITY/CLS/BITMAP/PRINT, no cut command', () => {
        const job = CORE.raster.buildJob(monoTest(384, 120), 384, 120, { profile: 'rpp320-tspl' });
        const text = Buffer.from(job.slice(0, 64)).toString('latin1');
        assert.match(text, /^SIZE 80\.0 mm,/);
        assert.ok(text.includes('GAP 0 mm,0 mm'));
        assert.ok(text.includes('DENSITY 8'));
        assert.ok(text.includes('CLS'));
        assert.ok(!job.includes(0x1D) || ![...job].some((b, i) => b === 0x1D && job[i + 1] === 0x56), 'no GS V cut');
        const tail = Buffer.from(job.slice(-16)).toString('latin1');
        assert.match(tail, /PRINT 1,1\r\n$/);
    });
    test('ESC/POS job: ESC @, ESC a 1, GS v 0, feed 3, NO cut (GS V)', () => {
        const job = CORE.raster.buildJob(monoTest(384, 100), 384, 100, { profile: 'generic-escpos' });
        assert.deepEqual([...job.slice(0, 2)], [ESC, 0x40]);
        assert.deepEqual([...job.slice(2, 5)], [ESC, 0x61, 0x01]);
        // no GS V (0x1D 0x56) anywhere
        for (let i = 0; i < job.length - 1; i++) {
            assert.notDeepEqual([job[i], job[i + 1]], [GS, 0x56], 'ESC/POS cut command leaked into job');
        }
        // ends with feed 3 lines
        assert.deepEqual([...job.slice(-3)], [ESC, 0x64, 0x03]);
    });
    test('ESC/POS copies=2 duplicates the job', () => {
        const j1 = CORE.raster.buildJob(monoTest(384, 100), 384, 100, { profile: 'generic-escpos', copies: 1 });
        const j2 = CORE.raster.buildJob(monoTest(384, 100), 384, 100, { profile: 'generic-escpos', copies: 2 });
        assert.equal(j2.length, j1.length * 2);
        assert.deepEqual([...j2.slice(0, 5)], [...j1.slice(0, 5)]);
    });
    test('TSPL copies=2 -> PRINT 2,1', () => {
        const job = CORE.raster.buildJob(monoTest(384, 100), 384, 100, { profile: 'rpp320-tspl', copies: 2 });
        const tail = Buffer.from(job.slice(-16)).toString('latin1');
        assert.match(tail, /PRINT 2,1\r\n$/);
    });
    test('TSPL density override', () => {
        const job = CORE.raster.buildJob(monoTest(384, 100), 384, 100, { profile: 'rpp320-tspl', density: 12 });
        assert.ok(Buffer.from(job.slice(0, 128)).toString('latin1').includes('DENSITY 12'));
    });
    test('unknown profile falls back to rpp320-tspl', () => {
        const job = CORE.raster.buildJob(monoTest(384, 100), 384, 100, { profile: 'nope' });
        assert.match(Buffer.from(job.slice(0, 16)).toString('latin1'), /^SIZE 80\.0 mm,/);
    });
});

describe('stickers: expandItems', () => {
    test('quantity 3 -> 3 entries', () => {
        assert.equal(CORE.stickers.expandItems([{ name: 'قميص', quantity: 3 }]).length, 3);
    });
    test('mixed quantities', () => {
        assert.equal(CORE.stickers.expandItems([{ quantity: 2 }, { quantity: 1 }, { quantity: 4 }]).length, 7);
    });
    test('minimum 1 sticker for a line with 0/invalid qty', () => {
        assert.equal(CORE.stickers.expandItems([{ quantity: 0 }]).length, 1);
        assert.equal(CORE.stickers.expandItems([{ quantity: 'abc' }]).length, 1);
    });
    test('itemLabel joins service', () => {
        assert.equal(CORE.stickers.itemLabel({ name: 'بنطلون', service: 'كي' }), 'بنطلون — كي');
        assert.equal(CORE.stickers.itemLabel({ name: 'شامبو' }), 'شامبو');
        assert.equal(CORE.stickers.itemLabel({}), '');
    });
});

describe('jobs: serial queue + abort', () => {
    test('tasks run in order', async () => {
        const jobs = CORE.jobs.create();
        const order = [];
        await Promise.all([
            jobs.enqueue(async () => { await CORE.util.sleep(30); order.push('a'); return 'a'; }),
            jobs.enqueue(async () => { order.push('b'); return 'b'; }),
            jobs.enqueue(async () => { order.push('c'); return 'c'; }),
        ]);
        assert.deepEqual(order, ['a', 'b', 'c']);
    });
    test('isBusy toggles', async () => {
        const jobs = CORE.jobs.create();
        let busyDuring = null;
        const p = jobs.enqueue(async () => { await CORE.util.sleep(20); busyDuring = jobs.isBusy(); });
        await p;
        assert.equal(busyDuring, true);
        assert.equal(jobs.isBusy(), false);
    });
    test('abort sets flag; JOB_ABORTED thrown by writeChunked when aborted', async () => {
        const jobs = CORE.jobs.create();
        jobs.enqueue(async () => {});
        jobs.abort();
        assert.equal(jobs.isAborted(), true);
        // writeChunked with an always-aborted environment stops before sending
        const conn = {
            ensure: async () => true,
            state: { characteristic: { properties: { write: true }, writeValueWithResponse: async () => {} } },
        };
        await assert.rejects(
            CORE.jobs.writeChunked(conn, new Uint8Array(10), { profile: 'generic-escpos', isAborted: () => true }),
            /JOB_ABORTED/
        );
    });
});

describe('jobs: writeChunked chunking + reconnect', () => {
    function mockConn(behavior) {
        const calls = [];
        const conn = {
            ensureCalls: 0,
            writes: calls,
            state: {
                device: { name: 'MOCK-PRINTER', gatt: { connected: true } },
                characteristic: {
                    properties: { write: true },
                    writeValueWithResponse: async (chunk) => {
                        calls.push([...chunk]);
                        if (behavior && behavior.onWrite) return behavior.onWrite(calls.length - 1, conn);
                    },
                },
            },
            ensure: async () => {
                conn.ensureCalls++;
                if (behavior && behavior.onEnsure) return behavior.onEnsure(conn.ensureCalls, conn);
                return true;
            },
        };
        return conn;
    }
    test('chunking: 1000 bytes at 128B -> 8 chunks, reassembles to input', async () => {
        const data = new Uint8Array(1000).map((_, i) => i % 251);
        const conn = mockConn();
        await CORE.jobs.writeChunked(conn, data, { profile: 'generic-escpos' });
        assert.equal(conn.writes.length, 8);
        assert.equal(conn.writes[7].length, 104);
        const reassembled = new Uint8Array(conn.writes.flat());
        assert.deepEqual([...reassembled], [...data]);
    });
    test('progress is monotonically increasing to 100', async () => {
        const data = new Uint8Array(300);
        const conn = mockConn();
        const ticks = [];
        await CORE.jobs.writeChunked(conn, data, { profile: 'generic-escpos', onProgress: (p) => ticks.push(p) });
        assert.equal(ticks[0], 0);
        assert.equal(ticks[ticks.length - 1], 100);
        for (let i = 1; i < ticks.length; i++) assert.ok(ticks[i] >= ticks[i - 1]);
    });
    test('auto reconnect: ensure fails first two times then succeeds', async () => {
        const data = new Uint8Array(20);
        const conn = mockConn({
            onEnsure: (n) => n > 2, // fail, fail, then succeed
        });
        const statuses = [];
        await CORE.jobs.writeChunked(conn, data, { profile: 'generic-escpos', onStatus: (m, t) => statuses.push(t) });
        assert.ok(conn.ensureCalls >= 3);
        assert.ok(statuses.includes('warning'));
    });
    test('throws when printer never becomes ready (max attempts)', async () => {
        const data = new Uint8Array(10);
        const conn = mockConn({ onEnsure: () => false });
        await assert.rejects(
            CORE.jobs.writeChunked(conn, data, { profile: 'generic-escpos' }),
            /انقطع الاتصال/
        );
    });
});

describe('connection: GATT discovery', () => {
    function mockDevice(servicesSpec) {
        const specs = servicesSpec || [];
        return {
            name: 'MOCK',
            gatt: {
                connected: true,
                getPrimaryService: async (uuid) => {
                    const found = specs.find(s => String(s.uuid) === String(uuid));
                    if (!found) throw new Error('service not found');
                    return {
                        getCharacteristic: async (chUuid) => {
                            const ch = found.characteristics.find(c => String(c.uuid) === String(chUuid) || c.numeric === chUuid);
                            if (!ch) throw new Error('char not found');
                            return ch.obj;
                        },
                        getCharacteristics: async () => found.characteristics.map(c => c.obj),
                    };
                },
                getPrimaryServices: async () => specs.map(s => ({
                    getCharacteristics: async () => s.characteristics.map(c => c.obj),
                })),
            },
        };
    }
    function writableChar() {
        const writes = [];
        return {
            obj: {
                properties: { write: true },
                writes,
                writeValueWithResponse: async (chunk) => { writes.push([...chunk]); },
            },
        };
    }
    const NORDIC = '6e400001-b5a3-f393-e0a9-e50e24dcca9e';
    const NORDIC_WRITE = '6e400002-b5a3-f393-e0a9-e50e24dcca9e';

    test('discovers Nordic UART when present', async () => {
        const ch = writableChar();
        const server = mockDevice([{ uuid: NORDIC, characteristics: [{ uuid: NORDIC_WRITE, obj: ch.obj }] }]).gatt;
        const found = await CORE.connection.discoverWritableCharacteristic(server, null);
        assert.equal(found, ch.obj);
    });
    test('falls back to full sweep when no known service matches', async () => {
        const ch = writableChar();
        const server = mockDevice([{ uuid: 'deadbeef-0000-0000-0000-000000000000', characteristics: [{ uuid: '0000ff02-0000-1000-8000-00805f9b34fb', obj: ch.obj }] }]).gatt;
        const found = await CORE.connection.discoverWritableCharacteristic(server, null);
        assert.equal(found, ch.obj);
    });
    test('returns null when nothing is writable', async () => {
        const server = mockDevice([{ uuid: NORDIC, characteristics: [{ uuid: NORDIC_WRITE, obj: { properties: { read: true } } }] }]).gatt;
        const found = await CORE.connection.discoverWritableCharacteristic(server, null);
        assert.equal(found, null);
    });
    test('ESC/POS probe writes DLE EOT when probing', async () => {
        const ch = writableChar();
        const server = mockDevice([{ uuid: NORDIC, characteristics: [{ uuid: NORDIC_WRITE, obj: ch.obj }] }]).gatt;
        const probe = new Uint8Array([0x10, 0x04, 0x01]);
        const found = await CORE.connection.discoverWritableCharacteristic(server, probe);
        assert.equal(found, ch.obj);
        assert.deepEqual(ch.obj.writes[0], [0x10, 0x04, 0x01]);
    });
    test('TSPL (probe=null) accepts writable characteristic without probing', async () => {
        const ch = writableChar();
        const server = mockDevice([{ uuid: NORDIC, characteristics: [{ uuid: NORDIC_WRITE, obj: ch.obj }] }]).gatt;
        const found = await CORE.connection.discoverWritableCharacteristic(server, null);
        assert.equal(found, ch.obj);
        assert.equal(ch.obj.writes.length, 0, 'no probe should be written for TSPL');
    });
});

describe('errors: Arabic mapping', () => {
    const f = CORE.errors.formatArabicError;
    test('NO_BLUETOOTH', () => assert.match(f('NO_BLUETOOTH'), /Web Bluetooth/));
    test('NotFoundError -> cancelled', () => assert.match(f({ name: 'NotFoundError', message: 'User cancelled' }), /إلغاء/));
    test('SecurityError -> HTTPS', () => assert.match(f({ name: 'SecurityError', message: 'https required' }), /HTTPS/));
    test('NetworkError -> retry', () => assert.match(f({ name: 'NetworkError', message: 'gatt fail' }), /تعذر الاتصال/));
    test('CHANNEL_FAIL', () => assert.match(f('CHANNEL_FAIL'), /قناة كتابة/));
    test('JOB_ABORTED', () => assert.match(f('JOB_ABORTED'), /إلغاء/));
    test('unknown passes through', () => assert.equal(f('weird custom error'), 'weird custom error'));
});

describe('invoice helpers', () => {
    test('copyLabel maps copies (ar)', () => {
        assert.equal(INVOICE.copyLabel('customer', 'ar'), 'نسخة الزبون');
        assert.equal(INVOICE.copyLabel('store', 'ar'), 'نسخة المحل');
        assert.equal(INVOICE.copyLabel('normal', 'ar'), '');
    });
    test('formatMoney', () => {
        assert.equal(INVOICE.formatMoney(12.5, '₪'), '12.50 ₪');
        assert.equal(INVOICE.formatMoney(null, '₪'), '—');
        assert.equal(INVOICE.formatMoney('abc', '₪'), '—');
    });
    test('barcodeBits: digits only, deterministic', () => {
        assert.match(INVOICE.barcodeBits('123'), /^[01]+$/);
        assert.equal(INVOICE.barcodeBits('abc'), null);
        assert.equal(INVOICE.barcodeBits(''), null);
    });
    test('wrapText wraps to width with stub ctx', () => {
        const ctx = { font: '', measureText: (s) => ({ width: String(s).length * 10 }) };
        const lines = INVOICE.wrapText(ctx, 'aaaa bbbb cccc dddd eeee ffff', 120, 'Cairo', 10, '');
        assert.ok(lines.length >= 2);
        for (const ln of lines) assert.ok(String(ln).length * 10 <= 120 || String(ln).split(' ').length === 1);
    });
    test('walk through modules loads cleanly', () => {
        assert.ok(typeof UI.mount === 'function');
        assert.ok(typeof STICKERS.drawSticker === 'function');
    });
});

describe('engine load + profile list', () => {
    test('module loads; 2 profiles; version set', () => {
        assert.equal(CORE.version, '1.0.0');
        const ids = CORE.profiles.list().map(p => p.id).sort();
        assert.deepEqual(ids, ['generic-escpos', 'rpp320-tspl']);
    });
    test('getProfile falls back for unknown', () => {
        assert.equal(CORE.profiles.get('unknown').id, 'rpp320-tspl');
    });
});
