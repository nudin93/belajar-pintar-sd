<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
    <li class="nav-item d-none d-sm-inline-block"><span class="nav-link font-weight-bold"><?php echo esc(app_setting('school_name', 'Belajar Pintar SD')); ?></span></li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <a class="nav-link" href="<?php echo site_url($me['role'] === 'admin' ? 'admin/notifikasi' : ($me['role'] === 'guru' ? 'guru/notifikasi' : 'siswa/notifikasi')); ?>">
        <i class="far fa-bell"></i>
        <span id="notifCount" data-url="<?php echo site_url('api/notif_count'); ?>" class="badge badge-warning navbar-badge" style="display:none"></span>
      </a>
    </li>
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#"><i class="fas fa-user-circle"></i> <?php echo esc($me['full_name']); ?> <small class="text-muted">(<?php echo esc($me['role']); ?>)</small></a>
      <div class="dropdown-menu dropdown-menu-right">
        <a href="<?php echo site_url('profile'); ?>" class="dropdown-item"><i class="fas fa-id-card mr-2"></i>Profil</a>
        <a href="<?php echo site_url('profile/password'); ?>" class="dropdown-item"><i class="fas fa-key mr-2"></i>Ganti Password</a>
        <div class="dropdown-divider"></div>
        <a href="<?php echo site_url('auth/logout'); ?>" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
      </div>
    </li>
  </ul>
</nav>
