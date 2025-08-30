```markdown
# Laravel Checkout API

A Laravel 10-based API checkout system for Abacus Multimedia.

# Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer

# Installation


1. Clone the repository:
```bash
git clone <repository-url >
cd abacus-multimedia-task
```

2. Install dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=abacus_checkout
DB_USERNAME=root
DB_PASSWORD=
```

6. Run migrations and seeders:
```bash
php artisan migrate --seed
```

7. Start the development server:
```bash
php artisan serve
```

## API Endpoints

### Authentication
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user (requires authentication)

### Checkout
- `GET /api/checkout` - Get checkout page data (requires authentication)
- `POST /api/checkout` - Process checkout (requires authentication)

### Orders
- `GET /api/orders` - Get user orders (requires authentication)


### User Activity
- `GET /user/login-duration` - Get user login duration (requires authentication)
- `GET /user/online-duration` - Get user online duration (requires authentication)


## Example Requests


### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password"
  }'
```

### Get Checkout Data
```bash
curl -X GET http://localhost:8000/api/checkout \
  -H "Authorization: Bearer <token>"
```

### Process Checkout
```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "shipping_address": "123 Main St, City, Country",
    "payment_method": "stripe"
  }'
```

## User Activity Tracking

The system automatically tracks:
- Login duration (time between login and logout)
- Online duration (time between login and last activity)

Access this data via the `/api/user/activity` endpoint.
```

Got it ✅
Here’s a **detailed explanation (documentation style)** you can directly put into your project’s **README.md** to explain the concept of calculating **online duration of an authenticated user**.

---

# 🔹 Online Duration Tracking (Laravel API)

This project implements a **Login & Online Duration tracking system** using **Laravel 10 + Sanctum**.
It records how long a user has been logged in and whether they are **actively online** or just **idle**.

---

## 🔹 Key Concepts

1. **Login Duration**

   * The total time a user spends between **login** and **logout**.
   * Calculated from `login_at` → `logout_at` in the `user_sessions` table.

2. **Online Duration**

   * The real-time duration the user has been **active** since login.
   * Uses `last_activity_at` to measure activity.
   * If the user is idle (no API calls for X minutes), they are considered **inactive**, even if not logged out.

## 🔹 How It Works

### 1. **On Login**

* A new record is created in `user_sessions` with `login_at = now()`.
* User’s `last_activity_at` is set to `now()`.

### 2. **On Each API Request**

* A custom middleware (`UpdateUserActivity`) updates the user’s `last_activity_at` field.
* This ensures the system knows the user is still **active**.

### 3. **On Logout**

* The latest `user_sessions` record is updated with `logout_at = now()`.
* The session is then marked as complete.

### 4. **Calculating Online Duration**

When fetching online duration:

* Get the **latest session** (`login_at`).
* Determine **end time**:

  * If `logout_at` exists → use `logout_at`.
  * If still logged in:

    * If `last_activity_at` is within **5 minutes** → consider user **active**, use `now()`.
    * Otherwise → consider user **idle**, use `last_activity_at`.


## 🔹 Example Scenarios

### ✅ Case 1: User is Active

* Login at `10:00`
* Last API request at `11:15`
* Current time `11:20`
* Online duration = `10:00 → 11:20` (80 mins)
* `is_active = true`

### ✅ Case 2: User Idle

* Login at `10:00`
* Last API request at `10:30`
* Current time `11:00` (no requests for 30 mins)
* Online duration = `10:00 → 10:30` (30 mins)
* `is_active = false`



## 🔹 API Response Example

```json
{
  "status": true,
  "online_duration": {
    "seconds": 5400,
    "minutes": 90,
    "hours": 1.5
  },
  "is_active": false,
  "last_activity_at": "2025-08-30T21:40:00"
}
```

5. **Active vs Idle**:

   * If `last_activity_at > now() - 5 minutes` → active
   * Else → idle

---

✅ With this logic, you get a **reliable measure of how long users are online**, distinguishing between **truly active users** and those who are just **logged in but idle**.


## Key Features Implemented

1. ✅ API-based checkout system using Laravel 10 and MySQL
2. ✅ Views data on a checkout page via API
3. ✅ Completes a checkout process with order creation
4. ✅ Simulated payment processing (can be extended with Stripe)
5. ✅ Stores order/payment details in MySQL
6. ✅ Calculates login duration of authenticated users
7. ✅ Calculates online duration of authenticated users 