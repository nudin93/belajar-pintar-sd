<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
class Tugas_model extends CI_Model {
  public function list_all() {
    return $this->db->select('a.*, c.name AS kelas, s.name AS mapel, u.full_name AS guru')->from('assignments a')->join('classes c', 'c.id=a.class_id')->join('subjects s', 's.id=a.subject_id')->join('teachers t', 't.id=a.teacher_id')->join('users u', 'u.id=t.user_id')->order_by('a.id', 'DESC')->limit(200)->get()->result_array();
  }
  public function list_by_teacher($tid) {
    $sel = 'a.*, c.name AS kelas, s.name AS mapel';
    if ($this->soal_table_ok()) $sel .= ', (SELECT COUNT(*) FROM assignment_photos ap WHERE ap.assignment_id=a.id) AS n_soal';
    if ($this->contoh_table_ok()) $sel .= ', (SELECT COUNT(*) FROM assignment_contoh ac WHERE ac.assignment_id=a.id) AS n_contoh';
    return $this->db->select($sel)->from('assignments a')->join('classes c', 'c.id=a.class_id')->join('subjects s', 's.id=a.subject_id')->where('a.teacher_id', (int)$tid)->order_by('a.id', 'DESC')->get()->result_array();
  }
  public function get($id) { return $this->db->get_where('assignments', array('id' => (int)$id), 1)->row_array(); }
  public function save($id, $d) {
    if ($id) { $this->db->where('id', (int)$id)->update('assignments', $d); $aid = (int)$id; }
    else { $this->db->insert('assignments', $d); $aid = (int)$this->db->insert_id(); }
    // placeholder submission per siswa kelas agar statistik akurat (cek-dulu-lalu-isi; CI3 tak punya ignore())
    $sids = $this->db->select('student_id')->get_where('class_students', array('class_id' => (int)$d['class_id']))->result_array();
    foreach ($sids as $s) {
      $ex = $this->db->get_where('student_submissions', array('assignment_id' => $aid, 'student_id' => (int)$s['student_id']), 1)->row_array();
      if (!$ex) $this->db->insert('student_submissions', array('assignment_id' => $aid, 'student_id' => (int)$s['student_id'], 'class_id' => (int)$d['class_id'], 'status' => 'belum'));
    }
    return $aid;
  }
  public function delete($id) { $this->db->delete('assignments', array('id' => (int)$id)); }
  // Salin soal terpilih ke tugas baru (draf "(Lanjutan)"). Tugas lama tak diganggu.
  public function copy_questions_to_new($aid, $tid, $qids) {
    $a = $this->db->get_where('assignments', array('id' => (int)$aid, 'teacher_id' => (int)$tid), 1)->row_array();
    $qids = array_values(array_unique(array_map('intval', (array)$qids)));
    if (!$a || !$qids) return 0;
    $qs = $this->db->where_in('id', $qids)->get_where('assignment_questions', array('assignment_id' => (int)$aid))->result_array();
    if (!$qs) return 0;
    $new = $this->save(null, array(
      'title' => $a['title'] . ' (Lanjutan)', 'instructions' => $a['instructions'],
      'class_id' => $a['class_id'], 'subject_id' => $a['subject_id'], 'teacher_id' => (int)$tid,
      'academic_year_id' => $a['academic_year_id'], 'start_at' => null, 'due_at' => null,
      'max_score' => $a['max_score'], 'attachment_path' => $a['attachment_path'],
      'max_photos' => $a['max_photos'], 'max_file_mb' => $a['max_file_mb'],
      'status' => 'draft', 'allow_late' => $a['allow_late']));
    $secMap = array();
    $hasSec = $this->section_col_ok();
    if ($hasSec && $this->sections_table_ok()) {
      $need = array();
      foreach ($qs as $q) { $s = isset($q['section_id']) ? (int)$q['section_id'] : 0; if ($s) $need[$s] = 1; }
      foreach (array_keys($need) as $os) {
        $sec = $this->db->get_where('assignment_sections', array('id' => $os, 'assignment_id' => (int)$aid), 1)->row_array();
        if (!$sec) continue;
        $this->db->insert('assignment_sections', array('assignment_id' => $new, 'title' => $sec['title'], 'sort_order' => $sec['sort_order']));
        $secMap[$os] = (int)$this->db->insert_id();
      }
    }
    foreach ($qs as $q) {
      $qr = array('assignment_id' => $new, 'question_type' => $q['question_type'], 'question_text' => $q['question_text'], 'points' => $q['points'], 'answer_key' => $q['answer_key'], 'discussion' => $q['discussion'], 'sort_order' => $q['sort_order']);
      if ($hasSec) {
        $os = isset($q['section_id']) ? (int)$q['section_id'] : 0;
        $qr['section_id'] = ($os && isset($secMap[$os])) ? $secMap[$os] : null;
      }
      $this->db->insert('assignment_questions', $qr);
      $nq = (int)$this->db->insert_id();
      foreach ($this->options($q['id']) as $o) {
        $this->db->insert('question_options', array('question_id' => $nq, 'option_label' => $o['option_label'], 'option_text' => $o['option_text'], 'is_correct' => $o['is_correct']));
      }
    }
    return $new;
  }
  // Gandakan tugas jadi tugas baru (draf): soal+bagian+opsi+foto ikut tersalin,
  // submisi murid mulai baru dari nol. Tugas lama tidak diganggu.
  public function duplicate($aid, $tid) {
    $a = $this->db->get_where('assignments', array('id' => (int)$aid, 'teacher_id' => (int)$tid), 1)->row_array();
    if (!$a) return 0;
    $new = $this->save(null, array(
      'title' => $a['title'] . ' (Lanjutan)', 'instructions' => $a['instructions'],
      'class_id' => $a['class_id'], 'subject_id' => $a['subject_id'], 'teacher_id' => (int)$tid,
      'academic_year_id' => $a['academic_year_id'], 'start_at' => $a['start_at'], 'due_at' => $a['due_at'],
      'max_score' => $a['max_score'], 'attachment_path' => $a['attachment_path'],
      'max_photos' => $a['max_photos'], 'max_file_mb' => $a['max_file_mb'],
      'status' => 'draft', 'allow_late' => $a['allow_late']));
    $secMap = array();
    if ($this->sections_table_ok()) {
      foreach ($this->sections($aid) as $s) {
        $this->db->insert('assignment_sections', array('assignment_id' => $new, 'title' => $s['title'], 'sort_order' => $s['sort_order']));
        $secMap[(int)$s['id']] = (int)$this->db->insert_id();
      }
    }
    $hasSec = $this->section_col_ok();
    foreach ($this->questions($aid) as $q) {
      $qr = array('assignment_id' => $new, 'question_type' => $q['question_type'], 'question_text' => $q['question_text'], 'points' => $q['points'], 'answer_key' => $q['answer_key'], 'discussion' => $q['discussion'], 'sort_order' => $q['sort_order']);
      if ($hasSec) {
        $os = isset($q['section_id']) ? (int)$q['section_id'] : 0;
        $qr['section_id'] = ($os && isset($secMap[$os])) ? $secMap[$os] : null;
      }
      $this->db->insert('assignment_questions', $qr);
      $nq = (int)$this->db->insert_id();
      foreach ($this->options($q['id']) as $o) {
        $this->db->insert('question_options', array('question_id' => $nq, 'option_label' => $o['option_label'], 'option_text' => $o['option_text'], 'is_correct' => $o['is_correct']));
      }
    }
    foreach (array(array('soal_table_ok', 'soal_photos', 'soal_photo_add'), array('contoh_table_ok', 'contoh_photos', 'contoh_photo_add')) as $cfg) {
      list($okM, $listM, $addM) = $cfg;
      if (!$this->$okM()) continue;
      $dir = FCPATH . 'uploads/assignments/' . $new;
      foreach ($this->$listM($aid) as $i => $p) {
        $src = FCPATH . 'uploads/' . $p['file_path'];
        if (!is_file($src)) continue;
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
        if (!in_array($ext, array('jpg', 'jpeg', 'png', 'webp'), true)) continue;
        $fn = 'assignments/' . $new . '/' . bin2hex(random_bytes(8)) . '.' . $ext;
        if (@copy($src, FCPATH . 'uploads/' . $fn)) {
          $this->$addM($new, $fn, $p['original_name'], $p['mime'], filesize(FCPATH . 'uploads/' . $fn), $i);
        }
      }
    }
    return $new;
  }
  // Foto soal: lampiran guru pada tugas, dilihat langsung oleh siswa. Tahan DB lama.
  public function soal_table_ok() { return $this->db->table_exists('assignment_photos'); }
  public function soal_photos($aid) {
    if (!$this->soal_table_ok()) return array();
    return $this->db->order_by('sort_order')->order_by('id')->get_where('assignment_photos', array('assignment_id' => (int)$aid))->result_array();
  }
  public function soal_photo_add($aid, $path, $orig, $mime, $size, $ord) {
    $this->db->insert('assignment_photos', array('assignment_id' => (int)$aid, 'file_path' => $path, 'original_name' => $orig, 'mime' => $mime, 'file_size' => (int)$size, 'sort_order' => (int)$ord));
  }
  public function soal_photo_delete($aid, $id) {
    $p = $this->db->get_where('assignment_photos', array('id' => (int)$id, 'assignment_id' => (int)$aid), 1)->row_array();
    if ($p) {
      @unlink(FCPATH . 'uploads/' . $p['file_path']);
      $this->db->delete('assignment_photos', array('id' => (int)$id));
    }
  }
  // Foto contoh mengerjakan: contoh jawaban dari guru, dilihat langsung oleh siswa. Tahan DB lama.
  public function contoh_table_ok() { return $this->db->table_exists('assignment_contoh'); }
  public function contoh_photos($aid) {
    if (!$this->contoh_table_ok()) return array();
    return $this->db->order_by('sort_order')->order_by('id')->get_where('assignment_contoh', array('assignment_id' => (int)$aid))->result_array();
  }
  public function contoh_photo_add($aid, $path, $orig, $mime, $size, $ord) {
    $this->db->insert('assignment_contoh', array('assignment_id' => (int)$aid, 'file_path' => $path, 'original_name' => $orig, 'mime' => $mime, 'file_size' => (int)$size, 'sort_order' => (int)$ord));
  }
  public function contoh_photo_delete($aid, $id) {
    $p = $this->db->get_where('assignment_contoh', array('id' => (int)$id, 'assignment_id' => (int)$aid), 1)->row_array();
    if ($p) {
      @unlink(FCPATH . 'uploads/' . $p['file_path']);
      $this->db->delete('assignment_contoh', array('id' => (int)$id));
    }
  }
  public function questions($aid) { return $this->db->order_by('sort_order')->order_by('id')->get_where('assignment_questions', array('assignment_id' => (int)$aid))->result_array(); }
  public function options($qid) { return $this->db->order_by('option_label')->get_where('question_options', array('question_id' => (int)$qid))->result_array(); }
  public function question_add($aid, $type, $text, $points, $key, $opt_texts, $correct_idx, $sec = 0) {
    $row = array('assignment_id' => (int)$aid, 'question_type' => $type, 'question_text' => $text, 'points' => (int)$points, 'answer_key' => ($key !== '' ? $key : null));
    // Masukkan ke bagian bila valid (kolom + bagian milik tugas ini).
    if ((int)$sec > 0 && $this->db->field_exists('section_id', 'assignment_questions')) {
      $ok = $this->db->get_where('assignment_sections', array('id' => (int)$sec, 'assignment_id' => (int)$aid), 1)->row_array();
      $row['section_id'] = $ok ? (int)$sec : null;
    }
    $this->db->insert('assignment_questions', $row);
    $qid = (int)$this->db->insert_id();
    foreach ((array)$opt_texts as $i => $t) {
      $t = trim($t);
      if ($t === '') continue;
      $this->db->insert('question_options', array('question_id' => $qid, 'option_label' => chr(65 + (int)$i), 'option_text' => $t, 'is_correct' => (int)((string)$correct_idx === (string)$i)));
    }
    $first_opt = isset($opt_texts[0]) ? trim((string)$opt_texts[0]) : '';
    if ($type === 'bs' && $first_opt === '') {
      $this->db->insert('question_options', array('question_id' => $qid, 'option_label' => 'A', 'option_text' => 'Benar', 'is_correct' => (int)($key === 'Benar')));
      $this->db->insert('question_options', array('question_id' => $qid, 'option_label' => 'B', 'option_text' => 'Salah', 'is_correct' => (int)($key === 'Salah')));
    }
    return $qid;
  }
  public function question_delete($aid, $qid) { $this->db->delete('assignment_questions', array('id' => (int)$qid, 'assignment_id' => (int)$aid)); }
  // Bagian soal: tiap tambahan soal baru bisa masuk bagian terpisah. Tahan DB lama.
  public function sections_table_ok() { return $this->db->table_exists('assignment_sections'); }
  public function section_col_ok() { return $this->db->field_exists('section_id', 'assignment_questions'); }
  public function sections($aid) {
    if (!$this->sections_table_ok()) return array();
    return $this->db->order_by('sort_order')->order_by('id')->get_where('assignment_sections', array('assignment_id' => (int)$aid))->result_array();
  }
  public function section_add($aid, $title) {
    if (!$this->sections_table_ok()) return 0;
    $mx = $this->db->select_max('sort_order', 'm')->get_where('assignment_sections', array('assignment_id' => (int)$aid))->row()->m;
    $this->db->insert('assignment_sections', array('assignment_id' => (int)$aid, 'title' => $title, 'sort_order' => (int)$mx + 1));
    return (int)$this->db->insert_id();
  }
  public function section_delete($aid, $sid) {
    if (!$this->sections_table_ok()) return;
    $sec = $this->db->get_where('assignment_sections', array('id' => (int)$sid, 'assignment_id' => (int)$aid), 1)->row_array();
    if (!$sec) return;
    if ($this->section_col_ok()) $this->db->delete('assignment_questions', array('section_id' => (int)$sid, 'assignment_id' => (int)$aid));
    $this->db->delete('assignment_sections', array('id' => (int)$sid));
  }
  // Soal dikelompokkan per bagian. $keepEmpty=true untuk halaman kelola guru.
  public function questions_grouped($aid, $keepEmpty = false) {
    $qs = $this->questions($aid);
    if (!$this->sections_table_ok() || !$this->section_col_ok()) return array(array('section' => null, 'qs' => $qs));
    $groups = array(); $map = array();
    foreach ($this->sections($aid) as $s) { $groups[] = array('section' => $s, 'qs' => array()); $map[(int)$s['id']] = count($groups) - 1; }
    $groups[] = array('section' => null, 'qs' => array()); $def = count($groups) - 1;
    foreach ($qs as $q) {
      $sid = isset($q['section_id']) ? (int)$q['section_id'] : 0;
      if ($sid && isset($map[$sid])) $groups[$map[$sid]]['qs'][] = $q;
      else $groups[$def]['qs'][] = $q;
    }
    $out = array();
    foreach ($groups as $g) {
      if ($g['section'] === null) { if ($g['qs']) $out[] = $g; }
      elseif ($g['qs'] || $keepEmpty) $out[] = $g;
    }
    return $out ? $out : array(array('section' => null, 'qs' => array()));
  }
  // Penilaian otomatis PG/BS/isian. Kembalikan array(total, dapat, manual, skor_otomatis).
  public function grade_auto($submission_id, $aid, $max_score, $answers) {
    $qs = $this->questions($aid);
    $total = 0; $got = 0; $manual = false;
    foreach ($qs as $q) {
      $total += (int)$q['points'];
      $val = trim((string)(isset($answers[$q['id']]) ? $answers[$q['id']] : ''));
      if (in_array($q['question_type'], array('uraian', 'foto'), true)) {
        $manual = true;
        $this->db->delete('student_answers', array('submission_id' => (int)$submission_id, 'question_id' => (int)$q['id']));
        $this->db->insert('student_answers', array('submission_id' => (int)$submission_id, 'question_id' => (int)$q['id'], 'answer_text' => $val));
        continue;
      }
      $correct = false; $selOpt = null;
      if ($q['question_type'] === 'pg') {
        $opt = $this->db->get_where('question_options', array('id' => (int)$val, 'question_id' => (int)$q['id']), 1)->row_array();
        $selOpt = $opt ? $opt['id'] : null;
        $correct = $opt && (int)$opt['is_correct'] === 1;
      } elseif ($q['question_type'] === 'bs') {
        $key = mb_strtolower(trim((string)$q['answer_key']));
        $correct = mb_strtolower($val) === $key || ($val === '1' && $key === 'benar') || ($val === '0' && $key === 'salah');
      } else {
        $alts = array_map(function ($x) { return mb_strtolower(trim($x)); }, explode('|', (string)$q['answer_key']));
        $correct = in_array(mb_strtolower($val), $alts, true);
      }
      if ($correct) $got += (int)$q['points'];
      $this->db->delete('student_answers', array('submission_id' => (int)$submission_id, 'question_id' => (int)$q['id']));
      $this->db->insert('student_answers', array('submission_id' => (int)$submission_id, 'question_id' => (int)$q['id'], 'answer_text' => $val, 'selected_option_id' => $selOpt, 'is_correct' => $correct ? 1 : 0, 'score' => $correct ? (int)$q['points'] : 0));
    }
    return array('total' => $total, 'got' => $got, 'manual' => $manual, 'auto' => ($total > 0 ? round($got / $total * (int)$max_score, 2) : null));
  }
}
