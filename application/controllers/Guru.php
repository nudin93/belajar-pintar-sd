<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
class Guru extends MY_Controller {
  public function __construct() {
    parent::__construct();
    $this->load->model(array('Data_model', 'Materi_model', 'Tugas_model', 'Submisi_model', 'Nilai_model', 'Notif_model'));
  }
  private function guru() { $this->require_role('guru'); }
  // Kelas milik guru: dari pemetaan mapel + wali kelas (lihat teacher_class_ids()).
  // Kosong = belum dipetakan (fallback semua kelas).
  private function own_classes() {
    $ids = teacher_class_ids((int)$this->me()['id']);
    $ids = array_values(array_unique(array_map('intval', $ids)));
    if (!$ids) return array('all' => true, 'rows' => $this->Data_model->classes(), 'ids' => array());
    $rows = $this->db->where_in('id', $ids)->order_by('grade_level')->order_by('name')->get('classes')->result_array();
    return array('all' => false, 'rows' => $rows, 'ids' => $ids);
  }
  private function assert_own_class($own, $cid) {
    if ($cid && !$own['all'] && !in_array((int)$cid, $own['ids'], true)) show_error('Kelas ini bukan kelas yang Anda ampu.', 403);
  }
  // Simpan lampiran foto tugas guru. $m = 'soal' | 'contoh' (input soal_photos[] / contoh_photos[]).
  // Kembalikan array(jumlah_tersimpan, catatan).
  private function simpan_lampiran($aid, $m, $maxMb) {
    $M = $this->Tugas_model;
    $listM = $m . '_photos'; $addM = $m . '_photo_add'; $delM = $m . '_photo_delete'; $okM = $m . '_table_ok';
    $field = $m . '_photos'; $keepField = 'keep_' . $m;
    $savedN = 0; $note = '';
    $files = isset($_FILES[$field]) ? $_FILES[$field] : null;
    $adaFile = false;
    if ($files && isset($files['tmp_name']) && is_array($files['tmp_name'])) foreach ($files['tmp_name'] as $tn) if ($tn) { $adaFile = true; break; }
    if (!$M->$okM()) {
      if ($adaFile) $note = 'Foto ' . $m . ' tidak tersimpan (fitur belum aktif).';
      return array(0, $note);
    }
    $keep = (array)$this->input->post($keepField);
    foreach ($M->$listM($aid) as $sp) {
      if (!in_array((string)$sp['id'], $keep, true)) $M->$delM($aid, $sp['id']);
    }
    $nExist = count($M->$listM($aid));
    $maxLamp = 10; $maxMb = max(1, (int)$maxMb);
    if (!$adaFile) return array(0, '');
    $new = 0; foreach ($files['tmp_name'] as $tn) if ($tn) $new++;
    if ($nExist + $new > $maxLamp) return array(0, 'Maksimal ' . $maxLamp . ' foto ' . $m . ' per tugas.');
    $dir = FCPATH . 'uploads/assignments/' . $aid;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $ord = $nExist;
    for ($i = 0; $i < count($files['tmp_name']); $i++) {
      $tmp = $files['tmp_name'][$i];
      if (!$tmp) continue;
      if ($files['error'][$i] !== UPLOAD_ERR_OK) { if ($files['error'][$i] !== UPLOAD_ERR_NO_FILE) $note = upload_error_msg((int)$files['error'][$i]); continue; }
      $v = validate_photo($tmp, $maxMb);
      if (!$v['ok']) { $note = $v['error']; continue; }
      $fn = 'assignments/' . $aid . '/' . bin2hex(random_bytes(12)) . '.' . $v['ext'];
      if (move_uploaded_file($tmp, FCPATH . 'uploads/' . $fn)) {
        $M->$addM($aid, $fn, $files['name'][$i], $v['mime'], filesize(FCPATH . 'uploads/' . $fn), $ord++);
        $savedN++;
      } else $note = 'Gagal menyimpan file ke server (folder uploads tidak bisa ditulis?).';
    }
    return array($savedN, $note);
  }
  private function done($url, $ok, $err = '') {
    $this->session->set_flashdata($ok !== '' ? 'msg' : 'err', $ok !== '' ? $ok : $err);
    redirect($url);
  }

  public function index() { redirect('guru/dashboard'); }

  public function dashboard() {
    $this->guru();
    $tid = $this->teacher_id();
    $cls = teacher_class_ids((int)$this->me()['id']);
    $in = $cls ? implode(',', array_map('intval', $cls)) : '0';
    $c = function ($q) { return (int)$this->db->query($q)->row()->n; };
    $data = array('title' => 'Dashboard Guru', 'menu' => 'dashboard', 'hasClass' => !empty($cls),
      'nSiswa' => $c("SELECT COUNT(DISTINCT student_id) n FROM class_students WHERE class_id IN ($in)"),
      'nTugas' => $c("SELECT COUNT(*) n FROM assignments WHERE teacher_id=$tid AND status='published'"),
      'nMenunggu' => $c("SELECT COUNT(*) n FROM student_submissions sb JOIN assignments a ON a.id=sb.assignment_id WHERE a.teacher_id=$tid AND sb.status IN ('dikumpulkan','menunggu')"),
      'nBelum' => $c("SELECT COUNT(*) n FROM student_submissions sb JOIN assignments a ON a.id=sb.assignment_id WHERE a.teacher_id=$tid AND sb.status IN ('belum','sedang','perbaikan')"),
      'nDinilai' => $c("SELECT COUNT(*) n FROM student_submissions sb JOIN assignments a ON a.id=sb.assignment_id WHERE a.teacher_id=$tid AND sb.status='dinilai'"),
      'recent' => $this->db->query("SELECT sb.submitted_at, u.full_name, a.title FROM student_submissions sb JOIN assignments a ON a.id=sb.assignment_id JOIN students st ON st.id=sb.student_id JOIN users u ON u.id=st.user_id WHERE a.teacher_id=$tid AND sb.submitted_at IS NOT NULL ORDER BY sb.submitted_at DESC LIMIT 8")->result_array(),
      'prog' => $this->db->query("SELECT c.name, SUM(sb.status='dinilai') d, COUNT(*) t FROM student_submissions sb JOIN classes c ON c.id=sb.class_id JOIN assignments a ON a.id=sb.assignment_id WHERE a.teacher_id=$tid GROUP BY c.id")->result_array());
    $this->render('guru/dashboard', $data);
  }

  public function kelas() {
    $this->guru();
    $tid = $this->teacher_id();
    $cls = teacher_class_ids((int)$this->me()['id']);
    $data = array('title' => 'Kelas yang Diajar', 'menu' => 'kelas', 'rows' => array(), 'own' => $this->db->query("SELECT DISTINCT c.* FROM classes c JOIN assignments a ON a.class_id=c.id WHERE a.teacher_id=$tid ORDER BY c.name")->result_array(),
      'wali' => array_column($this->db->select('id')->get_where('classes', array('homeroom_teacher_id' => $tid))->result_array(), 'id'));
    if ($cls) $data['rows'] = $this->db->query('SELECT c.*, COUNT(cs.student_id) n FROM classes c LEFT JOIN class_students cs ON cs.class_id=c.id WHERE c.id IN (' . implode(',', array_map('intval', $cls)) . ') GROUP BY c.id')->result_array();
    $this->render('guru/kelas', $data);
  }

  public function siswa() {
    $this->guru();
    $cls = teacher_class_ids((int)$this->me()['id']);
    $fk = (int)$this->input->get('kelas');
    if ($fk && $cls && !in_array($fk, $cls, true)) show_error('Bukan kelas Anda.', 403);
    $where = $fk ? ' AND c.id=' . $fk : ($cls ? ' AND c.id IN (' . implode(',', array_map('intval', $cls)) . ')' : ' AND 1=0');
    $data = array('title' => 'Daftar Siswa', 'menu' => 'siswa', 'fk' => $fk,
      'kelas' => ($cls ? $this->db->where_in('id', $cls)->order_by('grade_level')->order_by('name')->get('classes')->result_array() : array()),
      'rows' => $this->db->query("SELECT u.full_name, s.nis, s.nisn, c.name AS kelas FROM students s JOIN users u ON u.id=s.user_id LEFT JOIN class_students cs ON cs.student_id=s.id LEFT JOIN classes c ON c.id=cs.class_id WHERE 1=1 $where ORDER BY c.name, u.full_name LIMIT 500")->result_array());
    $this->render('guru/siswa', $data);
  }

  public function materi() {
    $this->guru();
    $tid = $this->teacher_id();
    if ($this->input->method() === 'post') {
      $ok = ''; $err = '';
      $t = trim($this->input->post('title'));
      if ($t === '') $err = 'Judul materi wajib diisi.';
      else {
        $own = $this->own_classes();
        $cid = ($this->input->post('class_id') !== '' ? (int)$this->input->post('class_id') : 0);
        $this->assert_own_class($own, $cid);
        $d = array('title' => $t, 'description' => $this->input->post('description') ?: null, 'content' => $this->input->post('content') ?: null, 'class_id' => ($cid ? $cid : null), 'subject_id' => ($this->input->post('subject_id') !== '' ? (int)$this->input->post('subject_id') : null), 'topic' => $this->input->post('topic') ?: null, 'video_url' => $this->input->post('video_url') ?: null, 'status' => $this->input->post('status'), 'teacher_id' => $tid);
        if ($this->input->post('id')) { $this->db->where(array('id' => (int)$this->input->post('id'), 'teacher_id' => $tid))->update('learning_materials', $d); $ok = 'Materi diperbarui.'; }
        else {
          $this->Materi_model->create($d);
          if ($this->input->post('class_id') !== '') notify_class((int)$this->input->post('class_id'), 'Materi baru: ' . $t, 'Ada materi baru untuk kelasmu.', 'materi');
          audit('materi_add_guru', $t); $ok = 'Materi "' . $t . '" disimpan.';
        }
      }
      $this->done('guru/materi', $ok, $err !== '' ? $err : 'Terjadi kesalahan, coba lagi.');
    }
    $own = $this->own_classes();
    $this->render('guru/materi', array('title' => 'Materi Pembelajaran', 'menu' => 'materi', 'msg' => '', 'kelas' => $own['rows'], 'kelas_all' => $own['all'], 'mapel' => $this->Data_model->subjects(true), 'rows' => $this->Materi_model->list_by_teacher($tid)));
  }
  public function hapus_materi($id) {
    $this->guru();
    $this->db->delete('learning_materials', array('id' => (int)$id, 'teacher_id' => $this->teacher_id()));
    $this->session->set_flashdata('msg', 'Materi dihapus.');
    redirect('guru/materi');
  }

  public function tugas() {
    $this->guru();
    $this->render('guru/tugas', array('title' => 'Daftar Tugas', 'menu' => 'tugas', 'rows' => $this->Tugas_model->list_by_teacher($this->teacher_id()), 'kelas' => $this->Data_model->classes(), 'mapel' => $this->Data_model->subjects(true)));
  }

  public function tugas_form($id = null) {
    $this->guru();
    $tid = $this->teacher_id();
    if ($this->input->method() === 'post') {
      $ok = ''; $err = '';
      $t = trim($this->input->post('title'));
      $pid = (int)$this->input->post('id');
      if ($pid && !$this->db->get_where('assignments', array('id' => $pid, 'teacher_id' => $tid), 1)->row_array()) show_error('Bukan tugas Anda.', 403);
      if ($t === '' || $this->input->post('class_id') === '' || $this->input->post('subject_id') === '') $err = 'Judul, kelas, dan mapel wajib diisi.';
      else {
        $this->assert_own_class($this->own_classes(), (int)$this->input->post('class_id'));
        $d = array('title' => $t, 'instructions' => $this->input->post('instructions'), 'class_id' => (int)$this->input->post('class_id'), 'subject_id' => (int)$this->input->post('subject_id'), 'teacher_id' => $tid, 'start_at' => ($this->input->post('start_at') !== '' ? $this->input->post('start_at') : null), 'due_at' => ($this->input->post('due_at') !== '' ? $this->input->post('due_at') : null), 'max_score' => (int)$this->input->post('max_score'), 'max_photos' => (int)$this->input->post('max_photos'), 'max_file_mb' => (int)$this->input->post('max_file_mb'), 'status' => $this->input->post('status'), 'allow_late' => (int)(bool)$this->input->post('allow_late'));
        $aid = $this->Tugas_model->save($pid ?: null, $d);
        // Foto soal + foto contoh mengerjakan (maks 10 tiap jenis), langsung dilihat siswa.
        list($savedSoal, $noteSoal) = $this->simpan_lampiran($aid, 'soal', $d['max_file_mb']);
        list($savedContoh, $noteContoh) = $this->simpan_lampiran($aid, 'contoh', $d['max_file_mb']);
        $notes = array();
        if ($savedSoal > 0) $notes[] = $savedSoal . ' foto soal';
        if ($savedContoh > 0) $notes[] = $savedContoh . ' foto contoh';
        if ($noteSoal !== '') $notes[] = $noteSoal;
        if ($noteContoh !== '') $notes[] = $noteContoh;
        if ($d['status'] === 'published' && !$pid) notify_class($d['class_id'], 'Tugas baru: ' . $t, 'Ada tugas baru, cek tenggatnya.', 'tugas');
        audit('tugas_guru', $t);
        $ok = 'Tugas "' . $t . '" disimpan.' . ($notes ? ' (' . implode('. ', $notes) . '.)' : '');
      }
      $this->done('guru/tugas', $ok, $err !== '' ? $err : 'Terjadi kesalahan, coba lagi.');
    }
    $edit = array('id' => '', 'title' => '', 'instructions' => '', 'class_id' => '', 'subject_id' => '', 'start_at' => '', 'due_at' => '', 'max_score' => 100, 'max_photos' => 5, 'max_file_mb' => 5, 'status' => 'draft', 'allow_late' => 0);
    if ($id) {
      $row = $this->db->get_where('assignments', array('id' => (int)$id, 'teacher_id' => $tid), 1)->row_array();
      if (!$row) show_error('Bukan tugas Anda.', 403);
      $edit = $row;
    }
    $own = $this->own_classes();
    $this->render('guru/tugas_form', array('title' => 'Buat Tugas', 'menu' => 'tugas_new', 'edit' => $edit, 'kelas' => $own['rows'], 'kelas_all' => $own['all'], 'mapel' => $this->Data_model->subjects(true), 'soal_photos' => ($id ? $this->Tugas_model->soal_photos($id) : array()), 'contoh_photos' => ($id ? $this->Tugas_model->contoh_photos($id) : array())));
  }
  public function hapus_tugas($id) {
    $this->guru();
    $this->db->delete('assignments', array('id' => (int)$id, 'teacher_id' => $this->teacher_id()));
    $this->session->set_flashdata('msg', 'Tugas dihapus.');
    redirect('guru/tugas');
  }
  // Duplikat: soal baru untuk murid tanpa mengganggu tugas lama yang sudah dikerjakan.
  public function duplikat_tugas($id) {
    $this->guru();
    $new = $this->Tugas_model->duplicate((int)$id, $this->teacher_id());
    if (!$new) show_error('Bukan tugas Anda.', 403);
    audit('tugas_duplikat', 'Tugas ' . (int)$id . ' -> ' . $new);
    $this->session->set_flashdata('msg', 'Tersalin sebagai draf "(Lanjutan)". Rapikan soalnya lalu terbitkan.');
    redirect('guru/tugas_form/' . $new);
  }

  public function bank_soal() {
    $this->guru();
    $tid = $this->teacher_id();
    $aid = (int)($this->input->get('assignment') ?: $this->input->post('assignment_id'));
    if ($aid && !$this->db->get_where('assignments', array('id' => $aid, 'teacher_id' => $tid), 1)->row_array()) show_error('Bukan tugas Anda.', 403);
    if ($this->input->method() === 'post' && $aid) {
      if ($this->input->post('act') === 'q_del') { $this->Tugas_model->question_delete($aid, (int)$this->input->post('id')); $this->done('guru/bank_soal?assignment=' . $aid, 'Soal dihapus.'); }
      if ($this->input->post('act') === 'sec_add') {
        $st = trim($this->input->post('sectitle'));
        if ($st === '') $this->done('guru/bank_soal?assignment=' . $aid, '', 'Nama bagian wajib diisi.');
        if (!$this->Tugas_model->section_add($aid, $st)) $this->done('guru/bank_soal?assignment=' . $aid, '', 'Fitur bagian belum aktif.');
        audit('bagian_soal', 'Tugas ' . $aid . ': ' . $st);
        $this->done('guru/bank_soal?assignment=' . $aid, 'Bagian "' . $st . '" ditambahkan. Soal baru berikutnya bisa dimasukkan ke bagian ini.');
      }
      if ($this->input->post('act') === 'sec_del') { $this->Tugas_model->section_delete($aid, (int)$this->input->post('id')); $this->done('guru/bank_soal?assignment=' . $aid, 'Bagian dihapus beserta soalnya.'); }
      if ($this->input->post('act') === 'copy_new') {
        $new = $this->Tugas_model->copy_questions_to_new($aid, $tid, (array)$this->input->post('qids'));
        if (!$new) $this->done('guru/bank_soal?assignment=' . $aid, '', 'Centang dulu soal yang akan disalin ke tugas baru.');
        audit('soal_salin', 'Tugas ' . $aid . ' -> ' . $new);
        $this->done('guru/bank_soal?assignment=' . $new, 'Soal terpilih disalin ke tugas baru (draf).');
      }
      $t = trim($this->input->post('qtext'));
      if ($t === '') $this->done('guru/bank_soal?assignment=' . $aid, '', 'Teks soal wajib diisi.');
      $this->Tugas_model->question_add($aid, $this->input->post('qtype'), $t, $this->input->post('points'), $this->input->post('answer_key'), (array)$this->input->post('opt_text'), $this->input->post('correct'), (int)$this->input->post('section_id'));
      audit('soal_guru', 'Tugas ' . $aid);
      $this->done('guru/bank_soal?assignment=' . $aid, 'Soal ditambahkan.');
    }
    if ($this->input->get('del')) $this->Tugas_model->question_delete($aid, (int)$this->input->get('del'));
    $groups = array();
    if ($aid) {
      $groups = $this->Tugas_model->questions_grouped($aid, true);
      foreach ($groups as &$g) foreach ($g['qs'] as &$qq) $qq['opts'] = in_array($qq['question_type'], array('pg', 'bs'), true) ? $this->Tugas_model->options($qq['id']) : array();
    }
    $this->render('guru/bank_soal', array('title' => 'Bank Soal', 'menu' => 'bank', 'aid' => $aid, 'asg' => $this->Tugas_model->list_by_teacher($tid), 'secs' => ($aid ? $this->Tugas_model->sections($aid) : array()), 'groups' => $groups, 'worked' => ($aid ? (int)$this->db->query("SELECT COUNT(*) n FROM student_submissions WHERE assignment_id=? AND status NOT IN ('belum','sedang')", array($aid))->row()->n : 0)));
  }

  public function jawaban() {
    $this->guru();
    $tid = $this->teacher_id();
    $fa = (int)$this->input->get('assignment');
    $w = 'a.teacher_id=' . $tid;
    if ($fa) $w .= ' AND a.id=' . $fa;
    if ($this->input->get('filter') === 'menunggu') $w .= " AND sb.status IN ('dikumpulkan','menunggu','terlambat')";
    $this->render('guru/jawaban', array('title' => 'Jawaban Masuk', 'menu' => 'jawaban', 'rows' => $this->Submisi_model->pending($w)));
  }

  public function periksa($id) {
    $this->guru();
    $tid = $this->teacher_id();
    $sb = $this->Submisi_model->detail($id);
    if (!$sb) show_404();
    if ((int)$sb['teacher_id'] !== $tid) show_error('Bukan tugas kelas Anda.', 403);
    $this->guard_teacher_class((int)$sb['class_id']);
    $data = array('title' => 'Pemeriksaan Foto Buku', 'menu' => 'jawaban', 'msg' => '', 'sb' => $sb,
      'photos' => $this->Submisi_model->photos($id), 'soal_photos' => $this->Tugas_model->soal_photos((int)$sb['assignment_id']), 'contoh_photos' => $this->Tugas_model->contoh_photos((int)$sb['assignment_id']), 'answers' => $this->Submisi_model->answers($id), 'grade' => $this->Nilai_model->get($id) ?: array('score' => '', 'comment' => '', 'is_published' => 0));
    if ($this->input->method() === 'post') {
      if ($this->input->post('act') === 'nilai') {
        $score = (float)$this->input->post('score'); $pub = (int)(bool)$this->input->post('publish');
        $this->Nilai_model->save($id, $score, $this->input->post('comment'), (int)$this->me()['id'], $pub);
        audit('nilai_guru', 'Submission ' . $id . ' -> ' . $score);
        if ($pub) notify((int)$sb['siswa_uid'], 'Nilai dipublikasikan: ' . $sb['tugas'], 'Nilaimu: ' . $score . '. ' . $this->input->post('comment'), 'hasil');
        $data['msg'] = 'Nilai ' . $score . ($pub ? ' dipublikasikan ke siswa.' : ' disimpan sebagai draf.');
        $data['grade'] = $this->Nilai_model->get($id);
      } elseif ($this->input->post('act') === 'perbaikan') {
        $m = trim($this->input->post('message'));
        if ($m !== '') $this->Submisi_model->feedback_add($id, $tid, $m);
        $this->Submisi_model->set_status($id, array('status' => 'perbaikan'));
        notify((int)$sb['siswa_uid'], 'Perlu perbaikan: ' . $sb['tugas'], $m, 'tugas');
        audit('perbaikan_guru', 'Submission ' . $id); $data['msg'] = 'Permintaan perbaikan dikirim ke siswa.';
      }
    }
    $this->render('guru/periksa', $data);
  }

  public function nilai() {
    $this->guru();
    $this->render('guru/nilai', array('title' => 'Nilai Siswa', 'menu' => 'nilai', 'rows' => $this->Nilai_model->list_filtered('a.teacher_id=' . $this->teacher_id())));
  }

  public function rekap() {
    $this->guru();
    $this->render('guru/rekap', array('title' => 'Rekap Nilai', 'menu' => 'rekap', 'rekap' => $this->Nilai_model->rekap('a.teacher_id=' . $this->teacher_id())));
  }

  // Kelas live (video tatap muka via Jitsi, tanpa tabel baru: status di application_settings).
  public function live() {
    $this->guru();
    if ($this->input->method() === 'post') {
      $act = $this->input->post('act');
      $cid = (int)$this->input->post('class_id');
      $this->assert_own_class($this->own_classes(), $cid);
      if ($act === 'start') {
        $host = strtolower(preg_replace('/[^a-z0-9]/i', '', (string)$this->input->server('HTTP_HOST')));
        $room = 'bpsd-' . substr($host, 0, 20) . '-c' . $cid . '-' . bin2hex(random_bytes(4));
        $this->Data_model->setting_set('live_class_' . $cid, json_encode(array('room' => $room, 'topic' => trim((string)$this->input->post('topic')), 'by' => $this->me()['full_name'], 'at' => date('Y-m-d H:i:s'))));
        notify_class($cid, 'Kelas live dimulai', 'Guru membuka kelas video. Buka menu Kelas Live untuk gabung.', 'live', (int)$this->me()['id']);
        audit('live_start', 'Kelas ' . $cid);
        redirect('guru/live_room/' . $cid);
      } elseif ($act === 'stop') {
        $this->db->delete('application_settings', array('setting_key' => 'live_class_' . $cid));
        audit('live_stop', 'Kelas ' . $cid);
        $this->done('guru/live', 'Kelas live ditutup.');
      }
    }
    $own = $this->own_classes();
    $live = array();
    foreach ($own['rows'] as $k) {
      $s = app_setting('live_class_' . $k['id'], '');
      $live[$k['id']] = $s !== '' ? json_decode($s, true) : null;
    }
    $this->render('guru/live', array('title' => 'Kelas Live', 'menu' => 'live', 'kelas' => $own['rows'], 'live' => $live));
  }

  public function live_room($cid) {
    $this->guru();
    $cid = (int)$cid;
    $this->assert_own_class($this->own_classes(), $cid);
    $s = app_setting('live_class_' . $cid, '');
    if ($s === '') redirect('guru/live');
    $info = json_decode($s, true);
    $cls = $this->db->get_where('classes', array('id' => $cid), 1)->row_array();
    $this->render('guru/live_room', array('title' => 'Kelas Live', 'menu' => 'live', 'cid' => $cid, 'room' => $info['room'], 'topic' => isset($info['topic']) ? $info['topic'] : '', 'kelas' => $cls ? $cls['name'] : ''));
  }

  public function notifikasi() {
    $this->guru();
    if ($this->input->get('read')) $this->Notif_model->mark($this->input->get('read'), (int)$this->me()['id']);
    if ($this->input->get('all')) $this->Notif_model->mark_all((int)$this->me()['id']);
    $this->render('notif_list', array('title' => 'Notifikasi', 'menu' => 'notif', 'rows' => $this->Notif_model->for_user((int)$this->me()['id'])));
  }
}
