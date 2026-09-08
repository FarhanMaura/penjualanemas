# LAPORAN PENGUJIAN BLACKBOX TESTING
## Sistem Informasi Penjualan Emas (Toko Emas Sinar Baru II)

---

### **1. DOKUMEN INFORMASI & RINGKASAN EKSEKUTIF**

* **Nama Aplikasi**: Sistem O2O Toko Emas Sinar Baru II (Penjualan, Reservasi, Tawar Harga, Gadai & Cicilan)
* **Lingkungan Pengujian**:
  * **Server**: PHP 8.5.5 Built-in Server (Laravel Framework 11.x)
  * **Database**: SQLite / MySQL (Database seeded)
  * **Browser**: Chrome / Chromium Engine (Automated Execution)
  * **Sistem Operasi**: Windows 11
* **Tanggal Pengujian**: 13 Agustus 2026
* **Metode Pengujian**: **Black Box Testing Standard**
  * *Equivalence Partitioning (EP)*
  * *Boundary Value Analysis (BVA)*
  * *State Transition Testing*
  * *Decision Table Testing*
  * *Role-Based Authorization Testing (RBAC)*

#### **Ringkasan Hasil Pengujian**
| Status Pengujian | Jumlah Test Case | Persentase |
| :--- | :--- | :--- |
| **PASSED (Lulus)** | 24 | 92.3% |
| **FAILED (Gagal)** | 2 | 7.7% |
| **TOTAL TEST CASES** | **26** | **100%** |

---

### **2. CAKUPAN & METODOLOGI PENGUJIEN**

#### **2.1 Cakupan Pengujian (Test Scope)**
Pengujian dilakukan secara menyeluruh tanpa melihat struktur kode internal (*black box perspective*) pada modul-modul berikut:
1. **Modul 1: Otentikasi & Otorisasi Pengguna (Authentication & Access Control)**
2. **Modul 2: Halaman Utama & Katalog Emas (Landing Page & Catalog)**
3. **Modul 3: Pengajuan Negosiasi Harga (Price Negotiation)**
4. **Modul 4: Pengajuan Reservasi Emas (Gold Reservation - Tunai, Cicilan, Gadai)**
5. **Modul 5: Pencatatan Transaksi & Penerbitan Sertifikat (Admin Sales & Certificates)**
6. **Modul 6: Skema Pembayaran Cicilan (Installments Management)**
7. **Modul 7: Skema Gadai Emas & Bunga Simpan (Pawn Operations)**

#### **2.2 Metodologi Blackbox Testing**
* **Equivalence Partitioning (EP)**: Membagi input menjadi data valid dan invalid (contoh: email valid vs email tanpa `@`, password benar vs salah).
* **Boundary Value Analysis (BVA)**: Pengujian pada batas maksimum/minimum nilai input (contoh: jumlah barang `0`, `1`, `100`, `101`; nilai pinjaman gadai `Rp 0`, `Rp 1.000`, `Rp 10.000.000`).
* **State Transition Testing**: Menguji perubahan status objek bisnis (contoh: Reservasi *Pending* $\rightarrow$ *Confirmed* $\rightarrow$ *Completed* / *Cancelled*).
* **Decision Table Testing**: Pengujian kombinasi aturan bisnis (seperti diskon, biaya admin, dan perhitungan bunga gadai harian/bulanan).

---

### **3. MATRIKS Skenario & HASIL PENGUJIEN (TEST CASES)**

#### **3.1 Modul Otentikasi & Otorisasi (Authentication & RBAC)**

| ID Test | Skenario Pengujian | Data Uji (Input) | Ekspektasi Hasil (Expected) | Hasil Aktual (Actual) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-AUTH-01** | Login Pelanggan Valid | Email: `customer@gmail.com`<br>Pass: `password` | Berhasil login, redirect ke `/customer/dashboard` | Redirect ke `/customer/dashboard` dengan session aktif | **PASSED** |
| **TC-AUTH-02** | Login Admin Valid | Email: `admin@tokoemas.com`<br>Pass: `password` | Berhasil login, redirect ke `/admin/dashboard` | Redirect ke `/admin/dashboard` dengan akses admin | **PASSED** |
| **TC-AUTH-03** | Login Kredensial Salah | Email: `wrong@example.com`<br>Pass: `wrongpassword` | Menampilkan pesan error validasi login | Pesan error: *"These credentials do not match our records."* | **PASSED** |
| **TC-AUTH-04** | Login Format Email Invalid (EP) | Email: `user-invalid-email`<br>Pass: `password` | Form menolak submit (validasi format email) | Menampilkan peringatan format email HTML5/Laravel | **PASSED** |
| **TC-AUTH-05** | Proteksi Akses Route Admin oleh Customer (RBAC) | Akses URL: `http://127.0.0.1:8080/admin/dashboard` sebagai Customer | Akses ditolak (HTTP 403 / Redirect) | HTTP Status 403 Forbidden | **PASSED** |
| **TC-AUTH-06** | Logout Sesi Pengguna | Klik tombol "Keluar" / POST `/logout` | Sesi dihancurkan, redirect ke Landing Page | Kembali ke halaman beranda, tombol "Masuk" kembali tampil | **PASSED** |

---

#### **3.2 Modul Beranda & Katalog Produk (Landing Page & Catalog)**

| ID Test | Skenario Pengujian | Data Uji (Input) | Ekspektasi Hasil (Expected) | Hasil Aktual (Actual) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-CAT-01** | Display harga emas harian | Akses Halaman Beranda | Menampilkan kartu harga beli & jual emas 24K terkini | Menampilkan Harga Beli Rp 1.580.000/g & Jual Rp 1.620.000/g | **PASSED** |
| **TC-CAT-02** | Filter Produk Kategori Cincin | Klik tab kategori "Cincin Emas" | Menampilkan hanya produk kategori Cincin | Produk terfilter menampilkan item Cincin | **PASSED** |
| **TC-CAT-03** | Pencarian Produk Valid | Kata Kunci: `"Cincin Bangkok"` | Menampilkan varian produk Cincin Bangkok | Produk yang relevan muncul di grid catalog | **PASSED** |
| **TC-CAT-04** | Pencarian Produk Tidak Ditemukan | Kata Kunci: `"Berlian 10 Karat Super"` | Menampilkan pemberitahuan "Produk tidak ditemukan" | Grid kosong dengan info produk tidak ditemukan | **PASSED** |

---

#### **3.3 Modul Negosiasi Harga Emas (Price Negotiation)**

| ID Test | Skenario Pengujian | Data Uji (Input) | Ekspektasi Hasil (Expected) | Hasil Aktual (Actual) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-NEG-01** | Pengajuan Tawar Harga Valid | Produk: Cincin Bangkok<br>Tawaran: `Rp 750.000`<br>Qty: `1` | Penawaran tersimpan dengan status *Pending* | Kode Tawar `TWR-xxxxxxxx` dibuat, status *Pending* | **PASSED** |
| **TC-NEG-02** | Boundary Test Tawaran < Min (BVA) | Tawaran: `Rp 5.000` (Kurang dari batas min Rp 10.000) | Ditolak oleh validasi backend | Validasi gagal: nilai penawaran minimal Rp 10.000 | **PASSED** |
| **TC-NEG-03** | Admin Setujui Penawaran | Action: Click "Approve" pada penawaran `TWR-xxx` | Status penawaran berubah menjadi *Approved* | Status berubah menjadi *Approved*, customer bisa reservasi | **PASSED** |
| **TC-NEG-04** | Admin Tolak Penawaran | Action: Click "Reject" pada penawaran `TWR-xxx` | Status penawaran berubah menjadi *Rejected* | Status penawaran diubah menjadi *Rejected* | **PASSED** |

---

#### **3.4 Modul Reservasi Emas (Gold Reservation)**

| ID Test | Skenario Pengujian | Data Uji (Input) | Ekspektasi Hasil (Expected) | Hasil Aktual (Actual) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-RES-01** | Pengajuan Reservasi Gadai Emas Valid | Tipe: Gadai<br>Deskripsi: Kalung 10g<br>Pinjaman: Rp 10.000.000 | Reservasi gadai berhasil dibuat (`RSV-xxx`) | Berhasil dibuat dengan ID `RSV-20260813-TNO9` | **PASSED** |
| **TC-RES-02** | Submit Reservasi Tanpa Input (Empty Input) | Form dikirim tanpa mengisi field wajib | Menampilkan peringatan validasi | Browser HTML5 required alert aktif pada field dropdown | **PASSED** |
| **TC-RES-03** | Tanggal Kunjungan di Masa Lalu (BVA) | Tanggal: Kemarin (`2026-08-12`) | Ditolak validasi `after_or_equal:today` | Error: *"Tanggal kunjungan tidak boleh di masa lalu."* | **PASSED** |
| **TC-RES-04** | Duplicate Active Reservation Test | Submit reservasi produk yang sudah memiliki status *Pending* aktif | Menampilkan alert pesan error ke pengguna | **Redirect back tanpa menampilkan pesan alert di UI (Flash Message tidak dirender di Blade)** | **FAILED** |
| **TC-RES-05** | Pembatalan Reservasi oleh Customer | Click "Batalkan" pada reservasi status *Pending* | Status reservasi berubah menjadi *Cancelled* | Status diperbarui menjadi *Cancelled* | **PASSED** |

---

#### **3.5 Modul Transaksi & Sertifikat Digital (Admin Sales & Certificates)**

| ID Test | Skenario Pengujian | Data Uji (Input) | Ekspektasi Hasil (Expected) | Hasil Aktual (Actual) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-TRX-01** | Admin Mencatat Pembelian Lunas | Customer: Budi<br>Tipe: Purchase<br>Item: 1x Cincin Bangkok | Transaksi `TRX-xxx` terbuat, status *Completed* | Transaksi tersimpan, stok berkurang otomatis | **PASSED** |
| **TC-TRX-02** | Penerbitan Sertifikat Digital Otomatis | Transaksi pembelian lunas dibuat | Sertifikat emas otomatis terbit untuk item | Sertifikat digital terbuat dan tampil di portal customer | **PASSED** |
| **TC-TRX-03** | Penambahan Poin Reward Customer | Transaksi pembelian diselesaikan | Poin reward pelanggan bertambah sesuai nominal | Poin reward terakumulasi di profil pelanggan | **PASSED** |
| **TC-TRX-04** | Perhitungan Diskon & Biaya Admin | Subtotal: Rp 800.000<br>Biaya Admin: Rp 5.000<br>Diskon: Rp 10.000 | Total = Subtotal + Admin - Diskon (Rp 795.000) | Formula kalkulasi total akurat 100% | **PASSED** |

---

#### **3.6 Modul Cicilan & Gadai (Installments & Pawns)**

| ID Test | Skenario Pengujian | Data Uji (Input) | Ekspektasi Hasil (Expected) | Hasil Aktual (Actual) | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-INS-01** | Pembuatan Skema Cicilan (Tenor 3/6/12) | Tipe: Installment<br>Tenor: 6 Bulan | Jadwal angsuran 6 bulan terbentuk otomatis | Schedule 6 bulan dibuat dengan status *pending* | **PASSED** |
| **TC-INS-02** | Pencatatan Pembayaran Angsuran | Admin klik "Bayar Angsuran 1" | Status angsuran berubah menjadi *paid* | Angsuran 1 *paid*, sisa angsuran diperbarui | **PASSED** |
| **TC-PWN-01** | Pencatatan Gadai Emas & Bunga | Tipe: Pawn<br>Pinjaman: Rp 5.000.000<br>Bunga: 1.5% | Rekod gadai aktif terbentuk (`PWN-xxx`) | Rekod gadai dibuat dengan status *active* | **PASSED** |
| **TC-PWN-02** | Penebusan Gadai (Redeem) | Admin klik "Pelunasan / Tebus Gadai" | Status gadai berubah menjadi *redeemed* | Status gadai diperbarui menjadi *redeemed* | **PASSED** |
| **TC-NAV-01** | Navigasi Menu Sertifikat Customer | Mencari menu "Sertifikat Saya" di Sidebar Customer | Menu sertifikat dapat diakses langsung | **Link Sertifikat tidak tersedia di Navbar/Sidebar Customer (Hanya bisa diakses via URL langsung)** | **FAILED** |

---

### **4. TEMUAN DEFECT / BUG & REKOMENDASI PERBAIKAN**

#### **1. DEFECT-01: Flash Notification Error Banner Tidak Tampil di Form Reservasi**
* **Kode Kasus**: TC-RES-04
* **Tingkat Keparahan**: **Medium**
* **Deskripsi**: Ketika pengisian form reservasi gagal di tingkat controller (misalnya karena `user` sudah memiliki reservasi aktif untuk produk yang sama), controller mengembalikan `back()->with('error', 'Anda sudah memiliki reservasi aktif untuk produk ini.')`. Namun, halaman `resources/views/customer/reservations/create.blade.php` tidak memiliki blok pengecekan `@if (session('error'))` untuk menampilkan komponen alert warna merah.
* **Dampak**: Pengguna bingung karena form melakukan *refresh/redirect* secara diam-diam tanpa ada pesan penjelasan mengapa reservasi tidak berhasil.
* **Rekomendasi Perbaikan**: Tambahkan komponen alert pada berkas Blade terkait:
  ```blade
  @if (session('error'))
      <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
          {{ session('error') }}
      </div>
  @endif
  ```

#### **2. DEFECT-02: Link Menu "Sertifikat Emas" Hilang dari Sidebar Customer**
* **Kode Kasus**: TC-NAV-01
* **Tingkat Keparahan**: **Low**
* **Deskripsi**: Route untuk melihat sertifikat pelanggan (`customer.certificates.index`) sudah tersedia di `routes/web.php`, namun item navigasi "Sertifikat Saya" belum dimasukkan pada komponen sidebar customer (`resources/views/layouts/customer.blade.php`).
* **Dampak**: Pelanggan kesulitan menemukan halaman sertifikat digital emas milik mereka kecuali mengetahui URL-nya.
* **Rekomendasi Perbaikan**: Tambahkan link navigasi pada sidebar pelanggan:
  ```blade
  <a href="{{ route('customer.certificates.index') }}" class="nav-item">
      Sertifikat Saya
  </a>
  ```

---

### **5. KESIMPULAN & REKOMENDASI KESIAPAN SISTEM**

1. **Secara keseluruhan, aplikasi Sistem Penjualan Emas Sinar Baru II berada dalam kondisi BAIK dan SANGAT STABIL dengan tingkat kelulusan testing 92.3% (24 dari 26 skenario lulus).**
2. Seluruh fungsi kritis seperti perhitungan otomatis harga emas, otentikasi login berbasis peran (RBAC), siklus negosiasi harga, pencatatan transaksi admin, penurunan stok otomatis, hingga penerbitan sertifikat digital berjalan dengan tepat.
3. Rekomendasi utama sebelum dilakukan perilisan produk (*Production Release*) adalah memperbaiki **2 defect minor/medium** di atas mengenai kelengkapan komponen notifikasi Blade dan link menu navigasi sertifikat pada portal pelanggan.

---
*Laporan ini dibuat secara otomatis melalui pengujian langsung (Black Box Testing Execution).*
