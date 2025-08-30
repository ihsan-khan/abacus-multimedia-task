````markdown
# 🚀 Laravel Checkout API

A **Laravel 10-based Checkout System** for **Abacus Multimedia** with authentication, order processing, and **user activity tracking** (login & online duration).

---

## 📦 Requirements

- PHP **8.1+**
- MySQL **5.7+**
- Composer

---

## ⚙️ Installation

1️⃣ **Clone Repository**
```bash
git clone <repository-url>
cd abacus-multimedia-task
````

2️⃣ **Install Dependencies**

```bash
composer install
```

3️⃣ **Copy Environment File**

```bash
cp .env.example .env
```

4️⃣ **Generate App Key**

```bash
php artisan key:generate
```

5️⃣ **Configure Database in `.env`**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=abacus_checkout
DB_USERNAME=root
DB_PASSWORD=
```

6️⃣ **Run Migrations & Seeders**

```bash
php artisan migrate --seed
```

7️⃣ **Start Server**

```bash
php artisan serve
```

---

## 🔑 API Endpoints

### 🧑 Authentication

* `POST /api/login` → Login user
* `POST /api/logout` → Logout user *(requires token)*

### 🛒 Checkout

* `GET /api/checkout` → Fetch checkout page data *(requires token)*
* `POST /api/checkout` → Process checkout *(requires token)*

### 📦 Cart

* `GET /api/cart` → Get user’s cart *(requires token)*
* `POST /api/cart/add` →  Add item to cart *(requires token)*

### 📊 User Activity

* `GET /user/login-duration` → Get user’s login duration
* `GET /user/online-duration` → Get user’s **active online duration**

---

## 📌 Example Requests

### 🔐 Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password"
  }'
```

### 🛒 Get Checkout Data

```bash
curl -X GET http://localhost:8000/api/checkout \
  -H "Authorization: Bearer <token>"
```

### ✅ Process Checkout

```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "shipping_address": "123 Main St, City, Country",
    "payment_method": "stripe"
  }'
```

---

# 📊 User Activity Tracking

This system automatically tracks **login duration** and **online duration** of users.

### 🔹 Key Concepts

1. **Login Duration**

   * Time between **login → logout**.
   * Calculated using `login_at` & `logout_at` in `user_sessions`.

2. **Online Duration**

   * Real-time activity since login.
   * Uses `last_activity_at` to check if the user is **active or idle**.

---

## 🔄 How It Works

1. **On Login**

   * Creates new session (`login_at = now()`).
   * Sets `last_activity_at = now()`.

2. **On Each API Request**

   * Middleware updates `last_activity_at`.

3. **On Logout**

   * Updates `logout_at = now()` in session.

4. **Calculating Online Duration**

   * If `logout_at` exists → use it.
   * If still logged in:

     * If `last_activity_at > now() - 5 mins` → user is **active**.
     * Else → user is **idle**.

5. **Active vs Idle**: 

  * If last_activity_at > now() - 5 minutes → active * Else → idle
---

## 📖 Example Scenarios

✅ **Active User**

* Login: `10:00` → Last activity: `11:15` → Current: `11:20`
* **Online duration:** 80 mins
* **Status:** `active`

✅ **Idle User**

* Login: `10:00` → Last activity: `10:30` → Current: `11:00`
* **Online duration:** 30 mins
* **Status:** `idle`



---

## 🟢 Example API Response

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

---

## ✨ Features Implemented

✅ API-based checkout system (Laravel 10 + MySQL)

✅ Fetch checkout page data via API

✅ Complete checkout process with order creation

✅ Simulated payment processing (Stripe-ready)

✅ Store order & payment details in DB

✅ Track **login duration** of users

✅ Track **online duration** (active vs idle)



