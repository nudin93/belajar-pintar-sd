# BELAJAR PINTAR SD — Edisi CodeIgniter 3
<!-- Pengembang: Zainudin Larau -->

**Pengembang: Zainudin Larau**

> **Kegunaan:** aplikasi pembelajaran SD kelas 1–6 — guru memberi materi dan
> tugas (termasuk foto soal), siswa memfoto jawaban buku tulis, guru memeriksa
> dan memberi nilai, semua terpantau lewat laporan. Deskripsi lengkap baca
> [`DESKRIPSI-APLIKASI.md`](DESKRIPSI-APLIKASI.md).

Aplikasi pembelajaran SD kelas 1–6 (migrasi dari versi PHP natif ke **CodeIgniter 3.1.13**, framework disertakan di folder `system/`). Tampilan AdminLTE 3 dan susunan menu **sama persis** seperti versi sebelumnya. Alur inti tetap: guru memberi tugas → siswa memfoto jawaban buku → guru memeriksa → nilai dipublikasikan.

Struktur MVC:
- `system/` — core CodeIgniter 3.1.13 (jangan diubah).
- `application/controllers/` — Auth, Install, Profile, Admin, Guru, Siswa, Laporan, Api.
- `application/models/` — User_model, Data_model, Materi_model, Tugas_model, Submisi_model, Nilai_model, Notif_model.
- `application/views/` — layouts (header, navbar, sidebar, footer) + admin/guru/siswa + auth + laporan.
- `application/core/MY_Controller.php` — auth, guard peran, render layout bersama.
- `application/helpers/bp_helper.php` — esc, status, audit, notifikasi, validasi foto.
- `application/config/` — config (CSRF AKTIF), database (ISI KREDENSIAL), routes, autoload.
- `assets/`, `uploads/`, `database.sql`, `index.php` (front controller).

## 1. Instalasi cPanel (cukup wizard /install)
1. Buat database + user di cPanel (MySQL Databases), beri hak penuh. Catat hostname (biasanya `localhost`), username, password, nama database.
2. Upload ZIP, ekstrak ke `public_html/belajar/` (contoh).
3. Buka `https://domain/belajar/index.php/install` (atau `https://domain/belajar/install` bila `.htaccess` sudah aktif). Halaman diagnosis akan menunjukkan apa yang kurang.
4. Langkah 1: isi kredensial database → Simpan & Tes Koneksi (installer menulis `application/config/database.php`; bila gagal karena permission, isi manual via File Manager lalu refresh).
5. Langkah 2: klik Import Otomatis `database.sql` (alternatif: phpMyAdmin > pilih database > Import `database.sql`).
6. Langkah 3: buat admin pertama (password min 8 karakter).
7. **HAPUS `application/controllers/Install.php`** lalu login `https://domain/belajar/index.php/auth`.
8. Opsional: rename `htaccess-ROOT-RENAME.txt` → `.htaccess` agar URL tanpa `index.php` (lalu uji `/install` dan `/auth`). Bila 404, kembali pakai `/index.php/...`.
9. Rename `uploads/htaccess-UPLOAD-RENAME.txt` → `uploads/.htaccess`.
10. Pastikan `application/cache/`, `application/logs/`, `uploads/` writable (755).
11. Isi: Data Sekolah, Tahun Ajaran, Guru, Siswa, Kelas (+anggota), Mapel. Uji 3 peran.

Bila install tetap gagal: baca tabel Diagnosis di halaman installer (menunjukkan PHP, mysqli, isi database.php, pesan koneksi MySQL persis, kelengkapan tabel) dan perbaiki sesuai pesannya.

## 2. URL penting (pakai `index.php/` bila `.htaccess` belum di-rename, mis. `/index.php/auth`)
- Login: `/auth` — Logout: `/auth/logout` — Install: `/install` (hapus setelah dipakai)
- Admin: `/admin/dashboard`, `/admin/guru`, `/admin/siswa`, `/admin/kelas`, `/admin/mapel`, `/admin/materi`, `/admin/bank_soal`, `/admin/tugas`, `/admin/pemeriksaan`, `/admin/periksa/ID`, `/admin/nilai`, `/admin/laporan`, `/admin/tahun`, `/admin/pengguna`, `/admin/pengaturan`, `/admin/backup`
- Guru: `/guru/dashboard`, `/guru/kelas`, `/guru/siswa`, `/guru/materi`, `/guru/bank_soal`, `/guru/tugas`, `/guru/tugas_form`, `/guru/jawaban`, `/guru/periksa/ID`, `/guru/nilai`, `/guru/rekap`
- Siswa: `/siswa/dashboard`, `/siswa/materi`, `/siswa/tugas`, `/siswa/tugas_detail/ID`, `/siswa/hasil`, `/siswa/riwayat`, `/siswa/prestasi`
- Ekspor: `/laporan/excel`, `/laporan/pdf` (otomatis dibatasi sesuai peran).

## 3. Keamanan (dijaga di server, bukan hanya menu)
Query Builder (auto-escape), password_hash/verify, session files + regenerate, **CSRF bawaan CI aktif** (semua form via form_open + token otomatis), esc() anti-XSS, validasi MIME foto (finfo+getimagesize+tolak polyglot PHP), nama file acak (random_bytes), guard kelas guru + kepemilikan siswa, audit_logs, guard BASEPATH di semua file application.

## 4. Pengujian wajib (cara + harapan) — sama seperti versi natif
1. Login 3 peran; siswa buka `/admin/dashboard` → 403; guru buka periksa milik guru lain → 403.
2. Tugas Draf tak tampil di siswa; Published tampil hanya di kelasnya.
3. Siswa: kamera & galeri, pratinjau, kirim 2 foto → bukti + Menunggu.
4. Guru membuka foto; URL acak tak tertebak; nilai DRAF tak terlihat siswa; publikasi → muncul di Hasil.
5. Minta perbaikan → status Perlu Diperbaiki + notifikasi.
6. Laporan filter + Excel terbuka + PDF via cetak. HP 360px rapi.
7. Upload shell.php              isian cocok kunci (| = alternatif), uraian & foto manual. Tanpa AI penilai tulisan tangan.
8. Batas default 5 foto @5 MB (Pengaturan; per-tugas bisa override). Format JPG/JPEG/PNG/WebP.

Catatan jujur: WhatsApp/email nonaktif (notifikasi dalam aplikasi penuh). Excel = HTML-table .xls. PDF = halaman cetak. Backup = dump via controller + anjuran backup cPanel.
