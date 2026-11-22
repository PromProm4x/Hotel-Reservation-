# De La Renaissance — Hotel Reservation Management System

A full-featured hotel reservation management system built with **Laravel 12**, **Blade + Tailwind CSS**, **MySQL**, and **Spatie Laravel-Permission**, for De La Renaissance Hotel.

## Features

- **Guest-facing booking** — room search by dates/guests/room type, real-time availability with date-overlap protection, branded booking flow, booking confirmation email, self-service cancellation, “My Bookings”.
- **Authentication & roles** — guest registration/login plus staff/admin back office, powered by Spatie Laravel-Permission (`admin`, `staff`, `guest`).
- **Room management** — CRUD for room types (pricing, amenities, photos) and room inventory (number, floor, status), admin-only.
- **Reservations** — statuses (Pending, Confirmed, Checked-in, Checked-out, Cancelled, No-show), walk-in bookings, monthly booking calendar.
- **Payments** — deposits, balances, full payments, refunds; derived payment status (Unpaid / Partially paid / Paid / Refunded); gateway-ready structure (`method`, `reference`, `meta` fields).
- **Front desk** — check-in / check-out workflow, live room status board grouped by floor (housekeeping view).
- **Guest CRM-lite** — profiles with booking history, internal notes, preferences.
- **Admin dashboard** — occupancy rate, daily/weekly/monthly revenue, today's arrivals & departures, upcoming arrivals.
- **Notifications** — queued booking confirmation, cancellation, and check-in reminder emails (scheduler sends reminders daily at 10:00).
- **Extras** — tax & currency via `hotel_settings`, soft deletes on rooms/room types/guests/reservations/payments, seeders with demo data, optional JSON API (Sanctum).

## Requirements

- PHP 8.3+
- Composer
- MySQL 8+ (or MariaDB)
- Node.js 20+ (for building Tailwind assets — optional; layouts fall back to the Tailwind CDN in development if no build exists)

## Setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Create the database (adjust credentials in .env first)
mysql -u root -p -e "CREATE DATABASE de_la_renaissance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. Publish the Spatie permission migration (IMPORTANT: before migrating)
php artisan vendor:publish --provider="Spatie\\Permission\\PermissionServiceProvider"

# 5. Migrate and seed demo data
php artisan migrate --seed

# 6. Front-end assets (optional but recommended)
npm install
npm run build

# 7. Run the app
php artisan serve

# 8. In separate terminals — queued mail + scheduled reminders
php artisan queue:work
php artisan schedule:work
```

By default `.env.example` uses the `log` mail driver, so emails are written to `storage/logs/laravel.log`. Point `MAIL_MAILER` at a real SMTP server for actual delivery.

## Demo accounts (seeded)

| Role  | Email                          | Password   |
|-------|--------------------------------|------------|
| Admin | admin@delarenaissance.test     | `password` |
| Staff | frontdesk@delarenaissance.test | `password` |
| Guest | guest@delarenaissance.test     | `password` |

The seeders also create 5 room types, ~30 rooms across 6 floors, 12 guest profiles, and ~14 demo reservations (past, in-house, and upcoming) with payments.

## Key URLs

| Area | URL |
|------|-----|
| Public site & booking | `/` , `/rooms` |
| Guest bookings | `/my-bookings` |
| Back office | `/admin` (admin & staff) |
| Room type / room CRUD | `/admin/room-types`, `/admin/rooms` (admin only) |
| Front desk board | `/admin/frontdesk` |
| Booking calendar | `/admin/reservations-calendar` |

## Architecture notes

- **Availability & double-booking prevention** — `App\Services\AvailabilityService` finds free rooms using a date-overlap query (`check_in < requested_check_out AND check_out > requested_check_in`) against blocking statuses (pending/confirmed/checked-in). `App\Services\ReservationService::create()` wraps booking creation in a DB transaction and locks the candidate room row (`lockForUpdate`) to prevent race-condition double bookings.
- **Pricing** — nightly rate × nights + tax from `hotel_settings.tax_rate`; totals are snapshotted on the reservation so later price changes don't affect existing bookings.
- **Payment gateways** — `payments` stores `method`, `type`, `status`, `reference`, and a JSON `meta` column; a Stripe/PayPal integration can create rows through the same model without schema changes.
- **Enums** — `App\Enums\ReservationStatus`, `RoomStatus`, `PaymentStatus` (backed string enums, cast on models).
- **Authorization** — routes are guarded with Spatie's `role:` middleware; `Gate::before` grants the `admin` role all abilities.

## Optional JSON API (Sanctum)

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/v1/room-types` | — | List active room types |
| GET | `/api/v1/availability?check_in=&check_out=&guests=` | — | Availability + quotes |
| GET | `/api/v1/user` | Token | Current user |
| GET | `/api/v1/reservations` | Token | The user's reservations |
| POST | `/api/v1/reservations` | Token | Create a booking |
| GET | `/api/v1/reservations/{code}` | Token | Booking details |

Create a token with `php artisan tinker`:

```php
User::where('email', 'guest@delarenaissance.test')->first()->createToken('mobile')->plainTextToken;
```

## Tests

The project ships with factories for all core models, so feature tests can be added quickly (e.g. asserting that overlapping reservations are rejected).

## Upgrade notes (v1.2)

**Reservation editing & deletion**
- Staff can now edit any reservation (dates, party size, room type, room assignment, nightly rate, discount, special requests) from the reservation page or the list. Availability is re-checked on save; the folio is repriced automatically.
- Reservations can be deleted (soft delete) from the list or detail page. Checked-in stays must be checked out first.

**Price editing & repricing**
- `nightly_rate` and `discount_amount` are editable per reservation.
- Totals are always recomputed as: room subtotal + services - discount + tax (tax applied on the net amount).

**Hotel services (extras)**
- New Services catalogue (Administration -> Services, admin only): breakfast, airport transfer, laundry, spa, late check-out, extra bed, minibar - seeded by `ServiceSeeder`.
- Staff can add/remove service charges on any reservation folio. Prices are snapshotted per line, so editing or deleting a service never changes past folios.
- New tables: `services`, `reservation_service_lines`; new reservation columns: `services_amount`, `discount_amount` (append-only migrations - safe for existing databases, just run `php artisan migrate`).

**Payments: KHQR / Bakong / cash**
- New payment method `khqr` (KHQR / Bakong) alongside cash, card, bank transfer, Stripe, PayPal.
- Guest booking pages show a scannable KHQR panel (QR rendered client-side) whenever a balance is due, with the hotel Bakong account and booking reference. Configure via `hotel_settings`: `khqr_enabled`, `bakong_account_id`, `bakong_account_name`.
- Note: the QR encodes a readable payment instruction for demo purposes. For production, generate a compliant EMV KHQR payload via the Bakong API / an acquiring bank SDK and reconcile via webhook.
- Payments recorded at the desk now default to Completed status (a Status select is also available), and each payment row shows the staff member who recorded it.

**API**
- `GET /api/v1/reservations/{code}` and the create endpoint now include `services`, `services_amount`, and `discount_amount`.
