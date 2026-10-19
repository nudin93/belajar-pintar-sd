-- Pengembang: Zainudin Larau — Belajar Pintar SD (file migrasi, dijalankan 1x via phpMyAdmin).
-- Migrasi: bagian soal (tiap tambahan soal bisa dipisah per bagian).
-- Cara pakai: buka phpMyAdmin > pilih database > Import > pilih file ini > Go. 1x saja.
-- (Bila muncul error "Duplicate column name", abaikan — artinya kolom sudah ada.)
CREATE TABLE IF NOT EXISTS assignment_sections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  assignment_id INT NOT NULL, title VARCHAR(200) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sec_asg FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE assignment_questions ADD COLUMN section_id INT NULL AFTER assignment_id;
