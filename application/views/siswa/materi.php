<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Materi Pelajaran</h1></div></div><section class="content"><div class="container-fluid">
<div class="row"><?php foreach ($rows as $m): ?><div class="col-md-6 col-lg-4"><div class="card kid-card"><div class="card-body">
<h5><?php echo esc($m['title']); ?></h5><p class="small text-muted"><?php echo esc($m['mapel']); ?> &middot; <?php echo esc($m['topic']); ?></p>
<p class="small"><?php echo esc(mb_substr((string)$m['description'], 0, 120)); ?></p>
<a class="btn btn-sm btn-primary" href="<?php echo site_url('siswa/materi_detail/' . $m['id']); ?>">Baca Materi</a></div></div></div><?php endforeach; ?></div>
<?php if (!$rows): ?><div class="alert alert-info">Belum ada materi untuk kelasmu.</div><?php endif; ?>
</div></section></div>