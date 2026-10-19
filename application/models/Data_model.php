<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
// Sekolah, tahun ajaran, mapel, kelas, pengaturan.
class Data_model extends CI_Model {
  public function school() { return $this->db->order_by('id', 'DESC')->limit(1)->get('schools')->row_array(); }
  public function save_school($d) {
    $old = $this->school();
    if ($old) $this->db->where('id', $old['id'])->update('schools', $d);
    else $this->db->insert('schools', $d);
  }
  public function years() { return $this->db->order_by('id', 'DESC')->get('academic_years')->result_array(); }
  public function year_add($name, $sem) { $this->db->insert('academic_years', array('name' => $name, 'semester' => $sem)); }
  public function year_activate($id) { $this->db->update('academic_years', array('is_active' => 0)); $this->db->where('id', (int)$id)->update('academic_years', array('is_active' => 1)); }
  public function year_delete($id) { $this->db->delete('academic_years', array('id' => (int)$id)); }
  public function subjects($only_active = false) {
    if ($only_active) $this->db->where('is_active', 1);
    return $this->db->order_by('name')->get('subjects')->result_array();
  }
  public function subject_save($name, $code) { $this->db->insert('subjects', array('name' => $name, 'code' => ($code !== '' ? $code : null))); }
  public function subject_delete($id) { $this->db->delete('subjects', array('id' => (int)$id)); }
  public function classes() { return $this->db->order_by('grade_level')->order_by('name')->get('classes')->result_array(); }
  public function class_get($id) { return $this->db->get_where('classes', array('id' => (int)$id))->row_array(); }
  public function class_save($id, $name, $grade, $homeroom) {
    $d = array('name' => $name, 'grade_level' => (int)$grade, 'homeroom_teacher_id' => ($homeroom !== '' ? (int)$homeroom : null));
    if ($id) $this->db->where('id', (int)$id)->update('classes', $d);
    else $this->db->insert('classes', $d);
    return $id ? (int)$id : (int)$this->db->insert_id();
  }
  public function class_delete($id) { $this->db->delete('classes', array('id' => (int)$id)); }
  public function class_members($class_id) {
    return $this->db->select('student_id')->get_where('class_students', array('class_id' => (int)$class_id))->result_array();
  }
  public function class_set_members($class_id, $student_ids) {
    $this->db->delete('class_students', array('class_id' => (int)$class_id));
    $seen = array(); // hapus-dulu-lalu-isi + dedupe di PHP (pengganti CI4 ignore())
    foreach ((array)$student_ids as $sid) {
      $sid = (int)$sid;
      if ($sid <= 0 || isset($seen[$sid])) continue;
      $seen[$sid] = true;
      $this->db->insert('class_students', array('class_id' => (int)$class_id, 'student_id' => $sid));
    }
  }
  public function teachers() {
    return $this->db->select('t.id, u.full_name')->from('teachers t')->join('users u', 'u.id=t.user_id')->order_by('u.full_name')->get()->result_array();
  }
  public function students_all() {
    return $this->db->select('s.id, u.full_name')->from('students s')->join('users u', 'u.id=s.user_id')->order_by('u.full_name')->get()->result_array();
  }
  public function guru_list() {
    return $this->db->select('u.*, t.nip, t.phone')->from('users u')->join('teachers t', 't.user_id=u.id', 'left')->where('u.role', 'guru')->order_by('u.full_name')->get()->result_array();
  }
  public function siswa_list() {
    // nik/nama_ibu hanya dipilih bila migrasi 2026-10-18 sudah dijalankan (tetap jalan di DB lama).
    $sel = 'u.*, s.nis, s.nisn, c.name AS kelas';
    if ($this->db->field_exists('nik', 'students')) $sel .= ', s.nik';
    if ($this->db->field_exists('nama_ibu', 'students')) $sel .= ', s.nama_ibu';
    $rows = $this->db->select($sel)->from('users u')->join('students s', 's.user_id=u.id', 'left')->join('class_students cs', 'cs.student_id=s.id', 'left')->join('classes c', 'c.id=cs.class_id', 'left')->where('u.role', 'siswa')->order_by('u.full_name')->get()->result_array();
    foreach ($rows as &$r) {
      if (!isset($r['nik'])) $r['nik'] = null;
      if (!isset($r['nama_ibu'])) $r['nama_ibu'] = null;
      if (!isset($r['nisn'])) $r['nisn'] = null;
    }
    return $rows;
  }
  public function setting_set($k, $v) { $this->db->replace('application_settings', array('setting_key' => $k, 'setting_value' => $v)); }
}
