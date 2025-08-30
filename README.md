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
- `POST /api/register` - Register a new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user (requires authentication)

### Checkout
- `GET /api/checkout` - Get checkout page data (requires authentication)
- `POST /api/checkout` - Process checkout (requires authentication)

### Orders
- `GET /api/orders` - Get user orders (requires authentication)
- `GET /api/orders/{order}` - Get specific order details (requires authentication)

### User Activity
- `GET /api/user/activity` - Get user login/online duration (requires authentication)

## Example Requests

### Register User
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
  }'
```

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

## Testing Payments

The payment system is simulated for demonstration purposes. By default, all payments succeed. To test failure scenarios, modify the `processPayment` method in `CheckoutController.php`.

## User Activity Tracking

The system automatically tracks:
- Login duration (time between login and logout)
- Online duration (time between login and last activity)

Access this data via the `/api/user/activity` endpoint.
```

## Key Features Implemented

1. ✅ API-based checkout system using Laravel 10 and MySQL
2. ✅ Views data on a checkout page via API
3. ✅ Completes a checkout process with order creation
4. ✅ Simulated payment processing (can be extended with Stripe)
5. ✅ Stores order/payment details in MySQL
6. ✅ Calculates login duration of authenticated users
7. ✅ Calculates online duration of authenticated users