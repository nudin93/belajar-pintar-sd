<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Prestasi Saya</h1></div></div><section class="content"><div class="container-fluid">
<div class="row">
<div class="col-md-4"><div class="card text-center"><div class="card-body"><h3><?php echo $nDinilai; ?></h3><p>Tugas dinilai</p></div></div></div>
<div class="col-md-4"><div class="card text-center"><div class="card-body"><h3><?php echo esc($rata); ?></h3><p>Rata-rata nilai</p></div></div></div>
<div class="col-md-4"><div class="card text-center"><div class="card-body"><h3><?php echo $foto; ?></h3><p>Foto dikirim</p></div></div></div></div>
<div class="row"><?php foreach ($badges as $b): ?><div class="col-6 col-md-4"><div class="card kid-card"><div class="card-body text-center"><i class="fas <?php echo $b[0]; ?> fa-3x text-warning"></i><h5 class="mt-2"><?php echo esc($b[1]); ?></h5><small class="text-muted"><?php echo esc($b[2]); ?></small></div></div></div><?php endforeach; ?></div>
<?php if (!$badges): ?><div class="alert alert-info">Kerjakan tugas pertamamu untuk membuka lencana!</div><?php endif; ?>
</div></section></div>