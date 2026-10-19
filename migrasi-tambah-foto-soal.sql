-- Pengembang: Zainudin Larau — Belajar Pintar SD (file migrasi, dijalankan 1x via phpMyAdmin).
-- Migrasi: tabel foto soal tugas (foto lampiran guru, dilihat siswa).
-- Cara pakai: buka phpMyAdmin > pilih database > Import > pilih file ini > Go. 1x saja.
CREATE TABLE IF NOT EXISTS assignment_photos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  assignment_id INT NOT NULL, file_path VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NULL, mime VARCHAR(50) NULL, file_size INT NULL,
  sort_order INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_soal_asg FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
