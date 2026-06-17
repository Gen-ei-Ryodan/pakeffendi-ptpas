# Business Rules

## Order

### Status Flow
Order memiliki 4 status yang **hanya bisa maju (forward-only)**:

```
new → on_progress → on_delivery → finished
```

- `new`: Order baru masuk, menunggu diproses admin.
- `on_progress`: Order sedang diproses (pick, pack).
- `on_delivery`: Order sedang dikirim.
- `finished`: Order selesai. **Tidak bisa diubah lagi.**
- Jika status sudah `finished`, update status ditolak.

### Order Number Format
Format: `W` + `YYMMDD` + `0001` (4 digit urut per hari).

Contoh: `W2602170001` = order pertama pada 17 Feb 2026.

### Order Deletion
Admin bisa menghapus order — soft delete melalui Eloquent. Tidak ada aturan khusus yang melarang hapus order setelah status tertentu di controller.

## Cart

### Cart Ownership
- **Guest**: Cart diikat oleh `session_id` (cookie `pas_cart_sid`).
- **Buyer (Customer)**: Cart diikat oleh `customer_id`, 1 cart aktif per customer.
- **Sales**: Cart dibuat atas nama customer (via `customer_id` + `sales_id`).

### Cart → Order (Checkout)
- Saat checkout, semua `cart_items` dikonversi ke `sales_order_items`.
- Cart di-set status `converted`.
- Cart baru (aktif) langsung dibuat untuk customer.

### Cart Merge
- Jika guest cart (session) ada itemnya dan user login sebagai customer → item digabung (merge) ke cart customer.
- Quantity produk yang sama dijumlah.
- Guest cart dihapus itemnya dan di-set status `converted`.

## Customer

### Account Status
- `pending`: Menunggu approval admin.
- `active`: Bisa login dan bertransaksi.
- Customer dengan status selain `active` **tidak bisa login**.

### Customer Registration
- **Self-register**: Tidak ada. Customer hanya bisa didaftarkan oleh Sales atau Admin.
- **API Guest Order**: Saat guest membuat order via API, customer dibuat otomatis (firstOrCreate by email) dengan account_type `Guest` dan status aktif.

### Email Verification
- Customer baru harus verifikasi email sebelum bisa login.
- Kode 6 digit dikirim via email.
- Verifikasi bisa via form input kode atau klik link langsung (`/verify-email/{code}`).
- Setelah verifikasi, field `email_verified_at` diisi dan `email_verification_code` dihapus.

### Change Password (Buyer only)
- Buyer yang sudah login bisa ganti password.
- Wajib verifikasi email (kirim kode 6 digit).
- Setelah berhasil, session buyer di-invalidate (harus login ulang).

## Sales

### Sales Role
- Sales login via `/login` (guard `web`).
- Sales bisa:
  - Membuat cart atas nama customer mereka.
  - Checkout atas nama customer.
  - Mendaftarkan buyer baru.
  - Melihat daftar customer mereka sendiri.
- Sales **tidak bisa** akses panel admin (`/admin/*`).

### Sales-Customer Relationship
- Customer memiliki `sales_id` → merujuk ke User yang mendaftarkannya.
- Sales hanya bisa bertransaksi dengan customer yang memiliki `sales_id` = id mereka.

## Product

### Pricing Tiers
Produk memiliki 3 level harga tier berdasarkan quantity:

| Tier | Qty Min | Price Field | Discount Field |
|------|---------|-------------|----------------|
| 1    | 1       | `price_1`   | `disc_1`       |
| 2    | `qty_2` | `price_2`   | `disc_2`       |
| 3    | `qty_3` | `price_3`   | `disc_3`       |

Tier 2 dan 3 hanya aktif jika `qty_* > 0` dan `price_* > 0`.

Rumus: `net_price = unit_price × (1 - (discount_percent / 100))`

### Product Visibility
Produk tampil di guest area jika:
1. `discontinued = false`
2. Kategori produk aktif (`is_active = true`)
3. Memiliki foto (`photo_path` tidak null/kosong dan bukan placeholder)

## Access Control

| Area           | Admin | Super Admin | Sales | Buyer | Guest |
|----------------|-------|-------------|-------|-------|-------|
| Panel Admin    | ✅    | ✅          | ❌    | ❌    | ❌    |
| Guest Browse   | ✅    | ✅          | ✅    | ✅    | ✅    |
| Cart/Checkout  | ❌    | ❌          | ✅    | ✅    | ❌    |
| Profile        | ❌    | ❌          | ✅    | ✅    | ❌    |
| Orders         | ❌    | ❌          | ✅    | ✅    | ❌    |
| Register Buyer | ❌    | ❌          | ✅    | ❌    | ❌    |
| My Customers   | ❌    | ❌          | ✅    | ❌    | ❌    |

## Activity Log
Semua aktivitas admin (login, logout, CRUD) dicatat ke tabel `activity_logs` via `ActivityLogger::log()`.
