# Changelog

> Catatan keputusan penting selama pengembangan.

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
- **Hero home = banner statis**: `bannerrrrr.png` (ditaruh user di root `backend/`, di-gitignore; salinan yang di-serve di `public/guest/img/bannerrrrr.png`) **menggantikan hero carousel broadcast** (element `#heroCarousel` dihapus). Frame `hero-banner-frame`/`hero-banner-media` dipertahankan; media home di-override `aspect-ratio:auto` + `height:auto` agar banner tampil utuh tanpa crop (dulu 21:9/16:9 dengan `object-fit:cover`). Chip brand tetap overlay di kiri bawah. `loading="lazy"`, fallback `placeholder-banner.svg`.

### Catatan
- Route `/register` **masih tidak ada** (pre-existing, bukan dari task ini) — `login.blade.php` menautkan "Daftar sekarang" ke `/register` yang 404. `register.blade.php` sudah di-redesign dan siap dipasang jika route dibuat. Buyer baru dibuat via `/register-buyer` (oleh sales).

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
