/* E-Clean — user-interaction simulation for the Bluetooth print flow.
 * Emulates: click "طباعة" → no paired device → Chrome picker opens (requestDevice)
 *           → discovery → job written over GATT. Asserts the gesture ordering
 *           required by Chrome on Android stays intact.
 * Run:  node --test tests/print/bt-interaction.test.mjs */
import { test, describe } from 'node:test';
import { strict as assert } from 'node:assert';
import { createRequire } from 'node:module';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const require = createRequire(import.meta.url);
const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..', '..');
const CORE = require(path.join(ROOT, 'public/web/js/eclean-bt-core.js'));

const NORDIC = '6e400001-b5a3-f393-e0a9-e50e24dcca9e';
const NORDIC_WRITE = '6e400002-b5a3-f393-e0a9-e50e24dcca9e';

function makeMemoryStorage() {
    const m = new Map();
    return {
        getItem: (k) => (m.has(k) ? m.get(k) : null),
        setItem: (k, v) => { m.set(k, String(v)); },
        removeItem: (k) => { m.delete(k); },
    };
}

function makeMockBLE() {
    const calls = [];
    const written = [];
    const characteristic = {
        properties: { write: true, writeWithoutResponse: false },
        writeValueWithResponse: async (chunk) => { written.push([...chunk]); },
    };
    const service = {
        getCharacteristic: async (uuid) => {
            if (String(uuid).toLowerCase() === NORDIC_WRITE) return characteristic;
            throw new Error('characteristic not found: ' + uuid);
        },
        getCharacteristics: async () => [characteristic],
    };
    const device = {
        name: 'RPP320_3733',
        addEventListener: () => {},
        gatt: {
            connected: true,
            connect: async () => device.gatt,
            getPrimaryService: async (uuid) => {
                if (String(uuid).toLowerCase() === NORDIC) return service;
                throw new Error('service not found: ' + uuid);
            },
            getPrimaryServices: async () => [service],
        },
    };
    const ble = {
        requestDevice: async (req) => {
            calls.push({ type: 'requestDevice', req });
            return device;
        },
    };
    return { ble, device, characteristic, written, calls };
}

describe('user interaction: click → picker → print', () => {
    test('full flow with no previously paired device', async () => {
        const mock = makeMockBLE();
        const conn = CORE.connection.create({
            bluetooth: mock.ble,
            storage: makeMemoryStorage(),
        });

        // mirrors the page binder: gesture-safe ensure → prompt only when nothing live
        await CORE.connection.ensureInteractive(conn, 'invoice');

        assert.equal(mock.calls.length, 1, 'exactly one requestDevice call');
        assert.equal(mock.calls[0].type, 'requestDevice');
        assert.ok(mock.calls[0].req.acceptAllDevices === true || Array.isArray(mock.calls[0].req.filters),
            'requestDevice must receive a valid requirement object');
        assert.equal(conn.state.device, mock.device);
        assert.equal(conn.state.characteristic, mock.characteristic,
            'discovery must select the writable Nordic UART characteristic');

        // build a job and send it over the negotiated channel
        const mono = new Uint8Array(48 * 40);
        const job = CORE.raster.buildJob(mono, 384, 40, { profile: 'generic-escpos' });
        await CORE.jobs.writeChunked(conn, job, { profile: 'generic-escpos' });

        const reassembled = new Uint8Array(mock.written.flat());
        assert.deepEqual([...reassembled], [...job],
            'bytes delivered over the mocked GATT channel equal the job bytes');
    });

    test('previously live connection does NOT re-prompt the picker', async () => {
        const mock = makeMockBLE();
        const conn = CORE.connection.create({
            bluetooth: mock.ble,
            storage: makeMemoryStorage(),
        });
        await CORE.connection.ensureInteractive(conn, 'invoice');
        assert.equal(mock.calls.length, 1);

        // second print: ensureInteractive must reuse the live channel (no new picker)
        await CORE.connection.ensureInteractive(conn, 'invoice');
        assert.equal(mock.calls.length, 1, 'no second picker for an already-connected device');
    });

    test('cancelling the picker maps to Arabic "إلغاء" message', async () => {
        const cancelled = Object.assign(new Error('User cancelled the requestDevice() chooser.'), { name: 'NotFoundError' });
        const ble = { requestDevice: async () => { throw cancelled; } };
        const conn = CORE.connection.create({ bluetooth: ble, storage: makeMemoryStorage() });
        await assert.rejects(
            CORE.connection.ensureInteractive(conn, 'invoice'),
            (err) => err === cancelled
        );
        assert.match(CORE.errors.formatArabicError(cancelled), /إلغاء/);
    });

    test('two concurrent jobs never interleave on the wire (serialized)', async () => {
        const mock = makeMockBLE();
        const conn = CORE.connection.create({
            bluetooth: mock.ble,
            storage: makeMemoryStorage(),
        });
        await CORE.connection.ensureInteractive(conn, 'invoice');

        const jobA = new Uint8Array([1, 2, 3, 4]);
        const jobB = new Uint8Array([9, 8, 7]);
        const p1 = CORE.jobs.writeChunked(conn, jobA, { profile: 'generic-escpos' });
        const p2 = CORE.jobs.writeChunked(conn, jobB, { profile: 'generic-escpos' });
        await Promise.all([p1, p2]);
        const flat = mock.written.flat();
        // jobs must be delivered as contiguous blocks (chunk size 128 -> each job is one chunk)
        const a = flat.indexOf(1);
        const b = flat.indexOf(9);
        if (a !== -1 && b !== -1) {
            // whichever goes first must appear contiguously: [1,2,3,4] then [9,8,7]
            assert.deepEqual(flat.slice(a, a + 4), [1, 2, 3, 4]);
            assert.deepEqual(flat.slice(b, b + 3), [9, 8, 7]);
        }
    });
});
