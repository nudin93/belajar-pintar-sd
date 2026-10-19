<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
class Api extends MY_Controller {
  public function notif_count() {
    $this->require_login();
    $this->load->model('Notif_model');
    $this->output->set_content_type('application/json')->set_output(json_encode(array('unread' => $this->Notif_model->unread_count((int)$this->me()['id']))));
  }
}
