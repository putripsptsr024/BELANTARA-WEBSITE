BELANTARA - Belanja Langsung Antar
BELANTARA adalah platform e-commerce yang memungkinkan pengguna untuk berbelanja secara langsung dengan pengiriman cepat. Aplikasi ini mendukung input manual untuk produk, penyimpanan permanen di database MySQL, dan tanpa data dummy.
Struktur Folder

assets/: Berisi file statis (CSS, JS, gambar).
backend/: Berisi logika server PHP dan koneksi database.
index.html: File utama untuk frontend.

Prasyarat

Laragon (dengan MySQL dan PHP aktif)
Browser modern

Instalasi

Salin folder Belantara ke C:\laragon\www.
Buka phpMyAdmin (http://localhost/phpmyadmin) dan jalankan skrip SQL dari create_database.sql untuk membuat database belantara.
Pastikan konfigurasi database di backend/config/db.php sesuai (default Laragon: root, tanpa password).
Akses aplikasi di http://belantara.test.

Fitur

Input manual produk melalui form (admin).
Penyimpanan permanen di MySQL.
Autentikasi pelanggan dengan hash password.
Keranjang belanja dan checkout dengan konfirmasi via WhatsApp.
Dashboard admin untuk mengelola produk, pesanan, dan pelanggan.

Catatan
