# Architecture

## Pola Arsitektur

**Laravel MVC (Model-View-Controller)** — standar Laravel.

- **Model** → Eloquent ORM (`app/Models/`)
- **View** → Blade templates (`resources/views/`)
- **Controller** → Logic routing (`app/Http/Controllers/`)

## Frontend

- **Admin Panel**: Blade + Bootstrap (server-side rendering).
- **Guest Web**: Blade + Bootstrap + vanilla JS (client-side interactivity via partials).
- **External Frontend**: REST API (`/api/guest/*`) untuk frontend SPA/mobile.

## Backend

### Route Structure

| File            | Prefix       | Middleware        | Deskripsi                    |
|-----------------|--------------|-------------------|------------------------------|
| `web.php`       | `/admin/*`   | `admin`           | Panel admin                  |
| `guest.php`     | `/*`         | —                 | Guest area (include dari web.php) |
| `api.php`       | `/api/guest/*`| —                | REST API untuk guest         |

### Controller Layer

- **Admin Controllers** (`Admin/`): CRUD data master, kelola order, log.
- **Guest Controllers** (`Guest/`): Login, cart, checkout, profile, orders, address.
- **Api Controllers** (`Api/Guest/`): REST endpoints untuk frontend eksternal.

## Service Pattern

Service layer untuk logika bisnis yang kompleks:

| Service              | Lokasi                        | Fungsi |
|----------------------|-------------------------------|--------|
| **CartService**      | `app/Services/CartService.php` | Resolve cart (session/customer), add/remove/update items, merge cart, checkout |
| **ActivityLogger**   | `app/Services/ActivityLogger.php` | Static helper untuk mencatat aktivitas admin ke tabel `activity_logs` |

## Repository Pattern

**Tidak digunakan.** Query langsung via Eloquent Model / Scope.

## State Management

- **Cart state**: Disimpan di DB (`carts` + `cart_items`).
  - Guest: diikat oleh `session_id` (cookie `pas_cart_sid`).
  - Customer: diikat oleh `customer_id`.
  - Sales: diikat oleh `customer_id` + `sales_id`.
- **Auth state**: Session-based (Laravel session).

## Authentication

Dua guard autentikasi:

| Guard      | Model            | Provider   | Login Page        |
|------------|------------------|------------|-------------------|
| `web`      | `App\Models\User`| `users`    | `/admin/login` atau `/login` (tergantung role) |
| `customer` | `App\Models\Customer` | `customers` | `/login` |

### Middleware

| Middleware      | Route Alias    | Fungsi |
|-----------------|----------------|--------|
| `EnsureAdmin`   | `admin`        | Hanya admin/super admin bisa akses `/admin/*` |
| `EnsureGuestLogin` | `guest.auth` | Customer (guard `customer`) atau Sales (guard `web`) bisa akses cart, profile, orders |
| `EnsureSales`   | `sales`        | Hanya role `sales` bisa akses (my-customers, cart select-customer) |

### Flow Login

1. User masuk ke `/login`.
2. Cek guard `customer` (email/HP) → jika cocok → login sebagai Buyer.
3. Jika gagal, cek guard `web` (email) → jika cocok → cek role:
   - `sales` → login sebagai Sales.
   - `admin` → ditolak (harus login di `/admin/login`).
4. Admin login via `/admin/login` → guard `web` → cek role admin/superadmin.

## Folder Structure Detail

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── AboutController.php
│   │   │   ├── AccountController.php
│   │   │   ├── AuthController.php
│   │   │   ├── BroadcastController.php
│   │   │   ├── CustomerController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── FavoriteBrandController.php
│   │   │   ├── LogbookController.php
│   │   │   ├── ProductBrandController.php
│   │   │   ├── ProductCategoryController.php
│   │   │   ├── ProductController.php
│   │   │   ├── ProductImageController.php
│   │   │   ├── ProductStatusController.php
│   │   │   └── SalesOrderController.php
│   │   ├── Api/Guest/
│   │   │   ├── GuestHomeApiController.php
│   │   │   ├── GuestOrderApiController.php
│   │   │   └── GuestProductApiController.php
│   │   └── Guest/
│   │       ├── AuthController.php
│   │       ├── CartController.php
│   │       ├── CustomerAddressController.php
│   │       ├── HomeController.php
│   │       ├── MyCustomerController.php
│   │       ├── OrderController.php
│   │       ├── ProfileController.php
│   │       └── RegionController.php
│   └── Middleware/
│       ├── EnsureAdmin.php
│       ├── EnsureGuestLogin.php
│       └── EnsureSales.php
├── Models/
│   ├── AboutPage.php
│   ├── ActivityLog.php
│   ├── Broadcast.php
│   ├── Cart.php
│   ├── CartItem.php
│   ├── Customer.php
│   ├── CustomerAddress.php
│   ├── FavoriteBrand.php
│   ├── Product.php
│   ├── ProductBrand.php
│   ├── ProductCategory.php
│   ├── ProductImage.php
│   ├── ProductStatus.php
│   ├── ProductVariant.php
│   ├── ProductVariantItem.php
│   ├── SalesOrder.php
│   ├── SalesOrderItem.php
│   └── User.php
├── Services/
│   ├── ActivityLogger.php
│   └── CartService.php
```
