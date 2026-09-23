---
name: Flutter web preview cache
description: Replit preview caching behavior for rebuilt Flutter web entrypoints
---

When a rebuilt Flutter web preview reports code that is absent from the current generated bundle, treat the report as a stale service-worker or browser entrypoint before changing application logic. Unregister existing service workers, disable service-worker bootstrapping for the static preview, and use a fresh physical entrypoint filename when a query-string cache bust is insufficient. If the preview has no WebGL, keep the lightweight API-backed HTML fallback in the web entrypoint so public customer data remains visible.

**Why:** The preview can continue reporting a removed plugin call while the served current bundle contains no reference to that plugin, making repeated source changes misleading.

**How to apply:** Verify the exact served bundle with `curl`/string search and compare the browser stack's script filename before deciding the runtime fix failed. Test both WebGL and no-WebGL paths after changing the bootstrap; the no-WebGL HTML fallback is a real visible customer surface and must stay aligned with the dynamic API sections.