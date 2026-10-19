<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | Belajar Pintar SD</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
<style>body{background:linear-gradient(135deg,#1b2a4a,#2f6fed);min-height:100vh}.login-card{border-radius:1rem}</style>
</head><body class="d-flex align-items-center justify-content-center p-3">
<div class="card login-card shadow col-12 col-sm-8 col-md-5 col-lg-4">
<div class="card-body p-4">
<div class="text-center mb-3"><i class="fas fa-graduation-cap fa-3x text-primary"></i>
<h3 class="mt-2 font-weight-bold">Belajar Pintar SD</h3>
<p class="text-muted"><?php echo esc(app_setting('school_name', '')); ?></p></div>
<?php if (!empty($err)): ?><div class="alert alert-danger"><?php echo esc($err); ?></div><?php endif; ?>
<?php echo form_open('auth'); ?>
<div class="form-group"><label>Username</label><input name="username" class="form-control" required autofocus autocomplete="username"></div>
<div class="form-group"><label>Password</label><input name="password" type="password" class="form-control" required autocomplete="current-password"></div>
<button class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt mr-1"></i>Masuk</button>
<?php echo form_close(); ?>
<p class="text-muted small mt-3 mb-0">Akun dibuat oleh admin/guru. Untuk instalasi baru, buka <code>install</code>.</p>
<p class="text-muted small mt-2 mb-0">Pengembang: <b>Zainudin Larau</b></p>
</div></div></body></html>
