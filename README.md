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



## ✨ Features Implemented

✅ API-based checkout system (Laravel 10 + MySQL)

✅ Fetch checkout page data via API

✅ Complete checkout process with order creation

✅ Simulated payment processing (Stripe-ready)

✅ Store order & payment details in DB

✅ Track **login duration** of users

✅ Track **online duration** (active vs idle)



