# Project Context

## Nama Project
**PAS** — Product Sales System (d/h Point of Sales)

## Tujuan Project
Sistem penjualan produk untuk **CV Sumber Sejahtera**. Memungkinkan:
- **Buyer (Customer)** browsing produk, keranjang, checkout, dan melihat riwayat order.
- **Sales** membuatkan order untuk buyer, mengelola data buyer.
- **Admin** mengelola data master (produk, brand, kategori, status, broadcast, dll.) dan memproses order.

## Tech Stack

| Layer      | Teknologi                              |
|------------|----------------------------------------|
| Backend    | Laravel 12, PHP ^8.2                   |
| Frontend   | Blade templates + Bootstrap + Vite     |
| Database   | MySQL                                  |
| Auth       | Session-based (2 guards: `web`, `customer`) |
| Mail       | Laravel Mail (SMTP)                    |

## Modul Utama

| Modul            | Area          | Deskripsi |
|------------------|---------------|-----------|
| Auth             | Guest, Admin  | Login multi-guard (Buyer via `customer` guard, Sales/Admin via `web` guard) |
| Product          | Admin, Guest  | CRUD produk, brand, kategori, status, varian, relasi produk |
| Cart & Checkout  | Guest         | Keranjang belanja berbasis session/customer, merge cart, tier pricing |
| Sales Order      | Admin, Guest  | Order dari buyer, proses update status (new → on_progress → on_delivery → finished) |
| Customer/Buyer   | Admin, Guest  | Registrasi buyer oleh Sales/Admin, approval, manajemen alamat |
| Broadcast        | Admin         | Banner/pengumuman di halaman utama |
| Activity Log     | Admin         | Log aktivitas admin |
| API              | Guest         | REST API (sync, home, products, create order) untuk frontend eksternal |

## User Role

| Role         | Guard    | Area Akses                         | Login Route     |
|--------------|----------|------------------------------------|-----------------|
| Admin        | `web`    | Panel admin (`/admin/*`)           | `/admin/login`  |
| Super Admin  | `web`    | Panel admin (`/admin/*`)           | `/admin/login`  |
| Sales        | `web`    | Guest area (cart, profile, orders) | `/login`        |
| Buyer        | `customer` | Guest area (cart, profile, orders) | `/login`        |
| Guest        | —        | Browse produk saja                 | —               |

## Flow Bisnis Ringkas

1. **Buyer** login → cart → checkout → order masuk status `new`.
2. **Sales** login → pilih buyer yang terdaftar → buatkan cart → checkout atas nama buyer.
3. **Admin** lihat order di panel → update status (on_progress → on_delivery → finished).
4. **Guest** (tidak login) bisa browsing produk tapi tidak bisa cart/checkout.

## Struktur Folder

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/        # Admin panel controllers
│   │   │   ├── Api/Guest/    # REST API controllers (guest)
│   │   │   └── Guest/        # Guest area controllers (web)
│   │   └── Middleware/       # EnsureAdmin, EnsureGuestLogin, EnsureSales
│   ├── Mail/                 # BuyerVerificationMail, ChangePasswordMail
│   ├── Models/               # Eloquent models
│   ├── Providers/
│   └── Services/             # CartService, ActivityLogger
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── docs/
├── public/
│   └── guest/                # Frontend assets (CSS, JS, images)
├── resources/
│   └── views/
│       ├── admin/            # Admin panel views
│       ├── emails/           # Email templates
│       └── guest/            # Guest area views (home, products, cart, etc.)
├── routes/
│   ├── api.php               # API routes
│   ├── guest.php             # Guest web routes
│   └── web.php               # Admin web routes (includes guest.php)
└── tests/
    ├── Feature/
    └── Unit/
```
