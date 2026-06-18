# Database

## Ringkasan

### Tabel

| Tabel                    | Model              | Keterangan                                       |
|--------------------------|--------------------|--------------------------------------------------|
| `users`                  | `User`             | Admin & Sales accounts                           |
| `customers`              | `Customer`         | Buyer accounts (authenticatable via `customer` guard) |
| `customer_addresses`     | `CustomerAddress`  | Alamat pengiriman buyer (bisa lebih dari 1)      |
| `product_categories`     | `ProductCategory`  | Kategori produk                                  |
| `product_brands`         | `ProductBrand`     | Brand/merek produk                               |
| `products`               | `Product`          | Data produk                                      |
| `product_images`         | `ProductImage`     | Gambar produk (bisa multiple per produk)         |
| `product_variants`       | `ProductVariant`   | Varian produk (misal: warna, ukuran)             |
| `product_variant_items`  | `ProductVariantItem`| Kombinasi varian + pricing                       |
| `product_statuses`       | `ProductStatus`    | Status produk (untuk sorting di halaman utama)   |
| `related_products`       | —                  | Relasi many-to-many antar produk                 |
| `favorite_brands`        | `FavoriteBrand`    | Brand yang ditampilkan di halaman utama          |
| `carts`                  | `Cart`             | Keranjang belanja (session atau customer)        |
| `cart_items`             | `CartItem`         | Item dalam keranjang                             |
| `sales_orders`           | `SalesOrder`       | Order/transaksi                                  |
| `sales_order_items`      | `SalesOrderItem`   | Item dalam order                                 |
| `broadcasts`             | `Broadcast`        | Banner/pengumuman di halaman utama               |
| `about_pages`            | `AboutPage`        | Konten halaman about                             |
| `activity_logs`          | `ActivityLog`      | Log aktivitas admin                              |

### Relationship Diagram

```
users (User)
├── hasMany → customers (sales_id)
└── hasMany → sales_orders (sales_id / sales_person_id)

customers (Customer)
├── belongsTo → users (sales_id)
├── belongsTo → carts (customer_id)
├── hasMany → sales_orders (customer_id)
├── hasMany → customer_addresses
└── hasOne → customer_addresses (is_active = true) [activeAddress]

customer_addresses
└── belongsTo → customers

product_categories
├── hasMany → products (product_category_code)
└── primaryKey: category_code (string)

product_brands
├── hasMany → products (product_brand_code)
├── hasOne → favorite_brands
└── primaryKey: brand_code (string)

products
├── belongsTo → product_categories (product_category_code)
├── belongsTo → product_brands (product_brand_code)
├── belongsToMany → products (related_products) [relatedProducts]
├── hasMany → product_images
├── hasMany → product_variants
├── hasMany → product_variant_items
└── hasMany → cart_items

product_variants
├── belongsTo → products
└── hasMany → product_images

product_variant_items
├── belongsTo → products
├── belongsTo → product_variants (variant_1_id)
├── belongsTo → product_variants (variant_2_id)
└── hasMany → product_images

product_images
├── belongsTo → products
├── belongsTo → product_variants
└── belongsTo → product_variant_items

related_products (pivot)
├── product_id
└── related_product_id

favorite_brands
└── belongsTo → product_brands (product_brand_code)

carts
├── belongsTo → customers
├── belongsTo → users (sales_id)
└── hasMany → cart_items

cart_items
├── belongsTo → carts
└── belongsTo → products

sales_orders
├── belongsTo → customers
├── belongsTo → users (sales_person_id)
├── belongsTo → users (sales_id)
└── hasMany → sales_order_items

sales_order_items
├── belongsTo → sales_orders
└── belongsTo → products

activity_logs
└── belongsTo → users (actor_id)

broadcasts (standalone)
about_pages (standalone)
product_statuses (standalone)
```

## Detail Tabel Penting

### users
| Column       | Type     | Notes                           |
|-------------|----------|---------------------------------|
| id          | bigint   | PK, auto-increment              |
| name        | string   |                                 |
| email       | string   | unique                          |
| password    | string   | hashed                          |
| role        | string   | `admin`, `super admin`, `sales` |
| photo_path  | string   | nullable                        |

### customers
| Column                  | Type     | Notes                          |
|------------------------|----------|--------------------------------|
| id                     | bigint   | PK, auto-increment             |
| customer_code          | string   | unique                         |
| full_name              | string   |                                |
| account_type           | string   | `personal`, `Guest`            |
| ktp_number             | string   |                                |
| npwp                   | string   | nullable                       |
| email                  | string   | unique                         |
| email_verified_at      | datetime | nullable                       |
| email_verification_code| string   | nullable, 6 digit code         |
| password               | string   | hashed                         |
| phone                  | string   |                                |
| address                | text     | nullable                       |
| province, city         | string   | nullable                       |
| postal_code            | string   | nullable                       |
| sales_id               | bigint   | FK → users.id, nullable       |
| status                 | string   | `pending`, `active`            |

### products
| Column                | Type     | Notes                          |
|-----------------------|----------|--------------------------------|
| id                    | bigint   | PK                             |
| sku                   | string   |                                |
| name                  | string   |                                |
| product_brand_code    | string   | FK → product_brands            |
| product_category_code | string   | FK → product_categories        |
| price_1/2/3           | decimal  | Harga per tier                 |
| qty_1/2/3             | integer  | Minimum qty per tier           |
| disc_1/2/3            | decimal  | Diskon per tier (%)            |
| discontinued          | boolean  |                                |
| photo_path            | string   | nullable                       |
| status_product        | string   | nullable, FK logic → product_statuses |
| no_urut_status        | integer  | Urutan sorting per status      |

### sales_orders
| Column           | Type     | Notes                              |
|------------------|----------|------------------------------------|
| id               | bigint   | PK                                 |
| order_no         | string   | unique, format: WYYMMDDXXXX        |
| customer_id      | bigint   | FK → customers, nullable           |
| status           | string   | `new`, `on_progress`, `on_delivery`, `finished` |
| payment_type     | string   | nullable                           |
| sales_person_id  | bigint   | FK → users, nullable               |
| sales_id         | bigint   | FK → users, nullable               |
| grand_total      | decimal  |                                    |
| dpp              | decimal  |                                    |
| ppn              | decimal  | default 0                          |
| ppn_percent      | decimal  | default 11                         |

### carts
| Column      | Type     | Notes                                |
|-------------|----------|--------------------------------------|
| id          | bigint   | PK                                   |
| customer_id | bigint   | FK → customers, nullable             |
| sales_id    | bigint   | FK → users, nullable                 |
| session_id  | string   | UUID untuk guest cart                |
| status      | string   | `active`, `converted`                |
