<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
class Auth extends CI_Controller {
  public function __construct() { parent::__construct(); $this->load->model('User_model'); }
  public function index() {
    $u = $this->session->userdata('user');
    if ($u) redirect($u['role'] === 'admin' ? 'admin/dashboard' : ($u['role'] === 'guru' ? 'guru/dashboard' : 'siswa/dashboard'));
    $data = array('err' => '');
    if ($this->input->method() === 'post') {
      $this->form_validation->set_rules('username', 'Username', 'required');
      $this->form_validation->set_rules('password', 'Password', 'required');
      if ($this->form_validation->run()) {
        $row = $this->User_model->by_username(trim($this->input->post('username')));
        if ($row && (int)$row['is_active'] === 1 && password_verify((string)$this->input->post('password'), $row['password_hash'])) {
          $this->session->sess_regenerate(true);
          $this->session->set_userdata('user', array('id' => (int)$row['id'], 'username' => $row['username'], 'role' => $row['role'], 'full_name' => $row['full_name']));
          audit('login', 'Login: ' . $row['username']);
          redirect($row['role'] === 'admin' ? 'admin/dashboard' : ($row['role'] === 'guru' ? 'guru/dashboard' : 'siswa/dashboard'));
        }
        $data['err'] = 'Username atau password salah, atau akun nonaktif.';
      } else $data['err'] = 'Lengkapi username dan password.';
    }
    $this->load->view('auth/login', $data);
  }
  public function logout() {
    $u = $this->session->userdata('user');
    audit('logout', 'Logout: ' . ($u ? $u['username'] : '-'));
    $this->session->sess_destroy();
    redirect('auth');
  }
}
