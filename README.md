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

The container runs migrations and seeds demo data automatically on first boot (idempotent — safe to restart). Once it's up:

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
- 8 customers with orders for that lot — 5 within the last 30 days, 3 outside that range, so the date filter has something real to filter
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

- **`lot_number` lives on `medications`, not `order_items`.** The PDF's minimum schema defines it that way, which implies one medication = one production lot in this simplified model. A real system would likely track lots at the inventory/batch level, but the given schema doesn't ask for that, so this stays faithful to the spec rather than adding unrequested complexity.
- **Authentication: Sanctum with bearer tokens**, not cookie-based SPA auth. Simpler to reason about and demo since frontend and backend share the same origin here, and it matches the PDF's explicit mention of "token-based authentication."
- **Login is by `username`, not `email`**, per the PDF's explicit requirement — the default Laravel scaffold (which uses email) was adjusted accordingly.
- **`medications/search` and `orders?lot=` are separate endpoints**, matching the two endpoints listed in the PDF. `medications/search` confirms the lot exists in the catalog; `orders` returns the actual transactional data. The frontend's search screen only calls `orders`, since the order data already carries enough medication info for the table.
- **No role-based access control.** The PDF requires only that the module be restricted to *authenticated* users (`auth:sanctum` on every protected route) — it never defines distinct roles or an ownership relationship between staff users and customers/orders. This is an internal pharmacy panel: any authenticated staff member needs to see any order or customer to do their job (tracing who bought a recalled lot). Role-based access is listed as an optional bonus in the PDF and was intentionally left out of scope.
- **CSV export streams the full filtered result**, not just the current page, since the point of exporting is usually to get everything matching a search, not one page of it. Output is escaped against CSV formula injection (fields starting with `=`, `+`, `-`, `@` are prefixed to prevent spreadsheet software from treating customer-supplied names as executable formulas).
- **Alerts are queued** (`ShouldQueue` on the Mailable), so triggering a bulk alert doesn't block the HTTP response while dozens of emails send.
- **Alert audit log includes `user_id`**, even though the PDF's minimum schema for `alerts` only lists `customer_id`, `order_id`, `sent_at`. Section 3.6 explicitly requires logging "user who triggered it," so that requirement took priority over the minimum schema listing.

## Assumptions

- The "last month" default filter is implemented as the last 30 days from the current date, applied when no `start_date`/`end_date` is provided.
- A customer's `phone` is optional (nullable) since the PDF describes contact as "email/phone," suggesting either is acceptable; `email` is required since it's the mandatory notification channel.
- `APP_DEBUG=true` is used in development/demo environments (including the Docker setup) to make debugging faster. In a real production deployment this **must** be `false` — leaving it on exposes stack traces in error responses.
- SMS alerting (listed as a bonus) was not implemented; email is the required channel and was prioritized.

## Bonus features implemented

- Bulk alerting (select multiple orders, send one recall email each)
- CSV export of search results
- Docker / Podman setup for the full stack (not just the database)
- Alert audit log (`alerts` table: who sent it, to whom, when)

## Git workflow

This repo follows a simplified Git Flow: `main` (stable) + `develop` (integration) + one `feature/*` branch per module, merged via pull request. See the closed PRs for the history of how each feature was built and reviewed.
