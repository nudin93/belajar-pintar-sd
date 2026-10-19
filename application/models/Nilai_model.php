<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
class Nilai_model extends CI_Model {
  public function get($sub_id) { return $this->db->get_where('grades', array('submission_id' => (int)$sub_id), 1)->row_array(); }
  public function save($sub_id, $score, $comment, $grader_id, $publish) {
    $old = $this->get($sub_id);
    if ($old) $this->db->query("UPDATE grades SET score=?, comment=?, graded_by=?, graded_at=NOW(), is_published=?, published_at=IF(?,NOW(),NULL), revision_count=revision_count+1 WHERE submission_id=?", array($score, $comment, $grader_id, (int)$publish, (int)$publish, (int)$sub_id));
    else $this->db->query("INSERT INTO grades (submission_id, score, comment, graded_by, graded_at, is_published, published_at) VALUES (?,?,?, ?,NOW(),?,IF(?,NOW(),NULL))", array((int)$sub_id, $score, $comment, $grader_id, (int)$publish, (int)$publish));
    $this->db->where('id', (int)$sub_id)->update('student_submissions', array('final_score' => $score, 'is_published' => (int)$publish, 'status' => 'dinilai'));
    return $old ? $old['score'] : null;
  }
  public function auto_create($sub_id, $score) {
    $this->db->query("INSERT INTO grades (submission_id, score, graded_by, graded_at, is_published) VALUES (?,?,NULL,NOW(),0) ON DUPLICATE KEY UPDATE score=VALUES(score)", array((int)$sub_id, $score));
  }
  public function list_filtered($where = '') {
    $sql = "SELECT g.score, g.comment, g.is_published, u.full_name AS siswa, c.name AS kelas, s.name AS mapel, a.title AS tugas FROM grades g JOIN student_submissions sb ON sb.id=g.submission_id JOIN assignments a ON a.id=sb.assignment_id JOIN students st ON st.id=sb.student_id JOIN users u ON u.id=st.user_id JOIN classes c ON c.id=sb.class_id JOIN subjects s ON s.id=a.subject_id";
    if ($where !== '') $sql .= " WHERE " . $where;
    return $this->db->query($sql . " ORDER BY c.name, u.full_name LIMIT 500")->result_array();
  }
  public function rekap($where = '') {
    $sql = "SELECT c.name AS kelas, s.name AS mapel, COUNT(sb.id) AS total, SUM(sb.status='dinilai') AS dinilai, ROUND(AVG(g.score),1) AS rata FROM student_submissions sb JOIN classes c ON c.id=sb.class_id JOIN assignments a ON a.id=sb.assignment_id JOIN subjects s ON s.id=a.subject_id LEFT JOIN grades g ON g.submission_id=sb.id";
    if ($where !== '') $sql .= " WHERE " . $where;
    return $this->db->query($sql . " GROUP BY c.id, s.id ORDER BY c.name, s.name LIMIT 200")->result_array();
  }
  public function latest_published($sid, $limit = 5) {
    return $this->db->query("SELECT g.score, a.title FROM grades g JOIN student_submissions sb ON sb.id=g.submission_id JOIN assignments a ON a.id=sb.assignment_id WHERE sb.student_id=? AND g.is_published=1 ORDER BY g.published_at DESC LIMIT " . (int)$limit, array((int)$sid))->result_array();
  }
  public function avg_published($sid) {
    return $this->db->query("SELECT ROUND(AVG(g.score),1) AS r FROM grades g JOIN student_submissions sb ON sb.id=g.submission_id WHERE sb.student_id=? AND g.is_published=1", array((int)$sid))->row_array();
  }
}
