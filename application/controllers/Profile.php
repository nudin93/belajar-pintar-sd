<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
class Profile extends MY_Controller {
  public function __construct() { parent::__construct(); $this->load->model('User_model'); }
  public function index() {
    $this->require_login();
    $data = array('title' => 'Profil Saya', 'menu' => '', 'msg' => '');
    $data['row'] = $this->db->get_where('users', array('id' => (int)$this->me()['id']), 1)->row_array();
    if ($this->input->method() === 'post') {
      $fn = trim($this->input->post('full_name'));
      if ($fn === '') $data['msg'] = 'Nama tidak boleh kosong.';
      else {
        $this->User_model->update_profile((int)$this->me()['id'], $fn, trim($this->input->post('email')));
        $u = $this->me(); $u['full_name'] = $fn; $this->session->set_userdata('user', $u);
        audit('profil_update', 'Profil diperbarui'); $data['msg'] = 'Profil disimpan.';
        $data['row'] = $this->db->get_where('users', array('id' => (int)$this->me()['id']), 1)->row_array();
      }
    }
    $this->render('profile/index', $data);
  }
  public function password() {
    $this->require_login();
    $data = array('title' => 'Ganti Password', 'menu' => '', 'msg' => '', 'err' => '');
    if ($this->input->method() === 'post') {
      $row = $this->db->select('password_hash')->get_where('users', array('id' => (int)$this->me()['id']), 1)->row_array();
      $old = (string)$this->input->post('old'); $n1 = (string)$this->input->post('n1'); $n2 = (string)$this->input->post('n2');
      if (!password_verify($old, $row['password_hash'])) $data['err'] = 'Password lama salah.';
      elseif (strlen($n1) < 6) $data['err'] = 'Password baru minimal 6 karakter.';
      elseif ($n1 !== $n2) $data['err'] = 'Konfirmasi password tidak sama.';
      else { $this->User_model->set_password((int)$this->me()['id'], $n1); audit('ganti_password', 'Ganti password'); $data['msg'] = 'Password berhasil diganti.'; }
    }
    $this->render('profile/password', $data);
  }
}
