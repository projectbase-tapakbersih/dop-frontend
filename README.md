# 🧹 Tapak Bersih - Shoe Laundry Frontend

> CodeIgniter 4 Frontend untuk Aplikasi Laundry Sepatu dengan integrasi Laravel API Backend

![CI4](https://img.shields.io/badge/CodeIgniter-4.x-orange)
![PHP](https://img.shields.io/badge/PHP-8.1+-blue)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)
![License](https://img.shields.io/badge/License-MIT-green)

## 📋 Daftar Isi

- [Tentang Project](#-tentang-project)
- [Fitur](#-fitur)
- [Tech Stack](#-tech-stack)
- [Requirements](#-requirements)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Struktur Project](#-struktur-project)
- [Routes](#-routes)
- [API Integration](#-api-integration)
- [Authentication](#-authentication)
- [Order Status Flow](#-order-status-flow)
- [Controllers](#-controllers)
- [Views](#-views)
- [Helpers](#-helpers)
- [Troubleshooting](#-troubleshooting)

---

## 📖 Tentang Project

**Tapak Bersih** adalah aplikasi web untuk layanan laundry sepatu profesional. Frontend ini dibangun menggunakan CodeIgniter 4 dan terhubung dengan Laravel API Backend untuk semua operasi data.

### Arsitektur

```
┌─────────────────┐     HTTP/JSON      ┌─────────────────┐
│                 │ ◄────────────────► │                 │
│   CI4 Frontend  │                    │  Laravel API    │
│   Port: 8080    │                    │  Port: 8000     │
│                 │                    │                 │
└─────────────────┘                    └─────────────────┘
        │                                      │
        │                                      │
        ▼                                      ▼
   User Browser                           Database
                                         (MySQL)
```

---

## ✨ Fitur

### 👤 Public (Guest)
- [x] Landing page dengan informasi layanan
- [x] Katalog layanan (services)
- [x] Galeri hasil kerja
- [x] Daftar cabang/lokasi
- [x] **Lacak pesanan tanpa login**
- [x] Registrasi & Login

### 🛒 Customer (User)
- [x] Dashboard dengan statistik pesanan
- [x] Buat pesanan baru (checkout)
- [x] Lihat daftar pesanan
- [x] Detail pesanan dengan timeline status
- [x] Batalkan pesanan
- [x] Pembayaran via Midtrans
- [x] Kelola profil

### 👨‍💼 Admin
- [x] Dashboard dengan statistik lengkap
- [x] Manajemen pesanan (CRUD + update status)
- [x] Manajemen pengguna
- [x] Manajemen layanan
- [x] Manajemen cabang
- [x] Manajemen galeri
- [x] Manajemen promo code
- [x] Laporan

---

## 🛠 Tech Stack

| Technology | Version | Description |
|------------|---------|-------------|
| CodeIgniter | 4.x | PHP Framework |
| PHP | 8.1+ | Server-side language |
| Bootstrap | 5.3 | CSS Framework |
| Bootstrap Icons | 1.11 | Icon library |
| jQuery | 3.x | JavaScript library |
| Midtrans | - | Payment Gateway |

---

## 📦 Requirements

- PHP >= 8.1
- Composer
- MySQL/MariaDB (untuk Laravel backend)
- Laravel API Backend running di `localhost:8000`
- Extension PHP: intl, curl, json, mbstring

---

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/your-repo/tapak-bersih-frontend.git
cd tapak-bersih-frontend
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Copy Environment File

```bash
cp env .env
```

### 4. Konfigurasi Environment

Edit file `.env`:

```ini
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------
CI_ENVIRONMENT = development

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------
app.baseURL = 'http://localhost:8080/'

#--------------------------------------------------------------------
# API CONFIGURATION
#--------------------------------------------------------------------
API_BASE_URL = 'http://localhost:8000/api'

#--------------------------------------------------------------------
# SESSION
#--------------------------------------------------------------------
session.driver = 'CodeIgniter\Session\Handlers\FileHandler'
session.cookieName = 'ci_session'
session.expiration = 7200
session.savePath = WRITEPATH . 'session'
```

### 5. Set Permissions

```bash
chmod -R 777 writable/
```

### 6. Jalankan Server

```bash
php spark serve --port=8080
```

### 7. Akses Aplikasi

Buka browser: `http://localhost:8080`

---

## ⚙️ Konfigurasi

### API Configuration

Buat file `app/Config/Api.php`:

```php
<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Api extends BaseConfig
{
    public string $baseUrl = 'http://localhost:8000/api';
    public int $timeout = 30;
}
```

### Session Configuration

File `app/Config/Session.php` sudah tersedia default dari CI4.

---

## 📁 Struktur Project

```
app/
├── Config/
│   ├── Routes.php          # Definisi semua routes
│   └── Api.php             # Konfigurasi API
│
├── Controllers/
│   ├── Admin/
│   │   ├── DashboardController.php
│   │   ├── OrderController.php
│   │   ├── UserController.php
│   │   ├── ServiceController.php
│   │   ├── BranchController.php
│   │   ├── GalleryController.php
│   │   └── PromoCodeController.php
│   │
│   ├── Auth/
│   │   └── AuthController.php
│   │
│   ├── Catalog/
│   │   ├── ServiceController.php
│   │   ├── BranchController.php
│   │   └── GalleryController.php
│   │
│   ├── Order/
│   │   ├── CheckoutController.php
│   │   └── GuestOrderController.php    # Track order tanpa login
│   │
│   ├── Payment/
│   │   └── PaymentController.php
│   │
│   ├── User/
│   │   ├── DashboardController.php
│   │   ├── OrderController.php
│   │   └── ProfileController.php
│   │
│   ├── BaseController.php
│   └── Home.php
│
├── Filters/
│   └── AuthFilter.php      # Middleware authentication
│
├── Helpers/
│   ├── api_helper.php      # Helper untuk API calls
│   ├── auth_helper.php     # Helper authentication
│   └── format_helper.php   # Helper formatting (rupiah, date)
│
└── Views/
    ├── admin/
    │   ├── dashboard.php
    │   ├── orders/
    │   │   ├── index.php
    │   │   └── detail.php
    │   ├── users/
    │   ├── services/
    │   ├── branches/
    │   ├── gallery/
    │   └── promo-codes/
    │
    ├── auth/
    │   ├── login.php
    │   └── register.php
    │
    ├── catalog/
    │   ├── services.php
    │   ├── branches.php
    │   └── gallery.php
    │
    ├── guest/
    │   ├── track.php           # Halaman lacak pesanan
    │   └── order_detail.php
    │
    ├── order/
    │   ├── create.php
    │   └── checkout.php
    │
    ├── payment/
    │   ├── show.php
    │   ├── success.php
    │   ├── pending.php
    │   └── failed.php
    │
    ├── user/
    │   ├── dashboard.php
    │   ├── orders/
    │   │   ├── index.php
    │   │   └── detail.php
    │   └── profile.php
    │
    ├── layouts/
    │   ├── main.php            # Layout utama (public)
    │   └── admin.php           # Layout admin
    │
    └── home.php
```

---

## 🛣 Routes

### Public Routes

| Method | URL | Controller | Description |
|--------|-----|------------|-------------|
| GET | `/` | Home::index | Landing page |
| GET | `/services` | Catalog\ServiceController::index | Daftar layanan |
| GET | `/services/{slug}` | Catalog\ServiceController::detail | Detail layanan |
| GET | `/branches` | Catalog\BranchController::index | Daftar cabang |
| GET | `/gallery` | Catalog\GalleryController::index | Galeri |

### Auth Routes

| Method | URL | Controller | Description |
|--------|-----|------------|-------------|
| GET | `/auth/login` | Auth\AuthController::loginPage | Halaman login |
| POST | `/auth/login` | Auth\AuthController::login | Proses login |
| GET | `/auth/register` | Auth\AuthController::registerPage | Halaman register |
| POST | `/auth/register` | Auth\AuthController::register | Proses register |
| GET | `/auth/logout` | Auth\AuthController::logout | Logout |

### Guest Order Routes (Tanpa Login)

| Method | URL | Controller | Description |
|--------|-----|------------|-------------|
| GET | `/guest/track` | Order\GuestOrderController::trackPage | Halaman lacak pesanan |
| POST | `/guest/track` | Order\GuestOrderController::track | Proses lacak (AJAX) |
| GET | `/guest/order/{order_number}` | Order\GuestOrderController::detail | Detail pesanan guest |

### Order Routes

| Method | URL | Controller | Description |
|--------|-----|------------|-------------|
| GET | `/order/create` | Order\CheckoutController::createPage | Halaman buat pesanan |
| GET | `/order/checkout` | Order\CheckoutController::checkoutPage | Halaman checkout |
| POST | `/order/checkout` | Order\CheckoutController::processCheckout | Proses checkout |
| POST | `/order/apply-promo` | Order\CheckoutController::applyPromo | Apply promo code |

### Payment Routes

| Method | URL | Controller | Description |
|--------|-----|------------|-------------|
| GET | `/payment/{order_number}` | Payment\PaymentController::show | Halaman pembayaran |
| POST | `/payment/{order_number}/pay` | Payment\PaymentController::pay | Proses bayar |
| GET | `/payment/finish` | Payment\PaymentController::finish | Callback Midtrans |

### User Routes (Authenticated)

| Method | URL | Controller | Description |
|--------|-----|------------|-------------|
| GET | `/user/dashboard` | User\DashboardController::index | Dashboard user |
| GET | `/user/orders` | User\OrderController::myOrders | Daftar pesanan |
| GET | `/user/orders/{order_number}` | User\OrderController::detail | Detail pesanan |
| POST | `/user/orders/{order_number}/cancel` | User\OrderController::cancelOrder | Batalkan pesanan |
| GET | `/user/profile` | User\ProfileController::index | Halaman profil |
| POST | `/user/profile/update` | User\ProfileController::update | Update profil |

### Admin Routes

| Method | URL | Controller | Description |
|--------|-----|------------|-------------|
| GET | `/admin/dashboard` | Admin\DashboardController::index | Dashboard admin |
| GET | `/admin/orders` | Admin\OrderController::index | Daftar pesanan |
| GET | `/admin/orders/{order_number}` | Admin\OrderController::show | Detail pesanan |
| POST | `/admin/orders/{order_number}/status` | Admin\OrderController::updateStatus | Update status |
| GET | `/admin/users` | Admin\UserController::index | Daftar users |
| GET | `/admin/services` | Admin\ServiceController::index | Daftar layanan |
| GET | `/admin/branches` | Admin\BranchController::index | Daftar cabang |
| GET | `/admin/gallery` | Admin\GalleryController::index | Daftar galeri |
| GET | `/admin/promo-codes` | Admin\PromoCodeController::index | Daftar promo |

---

## 🔌 API Integration

### API Helper

File: `app/Helpers/api_helper.php`

```php
<?php

/**
 * Make API request to Laravel backend
 */
function api_request(string $endpoint, string $method = 'GET', array $data = [], bool $withAuth = true): array
{
    $baseUrl = env('API_BASE_URL', 'http://localhost:8000/api');
    $url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
    
    $headers = ['Content-Type: application/json'];
    
    if ($withAuth) {
        $token = session()->get('api_token');
        if ($token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true) ?? [];
}
```

### API Endpoints Mapping

| CI4 Function | Laravel API Endpoint | Method |
|--------------|---------------------|--------|
| Login | `/api/auth/login` | POST |
| Register | `/api/auth/register` | POST |
| Get Services | `/api/services` | GET |
| Get Branches | `/api/branches` | GET |
| Get Gallery | `/api/gallery` | GET |
| Create Order | `/api/orders` | POST |
| Guest Order | `/api/guest/orders/{order_number}` | GET |
| User Orders | `/api/user/orders` | GET |
| User Order Detail | `/api/user/order/{orderNumber}` | GET |
| Admin Orders | `/api/admin/orders` | GET |
| Update Status | `/api/admin/orders/{order}/status` | PATCH |

---

## 🔐 Authentication

### Session Storage

Setelah login berhasil, data disimpan di session:

```php
session()->set([
    'isLoggedIn' => true,
    'api_token' => $response['token'],
    'user' => [
        'id' => $response['user']['id'],
        'name' => $response['user']['name'],
        'email' => $response['user']['email'],
        'role' => $response['user']['role'], // 'admin' atau 'customer'
        'phone' => $response['user']['phone'],
    ]
]);
```

### Auth Helper Functions

```php
// Cek apakah user sudah login
is_logged_in(): bool

// Cek apakah user adalah admin
is_admin(): bool

// Get user data dari session
get_user_data(): ?array

// Get API token
get_api_token(): ?string
```

### Auth Filter (Middleware)

File: `app/Filters/AuthFilter.php`

```php
// Untuk routes yang membutuhkan login
$routes->group('user', ['filter' => 'auth'], function($routes) {
    // ...
});

// Untuk routes admin
$routes->group('admin', ['filter' => 'auth:admin'], function($routes) {
    // ...
});
```

---

## 📊 Order Status Flow

### Status Values (Database Enum)

```php
$statuses = [
    'waiting_pickup'     => 'Menunggu Penjemputan',
    'dalam_penjemputan'  => 'Dalam Penjemputan',
    'in_progress'        => 'Sedang Diproses',
    'ready_for_delivery' => 'Siap Diantar',
    'on_delivery'        => 'Dalam Pengiriman',
    'completed'          => 'Selesai',
    'cancelled'          => 'Dibatalkan'
];
```

### Status Flow Diagram

```
┌─────────────────┐
│ waiting_pickup  │ (Menunggu Penjemputan)
└────────┬────────┘
         │
         ▼
┌─────────────────────┐
│ dalam_penjemputan   │ (Kurir sedang menjemput)
└────────┬────────────┘
         │
         ▼
┌─────────────────┐
│  in_progress    │ (Sedang dikerjakan)
└────────┬────────┘
         │
         ▼
┌─────────────────────┐
│ ready_for_delivery  │ (Selesai, siap diantar)
└────────┬────────────┘
         │
         ▼
┌─────────────────┐
│  on_delivery    │ (Kurir mengantarkan)
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   completed     │ (Selesai diterima customer)
└─────────────────┘

         │
         │ (Bisa dibatalkan sebelum in_progress)
         ▼
┌─────────────────┐
│   cancelled     │
└─────────────────┘
```

### Status Badge Colors

| Status | Badge Class | Color |
|--------|-------------|-------|
| waiting_pickup | `warning` | Yellow |
| dalam_penjemputan | `info` | Blue |
| in_progress | `primary` | Blue |
| ready_for_delivery | `success` | Green |
| on_delivery | `info` | Blue |
| completed | `success` | Green |
| cancelled | `danger` | Red |

---

## 🎮 Controllers

### BaseController

Semua controller extends dari `BaseController` yang menyediakan:

- Helper loading
- Common methods
- Response formatting

### Controller Namespaces

```php
namespace App\Controllers\Admin;      // Admin controllers
namespace App\Controllers\Auth;       // Authentication
namespace App\Controllers\Catalog;    // Public catalog
namespace App\Controllers\Order;      // Order & checkout
namespace App\Controllers\Payment;    // Payment processing
namespace App\Controllers\User;       // User dashboard
```

---

## 🎨 Views

### Layout System

```php
// Di view file
<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<!-- Custom CSS -->
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page content -->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Custom JS -->
<?= $this->endSection() ?>
```

### Available Layouts

| Layout | File | Usage |
|--------|------|-------|
| Main | `layouts/main.php` | Public pages |
| Admin | `layouts/admin.php` | Admin pages |

### Navbar Menu Structure

**Guest (Not Logged In):**
```
[Beranda] [Layanan] [Gallery] [Cabang] | [Lacak Pesanan] [Login] [Pesan Sekarang]
```

**User (Customer):**
```
[Beranda] [Layanan] [Gallery] [Cabang] | [Pesanan] [▼ Username]
                                                    ├── Dashboard
                                                    ├── Pesanan Saya
                                                    ├── Profile
                                                    └── Logout
```

**Admin:**
```
[Beranda] [Layanan] [Gallery] [Cabang] | [Pesanan] [▼ Admin Name]
                                                    ├── Admin Dashboard
                                                    ├── ──────────────
                                                    ├── Dashboard
                                                    ├── Pesanan Saya
                                                    ├── Profile
                                                    └── Logout
```

---

## 🔧 Helpers

### Format Helper (`format_helper.php`)

```php
// Format ke Rupiah
format_rupiah(50000);  // Output: Rp 50.000

// Format tanggal Indonesia
format_tanggal('2026-01-09');  // Output: 09 Januari 2026

// Format datetime
format_datetime('2026-01-09 14:30:00');  // Output: 09 Jan 2026, 14:30
```

### Auth Helper (`auth_helper.php`)

```php
// Check login status
if (is_logged_in()) { ... }

// Check admin role
if (is_admin()) { ... }

// Get user data
$user = get_user_data();
echo $user['name'];

// Get API token for requests
$token = get_api_token();
```

### API Helper (`api_helper.php`)

```php
// GET request
$services = api_request('/services', 'GET');

// POST request with auth
$result = api_request('/orders', 'POST', $orderData, true);

// Request without auth
$result = api_request('/guest/orders/ORD-123', 'GET', [], false);
```

---

## 🐛 Troubleshooting

### 1. 404 Route Not Found

**Problem:** `Can't find a route for 'GET: guest/track'`

**Solution:** Pastikan routes sudah benar di `Routes.php`:

```php
// ❌ SALAH (dalam group, jangan pakai prefix lagi)
$routes->group('guest', function($routes) {
    $routes->get('guest/track', ...);  // Double prefix!
});

// ✅ BENAR
$routes->group('guest', function($routes) {
    $routes->get('track', 'Order\GuestOrderController::trackPage');
});
```

### 2. API Connection Error

**Problem:** `cURL error: Connection refused`

**Solution:** Pastikan Laravel backend running:

```bash
cd laravel-backend
php artisan serve --port=8000
```

### 3. Session Not Working

**Problem:** Login berhasil tapi session tidak tersimpan

**Solution:** 
```bash
# Set permission writable folder
chmod -R 777 writable/

# Atau buat folder session
mkdir -p writable/session
chmod 777 writable/session
```

### 4. CORS Error

**Problem:** CORS policy blocking requests

**Solution:** Di Laravel backend, pastikan CORS sudah dikonfigurasi di `config/cors.php`:

```php
'allowed_origins' => ['http://localhost:8080'],
```

### 5. Status Update Error

**Problem:** `Data truncated for column 'order_status'`

**Solution:** Jalankan migration untuk update enum di Laravel:

```sql
ALTER TABLE orders MODIFY COLUMN order_status ENUM(
    'waiting_pickup',
    'dalam_penjemputan',
    'in_progress',
    'ready_for_delivery',
    'on_delivery',
    'completed',
    'cancelled'
) DEFAULT 'waiting_pickup';
```

---

## 📝 Development Notes

### Clear Cache

```bash
php spark cache:clear
```

### Run Development Server

```bash
php spark serve --port=8080
```

### Check Routes

```bash
php spark routes
```

---

## 📄 License

MIT License - feel free to use for your projects.

---

## 👥 Contributors

- Zein Kurnia

---

## 📞 Support

Jika ada pertanyaan atau masalah:
- Email: support@tapakbersih.com
- WhatsApp: +62 812-3456-7890

---

**Made with ❤️ using CodeIgniter 4**
