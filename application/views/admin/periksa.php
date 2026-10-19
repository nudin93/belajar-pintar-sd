<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Periksa: <?php echo esc($sb["tugas"]); ?></h1></div></div><section class="content"><div class="container-fluid"><?php if (!empty($msg)): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
<?php if (!empty($err)): ?><div class="alert alert-danger"><?php echo esc($err); ?></div><?php endif; ?>
<div class="row"><div class="col-md-7">
<div class="card"><div class="card-header"><b>Foto Jawaban (<?php echo count($photos); ?>)</b> <small class="text-muted">klik untuk memperbesar</small></div><div class="card-body"><div class="row">
<?php foreach ($photos as $p): ?><div class="col-6 col-md-4 mb-2"><a href="<?php echo base_url('uploads/' . $p['file_path']); ?>" target="_blank"><img class="photo-thumb" loading="lazy" src="<?php echo base_url('uploads/' . $p['file_path']); ?>"></a></div><?php endforeach; ?>
<?php if (!$photos): ?><p class="text-muted">Tidak ada foto.</p><?php endif; ?>
</div></div></div>
<div class="card"><div class="card-header"><b>Jawaban Pilihan/Isian (otomatis)</b></div><div class="card-body">
<?php foreach ($answers as $a): ?><p class="small mb-1"><b>[<?php echo esc($a['question_type']); ?>]</b> <?php echo esc($a['question_text']); ?><br>Jawaban: <?php echo esc($a['answer_text']); ?> &middot; Skor: <?php echo esc($a['score']); ?></p><?php endforeach; ?>
<?php if (!$answers): ?><p class="text-muted small">Tugas foto buku / belum ada jawaban otomatis.</p><?php endif; ?></div></div>
</div><div class="col-md-5">
<div class="card"><div class="card-body">
<p><b><?php echo esc($sb['siswa']); ?></b> (<?php echo esc($sb['kelas']); ?>, <?php echo esc($sb['mapel']); ?>)<br>
<small class="text-muted">Dikumpulkan: <?php echo esc(fmt_tgl($sb['submitted_at'])); ?> &middot; Status: <?php echo status_label($sb['status']); ?></small></p>
<p class="small"><b>Instruksi:</b><br><?php echo nl2br(esc($sb['instructions'])); ?></p>
<?php echo form_open('admin/periksa/' . $sb['id']); ?><input type="hidden" name="act" value="nilai">
<div class="form-group"><label>Nilai (maks <?php echo (int)$sb['max_score']; ?>)</label><input name="score" type="number" step="0.01" min="0" max="<?php echo (int)$sb['max_score']; ?>" class="form-control" value="<?php echo esc($grade['score']); ?>" required></div>
<div class="form-group"><label>Komentar guru</label><textarea name="comment" class="form-control" rows="3"><?php echo esc($grade['comment']); ?></textarea></div>
<label><input type="checkbox" name="publish" value="1" <?php echo !empty($grade['is_published']) ? 'checked' : ''; ?>> Publikasikan hasil ke siswa</label><br><br>
<button class="btn btn-primary">Simpan Nilai</button><?php echo form_close(); ?>
<hr><?php echo form_open('admin/periksa/' . $sb['id']); ?><input type="hidden" name="act" value="perbaikan">
<div class="form-group"><label>Minta perbaikan</label><textarea name="message" class="form-control" rows="2" placeholder="Tulis yang harus diperbaiki"></textarea></div>
<button class="btn btn-warning">Kirim Permintaan Perbaikan</button><?php echo form_close(); ?>
</div></div>
<div class="card"><div class="card-header"><b>Riwayat Komentar</b></div><div class="card-body">
<?php foreach ($fb as $f): ?><p class="small"><b><?php echo esc($f['full_name']); ?></b> <span class="text-muted"><?php echo esc(fmt_tgl($f['created_at'])); ?></span><br><?php echo esc($f['message']); ?></p><?php endforeach; ?>
<?php if ($grade): ?><p class="small">Nilai: <b><?php echo esc($grade['score']); ?></b> <?php echo $grade['is_published'] ? '(dipublikasikan)' : '(belum dipublikasikan)'; ?></p><?php endif; ?>
</div></div>
</div></div>
</div></section></div>