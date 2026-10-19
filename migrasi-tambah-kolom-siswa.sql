-- Pengembang: Zainudin Larau — Belajar Pintar SD (file migrasi, dijalankan 1x via phpMyAdmin).
-- =====================================================
-- MIGRASI 2026-10-18: kolom baru untuk impor Dapodik
-- Jalankan SEKALI di phpMyAdmin (pilih database > SQL > tempel > Go).
-- Aman untuk data lama (kolom baru boleh kosong/NULL).
-- =====================================================
ALTER TABLE `students`
  ADD COLUMN `nik` VARCHAR(20) NULL AFTER `nisn`,
  ADD COLUMN `nama_ibu` VARCHAR(150) NULL AFTER `birth_date`;
