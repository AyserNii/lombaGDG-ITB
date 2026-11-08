# BARENG

One Platform. Shared Needs. Better Impact.

BARENG adalah platform kolaborasi berbasis lokasi yang membantu masyarakat menggabungkan kebutuhan sehari-hari agar lebih hemat biaya, waktu, dan sumber daya. Alih-alih setiap orang melakukan aktivitas secara terpisah, BARENG menyatukan kebutuhan yang serupa ke dalam satu sistem cerdas untuk menciptakan efisiensi ekonomi dan lingkungan.

## Tentang Produk

Dalam kehidupan sehari-hari, banyak orang melakukan aktivitas yang sebenarnya bisa digabungkan:

- Memesan makanan dari restoran yang sama
- Membeli kebutuhan rumah tangga dari toko yang sama
- Mengirim barang menuju wilayah yang sama

Namun, setiap orang melakukannya sendiri-sendiri. Akibatnya, biaya pengiriman berulang, kendaraan melakukan perjalanan yang sebetulnya bisa dikonsolidasikan, dan penggunaan kemasan berpotensi semakin meningkat.

BARENG hadir untuk menyelesaikan masalah tersebut dengan solusi yang mempertemukan orang berdasarkan lokasi, tujuan, waktu, jenis kebutuhan, dan kapasitas pesanan, lalu menghasilkan rekomendasi grup yang relevan.

## Visi Produk

BARENG berkomitmen membangun ekosistem kolaborasi kebutuhan masyarakat yang:

- Mengurangi biaya pengiriman dan pembelian
- Mengoptimalkan penggunaan kendaraan dan sumber daya
- Menekan pemborosan kemasan dan perjalanan berulang
- Meningkatkan rasa gotong royong dalam komunitas
- Memberikan pengalaman belanja dan pengiriman yang lebih efisien

## Modul Utama

BARENG dibangun dalam satu platform dengan satu sistem Smart Matching Engine dan tiga jenis layanan utama:

### 1) BARENG MAKAN

Menggabungkan pesanan makanan dari merchant yang sama atau dengan skema pengiriman yang kompatibel.

Contoh:
- Dimas ingin memesan nasi goreng
- Andi ingin memesan ayam geprek
- Budi ingin memesan mie ayam

Ketiganya dapat bergabung dalam satu sesi agar biaya pengiriman bisa dibagi bersama.

### 2) BARENG BELANJA

Menggabungkan pembelian barang dari toko yang sama agar pengiriman atau minimum pembelian bisa dibagi secara efisien.

Contoh:
- Dimas membeli beras
- Andi membeli minyak
- Budi membeli telur

Semua item bisa dikumpulkan dalam satu sesi pembelian bersama untuk mengurangi beban logistik dan biaya.

### 3) BARENG KIRIM

Modul ekspansi untuk menggabungkan kebutuhan pengiriman barang dengan tujuan yang berdekatan.

Contoh:
- Dimas mengirim paket ke Depok
- Andi juga mengirim paket ke Depok

Sistem akan mendeteksi pola tujuan yang berdekatan dan menawarkan konsolidasi pengiriman.

## Target Pengguna dan Role

| Role | Tanggung Jawab |
| --- | --- |
| Customer | Membuat dan mengikuti sesi BARENG |
| Session Creator | Membuat sesi dan mengelola grup |
| Merchant | Mengelola toko, produk, dan pesanan |
| Admin | Mengelola platform dan memantau aktivitas |

Catatan: Session Creator bukan role yang terpisah di database. Setiap pengguna dapat menjadi pembuat sesi saat diperlukan.

### Hak Akses

#### Customer
- Registrasi dan login
- Mengelola profil dan alamat
- Membuat sesi
- Mencari sesi terdekat
- Bergabung dengan sesi
- Mengelola item pesanan
- Melihat pembagian biaya
- Melihat riwayat dan dampak

#### Merchant
- Mengelola informasi toko
- Mengelola menu atau produk
- Mengatur ketersediaan stok
- Melihat pesanan yang terkumpul
- Memperbarui status pesanan

#### Admin
- Mengelola pengguna
- Memverifikasi merchant
- Memantau transaksi
- Memantau sesi aktif
- Mengelola kategori
- Melihat statistik dampak lingkungan

## User Journey End-to-End

### Customer Journey
1. Register / Login  
   Pengguna membuat akun dan mengisi profil.
2. Set Location  
   Pengguna menentukan alamat atau lokasi pengiriman.
3. Choose Service  
   Memilih BARENG MAKAN, BARENG BELANJA, atau BARENG KIRIM.
4. Create or Discover Session  
   Pengguna membuat grup baru atau mencari grup yang sudah ada.
5. Smart Matching  
   Sistem mencari sesi berdasarkan merchant, lokasi, waktu, dan kapasitas.
6. Join Session  
   Pengguna bergabung, memilih barang, dan memasukkan jumlah pesanan.
7. Cost Calculation  
   Sistem menghitung total harga, ongkir, dan pembagian biaya.
8. Checkout  
   Pengguna mengonfirmasi pesanan.
9. Order Processing  
   Merchant menerima pesanan dan memprosesnya.
10. Completion & Impact  
   Pesanan selesai dan pengguna mendapatkan ringkasan penghematan.

## Detail Fitur Setiap Modul

### A. BARENG MAKAN

| Fitur | Deskripsi | Prioritas |
| --- | --- | --- |
| Restaurant discovery | Menampilkan merchant sekitar | P0 |
| Menu catalog | Daftar menu dan harga | P0 |
| Create group order | Membuat sesi pemesanan | P0 |
| Find nearby group | Mencari grup yang tersedia | P0 |
| Join group | Bergabung dengan grup | P0 |
| Cart management | Mengelola pesanan individu | P0 |
| Shared delivery fee | Membagi ongkir | P0 |
| Group progress | Menampilkan anggota dan total pesanan | P0 |
| Order status | Status pemesanan | P0 |
| Group recommendation | Rekomendasi grup dari sistem | P1 |

### B. BARENG BELANJA

| Fitur | Deskripsi | Prioritas |
| --- | --- | --- |
| Store discovery | Menampilkan toko sekitar | P0 |
| Product catalog | Daftar produk dan harga | P0 |
| Create group purchase | Membuat sesi belanja | P0 |
| Join purchase | Bergabung dengan pembelian | P0 |
| Shared cart | Keranjang bersama | P0 |
| Quantity management | Mengatur jumlah barang | P0 |
| Minimum purchase | Target minimum pembelian | P1 |
| Bulk discount | Diskon pembelian bersama | P1 |
| Shared delivery fee | Pembagian ongkir | P0 |
| Stock validation | Validasi stok | P0 |

### C. BARENG KIRIM

| Fitur | Deskripsi | Prioritas |
| --- | --- | --- |
| Delivery matching | Mencocokkan pengiriman dengan tujuan serupa | P1 |
| Route optimization | Memprioritaskan jalur pengiriman efisien | P1 |
| Shared delivery estimate | Estimasi biaya pengiriman bersama | P1 |
| Pickup coordination | Koordinasi penjemputan barang | P1 |
| Tracking status | Melacak status pengiriman | P2 |

## Smart Matching Engine

Core engine BARENG akan memadukan beberapa parameter untuk menentukan sesi yang paling cocok, yaitu:

- Lokasi pengguna dan merchant
- Tujuan pengiriman atau alamat tujuan
- Waktu pemesanan atau pengiriman
- Jenis kebutuhan dan kategori item
- Kapasitas grup dan jumlah pesanan
- Ketersediaan stok dan merchant

Dengan pendekatan ini, pengguna tidak hanya menemukan opsi yang dekat, tetapi juga sesi yang paling masuk akal secara logistik dan ekonomis.

## Nilai Utama yang Dihasilkan

BARENG tidak hanya sekadar fitur belanja atau pengiriman. Platform ini dirancang untuk mengubah kebiasaan konsumsi dan logistik menjadi model yang lebih terstruktur, efisien, dan berdampak sosial.

Beberapa nilai utamanya adalah:

- Efisiensi biaya: ongkos dibagi bersama dalam satu sesi
- Efisiensi waktu: lebih sedikit proses berulang dan pengiriman terpisah
- Efisiensi sumber daya: pemanfaatan kendaraan dan stok lebih optimal
- Keberlanjutan: pengurangan pemborosan kemasan dan perjalanan tidak perlu
- Kolaborasi masyarakat: orang-orang dengan kebutuhan serupa bisa terhubung lebih mudah

## Stack Teknologi

Proyek ini dibangun menggunakan Laravel sebagai framework backend utama, dengan arsitektur yang siap dikembangkan menjadi aplikasi web produk yang scalable.

- PHP / Laravel
- MySQL / SQLite untuk pengembangan lokal
- Blade templating
- Vite + JavaScript untuk frontend asset
- Composer + npm untuk dependency management

## Setup Proyek

### Persyaratan
- PHP 8.3+
- Composer
- Node.js dan npm
- Database lokal

### Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

### Menjalankan aplikasi

```bash
php artisan serve
```

Untuk mode development dengan Vite dan queue:

```bash
npm run dev
```

## Roadmap

### MVP
- BARENG MAKAN
- BARENG BELANJA
- Sistem matching sederhana
- Fitur checkout dan pembagian biaya

### Expansion
- BARENG KIRIM
- Tracking status pengiriman
- Rekomendasi grup berbasis AI
- Statistik dampak lingkungan dan penghematan

## Kontribusi

Kontribusi sangat terbuka untuk pengembangan fitur, perbaikan UI/UX, dan peningkatan sistem matching. Silakan buat branch baru, lakukan perubahan, dan kirim pull request dengan deskripsi yang jelas.

## Lisensi

Proyek ini dibuat untuk kebutuhan pengembangan produk dan dapat disesuaikan untuk kebutuhan tim maupun komunitas. Untuk lisensi proyek, sesuaikan dengan kebijakan internal tim atau gunakan lisensi yang berlaku di repository Anda.

---

BARENG adalah solusi kolaborasi kebutuhan masyarakat yang mengedepankan efisiensi, solidaritas, dan dampak nyata. Dengan satu platform dan satu sistem matching, kita bisa membuat aktivitas harian lebih terorganisir, lebih murah, dan lebih berkelanjutan.
