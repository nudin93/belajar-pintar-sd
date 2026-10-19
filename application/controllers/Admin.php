<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
// Pola semua method CRUD: POST -> flashdata -> redirect (PRG). Hasil tampil sebagai toast di footer.
class Admin extends MY_Controller {
  private $_stu_extra = null; // cache field_exists nik/nama_ibu (migrasi 2026-10-18)
  public function __construct() {
    parent::__construct();
    $this->load->model(array('User_model', 'Data_model', 'Materi_model', 'Tugas_model', 'Submisi_model', 'Nilai_model', 'Notif_model'));
  }
  private function admin() { $this->require_role('admin'); }
  private function done($url, $ok, $err = '') {
    $this->session->set_flashdata($ok !== '' ? 'msg' : 'err', $ok !== '' ? $ok : $err);
    redirect($url);
  }

  public function index() { redirect('admin/dashboard'); }

  public function dashboard() {
    $this->admin();
    $c = function ($q) { return (int)$this->db->query($q)->row()->n; };
    $data = array('title' => 'Dashboard Admin', 'menu' => 'dashboard',
      'nSiswa' => $c("SELECT COUNT(*) n FROM users WHERE role='siswa' AND is_active=1"),
      'nGuru' => $c("SELECT COUNT(*) n FROM users WHERE role='guru' AND is_active=1"),
      'nKelas' => $c("SELECT COUNT(*) n FROM classes"), 'nMapel' => $c("SELECT COUNT(*) n FROM subjects WHERE is_active=1"),
      'nTugasAktif' => $c("SELECT COUNT(*) n FROM assignments WHERE status='published'"),
      'nMenunggu' => $c("SELECT COUNT(*) n FROM student_submissions WHERE status IN ('dikumpulkan','menunggu')"),
      'nDinilai' => $c("SELECT COUNT(*) n FROM student_submissions WHERE status='dinilai'"),
      'nBelum' => $c("SELECT COUNT(*) n FROM student_submissions WHERE status IN ('belum','sedang','perbaikan')"),
      'perMapel' => $this->db->query("SELECT s.name, COUNT(sb.id) j FROM subjects s LEFT JOIN assignments a ON a.subject_id=s.id LEFT JOIN student_submissions sb ON sb.assignment_id=a.id GROUP BY s.id ORDER BY s.name")->result_array(),
      'aktiv' => $this->Notif_model->recent_audit(10));
    $this->render('admin/dashboard', $data);
  }

  public function sekolah() {
    $this->admin();
    $data = array('title' => 'Data Sekolah', 'menu' => 'sekolah', 'msg' => '', 'sch' => array_merge(array('name' => '', 'npsn' => '', 'address' => '', 'principal' => '', 'phone' => '', 'email' => '', 'academic_year_id' => ''), (array)$this->Data_model->school()), 'years' => $this->Data_model->years());
    if ($this->input->method() === 'post') {
      $d = array('name' => trim($this->input->post('name')), 'npsn' => $this->input->post('npsn') ?: null, 'address' => $this->input->post('address') ?: null, 'principal' => $this->input->post('principal') ?: null, 'phone' => $this->input->post('phone') ?: null, 'email' => $this->input->post('email') ?: null, 'academic_year_id' => ($this->input->post('academic_year_id') !== '' ? (int)$this->input->post('academic_year_id') : null));
      if ($d['name'] === '') $data['msg'] = 'Nama sekolah wajib diisi.';
      else { $this->Data_model->save_school($d); audit('sekolah_update', 'Data sekolah disimpan'); $data['msg'] = 'Data sekolah disimpan.'; $data['sch'] = array_merge($data['sch'], (array)$this->Data_model->school()); }
    }
    $this->render('admin/sekolah', $data);
  }

  public function guru() {
    $this->admin();
    if ($this->input->method() === 'post') {
      $act = $this->input->post('act'); $ok = ''; $err = '';
      if ($act === 'save') {
        $un = trim($this->input->post('username')); $fn = trim($this->input->post('full_name')); $pw = (string)$this->input->post('password');
        $id = (int)$this->input->post('id');
        if ($fn === '') $err = 'Nama lengkap wajib diisi.';
        elseif ($id) {
          $row = $this->db->select('username')->get_where('users', array('id' => $id), 1)->row_array();
          $this->db->where('id', $id)->update('users', array('full_name' => $fn));
          $this->db->where('user_id', $id)->update('teachers', array('nip' => $this->input->post('nip') ?: null, 'phone' => $this->input->post('phone') ?: null));
          audit('guru_edit', 'Edit guru ' . ($row ? $row['username'] : $id)); $ok = 'Data guru diperbarui.';
        } else {
          if ($un === '' || strlen($pw) < 6) $err = 'Username dan password (min 6) wajib untuk guru baru.';
          elseif ($this->User_model->by_username($un)) $err = 'Username "' . $un . '" sudah dipakai. Gunakan username lain.';
          else {
            $this->db->trans_start();
            try { $uid = $this->User_model->create($un, $pw, 'guru', $fn); } catch (Exception $e) { $uid = 0; }
            if ($uid) {
              $this->db->insert('teachers', array('user_id' => $uid, 'nip' => $this->input->post('nip') ?: null, 'phone' => $this->input->post('phone') ?: null));
              $this->db->trans_complete();
              if ($this->db->trans_status()) { audit('guru_add', 'Tambah guru ' . $un); $ok = 'Guru "' . $fn . '" ditambahkan.'; }
              else $err = 'Gagal menyimpan.';
            } else { $this->db->trans_rollback(); $err = 'Gagal menyimpan (username mungkin duplikat).'; }
          }
        }
      }
      elseif ($act === 'toggle') { $this->User_model->toggle((int)$this->input->post('id')); $ok = 'Status akun diubah.'; }
      elseif ($act === 'reset') { $this->User_model->reset_pw((int)$this->input->post('id'), 'Guru1234'); $ok = 'Password direset menjadi Guru1234. Minta guru segera mengganti.'; }
      elseif ($act === 'del') { $this->User_model->delete((int)$this->input->post('id')); $ok = 'Data guru dihapus.'; }
      $this->done('admin/guru', $ok, $err !== '' ? $err : 'Terjadi kesalahan, coba lagi.');
    }
    $this->render('admin/guru', array('title' => 'Data Guru', 'menu' => 'guru', 'rows' => $this->Data_model->guru_list()));
  }

  public function siswa() {
    $this->admin();
    if ($this->input->method() === 'post') {
      $act = $this->input->post('act'); $ok = ''; $err = '';
      if ($act === 'save') {
        $un = trim($this->input->post('username')); $fn = trim($this->input->post('full_name')); $pw = (string)$this->input->post('password');
        $id = (int)$this->input->post('id');
        if ($fn === '') $err = 'Nama lengkap wajib diisi.';
        elseif ($id) {
          $this->db->where('id', $id)->update('users', array('full_name' => $fn));
          $stu = array('nis' => $this->input->post('nis') ?: null);
          if ($this->students_has_extra()) {
            $stu['nik'] = $this->clean_nik($this->input->post('nik'));
            $stu['nama_ibu'] = $this->clean_ibuname($this->input->post('nama_ibu'));
          }
          $this->db->where('user_id', $id)->update('students', $stu);
          $sid = $this->db->select('id')->get_where('students', array('user_id' => $id), 1)->row_array();
          if ($sid && $this->input->post('class_id') !== '') {
            $this->db->delete('class_students', array('student_id' => (int)$sid['id']));
            $this->db->insert('class_students', array('class_id' => (int)$this->input->post('class_id'), 'student_id' => (int)$sid['id']));
          }
          audit('siswa_edit', 'Edit siswa id ' . $id); $ok = 'Data siswa diperbarui.';
        } else {
          if ($un === '' || strlen($pw) < 6) $err = 'Username dan password (min 6) wajib untuk siswa baru.';
          elseif ($this->User_model->by_username($un)) $err = 'Username "' . $un . '" sudah dipakai. Gunakan username lain.';
          else {
            $this->db->trans_start();
            try { $uid = $this->User_model->create($un, $pw, 'siswa', $fn); } catch (Exception $e) { $uid = 0; }
            if ($uid) {
              $stu = array('user_id' => $uid, 'nis' => $this->input->post('nis') ?: null);
              if ($this->students_has_extra()) {
                $stu['nik'] = $this->clean_nik($this->input->post('nik'));
                $stu['nama_ibu'] = $this->clean_ibuname($this->input->post('nama_ibu'));
              }
              $this->db->insert('students', $stu);
              $sid = (int)$this->db->insert_id();
              if ($this->input->post('class_id') !== '') $this->db->insert('class_students', array('class_id' => (int)$this->input->post('class_id'), 'student_id' => $sid)); // siswa baru: baris pasti belum ada
              $this->db->trans_complete();
              if ($this->db->trans_status()) { audit('siswa_add', 'Tambah siswa ' . $un); $ok = 'Siswa "' . $fn . '" ditambahkan.'; }
              else $err = 'Gagal menyimpan.';
            } else { $this->db->trans_rollback(); $err = 'Gagal menyimpan (username mungkin duplikat).'; }
          }
        }
      }
      elseif ($act === 'toggle') { $this->User_model->toggle((int)$this->input->post('id')); $ok = 'Status akun diubah.'; }
      elseif ($act === 'reset') { $this->User_model->reset_pw((int)$this->input->post('id'), 'Siswa1234'); $ok = 'Password direset menjadi Siswa1234.'; }
      elseif ($act === 'bulkdel') {
        $ids = (array)$this->input->post('ids');
        $me = (int)$this->me()['id']; $n = 0;
        foreach ($ids as $did) {
          $did = (int)$did;
          if ($did <= 0 || $did === $me) continue;
          $row = $this->db->select('role')->get_where('users', array('id' => $did), 1)->row_array();
          if (!$row || $row['role'] !== 'siswa') continue;
          $this->User_model->delete($did); $n++;
        }
        audit('siswa_bulkdel', 'Hapus massal: ' . $n . ' siswa');
        if ($n) $ok = $n . ' siswa dihapus.';
        else $err = 'Tidak ada yang dihapus (centang dulu).';
      }
      elseif ($act === 'import' && !empty($_FILES['upfile']['tmp_name'])) {
        $nm = (string)$_FILES['upfile']['name'];
        $ext = strtolower(pathinfo($nm, PATHINFO_EXTENSION));
        if ((int)$_FILES['upfile']['size'] > 5 * 1024 * 1024) $err = 'File melebihi 5 MB.';
        elseif ($ext === 'xlsx') $this->import_dapodik($_FILES['upfile']['tmp_name'], $ok, $err);
        elseif ($ext === 'csv') {
          $n = 0; $dup = 0; $fh = fopen($_FILES['upfile']['tmp_name'], 'r');
          while (($r = fgetcsv($fh)) !== false) {
            if (count($r) < 3) continue;
            list($fn, $un, $pw) = array_map('trim', $r);
            if ($un === '' || $fn === '' || strlen($pw) < 6 || $this->User_model->by_username($un)) { $dup++; continue; }
            try { $uid = $this->User_model->create($un, $pw, 'siswa', $fn); $this->db->insert('students', array('user_id' => $uid)); $n++; } catch (Exception $e) { $dup++; }
          }
          fclose($fh); $ok = 'Impor selesai: ' . $n . ' ditambahkan' . ($dup ? ', ' . $dup . ' dilewati (duplikat/tak valid)' : '') . '.';
        }
        else $err = 'Format ditolak. Unggah file .csv (nama,username,password) atau .xlsx Dapodik.';
      }
      $this->done('admin/siswa', $ok, $err !== '' ? $err : 'Terjadi kesalahan, coba lagi.');
    }
    $this->render('admin/siswa', array('title' => 'Data Siswa', 'menu' => 'siswa', 'rows' => $this->Data_model->siswa_list(), 'kelas' => $this->Data_model->classes()));
  }

  // Impor XLSX Dapodik (Lampiran Peserta Didik): deteksi header, gabung baris
  // lanjutan (sel wrap), buat akun siswa + profil + penempatan kelas otomatis.
  private function import_dapodik($tmp, &$ok, &$err) {
    $pw = trim((string)$this->input->post('pw_awal'));
    if ($pw === '') $pw = 'Siswa1234';
    if (strlen($pw) < 6) { $err = 'Password awal minimal 6 karakter.'; return; }
    $this->load->library('Xlsx_reader');
    $rows = $this->xlsx_reader->read($tmp, 0);
    if (!$rows) { $err = 'Excel tidak bisa dibaca' . ($this->xlsx_reader->error !== '' ? ': ' . $this->xlsx_reader->error : '.'); return; }
    // Cari baris header (memuat "nama" + nisn/nis/nik).
    $hidx = null;
    foreach ($rows as $r) {
      $low = array();
      foreach ($r as $c) $low[] = mb_strtolower(trim((string)$c));
      if (in_array('nama', $low, true) && (in_array('nisn', $low, true) || in_array('nis', $low, true) || in_array('nik', $low, true))) {
        $hidx = array();
        foreach ($r as $j => $c) {
          $k = mb_strtolower(trim((string)$c));
          if ($k !== '' && !isset($hidx[$k])) $hidx[$k] = $j;
        }
        break;
      }
    }
    if (!$hidx) { $err = 'Header tidak dikenali. Pastikan ada kolom Nama + NISN/NIS/NIK.'; return; }
    $col = function ($names) use ($hidx) {
      foreach ((array)$names as $n) if (isset($hidx[$n])) return $hidx[$n];
      return null;
    };
    $cNo = $col('no'); $cNama = $col(array('nama', 'nama siswa', 'nama peserta didik', 'nama lengkap'));
    $cJk = $col(array('l/p', 'jk', 'jkl', 'jenis kelamin', 'kelamin', 'l-p'));
    $cLahir = $col(array('tgl lahir', 'tanggal lahir', 'ttl', 'tanggal_lahir'));
    $cNis = $col(array('nis', 'no induk', 'nomor induk'));
    $cNisn = $col(array('nisn'));
    $cNik = $col(array('nik', 'no nik', 'nomor nik'));
    $cIbu = $col(array('nama ibu', 'nama_ibu', 'ibu', 'nama ibu kandung'));
    $cKelas = $col(array('tingkat', 'kelas', 'rombel', 'rombongan belajar', 'kelas/rombel'));
    if ($cNama === null) { $err = 'Kolom Nama tidak ditemukan.'; return; }
    $val = function ($r, $c) { return ($c === null || !isset($r[$c])) ? '' : trim((string)$r[$c]); };
    // Kelompokkan: baris baru bila kolom No terisi angka; baris lanjutan digabung.
    $recs = array();
    $header_seen = false;
    foreach ($rows as $r) {
      if (!$header_seen) {
        $low = array();
        foreach ($r as $c) $low[] = mb_strtolower(trim((string)$c));
        if (in_array('nama', $low, true)) { $header_seen = true; }
        continue;
      }
      $cells = array('nama' => $val($r, $cNama), 'jk' => $val($r, $cJk), 'lahir' => $val($r, $cLahir),
        'nis' => $val($r, $cNis), 'nisn' => preg_replace('/\D/', '', $val($r, $cNisn)),
        'nik' => preg_replace('/\D/', '', $val($r, $cNik)), 'ibu' => preg_replace('/\s+/', ' ', $val($r, $cIbu)),
        'kelas' => $val($r, $cKelas), 'no' => $val($r, $cNo));
      $any = ($cells['nama'] !== '' || $cells['nisn'] !== '' || $cells['nis'] !== '' || $cells['nik'] !== '' || $cells['jk'] !== '' || $cells['lahir'] !== '' || $cells['kelas'] !== '');
      if (!$any) continue;
      if ($cells['no'] !== '' && preg_match('/^\d+$/', $cells['no'])) { $recs[] = $cells; }
      elseif (!empty($recs)) {
        $last = count($recs) - 1;
        if ($cells['nama'] !== '') $recs[$last]['nama'] .= ' ' . $cells['nama'];
        if ($cells['nisn'] !== '') $recs[$last]['nisn'] .= $cells['nisn'];
        if ($cells['nis'] !== '') $recs[$last]['nis'] .= $cells['nis'];
        if ($cells['nik'] !== '') $recs[$last]['nik'] .= $cells['nik'];
        if ($cells['ibu'] !== '') $recs[$last]['ibu'] .= ($recs[$last]['ibu'] !== '' ? ' ' : '') . $cells['ibu'];
        if ($recs[$last]['lahir'] === '' && $cells['lahir'] !== '') $recs[$last]['lahir'] = $cells['lahir'];
        if ($recs[$last]['kelas'] === '' && $cells['kelas'] !== '') $recs[$last]['kelas'] = $cells['kelas'];
        if ($recs[$last]['jk'] === '' && $cells['jk'] !== '') $recs[$last]['jk'] = $cells['jk'];
      }
    }
    if (!$recs) { $err = 'Tidak ada data siswa terbaca setelah header.'; return; }    $kelasCache = array(); // grade => id
    foreach ($this->Data_model->classes() as $k) {
      $g = (int)$k['grade_level'];
      if (!isset($kelasCache[$g])) $kelasCache[$g] = array('ids' => array(), 'byname' => array());
      $kelasCache[$g]['ids'][] = (int)$k['id'];
      $kelasCache[$g]['byname'][mb_strtolower(preg_replace('/\s+/', '', $k['name']))] = (int)$k['id'];
    }
    $n = 0; $dup = 0; $bad = 0; $noClass = 0; $perKelas = array(); $fails = array(); $noClassNames = array();
    foreach ($recs as $rc) {
      $nama = preg_replace('/\s+/', ' ', trim($rc['nama']));
      if ($nama === '') { $bad++; continue; }
      $un = '';
      if (preg_match('/^[A-Za-z0-9_.\-]{3,60}$/', $rc['nis'])) $un = $rc['nis'];
      elseif (preg_match('/^\d{3,60}$/', $rc['nisn'])) $un = $rc['nisn'];
      else {
        $slug = preg_replace('/[^a-z0-9]/', '', mb_strtolower($nama));
        if (strlen($slug) < 3) $slug .= 'siswa';
        $un = $slug; $i = 2;
        while ($this->User_model->by_username($un)) $un = $slug . $i++;
      }
      if ($this->User_model->by_username($un)) { $dup++; continue; }
      if ($rc['nisn'] !== '' && strlen($rc['nisn']) >= 9) {
        $dupe = $this->db->select('s.id')->from('students s')->where('s.nisn', $rc['nisn'])->limit(1)->get()->row_array();
        if ($dupe) { $dup++; continue; }
      }
      $this->User_model->create($un, $pw, 'siswa', $nama);
      $urow = $this->User_model->by_username($un);
      if (!$urow) { $fails[] = $nama . ': gagal simpan akun'; $bad++; continue; }
      $jk = mb_strtoupper(mb_substr(trim($rc['jk']), 0, 1));
      $prof = array('user_id' => (int)$urow['id'],
        'nis' => ($rc['nis'] !== '' ? $rc['nis'] : null),
        'nisn' => ($rc['nisn'] !== '' ? $rc['nisn'] : null),
        'gender' => ($jk === 'L' || $jk === 'P') ? $jk : null,
        'birth_date' => $this->dapodik_date($rc['lahir']));
      if ($this->students_has_extra()) {
        $prof['nik'] = $this->clean_nik($rc['nik']);
        $prof['nama_ibu'] = $this->clean_ibuname($rc['ibu']);
      }
      $this->db->insert('students', $prof);
      $srow = $this->db->select('id')->get_where('students', array('user_id' => (int)$urow['id']), 1)->row_array();
      if (!$srow) { $fails[] = $nama . ': gagal simpan profil'; $bad++; continue; }
      $kid = $this->match_class($rc['kelas'], $kelasCache);
      if ($kid) {
        $this->db->delete('class_students', array('student_id' => (int)$srow['id']));
        $this->db->insert('class_students', array('class_id' => $kid, 'student_id' => (int)$srow['id']));
        $kn = isset($perKelas[$kid]) ? $perKelas[$kid] : array('n' => 0, 'label' => '');
        $kn['n']++;
        if ($kn['label'] === '') {
          $kr = $this->db->select('name')->get_where('classes', array('id' => $kid), 1)->row_array();
          $kn['label'] = $kr ? $kr['name'] : ('#' . $kid);
        }
        $perKelas[$kid] = $kn;
      } else {
        $noClass++;
        if (count($noClassNames) < 15) $noClassNames[] = $nama . ' (' . ($rc['kelas'] !== '' ? $rc['kelas'] : 'tanpa kelas') . ')';
      }
      $n++;
    }
    audit('siswa_import_xlsx', 'Impor Dapodik: ' . $n . ' tambah, ' . $dup . ' duplikat, ' . $bad . ' gagal');
    $parts = array();
    foreach ($perKelas as $pk) $parts[] = $pk['label'] . ': ' . $pk['n'];
    $ok = 'Impor Dapodik selesai: ' . $n . ' siswa ditambahkan' . ($parts ? ' (' . implode(', ', $parts) . ')' : '')
      . ($dup ? ', ' . $dup . ' duplikat dilewati' : '') . ($bad ? ', ' . $bad . ' gagal' : '')
      . ($noClass ? ', ' . $noClass . ' tanpa kelas (tetapkan via Anggota Kelas)' : '')
      . '. Password awal: ' . $pw . '.';
    if ($fails) $ok .= ' Contoh gagal: ' . implode('; ', array_slice($fails, 0, 10)) . '.';
    if ($noClassNames) $ok .= ' Tanpa kelas cth: ' . implode('; ', $noClassNames) . '.';
  }

  // Kolom nik/nama_ibu ada bila migrasi 2026-10-18 sudah dijalankan.
  private function students_has_extra() {
    if ($this->_stu_extra === null) {
      $this->_stu_extra = $this->db->field_exists('nik', 'students') && $this->db->field_exists('nama_ibu', 'students');
    }
    return $this->_stu_extra;
  }

  private function clean_nik($s) {
    $d = preg_replace('/\D/', '', (string)$s);
    if ($d === '') return null;
    return substr($d, 0, 20);
  }

  private function clean_ibuname($s) {
    $t = preg_replace('/\s+/', ' ', trim((string)$s));
    if ($t === '') return null;
    return mb_substr($t, 0, 150);
  }

  private function dapodik_date($s) {
    $s = trim((string)$s);
    if ($s === '') return null;
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $s, $m)) return $m[1] . '-' . $m[2] . '-' . $m[3];
    if (preg_match('/^(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{4})/', $s, $m)) {
      if (checkdate((int)$m[2], (int)$m[1], (int)$m[3])) return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
    }
    if (preg_match('/^(\d{4})(\d{2})(\d{2})$/', preg_replace('/\D/', '', $s), $m)) {
      if (checkdate((int)$m[2], (int)$m[3], (int)$m[1])) return $m[1] . '-' . $m[2] . '-' . $m[3];
    }
    if (is_numeric($s) && (float)$s > 20000 && (float)$s < 60000) {
      return gmdate('Y-m-d', (int)round(((float)$s - 25569) * 86400));
    }
    $ts = strtotime($s);
    return $ts ? date('Y-m-d', $ts) : null;
  }

  private function match_class($raw, &$cache) {
    $raw = trim((string)$raw);
    if ($raw === '') return 0;
    $norm = mb_strtolower(preg_replace('/\s+/', '', $raw));
    $grade = 0; $suffix = '';
    if (preg_match('/(\d{1,2})/', $raw, $m)) { $grade = (int)$m[1]; $suffix = trim(str_ireplace(array('kelas', $m[1]), '', $raw)); }
    else {
      $rom = array('XII' => 12, 'XI' => 11, 'X' => 10, 'IX' => 9, 'VIII' => 8, 'VII' => 7, 'VI' => 6, 'V' => 5, 'IV' => 4, 'III' => 3, 'II' => 2, 'I' => 1);
      foreach ($rom as $k => $v) if (strpos(mb_strtoupper($norm), $k) !== false) { $grade = $v; break; }
    }
    if ($grade < 1 || $grade > 12) return 0;
    if (!isset($cache[$grade])) {
      $nid = $this->Data_model->class_save(null, 'Kelas ' . $grade, $grade, '');
      $cache[$grade] = array('ids' => array($nid), 'byname' => array('kelas' . $grade => $nid));
    }
    $g = $cache[$grade];
    if (count($g['ids']) === 1 && $suffix === '') return $g['ids'][0];
    if (isset($g['byname'][$norm])) return $g['byname'][$norm];
    $try = mb_strtolower(preg_replace('/\s+/', '', 'kelas' . $grade . $suffix));
    if (isset($g['byname'][$try])) return $g['byname'][$try];
    if (count($g['ids']) === 1) return $g['ids'][0];
    return 0; // beberapa paralel & tak cocok nama -> tetapkan manual
  }

  public function kelas() {
    $this->admin();
    if ($this->input->method() === 'post') {
      $act = $this->input->post('act'); $ok = ''; $err = '';
      if ($act === 'save') {
        $nm = trim($this->input->post('name')); $gr = (int)$this->input->post('grade_level');
        if ($nm === '' || $gr < 1 || $gr > 6) $err = 'Nama dan tingkat 1-6 wajib diisi.';
        else { $this->Data_model->class_save($this->input->post('id') ? (int)$this->input->post('id') : null, $nm, $gr, $this->input->post('homeroom')); audit('kelas_save', $nm); $ok = 'Kelas "' . $nm . '" disimpan.'; }
      } elseif ($act === 'del') { $this->Data_model->class_delete((int)$this->input->post('id')); $ok = 'Kelas dihapus.'; }
      elseif ($act === 'member') { $this->Data_model->class_set_members((int)$this->input->post('id'), $this->input->post('students')); audit('kelas_member', 'Kelas ' . $this->input->post('id')); $ok = 'Anggota kelas disimpan.'; }
      $this->done('admin/kelas', $ok, $err !== '' ? $err : 'Terjadi kesalahan, coba lagi.');
    }
    $rows = $this->Data_model->classes();
    foreach ($rows as &$r) $r['members'] = array_map(function ($m) { return (int)$m['student_id']; }, $this->Data_model->class_members($r['id']));
    $this->render('admin/kelas', array('title' => 'Data Kelas', 'menu' => 'kelas', 'rows' => $rows, 'teachers' => $this->Data_model->teachers(), 'allS' => $this->Data_model->students_all()));
  }

  public function mapel() {
    $this->admin();
    if ($this->input->method() === 'post') {
      $ok = ''; $err = '';
      if ($this->input->post('act') === 'save') {
        $nm = trim($this->input->post('name'));
        if ($nm === '') $err = 'Nama mapel wajib diisi.';
        elseif ($this->input->post('id')) { $this->db->where('id', (int)$this->input->post('id'))->update('subjects', array('name' => $nm, 'code' => ($this->input->post('code') !== '' ? $this->input->post('code') : null), 'is_active' => (int)(bool)$this->input->post('is_active'))); $ok = 'Mapel diperbarui.'; }
        else { $this->Data_model->subject_save($nm, trim($this->input->post('code'))); $ok = 'Mapel "' . $nm . '" ditambahkan.'; }
      } elseif ($this->input->post('act') === 'del') { $this->Data_model->subject_delete((int)$this->input->post('id')); $ok = 'Mapel dihapus.'; }
      $this->done('admin/mapel', $ok, $err !== '' ? $err : 'Terjadi kesalahan, coba lagi.');
    }
    $this->render('admin/mapel', array('title' => 'Mata Pelajaran', 'menu' => 'mapel', 'rows' => $this->Data_model->subjects()));
  }

  public function tahun() {
    $this->admin();
    if ($this->input->method() === 'post') {
      $ok = ''; $err = '';
      $act = $this->input->post('act');
      if ($act === 'save') {
        if (trim($this->input->post('name')) === '') $err = 'Nama tahun wajib diisi.';
        else { $this->Data_model->year_add(trim($this->input->post('name')), $this->input->post('semester')); $ok = 'Tahun ajaran ditambahkan.'; }
      }
      elseif ($act === 'active') { $this->Data_model->year_activate((int)$this->input->post('id')); $ok = 'Tahun aktif diubah.'; }
      elseif ($act === 'del') { $this->Data_model->year_delete((int)$this->input->post('id')); $ok = 'Tahun ajaran dihapus.'; }
      $this->done('admin/tahun', $ok, $err !== '' ? $err : 'Terjadi kesalahan, coba lagi.');
    }
    $this->render('admin/tahun', array('title' => 'Tahun Ajaran', 'menu' => 'tahun', 'rows' => $this->Data_model->years()));
  }

  public function pengguna() {
    $this->admin();
    if ($this->input->method() === 'post') {
      $act = $this->input->post('act'); $ok = '';
      if ($act === 'toggle') { $this->User_model->toggle((int)$this->input->post('id')); $ok = 'Status diubah.'; }
      elseif ($act === 'reset') { $this->User_model->reset_pw((int)$this->input->post('id'), 'Belajar1234'); $ok = 'Password direset menjadi Belajar1234.'; }
      elseif ($act === 'role') { $this->User_model->set_role((int)$this->input->post('id'), $this->input->post('role')); $ok = 'Peran diubah.'; }
      $this->done('admin/pengguna' . ($this->input->get('role') ? '?role=' . $this->input->get('role') : ''), $ok, 'Terjadi kesalahan, coba lagi.');
    }
    $f = $this->input->get('role');
    $this->render('admin/pengguna', array('title' => 'Pengguna & Hak Akses', 'menu' => 'pengguna', 'rows' => $this->User_model->list_by_role($f ?: null)));
  }

  public function materi() {
    $this->admin();
    if ($this->input->method() === 'post') {
      $act = $this->input->post('act'); $ok = ''; $err = '';
      if ($act === 'save') {
        $t = trim($this->input->post('title'));
        if ($t === '') $err = 'Judul materi wajib diisi.';
        else {
          $d = array('title' => $t, 'description' => $this->input->post('description') ?: null, 'content' => $this->input->post('content') ?: null, 'class_id' => ($this->input->post('class_id') !== '' ? (int)$this->input->post('class_id') : null), 'subject_id' => ($this->input->post('subject_id') !== '' ? (int)$this->input->post('subject_id') : null), 'topic' => $this->input->post('topic') ?: null, 'video_url' => $this->input->post('video_url') ?: null, 'status' => $this->input->post('status'));
          if ($this->input->post('id')) { $mid = (int)$this->input->post('id'); $this->db->where('id', $mid)->update('learning_materials', $d); $ok = 'Materi diperbarui.'; }
          else { $mid = $this->Materi_model->create($d); $ok = 'Materi "' . $t . '" disimpan.'; }
          $this->upload_materi_file('image', $mid, 'image_path');
          $this->upload_materi_file('pdf', $mid, 'pdf_path');
          audit('materi_add', $t);
          if ($this->input->post('class_id') !== '' && !$this->input->post('id')) notify_class((int)$this->input->post('class_id'), 'Materi baru: ' . $t, 'Ada materi baru untuk kelasmu.', 'materi');
        }
      } elseif ($act === 'del') { $this->Materi_model->delete((int)$this->input->post('id')); $ok = 'Materi dihapus.'; }
      elseif ($act === 'pub') { $this->Materi_model->toggle((int)$this->input->post('id')); $ok = 'Status publikasi diubah.'; }
      $this->done('admin/materi', $ok, $err !== '' ? $err : 'Terjadi kesalahan, coba lagi.');
    }
    $this->render('admin/materi', array('title' => 'Materi Pembelajaran', 'menu' => 'materi', 'rows' => $this->Materi_model->list_all(), 'kelas' => $this->Data_model->classes(), 'mapel' => $this->Data_model->subjects()));
  }
  private function upload_materi_file($field, $mid, $col) {
    if (empty($_FILES[$field]['tmp_name']) || !is_uploaded_file($_FILES[$field]['tmp_name'])) return;
    $allow = $field === 'image' ? array('image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp') : array('application/pdf' => 'pdf');
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES[$field]['tmp_name']);
    if (!isset($allow[$mime])) return;
    $dir = FCPATH . 'uploads/' . ($field === 'image' ? 'materi_img' : 'materi_pdf');
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $fn = ($field === 'image' ? 'materi_img/' : 'materi_pdf/') . bin2hex(random_bytes(12)) . '.' . $allow[$mime];
    if (move_uploaded_file($_FILES[$field]['tmp_name'], FCPATH . 'uploads/' . $fn)) $this->Materi_model->set_file($mid, $col, $fn);
  }

  public function tugas() {
    $this->admin();
    if ($this->input->method() === 'post') {
      $ok = ''; $err = '';
      if ($this->input->post('act') === 'save') {
        $t = trim($this->input->post('title'));
        if ($t === '' || $this->input->post('class_id') === '' || $this->input->post('subject_id') === '' || $this->input->post('teacher_id') === '') $err = 'Judul, kelas, mapel, dan guru wajib diisi.';
        else {
          $d = array('title' => $t, 'instructions' => $this->input->post('instructions'), 'class_id' => (int)$this->input->post('class_id'), 'subject_id' => (int)$this->input->post('subject_id'), 'teacher_id' => (int)$this->input->post('teacher_id'), 'start_at' => ($this->input->post('start_at') !== '' ? $this->input->post('start_at') : null), 'due_at' => ($this->input->post('due_at') !== '' ? $this->input->post('due_at') : null), 'max_score' => (int)$this->input->post('max_score'), 'max_photos' => (int)$this->input->post('max_photos'), 'max_file_mb' => (int)$this->input->post('max_file_mb'), 'status' => $this->input->post('status'), 'allow_late' => (int)(bool)$this->input->post('allow_late'));
          $aid = $this->Tugas_model->save($this->input->post('id') ? (int)$this->input->post('id') : null, $d);
          if ($d['status'] === 'published' && !$this->input->post('id')) notify_class($d['class_id'], 'Tugas baru: ' . $t, 'Ada tugas baru, cek tenggatnya.', 'tugas');
          audit('tugas_save', $t); $ok = 'Tugas "' . $t . '" disimpan.';
        }
      } elseif ($this->input->post('act') === 'del') { $this->Tugas_model->delete((int)$this->input->post('id')); $ok = 'Tugas dihapus.'; }
      $this->done('admin/tugas', $ok, $err !== '' ? $err : 'Terjadi kesalahan, coba lagi.');
    }
    $this->render('admin/tugas', array('title' => 'Tugas & Ujian', 'menu' => 'tugas', 'rows' => $this->Tugas_model->list_all(), 'kelas' => $this->Data_model->classes(), 'mapel' => $this->Data_model->subjects(true), 'guru' => $this->Data_model->teachers()));
  }

  public function bank_soal() {
    $this->admin();
    $aid = (int)($this->input->get('assignment') ?: $this->input->post('assignment_id'));
    if ($this->input->method() === 'post' && $aid) {
      if ($this->input->post('act') === 'q_del') { $this->Tugas_model->question_delete($aid, (int)$this->input->post('id')); $this->done('admin/bank_soal?assignment=' . $aid, 'Soal dihapus.'); }
      $t = trim($this->input->post('qtext'));
      if ($t === '') $this->done('admin/bank_soal?assignment=' . $aid, '', 'Teks soal wajib diisi.');
      $this->Tugas_model->question_add($aid, $this->input->post('qtype'), $t, $this->input->post('points'), $this->input->post('answer_key'), (array)$this->input->post('opt_text'), $this->input->post('correct'));
      audit('soal_add', 'Tugas ' . $aid);
      $this->done('admin/bank_soal?assignment=' . $aid, 'Soal ditambahkan.');
    }
    $qs = array();
    if ($aid) { $qs = $this->Tugas_model->questions($aid); foreach ($qs as &$q) $q['opts'] = $this->Tugas_model->options($q['id']); }
    $this->render('admin/bank_soal', array('title' => 'Bank Soal', 'menu' => 'bank', 'aid' => $aid, 'asg' => $this->Tugas_model->list_all(), 'qs' => $qs));
  }
  public function hapus_soal($aid, $qid) {
    $this->admin();
    $this->Tugas_model->question_delete($aid, $qid);
    $this->session->set_flashdata('msg', 'Soal dihapus.');
    redirect('admin/bank_soal?assignment=' . (int)$aid);
  }

  public function pemeriksaan() {
    $this->admin();
    $f = $this->input->get('status') ?: 'menunggu';
    $w = $f === 'semua' ? "sb.status IN ('dikumpulkan','menunggu','dinilai','perbaikan','terlambat')" : ($f === 'dinilai' ? "sb.status='dinilai'" : "sb.status IN ('dikumpulkan','menunggu','terlambat')");
    $this->render('admin/pemeriksaan', array('title' => 'Pemeriksaan Jawaban', 'menu' => 'periksa', 'rows' => $this->Submisi_model->pending($w)));
  }

  public function periksa($id) {
    $this->admin();
    $sb = $this->Submisi_model->detail($id);
    if (!$sb) show_404();
    $data = array('title' => 'Detail Pemeriksaan', 'menu' => 'periksa', 'msg' => '', 'sb' => $sb,
      'photos' => $this->Submisi_model->photos($id), 'answers' => $this->Submisi_model->answers($id),
      'grade' => $this->Nilai_model->get($id) ?: array('score' => '', 'comment' => '', 'is_published' => 0), 'fb' => $this->Submisi_model->feedback($id));
    if ($this->input->method() === 'post') {
      if ($this->input->post('act') === 'nilai') {
        $score = (float)$this->input->post('score'); $pub = (int)(bool)$this->input->post('publish');
        $old = $this->Nilai_model->save($id, $score, $this->input->post('comment'), (int)$this->me()['id'], $pub);
        audit('nilai_simpan', 'Submission ' . $id . ': ' . ($old === null ? '-' : $old) . ' -> ' . $score);
        if ($pub) notify((int)$sb['siswa_uid'], 'Nilai dipublikasikan: ' . $sb['tugas'], 'Nilaimu: ' . $score . '. ' . $this->input->post('comment'), 'hasil');
        $data['msg'] = 'Nilai ' . $score . ' disimpan' . ($pub ? ' dan dipublikasikan ke siswa.' : ' sebagai draf.');
        $data['grade'] = $this->Nilai_model->get($id);
      } elseif ($this->input->post('act') === 'perbaikan') {
        $tid = $this->db->select('id')->limit(1)->get('teachers')->row()->id ?? null;
        if ($tid && trim($this->input->post('message')) !== '') $this->Submisi_model->feedback_add($id, $tid, trim($this->input->post('message')));
        $this->Submisi_model->set_status($id, array('status' => 'perbaikan'));
        notify((int)$sb['siswa_uid'], 'Perlu perbaikan: ' . $sb['tugas'], $this->input->post('message'), 'tugas');
        audit('minta_perbaikan', 'Submission ' . $id); $data['msg'] = 'Permintaan perbaikan dikirim ke siswa.';
      }
    }
    $this->render('admin/periksa', $data);
  }

  public function nilai() {
    $this->admin();
    $fk = $this->input->get('kelas');
    $this->render('admin/nilai', array('title' => 'Nilai Siswa', 'menu' => 'nilai', 'kelas' => $this->Data_model->classes(), 'fk' => $fk, 'rows' => $this->Nilai_model->list_filtered($fk ? 'c.id=' . (int)$fk : '')));
  }

  public function laporan() {
    $this->admin();
    $fk = $this->input->get('kelas'); $fm = $this->input->get('mapel');
    $w = array();
    if ($fk) $w[] = 'c.id=' . (int)$fk;
    if ($fm) $w[] = 's.id=' . (int)$fm;
    $this->render('admin/laporan', array('title' => 'Laporan Pembelajaran', 'menu' => 'laporan', 'kelas' => $this->Data_model->classes(), 'mapel' => $this->Data_model->subjects(), 'fk' => $fk, 'fm' => $fm, 'rekap' => $this->Nilai_model->rekap(implode(' AND ', $w))));
  }

  public function pengaturan() {
    $this->admin();
    $keys = array('school_name' => 'Nama sekolah', 'max_photo_mb' => 'Maks MB per foto', 'max_photos_per_task' => 'Maks foto per tugas', 'timezone' => 'Zona waktu');
    $data = array('title' => 'Pengaturan Aplikasi', 'menu' => 'pengaturan', 'msg' => '', 'keys' => $keys, 'cur' => array());
    foreach ($keys as $k => $l) $data['cur'][$k] = app_setting($k, '');
    if ($this->input->method() === 'post') {
      foreach ($keys as $k => $l) $this->Data_model->setting_set($k, trim($this->input->post($k)));
      audit('pengaturan', 'Pengaturan disimpan'); $data['msg'] = 'Pengaturan disimpan.';
      foreach ($keys as $k => $l) $data['cur'][$k] = app_setting($k, '');
    }
    $this->render('admin/pengaturan', $data);
  }

  public function backup() {
    $this->admin();
    $data = array('title' => 'Backup / Pemulihan', 'menu' => 'backup', 'msg' => '', 'err' => '');
    if ($this->input->get('do') === 'download') {
      $tables = $this->db->query('SHOW TABLES')->result_array();
      $tkey = 'Tables_in_' . $this->db->database;
      $out = "-- Backup Belajar Pintar SD " . date('Y-m-d H:i:s') . "\nSET FOREIGN_KEY_CHECKS=0;\n";
      foreach ($tables as $t) {
        $tn = $t[$tkey];
        $cr = $this->db->query('SHOW CREATE TABLE `' . $tn . '`')->row_array();
        $out .= "\nDROP TABLE IF EXISTS `$tn`;\n" . $cr['Create Table'] . ";\n";
        foreach ($this->db->get($tn)->result_array() as $r) {
          $vals = array_map(function ($v) { return $v === null ? 'NULL' : $this->db->escape($v); }, array_values($r));
          $out .= 'INSERT INTO `' . $tn . '` VALUES (' . implode(',', $vals) . ");\n";
        }
      }
      $out .= "\nSET FOREIGN_KEY_CHECKS=1;\n";
      audit('backup', 'Unduh backup');
      $this->output->set_header('Content-Type: application/sql')->set_header('Content-Disposition: attachment; filename="backup-' . date('Ymd-His') . '.sql"')->set_output($out);
      return;
    }
    if (!empty($_FILES['sql']['tmp_name'])) {
      try { $this->db->query('SET FOREIGN_KEY_CHECKS=0'); $this->db->query(file_get_contents($_FILES['sql']['tmp_name'])); $this->db->query('SET FOREIGN_KEY_CHECKS=1'); audit('restore', 'Restore dari file'); $data['msg'] = 'Pemulihan selesai.'; }
      catch (Exception $e) { $data['err'] = 'Pemulihan gagal: ' . esc($e->getMessage()); }
    }
    $this->render('admin/backup', $data);
  }

  public function notifikasi() {
    $this->admin();
    if ($this->input->get('read')) $this->Notif_model->mark($this->input->get('read'), (int)$this->me()['id']);
    if ($this->input->get('all')) $this->Notif_model->mark_all((int)$this->me()['id']);
    $this->render('notif_list', array('title' => 'Notifikasi', 'menu' => '', 'rows' => $this->Notif_model->for_user((int)$this->me()['id'])));
  }
}
