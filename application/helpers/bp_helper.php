<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
function esc($s) { return htmlspecialchars((string)($s === null ? '' : $s), ENT_QUOTES, 'UTF-8'); }
function fmt_tgl($dt, $withTime = true) {
  if (!$dt) return '-';
  $ts = strtotime($dt);
  if (!$ts) return '-';
  return $withTime ? date('d/m/Y H:i', $ts) : date('d/m/Y', $ts);
}
function status_label($st) {
  $map = array('belum'=>'Belum Dikerjakan','sedang'=>'Sedang Dikerjakan','dikumpulkan'=>'Sudah Dikumpulkan','menunggu'=>'Menunggu Pemeriksaan','dinilai'=>'Sudah Dinilai','perbaikan'=>'Perlu Diperbaiki','terlambat'=>'Terlambat','draft'=>'Draf','published'=>'Diterbitkan','closed'=>'Ditutup');
  $cls = array('belum'=>'secondary','sedang'=>'info','dikumpulkan'=>'primary','menunggu'=>'warning','dinilai'=>'success','perbaikan'=>'danger','terlambat'=>'danger','draft'=>'secondary','published'=>'success','closed'=>'dark');
  $t = isset($map[$st]) ? $map[$st] : $st;
  $c = isset($cls[$st]) ? $cls[$st] : 'secondary';
  return '<span class="badge badge-' . $c . '">' . esc($t) . '</span>';
}
function audit($action, $detail = '') {
  try {
    $ci = get_instance();
    $u = $ci->session->userdata('user');
    $ci->db->insert('audit_logs', array('user_id' => $u ? $u['id'] : null, 'action' => $action, 'detail' => (string)$detail, 'ip_address' => $ci->input->ip_address()));
  } catch (Exception $e) {}
}
function notify($user_id, $title, $message, $link = '') {
  try { get_instance()->db->insert('notifications', array('user_id' => (int)$user_id, 'title' => $title, 'message' => $message, 'link' => $link)); }
  catch (Exception $e) {}
}
function notify_class($class_id, $title, $message, $link = '', $exclude_user = null) {
  try {
    $ci = get_instance();
    $rows = $ci->db->select('s.user_id')->from('class_students cs')->join('students s', 's.id=cs.student_id')->where('cs.class_id', (int)$class_id)->get()->result_array();
    foreach ($rows as $r) {
      if ($exclude_user && (int)$r['user_id'] === (int)$exclude_user) continue;
      notify((int)$r['user_id'], $title, $message, $link);
    }
  } catch (Exception $e) {}
}
function app_setting($key, $def = '') {
  try {
    $row = get_instance()->db->select('setting_value')->get_where('application_settings', array('setting_key' => $key))->row_array();
    return $row ? $row['setting_value'] : $def;
  } catch (Exception $e) { return $def; }
}
function upload_error_msg($code) {
  $m = array(1=>'File melebihi batas server.', 2=>'File melebihi batas form.', 3=>'File terunggah sebagian, coba lagi.', 4=>'Tidak ada file.', 6=>'Folder tmp hilang.', 7=>'Gagal menulis.', 8=>'Ekstensi PHP menghentikan unggahan.');
  return isset($m[$code]) ? $m[$code] : 'Upload gagal.';
}
// Validasi foto jawaban via MIME asli + getimagesize + tolak polyglot PHP.
function validate_photo($tmp, $maxMb) {
  $allowed = array('image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp');
  if (!is_uploaded_file($tmp)) return array('ok' => false, 'error' => 'File tidak valid.');
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($tmp);
  if (!isset($allowed[$mime])) return array('ok' => false, 'error' => 'Format ditolak. Gunakan JPG, PNG, atau WebP.');
  if (@getimagesize($tmp) === false) return array('ok' => false, 'error' => 'File bukan gambar asli.');
  if (filesize($tmp) > $maxMb * 1024 * 1024) return array('ok' => false, 'error' => 'Ukuran melebihi ' . (int)$maxMb . ' MB.');
  $head = @file_get_contents($tmp, false, null, 0, 256);
  if ($head !== false && preg_match('/<\?(php|=)/i', $head)) return array('ok' => false, 'error' => 'File mencurigakan ditolak.');
  return array('ok' => true, 'ext' => $allowed[$mime], 'mime' => $mime);
}
function teacher_class_ids($teacher_user_id) {
  $ci = get_instance();
  $t = $ci->db->select('id')->get_where('teachers', array('user_id' => (int)$teacher_user_id))->row_array();
  if (!$t) return array();
  $out = array();
  foreach ($ci->db->select('DISTINCT(class_id) AS c')->get_where('class_subjects', array('teacher_id' => (int)$t['id']))->result_array() as $r) $out[] = (int)$r['c'];
  // Wali kelas: semua murid di kelas yang diwalikannya ikut masuk daftar guru.
  foreach ($ci->db->select('id')->get_where('classes', array('homeroom_teacher_id' => (int)$t['id']))->result_array() as $r) $out[] = (int)$r['id'];
  return array_values(array_unique($out));
}
function student_profile($user_id) {
  return get_instance()->db->select('s.*, cs.class_id, c.name AS class_name')->from('students s')->join('class_students cs', 'cs.student_id=s.id', 'left')->join('classes c', 'c.id=cs.class_id', 'left')->where('s.user_id', (int)$user_id)->limit(1)->get()->row_array();
}
