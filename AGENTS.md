# AGENTS.md — Standard Operating Procedure for AI

## Cara Kerja

1. **Baca dokumentasi dulu** — sebelum membaca kode atau melakukan perubahan, baca docs yang relevan.
2. **Gunakan docs sebagai single source of truth** — jika ada konflik antara kode dan docs, tanyakan ke user.
3. **Jangan membuat asumsi** — jika docs tidak cukup jelas, tanya user.

## Urutan Membaca Docs

Saat memulai task baru, baca docs dalam urutan ini:

1. **PROJECT_CONTEXT.md** — Pahami project secara umum (nama, tujuan, tech stack, modul, role).
2. **ARCHITECTURE.md** — Pahami pola coding, struktur folder, dan alur kerja.
3. **BUSINESS_RULES.md** — Pahami aturan bisnis (paling penting — jangan langgar).
4. **CODING_STANDARDS.md** — Ikuti standar coding yang sudah ditetapkan.
5. **DATABASE.md** — Pahami struktur database (jika terkait task).
6. **API_REFERENCE.md** — Pahami endpoint API (jika terkait task).
7. **CHANGELOG.md** — Cek keputusan penting sebelumnya.

> Jika task hanya terkait area tertentu (misal: fixing bug di cart), baca docs yang relevan saja.

## Batasan Perubahan

- **Jangan ubah arsitektur** tanpa diskusi dengan user. Pola MVC, service pattern, dual guard auth sudah ditetapkan.
- **Jangan tambah package baru** tanpa persetujuan user.
- **Jangan ubah business rules** (BUSINESS_RULES.md) tanpa persetujuan user.
- **Jangan hapus atau rename file** yang sudah ada tanpa memastikan tidak ada dependensi.
- **Ikuti CODING_STANDARDS.md** untuk semua kode baru.

## Workflow Analisa

Saat mengerjakan task (bug fix, feature, dll.):

### 1. Analisis
- Baca docs terkait area yang akan diubah.
- Baca kode yang relevan (controller, model, service, view).
- Identifikasi root cause atau titik perubahan.

### 2. Rencana
- Tentukan file apa saja yang perlu diubah.
- Pastikan perubahan tidak melanggar business rules.

### 3. Eksekusi
- Ubah kode sesuai rencana.
- Update docs jika ada perubahan signifikan (API, business rules, database).

### 4. Verifikasi
- Pastikan tidak ada error syntax.
- Pastikan perubahan konsisten dengan docs.

## Catatan Penting

- **Jangan tulis ulang dokumen yang sudah ada** — edit hanya jika ada perubahan.
- **Update docs** saat ada perubahan arsitektur, business rules, API, atau database.
- **Jangan membuat dokumentasi baru** di luar struktur yang sudah ada tanpa persetujuan user.
- Jika menemukan inkonsistensi antara kode dan docs, tanyakan ke user.
