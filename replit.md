# Laundry Admin

## Run locally on Replit

The active workflow is **Laundry Admin** and serves the Laravel application from:

`Laundry-AdminPanel-SourceCode/install/install`

It runs on port `5000` with PHP 8.4 and a local SQLite database at:

`Laundry-AdminPanel-SourceCode/install/install/database/database.sqlite`

The database schema is initialized with Laravel migrations and the attached MariaDB export
is imported into the local SQLite copy. The demo login configured for this environment is:

- Email: `root@readyecommerce.com`
- Password: `secret`

The login page is available at `/login`. The workflow intentionally unsets Replit's shared
`DATABASE_URL` so this imported copy stays on its local SQLite database.

The customer Flutter app is built for web with Flutter 3.41.6 and served by the
**Laundry Customer App** workflow on port `5001` from:

`Best-Laundry-Customer-App-SDK-3.41.6/build/web`

Its current API default remains the URL configured in the Flutter source. The local admin
workflow and the customer app are separate services.

## Important scope

The separate `laundry-sys` directory is intentionally not used or modified by this setup.