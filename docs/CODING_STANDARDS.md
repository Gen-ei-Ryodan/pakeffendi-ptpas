# Coding Standards

## Bahasa
- Kode: PHP, JavaScript, Blade, CSS.
- Bahasa: Seluruh kode ditulis dalam **Bahasa Inggris** (variable, function, comment).
- Bahasa konten/view: **Bahasa Indonesia** (untuk user-facing text, flash messages, error messages).

## Naming Convention

| Element            | Convention       | Contoh                  |
|--------------------|------------------|-------------------------|
| Class              | PascalCase       | `SalesOrderController`  |
| Method/Function    | camelCase        | `getPricingTiers()`     |
| Variable           | camelCase        | `$grandTotal`           |
| Route              | kebab-case       | `sales-orders.index`    |
| View file          | kebab-case       | `verify-email.blade.php`|
| Migration          | snake_case       | `create_products_table` |
| Database column    | snake_case       | `sales_person_id`       |
| Config key         | snake_case       | `app.name`              |
| Environment key    | UPPER_SNAKE_CASE | `APP_ENV`               |

## PHP: Type Hinting & Strict Types
- Gunakan `declare(strict_types=1)` di semua file baru.
- Tambahkan type hint di parameter method dan return type.
- Gunakan `mixed` hanya jika benar-benar diperlukan.
- Hindari `@param` / `@return` PHPDoc yang hanya mengulang type hint — gunakan hanya untuk klarifikasi tambahan.

## Validation
- Validasi dilakukan di Controller menggunakan `$request->validate()`.
- Aturan validasi ditulis sebagai array — jangan gunakan `Validator` facade langsung.
- Pesan error validasi dalam Bahasa Indonesia.

## API Response Format
JSON responses mengikuti format:

```php
// Success
return response()->json(['key' => 'value'], 200);

// Created
return response()->json([...], 201);

// Error
abort(422, 'Message in Indonesian');
```

## Controller Structure
- Controller tidak boleh berisi query Eloquent yang kompleks — pindahkan ke Model Scope atau Service.
- Gunakan `__construct()` untuk dependency injection Service.

## Model Scopes
Gunakan local scopes untuk query yang sering dipakai:

```php
public function scopeActive($query)
{
    return $query->where('discontinued', false);
}
```

## Views (Blade)
- Gunakan `@extends('guest.layouts.app')` untuk layout.
- Gunakan `@section('content')` untuk konten.
- Partial/shared components di `resources/views/guest/partials/`.
- Hindari PHP logic berlebihan di Blade — gunakan View Composer atau Controller.

## Route Definitions
- Route dipisah per file: `web.php` (admin), `guest.php` (guest web), `api.php` (REST).
- Nama route prefixed: `admin.*`, `guest.*`, `api.guest.*`.
- Gunakan `Route::resource()` untuk CRUD standar.
- Gunakan `Route::controller()` atau explicit Route::method() untuk non-CRUD.

## Middleware
- Middleware kustom ditempatkan di `app/Http/Middleware/`.
- Daftarkan di `bootstrap/app.php` dengan `->withMiddleware()`.
- Middleware digunakan sebagai alias, bukan class inline.

## Services
Service class ditempatkan di `app/Services/`.
- Method public untuk operasi bisnis.
- Dependency Injection via constructor.
- Tidak menyimpan state.

## Naming Rules

| Item              | Convention       | Benar                  | Salah              |
|-------------------|------------------|------------------------|--------------------|
| Route name        | kebab-case       | `sales-orders.index`   | `sales_orders`     |
| View file         | kebab-case       | `verify-email.blade.php`| `verifyEmail`      |
| URL               | kebab-case       | `/sales-orders`        | `/salesOrders`     |
| DB column         | snake_case       | `sales_person_id`      | `salesPersonId`    |
| JS variable       | camelCase        | `orderNo`              | `order_no`         |
| CSS class         | kebab-case       | `btn-primary`          | `btnPrimary`       |

## Git & Commit
- Tidak ada aturan spesifik — ikuti gaya commit yang sudah ada di repo.
