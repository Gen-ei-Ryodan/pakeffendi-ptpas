# API Reference

## Base URL
- **Guest API**: `/api/guest`

---

## Guest API

### 1. Sync
`GET /api/guest/sync`

Cek apakah ada perubahan data. Digunakan untuk polling.

**Response (200):**
```json
{
  "version": 1700000000,
  "changed_at": "2026-02-10T00:00:00.000000Z"
}
```

---

### 2. Home
`GET /api/guest/home`

Data untuk halaman utama (categories, brands, broadcasts, featured products, about).

**Response (200):**
```json
{
  "version": 1700000000,
  "categories": [
    {
      "id": "TOOL",
      "category_code": "TOOL",
      "name": "POWER TOOL",
      "image_path": null,
      "updated_at": "2026-02-10T00:00:00.000000Z"
    }
  ],
  "brands": [
    {
      "id": "BOSCH",
      "brand_code": "BOSCH",
      "brand_name": "BOSCH",
      "brand_image_path": null,
      "is_favorite": true,
      "updated_at": "2026-02-10T00:00:00.000000Z"
    }
  ],
  "broadcasts": [
    {
      "id": 1,
      "image_path": "https://...",
      "description": "...",
      "updated_at": "2026-02-10T00:00:00.000000Z"
    }
  ],
  "featured_products": [
    {
      "id": 1,
      "sku": "SKU-00001",
      "name": "Product Name",
      "variant": null,
      "brand": "BOSCH",
      "image_path": null,
      "price_1": 25000,
      "updated_at": "2026-02-10T00:00:00.000000Z"
    }
  ],
  "about": {
    "content": "<h3>About Content</h3>",
    "updated_at": "2026-02-10T00:00:00.000000Z"
  }
}
```

---

### 3. Product List
`GET /api/guest/products`

**Query Params:**
| Param       | Type    | Description                          |
|-------------|---------|--------------------------------------|
| `q`         | string  | Cari berdasarkan `name` atau `sku`   |
| `category_id` | string | Filter kategori (category_code)      |
| `brand_id`  | string  | Filter brand (brand_code)            |
| `per_page`  | integer | 1-50, default 20                     |

**Response (200):** JSON pagination Laravel standar.

---

### 4. Product Detail
`GET /api/guest/products/{product}`

**Response (200):**
```json
{
  "id": 1,
  "sku": "SKU-00001",
  "name": "Product Name",
  "variant": null,
  "description": "Product description",
  "unit": "pcs",
  "weight_kg": 1.5,
  "brand": "BOSCH",
  "category": "POWER TOOL",
  "photo_path": null,
  "image_path": null,
  "price_tiers": [
    { "min_qty": 1, "max_qty": 9, "price": 25000, "discount_percent": 0 },
    { "min_qty": 10, "max_qty": null, "price": 23000, "discount_percent": 0 }
  ],
  "updated_at": "2026-02-10T00:00:00.000000Z"
}
```

**Error:** 404 jika produk discontinued atau kategori tidak aktif.

---

### 5. Create Order (Guest → Admin)
`POST /api/guest/orders`

**Request Body:**
```json
{
  "customer": {
    "full_name": "Budi",
    "email": "budi@example.com",
    "phone": "08123456789",
    "address": "Jl. Merdeka No. 1"
  },
  "delivery_to": "Budi",
  "delivery_phone": "08123456789",
  "delivery_address": "Jl. Merdeka No. 1",
  "notes": "Catatan tambahan",
  "items": [
    { "product_id": 1, "quantity": 2 }
  ]
}
```

**Response (201):**
```json
{
  "order_id": 1,
  "order_no": "W2602100001",
  "status": "Payment Pending",
  "grand_total": 50000
}
```

**Notes:**
- Customer dibuat otomatis (`firstOrCreate` by email) dengan account_type `Guest`.
- Order status: `Payment Pending`.

---

## Web Routes (Server-Rendered)

### Guest Area

#### Auth
| Method | URI                       | Name                      | Middleware     |
|--------|---------------------------|---------------------------|---------------|
| GET    | `/login`                  | `guest.login`             | guest:customer |
| POST   | `/login`                  | `guest.login.store`       | guest:customer |
| GET    | `/verify-email`           | `guest.verify-email`      | —             |
| POST   | `/verify-email`           | `guest.verify-email.store`| —             |
| GET    | `/verify-email/{code}`    | `guest.verify-email.direct`| —            |
| GET    | `/register-buyer`         | `guest.register-buyer`    | auth:web      |
| POST   | `/register-buyer`         | `guest.register-buyer.store`| auth:web    |
| GET    | `/change-password`        | `guest.change-password`   | guest.auth    |
| POST   | `/change-password/send-code` | `guest.change-password.send-code` | guest.auth |
| POST   | `/change-password`        | `guest.change-password.store` | guest.auth |
| POST   | `/logout`                 | `guest.logout`            | guest.auth    |

#### Cart
| Method | URI                                    | Name                          | Middleware     |
|--------|----------------------------------------|-------------------------------|---------------|
| GET    | `/cart`                                | `guest.cart.index`            | guest.auth    |
| GET    | `/cart/summary`                        | `guest.cart.summary`          | —             |
| POST   | `/cart/items`                          | `guest.cart.items.store`      | guest.auth    |
| POST   | `/cart/items/{product}`                | `guest.cart.items.set`        | guest.auth    |
| DELETE | `/cart/items/{product}`                | `guest.cart.items.destroy`    | guest.auth    |
| DELETE | `/cart`                                | `guest.cart.clear`            | guest.auth    |
| POST   | `/cart/checkout`                       | `guest.cart.checkout`         | guest.auth    |
| GET    | `/cart/customers/{customer}/addresses`  | `guest.cart.customer-addresses` | auth:web, sales |
| GET    | `/cart/select-customer/{customerId}`    | `guest.cart.select-customer`  | auth:web, sales |
| GET    | `/cart/clear-customer`                 | `guest.cart.clear-customer`   | auth:web, sales |
| GET    | `/cart/my-customers`                   | `guest.cart.my-customers`     | auth:web, sales |

#### Profile
| Method | URI                            | Name                              | Middleware     |
|--------|--------------------------------|-----------------------------------|---------------|
| GET    | `/profile`                     | `guest.profile.index`             | guest.auth    |
| POST   | `/profile`                     | `guest.profile.update`            | guest.auth    |
| GET    | `/profile/logs`                | `guest.profile.logs`              | auth:web, sales |
| GET    | `/profile/addresses`           | `guest.profile.addresses.index`   | guest.auth    |
| POST   | `/profile/addresses`           | `guest.profile.addresses.store`   | guest.auth    |
| PUT    | `/profile/addresses/{address}` | `guest.profile.addresses.update`  | guest.auth    |
| DELETE | `/profile/addresses/{address}` | `guest.profile.addresses.destroy` | guest.auth    |
| POST   | `/profile/addresses/{address}/active` | `guest.profile.addresses.set-active` | guest.auth |

#### Orders
| Method | URI              | Name                  | Middleware  |
|--------|------------------|-----------------------|------------|
| GET    | `/orders`        | `guest.orders.index`  | guest.auth |
| GET    | `/orders/{order}`| `guest.orders.show`   | guest.auth |

#### Regions
| Method | URI                                          | Name                        |
|--------|----------------------------------------------|-----------------------------|
| GET    | `/regions/provinces`                         | `guest.regions.provinces`   |
| GET    | `/regions/regencies/{provinceCode}`           | `guest.regions.regencies`   |
| GET    | `/regions/districts/{regencyCode}`            | `guest.regions.districts`   |
| GET    | `/regions/villages/{districtCode}`            | `guest.regions.villages`    |

#### My Customers (Sales only)
| Method | URI                                    | Name                              | Middleware       |
|--------|----------------------------------------|-----------------------------------|------------------|
| GET    | `/profile/my-customers`                | `guest.profile.my-customers.index` | auth:web, sales  |
| GET    | `/profile/my-customers/{customer}`     | `guest.profile.my-customers.show`  | auth:web, sales  |

### Admin Area
| Method | URI                        | Name                      | Middleware |
|--------|----------------------------|---------------------------|------------|
| GET    | `/admin/login`             | `admin.login`             | guest:web  |
| POST   | `/admin/login`             | `admin.login.store`       | guest:web  |
| POST   | `/admin/logout`            | `admin.logout`            | auth       |
| GET    | `/admin/dashboard`         | `admin.dashboard`         | admin      |
| GET    | `/admin/accounts`          | `admin.accounts.index`    | admin      |
| GET    | `/admin/customers`         | `admin.customers.index`   | admin      |
| PATCH  | `/admin/customers/{customer}/approve` | `admin.customers.approve` | admin |
| PATCH  | `/admin/customers/{customer}/reject`  | `admin.customers.reject`  | admin |
| GET    | `/admin/categories`        | `admin.categories.index`  | admin      |
| GET    | `/admin/brands`            | `admin.brands.index`      | admin      |
| GET    | `/admin/products`          | `admin.products.index`    | admin      |
| GET    | `/admin/statuses`          | `admin.statuses.index`    | admin      |
| GET    | `/admin/sales-orders`      | `admin.sales-orders.index`| admin      |
| GET    | `/admin/broadcasts`        | `admin.broadcasts.index`  | admin      |
| GET    | `/admin/favorite-brands`   | `admin.favorite-brands.index`| admin   |
| GET    | `/admin/logs`              | `admin.logs.index`        | admin      |
| GET    | `/admin/about`             | `admin.about.edit`        | admin      |

Semua resource admin mengikuti pola `Route::resource()` untuk CRUD (create, edit, update, destroy).
