<!-- Pengembang: Zainudin Larau — Belajar Pintar SD -->
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col"><h1 class="m-0">Daftar Tugas</h1></div></div></div></div><section class="content"><div class="container-fluid">
<a class="btn btn-primary mb-3" href="<?php echo site_url('guru/tugas_form'); ?>"><i class="fas fa-plus mr-1"></i>Buat Tugas</a>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table datatable"><thead><tr><th>Judul</th><th>Kelas</th><th>Mapel</th><th>Tenggat</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?php echo esc($r['title']); ?><?php if (!empty($r['n_soal'])): ?> <span class="badge badge-info"><?php echo (int)$r['n_soal']; ?> foto soal</span><?php endif; ?><?php if (!empty($r['n_contoh'])): ?> <span class="badge badge-success"><?php echo (int)$r['n_contoh']; ?> contoh</span><?php endif; ?></td><td><?php echo esc($r['kelas']); ?></td><td><?php echo esc($r['mapel']); ?></td><td class="small"><?php echo esc(fmt_tgl($r['due_at'])); ?></td><td><?php echo status_label($r['status']); ?></td><td class="text-nowrap">
<a class="btn btn-sm btn-secondary" href="<?php echo site_url('guru/tugas_form/' . $r['id']); ?>">Edit</a>
<a class="btn btn-sm btn-dark" href="<?php echo site_url('guru/duplikat_tugas/' . $r['id']); ?>" onclick="return confirm('Duplikat tugas ini jadi tugas baru (draf)? Soal lama bisa dihapus dari salinannya.')">Duplikat</a>
<a class="btn btn-sm btn-info" href="<?php echo site_url('guru/bank_soal?assignment=' . $r['id']); ?>">Soal</a>
<a class="btn btn-sm btn-warning" href="<?php echo site_url('guru/jawaban?assignment=' . $r['id']); ?>">Jawaban</a>
<a class="btn btn-sm btn-danger" href="<?php echo site_url('guru/hapus_tugas/' . $r['id']); ?>" onclick="return confirm('Hapus tugas ini?')">Hapus</a></td></tr><?php endforeach; ?>
</tbody></table></div></div></div>
</div></section></div>