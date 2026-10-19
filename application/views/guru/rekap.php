<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Rekap Nilai</h1></div></div><section class="content"><div class="container-fluid">
<div class="mb-3"><a class="btn btn-success btn-sm" target="_blank" href="<?php echo site_url('laporan/excel'); ?>"><i class="fas fa-file-excel mr-1"></i>Export Excel (tugasku)</a>
<a class="btn btn-danger btn-sm" target="_blank" href="<?php echo site_url('laporan/pdf'); ?>"><i class="fas fa-file-pdf mr-1"></i>Export PDF</a></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table datatable"><thead><tr><th>Kelas</th><th>Mapel</th><th>Total</th><th>Dinilai</th><th>Rata-rata</th></tr></thead><tbody>
<?php foreach ($rekap as $r): ?><tr><td><?php echo esc($r['kelas']); ?></td><td><?php echo esc($r['mapel']); ?></td><td><?php echo (int)$r['total']; ?></td><td><?php echo (int)$r['dinilai']; ?></td><td><b><?php echo esc($r['rata']); ?></b></td></tr><?php endforeach; ?>
</tbody></table></div></div></div>
</div></section></div>