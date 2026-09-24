---
name: Admin preview authentication
description: Constraints for Laravel admin login when the app is viewed through a Replit preview proxy.
---

Admin login forms, redirects, and core static assets should use relative paths rather than absolute URLs derived from a local APP_URL. The preview proxy can serve the initial page on one host and send a form or asset request to another, which breaks the session/CSRF flow or removes the original styling.

**Why:** Imported Laravel projects often keep APP_URL pointed at 127.0.0.1, while the browser preview uses a forwarded host. Relative paths preserve the same host, session, CSS, and JavaScript loading.

**How to apply:** For auth pages, core redirects, and the main admin layout, prefer `/login`, `/dashboard`, `/web/css/...`, and `/web/js/...` or Laravel route generation with absolute=false. When demo credentials are shown in the UI, verify their exact spelling against the configured value before debugging authentication.