<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
class Siswa extends MY_Controller {
  public function __construct() {
    parent::__construct();
    $this->load->model(array('Materi_model', 'Tugas_model', 'Submisi_model', 'Nilai_model', 'Notif_model'));
  }
  private function siswa() { $this->require_role('siswa'); }
  private function prof() { return student_profile((int)$this->me()['id']); }

  public function index() { redirect('siswa/dashboard'); }

  public function dashboard() {
    $this->siswa();
    $p = $this->prof(); $cid = (int)$p['class_id']; $sid = (int)$p['id'];
    $c = function ($q, $a = array()) { return (int)$this->db->query($q, $a)->row()->n; };
    $data = array('title' => 'Dashboard', 'menu' => 'dashboard', 'prof' => $p,
      'nBaru' => $cid ? $c("SELECT COUNT(*) n FROM assignments WHERE class_id=? AND status='published'", array($cid)) : 0,
      'nBelum' => $c("SELECT COUNT(*) n FROM student_submissions WHERE student_id=? AND status IN ('belum','sedang','perbaikan')", array($sid)),
      'nTunggu' => $c("SELECT COUNT(*) n FROM student_submissions WHERE student_id=? AND status IN ('dikumpulkan','menunggu')", array($sid)),
      'nDinilai' => $c("SELECT COUNT(*) n FROM student_submissions WHERE student_id=? AND status='dinilai'", array($sid)),
      'nilaiBaru' => $this->Nilai_model->latest_published($sid),
      'materi' => $cid ? $this->Materi_model->list_for_class($cid) : array());
    $data['materi'] = array_slice($data['materi'], 0, 6);
    $total = max(1, $data['nBelum'] + $data['nTunggu'] + $data['nDinilai']);
    $data['prog'] = round($data['nDinilai'] / $total * 100);
    $data['total'] = $total;
    $this->render('siswa/dashboard', $data);
  }

  public function materi() {
    $this->siswa();
    $p = $this->prof();
    $data = array('title' => 'Materi Pelajaran', 'menu' => 'materi', 'rows' => $this->Materi_model->list_for_class((int)$p['class_id']));
    $this->render('siswa/materi', $data);
  }

  public function materi_detail($id) {
    $this->siswa();
    $p = $this->prof();
    $m = $this->Materi_model->get_published($id);
    if (!$m) show_404();
    if ($m['class_id'] && (int)$m['class_id'] !== (int)$p['class_id']) show_error('Materi ini bukan untuk kelasmu.', 403);
    $m['mapel'] = $m['subject_id'] ? $this->db->select('name')->get_where('subjects', array('id' => $m['subject_id']))->row()->name : '';
    $this->render('siswa/materi_detail', array('title' => $m['title'], 'menu' => 'materi', 'm' => $m));
  }

  public function tugas() {
    $this->siswa();
    $p = $this->prof(); $sid = (int)$p['id']; $cid = (int)$p['class_id'];
    $ff = $this->input->get('filter');
    $sql = "SELECT a.*, s.name AS mapel, sb.status, sb.final_score, g.is_published FROM assignments a JOIN subjects s ON s.id=a.subject_id LEFT JOIN student_submissions sb ON sb.assignment_id=a.id AND sb.student_id=? LEFT JOIN grades g ON g.submission_id=sb.id WHERE a.class_id=? AND a.status='published'";
    if ($ff === 'belum') $sql .= " AND (sb.status IS NULL OR sb.status IN ('belum','sedang','perbaikan'))";
    $data = array('title' => 'Tugas Saya', 'menu' => 'tugas', 'rows' => $this->db->query($sql . " ORDER BY a.due_at ASC", array($sid, $cid))->result_array());
    $this->render('siswa/tugas', $data);
  }

  public function tugas_detail($id) {
    $this->siswa();
    $p = $this->prof(); $sid = (int)$p['id']; $cid = (int)$p['class_id'];
    $a = $this->db->query("SELECT a.*, s.name AS mapel, c.name AS kelas FROM assignments a JOIN subjects s ON s.id=a.subject_id JOIN classes c ON c.id=a.class_id WHERE a.id=? AND a.status='published'", array((int)$id))->row_array();
    if (!$a) show_404();
    if ((int)$a['class_id'] !== $cid) show_error('Tugas ini bukan untuk kelasmu.', 403);
    $sb = $this->Submisi_model->ensure((int)$id, $sid, $cid);
    if ($sb['status'] === 'belum') { $this->Submisi_model->set_status($sb['id'], array('status' => 'sedang')); $sb['status'] = 'sedang'; }
    $data = array('title' => $a['title'], 'menu' => 'tugas', 'msg' => '', 'err' => '', 'a' => $a, 'sb' => $sb,
      'qs' => $this->Tugas_model->questions($id), 'savedAns' => $this->Submisi_model->answer_map($sb['id']),
      'soal_photos' => $this->Tugas_model->soal_photos((int)$id),
      'contoh_photos' => $this->Tugas_model->contoh_photos((int)$id),
      'fb' => $this->Submisi_model->feedback($sb['id']), 'grade' => $this->Nilai_model->get($sb['id']));
    foreach ($data['qs'] as &$q) if ($q['question_type'] === 'pg') $q['opts'] = $this->Tugas_model->options($q['id']);
    $data['groups'] = $this->Tugas_model->questions_grouped((int)$id);
    foreach ($data['groups'] as &$g) foreach ($g['qs'] as &$qq) if ($qq['question_type'] === 'pg') $qq['opts'] = $this->Tugas_model->options($qq['id']);
    if ($this->input->method() === 'post') $this->kirim($data, $a, $sb);
    $data['photos'] = $this->Submisi_model->photos($sb['id']);
    $this->render('siswa/tugas_detail', $data);
  }

  private function kirim(&$data, $a, $sb) {
    // Kunci server-side: tugas yang sudah dinilai tidak bisa dikirim ulang.
    // Tombol baru muncul lagi bila guru memerintahkan kerjakan ulang (status perbaikan).
    $fresh = $this->Submisi_model->get($sb['id']);
    if ($fresh && $fresh['status'] === 'dinilai') { $data['err'] = 'Tugas ini sudah dinilai, jawaban dikunci dan tidak bisa dikirim ulang.'; return; }
    $maxPhotos = (int)$a['max_photos']; $maxMb = (int)$a['max_file_mb'];
    // hapus foto yang tidak dicentang
    $keep = (array)$this->input->post('keep_photo');
    foreach ($this->Submisi_model->photos($sb['id']) as $ph) {
      if (!in_array((string)$ph['id'], $keep, true)) $this->Submisi_model->photo_delete($ph['id']);
    }
    $nExisting = $this->Submisi_model->photo_count($sb['id']);
    $files = isset($_FILES['photos']) ? $_FILES['photos'] : null;
    $newCount = 0;
    if ($files && isset($files['tmp_name']) && is_array($files['tmp_name'])) foreach ($files['tmp_name'] as $t) if ($t) $newCount++;
    if ($nExisting + $newCount > $maxPhotos) { $data['err'] = 'Maksimal ' . $maxPhotos . ' foto per tugas.'; return; }
    $dir = FCPATH . 'uploads/submissions/' . (int)$sb['id'];
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $ord = $nExisting;
    if ($files) for ($i = 0; $i < count($files['tmp_name']); $i++) {
      $tmp = $files['tmp_name'][$i];
      if (!$tmp || $files['error'][$i] !== UPLOAD_ERR_OK) { if ($files['error'][$i] !== UPLOAD_ERR_NO_FILE) $data['err'] = upload_error_msg((int)$files['error'][$i]); continue; }
      $v = validate_photo($tmp, $maxMb);
      if (!$v['ok']) { $data['err'] = $v['error']; continue; }
      $fn = 'submissions/' . (int)$sb['id'] . '/' . bin2hex(random_bytes(12)) . '.' . $v['ext'];
      if (move_uploaded_file($tmp, FCPATH . 'uploads/' . $fn)) {
        $this->Submisi_model->photo_add($sb['id'], $fn, $files['name'][$i], $v['mime'], filesize(FCPATH . 'uploads/' . $fn), $ord++);
      }
    }
    // Wajib foto jawaban bila tugas tak punya soal jawaban-teks (pg/bs/isian),
    // agar yang masuk ke guru tidak kosong. Tugas PG murni tetap boleh tanpa foto.
    $needPhoto = true;
    foreach ((array)$data['qs'] as $q) {
      if (in_array($q['question_type'], array('pg', 'bs', 'isian'), true)) { $needPhoto = false; break; }
    }
    if ($needPhoto && $this->Submisi_model->photo_count($sb['id']) < 1) { $data['err'] = 'Wajib melampirkan minimal 1 foto jawaban (foto tulisan di bukumu) sebelum mengirim.'; return; }
    $res = $this->Tugas_model->grade_auto($sb['id'], $a['id'], $a['max_score'], (array)$this->input->post('ans'));
    $now = date('Y-m-d H:i:s');
    $late = ($a['due_at'] && $now > $a['due_at']) ? 1 : 0;
    if ($late && !(int)$a['allow_late']) { $data['err'] = 'Tenggat sudah lewat dan tugas ini tidak menerima keterlambatan.'; return; }
    if (!$res['manual'] && $res['total'] > 0) {
      $this->Submisi_model->set_status($sb['id'], array('status' => 'dinilai', 'submitted_at' => $now, 'updated_at' => $now, 'is_late' => $late, 'auto_score' => $res['auto'], 'final_score' => $res['auto'], 'is_published' => 0));
      $this->Nilai_model->auto_create($sb['id'], $res['auto']);
      $st = 'dinilai';
    } else {
      $st = $late ? 'terlambat' : 'menunggu';
      $this->Submisi_model->set_status($sb['id'], array('status' => $st, 'submitted_at' => $now, 'updated_at' => $now, 'is_late' => $late, 'auto_score' => $res['auto']));
    }
    $tu = $this->Submisi_model->teacher_of_assignment($a['id']);
    if ($tu) notify((int)$tu['user_id'], 'Jawaban masuk: ' . $a['title'], $this->me()['full_name'] . ' mengumpulkan tugas.', 'jawaban');
    audit('kirim_jawaban', 'Tugas ' . $a['id'] . ' oleh ' . $this->me()['username']);
    $data['msg'] = 'Jawaban terkirim! Status: ' . ($st === 'dinilai' ? 'Sudah dinilai otomatis (menunggu publikasi guru)' : ($late ? 'Terlambat — menunggu pemeriksaan' : 'Menunggu pemeriksaan')) . ($res['auto'] !== null ? ' Skor otomatis sementara: ' . $res['auto'] : '');
    $data['sb'] = $this->Submisi_model->get($sb['id']);
    $data['savedAns'] = $this->Submisi_model->answer_map($sb['id']);
  }

  public function hasil() {
    $this->siswa();
    $p = $this->prof();
    $rows = $this->db->query("SELECT a.title, a.max_score, s.name AS mapel, sb.submitted_at, sb.status, g.score, g.comment FROM student_submissions sb JOIN assignments a ON a.id=sb.assignment_id JOIN subjects s ON s.id=a.subject_id LEFT JOIN grades g ON g.submission_id=sb.id AND g.is_published=1 WHERE sb.student_id=? ORDER BY sb.submitted_at DESC", array((int)$p['id']))->result_array();
    $this->render('siswa/hasil', array('title' => 'Hasil & Nilai', 'menu' => 'hasil', 'rows' => $rows));
  }

  public function riwayat() {
    $this->siswa();
    $p = $this->prof();
    $rows = $this->db->query("SELECT sb.*, a.title, s.name AS mapel FROM student_submissions sb JOIN assignments a ON a.id=sb.assignment_id JOIN subjects s ON s.id=a.subject_id WHERE sb.student_id=? ORDER BY sb.submitted_at DESC LIMIT 100", array((int)$p['id']))->result_array();
    $this->render('siswa/riwayat', array('title' => 'Riwayat Belajar', 'menu' => 'riwayat', 'rows' => $rows));
  }

  public function prestasi() {
    $this->siswa();
    $p = $this->prof(); $sid = (int)$p['id'];
    $nDinilai = (int)$this->db->query("SELECT COUNT(*) n FROM student_submissions WHERE student_id=$sid AND status='dinilai'")->row()->n;
    $avg = $this->Nilai_model->avg_published($sid);
    $foto = (int)$this->db->query("SELECT COUNT(*) n FROM submission_photos p JOIN student_submissions sb ON sb.id=p.submission_id WHERE sb.student_id=$sid")->row()->n;
    $badges = array();
    if ($nDinilai >= 1) $badges[] = array('fa-star', 'Tugas Pertama', 'Menyelesaikan 1 tugas');
    if ($nDinilai >= 5) $badges[] = array('fa-medal', 'Rajin Belajar', 'Menyelesaikan 5 tugas');
    if ($nDinilai >= 10) $badges[] = array('fa-trophy', 'Bintang Kelas', 'Menyelesaikan 10 tugas');
    if ($avg['r'] !== null && (float)$avg['r'] >= 85) $badges[] = array('fa-crown', 'Nilai Hebat', 'Rata-rata di atas 85');
    if ($foto >= 10) $badges[] = array('fa-camera', 'Fotografer Cilik', 'Mengirim 10 foto jawaban');
    $this->render('siswa/prestasi', array('title' => 'Prestasi Saya', 'menu' => 'prestasi', 'nDinilai' => $nDinilai, 'rata' => $avg['r'], 'foto' => $foto, 'badges' => $badges));
  }

  // Kelas live: gabung ke sesi video yang dibuka guru kelasmu.
  public function live() {
    $this->siswa();
    $p = $this->prof(); $cid = (int)$p['class_id'];
    $info = $cid ? json_decode(app_setting('live_class_' . $cid, ''), true) : null;
    if (!is_array($info) || empty($info['room'])) $info = null;
    $this->render('siswa/live', array('title' => 'Kelas Live', 'menu' => 'live', 'info' => $info, 'kelas' => $p['class_name']));
  }

  public function notifikasi() {
    $this->siswa();
    if ($this->input->get('read')) $this->Notif_model->mark($this->input->get('read'), (int)$this->me()['id']);
    if ($this->input->get('all')) $this->Notif_model->mark_all((int)$this->me()['id']);
    $data = array('title' => 'Notifikasi', 'menu' => 'notif', 'rows' => $this->Notif_model->for_user((int)$this->me()['id']));
    $this->render('notif_list', $data);
  }
}
