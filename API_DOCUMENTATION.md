# رابطة سوق السيارات — API Documentation

Laravel 11 REST API for a car marketplace, serving two clients from one codebase:

- **Mobile app** — `/api/v1/mobile/*`
- **Admin dashboard** (Angular) — `/api/admin/*`

Live server: `https://api-rabta.rabtetsouqelsayarat.com/api`

---

## Authentication

Both clients use **Laravel Sanctum** Bearer tokens. There is no session/CSRF flow — every request is stateless JSON.

```
Authorization: Bearer <token>
Accept: application/json
Accept-Language: ar|en   (optional, defaults to ar)
```

- Mobile tokens are issued by `POST /v1/mobile/auth/login` or `/register`, valid 30 days.
- Admin tokens are issued by `POST /admin/auth/login` and require the `admin` role (checked both at login and via route middleware).
- Some mobile browsing endpoints (home, car list/detail, search, showrooms) accept an **optional** token — if present and valid, the response is personalized (e.g. `is_favorited`); if absent, the request still succeeds as a guest.

---

## Response envelope

Every response, success or error, has the same shape:

```json
{
  "success": true,
  "message": "...",
  "data": ...,
  "meta": null,
  "errors": null
}
```

Paginated responses populate `meta`:

```json
{
  "current_page": 1,
  "per_page": 15,
  "total": 42,
  "last_page": 3,
  "next_page_url": "...",
  "prev_page_url": null
}
```

`GET /v1/mobile/my-listings` additionally includes `active_count`, `pending_count`, `sold_count`, `total` in `meta`.

### Status codes

| Code | Meaning |
|---|---|
| 200 | Success |
| 201 | Created |
| 400 | Business-rule error (e.g. invalid OTP, color in use) |
| 401 | Unauthenticated |
| 403 | Forbidden (non-admin hitting admin routes) |
| 404 | Not found |
| 422 | Validation error (`errors` populated with field-level messages) |
| 429 | Rate-limited |
| 500 | Server error (message hidden unless `APP_DEBUG=true`) |

### Pagination

All list endpoints accept `page` and `per_page` (default 15, max 50 — clamped, not rejected).

---

## Bilingual content

Lookup/listing entities (`City`, `Brand`, `CarModel`, `Color`, `Car`, `Showroom`, `Ad`) store `name_ar`/`name_en` (or `title_ar`/`title_en`, `description_ar`/`description_en`) as separate columns.

- **Mobile resources** return a single localized field (`name`, `title`, `description`) resolved from the `Accept-Language` header.
- **Admin resources** always return both `_ar` and `_en` fields, since the dashboard edits both languages at once.

---

## Enums (fixed value sets)

| Enum | Values |
|---|---|
| `CarStatus` | `active`, `pending`, `needs_inspection`, `rejected`, `sold` |
| `SellerType` | `admin`, `individual`, `showroom` |
| `PaymentType` | `cash`, `installment`, `both` |
| `CarCondition` | `new`, `used` |
| `Transmission` | `automatic`, `manual` |
| `FuelType` | `petrol`, `diesel`, `electric`, `hybrid` |
| `BodyType` | `sedan`, `suv`, `hatchback`, `coupe`, `pickup`, `van`, `other` |
| `AdType` | `banner`, `sell_your_car`, `service` |
| `AdActionType` | `car`, `url`, `screen` |
| `SortBy` | `newest`, `oldest`, `price_asc`, `price_desc`, `mileage_asc` |
| `UserRole` | `admin`, `showroom_owner`, `user` |

---

## Mobile API (`/api/v1/mobile`)

### Auth

| Method | Path | Auth | Notes |
|---|---|---|---|
| POST | `/auth/register` | — | Returns `user` + `token` |
| POST | `/auth/login` | — | Returns `user` + `token` |
| POST | `/auth/logout` | ✅ | Revokes current token only |
| POST | `/auth/otp/send` | — | Identity-verification OTP (static `1234` in dev) |
| POST | `/auth/otp/verify` | — | Verifies the OTP above |
| POST | `/auth/forgot-password` | — | Step 1 of password reset. Throttled 6/min. Same response whether email exists or not (no user enumeration) |
| POST | `/auth/verify-reset-otp` | — | Step 2: verifies OTP, returns a `reset_token` (60 chars, 10 min TTL). Throttled 6/min |
| POST | `/auth/reset-password` | — | Step 3: `email` + `reset_token` + new `password`. Revokes all existing tokens |

> **Important:** the forgot-password flow is 3 separate steps — `forgot-password` → `verify-reset-otp` → `reset-password`. Don't call the generic `otp/verify` in this flow; it consumes the code without issuing a `reset_token`.

### Profile (gated by `auth:sanctum`; most also require `policy.accepted`)

| Method | Path | Notes |
|---|---|---|
| GET | `/profile` | Not policy-gated (must be reachable pre-acceptance) |
| POST | `/profile/accept-policy` | Not policy-gated |
| PUT | `/profile` | Requires policy acceptance |
| PUT | `/profile/password` | Requires policy acceptance |
| PUT | `/profile/preferences` | Requires policy acceptance |
| DELETE | `/profile` | Requires policy acceptance |

### Lookup (public)

| Method | Path |
|---|---|
| GET | `/lookup/cities` |
| GET | `/lookup/brands` |
| GET | `/lookup/brands/{brand}/models` |
| GET | `/lookup/colors` |

### Home & Discovery

| Method | Path | Auth | Notes |
|---|---|---|---|
| GET | `/home` | optional | Returns `ads`, `brands` (with cars), `customer_cars`, `latest_cars`, `showrooms`, `featured_cars` |
| GET | `/cars` | optional | Filterable, sortable, paginated. Scoped to `status=active` |
| GET | `/cars/{car}` | optional | Increments `views_count`. 404s if not active |
| GET | `/search` | optional | Same filters as `/cars`, `search` required (min 2 chars) |

**Filter query params** (all optional, combinable): `brand_id`, `car_model_id`, `city_id`, `color_id`, `year_from`, `year_to`, `price_min`, `price_max`, `mileage_max`, `condition`, `transmission`, `fuel_type`, `body_type`, `payment_type`, `has_inspection`, `seller_type`, `search`, `sort_by` (default `newest`).

### Favorites (`auth:sanctum`)

| Method | Path | Notes |
|---|---|---|
| GET | `/favorites` | Paginated list of favorited cars |
| POST | `/favorites/{car}` | Toggles — adds if absent, removes if present. Returns `{ "added": bool }` |

### Ratings

| Method | Path | Auth | Notes |
|---|---|---|---|
| GET | `/cars/{car}/ratings` | — | Paginated, most recent first |
| POST | `/cars/{car}/ratings` | ✅ | `rating` (1–5, required), `comment` (nullable, max 500). One rating per user per car — resubmitting updates it |

### Showrooms (public)

| Method | Path |
|---|---|
| GET | `/showrooms` |
| GET | `/showrooms/{showroom}` |
| GET | `/showrooms/{showroom}/cars` |

### My Listings (`auth:sanctum`)

| Method | Path | Notes |
|---|---|---|
| GET | `/my-listings` | Cars where `seller_type=individual` and `seller_id=auth user`. All statuses included. `meta` has status counts |

---

## Admin API (`/api/admin`)

All routes below require `auth:sanctum` + the `admin` middleware alias (role check via spatie/laravel-permission), except login itself.

### Auth

| Method | Path | Notes |
|---|---|---|
| POST | `/auth/login` | Re-verifies the `admin` role server-side even with a valid password |
| GET | `/auth/me` | |
| POST | `/auth/logout` | |

### Users

| Method | Path |
|---|---|
| GET | `/users` |
| GET | `/users/{user}` |
| PUT | `/users/{user}/toggle-active` |
| PUT | `/users/{user}/role` |
| DELETE | `/users/{user}` |

### Cities / Brands / Car Models

Standard CRUD at `/cities`, `/brands`, `/car-models`. Brands and car models support a `logo`/media upload on create/update (Brands only).

### Cars

| Method | Path | Notes |
|---|---|---|
| GET | `/cars` | All statuses/seller_types. Filters: `status`, `seller_type`, `brand_id`, `city_id`, `search` |
| POST | `/cars` | Creates with `seller_type=admin`, `seller_id=null` |
| GET | `/cars/{car}` | |
| PUT | `/cars/{car}` | |
| DELETE | `/cars/{car}` | Soft delete |
| POST | `/cars/{car}/images` | Multipart, `images[]`, max 5MB each, jpeg/png/webp. Max 10 images total (existing + new) — throws `CarImageLimitExceededException` (422) if exceeded |
| DELETE | `/cars/{car}/images/{mediaId}` | |
| POST | `/cars/{car}/inspection` | Multipart, `file`, max 10MB, jpeg/png/pdf. Sets `has_inspection_report=true` |
| POST | `/cars/{car}/sold` | Marks `status=sold` |
| PUT | `/cars/{car}/status` | `status` (enum) + `rejection_reason` (required if `status=rejected`) |

### Showroom (single profile, Phase 1)

| Method | Path | Notes |
|---|---|---|
| GET | `/showroom` | Lazily creates a default row if none exists |
| PUT | `/showroom` | |
| POST | `/showroom/logo` | Multipart, `logo`, max 2MB |

### Ads

Standard CRUD at `/ads`. Create/update accept a multipart `image` (max 5MB). Ordered by `sort_order`.

### Colors

Standard CRUD at `/colors`. `DELETE` returns 422 if any car currently uses that color.

---

## Known gotchas

- **`config/sanctum.php`'s `guard` must stay `['web']`** — setting it to `['sanctum']` while the default auth guard is also `sanctum` causes infinite recursion.
- **Never add `$middleware->statefulApi()`** in `bootstrap/app.php` — this API is Bearer-only, no CSRF/session mode.
- **`Illuminate\Auth\Middleware\Authenticate::redirectUsing()`** is registered in `AppServiceProvider` to return `null` — without this, an unauthenticated request that omits `Accept: application/json` crashes with a 500 (`Route [login] not defined`) instead of a clean 401, because Laravel's default guest-redirect assumes a web app with a login view.
- **`app/Exceptions/Handler.php` must be wired via `bootstrap/app.php`'s `withExceptions()`** — in Laravel 11, simply having the class isn't enough; it needs `$exceptions->render(...)` to delegate to it.
- **The GD PHP extension is required** for image uploads (Spatie MediaLibrary conversions). Missing GD causes a 500 on any image upload (`Call to undefined function ... imagecreatefromstring()`).
- **Composer 2.10+ blocks `laravel/framework` installs** if any advisory is flagged for the resolved version range, even when none are exploitable in this app's context. If `composer update` refuses to resolve `laravel/framework ^11.0` on the server, either downgrade Composer (`composer self-update 2.7.7`) or configure `policy.advisories.block=false` — check your Composer version's exact config surface first, as this varies between 2.x releases.
- **`storage/app/firebase/service-account.json`** holds live Firebase credentials for push notifications (`app/Traits/SendsFirebaseNotifications.php`) — never commit it; it's git-ignored on purpose. Deploy it manually to each environment.

---

## Testing

```bash
php artisan test
```

SQLite in-memory (`phpunit.xml`), covers `CarFilterService`, `FavoriteService`, `RatingService`, `CarService`. Factories exist for `User`, `Brand`, `City`, `CarModel`, `Color`, `Showroom`, `Car`.

## Postman

`postman/cars_marketplace_api.postman_collection.json` — see the collection for ready-made requests per endpoint, including filter combinations, multipart upload examples, and bilingual (`ar`/`en`) variants.
