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

## 📚 API Reference

### Authentication

#### Login
- **POST** `/api/login`
- **Body:**  
  ```json
  {
    "email": "test@example.com",
    "password": "password"
  }
  ```
- **Response:**  
  ```json
  {
    "token": "1|YzcJnKJq8VajiPlnD7ooIpxaUh4qUciGvWRymD9G580b43a7",
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com",
        "email_verified_at": "2025-08-31T19:19:24.000000Z",
        "created_at": "2025-08-31T19:19:24.000000Z",
        "updated_at": "2025-08-31T19:19:29.000000Z",
        "last_activity_at": "2025-08-31T19:19:29.000000Z",
        "last_seen_at": null,
        "current_session_start": null,
        "inactivity_threshold": null,
        "total_online_seconds": 0
        }
    }
  ```

#### Logout
- **POST** `/api/logout`
- **Headers:**  
  `Authorization: Bearer <token>`
- **Response:**  
  ```json
  {
    "message": "Logged out successfully"
  }
  ```

---

### Cart

#### Get Cart Items
- **GET** `/api/cart`
- **Headers:**  
  `Authorization: Bearer <token>`
- **Response:**  
  ```json
    {
    "cart_items": [
        {
            "id": 1,
            "cart_id": 1,
            "product_id": 1,
            "quantity": 1,
            "price": "129.99",
            "created_at": "2025-08-31T19:20:49.000000Z",
            "updated_at": "2025-08-31T19:20:49.000000Z",
            "product": {
                "id": 1,
                "name": "Wireless Headphones",
                "description": "High-quality wireless headphones with noise cancellation",
                "price": "129.99",
                "stock": 50,
                "image": "headphones.jpg",
                "is_active": 1,
                "created_at": "2025-08-31T19:19:24.000000Z",
                "updated_at": "2025-08-31T19:19:24.000000Z"
            }
        }
    ],
    "summary": {
        "subtotal": 129.99,
        "item_count": 1,
        "unique_items": 1
        }
    }
  ```

#### Add Item to Cart
- **POST** `/api/cart/add`
- **Headers:**  
  `Authorization: Bearer <token>`
- **Body:**  
  ```json
  {
    "product_id": 1,
    "quantity": 1
  }
  ```
- **Response:**  
  ```json
  {
    "message": "Product added to cart",
    "cart_item": {
        "cart_id": 1,
        "product_id": "1",
        "quantity": "1",
        "price": 129.99,
        "updated_at": "2025-08-31T19:20:49.000000Z",
        "created_at": "2025-08-31T19:20:49.000000Z",
        "id": 1,
        "product": {
            "id": 1,
            "name": "Wireless Headphones",
            "description": "High-quality wireless headphones with noise cancellation",
            "price": "129.99",
            "stock": 50,
            "image": "headphones.jpg",
            "is_active": 1,
            "created_at": "2025-08-31T19:19:24.000000Z",
            "updated_at": "2025-08-31T19:19:24.000000Z"
        }
        }
    }
  ```

---

### Checkout

#### Get Checkout Summary/show cart
- **GET** `/api/checkout`
- **Headers:**  
  `Authorization: Bearer <token>`
- **Response:**  
  ```json
  {
    "cart_items": [
        {
            "id": 1,
            "cart_id": 1,
            "product_id": 1,
            "quantity": 1,
            "price": "129.99",
            "created_at": "2025-08-31T19:20:49.000000Z",
            "updated_at": "2025-08-31T19:20:49.000000Z",
            "product": {
                "id": 1,
                "name": "Wireless Headphones",
                "description": "High-quality wireless headphones with noise cancellation",
                "price": "129.99",
                "stock": 50,
                "image": "headphones.jpg",
                "is_active": 1,
                "created_at": "2025-08-31T19:19:24.000000Z",
                "updated_at": "2025-08-31T19:19:24.000000Z"
            }
        }
    ],
    "summary": {
        "subtotal": 129.99,
        "tax": 12.999000000000002,
        "shipping": 5,
        "total": 147.989
        }
    }
  ```

#### Process Checkout
- **POST** `/api/checkout`
- **Headers:**  
  `Authorization: Bearer <token>`
- **Body:**  
  ```json
  {
    "shipping_address": "123 Main St",
    "payment_method_id": "pm_card_visa"
  }
  ```
- **Response:**  
  ```json
  {
    "message": "Order placed successfully",
    "order_number": "ORD-ZMFVIQ4GBX"
    }
  ```

---

### User Activity

#### Get Current Login Duration
- **GET** `/api/user/login-duration`
- **Headers:**  
  `Authorization: Bearer <token>`
- **Response:**  
  ```json
  {
    "status": true,
    "data": {
        "login_at": "2025-08-31 19:19:29",
        "duration_in_minutes": 6.7,
        "is_active": true
        }
    }
  ```

#### Get All Login Durations
- **GET** `/api/user/login-durations`
- **Headers:**  
  `Authorization: Bearer <token>`
- **Response:**  
  ```json
  {
    "status": true,
    "data": {
        "sessions": [
            {
                "login_at": "2025-08-31 19:27:21",
                "logout_at": null,
                "duration_in_minutes": 0.18,
                "is_active": true
            },
            {
                "login_at": "2025-08-31 19:19:29",
                "logout_at": null,
                "duration_in_minutes": 8.05,
                "is_active": true
            }
        ],
        "total_duration": {
            "seconds": 494,
            "minutes": 8.23,
            "hours": 0.14
        }
        }
    }
  ```

#### Get Online/Idle Stats
- **GET** `/api/user/online-duration`
- **Headers:**  
  `Authorization: Bearer <token>`
- **Response:**  
  ```json
  {
    "current_session": {
        "started_at": "2025-08-31 19:24:50",
        "duration_seconds": 110,
        "duration_formatted": "00:01:50",
        "expires_in_seconds": 30,
        "will_expire_at": "2025-08-31 19:27:11"
    },
    "total_online": {
        "seconds": 351,
        "formatted": "00:05:51"
    },
    "last_activity": "2025-08-31 19:26:11",
    "is_online": true,
    "session_active": true
    }
  ```

### How Stripe Is Used

- When a user submits a checkout request (`POST /api/checkout`), the backend receives a `payment_method_id` (such as `"pm_card_visa"` for testing).
- The `CheckoutController` calls the `PaymentService`, which interacts with Stripe’s API to process the payment for the order total.
- If Stripe confirms the payment (`status: succeeded`), the order status is updated to `completed` and the cart is cleared.
- If the payment fails, the order status is set to `failed` and the transaction is rolled back.


## ✨ Features Implemented

✅ API-based checkout system (Laravel 10 + MySQL)

✅ Fetch checkout page data via API

✅ Complete checkout process with order creation

✅ Simulated payment processing (Stripe-ready)

✅ Store order & payment details in DB

✅ Track **login duration** of users

✅ Track **online duration** (active vs idle)



