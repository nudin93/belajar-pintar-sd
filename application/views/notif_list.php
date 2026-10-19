<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Notifikasi</h1></div></div>
<section class="content"><div class="container-fluid">
<a class="btn btn-sm btn-secondary mb-2" href="?all=1">Tandai semua dibaca</a>
<?php foreach ($rows as $n): ?><div class="card <?php echo $n['is_read'] ? '' : 'border-warning'; ?>"><div class="card-body py-2">
<b><?php echo esc($n['title']); ?></b> <small class="text-muted"><?php echo esc(fmt_tgl($n['created_at'])); ?></small><br>
<span class="small"><?php echo esc($n['message']); ?></span>
<?php if ($n['link']): ?> <a class="small" href="<?php echo site_url(strpos($n['link'], '/') !== false ? $n['link'] : ($me['role'] . '/' . $n['link'])); ?>">Buka</a><?php endif; ?>
<?php if (!$n['is_read']): ?> <a class="btn btn-sm btn-link" href="?read=<?php echo $n['id']; ?>">Tandai dibaca</a><?php endif; ?>
</div></div><?php endforeach; ?>
<?php if (!$rows): ?><p class="text-muted">Belum ada notifikasi.</p><?php endif; ?>
</div></section></div>
