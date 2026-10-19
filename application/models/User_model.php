<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
class User_model extends CI_Model {
  public function by_username($u) { return $this->db->get_where('users', array('username' => $u), 1)->row_array(); }
  public function list_by_role($role = null) {
    if ($role) $this->db->where('role', $role);
    return $this->db->order_by('role')->order_by('full_name')->get('users')->result_array();
  }
  public function create($username, $password, $role, $full_name) {
    $this->db->insert('users', array('username' => $username, 'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'role' => $role, 'full_name' => $full_name));
    return (int)$this->db->insert_id();
  }
  public function toggle($id) { $this->db->set('is_active', '1-is_active', false)->where('id', (int)$id)->update('users'); }
  public function reset_pw($id, $plain) { $this->db->where('id', (int)$id)->update('users', array('password_hash' => password_hash($plain, PASSWORD_DEFAULT))); }
  public function set_role($id, $role) { $this->db->where('id', (int)$id)->update('users', array('role' => $role)); }
  public function update_profile($id, $full, $email) { $this->db->where('id', (int)$id)->update('users', array('full_name' => $full, 'email' => ($email !== '' ? $email : null))); }
  public function set_password($id, $plain) { $this->reset_pw($id, $plain); }
  public function count_all() { return (int)$this->db->count_all('users'); }
  public function delete($id) { $this->db->delete('users', array('id' => (int)$id)); }
}
