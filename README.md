# رابطة سوق السيارات — Car Marketplace API

Laravel 11 REST API for a car marketplace. Serves two separate clients from one codebase:

- **Mobile app** (`/api/v1/mobile/*`) — auth, profile, browsing, favorites, ratings, showrooms,
  bookings, notifications, and "notify me" watch requests for end users.
- **Admin dashboard** (`/api/admin/*`) — a separate Angular-driven admin panel managing users,
  brands, cities, car models, cars, showrooms, ads, maintenance centers, and bookings.

Every route returns JSON. There is no web/session frontend. Auth is Sanctum-based (Bearer tokens)
for both clients.

## Setup

```bash
composer install
cp .env.example .env   # or hand-edit .env directly if no example exists yet
php artisan key:generate
php artisan migrate
php artisan db:seed              # full dev dataset (roles, cities, brands, colors, app config, terms, admin user)
php artisan serve --port=8000
```

Queued jobs (notifications, price-drop alerts) require a worker:

```bash
php artisan queue:work database --queue=notifications,default
```

## Environment variables

Key `.env` values (see `.env.production.example` for the production template):

| Variable | Purpose |
|---|---|
| `DB_*` | MySQL connection |
| `QUEUE_CONNECTION` | `database` — required for notification/price-drop jobs to run async |
| `FIREBASE_CREDENTIALS` | Path to (or raw JSON of) the Firebase service-account file, used for FCM push notifications |
| `MAIL_*` | SMTP for OTP emails (defaults to `log` driver in dev — OTPs land in `storage/logs/laravel.log`) |
| `FILESYSTEM_DISK` | Media storage disk. Currently local/public disk; cloud storage (S3/R2) is a planned follow-up, not wired up yet |

## API versioning

- Mobile routes are versioned under `/api/v1/mobile/*`. Any breaking change to mobile contracts
  should introduce a new `/api/v2/mobile/*` prefix rather than mutating v1 in place.
- Admin routes (`/api/admin/*`) are not versioned — the Angular dashboard is deployed in lockstep
  with the API, so breaking admin changes ship together with their dashboard consumer.
- Every response uses the same envelope regardless of version:
  `{ "success": bool, "message": string, "data": any, "meta": object|null, "errors": object|null }`.

## Testing

```bash
php artisan test
```

309+ feature/unit tests cover auth, car filtering/search, favorites/ratings, bookings, watch
requests, notifications, and admin CRUD across all sprints.

## Deployment

See `deploy/README.md` for the nginx, supervisor, and cron configs, plus `deploy.sh` for the
one-shot deploy script (git pull → composer install → migrate → cache → queue:restart) and
`.env.production.example` for the production environment template. Target: Contabo VPS, Ubuntu
22.04, Nginx, PHP 8.3.

## Postman collection

`postman/cars_marketplace_api.postman_collection.json` has ready-made requests for every endpoint
across all sprints, organized by feature area under `📱 Mobile` and `🛡️ Admin`. Import it alongside
setting `base_url`, `mobile_token`, and `admin_token` collection variables (auto-populated by the
login requests' test scripts).
