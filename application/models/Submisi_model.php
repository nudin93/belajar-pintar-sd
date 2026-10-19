<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
class Submisi_model extends CI_Model {
  public function ensure($aid, $sid, $class_id) {
    $row = $this->db->get_where('student_submissions', array('assignment_id' => (int)$aid, 'student_id' => (int)$sid), 1)->row_array();
    if (!$row) {
      // Baris dipastikan belum ada oleh cek di atas; insert biasa (CI3 tak punya ignore()).
      $this->db->insert('student_submissions', array('assignment_id' => (int)$aid, 'student_id' => (int)$sid, 'class_id' => (int)$class_id, 'status' => 'sedang'));
      $row = $this->db->get_where('student_submissions', array('assignment_id' => (int)$aid, 'student_id' => (int)$sid), 1)->row_array();
    }
    return $row;
  }
  public function get($id) { return $this->db->get_where('student_submissions', array('id' => (int)$id), 1)->row_array(); }
  public function photos($sub_id) { return $this->db->order_by('sort_order')->order_by('id')->get_where('submission_photos', array('submission_id' => (int)$sub_id))->result_array(); }
  public function photo_count($sub_id) { return (int)$this->db->where('submission_id', (int)$sub_id)->count_all_results('submission_photos'); }
  public function photo_add($sub_id, $path, $orig, $mime, $size, $ord) {
    $this->db->insert('submission_photos', array('submission_id' => (int)$sub_id, 'file_path' => $path, 'original_name' => $orig, 'mime' => $mime, 'file_size' => (int)$size, 'sort_order' => (int)$ord));
  }
  public function photo_delete($id) {
    $p = $this->db->get_where('submission_photos', array('id' => (int)$id), 1)->row_array();
    if ($p) {
      @unlink(FCPATH . 'uploads/' . $p['file_path']);
      $this->db->delete('submission_photos', array('id' => (int)$id));
    }
  }
  public function answers($sub_id) {
    return $this->db->select('sa.*, q.question_text, q.question_type, q.points')->from('student_answers sa')->join('assignment_questions q', 'q.id=sa.question_id')->where('sa.submission_id', (int)$sub_id)->get()->result_array();
  }
  public function answer_map($sub_id) {
    $out = array();
    foreach ($this->db->get_where('student_answers', array('submission_id' => (int)$sub_id))->result_array() as $r) $out[$r['question_id']] = $r;
    return $out;
  }
  public function feedback($sub_id) {
    return $this->db->select('f.*, u.full_name')->from('teacher_feedback f')->join('teachers t', 't.id=f.teacher_id')->join('users u', 'u.id=t.user_id')->where('f.submission_id', (int)$sub_id)->order_by('f.id', 'DESC')->get()->result_array();
  }
  public function feedback_add($sub_id, $tid, $msg, $rev = 1) {
    $this->db->insert('teacher_feedback', array('submission_id' => (int)$sub_id, 'teacher_id' => (int)$tid, 'message' => $msg, 'requires_revision' => (int)$rev));
  }
  public function set_status($sub_id, $d) { $this->db->where('id', (int)$sub_id)->update('student_submissions', $d); }
  public function pending($where_extra = '', $limit = 300) {
    $sql = "SELECT sb.*, a.title AS tugas, a.max_score, c.name AS kelas, s.name AS mapel, u.full_name AS siswa, (SELECT COUNT(*) FROM submission_photos p WHERE p.submission_id=sb.id) AS foto FROM student_submissions sb JOIN assignments a ON a.id=sb.assignment_id JOIN classes c ON c.id=sb.class_id JOIN students st ON st.id=sb.student_id JOIN users u ON u.id=st.user_id JOIN subjects s ON s.id=a.subject_id WHERE " . $where_extra . " ORDER BY sb.submitted_at DESC LIMIT " . (int)$limit;
    return $this->db->query($sql)->result_array();
  }
  public function teacher_of_assignment($aid) {
    return $this->db->select('t.user_id')->from('teachers t')->join('assignments a', 'a.teacher_id=t.id')->where('a.id', (int)$aid)->limit(1)->get()->row_array();
  }
  public function detail($id) {
    $sql = "SELECT sb.*, a.title AS tugas, a.instructions, a.max_score, a.due_at, a.teacher_id, c.name AS kelas, s.name AS mapel, u.full_name AS siswa, u.id AS siswa_uid FROM student_submissions sb JOIN assignments a ON a.id=sb.assignment_id JOIN classes c ON c.id=sb.class_id JOIN students st ON st.id=sb.student_id JOIN users u ON u.id=st.user_id JOIN subjects s ON s.id=a.subject_id WHERE sb.id = ? LIMIT 1";
    return $this->db->query($sql, array((int)$id))->row_array();
  }
}
