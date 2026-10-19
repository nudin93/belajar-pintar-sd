<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Dashboard Admin</h1></div></div><section class="content"><div class="container-fluid">
<div class="row">
<?php $cards = array(array('Siswa', $nSiswa, 'fa-child', 'info'), array('Guru', $nGuru, 'fa-chalkboard-teacher', 'success'), array('Kelas', $nKelas, 'fa-door-open', 'warning'), array('Mapel', $nMapel, 'fa-book', 'primary'), array('Tugas Aktif', $nTugasAktif, 'fa-tasks', 'info'), array('Menunggu Periksa', $nMenunggu, 'fa-search', 'warning'), array('Sudah Dinilai', $nDinilai, 'fa-check', 'success'), array('Belum Kumpul', $nBelum, 'fa-clock', 'danger'));
foreach ($cards as $k): ?>
<div class="col-lg-3 col-6"><div class="small-box bg-<?php echo $k[3]; ?>"><div class="inner"><h3><?php echo (int)$k[1]; ?></h3><p><?php echo esc($k[0]); ?></p></div><div class="icon"><i class="fas <?php echo $k[2]; ?>"></i></div></div></div>
<?php endforeach; ?></div>
<div class="row"><div class="col-md-7"><div class="card"><div class="card-header"><b>Pengumpulan per Mapel</b></div><div class="card-body"><canvas id="chMapel" height="120"></canvas></div></div></div>
<div class="col-md-5"><div class="card"><div class="card-header"><b>Aktivitas Terbaru</b></div><div class="card-body p-0"><ul class="list-group list-group-flush">
<?php foreach ($aktiv as $a): ?><li class="list-group-item small"><b><?php echo esc($a['full_name']); ?></b> <?php echo esc($a['action']); ?> <span class="text-muted"><?php echo esc(fmt_tgl($a['created_at'])); ?></span><br><span class="text-muted"><?php echo esc(mb_substr((string)$a['detail'], 0, 120)); ?></span></li><?php endforeach; ?>
</ul></div></div></div></div>
<div class="row"><div class="col-12"><div class="card"><div class="card-header"><b>Pintasan</b></div><div class="card-body">
<a class="btn btn-primary mr-2 mb-2" href="<?php echo site_url('admin/tugas'); ?>"><i class="fas fa-plus mr-1"></i>Tugas</a>
<a class="btn btn-warning mr-2 mb-2" href="<?php echo site_url('admin/pemeriksaan'); ?>"><i class="fas fa-search mr-1"></i>Periksa (<?php echo $nMenunggu; ?>)</a>
<a class="btn btn-success mr-2 mb-2" href="<?php echo site_url('admin/laporan'); ?>"><i class="fas fa-chart-bar mr-1"></i>Laporan</a>
<a class="btn btn-secondary mb-2" href="<?php echo site_url('admin/pengguna'); ?>"><i class="fas fa-users-cog mr-1"></i>Pengguna</a>
</div></div></div></div>
<script>document.addEventListener('DOMContentLoaded', function(){ if (window.Chart) { new Chart(document.getElementById('chMapel'), {type:'bar', data:{labels:<?php echo json_encode(array_column($perMapel, 'name')); ?>, datasets:[{label:'Pengumpulan', data:<?php echo json_encode(array_map('intval', array_column($perMapel, 'j'))); ?>, backgroundColor:'#2f6fed'}]}, options:{plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}}); } });</script>
</div></section></div>