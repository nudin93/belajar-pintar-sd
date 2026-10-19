<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Kelas Live</h1><p class="text-muted small mb-0">Tatap muka video dengan murid. Guru masuk dulu, murid dapat notifikasi otomatis.</p></div></div><section class="content"><div class="container-fluid">
<?php foreach ($kelas as $k): ?><?php $s = isset($live[$k['id']]) ? $live[$k['id']] : null; ?>
<div class="card"><div class="card-body">
<b><?php echo esc($k['name']); ?></b>
<?php if (is_array($s) && !empty($s['room'])): ?> <span class="badge badge-danger"><i class="fas fa-circle mr-1"></i>LIVE</span>
<p class="small text-muted mb-2"><?php echo esc(isset($s['topic']) && $s['topic'] !== '' ? $s['topic'] : 'Tanpa topik'); ?> &middot; dibuka <?php echo esc(isset($s['at']) ? $s['at'] : ''); ?></p>
<a class="btn btn-sm btn-success" href="<?php echo site_url('guru/live_room/' . $k['id']); ?>"><i class="fas fa-video mr-1"></i>Masuk</a>
<?php echo form_open('guru/live', array('style' => 'display:inline')); ?><input type="hidden" name="act" value="stop"><input type="hidden" name="class_id" value="<?php echo (int)$k['id']; ?>"><button class="btn btn-sm btn-secondary" onclick="return confirm('Tutup kelas live ini?')">Tutup</button><?php echo form_close(); ?>
<?php else: ?> <span class="badge badge-secondary">Tidak live</span>
<?php echo form_open('guru/live', array('class' => 'form-inline mt-2')); ?><input type="hidden" name="act" value="start"><input type="hidden" name="class_id" value="<?php echo (int)$k['id']; ?>">
<input name="topic" class="form-control form-control-sm mr-2 mb-1" placeholder="Topik (mis. Pecahan)">
<button class="btn btn-sm btn-danger mb-1"><i class="fas fa-broadcast-tower mr-1"></i>Mulai Live</button><?php echo form_close(); ?>
<?php endif; ?>
</div></div><?php endforeach; ?>
<?php if (!$kelas): ?><div class="alert alert-warning">Belum ada kelas yang Anda ampu. Hubungi admin.</div><?php endif; ?>
</div></section></div>
