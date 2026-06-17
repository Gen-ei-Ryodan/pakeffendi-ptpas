# Changelog

> Catatan keputusan penting selama pengembangan.

## v1.0 (Initial)

### Keputusan Arsitektur
- **Framework**: Laravel 12 dengan PHP 8.2.
- **Database**: MySQL — menggunakan session-based auth (bukan JWT/Sanctum).
- **Authentication**: Dual guard (`web` untuk users, `customer` untuk buyers).
- **Frontend**: Blade templates + Bootstrap (server-side rendering).
- **Frontend API**: REST API `/api/guest/*` untuk frontend eksternal/SPA.
- **Service Pattern**: Cart logic dipisah ke `CartService`, logging ke `ActivityLogger`.
- **No Repository Pattern**: Query langsung via Eloquen Model/Scope.

### Business Rules
- Order status hanya maju (forward-only): new → on_progress → on_delivery → finished.
- Pricing tier berdasarkan quantity (price_1/disc_1, price_2/disc_2, price_3/disc_3).
- Cart merge dilakukan saat guest login sebagai customer.
- PPN default 11% (disimpan di sales_orders.ppn_percent).

### Database
- Primary keys string untuk product_categories (`category_code`) dan product_brands (`brand_code`).
- Order number format: `W` + `YYMMDD` + `0001` (4 digit, reset per hari).
- Cart menggunakan session_id (UUID) untuk guest, customer_id untuk buyer.

### Middleware
- `EnsureGuestLogin`: Allow customer (guard `customer`) dan sales (guard `web`) — admin dialihkan ke login.
- `EnsureAdmin`: Allow admin/super admin — sales & customer ditolak.
- `EnsureSales`: Allow role `sales` only.

### Email
- Email verifikasi untuk buyer baru (6 digit code).
- Email change password (6 digit code).

### Perubahan Besar
- Migrasi dari sistem guest order API ke sistem cart + checkout terintegrasi.
- Penambahan sistem verifikasi email untuk buyer.

## Catatan
- Dokumentasi ini diperbarui setiap kali ada keputusan arsitektur/bisnis signifikan.
- Update kecil (bug fix, minor feature) tidak perlu dicatat di sini.
