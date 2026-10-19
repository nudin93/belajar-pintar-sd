<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Nilai Siswa</h1></div></div><section class="content"><div class="container-fluid">
<?php echo form_open('admin/nilai', array('method' => 'get', 'class' => 'form-inline mb-3')); ?><select name="kelas" class="form-control mr-2" onchange="this.form.submit()"><option value="">Semua kelas</option>
<?php foreach ($kelas as $k): ?><option value="<?php echo $k['id']; ?>" <?php echo $fk == $k['id'] ? 'selected' : ''; ?>><?php echo esc($k['name']); ?></option><?php endforeach; ?></select><?php echo form_close(); ?>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table datatable"><thead><tr><th>Siswa</th><th>Kelas</th><th>Mapel</th><th>Tugas</th><th>Nilai</th><th>Publik</th><th>Komentar</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?php echo esc($r['siswa']); ?></td><td><?php echo esc($r['kelas']); ?></td><td><?php echo esc($r['mapel']); ?></td><td><?php echo esc($r['tugas']); ?></td><td><b><?php echo esc($r['score']); ?></b></td><td><?php echo $r['is_published'] ? 'Ya' : 'Belum'; ?></td><td class="small"><?php echo esc(mb_substr((string)$r['comment'], 0, 80)); ?></td></tr><?php endforeach; ?>
</tbody></table></div></div></div>
</div></section></div>