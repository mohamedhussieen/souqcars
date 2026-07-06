# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

Laravel 11 REST API for a car marketplace (سوق السيارات). It serves two separate clients from one codebase:

- **Mobile app** (`/api/v1/mobile/*`) — auth, profile, and lookup data for end users.
- **Admin dashboard** (`/api/admin/*`) — a separate Angular-driven admin panel that manages users, brands, cities, and car models.

There is no web/session frontend — every route returns JSON. Auth is Sanctum-based (Bearer tokens) for both clients; there is no CSRF/cookie-session flow (`bootstrap/app.php` does not call `statefulApi()`, and Sanctum's `guard` config is left at its default `['web']` — do not add `statefulApi()` or point it at `['sanctum']`, it causes infinite guard recursion, see "Known gotchas" below).

## Commands

```bash
composer install                       # install PHP dependencies
php artisan serve --port=8000          # run dev server
php artisan migrate                    # run migrations
php artisan migrate:fresh --seed       # rebuild DB from scratch with seed data
php artisan db:seed                    # re-run seeders only (all are idempotent, safe to re-run)
php artisan config:clear               # clear cached config (needed after editing .env or config/*)
php artisan route:list                 # inspect all registered routes
```

There is no test suite in this repo yet (`phpunit.xml`/`tests/` are not present despite `phpunit` being a dev dependency). Verify behavior by hitting endpoints directly (curl/Postman) against a running `artisan serve` instance.

The `postman/cars_marketplace_api.postman_collection.json` collection has ready-made requests for every endpoint — use it instead of hand-writing curl calls when exploring the API.

## Architecture

### Two independent route groups, two independent auth scopes

`routes/api.php` defines three top-level groups that never share a guard or a role assumption:

- `core/*` — fully public (app-config endpoint for mobile maintenance/version gating).
- `admin/*` — requires `auth:sanctum` + the custom `admin` middleware alias (`EnsureIsAdmin`, checks `hasRole('admin')` via spatie/laravel-permission). `AdminAuthService::login()` independently re-checks the admin role before issuing a token — a non-admin user's credentials are rejected here even with a valid password.
- `v1/mobile/*` — requires `auth:sanctum` only; regular users never need the `admin` role.

Both mobile and admin controllers can authenticate against the *same* `User` model/table — there is no separate `admins` table. What differs is the middleware stack and which `Service` class is called.

### Controller → Request → Service → Resource pipeline

Every endpoint follows the same shape:

1. A thin single-action controller (`__invoke`) in `app/Http/Controllers/Api/{Admin,Mobile,Core}/...`.
2. A `FormRequest` in `app/Http/Requests/{Admin,Auth,Profile,Lookup,Core}/...` does all validation. Authorization for admin-only requests is deliberately left as `return true` in the request class — the actual admin check happens in route middleware, not in the request's `authorize()`.
3. All business logic lives in `app/Services/*Service.php`, never in controllers. Admin and mobile flows that touch the same resource use **separate** services (e.g., `AdminUserService` vs. nothing-for-mobile, `AdminBrandService` vs. `LookupService` for brands) — don't try to unify them, the admin versions return both `name_ar`/`name_en` while mobile versions return one localized `name` field.
4. Responses go through API Resources (`app/Http/Resources/**`), never raw models/arrays.

### Bilingual content pattern

Lookup-style models (`City`, `Brand`, `CarModel`) store `name_ar`/`name_en` as separate columns instead of using a translations table. Two different resource shapes exist for the same model:

- **Mobile resources** (`CityResource`, `BrandResource`, `CarModelResource`) use the `HasLocalizedFields` trait to collapse both columns into a single `name` field, resolved from `app()->getLocale()` (set per-request by the `EnsureLocale` middleware, which reads the `Accept-Language` header — not a query param).
- **Admin resources** (`app/Http/Resources/Admin/Admin*Resource.php`) expose `name_ar` and `name_en` both, since the dashboard needs to edit both languages at once.

When adding a new bilingual lookup entity, mirror this dual-resource pattern rather than inventing a new one.

### Unified API response envelope

Every response — success, paginated, or error — has the same top-level shape, produced by `ApiResponseTrait` (used by `BaseApiController` and by `app/Exceptions/Handler.php` for uncaught exceptions):

```json
{ "success": true|false, "message": "...", "data": ..., "meta": null|{...}, "errors": null|{...} }
```

`app/Exceptions/Handler.php` intercepts every exception for requests where `expectsJson()` or the path is `api/*`, and maps `ValidationException` → 422, `ModelNotFoundException` → 404, `AuthenticationException` → 401, anything else → 500 (with the real message only when `APP_DEBUG=true`). Don't hand-roll try/catch blocks in controllers for these cases — the handler already does it globally.

### OTP flow is unified across three different mobile features

`AuthService::sendOtp()`/`verifyOtp()` back **all** of: `otp/send`+`otp/verify` (identity verification), `forgot-password` (which just calls `sendOtp()`), and `reset-password` (which calls `verifyOtp()` internally before changing the password). There is no separate password-reset-token mechanism — everything is one 4-digit, 5-minute-expiry OTP keyed by email in the `otp_codes` table. If you touch OTP logic, check all three call sites.

`OtpCodeMail` currently sends through whatever `MAIL_MAILER` is configured; in most environments so far this has been `log` (OTP codes land in `storage/logs/laravel.log`, not a real inbox) until real SMTP credentials are available.

### Policy-acceptance gate

`users.policy_accepted_at` (nullable timestamp) gates all of `/v1/mobile/profile/*` via the `policy.accepted` middleware alias (`EnsurePolicyAccepted`). The one exception is `POST /v1/mobile/profile/accept-policy` itself, which only requires `auth:sanctum` — a user must be able to reach it before they've accepted anything. When adding a new mobile profile endpoint, decide deliberately whether it belongs inside or outside this gate.

### Role model

Three roles exist via spatie/laravel-permission (seeded by `RoleSeeder`): `admin`, `showroom_owner`, `user`. `UserRole` enum is the source of truth for role string values — don't hardcode role name strings, reference `UserRole::Admin->value` etc. `showroom_owner` is defined but has no distinct behavior/routes yet.

### App config / mobile version gating

`AppConfig` (one row per `Platform` enum case — currently `android`/`ios`) drives the public `core/app-config` endpoint, which mobile clients poll for `maintenance_enabled` and forced-upgrade checks (`min_version`/`current_version`/`force_upgrade`). This table is meant to be edited via the admin dashboard, not hardcoded — there's no admin CRUD for it yet, so it's currently seed-only (`AppConfigSeeder`).

## Known gotchas (already hit and fixed once — don't reintroduce)

- **`config/sanctum.php`'s `guard` key must stay `['web']`.** Setting it to `['sanctum']` while `auth.defaults.guard` is `sanctum` causes `Laravel\Sanctum\Guard` to resolve itself recursively, exhausting PHP memory with no useful error message.
- **Don't add `$middleware->statefulApi()`** in `bootstrap/app.php`. This API is Bearer-token-only; enabling Sanctum's stateful/session mode causes 419 CSRF failures on any same-origin POST (e.g. from API-doc "try it" UIs) for no benefit here.
- **This repo was hand-assembled, not `laravel new`'d** — several normally-scaffolded files (`config/*.php` beyond the defaults, `bootstrap/providers.php`, `app/Http/Controllers/Controller.php`, the sanctum/permission/medialibrary migrations) had to be added manually, and package migrations (Sanctum's `personal_access_tokens`, spatie/permission's role tables, spatie/medialibrary's `media` table) were published from vendor and renumbered to sort before they're needed. If a fresh clone throws "table not found" for one of these, check whether its migration file is actually present before assuming a code bug.
- **MySQL passwords with `#`, `$`, or other shell/`.env`-special characters get silently truncated** if placed unquoted in `.env` (`#` starts a comment). Always re-verify with `grep DB_PASSWORD .env` after setting it, and prefer alphanumeric-only passwords for `.env`-stored credentials.
