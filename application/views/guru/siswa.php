<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Daftar Siswa</h1></div></div><section class="content"><div class="container-fluid">
<div class="card"><div class="card-body">
<form method="get" class="form-inline mb-2">
<label class="mr-2">Kelas</label>
<select name="kelas" class="form-control mr-2" onchange="this.form.submit()">
<option value="">- Semua kelas saya (<?php echo count($rows); ?> siswa) -</option>
<?php foreach ($kelas as $k): ?><option value="<?php echo (int)$k['id']; ?>" <?php echo ((int)$fk === (int)$k['id']) ? 'selected' : ''; ?>><?php echo esc($k['name']); ?></option><?php endforeach; ?>
</select>
<?php if ($fk): ?><a class="btn btn-sm btn-secondary" href="<?php echo site_url('guru/siswa'); ?>">Tampilkan semua</a><?php endif; ?>
</form>
</div><div class="card-body p-0"><div class="table-responsive"><table class="table datatable"><thead><tr><th>Nama</th><th>NIS</th><th>NISN</th><th>Kelas</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?php echo esc($r['full_name']); ?></td><td><?php echo esc($r['nis']); ?></td><td><?php echo esc($r['nisn']); ?></td><td><?php echo esc($r['kelas']); ?></td></tr><?php endforeach; ?>
</tbody></table></div></div></div>
<?php if (!$rows): ?><div class="alert alert-warning">Belum ada murid di daftar Anda. Hubungi admin bila Anda wali kelas ini.</div><?php endif; ?>
</div></section></div>
