<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Halo, <?php echo esc($me["full_name"]); ?>!</h1></div></div><section class="content"><div class="container-fluid">
<div class="kid-hero p-4 mb-3"><h4 class="mb-1">Semangat belajar hari ini!</h4>
<p class="mb-2">Kelas: <b><?php echo esc($prof['class_name']); ?></b></p>
<div class="progress" style="height:14px"><div class="progress-bar bg-success" style="width:<?php echo $prog; ?>%"><?php echo $prog; ?>%</div></div>
<small><?php echo $nDinilai; ?> tugas selesai dinilai dari <?php echo $total; ?></small><br>
<a class="btn btn-light btn-sm mt-2" href="<?php echo site_url('siswa/tugas?filter=belum'); ?>"><i class="fas fa-play mr-1"></i>Mulai Belajar</a></div>
<div class="row">
<?php $cards = array(array('Tugas baru', $nBaru, 'fa-inbox', 'primary', 'siswa/tugas'), array('Belum selesai', $nBelum, 'fa-clock', 'warning', 'siswa/tugas?filter=belum'), array('Menunggu diperiksa', $nTunggu, 'fa-search', 'info', 'siswa/tugas'), array('Sudah dinilai', $nDinilai, 'fa-star', 'success', 'siswa/hasil'));
foreach ($cards as $k): ?>
<div class="col-6 col-lg-3"><div class="card kid-card"><div class="card-body text-center"><i class="fas <?php echo $k[2]; ?> fa-2x text-<?php echo $k[3]; ?>"></i><h3><?php echo $k[1]; ?></h3><p class="mb-2"><?php echo $k[0]; ?></p><a class="btn btn-sm btn-outline-primary" href="<?php echo site_url($k[4]); ?>">Buka</a></div></div></div>
<?php endforeach; ?></div>
<div class="row"><div class="col-md-6"><div class="card"><div class="card-header"><b>Nilai terbaru</b></div><div class="card-body p-0"><ul class="list-group list-group-flush">
<?php foreach ($nilaiBaru as $n): ?><li class="list-group-item"><?php echo esc($n['title']); ?> <b class="float-right"><?php echo esc($n['score']); ?></b></li><?php endforeach; ?>
<?php if (!$nilaiBaru): ?><li class="list-group-item text-muted">Belum ada nilai.</li><?php endif; ?></ul></div></div></div>
<div class="col-md-6"><div class="card"><div class="card-header"><b>Materi terbaru</b></div><div class="card-body p-0"><ul class="list-group list-group-flush">
<?php foreach ($materi as $m): ?><li class="list-group-item"><a href="<?php echo site_url('siswa/materi_detail/' . $m['id']); ?>"><?php echo esc($m['title']); ?></a><br><small class="text-muted"><?php echo esc($m['mapel']); ?></small></li><?php endforeach; ?>
<?php if (!$materi): ?><li class="list-group-item text-muted">Belum ada materi.</li><?php endif; ?></ul></div></div></div></div>
</div></section></div>