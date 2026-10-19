<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Pemeriksaan Jawaban</h1></div></div><section class="content"><div class="container-fluid">
<div class="mb-2"><a class="btn btn-sm btn-warning" href="<?php echo site_url('admin/pemeriksaan?status=menunggu'); ?>">Menunggu</a>
<a class="btn btn-sm btn-success" href="<?php echo site_url('admin/pemeriksaan?status=dinilai'); ?>">Sudah dinilai</a>
<a class="btn btn-sm btn-secondary" href="<?php echo site_url('admin/pemeriksaan?status=semua'); ?>">Semua</a></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-striped datatable"><thead><tr><th>Siswa</th><th>Kelas</th><th>Mapel</th><th>Tugas</th><th>Dikumpulkan</th><th>Foto</th><th>Status</th><th>Nilai</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?php echo esc($r['siswa']); ?></td><td><?php echo esc($r['kelas']); ?></td><td><?php echo esc($r['mapel']); ?></td><td><?php echo esc($r['tugas']); ?></td>
<td class="small"><?php echo esc(fmt_tgl($r['submitted_at'])); ?><?php echo $r['is_late'] ? ' <span class="badge badge-danger">terlambat</span>' : ''; ?></td>
<td><?php echo (int)$r['foto']; ?></td><td><?php echo status_label($r['status']); ?></td><td><?php echo $r['final_score'] === null ? '-' : esc($r['final_score']); ?></td>
<td><a class="btn btn-sm btn-primary" href="<?php echo site_url('admin/periksa/' . $r['id']); ?>">Periksa</a></td></tr><?php endforeach; ?>
</tbody></table></div></div></div>
</div></section></div>