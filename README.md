# GateKeeper — Event Ticket Booking & Management

Full-stack Laravel + MySQL university project (CSE416 Web Engineering Lab).

A complete Event Ticket Booking & Management platform with three roles: **Attendee**, **Organizer**, **Admin**. Includes QR ticketing, real-time webcam check-in, PDF tickets, email notifications, and full role-based dashboards.

---

## Quick Start

```bash
# 1. Install PHP dependencies (already done if you cloned with vendor/)
composer install

# 2. (Optional) JS — only if you want to rebuild Breeze CSS. Not required;
#    the app uses public/css/app.css which is plain CSS.
# npm install && npm run build

# 3. Run migrations & seed demo data
php artisan migrate:fresh --seed

# 4. Boot the dev server
php artisan serve
# -> http://127.0.0.1:8000
```

### Demo Credentials

| Role      | Email                       | Password |
| --------- | --------------------------- | -------- |
| Admin     | admin@gatekeeper.test       | password |
| Organizer | organizer@gatekeeper.test   | password |
| Organizer | nusrat@gatekeeper.test      | password |
| Attendee  | user@gatekeeper.test        | password |
| Attendee  | sumon@gatekeeper.test       | password |

---

## Switching from SQLite to MySQL

The project ships with **SQLite** for instant boot. To use **MySQL** (as required by the spec):

1. Create a database:
   ```sql
   CREATE DATABASE gatekeeper;
   ```
2. Edit `.env` — comment the SQLite line, uncomment the MySQL block:
   ```env
   # DB_CONNECTION=sqlite
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=gatekeeper
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Re-run `php artisan migrate:fresh --seed`

---

## Email (SMTP) Setup

The default `MAIL_MAILER=log` stores outgoing emails in `storage/logs/laravel.log` so you can verify locally without configuring SMTP.

For real SMTP (e.g., Mailtrap), set in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_user
MAIL_PASSWORD=your_pass
MAIL_FROM_ADDRESS=noreply@gatekeeper.test
MAIL_FROM_NAME="GateKeeper"
```

---

## Architecture

### Database Schema

| Table               | Key Columns                                                                              |
| ------------------- | ---------------------------------------------------------------------------------------- |
| `users`             | id, name, email, password, **role** (`attendee`/`organizer`/`admin`)                    |
| `events`            | id, organizer_id, title, description, date, location, image, status                      |
| `ticket_categories` | id, event_id, name, price, capacity                                                      |
| `tickets`           | id, user_id, event_id, category_id, **unique_code**, payment_status, is_used, checked_in_at |

### Relationships

- `User` → `hasMany(Event)` (as organizer), `hasMany(Ticket)`
- `Event` → `belongsTo(User)`, `hasMany(TicketCategory)`, `hasMany(Ticket)`
- `TicketCategory` → `belongsTo(Event)`, `hasMany(Ticket)`
- `Ticket` → `belongsTo(User)`, `belongsTo(Event)`, `belongsTo(TicketCategory)`

### Routes by Role

| Prefix       | Middleware                  | Purpose                                            |
| ------------ | --------------------------- | -------------------------------------------------- |
| `/`          | (public)                    | Home, browse/view events, about, contact           |
| `/attendee`  | `auth, role:attendee`       | Dashboard, book, my tickets, mark as paid, PDF    |
| `/organizer` | `auth, role:organizer`      | Dashboard, CRUD events + categories, analytics, CSV |
| `/admin`     | `auth, role:admin`          | Manage users + events, system stats               |
| `/scanner`   | `auth, role:organizer,admin`| Webcam QR check-in (POST `/ticket/verify`)         |

### Key Files

```
app/
├── Http/
│   ├── Controllers/{EventController, BookingController, TicketController, AdminController, ScannerController, PageController}.php
│   ├── Middleware/RoleMiddleware.php
│   └── Requests/{StoreEventRequest, UpdateEventRequest, BookTicketRequest}.php
├── Mail/TicketBookedMail.php
└── Models/{User, Event, TicketCategory, Ticket}.php

resources/views/
├── layouts/main.blade.php          ← shared layout
├── home.blade.php
├── about.blade.php  contact.blade.php
├── events/{index, show}.blade.php
├── attendee/{dashboard, book, tickets, ticket_pdf}.blade.php
├── organizer/{dashboard, events/{create, edit, analytics}}.blade.php
├── admin/{dashboard, users/{index, edit}, events}.blade.php
├── scanner/index.blade.php
├── auth/{login, register}.blade.php  (Breeze, restyled)
└── emails/ticket_booked.blade.php

routes/web.php                       ← all routes grouped by role
database/seeders/DatabaseSeeder.php  ← demo users + events + categories + tickets

public/
├── css/app.css                      ← original style.css + GateKeeper additions
└── js/app.js                        ← original script.js
```

---

## Feature Walkthrough

### Use Case 1 — Attendee
1. Register at `/register` (choose "Attendee" role) or login as `user@gatekeeper.test`
2. Browse `/events`, click any event
3. Pick a ticket category, click **Reserve Ticket** → ticket created with status `pending`
4. Visit **My Tickets** (`/attendee/tickets`)
5. Click **Mark as Paid** → fake payment → email sent → ticket becomes `paid`
6. Click **Download PDF** → DomPDF generates a printable ticket with QR code

### Use Case 2 — Organizer
1. Login as `organizer@gatekeeper.test`
2. Dashboard shows totals (events / tickets sold / revenue)
3. Click **+ New Event** → fill title/date/location, add multiple ticket categories with prices and capacities
4. Toggle Publish/Unpublish, view per-category Stats, download attendee CSV

### Use Case 3 — Admin
1. Login as `admin@gatekeeper.test`
2. System-wide dashboard (users, events, tickets, revenue, check-ins)
3. Manage users (search by name/email/role, edit role/password, delete)
4. View / delete any event

### QR Check-in (Webcam Scanner)
1. Login as Organizer or Admin → click **Scanner** in nav (or `/scanner`)
2. Browser asks for camera permission → grant it
3. Point camera at any printed/displayed ticket QR
4. Frontend (html5-qrcode) decodes → AJAX POSTs `/ticket/verify` with `{code}`
5. Backend returns one of:
   - `success` (200) → ticket marked `is_used=true`, `checked_in_at=now()`
   - `pending` (402) → payment not completed
   - `used`    (409) → already checked in
   - `error`   (404) → invalid code
6. UI flashes green/red, plays a beep tone

---

## Verified End-to-End

The following were smoke-tested with real HTTP requests during development:

- ✅ Public routes (`/`, `/events`, `/about`, `/contact`, `/login`, `/register`) → 200
- ✅ Admin login → admin dashboard, users page, events page → 200
- ✅ Organizer login → organizer dashboard, create form → 200
- ✅ Attendee login → dashboard, my tickets, event detail → 200
- ✅ Attendee booking POST creates a new ticket
- ✅ PDF download generates valid `application/pdf` (~880 KB) including QR
- ✅ CSV attendee export generates valid `text/csv`
- ✅ `POST /ticket/verify` returns correct status for all four scenarios:
  - paid+unused → success
  - re-scanned  → already checked in
  - pending     → payment pending
  - invalid     → invalid ticket

---

## Stack

- **Laravel 12** (latest as of writing)
- **PHP 8.4**
- **Laravel Breeze** (Blade auth scaffolding)
- **simplesoftwareio/simple-qrcode** (QR generation)
- **barryvdh/laravel-dompdf** (PDF generation)
- **html5-qrcode** (browser webcam scanner — CDN)
- **MySQL** (production) / **SQLite** (default for demo)

---

## Original Frontend

The static HTML/CSS/JS supplied as the project starting point lives in `_legacy_html/` for reference. All pages have been ported to Blade with dynamic data, while `style.css` was copied to `public/css/app.css` and extended with additional component styles for dashboards, scanner, tables, forms, etc.
