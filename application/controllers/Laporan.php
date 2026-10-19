<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
class Laporan extends MY_Controller {
  public function __construct() { parent::__construct(); $this->load->model('Nilai_model'); }
  private function scope_where() {
    $u = $this->me();
    if ($u['role'] === 'guru') { $tid = $this->teacher_id(); return 'a.teacher_id=' . $tid; }
    if ($u['role'] === 'siswa') { $prof = student_profile((int)$u['id']); return 'sb.student_id=' . (int)$prof['id']; }
    return '';
  }
  private function filtered($extra = '') {
    $w = $this->scope_where();
    $fk = $this->input->get('kelas'); $fm = $this->input->get('mapel');
    if ($fk !== null && $fk !== '') $w .= ($w !== '' ? ' AND ' : '') . 'c.id=' . (int)$fk;
    if ($fm !== null && $fm !== '') $w .= ($w !== '' ? ' AND ' : '') . 's.id=' . (int)$fm;
    return $w;
  }
  public function excel() {
    $this->require_login();
    $w = $this->filtered();
    $sql = "SELECT u.full_name AS siswa, c.name AS kelas, s.name AS mapel, a.title AS tugas, sb.status, g.score, g.is_published, sb.submitted_at FROM student_submissions sb JOIN assignments a ON a.id=sb.assignment_id JOIN students st ON st.id=sb.student_id JOIN users u ON u.id=st.user_id JOIN classes c ON c.id=sb.class_id JOIN subjects s ON s.id=a.subject_id LEFT JOIN grades g ON g.submission_id=sb.id";
    if ($w !== '') $sql .= " WHERE " . $w;
    $rows = $this->db->query($sql . " ORDER BY c.name, u.full_name LIMIT 2000")->result_array();
    audit('export_excel', 'export');
    $this->output->set_header('Content-Type: application/vnd.ms-excel')->set_header('Content-Disposition: attachment; filename="laporan-nilai.xls"');
    $this->load->view('laporan/excel', array('rows' => $rows));
  }
  public function pdf() {
    $this->require_login();
    $w = $this->filtered();
    $sql = "SELECT u.full_name AS siswa, c.name AS kelas, s.name AS mapel, a.title AS tugas, sb.status, g.score FROM student_submissions sb JOIN assignments a ON a.id=sb.assignment_id JOIN students st ON st.id=sb.student_id JOIN users u ON u.id=st.user_id JOIN classes c ON c.id=sb.class_id JOIN subjects s ON s.id=a.subject_id LEFT JOIN grades g ON g.submission_id=sb.id";
    if ($w !== '') $sql .= " WHERE " . $w;
    $rows = $this->db->query($sql . " ORDER BY c.name, u.full_name LIMIT 2000")->result_array();
    audit('export_pdf', 'cetak laporan');
    $this->load->view('laporan/pdf', array('rows' => $rows, 'me' => $this->me()));
  }
}
