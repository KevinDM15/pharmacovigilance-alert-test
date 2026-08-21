# Pharmacovigilance Alert System

Module for a compounding pharmacy to identify and notify customers who purchased a medication associated with a specific lot number, within a configurable date range.

Built for the LifeFile Development Test: PHP/Laravel API, Vue 3 SPA, MySQL, RESTful endpoints.

## Stack

- **Backend:** PHP 8.4, Laravel 13, Sanctum (token auth)
- **Frontend:** Vue 3 SPA, Vue Router, Axios, Tailwind CSS v4
- **Database:** MySQL 8.0
- **Containers:** Docker / Podman (multi-stage build)

## Quick start (Docker)

The fastest way to run the full application — backend, frontend, and database — with nothing installed locally except a container runtime.

```bash
# 1. Generate an APP_KEY
export APP_KEY="base64:$(openssl rand -base64 32)"

# 2. Build and start everything
docker compose up --build
# or, with Podman:
podman-compose up --build
```

The container runs migrations and seeds demo data automatically on first boot (idempotent, safe to restart). Once it's up:

- App: http://localhost:8000/pharmacovigilance/login
- Login: `admin` / `password`
- MySQL: exposed on `localhost:3306` (user `pharmacovigilance` / password `secret`) if you want to inspect the database with an external client

To use different credentials instead of the defaults, export `DB_PASSWORD`, `DB_ROOT_PASSWORD`, and/or `SEED_ADMIN_PASSWORD` before running `docker compose up`.

## Local setup (without Docker)

Requires PHP 8.3+, Composer, Node 20+, pnpm, and a MySQL 8 instance reachable from your machine.

```bash
composer install
pnpm install

cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database connection (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`). If you don't have MySQL installed locally, you can run just the database in a container:

```bash
podman run -d --name pharmacovigilance-mysql \
  -e MYSQL_ROOT_PASSWORD=root \
  -e MYSQL_DATABASE=pharmacovigilance \
  -e MYSQL_USER=pharmacovigilance \
  -e MYSQL_PASSWORD=secret \
  -p 3306:3306 \
  docker.io/library/mysql:8.0
```

Then:

```bash
php artisan migrate --seed
pnpm build

php artisan serve
```

Visit `http://localhost:8000/pharmacovigilance/login` and sign in with `admin` / `password`.

For frontend development with hot reload, run `pnpm dev` in a separate terminal instead of `pnpm build`.

### Sending real alert emails

By default `MAIL_MAILER=log`, so alert emails are written to `storage/logs/laravel.log` instead of being sent. To see them delivered, point `MAIL_*` at a real SMTP provider (or a local catcher like Mailpit) in `.env`.

Alert emails are queued (`QUEUE_CONNECTION=database`). Run a worker to process them:

```bash
php artisan queue:work
```

## Demo data

The seeder creates:

- An admin user (`admin` / `password`, configurable via `SEED_ADMIN_USERNAME`/`SEED_ADMIN_PASSWORD`)
- A medication with **lot number `951357`** (the test scenario's recalled lot)
- 8 customers with orders for that lot 5 within the last 30 days, 3 outside that range, so the date filter has something real to filter
- Unrelated medications and orders as noise, so search results aren't "the entire database"

Search for lot `951357` (pre-filled in the search field) to see the scenario in action.

## API overview

All endpoints except `/api/login` require a Sanctum bearer token (`Authorization: Bearer <token>`).

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/login` | Authenticate, returns a token |
| POST | `/api/logout` | Revoke the current token |
| GET | `/api/medications/search?lot=` | Confirm a lot exists in the catalog |
| GET | `/api/orders?lot=&start_date=&end_date=` | List orders for a lot, paginated |
| GET | `/api/orders/export?lot=&start_date=&end_date=` | Download matching orders as CSV |
| GET | `/api/orders/{id}` | Order detail |
| GET | `/api/customers/{id}` | Customer detail |
| POST | `/api/alerts/send` | Send a recall alert email (single or bulk via `order_ids`) |

## Design decisions

- **Data model:** a medication's lot number lives on the `medications` table itself, not in a separate inventory table, one medication record represents one production lot.
- **Authentication:** Sanctum with bearer tokens, not session cookies. Login is by `username`, not email. Tokens expire after 8 hours; login is rate-limited to 5 attempts per minute.
- **Search architecture:** two separate endpoints — one confirms a lot exists in the catalog, the other returns the actual matching orders. The frontend only calls the second, since order data already carries what the table needs.
- **Authorization:** authentication only, no distinct roles or ownership relationship between staff and customers/orders — any signed-in user can see and act on any record, since the whole point of the panel is tracing buyers of a recalled lot across the entire customer base.
- **Alerts:** a single endpoint handles both individual and bulk sends (it takes a list of orders). Emails are queued instead of sent synchronously, so a bulk alert doesn't block the response. Each send is validated against the order actually containing the relevant lot before anything goes out.
- **CSV export** downloads the full filtered result, not just the page currently on screen. Fields are sanitized against spreadsheet formula injection.
- **Frontend:** SPA with Vue Router, simple shared state (no external state library), toast notifications anchored to a fixed corner, and search filters mirrored in the URL so results survive back-navigation.
- **Infrastructure:** containers for both the database and the full application, with idempotent startup (won't duplicate demo data on restart) and credentials configurable via environment variables instead of hardcoded.

## Bonus features implemented

- Bulk alerting (select multiple orders, send one recall email each)
- CSV export of search results
- Docker / Podman setup for the full stack (not just the database)
- Alert audit log (`alerts` table: who sent it, to whom, when)

## Git workflow

This repo follows a simplified Git Flow: `main` (stable) + `develop` (integration) + one `feature/*` branch per module, merged via pull request. See the closed PRs for the history of how each feature was built and reviewed.
