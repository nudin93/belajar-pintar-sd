<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Profil Saya</h1></div></div>
<section class="content"><div class="container-fluid"><div class="card"><div class="card-body">
<?php if (!empty($msg)): ?><div class="alert alert-success"><?php echo esc($msg); ?></div><?php endif; ?>
<p><strong>Username:</strong> <?php echo esc($row['username']); ?> &middot; <strong>Peran:</strong> <?php echo esc($row['role']); ?></p>
<?php echo form_open('profile'); ?>
<div class="form-group"><label>Nama lengkap</label><input name="full_name" class="form-control" value="<?php echo esc($row['full_name']); ?>"></div>
<div class="form-group"><label>Email</label><input name="email" type="email" class="form-control" value="<?php echo esc($row['email']); ?>"></div>
<button class="btn btn-primary">Simpan</button> <a class="btn btn-secondary" href="<?php echo site_url('profile/password'); ?>">Ganti Password</a>
<?php echo form_close(); ?></div></div></div></section></div>
