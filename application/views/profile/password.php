<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Ganti Password</h1></div></div>
<section class="content"><div class="container-fluid"><div class="card col-md-6"><div class="card-body">
<?php if (!empty($msg)): ?><div class="alert alert-success"><?php echo esc($msg); ?></div><?php endif; ?>
<?php if (!empty($err)): ?><div class="alert alert-danger"><?php echo esc($err); ?></div><?php endif; ?>
<?php echo form_open('profile/password'); ?>
<div class="form-group"><label>Password lama</label><input name="old" type="password" class="form-control" required></div>
<div class="form-group"><label>Password baru</label><input name="n1" type="password" class="form-control" required></div>
<div class="form-group"><label>Ulangi password baru</label><input name="n2" type="password" class="form-control" required></div>
<button class="btn btn-primary">Simpan</button><?php echo form_close(); ?></div></div></div></section></div>
