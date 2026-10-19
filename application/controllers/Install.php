<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
// Installer wizard: HAPUS file ini setelah admin pertama dibuat.
// Langkah: 1) isi kredensial DB (tulis database.php) -> 2) import database.sql otomatis
// -> 3) buat admin pertama. Sengaja TIDAK memakai User_model / $this->db agar tetap
// jalan walau database.php masih placeholder (library database di-autoload sehingga
// koneksi CI bisa gagal; wizard memakai mysqli mentah + diagnosis ramah).
class Install extends CI_Controller {

  private $required_tables = array(
    'application_settings', 'academic_years', 'schools', 'users', 'teachers',
    'students', 'classes', 'class_students', 'subjects', 'class_subjects',
    'learning_materials', 'assignments', 'assignment_questions', 'question_options',
    'student_submissions', 'submission_photos', 'student_answers', 'grades',
    'teacher_feedback', 'notifications', 'audit_logs'
  );

  public function index() {
    $data = array('err' => '', 'ok' => '');
    $info = array();
    $data['checks'] = $this->diagnose($info);

    if ($this->input->method() === 'post') {
      $step = $this->input->post('step');
      if ($step === 'db_save') {
        $this->save_db_config($data);
      } elseif ($step === 'import') {
        $this->import_sql($data);
      } elseif ($step === 'admin') {
        $this->create_admin($data);
      }
      $data['checks'] = $this->diagnose($info);
    }

    $data['info'] = $info;
    $data['stage'] = $this->stage($info);
    $this->load->view('auth/install', $data);
  }

  private function diagnose(&$info) {
    $info = array(
      'php_ok' => version_compare(PHP_VERSION, '5.6', '>='),
      'php_ver' => PHP_VERSION,
      'mysqli_ok' => extension_loaded('mysqli'),
      'db_file_writable' => is_writable(APPPATH . 'config/database.php'),
      'cfg' => $this->read_db_config(),
      'conn_ok' => false, 'conn_err' => '',
      'tables_ok' => false, 'tables_have' => 0, 'tables_need' => count($this->required_tables),
      'admin_count' => null,
    );
    $c = $info['cfg'];
    $info['cfg_filled'] = ($c && strpos($c['username'], 'ISI_') !== 0 && strpos($c['database'], 'ISI_') !== 0);
    if ($info['mysqli_ok'] && $info['cfg_filled']) {
      $m = $this->connect($info['conn_err']);
      if ($m) {
        $info['conn_ok'] = true;
        $have = $this->existing_tables($m);
        $info['tables_have'] = count(array_intersect($this->required_tables, $have));
        $info['tables_ok'] = ($info['tables_have'] >= count($this->required_tables));
        if (in_array('users', $have, true)) {
          $r = $m->query('SELECT COUNT(*) AS n FROM `users`');
          if ($r) { $row = $r->fetch_assoc(); $info['admin_count'] = (int)$row['n']; $r->free(); }
        }
        $m->close();
      }
    }
    $admin_n = $info['admin_count'];
    return array(
      array('label' => 'PHP >= 5.6 (terdeteksi ' . $info['php_ver'] . ')', 'ok' => $info['php_ok']),
      array('label' => 'Ekstensi PHP mysqli aktif', 'ok' => $info['mysqli_ok']),
      array('label' => 'database.php sudah diisi (bukan ISI_...)', 'ok' => $info['cfg_filled']),
      array('label' => 'Koneksi ke database', 'ok' => $info['conn_ok'], 'detail' => $info['conn_err']),
      array('label' => 'Tabel lengkap (' . $info['tables_have'] . '/' . $info['tables_need'] . ')', 'ok' => $info['tables_ok']),
      array('label' => 'Admin sudah ada (installer terkunci)', 'ok' => ($admin_n > 0), 'neutral' => ($admin_n === null || $admin_n === 0)),
      array('label' => 'database.php bisa ditulis installer', 'ok' => $info['db_file_writable']),
    );
  }

  private function stage($info) {
    if ($info['admin_count'] > 0) return 'locked';
    if (!$info['cfg_filled'] || !$info['conn_ok']) return 'db';
    if (!$info['tables_ok']) return 'import';
    return 'admin';
  }

  private function read_db_config() {
    $f = APPPATH . 'config/database.php';
    if (!is_file($f)) return null;
    $src = @file_get_contents($f);
    if ($src === false) return null;
    $out = array('hostname' => 'localhost', 'username' => '', 'password' => '', 'database' => '');
    foreach ($out as $k => $v) {
      if (preg_match("/'" . $k . "'\\s*=>\\s*'((?:[^'\\\\]|\\\\.)*)'/", $src, $m)) $out[$k] = stripcslashes($m[1]);
    }
    return $out;
  }

  private function connect(&$err) {
    $c = $this->read_db_config();
    if (!$c) { $err = 'File application/config/database.php tidak ditemukan.'; return null; }
    if (function_exists('mysqli_report')) mysqli_report(MYSQLI_REPORT_OFF);
    $m = @new mysqli($c['hostname'], $c['username'], $c['password'], $c['database']);
    if ($m->connect_errno) { $err = 'Koneksi gagal (' . $m->connect_errno . '): ' . $m->connect_error; return null; }
    $m->set_charset('utf8mb4');
    $err = '';
    return $m;
  }

  private function existing_tables($m) {
    $have = array();
    $r = $m->query('SHOW TABLES');
    if ($r) { while ($row = $r->fetch_array(MYSQLI_NUM)) $have[] = $row[0]; $r->free(); }
    return $have;
  }

  private function save_db_config(&$data) {
    $host = trim((string)$this->input->post('hostname'));
    $user = trim((string)$this->input->post('username'));
    $pass = (string)$this->input->post('password');
    $name = trim((string)$this->input->post('database'));
    if ($host === '' || $user === '' || $name === '') { $data['err'] = 'Hostname, username, dan nama database wajib diisi.'; return; }
    if (!preg_match('/^[A-Za-z0-9_.\-]+$/', $host) || !preg_match('/^[A-Za-z0-9_\-]+$/', $user) || !preg_match('/^[A-Za-z0-9_\-]+$/', $name)) {
      $data['err'] = 'Karakter hostname/username/database tidak valid (huruf, angka, _ - . saja).';
      return;
    }
    $content = "<?php\n"
      . "defined('BASEPATH') OR exit('No direct script access allowed');\n"
      . "// Ditulis otomatis oleh installer (Install.php) pada " . date('Y-m-d H:i:s') . ". HAPUS Install.php setelah dipakai.\n"
      . "\$active_group = 'default';\n\$query_builder = TRUE;\n"
      . "\$db['default'] = array(\n"
      . "  'dsn' => '',\n"
      . "  'hostname' => " . $this->qstr($host) . ",\n"
      . "  'username' => " . $this->qstr($user) . ",\n"
      . "  'password' => " . $this->qstr($pass) . ",\n"
      . "  'database' => " . $this->qstr($name) . ",\n"
      . "  'dbdriver' => 'mysqli',\n  'dbprefix' => '',\n  'pconnect' => FALSE,\n"
      . "  'db_debug' => FALSE,\n"
      . "  'cache_on' => FALSE,\n  'char_set' => 'utf8mb4',\n  'dbcollat' => 'utf8mb4_general_ci',\n"
      . "  'swap_pre' => '',\n  'encrypt' => FALSE,\n  'compress' => FALSE,\n  'stricton' => FALSE,\n"
      . "  'failover' => array(),\n  'save_queries' => TRUE\n);\n";
    $f = APPPATH . 'config/database.php';
    if (!is_writable($f)) { $data['err'] = 'database.php tidak bisa ditulis (permission). Isi manual via File Manager lalu refresh halaman ini.'; return; }
    if (@file_put_contents($f, $content, LOCK_EX) === false) { $data['err'] = 'Gagal menulis database.php. Isi manual via File Manager lalu refresh.'; return; }
    $data['ok'] = 'Kredensial tersimpan. Lanjut ke langkah berikutnya di bawah.';
  }

  private function qstr($s) {
    return "'" . str_replace(array('\\', "'"), array('\\\\', "\\'"), $s) . "'";
  }

  private function import_sql(&$data) {
    $sql_file = FCPATH . 'database.sql';
    if (!is_file($sql_file)) { $data['err'] = 'File database.sql tidak ditemukan di root aplikasi. Upload ulang paket ZIP.'; return; }
    $m = $this->connect($err);
    if (!$m) { $data['err'] = $err; return; }
    $sql = @file_get_contents($sql_file);
    if ($sql === false) { $m->close(); $data['err'] = 'database.sql tidak bisa dibaca.'; return; }
    $sql = str_replace("\r\n", "\n", $sql);
    $parts = preg_split('/;[ \t]*\n/', $sql);
    $ok = 0; $fail = array();
    foreach ($parts as $stmt) {
      $stmt = trim($stmt);
      if ($stmt === '') continue;
      if (!$m->query($stmt)) $fail[] = 'Error ' . $m->errno . ': ' . $m->error;
      else $ok++;
    }
    $m->close();
    if ($fail) $data['err'] = 'Import jalan ' . $ok . ' perintah, ' . count($fail) . ' gagal. Kesalahan pertama: ' . $fail[0] . ' (Alternatif: import manual via phpMyAdmin.)';
    else $data['ok'] = 'Import berhasil (' . $ok . ' perintah). Lanjut buat admin di bawah.';
  }

  private function create_admin(&$data) {
    $m = $this->connect($err);
    if (!$m) { $data['err'] = $err; return; }
    $have = $this->existing_tables($m);
    if (!in_array('users', $have, true)) { $m->close(); $data['err'] = 'Tabel users belum ada. Jalankan import dulu.'; return; }
    $r = $m->query('SELECT COUNT(*) AS n FROM `users`');
    $row = $r ? $r->fetch_assoc() : null;
    if ($r) $r->free();
    if ($row && (int)$row['n'] > 0) { $m->close(); $data['err'] = 'Installer dikunci: sudah ada pengguna. Hapus application/controllers/Install.php sekarang.'; return; }
    $fn = trim((string)$this->input->post('full_name'));
    $un = trim((string)$this->input->post('username'));
    $p1 = (string)$this->input->post('p1');
    $p2 = (string)$this->input->post('p2');
    if ($fn === '' || $un === '') { $m->close(); $data['err'] = 'Nama dan username wajib diisi.'; return; }
    if (!preg_match('/^[A-Za-z0-9_.\-]{3,60}$/', $un)) { $m->close(); $data['err'] = 'Username 3-60 karakter (huruf/angka/_ - .).'; return; }
    if (strlen($p1) < 8) { $m->close(); $data['err'] = 'Password minimal 8 karakter.'; return; }
    if ($p1 !== $p2) { $m->close(); $data['err'] = 'Konfirmasi password tidak sama.'; return; }
    $dup = $m->query("SELECT id FROM `users` WHERE `username`='" . $m->real_escape_string($un) . "' LIMIT 1");
    if ($dup && $dup->num_rows > 0) { $dup->free(); $m->close(); $data['err'] = 'Username sudah dipakai. Pilih username lain.'; return; }
    if ($dup) $dup->free();
    $hash = password_hash($p1, PASSWORD_DEFAULT);
    $role = 'admin';
    $stmt = $m->prepare('INSERT INTO `users` (`username`,`password_hash`,`role`,`full_name`) VALUES (?,?,?,?)');
    if (!$stmt) { $m->close(); $data['err'] = 'Gagal menyiapkan query admin.'; return; }
    $stmt->bind_param('ssss', $un, $hash, $role, $fn);
    if (!$stmt->execute()) { $e = $stmt->error; $stmt->close(); $m->close(); $data['err'] = 'Gagal membuat admin: ' . $e; return; }
    $stmt->close(); $m->close();
    $data['ok'] = 'Admin "' . $un . '" dibuat. WAJIB: hapus application/controllers/Install.php sekarang via File Manager, lalu login.';
  }
}
