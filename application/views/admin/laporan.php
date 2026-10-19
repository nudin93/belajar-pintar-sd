<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Laporan Pembelajaran</h1></div></div><section class="content"><div class="container-fluid">
<?php echo form_open('admin/laporan', array('method' => 'get', 'class' => 'form-inline mb-3')); ?>
<select name="kelas" class="form-control mr-2"><option value="">Semua kelas</option><?php foreach ($kelas as $k): ?><option value="<?php echo $k['id']; ?>" <?php echo $fk == $k['id'] ? 'selected' : ''; ?>><?php echo esc($k['name']); ?></option><?php endforeach; ?></select>
<select name="mapel" class="form-control mr-2"><option value="">Semua mapel</option><?php foreach ($mapel as $m): ?><option value="<?php echo $m['id']; ?>" <?php echo $fm == $m['id'] ? 'selected' : ''; ?>><?php echo esc($m['name']); ?></option><?php endforeach; ?></select>
<button class="btn btn-primary mr-2">Filter</button>
<a class="btn btn-success mr-2" target="_blank" href="<?php echo site_url('laporan/excel?kelas=' . $fk . '&mapel=' . $fm); ?>"><i class="fas fa-file-excel mr-1"></i>Excel</a>
<a class="btn btn-danger" target="_blank" href="<?php echo site_url('laporan/pdf?kelas=' . $fk . '&mapel=' . $fm); ?>"><i class="fas fa-file-pdf mr-1"></i>PDF</a>
<?php echo form_close(); ?>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table datatable"><thead><tr><th>Kelas</th><th>Mapel</th><th>Total</th><th>Dinilai</th><th>Menunggu</th><th>Rata-rata</th></tr></thead><tbody>
<?php foreach ($rekap as $r): ?><tr><td><?php echo esc($r['kelas']); ?></td><td><?php echo esc($r['mapel']); ?></td><td><?php echo (int)$r['total']; ?></td><td><?php echo (int)$r['dinilai']; ?></td><td><?php echo (int)$r['total'] - (int)$r['dinilai']; ?></td><td><b><?php echo esc($r['rata']); ?></b></td></tr><?php endforeach; ?>
</tbody></table></div></div></div>
</div></section></div>