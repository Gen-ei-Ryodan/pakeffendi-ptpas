# Changelog

> Catatan keputusan penting selama pengembangan.

## Guest Home Redesign (Frontend)

### Keputusan Desain
- **Arah visual "Trading Floor"** untuk halaman `guest/home` (scoped di `.home-page`): palet navy-ledger (`#07294a`/`#003366`) + amber aksen (`#f59e0b`), tidak mengubah brand warna global.
- **Typography**: display face Space Grotesk (dimuat hanya di halaman home via `@stack('styles')`), body tetap Inter.
- **Signature**: *market ticker* — band marquee kategori di bawah hero (linear motion, pause on hover, konten diduplikasi via JS agar loop seamless, nonaktif saat `prefers-reduced-motion`).
- **Motion**: hero entrance (ease-out-quint), scroll reveal via `IntersectionObserver` (hanya `opacity` + `transform`), feedback tekan `scale(0.97)` saat pointer-down, hover lift dibatasi `@media (hover:hover)`, carousel jadi crossfade (`carousel-fade`).
- **Aksesibilitas**: semua animasi mati/reduksi saat `prefers-reduced-motion`; chip hero solid saat `prefers-reduced-transparency`; `focus-visible` ring; hover hanya di perangkat pointer halus.

### Perubahan Behavior
- **Kartu kategori desktop** (sebelumnya hanya notifikasi yang rusak) sekarang menavigasi ke `/products?category_id=...`, konsisten dengan tile mobile; support keyboard Enter/Space.
- **Form newsletter** diperbaiki: sebelumnya `document.querySelector('form')` menarget form pencarian header (bug), sekarang ditarget via `#newsletterForm` + validasi email.

### File
- `resources/views/guest/home.blade.php` (restrukturisasi + `@push('styles')`/`@push('scripts')`)
- `public/guest/css/app.css` (tambah section scoped `.home-page` di akhir file)

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
- Menambah produk yang sudah ada di cart TIDAK menambah quantity; muncul notifikasi "Barang sudah ada di keranjang." (quantity hanya diubah via halaman cart).

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
- Email order dikirim per penerima agar alamat yang gagal tidak menghentikan penerima lain.
- Penerima admin diambil otomatis dari semua user admin/super admin di database; alamat dummy `.local` di-skip.

### Perubahan Besar
- Migrasi dari sistem guest order API ke sistem cart + checkout terintegrasi.
- Penambahan sistem verifikasi email untuk buyer.

## Catatan
- Dokumentasi ini diperbarui setiap kali ada keputusan arsitektur/bisnis signifikan.
- Update kecil (bug fix, minor feature) tidak perlu dicatat di sini.
