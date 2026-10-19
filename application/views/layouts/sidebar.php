<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="<?php echo site_url(); ?>" class="brand-link">
    <i class="fas fa-graduation-cap brand-image ml-2 mt-2 text-white" style="font-size:1.6rem"></i>
    <span class="brand-text font-weight-bold text-white">Belajar Pintar SD</span>
  </a>
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <?php $am = isset($menu) ? $menu : ''; ?>
        <?php if ($me['role'] === 'admin'): ?>
          <?php $items = array(array('dashboard', 'admin/dashboard', 'fa-tachometer-alt', 'Dashboard'), array('sekolah', 'admin/sekolah', 'fa-school', 'Data Sekolah'), array('guru', 'admin/guru', 'fa-chalkboard-teacher', 'Data Guru'), array('siswa', 'admin/siswa', 'fa-child', 'Data Siswa'), array('kelas', 'admin/kelas', 'fa-door-open', 'Data Kelas'), array('mapel', 'admin/mapel', 'fa-book', 'Mata Pelajaran'), array('materi', 'admin/materi', 'fa-book-open', 'Materi'), array('bank', 'admin/bank_soal', 'fa-question-circle', 'Bank Soal'), array('tugas', 'admin/tugas', 'fa-tasks', 'Tugas & Ujian'), array('periksa', 'admin/pemeriksaan', 'fa-search', 'Pemeriksaan'), array('nilai', 'admin/nilai', 'fa-star', 'Nilai Siswa'), array('laporan', 'admin/laporan', 'fa-chart-bar', 'Laporan'), array('tahun', 'admin/tahun', 'fa-calendar-alt', 'Tahun Ajaran'), array('pengguna', 'admin/pengguna', 'fa-users-cog', 'Pengguna'), array('pengaturan', 'admin/pengaturan', 'fa-cog', 'Pengaturan'), array('backup', 'admin/backup', 'fa-database', 'Backup')); ?>
        <?php elseif ($me['role'] === 'guru'): ?>
          <?php $items = array(array('dashboard', 'guru/dashboard', 'fa-tachometer-alt', 'Dashboard'), array('kelas', 'guru/kelas', 'fa-door-open', 'Kelas yang Diajar'), array('siswa', 'guru/siswa', 'fa-child', 'Daftar Siswa'), array('live', 'guru/live', 'fa-video', 'Kelas Live'), array('materi', 'guru/materi', 'fa-book-open', 'Materi'), array('bank', 'guru/bank_soal', 'fa-question-circle', 'Bank Soal'), array('tugas_new', 'guru/tugas_form', 'fa-plus', 'Buat Tugas'), array('tugas', 'guru/tugas', 'fa-tasks', 'Daftar Tugas'), array('jawaban', 'guru/jawaban', 'fa-inbox', 'Jawaban Masuk'), array('nilai', 'guru/nilai', 'fa-star', 'Nilai Siswa'), array('rekap', 'guru/rekap', 'fa-chart-bar', 'Rekap Nilai'), array('notif', 'guru/notifikasi', 'fa-bell', 'Notifikasi')); ?>
        <?php else: ?>
          <?php $items = array(array('dashboard', 'siswa/dashboard', 'fa-home', 'Dashboard'), array('materi', 'siswa/materi', 'fa-book-open', 'Materi Pelajaran'), array('tugas', 'siswa/tugas', 'fa-tasks', 'Tugas Saya'), array('live', 'siswa/live', 'fa-video', 'Kelas Live'), array('hasil', 'siswa/hasil', 'fa-star', 'Hasil & Nilai'), array('riwayat', 'siswa/riwayat', 'fa-history', 'Riwayat Belajar'), array('prestasi', 'siswa/prestasi', 'fa-trophy', 'Prestasi Saya'), array('notif', 'siswa/notifikasi', 'fa-bell', 'Notifikasi')); ?>
        <?php endif; ?>
        <?php foreach ($items as $it): ?><li class="nav-item"><a href="<?php echo site_url($it[1]); ?>" class="nav-link <?php echo $am === $it[0] ? 'active' : ''; ?>"><i class="nav-icon fas <?php echo $it[2]; ?>"></i><p><?php echo $it[3]; ?></p></a></li><?php endforeach; ?>
        <li class="nav-item"><a href="<?php echo site_url('auth/logout'); ?>" class="nav-link text-warning"><i class="nav-icon fas fa-sign-out-alt"></i><p>Logout</p></a></li>
      </ul>
    </nav>
  </div>
</aside>
