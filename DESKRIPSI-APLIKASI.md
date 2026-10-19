# BELAJAR PINTAR SD — Deskripsi & Kegunaan Aplikasi
<!-- Pengembang: Zainudin Larau -->

**Pengembang: Zainudin Larau**

## Apa itu Belajar Pintar SD?
Belajar Pintar SD adalah aplikasi pembelajaran berbasis web untuk Sekolah Dasar
kelas 1–6, dibangun dengan **CodeIgniter 3 + AdminLTE 3 + MySQL** agar siap
dipasang di hosting cPanel sekolah. Alur intinya sederhana: guru memberi
materi dan tugas → siswa mengerjakan di buku tulis lalu memfoto jawabannya →
guru memeriksa dan memberi nilai → nilai dipublikasikan dan bisa dilihat siswa
dan orang tua.

## Untuk siapa?
- **Admin / operator sekolah** — mengelola data sekolah, tahun ajaran, guru,
  siswa, kelas, mata pelajaran, dan akun pengguna.
- **Guru / wali kelas** — mengelola materi, bank soal, tugas (termasuk foto
  soal dan foto contoh pengerjaan), memeriksa jawaban foto siswa, memberi
  nilai, membuka Kelas Live (video), dan melihat rekap nilai.
- **Siswa** — membaca materi, mengerjakan tugas, mengunggah foto jawaban dari
  kamera/galeri HP, melihat nilai dan riwayat belajar.
- **Kepala sekolah / orang tua** — memantau laporan nilai dan aktivitas belajar
  lewat menu laporan dan ekspor Excel/laporan cetak.

## Fitur utama
1. **Materi per kelas & mapel** — guru mengunggah materi, siswa hanya melihat
   materi kelasnya sendiri.
2. **Tugas dengan foto soal** — soal bisa berupa teks (PG/benar-salah/isian/
   uraian) plus foto soal dan foto contoh cara mengerjakan, langsung tampil di
   halaman siswa (klik untuk memperbesar).
3. **Kirim jawaban berupa foto** — siswa foto halaman buku tulis (kamera atau
   galeri), wajib minimal 1 foto untuk tugas foto; tugas pilihan ganda murni
   tetap bisa tanpa foto.
4. **Pemeriksaan & nilai** — guru memeriksa foto jawaban, memberi nilai
   0–100 + komentar, mempublikasikan atau meminta perbaikan; tugas yang sudah
   dinilai terkunci agar tidak diubah siswa (tombol kirim hilang, muncul lagi
   berwarna merah saat guru meminta kerjakan ulang).
5. **Bank soal ala Google Form** — tambah soal dinamis (PG/BS/Isian/Uraian/
   Foto), opsi PG bisa tambah sampai 10, kunci jawaban tersimpan, soal
   dikelompokkan per bagian dan per guru/pelajaran, bisa disalin ke tugas baru
   tanpa mengganggu tugas lama yang sudah dikerjakan.
6. **Kelas Live (video)** — guru membuka sesi video tertanam, siswa bergabung
   dari HP/laptop (butuh internet + izin kamera/mic).
7. **Impor siswa format Dapodik (XLSX)** — operator mengimpor file Lampiran
   Peserta Didik langsung (termasuk NISN, NIK, nama ibu), kelas dibuat/dicocokkan
   otomatis, lengkap dengan rekap per-kelas.
8. **Laporan & ekspor** — rekap nilai per kelas/mapel, ekspor Excel dan halaman
   cetak/PDF, riwayat dan prestasi siswa.
9. **Notifikasi dalam aplikasi** — pemberitahuan tugas baru, jawaban masuk,
   nilai terbit, dan permintaan perbaikan.
10. **Keamanan server** — Query Builder, password_hash, session + regenerate,
    CSRF aktif, validasi foto (tolak file PHP berbahaya), nama file acak,
    guard peran dan kepemilikan data, serta log audit.

## Manfaat
- Mengurangi kertas dan fotokopi: soal dan contoh tampil sebagai foto di HP.
- Cocok untuk HP kentang: halaman ringan, bisa foto langsung dari kamera.
- Tertib administrasi: data siswa/guru/kelas/nilai tersimpan rapi per tahun
  ajaran dan mudah diekspor untuk rapor atau Dapodik.
- Adil dan transparan: tugas terkunci setelah dinilai, perbaikan tercatat,
  riwayat tidak hilang.

## Teknologi
PHP 7.4+ (disarankan 8.x), MySQL/MariaDB, CodeIgniter 3.1.13 (disertakan di
folder `system/`), AdminLTE 3, jQuery + DataTables, Jitsi untuk Kelas Live.
Berjalan di cPanel biasa tanpa root/SSH; installer wizard `/install` memandu
pengisian database dan pembuatan admin pertama.
