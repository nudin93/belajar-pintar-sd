<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Dashboard Guru</h1></div></div><section class="content"><div class="container-fluid">
<?php if (!$hasClass): ?><div class="alert alert-warning">Akun Anda belum terhubung ke kelas. Hubungi admin.</div><?php endif; ?>
<div class="row">
<?php $cards = array(array('Siswa diajar', $nSiswa, 'fa-child', 'info'), array('Tugas aktif', $nTugas, 'fa-tasks', 'primary'), array('Menunggu periksa', $nMenunggu, 'fa-search', 'warning'), array('Belum kumpul', $nBelum, 'fa-clock', 'danger'), array('Sudah dinilai', $nDinilai, 'fa-check', 'success'));
foreach ($cards as $k): ?>
<div class="col-lg-3 col-6"><div class="small-box bg-<?php echo $k[3]; ?>"><div class="inner"><h3><?php echo $k[1]; ?></h3><p><?php echo $k[0]; ?></p></div><div class="icon"><i class="fas <?php echo $k[2]; ?>"></i></div></div></div>
<?php endforeach; ?></div>
<div class="row"><div class="col-md-6"><div class="card"><div class="card-header"><b>Pengumpulan terbaru</b></div><div class="card-body p-0"><ul class="list-group list-group-flush">
<?php foreach ($recent as $r): ?><li class="list-group-item small"><b><?php echo esc($r['full_name']); ?></b> mengumpulkan <b><?php echo esc($r['title']); ?></b><br><span class="text-muted"><?php echo esc(fmt_tgl($r['submitted_at'])); ?></span></li><?php endforeach; ?>
<?php if (!$recent): ?><li class="list-group-item text-muted">Belum ada pengumpulan.</li><?php endif; ?></ul></div></div></div>
<div class="col-md-6"><div class="card"><div class="card-header"><b>Progres per kelas</b></div><div class="card-body"><canvas id="chProg" height="140"></canvas></div></div></div></div>
<script>document.addEventListener('DOMContentLoaded', function(){ if (window.Chart) { new Chart(document.getElementById('chProg'), {type:'bar', data:{labels:<?php echo json_encode(array_column($prog, 'name')); ?>, datasets:[{label:'Dinilai', data:<?php echo json_encode(array_map('intval', array_column($prog, 'd'))); ?>, backgroundColor:'#28a745'}, {label:'Total', data:<?php echo json_encode(array_map('intval', array_column($prog, 't'))); ?>, backgroundColor:'#dee2e6'}]}, options:{scales:{y:{beginAtZero:true}}}}); } });</script>
</div></section></div>