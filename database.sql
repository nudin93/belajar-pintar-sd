-- =====================================================
-- BELAJAR PINTAR SD - database.sql
-- Pengembang: Zainudin Larau
-- Kegunaan: database aplikasi pembelajaran SD kelas 1-6
-- (materi, tugas foto jawaban, pemeriksaan, nilai, laporan).
-- Cara pakai: buat database di cPanel, lalu buka /install (atau /index.php/install)
-- dan ikuti wizard: isi kredensial DB -> import otomatis file ini -> buat admin pertama.
-- Import manual via phpMyAdmin tetap bisa sebagai alternatif.
-- Charset: utf8mb4. Engine: InnoDB.
-- =====================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS application_settings (
  setting_key VARCHAR(100) PRIMARY KEY,
  setting_value TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS academic_years (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(20) NOT NULL,
  semester ENUM('ganjil','genap') NOT NULL DEFAULT 'ganjil',
  is_active TINYINT(1) NOT NULL DEFAULT 0,
  start_date DATE NULL, end_date DATE NULL,
  KEY idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS schools (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  npsn VARCHAR(30) NULL, address TEXT NULL,
  principal VARCHAR(120) NULL, phone VARCHAR(40) NULL, email VARCHAR(120) NULL,
  logo_path VARCHAR(255) NULL, academic_year_id INT NULL,
  CONSTRAINT fk_school_year FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','guru','siswa') NOT NULL,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_role (role), KEY idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS teachers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  nip VARCHAR(40) NULL, phone VARCHAR(40) NULL, address TEXT NULL,
  CONSTRAINT fk_teacher_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  nis VARCHAR(40) NULL, nisn VARCHAR(40) NULL,
  nik VARCHAR(20) NULL,
  gender ENUM('L','P') NULL, birth_place VARCHAR(80) NULL, birth_date DATE NULL,
  nama_ibu VARCHAR(150) NULL,
  address TEXT NULL, phone VARCHAR(40) NULL,
  CONSTRAINT fk_student_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS classes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(20) NOT NULL,
  grade_level TINYINT NOT NULL,
  academic_year_id INT NULL,
  homeroom_teacher_id INT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_class_year FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE SET NULL,
  CONSTRAINT fk_class_home FOREIGN KEY (homeroom_teacher_id) REFERENCES teachers(id) ON DELETE SET NULL,
  KEY idx_grade (grade_level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS class_students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  class_id INT NOT NULL, student_id INT NOT NULL, academic_year_id INT NULL,
  UNIQUE KEY uq_class_student (class_id, student_id),
  CONSTRAINT fk_cs_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
  CONSTRAINT fk_cs_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS subjects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NULL, name VARCHAR(100) NOT NULL, description TEXT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS class_subjects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  class_id INT NOT NULL, subject_id INT NOT NULL, teacher_id INT NOT NULL,
  academic_year_id INT NULL,
  UNIQUE KEY uq_cst (class_id, subject_id, academic_year_id),
  CONSTRAINT fk_csub_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
  CONSTRAINT fk_csub_subj FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
  CONSTRAINT fk_csub_teach FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS learning_materials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL, description TEXT NULL, content MEDIUMTEXT NULL,
  class_id INT NULL, subject_id INT NULL, teacher_id INT NULL,
  topic VARCHAR(150) NULL, image_path VARCHAR(255) NULL, pdf_path VARCHAR(255) NULL,
  video_url VARCHAR(255) NULL, status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NULL,
  CONSTRAINT fk_mat_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL,
  CONSTRAINT fk_mat_subj FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL,
  CONSTRAINT fk_mat_teach FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL,
  KEY idx_mat_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS assignments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL, instructions MEDIUMTEXT NOT NULL,
  class_id INT NOT NULL, subject_id INT NOT NULL, teacher_id INT NOT NULL,
  academic_year_id INT NULL, start_at DATETIME NULL, due_at DATETIME NULL,
  max_score INT NOT NULL DEFAULT 100, attachment_path VARCHAR(255) NULL,
  max_photos TINYINT NOT NULL DEFAULT 5, max_file_mb TINYINT NOT NULL DEFAULT 5,
  status ENUM('draft','published','closed') NOT NULL DEFAULT 'draft',
  allow_late TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_asg_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
  CONSTRAINT fk_asg_subj FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
  CONSTRAINT fk_asg_teach FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
  KEY idx_asg_status (status), KEY idx_asg_due (due_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS assignment_questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  assignment_id INT NOT NULL, section_id INT NULL,
  question_type ENUM('pg','bs','isian','uraian','foto') NOT NULL DEFAULT 'foto',
  question_text MEDIUMTEXT NOT NULL, points INT NOT NULL DEFAULT 10,
  answer_key TEXT NULL, discussion MEDIUMTEXT NULL, sort_order INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_q_asg FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS assignment_sections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  assignment_id INT NOT NULL, title VARCHAR(200) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sec_asg FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS question_options (
  id INT AUTO_INCREMENT PRIMARY KEY,
  question_id INT NOT NULL, option_label VARCHAR(5) NOT NULL,
  option_text TEXT NOT NULL, is_correct TINYINT(1) NOT NULL DEFAULT 0,
  CONSTRAINT fk_opt_q FOREIGN KEY (question_id) REFERENCES assignment_questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS student_submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  assignment_id INT NOT NULL, student_id INT NOT NULL, class_id INT NULL,
  status ENUM('belum','sedang','dikumpulkan','menunggu','dinilai','perbaikan','terlambat') NOT NULL DEFAULT 'belum',
  submitted_at DATETIME NULL, updated_at DATETIME NULL,
  is_late TINYINT(1) NOT NULL DEFAULT 0,
  auto_score DECIMAL(6,2) NULL, final_score DECIMAL(6,2) NULL, is_published TINYINT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY uq_asg_stu (assignment_id, student_id),
  CONSTRAINT fk_sub_asg FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
  CONSTRAINT fk_sub_stu FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS submission_photos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  submission_id INT NOT NULL, file_path VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NULL, mime VARCHAR(50) NULL, file_size INT NULL,
  sort_order INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ph_sub FOREIGN KEY (submission_id) REFERENCES student_submissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS assignment_photos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  assignment_id INT NOT NULL, file_path VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NULL, mime VARCHAR(50) NULL, file_size INT NULL,
  sort_order INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_soal_asg FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS assignment_contoh (
  id INT AUTO_INCREMENT PRIMARY KEY,
  assignment_id INT NOT NULL, file_path VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NULL, mime VARCHAR(50) NULL, file_size INT NULL,
  sort_order INT NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_contoh_asg FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS student_answers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  submission_id INT NOT NULL, question_id INT NOT NULL,
  answer_text TEXT NULL, selected_option_id INT NULL,
  is_correct TINYINT(1) NULL, score DECIMAL(6,2) NULL,
  CONSTRAINT fk_ans_sub FOREIGN KEY (submission_id) REFERENCES student_submissions(id) ON DELETE CASCADE,
  CONSTRAINT fk_ans_q FOREIGN KEY (question_id) REFERENCES assignment_questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS grades (
  id INT AUTO_INCREMENT PRIMARY KEY,
  submission_id INT NOT NULL UNIQUE, score DECIMAL(6,2) NOT NULL,
  comment TEXT NULL, graded_by INT NULL, graded_at DATETIME NULL,
  published_at DATETIME NULL, is_published TINYINT(1) NOT NULL DEFAULT 0,
  revision_count INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_gr_sub FOREIGN KEY (submission_id) REFERENCES student_submissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS teacher_feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  submission_id INT NOT NULL, teacher_id INT NOT NULL,
  message TEXT NOT NULL, requires_revision TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_fb_sub FOREIGN KEY (submission_id) REFERENCES student_submissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL, title VARCHAR(200) NOT NULL, message TEXT NULL,
  link VARCHAR(255) NULL, is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  KEY idx_notif_user (user_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL, action VARCHAR(150) NOT NULL, detail TEXT NULL,
  ip_address VARCHAR(60) NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_audit_user (user_id), KEY idx_audit_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- Data awal: tahun ajaran, mapel, pengaturan (TANPA user bawaan; buat via wizard /install)
INSERT INTO academic_years (name, semester, is_active, start_date, end_date) VALUES
('2025/2026','ganjil',1,'2025-07-01','2025-12-31')
ON DUPLICATE KEY UPDATE name=name;

INSERT INTO subjects (code, name) VALUES
('BIN','Bahasa Indonesia'),('MTK','Matematika'),('IPAS','IPAS'),
('PP','Pendidikan Pancasila'),('PABP','Pendidikan Agama dan Budi Pekerti'),
('PJOK','PJOK'),('SB','Seni dan Budaya'),('BING','Bahasa Inggris')
ON DUPLICATE KEY UPDATE name=name;

INSERT INTO application_settings (setting_key, setting_value) VALUES
('school_name','SDN Contoh 01'),
('max_photo_mb','5'),
('max_photos_per_task','5'),
('allow_whatsapp','0'),
('timezone','Asia/Jakarta')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);
