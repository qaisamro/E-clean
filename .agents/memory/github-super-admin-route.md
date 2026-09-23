---
name: GitHub super admin route
description: Location and route of the super-admin screen in the imported E-clean Laravel project.
---

The GitHub project does not have a separate Super-admin folder. Its super-admin management screen is the `/admins` route, backed by the root AdminController and the resources/views/root/admin views.

**Why:** The current working copy had replaced the original named route with an admin-placeholder route, so the sidebar link opened Dashboard instead of the super-admin screen.

**How to apply:** Keep `/admins` mapped to the root admin controller when restoring or testing the GitHub admin panel; do not interpret the presence of root/admin view files as a separate application.