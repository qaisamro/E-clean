# Elite Clean — Direct Web Bluetooth printing

## Overview
Unified, dependency-free Bluetooth/BLE print engine for both Laravel apps. The whole flow runs in Chrome on Android over **Web Bluetooth (GATT)** — no apps, no print services, no server round-trips. Buttons only prompt the system picker from a direct user gesture; there is an `ensureInteractive()` fast path so `requestDevice()` always fires inside the click handler.

## Shared files (byte-identical in both apps)
| file | role |
|---|---|
| `public/web/js/eclean-bt-core.js` | UMD engine: GATT discovery over 13 known services + full sweep, TSPL/ESC-POS profiles, raster (`GS v 0` / `BITMAP`), serial job queue with abort + **per-channel write mutex**, Arabic errors, localStorage prefs |
| `public/web/js/eclean-bt-invoice.js` | Receipt canvas renderer (RTL, Cairo, barcode Code 39, copy ribbon) + `printInvoice()` |
| `public/web/js/eclean-bt-stickers.js` | Piece-sticker canvas renderer (quantity-expanded) + `printStickers()` |
| `public/web/js/eclean-bt-ui.js` | Connection panel: profile picker (TSPL/ESC-POS), connect / reconnect / test / diagnostics / forget |
| `tests/print/bt-print.test.mjs` | Node engine tests — **47 assertions** |
| `tests/print/bt-interaction.test.mjs` | Node user-flow tests — **4 assertions** (click → picker → GATT write → serialization) |

Run both: `node --test tests/print/bt-print.test.mjs tests/print/bt-interaction.test.mjs` → **51/51 pass**.

## Printer profiles
| profile | paper | protocol | notes |
|---|---|---|---|
| `rpp320-tspl` | 80 mm continuous @ 203 dpi (576 px) | **TSPL** | `SIZE (roll width) / GAP 0,0 / DENSITY 8 / DIRECTION 0 / CLS / BITMAP / PRINT 1,1` — no cutter command |
| `generic-escpos` | 80 mm continuous @ 203 dpi (576 px) | **ESC/POS** | `ESC @`, `ESC a 1`, banded `GS v 0`, feed 3 lines, no cut |

TSPL specifics: `SIZE` carries the **physical roll width** (80 mm), height = image height rounded to 0.1 mm; `GAP 0 mm,0 mm` for continuous paper; `BITMAP x,y,bytesPerRow,height,mode,data` over raw binary; commands terminated with `\r\n`.

Quantity-aware stickers: `EcleanBT.stickers.expandItems()` expands every line by its quantity (min 1); preview list and print list both render the expanded set.

## Verified / not verified on hardware
- **Software-proven**: 51 Node assertions (raster, chunking, discovery with mocked GATT, reconnect, queue/abort/mutex, expandItems, copy labels, barcode, profile fallback, gesture-ordering flow).
- **Not yet proven**: real print on RPP320 or the label printer. GATT availability, service UUIDs, TSPL vs ESC/POS mode, `DLE EOT` behavior, Arabic shaping on the physical printer **must** be verified on-device.

## On-device verification checklist
1. Android Chrome (HTTPS) → `/orders/{id}` → Bluetooth panel shows «اتصال».
2. Tap «اتصال» → the system picker appears (only from your tap) → pick the printer.
3. «طباعة تجريبية» prints a normal-copy invoice; «تشخيص» prints a calibration sheet.
4. If TSPL prints garbage on a different printer → switch the profile to **ESC/POS عام** in the panel (saved per browser role) and retry.
5. If no device appears / «تعذر العثور على قناة كتابة» → the printer is **Classic SPP only** and cannot be driven from Chrome; it needs the vendor's print service (out of scope).
6. Stickers: a line with qty 3 prints 3 stickers in preview and on paper.
7. Continuous-paper orientation: if printing is mirrored/inverted, the fix belongs in profile (`DIRECTION`) after physical verification, not in blind code edits.

## Permissions
- ERP: `orders.view` covers order show / invoice / stickers pages; ERP print surfaces follow the page permission model.
- Web_Admin: invoice buttons + BT panel inherit `root|admin|vendor_admin` or `vendor_sub && can(order.pos-invoice)`; stickers BT panel + print button + PDF link require the same `order.print.labels` guard as the labels PDF.

## Operational notes
- Preferences saved per browser role (`ecleanbt.invoice`, `ecleanbt.label`) via localStorage + `getDevices()` where the browser allows.
- `window.print()` / PDF remain as separate fallback paths («PDF» buttons) — never presented as Bluetooth.
- Success only after the whole byte stream is accepted by the channel; errors map to precise Arabic messages.
- All print buttons are double-click safe (`printing` flag) and the engine serializes writes per channel.
