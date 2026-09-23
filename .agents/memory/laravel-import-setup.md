---
name: Imported Laravel setup
description: Durable setup constraints for Laravel packages imported as installer archives.
---

Imported Laravel installer archives may contain controllers and views but omit the application's web route file. Check route completeness before assuming the admin UI is available.

**Why:** An imported package can boot into its installer while the actual admin navigation is unreachable because only installer routes are registered.

**How to apply:** Inspect the archive's routes directory and route provider early. Restore the intended route map before treating the application as fully functional; use only a minimal login/dashboard route map when the requested scope is initial startup.

Replit projects can expose a shared `DATABASE_URL` process variable that overrides `.env` values through Laravel's connection `url` setting, even when `DB_CONNECTION=sqlite`.

**Why:** A local setup intended for an extracted copy can accidentally resolve to a PostgreSQL connection if the framework honors the ambient URL.

**How to apply:** For isolated local SQLite runs, explicitly ignore the ambient URL for the SQLite connection and unset it in the workflow command. Never run destructive database commands until the resolved driver is verified.