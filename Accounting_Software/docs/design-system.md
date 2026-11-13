# Elite Clean ERP — Design System (Clean Light)

Canonical source: `public/assets/css/ec-theme.css` (loaded by every layout with a `?v=` cache-bust token).
Brand identity: **Elite Clean teal + warm neutrals**. No navy, no purple. Do not reintroduce Sneat's `#696cff` palette.

## Direction & foundations
- Pattern: **Trust & Authority** (enterprise ERP, Arabic RTL first).
- Style: **Accessible & Ethical** — high contrast, visible focus, reduced motion, 44px touch targets.
- Typography: **Cairo** variable font (local, `assets/vendor/fonts/Cairo/`), RTL-first, `lang/dir` = `ar / rtl`.

## Color tokens
| Token | Value | Role / contrast |
|---|---|---|
| `--ec-primary` | `#0f766e` (teal-700) | CTA background — 5.47:1 on white |
| `--ec-primary-hover` | `#115e59` (teal-800) | hover (7.58:1) |
| `--ec-primary-active` | `#134e4a` (teal-900) | pressed |
| `--ec-primary-strong` | `#0d9488` (teal-600) | icons / chart lines / accents |
| `--ec-primary-soft` · `--ec-primary-soft-2` | `#ccfbf1` · `#f0fdfa` | chips, soft fills |
| `--ec-ink` | `#262626` | headings (near-black, warm) |
| `--ec-body` | `#404040` | body text — 10.4:1 on white |
| `--ec-muted` | `#6b7280` | secondary text — 4.5:1 (AA) |
| `--ec-faint` | `#a3a3a3` | hints only — never body text |
| `--ec-bg` · `--ec-surface` | `#f7f7f7` · `#ffffff` | app bg · cards |
| success / warning / danger / info + softs | `--ec-success #15803d` · `--ec-warning #b45309` · `--ec-danger #dc2626` · `--ec-info #1d4ed8` | semantic states |

## Radius · spacing · elevation
- Radius: `--ec-radius-xs 6` · `sm 9` · `md 12` · `--ec-radius 16` · `lg 20` · `pill 999`.
- Spacing scale: 4 / 8 base — `--ec-space-1..12` (.25rem → 3rem). Use this rhythm; avoid ad-hoc values.
- Elevation: `--ec-shadow-sm` (cards) · `--ec-shadow` (popovers/hover) · `--ec-shadow-lg` (strong hover/drawer).
- Motion: `--ec-transition` (160ms) · `--ec-transition-slow` (280ms) · both collapse to 0 under `prefers-reduced-motion`.

## Components
- **Buttons** (`.btn`, `.btn-primary`, outline variants): teal gradient AA, radius-sm, `:active` scale .99, focus-visible ring. Disabled = reduced opacity + no shadow (Bootstrap `disabled`).
- **Cards** (`.ec-card`, `.clickable`): white surface, `--ec-radius`, `--ec-shadow-sm`, hover lift.
- **Tables** (`.table`): tabular-nums everywhere, teal hover, `--ec-line-soft` borders. Wrap in `.table-responsive`.
- **Forms**: Cairo labels `fw-semibold`, `--ec-ring` focus ring, `.is-invalid` + `<x-input-error>` for validation.
- **Badges/chips**: pill radius, soft background + strong foreground (all pairs ≥ 4.5).
- **Overview KPI cards** (`.ec-kpi` / `.ec-kpi-link`): whole card is the link — hover lift, `aria-label` required.
- **Empty states** (`.ec-empty-icon`): dashed border, muted icon, single CTA.

## Accessibility (WCAG 2.2 AA)
- Contrast: body ≥ 4.5:1, large text/UI glyphs ≥ 3:1 (all brand pairs verified; recalc when adding colors).
- Focus: visible 2px outline + 3px soft ring on **every** interactive element (global `:focus-visible` rule).
- Keyboard: skip link «الانتقال إلى المحتوى» (`.ec-skip-link`) targeting `#main-content` in every layout.
- RTL: logical properties everywhere (`inset-inline-*`, `margin-inline-*`), `dir="rtl"` from `config('app.dir')`.
- Reduced motion: global `@media (prefers-reduced-motion: reduce)` collapse.
- Semantics: icons decorative → `aria-hidden="true"`; icon buttons carry `aria-label`.

## Icons
Boxicons (`bx-*`) used throughout; keep stroke/fill consistency per hierarchy level. No emoji as structural icons (removed from login heading).

## Adding a page
1. Copy an existing section (`ec-page-header` + `ec-card`) — never start from a blank `<h4>`.
2. Use tokens only (no hex in Blade); keep RTL logical props; mirror pair colors from the semantic ramp.
3. Verify: `php artisan view:cache` + kernel smoke test of the route (200) + keyboard-only pass.
4. Bump `?v=ecN` in layouts when `ec-theme.css` changes; hard-refresh (Ctrl+Shift+R).
