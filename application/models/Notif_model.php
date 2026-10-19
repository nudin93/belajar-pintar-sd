<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
class Notif_model extends CI_Model {
  public function for_user($uid, $limit = 100) {
    return $this->db->where('user_id', (int)$uid)->order_by('id', 'DESC')->limit($limit)->get('notifications')->result_array();
  }
  public function unread_count($uid) { return (int)$this->db->where(array('user_id' => (int)$uid, 'is_read' => 0))->count_all_results('notifications'); }
  public function mark($id, $uid) { $this->db->where(array('id' => (int)$id, 'user_id' => (int)$uid))->update('notifications', array('is_read' => 1)); }
  public function mark_all($uid) { $this->db->where('user_id', (int)$uid)->update('notifications', array('is_read' => 1)); }
  public function recent_audit($limit = 10) {
    return $this->db->select('a.action, a.detail, a.created_at, u.full_name')->from('audit_logs a')->join('users u', 'u.id=a.user_id', 'left')->order_by('a.id', 'DESC')->limit($limit)->get()->result_array();
  }
}
