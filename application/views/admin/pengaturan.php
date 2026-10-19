<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Pengaturan Aplikasi</h1></div></div><section class="content"><div class="container-fluid"><?php if (!empty($msg)): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
<?php if (!empty($err)): ?><div class="alert alert-danger"><?php echo esc($err); ?></div><?php endif; ?>
<div class="card col-md-6"><div class="card-body">
<?php echo form_open('admin/pengaturan'); ?>
<?php foreach ($keys as $k => $l): ?><div class="form-group"><label><?php echo esc($l); ?></label><input name="<?php echo $k; ?>" class="form-control" value="<?php echo esc($cur[$k]); ?>"></div><?php endforeach; ?>
<button class="btn btn-primary">Simpan</button><?php echo form_close(); ?>
<p class="text-muted small mt-3">WhatsApp/email hanya diaktifkan bila kredensial sudah dipasang (saat ini notifikasi dalam aplikasi).</p></div></div>
</div></section></div>