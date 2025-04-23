# Sistem Manajemen Inventaris Gudang

Sistem manajemen inventaris gudang dengan pendekatan service-to-service communication. Sistem ini terdiri dari tiga layanan utama yang saling berkomunikasi secara langsung menggunakan REST API.

## Arsitektur Sistem

Sistem terdiri dari tiga service utama:

1. **Product Service (Port 3002)**
   - Mengelola data produk
   - Provider untuk data produk
   - Consumer untuk transaksi

2. **Inventory Service (Port 3001)**
   - Mengelola stok dan lokasi barang
   - Provider untuk data inventaris
   - Consumer untuk data produk

3. **Transaction Service (Port 3003)**
   - Mencatat transaksi masuk/keluar
   - Provider untuk data transaksi
   - Consumer untuk data produk dan inventaris

## Teknologi yang Digunakan

- Frontend: HTML, CSS (Bootstrap), JavaScript
- Backend: Node.js dengan Express
- Database: In-memory (untuk demo)
- Komunikasi: REST API dengan format JSON

## Cara Menjalankan

1. Install dependencies:
   ```bash
   npm install
   ```

2. Jalankan ketiga service:
   ```bash
   # Terminal 1
   npm run start-product

   # Terminal 2
   npm run start-inventory

   # Terminal 3
   npm run start-transaction
   ```

3. Buka aplikasi frontend di browser:
   - Buka file `frontend/index.html`
   - Atau gunakan static file server

## API Endpoints

### Product Service (localhost:3002)

- `GET /products` - Mendapatkan semua produk
- `GET /products/:id` - Mendapatkan produk berdasarkan ID
- `POST /products` - Membuat produk baru
- `DELETE /products/:id` - Menghapus produk

### Inventory Service (localhost:3001)

- `GET /inventory` - Mendapatkan semua item inventaris
- `GET /inventory/:id` - Mendapatkan item inventaris berdasarkan ID
- `POST /inventory` - Menambah item inventaris baru
- `PUT /inventory/:id` - Update jumlah stok
- `DELETE /inventory/:id` - Menghapus item inventaris

### Transaction Service (localhost:3003)

- `GET /transactions` - Mendapatkan semua transaksi
- `POST /transactions` - Membuat transaksi baru

## Fitur

1. Manajemen Produk
   - Tambah, lihat, dan hapus produk
   - Kategorisasi produk

2. Manajemen Inventaris
   - Tracking stok per produk
   - Lokasi penyimpanan
   - Update stok otomatis saat transaksi

3. Manajemen Transaksi
   - Catat transaksi masuk/keluar
   - Validasi stok saat transaksi keluar
   - Riwayat transaksi

## Implementasi Service-to-Service Communication

1. **Inventory Service sebagai Consumer**
   - Mengambil data produk dari Product Service untuk menampilkan nama produk
   - Endpoint: `GET /products/:id`

2. **Transaction Service sebagai Consumer**
   - Mengambil data produk dari Product Service
   - Mengambil dan mengupdate data inventaris dari Inventory Service
   - Endpoints yang digunakan:
     - `GET /products/:id`
     - `GET /inventory`
     - `PUT /inventory/:id`
     - `POST /inventory` 