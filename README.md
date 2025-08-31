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

## 📚 API Endpoints

| Method | Endpoint                       | Description                       | Auth Required |
|--------|-------------------------------|-----------------------------------|--------------|
| POST   | `/api/login`                  | User login                        | No           |
| POST   | `/api/logout`                 | User logout                       | Yes          |
| GET    | `/api/cart`                   | Get cart items & summary          | Yes          |
| POST   | `/api/cart/add`               | Add item to cart                  | Yes          |
| GET    | `/api/checkout`               | Get checkout summary              | Yes          |
| POST   | `/api/checkout`               | Process checkout & payment        | Yes          |
| GET    | `/api/user/login-duration`    | Get current login duration        | Yes          |
| GET    | `/api/user/login-durations`   | Get all login durations           | Yes          |
| GET    | `/api/user/online-duration`   | Get online/idle stats             | Yes          |

## 🧑‍💻 Example Usage

### Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'
```
### Logout

```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer <token>"
```


### Get Cart items

```bash
curl -X GET http://localhost:8000/api/cart \
  -H "Authorization: Bearer <token>"
```

### Add Item to Cart

```bash
curl -X POST http://localhost:8000/api/cart/add \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"product_id":1,"quantity":2}'
```

### Get Checkout Summary/show cart

```bash
curl -X GET http://localhost:8000/api/checkout \
  -H "Authorization: Bearer <token>"
```

### Checkout

```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"shipping_address":"123 Main St","payment_method_id":"pm_card_visa"}'
```

### Get Current Login Duration

```bash
curl -X GET http://localhost:8000/api/user/login-duration \
  -H "Authorization: Bearer <token>"
```

### Get All Login Durations

```bash
curl -X GET http://localhost:8000/api/user/login-durations \
  -H "Authorization: Bearer <token>"
```

### Get Online/Idle Stats

```bash
curl -X GET http://localhost:8000/api/user/online-duration \
  -H "Authorization: Bearer <token>"
```


## ✨ Features Implemented

✅ API-based checkout system (Laravel 10 + MySQL)

✅ Fetch checkout page data via API

✅ Complete checkout process with order creation

✅ Simulated payment processing (Stripe-ready)

✅ Store order & payment details in DB

✅ Track **login duration** of users

✅ Track **online duration** (active vs idle)



