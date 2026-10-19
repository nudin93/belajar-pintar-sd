<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
class Materi_model extends CI_Model {
  public function list_all($limit = 200) {
    return $this->db->select('m.*, c.name AS kelas, s.name AS mapel')->from('learning_materials m')->join('classes c', 'c.id=m.class_id', 'left')->join('subjects s', 's.id=m.subject_id', 'left')->order_by('m.id', 'DESC')->limit($limit)->get()->result_array();
  }
  public function list_for_class($class_id) {
    return $this->db->select('m.*, s.name AS mapel')->from('learning_materials m')->join('subjects s', 's.id=m.subject_id', 'left')->where('m.status', 'published')->group_start()->where('m.class_id', (int)$class_id)->or_where('m.class_id IS NULL', null, false)->group_end()->order_by('m.id', 'DESC')->get()->result_array();
  }
  public function list_by_teacher($tid) {
    return $this->db->select('m.*, c.name AS kelas, s.name AS mapel')->from('learning_materials m')->join('classes c', 'c.id=m.class_id', 'left')->join('subjects s', 's.id=m.subject_id', 'left')->where('m.teacher_id', (int)$tid)->order_by('m.id', 'DESC')->limit(200)->get()->result_array();
  }
  public function get_published($id) { return $this->db->get_where('learning_materials', array('id' => (int)$id, 'status' => 'published'), 1)->row_array(); }
  public function create($d) { $this->db->insert('learning_materials', $d); return (int)$this->db->insert_id(); }
  public function set_file($id, $col, $path) { $this->db->where('id', (int)$id)->update('learning_materials', array($col => $path)); }
  public function toggle($id) { $this->db->query("UPDATE learning_materials SET status = IF(status='published','draft','published') WHERE id = ?", array((int)$id)); }
  public function delete($id) { $this->db->delete('learning_materials', array('id' => (int)$id)); }
}
