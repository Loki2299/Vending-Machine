# Vending Machine Coding Test

Laravel application location: `/Users/thawzin/Documents/myProject/Coding Test`

## Database Design

Use MySQL database `VendingMachingDB`.

- `users`: `id`, `name`, `email`, `role` (`admin` or `user`), `password`, timestamps. Passwords are hashed by Laravel.
- `products`: `id`, `name`, `price decimal(10,3)`, `quantity_available`, timestamps.
- `transactions`: `id`, `user_id`, `product_id`, `quantity`, `unit_price`, `total_price`, timestamps.

Relationships:

- One user has many transactions.
- One product has many transactions.
- Each transaction belongs to exactly one user and one product.

Laravel uses PDO through its MySQL database driver. Product CRUD and purchasing are kept behind `App\Contracts\ProductServiceInterface` and implemented by `App\Services\ProductService`, then injected into `ProductsController`. A low-level PDO example is also included in `App\Database\PdoConnection` and `App\Database\PdoVendingRepository`.

## Features

- Session authentication with login, registration, logout, and hashed passwords.
- Role-based access control with `admin` middleware.
- Admin-only product create, edit, update, delete views.
- Public product listing with pagination and sorting.
- Authenticated product purchase flow at SEO-friendly URLs such as `/products/1/buy`.
- Purchase action uses a PHP attribute: `#[Post('/products/{product}/buy', name: 'products.purchase.store')]`.
- Server-side validation and browser-side validation for product forms and purchases.
- REST API under `/api`, with signed bearer token login via `/api/login`.
- PHPUnit tests with dependency injection and mocked `ProductServiceInterface`.

## Seeded Accounts

- Admin: `admin@example.com` / `password`
- User: `user@example.com` / `password`

Seeded products:

- Coke: `3.990`
- Pepsi: `6.885`
- Water: `0.500`

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

## Railway Deployment

1. Push this folder to GitHub.
2. Create a Railway project from the GitHub repo.
3. Add a MySQL service and set these variables:
   - `APP_KEY`
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `DB_CONNECTION=mysql`
   - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
4. Railway will use `railway.json` to run migrations, seed data, and start the app.
