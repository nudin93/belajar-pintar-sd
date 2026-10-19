<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Riwayat Belajar</h1></div></div><section class="content"><div class="container-fluid">
<?php foreach ($rows as $r): ?><div class="card"><div class="card-body py-2"><b><?php echo esc($r['title']); ?></b> <small class="text-muted"><?php echo esc($r['mapel']); ?></small> <?php echo status_label($r['status']); ?><br>
<small class="text-muted">Dikumpulkan: <?php echo esc(fmt_tgl($r['submitted_at'])); ?><?php echo $r['final_score'] !== null ? ' &middot; Nilai: ' . esc($r['final_score']) : ''; ?></small></div></div><?php endforeach; ?>
<?php if (!$rows): ?><p class="text-muted">Belum ada riwayat.</p><?php endif; ?>
</div></section></div>