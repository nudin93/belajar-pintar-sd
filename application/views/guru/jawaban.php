<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Jawaban Masuk</h1></div></div><section class="content"><div class="container-fluid">
<div class="mb-2"><a class="btn btn-sm btn-secondary" href="<?php echo site_url('guru/jawaban'); ?>">Semua</a> <a class="btn btn-sm btn-warning" href="<?php echo site_url('guru/jawaban?filter=menunggu'); ?>">Menunggu pemeriksaan</a></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table datatable"><thead><tr><th>Siswa</th><th>Kelas</th><th>Tugas</th><th>Dikumpulkan</th><th>Foto</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?php echo esc($r['siswa']); ?></td><td><?php echo esc($r['kelas']); ?></td><td><?php echo esc($r['tugas']); ?></td><td class="small"><?php echo esc(fmt_tgl($r['submitted_at'])); ?></td><td><?php echo (int)$r['foto']; ?></td><td><?php echo status_label($r['status']); ?></td><td><a class="btn btn-sm btn-primary" href="<?php echo site_url('guru/periksa/' . $r['id']); ?>">Periksa</a></td></tr><?php endforeach; ?>
</tbody></table></div></div></div>
</div></section></div>