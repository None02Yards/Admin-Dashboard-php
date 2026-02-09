# Simple Voting (PHP MVC)

Minimal PHP MVC voting app with role-based auth (admin/voter), admin CRUD, and helper scripts.

Quick start (local)
- Edit DB in `config/config.php` or set env vars DB_HOST/DB_NAME/DB_USER/DB_PASS.
- Apply schema: `./scripts/db/migrate.sh`
- Seed demo data: `./scripts/db/seed.sh`
- Create admin: `./scripts/make-admin.sh`
- Start dev server:
  - Unix: `./scripts/dev-server.sh 127.0.0.1 8000`
  - Windows PowerShell: `php -S 127.0.0.1:8000 -t . router.php`
- Open: http://127.0.0.1:8000

Important files
- Front controller: `index.php` (project root)
- Dev router: `router.php` (project root) — required for built-in server
- Schema: `sql/schema.sql`
- Config: `config/config.php`
- Scripts: `scripts/` and `scripts/db/` (migrate, seed, backup, restore, reset, make-admin, etc.)

Useful routes
- Login: `/auth/login`
- Vote UI: `/` (voter)
- Admin dashboard: `/admin/dashboard`
- Listing APIs (JSON): `/listing/api/positions`, `/listing/api/candidates`, `/listing/api/users`

Notes
- Scripts are for development only. Destructive scripts require confirmation.
- Docker files are optional; you can ignore them if you don't use Docker.