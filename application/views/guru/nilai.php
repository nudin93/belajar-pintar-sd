<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Nilai Siswa</h1></div></div><section class="content"><div class="container-fluid">
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table datatable"><thead><tr><th>Siswa</th><th>Kelas</th><th>Mapel</th><th>Tugas</th><th>Nilai</th><th>Publik</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?php echo esc($r['siswa']); ?></td><td><?php echo esc($r['kelas']); ?></td><td><?php echo esc($r['mapel']); ?></td><td><?php echo esc($r['tugas']); ?></td><td><b><?php echo esc($r['score']); ?></b></td><td><?php echo $r['is_published'] ? 'Ya' : 'Belum'; ?></td></tr><?php endforeach; ?>
</tbody></table></div></div></div>
</div></section></div>