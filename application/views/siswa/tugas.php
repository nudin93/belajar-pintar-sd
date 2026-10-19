<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Tugas Saya</h1></div></div><section class="content"><div class="container-fluid">
<div class="mb-2"><a class="btn btn-sm btn-secondary" href="<?php echo site_url('siswa/tugas'); ?>">Semua</a> <a class="btn btn-sm btn-warning" href="<?php echo site_url('siswa/tugas?filter=belum'); ?>">Belum selesai</a></div>
<?php foreach ($rows as $r): ?><div class="card"><div class="card-body">
<h5><?php echo esc($r['title']); ?> <?php echo status_label($r['status'] ? $r['status'] : 'belum'); ?></h5>
<p class="small text-muted mb-1"><?php echo esc($r['mapel']); ?> &middot; Tenggat: <?php echo esc(fmt_tgl($r['due_at'])); ?>
<?php if ($r['is_published'] && $r['final_score'] !== null): ?> &middot; Nilai: <b><?php echo esc($r['final_score']); ?></b><?php endif; ?></p>
<p class="small"><?php echo esc(mb_substr((string)$r['instructions'], 0, 140)); ?></p>
<a class="btn btn-sm btn-primary" href="<?php echo site_url('siswa/tugas_detail/' . $r['id']); ?>"><?php echo in_array($r['status'] ? $r['status'] : 'belum', array('belum', 'sedang', 'perbaikan')) ? 'Kerjakan' : 'Lihat'; ?></a>
</div></div><?php endforeach; ?>
<?php if (!$rows): ?><div class="alert alert-info">Tidak ada tugas. Rajin cek lagi ya!</div><?php endif; ?>
</div></section></div>