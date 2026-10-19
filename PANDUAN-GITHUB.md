# Pasang dari GitHub — Belajar Pintar SD (Pengembang: Zainudin Larau)
1. Clone: `git clone https://github.com/USERNAME/belajar-pintar-sd.git` lalu upload isi folder ke hosting (public_html/belajar/).
   - Tanpa git: tombol hijau Code > Download ZIP > ekstrak > upload.
2. Buat database + user di cPanel, catat kredensialnya.
3. Buka `https://domain/belajar/index.php/install` > isi kredensial > import otomatis > buat admin.
4. HAPUS `application/controllers/Install.php` dari hosting, lalu login di `/index.php/auth`.
5. Opsional: rename `htaccess-ROOT-RENAME.txt` > `.htaccess` (URL tanpa index.php) dan `uploads/htaccess-UPLOAD-RENAME.txt` > `uploads/.htaccess`.
Panduan lengkap + deskripsi kegunaan: baca README.md dan DESKRIPSI-APLIKASI.md.
