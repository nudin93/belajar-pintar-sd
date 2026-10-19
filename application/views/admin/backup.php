<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Backup / Pemulihan</h1></div></div><section class="content"><div class="container-fluid"><?php if (!empty($msg)): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
<?php if (!empty($err)): ?><div class="alert alert-danger"><?php echo esc($err); ?></div><?php endif; ?>
<div class="card"><div class="card-body">
<a class="btn btn-primary mb-3" href="<?php echo site_url('admin/backup?do=download'); ?>"><i class="fas fa-download mr-1"></i>Unduh Backup (.sql)</a>
<?php echo form_open_multipart('admin/backup'); ?>
<div class="form-group"><label>Pulihkan dari file .sql</label><input type="file" name="sql" accept=".sql" class="form-control-file" required></div>
<button class="btn btn-warning" onclick="return confirm('Timpa database dengan file ini?')">Pulihkan</button><?php echo form_close(); ?>
<p class="text-muted small mt-2">Untuk hosting dengan batasan, gunakan juga backup bawaan cPanel + phpMyAdmin (Export).</p></div></div>
</div></section></div>