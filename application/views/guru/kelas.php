<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Kelas yang Diajar</h1></div></div><section class="content"><div class="container-fluid">
<?php foreach ($rows as $r): ?><div class="card"><div class="card-body"><b><?php echo esc($r['name']); ?></b> <span class="text-muted">Tingkat <?php echo (int)$r['grade_level']; ?> &middot; <?php echo (int)$r['n']; ?> siswa</span>
<?php if (!empty($wali) && in_array((int)$r['id'], array_map('intval', $wali), true)): ?> <span class="badge badge-success">Wali Kelas</span><?php endif; ?>
<a class="btn btn-sm btn-info float-right" href="<?php echo site_url('guru/siswa?kelas=' . $r['id']); ?>">Lihat Siswa</a></div></div><?php endforeach; ?>
<?php if ($own && !$rows): ?><div class="alert alert-info">Anda punya tugas di kelas berikut (dari tugas yang Anda buat):</div>
<?php foreach ($own as $r): ?><div class="card"><div class="card-body"><b><?php echo esc($r['name']); ?></b></div></div><?php endforeach; ?><?php endif; ?>
<?php if (!$rows && !$own): ?><div class="alert alert-warning">Belum ada kelas. Hubungi admin.</div><?php endif; ?>
</div></section></div>
