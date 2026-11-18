# Changelog

> Catatan keputusan penting selama pengembangan.

## Fix Cart "Item Balik Lagi" + Optimasi Gambar WebP

### Keputusan Desain
- **Konteks customer sales dibaca dari cookie DAN session** (`pas_sales_cid`): cookie tetap primer, session sebagai fallback saat cookie hilang/gagal decrypt — sehingga request AJAX/fetch DELETE tidak pernah diam-diam jatuh ke fallback guest session cart saat customer (mis. "Testing Vina") sedang dipilih.
- **Mutasi cart (`removeItem`, `clear`, `setItemQuantity`, `updateItemNotes`) wajib konteks customer yang valid untuk sales** — jika tidak ada customer terpilih → `422 "Silakan pilih customer terlebih dahulu di halaman Keranjang."` (sama seperti `addItem`), bukan operasi senyap ke cart guest. Halaman baca (`index`, `summary`, `count`) tetap toleran.
- **Merge guest→customer cart hanya untuk flow login buyer** (`$salesId === null`, sesuai BUSINESS_RULES.md). Saat konteks sales aktif, merge di-skip dan cookie `pas_cart_sid` di-flush ke session cart customer — mencegah merge loop yang menghidupkan item yang sudah dihapus. Item cart guest lama ditinggalkan (bukan di-merge) pada konteks sales.
- **Client**: `mobRemoveItem` kini memeriksa `res.ok` sebelum menghapus baris dari DOM (sebelumnya error 4xx/5xx tetap dianggap sukses → item "hilang" lalu muncul lagi saat refresh). `removeItem`/`clearCart`/`mobRemoveItem` menunggu `flushPendingUpdates()` sebelum DELETE agar update qty yang sedang in-flight tidak membuat ulang item yang baru dihapus; update qty mobile ikut terdaftar di `_pendingUpdates`.

### Perubahan Behavior
- Sales tanpa customer terpilih menerima `422` pada operasi mutasi cart (hapus/ubah qty/clear/notes) — sebelumnya operasi jatuh ke cart guest tanpa pemberitahuan.
- Cart merge tidak lagi terjadi pada konteks sales memilih customer (hanya saat buyer login).

### Optimasi Gambar
- **Command baru `php artisan optimize:images`** (`app/Console/Commands/OptimizeImages.php`): konversi rekursif gambar di `storage/app/public/products` dan `storage/app/public/product-images` (jpg/jpeg/png/gif) ke `.webp` (default kualitas 82, rentang 80-85), driver GD (fallback Imagick), aman: original tidak dihapus, file `.webp` yang sudah up-to-date di-skip, kegagalan per-file tidak menghentikan proses. Flag: `--dir=` (satu direktori, override default), `--quality=`.
- **`Product::getPhotoUrlAttribute()`** kini memprioritaskan varian `.webp` jika file-nya ada di storage public; jika tidak ada, tetap memakai file asli.
- **Lazy loading**: `loading="lazy" decoding="async"` dipasang pada gambar produk list (`product-card-item`), gambar cart desktop & mobile, dan related products di halaman detail — mengurangi CLS/bandwidth. Gambar hero/LCP (banner, gambar utama detail produk) sengaja tetap eager.

### File
- `app/Http/Controllers/Guest/CartController.php` (session+cookie active customer, `resolveCart($request, $requireSelectedCustomer)`)
- `app/Services/CartService.php` (skip merge untuk konteks sales + flush cookie)
- `resources/views/guest/cart/index.blade.php` (cek `res.ok` di `mobRemoveItem`, flush pending updates sebelum delete, registrasi update qty mobile)
- `app/Console/Commands/OptimizeImages.php` (baru)
- `app/Models/Product.php` (prioritas WebP di `photo_url`)
- `resources/views/guest/partials/product-card-item.blade.php`, `resources/views/guest/cart/index.blade.php`, `resources/views/guest/products/show.blade.php` (lazy/async)
- `tests/Feature/SalesCartRemoveTest.php` (baru: delete sales + customer, tolak fallback guest, skip merge, prioritas WebP)

## Register Info Page (Guest)

### Keputusan Desain
- **`GET /register` kini ada** dan menampilkan **halaman info registrasi**, bukan form pendaftaran — sejalan dengan BUSINESS_RULES.md (*"Self-register: Tidak ada. Customer hanya bisa didaftarkan oleh Sales atau Admin"*). Sebelumnya URL ini mengembalikan **404** padahal `login.blade.php` menautkan "Daftar sekarang" ke `/register`.
- Pesan utama halaman: **"Registrasi Customer Baru — Silakan hubungi Sales / Admin kami"**, dilengkapi notice bahwa pendaftaran tidak dapat dilakukan mandiri dan kanal kontak **WhatsApp (`+62 851-0182-8797`) & Telepon** (tanpa email, tanpa blok langkah).
- Desain mengikuti style auth yang ada: desktop **split-screen "Trading Floor"** (`auth-aside` navy + kartu di `--hp-paper`), mobile mengikuti pola `login-mob-*`. Link login tetap membawa parameter `?redirect=`.
- **Halaman `/login` & `/register` full-bleed tanpa scroll**: body diberi class `auth-fullscreen` sehingga `main-content` (padding) dan `container` (`mt-*`/`px-*`) di-nol-kan via CSS — split-screen pas tinggi viewport. Scroll internal hanya terjadi di panel kanan (`.auth-main`) jika kartu lebih tinggi dari layar.
- Route dinamai `guest.register`; link "Daftar sekarang" di halaman login kini tidak lagi 404.

### File
- `routes/guest.php` (route `GET /register` → `guest.auth.register-info`)
- `resources/views/guest/auth/register-info.blade.php` (baru)
- `resources/views/guest/layouts/app.blade.php` (class `auth-fullscreen` untuk `/login` & `/register`)
- `resources/views/guest/auth/login.blade.php` (cache-buster `auth.css`)
- `public/guest/css/auth.css` (full-bleed `auth-fullscreen`, section `.auth-notice/.auth-contacts` + mobile `#mobRegisterInfo`)
- `public/guest/css/app.css` (centering tablet `#mobRegisterInfo .login-mob-inner`)
- `docs/API_REFERENCE.md` (tambah baris `GET /register`)

## Guest UI Continuation (Frontend)

### Keputusan Desain
- **Design tokens "Trading Floor" dipromosikan global**: blok `--hp-*` dipindah dari `.home-page` ke `:root`, sehingga dipakai juga di halaman auth dan detail produk. Space Grotesk kini dimuat di layout (`guest/layouts/app.blade.php`) untuk semua halaman guest (tidak lagi hanya home).
- **PAS Splash** (initial page load, semua halaman guest): overlay full-screen navy gradient dengan mark `PAS` + dot amber pulsing + progress bar amber, muncul sebelum konten, fade-out saat `window.load` (min ~680ms, fallback timeout 2.8s), dinonaktifkan animasinya saat `prefers-reduced-motion`.
- **Login & Register desktop (≥992px)** di-redesign jadi **split-screen "Trading Floor"**: panel kiri navy (brand, headline, bullet points, stats, glow dekoratif), panel kanan kartu form di atas `--hp-paper`. Class baru `auth-desktop/auth-shell/auth-aside/auth-main/auth-card-wrap/...` di `public/guest/css/auth.css`. `body[data-spa="false"] .desktop-header` disembunyikan agar layar auth full-height.
- **Auth-card bersama** (register-buyer, verify-email, change-password): polish card radius 22px, border `--hp-line`, shadow lembut, input & tombol primary bertema navy — berlaku otomatis via `.login-page .auth-card`.
- **Detail produk** (`guest/products/show.blade.php`) di-redesign dengan class `pd-page/*`: image card dengan badge "PAS Official", harga di block bertanda putus-putus, tier pricing sebagai baris list dengan baris pertama aktif, spec grid 2 kolom, stepper kuantitas + tombol navy, benefit chips, related products memakai `products-grid-manual`.

### Perubahan Behavior
- Loading splash baru menggantikan/mendampingi overlay lama yang dibuat JS — splash branded `#pas-splash` disisipkan langsung di layout setelah `<body>` (tampil sebelum CSS/JS lain selesai).
- Desktop password fields login/register kini punya tombol show/hide (`auth-eye`) yang benar-benar ter-wire di blade.
- **Hero home = banner statis**: `bannerrrrr.png` (ditaruh user di root `backend/`, di-gitignore; salinan yang di-serve di `public/guest/img/bannerrrrr.png`) **menggantikan hero carousel broadcast** (element `#heroCarousel` dihapus). Frame `hero-banner-frame`/`hero-banner-media` dipertahankan; media home diberi **max-height** (`clamp(180px, 46vw, 460px)`) dengan `object-fit:cover` + `object-position:center` agar banner tidak setinggi ukuran asli (1536×1024 ~ 3:2) tapi tetap proporsional sebagai banner. Chip brand tetap overlay di kiri bawah. `loading="lazy"`, fallback `placeholder-banner.svg`.

### Catatan
- Route `/register` **dulu tidak ada** (404) — kini sudah dibuat sebagai **halaman info registrasi** (lihat section "Register Info Page"), bukan form self-register. Buyer baru tetap dibuat via `/register-buyer` (oleh sales).

### File
- `resources/views/guest/layouts/app.blade.php` (Space Grotesk global + splash markup + inline script)
- `resources/views/guest/auth/login.blade.php`, `register.blade.php` (desktop split-screen)
- `resources/views/guest/products/show.blade.php` (redesign `pd-page`)
- `resources/views/guest/home.blade.php` (hapus link Space Grotesk redundan; hero diganti banner statis, hapus `#heroCarousel`)
- `public/guest/img/bannerrrrr.png` (banner hero home, diserve dari public)
- `public/guest/css/app.css` (tokens ke `:root`; splash CSS; section `.pd-page`)
- `public/guest/css/auth.css` (desktop auth split-screen + polish `.login-page .auth-card`)

## Guest Home Redesign (Frontend)

### Keputusan Desain
- **Arah visual "Trading Floor"** untuk halaman `guest/home` (scoped di `.home-page`): palet navy-ledger (`#07294a`/`#003366`) + amber aksen (`#f59e0b`), tidak mengubah brand warna global.
- **Typography**: display face Space Grotesk (dimuat hanya di halaman home via `@stack('styles')`), body tetap Inter.
- **Signature**: *market ticker* — band marquee kategori di bawah hero (linear motion, pause on hover, konten diduplikasi via JS agar loop seamless, nonaktif saat `prefers-reduced-motion`).
- **Motion**: hero entrance (ease-out-quint), scroll reveal via `IntersectionObserver` (hanya `opacity` + `transform`), feedback tekan `scale(0.97)` saat pointer-down, hover lift dibatasi `@media (hover:hover)`, carousel jadi crossfade (`carousel-fade`).
- **Aksesibilitas**: semua animasi mati/reduksi saat `prefers-reduced-motion`; chip hero solid saat `prefers-reduced-transparency`; `focus-visible` ring; hover hanya di perangkat pointer halus.

### Perubahan Behavior
- **Kategori home**: layout desktop berubah dari horizontal-slide cards (`categories-scroll`) menjadi grid responsif non-slide — 5 kolom desktop / 4 kolom tablet / 3 kolom mobile, kartu lebih besar, dengan tile **"Lihat Semua"** ke `/categories` (tampil jika jumlah kategori > 10 desktop, > 6 mobile). Semua item adalah `<a>` asli (tanpa JS navigasi).
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
