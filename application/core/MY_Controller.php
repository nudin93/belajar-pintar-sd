<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
// Basis semua controller: auth + guard peran + render layout AdminLTE bersama.
class MY_Controller extends CI_Controller {
  protected $me = null;
  public function __construct() {
    parent::__construct();
  }
  protected function me() {
    if ($this->me === null) $this->me = $this->session->userdata('user');
    return $this->me;
  }
  protected function require_login() {
    if (!$this->me()) redirect('auth');
  }
  protected function require_role() {
    $this->require_login();
    $roles = func_get_args();
    if (!in_array($this->me()['role'], $roles, true)) show_error('Akses ditolak untuk peran Anda.', 403);
  }
  protected function teacher_id() {
    $row = $this->db->select('id')->get_where('teachers', array('user_id' => (int)$this->me()['id']))->row_array();
    return $row ? (int)$row['id'] : 0;
  }
  // Guru hanya boleh membuka kelas yang dia ampu; lewati bila guru belum dipetakan (tugas miliknya tetap boleh).
  protected function guard_teacher_class($class_id) {
    if ($this->me()['role'] === 'admin') return;
    $mapped = teacher_class_ids((int)$this->me()['id']);
    if ($mapped && !in_array((int)$class_id, $mapped, true)) show_error('Anda tidak mengajar kelas ini.', 403);
  }
  protected function render($content_view, $data = array()) {
    $data['me'] = $this->me();
    $data['title'] = isset($data['title']) ? $data['title'] : 'Dashboard';
    $data['menu'] = isset($data['menu']) ? $data['menu'] : '';
    $this->load->view('layouts/header', $data);
    $this->load->view('layouts/navbar', $data);
    $this->load->view('layouts/sidebar', $data);
    $this->load->view($content_view, $data);
    $this->load->view('layouts/footer', $data);
  }
}
