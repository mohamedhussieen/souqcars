# Project Reference — رابطة سوق السيارات (Car Marketplace API)

A map of what things are called, how the routes are laid out, and how a request actually flows
through the codebase. Read `CLAUDE.md` first for the high-level architecture rules this project
follows; this file is the detailed reference for day-to-day navigation.

---

## 1. Glossary — enums and what their values mean

Every fixed value set in this project is a PHP 8.1 backed enum under `app/Enums/`. Database
columns store the enum's string value; models cast the column back to the enum instance.

| Enum | Values | Used on | Meaning |
|---|---|---|---|
| `CarStatus` | `active`, `pending`, `needs_inspection`, `rejected`, `sold` | `Car.status` | Listing lifecycle. New listings start `pending`. Admin moves them to `active` (approve), `rejected` (with a `rejection_reason`), or `needs_inspection`. `sold` is terminal — set via "Mark Sold" or an admin status update. Only `active` cars are visible to mobile browsing/search. |
| `SellerType` | `admin`, `individual`, `showroom` | `Car.seller_type` | Who's selling the car. Paired with `Car.seller_id`, which is a **loose** polymorphic reference (not a real Eloquent `morphTo`) resolved by `Car::seller()` via a `match` expression — `admin` sellers have `seller_id = null`, `individual` points to a `User`, `showroom` points to the single `Showroom` row. |
| `PaymentType` | `cash`, `installment`, `both` | `Car.payment_type` | Accepted payment options for a listing. |
| `CarCondition` | `new`, `used` | `Car.condition` | |
| `Transmission` | `automatic`, `manual` | `Car.transmission` | |
| `FuelType` | `petrol`, `diesel`, `electric`, `hybrid` | `Car.fuel_type` | |
| `BodyType` | `sedan`, `suv`, `hatchback`, `coupe`, `pickup`, `van`, `other` | `Car.body_type` | |
| `SortBy` | `newest`, `oldest`, `price_asc`, `price_desc`, `mileage_asc` | query param `sort_by` on List Cars / Home / Search | `price_desc` is the "highest price first" sort. |
| `UserRole` | `admin`, `showroom_owner`, `user` | Spatie Permission role name | Source of truth for role strings — never hardcode `'admin'` etc., reference `UserRole::Admin->value`. `showroom_owner` exists but has no distinct behavior/routes yet (reserved for when Showroom becomes multi-tenant). |
| `BookingStatus` | `pending`, `confirmed`, `completed`, `cancelled` | `Booking.status` | Valid transitions (enforced in `BookingService::updateStatus()`): `pending→confirmed`, `pending→cancelled`, `confirmed→completed`, `confirmed→cancelled`. Any other transition is a 422. |
| `NotificationType` | `car_match`, `showroom_reply`, `booking_confirmed`, `booking_cancelled`, `price_drop`, `listing_approved`, `listing_rejected`, `car_available` | `Notification.type` | Each has its own bilingual title/body translation keys under `messages.notifications.*` in `resources/lang/{ar,en}/messages.php`. |
| `Platform` | `android`, `ios` | `AppConfig.platform` | Drives the public `core/app-config` maintenance/force-upgrade gate mobile clients poll. |
| `ThemeMode` | `light`, `dark` | `User.theme` | |
| `AdType` | `banner`, `sell_your_car`, `service` | `Ad.type` | Placement/purpose of a promotional ad. |
| `AdActionType` | `car`, `url`, `screen` | `Ad.action_type` | What tapping the ad navigates to; `action_value` holds the target (a car id, a URL, a screen name). |

## 2. Naming you'll see repeated everywhere

- **`seller_type` / `seller_id`** — see `SellerType` above. Grep for `seller()` in `Car.php` to see
  the resolution logic. `Showroom::cars()` and `MyListingsController` both filter on this pair
  manually rather than using a real FK relation.
- **`_ar` / `_en` suffix pattern** — every bilingual field (names, titles, descriptions, addresses)
  is two plain columns, not a translations table. `HasLocalizedFields` trait collapses them to one
  `name`/`title`/`address` field on mobile-facing Resources, resolved from `app()->getLocale()`.
  Admin Resources always expose both raw `_ar`/`_en` fields since the dashboard edits both at once.
- **`HasDependentRecordsException`** — the generic 422 thrown by `delete()` methods across
  `AdminBrandService`, `AdminCarModelService`, `AdminCityService`, `MaintenanceCenterService` when
  the record being deleted still has dependent rows (cars, bookings). Mirrors the older
  `color_in_use`-style check that already existed for `Color`.
- **`NotOwnerException`** — the 403 thrown when a user tries to touch a `Booking`/`Notification`
  they don't own. Registered in `app/Exceptions/Handler.php` alongside the other custom exceptions.
- **`translationKey()` + `status()`** — the informal contract every custom business-rule exception
  implements (`BookingConflictException`, `BookingNotCancellableException`,
  `InvalidBookingStatusTransitionException`, `InvalidCarStateException`, `HasDependentRecordsException`,
  `NotOwnerException`, plus the older `PasswordResetException`/`CarImageLimitExceededException`).
  `Handler::handleApiException()` catches each by `instanceof` and renders
  `error(__($e->translationKey()), $e->status())`.
- **`ApiResponseTrait`** — every controller (via `BaseApiController`) and the exception `Handler`
  use this trait's `success()`, `successPaginated()`, `error()`, `notFound()`, `unauthorized()`,
  `validationError()` methods. No controller ever hand-rolls `response()->json()`.
- **`auth.optional` middleware (`OptionalSanctumAuth`)** — resolves the Sanctum user if a valid
  Bearer token is present, but never rejects the request. Applied to the whole `v1/mobile` group so
  guest-browsable endpoints (home, car list/detail, search, showrooms) can still enrich
  `is_favorited` when a token happens to be present.
- **`policy.accepted` middleware (`EnsurePolicyAccepted`)** — gates most of `/profile/*`. The one
  exception is `POST /profile/accept-policy` itself (must be reachable before accepting anything).
- **`admin` middleware (`EnsureIsAdmin`)** — checks `hasRole('admin')`. Always paired with
  `auth:sanctum` on admin routes (double protection — never relied on alone).

## 3. Request lifecycle (how one endpoint is actually wired)

```
routes/api.php
   → Controller (single-action __invoke, extends BaseApiController)
      → FormRequest (validates; authorize() is almost always `true` — auth/role is enforced by
         route middleware, not inside the request class)
         → Service (all business logic lives here — never in controllers or models)
            → Model / Eloquent
         ← returns a model or plain array
      ← Resource (never return raw models/arrays — always wrap in an API Resource)
   ← ApiResponseTrait::success()/successPaginated()/error()
```

Paginated list endpoints follow one extra step: the Service returns a `LengthAwarePaginator`, then
the controller re-wraps its items through the Resource before handing it to `successPaginated()`:

```php
$paginator->setCollection(
    collect(SomeResource::collection($paginator->getCollection())->toArray($request))
);
```

Every response, success or error, has the same envelope:

```json
{ "success": true, "message": "...", "data": ..., "meta": null, "errors": null }
```

## 4. Route map

Base path is `/api`. Three top-level groups, each with its own middleware stack — they never share
a guard or a role assumption.

### `core/*` — fully public

| Method | Path | Purpose |
|---|---|---|
| GET | `core/app-config` | Maintenance mode + forced-upgrade gate, polled by mobile clients. |
| GET | `core/terms` | Locale-aware terms & conditions. |

### `v1/mobile/*` — Flutter app (`auth.optional` at the group level; `auth:sanctum` / `policy.accepted` layered per-route)

| Area | Method | Path | Auth |
|---|---|---|---|
| Auth | POST | `auth/register` | public |
| | POST | `auth/login` | public |
| | POST | `auth/otp/send` | public |
| | POST | `auth/otp/verify` | public |
| | POST | `auth/forgot-password` | public, throttled 6/min/IP |
| | POST | `auth/verify-reset-otp` | public, throttled 6/min/IP |
| | POST | `auth/reset-password` | public |
| | POST | `auth/logout` | auth |
| Profile | POST | `profile/accept-policy` | auth |
| | GET | `profile` | auth |
| | PUT | `profile` | auth + policy |
| | PUT | `profile/password` | auth + policy |
| | PUT | `profile/preferences` | auth + policy |
| | DELETE | `profile` | auth + policy |
| | GET | `profile/stats` | auth |
| Lookup | GET | `lookup/cities`, `lookup/brands`, `lookup/brands/{brand}/models`, `lookup/colors` | public |
| Home/Discovery | GET | `home`, `search` | public (auth optional) |
| Cars | GET | `cars`, `cars/{car}`, `cars/{car}/ratings` | public |
| | POST | `cars/{car}/ratings` | auth |
| | POST/DELETE | `cars/{car}/watch` | auth |
| Favorites | GET | `favorites` | auth |
| | POST | `favorites/{car}` | auth (like/unlike toggle) |
| My Listings | GET | `my-listings` | auth |
| Showrooms | GET | `showrooms`, `showrooms/{showroom}`, `showrooms/{showroom}/cars` | public |
| Maintenance | GET | `maintenance-centers`, `maintenance-centers/{center}` | public |
| Bookings | POST/GET | `bookings` | auth |
| | GET | `bookings/{booking}` | auth (owner only, 403 otherwise) |
| | DELETE | `bookings/{booking}/cancel` | auth (owner or admin) |
| Notifications | GET | `notifications`, `notifications/unread-count` | auth |
| | PUT | `notifications/read-all`, `notifications/{notification}/read` | auth |
| Watch Requests | GET | `watch-requests` | auth |

### `admin/*` — Angular dashboard (`auth:sanctum` + `admin` role on every route except login/me/logout)

| Area | Method | Path |
|---|---|---|
| Auth | POST `auth/login` (public), GET `auth/me`, POST `auth/logout` |
| Stats | GET `stats`, `analytics/cars-per-month`, `analytics/bookings-per-status`, `analytics/top-brands`, `analytics/top-cities` |
| Users | GET `users`, `users/{user}`; PUT `users/{user}/toggle-active`, `users/{user}/ban`, `users/{user}/unban`, `users/{user}/role`; DELETE `users/{user}` |
| Cities / Brands / Car Models / Colors | Standard GET list, POST create, PUT `{id}` update, DELETE `{id}` (Brands also has POST `{brand}/logo`) |
| Cars | GET `cars`, `cars/{car}`; POST `cars`, `cars/{car}/images`, `cars/{car}/inspection`, `cars/{car}/sold`; PUT `cars/{car}`, `cars/{car}/status`; DELETE `cars/{car}`, `cars/{car}/images/{mediaId}` |
| Showroom | GET/PUT `showroom` (singleton), POST `showroom/logo` |
| Ads | GET `ads`; POST `ads`; PUT `ads/reorder`, `ads/{ad}`, `ads/{ad}/toggle`; DELETE `ads/{ad}` |
| Maintenance Centers | GET/POST `maintenance-centers`; PUT/DELETE `maintenance-centers/{center}`; POST `maintenance-centers/{center}/logo`; POST/PUT/DELETE `maintenance-centers/{center}/services[/{service}]` |
| Bookings | GET `bookings`; PUT `bookings/{booking}/status` |
| Watch Requests | GET `watch-requests` (read-only demand overview, grouped by brand+model) |

## 5. Architecture notes worth knowing before you touch things

- **Showroom is a Phase-1 singleton.** One row, admin-managed profile (`GET/PUT /admin/showroom`,
  `POST /admin/showroom/logo`). It is *not* a multi-tenant marketplace of many showrooms yet — the
  `user_id` column exists but is unused, reserved for a future ownership phase. Don't build
  multi-showroom CRUD on top of this without a deliberate migration decision first.
- **Media storage is local/public disk.** Cloud storage (S3/R2) was scoped out of Sprint 3/4 —
  `config/filesystems.php` has an `s3` disk block but the driver package isn't installed. All
  `HasMedia` models (`Car`, `Showroom`, `Brand`, `User`, `Ad`, `MaintenanceCenter`) use Spatie Media
  Library against the default disk. This is a known follow-up task, not an oversight.
  `FILESYSTEM_DISK` in `.env.production.example` is set to `public`, not `s3`/`r2`.
  `Car`'s `inspection_report` collection is still meant to be served privately in principle, but
  since there's no cloud disk yet it currently resolves via the same public disk as everything
  else — revisit `getInspectionReportUrlAttribute()` when cloud storage lands.
- **FCM push already existed before Sprint 3/4.** `User` has `SendsFirebaseNotifications` (trait:
  multicast send + automatic dead-token pruning) and a `UserFcmToken` model/relation. Sprint 3/4's
  `NotificationService::send()` calls `$user->sendPushNotification()` directly — there is
  deliberately no separate `FCMService` class, to avoid duplicating that trait's logic.
- **Queues are real (`QUEUE_CONNECTION=database`).** `CarObserver` (registered in
  `AppServiceProvider::boot()`) dispatches `SendWatchNotificationsJob` when a car becomes `active`,
  and `SendPriceDropJob` when an active car's price decreases. Both jobs run on the `notifications`
  queue with `WithoutOverlapping` middleware keyed per car. Run
  `php artisan queue:work database --queue=notifications,default` locally to see these actually
  fire; without a worker running they just sit in the `jobs` table.
- **Rate limiting** is defined in `AppServiceProvider::registerRateLimiters()` (`mobile-auth`
  10/min/IP, `mobile-api` 60/min/user or 30/min/IP guest, `admin-api` 120/min/user) and applied at
  the route-group level in `routes/api.php`. The pre-existing `throttle:6,1` on
  forgot-password/verify-reset-otp was intentionally left untouched (existing tests assert exactly
  6 requests/minute there) rather than changed to match the newer 3/minute figure floated during
  planning — a deliberate, documented deviation.
- **`Handler::handleApiException()`** forwards the original exception's headers on 429 responses
  (`Retry-After` included) — this was a real bug fixed during Sprint 3/4; don't reintroduce a bare
  `$this->error(..., 429)` without `->withHeaders($e->getHeaders())`.
- **Feature-test gotcha (harness, not app code):** a test that authenticates as two different users
  via `withHeader('Authorization', ...)` twice in the same test method will silently keep resolving
  the *first* user for the second call, because Sanctum's guard caches its resolved user on the
  test's shared app instance. Real HTTP requests are unaffected (each gets a fresh app boot) — this
  only bites multi-actor feature tests. Fix: call `app('auth')->forgetGuards()` between the two
  `withHeader()` calls. See `tests/Feature/Mobile/BookingTest.php` for the pattern.

## 6. Seeders

`php artisan db:seed` (or `migrate:fresh --seed`) runs, in order:

1. `RoleSeeder` — admin / showroom_owner / user roles.
2. `CitySeeder` — 10 Egyptian cities.
3. `BrandSeeder` — 10 brands with 3-5 models each.
4. `ColorSeeder` — 15 colors.
5. `AppConfigSeeder`, `PolicyTermSeeder`, `AdminUserSeeder` — app-config rows, terms sections, one
   admin login (`admin@cars-marketplace.test` / `admin12345`).
6. `CarSeeder` — 40 realistic car listings created through `CarService::create()` (the same path
   the admin dashboard uses), spread across all 5 `CarStatus` values and all 3 `SellerType` values
   (admin, the singleton showroom, and 5 newly-created individual-seller users), each with 2-4
   generated placeholder gallery images and occasional inspection reports.
7. `MaintenanceCenterSeeder` — 3 maintenance centers with 2-3 services each.
8. `BookingSeeder` — one booking per individual-seller user, spread across all 4 `BookingStatus`
   values.
9. `NotificationSeeder` — a handful of notifications (mixed read/unread) per individual-seller user.
10. `WatchRequestSeeder` — one watch request per individual-seller user against a sold car's
    brand/model.

`ProductionSeeder` (used by `deploy.sh`) runs only steps 1-4 — no test users, no fake cars/bookings.

## 7. Testing

`php artisan test` — 316 tests (unit + feature) across every sprint. Unit tests
(`tests/Unit/*ServiceTest.php`) instantiate services directly against a real `RefreshDatabase`
connection (no mocks). Feature tests (`tests/Feature/{Admin,Mobile,Core}/*Test.php`) hit real HTTP
routes via a local `tokenFor(User $user)` helper for Sanctum auth.

## 8. Postman collection

`postman/cars_marketplace_api.postman_collection.json` covers all 108 API routes, organized into
🌍 Core / 📱 Mobile / 🛡️ Admin folders mirroring the route map above. Every request and every body
field has a description. Collection variables: `base_url`, `mobile_url`, `admin_url`, `core_url`,
`mobile_token`, `admin_token`, `reset_token`, `locale`. The two login requests carry test scripts
that auto-populate `mobile_token`/`admin_token` from the response so every other request's Bearer
auth works immediately after logging in.
