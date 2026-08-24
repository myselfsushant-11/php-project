# CINEFILE — Movie Ticket Booking System

A complete PHP + MySQL + Bootstrap 5 + AJAX movie ticket booking system, inspired by the
UX of modern cinema platforms (QuickShow) but built with 100% original code. The brand
name **CINEFILE** reads as film-culture rather than generic ticketing — a nod to the
people who show up for every screening, not just the blockbusters.

---

## 1. Requirements

- XAMPP (Apache + MySQL + PHP 8+) — https://www.apachefriends.org
- A modern browser

## 2. Installation

1. Copy the entire `cinefile` folder into your XAMPP `htdocs` directory so the path is:
   ```
   C:\xampp\htdocs\cinefile\
   ```
   (You can rename the folder if you like — just update the URL accordingly.)

2. Start **Apache** and **MySQL** from the XAMPP Control Panel.

3. Open **phpMyAdmin** at `http://localhost/phpmyadmin`.

4. Create nothing manually — just click **Import**, choose `database.sql` from this
   project, and run it. This will:
   - Create the `movie_ticket_booking` database
   - Create all tables with proper foreign keys
   - Seed 6 demo movies, 3 halls, and a set of showtimes for the next few days
   - Auto-generate seats for every seeded showtime
   - Create a default admin account

5. Visit the site:
   - **User site:** http://localhost/cinefile/
   - **Admin console:** http://localhost/cinefile/admin/login.php

## 3. Default Admin Login

```
Email:    admin@cinefile.com
Password: Admin@123
```

Change this password immediately in a real deployment (there is no self-service admin
password change UI yet — update it directly in the `admins` table using
`password_hash()`-generated values, or extend `admin/profile.php` yourself).

## 4. Default Config

Database credentials live in `config/database.php`:

```php
$DB_HOST = 'localhost';
$DB_NAME = 'movie_ticket_booking';
$DB_USER = 'root';
$DB_PASS = '';
```

This matches a stock XAMPP install (root user, no password). Adjust if your MySQL is
configured differently.

## 5. Folder Structure

```
cinefile/
├── index.php, login.php, register.php, movies.php, movie.php,
│   seat-selection.php, checkout.php, booking-success.php,
│   print-ticket.php, my-bookings.php, profile.php, logout.php
├── config/database.php
├── includes/ (auth.php, functions.php, header.php, navbar.php, footer.php)
├── ajax/ (register, login, logout, search_movies, get_showtimes,
│          get_seats, create_booking, get_user_bookings,
│          update_profile, change_password)
├── admin/
│   ├── login.php, logout.php, dashboard.php, movies.php,
│   │   showtimes.php, halls.php, bookings.php, users.php
│   ├── includes/ (header.php, sidebar.php, footer.php)
│   └── ajax/ (login, movies, halls, showtimes, bookings, users)
├── assets/
│   ├── css/ (style.css, admin.css)
│   ├── js/ (app.js, admin.js)
│   └── images/ (movies/, uploads/)
└── database.sql
```

## 6. Key Features

- **Booking flow:** Home → Movies → Movie Details → Date/Showtime (AJAX) → Seat
  Selection (live seat map + instant price calc) → Login gate → Checkout → Confirm →
  Digital Ticket → My Bookings.
- **Double-booking prevention:** `ajax/create_booking.php` wraps the whole booking in a
  MySQL transaction, re-checks and row-locks (`SELECT ... FOR UPDATE`) the requested
  seats, and only commits if every seat is still available. If a seat was taken a moment
  earlier by someone else, the customer gets a clear "Seat X is no longer available"
  message and is redirected back to a refreshed seat map.
- **Admin console:** movie CRUD (with poster/backdrop upload), hall management (define
  rows × seats-per-row layout), showtime scheduling (seats auto-generate on creation),
  booking overview with multi-field filters, read-only user directory (no plaintext
  passwords, ever), and a live dashboard (revenue, totals, popular movies, recent
  activity).
- **Security:** PDO prepared statements everywhere, `password_hash()` /
  `password_verify()`, PHP sessions for both user and admin auth (kept separate),
  server-side validation on every AJAX endpoint, `htmlspecialchars()` on all echoed
  user input, restricted file uploads (jpg/jpeg/png/webp, size-capped, unique filenames,
  stored as paths not blobs).
- **No payment gateway** — bookings are marked "Confirmed" for payment status by design,
  per the academic project scope. Wiring in Khalti/eSewa later only touches
  `checkout.php` and `ajax/create_booking.php`.

## 7. Notes for Extending

- To add more movie posters/backdrops, drop images into `assets/images/movies/` via the
  admin Movies page — uploads are saved there automatically with unique filenames.
- Seat layout per hall is fully configurable from **Admin → Halls**; every new showtime
  created against a hall regenerates its own independent seat map.
- All AJAX endpoints return a consistent `{ success: bool, message: string, ... }`
  shape, so it's straightforward to add new ones following the same pattern.
