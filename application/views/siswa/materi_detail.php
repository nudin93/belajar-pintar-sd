<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0"><?php echo esc($m["title"]); ?></h1></div></div><section class="content"><div class="container-fluid">
<div class="card"><div class="card-body">
<p><span class="badge badge-info"><?php echo esc($m['mapel']); ?></span> <span class="badge badge-secondary"><?php echo esc($m['topic']); ?></span></p>
<?php if ($m['description']): ?><p><?php echo nl2br(esc($m['description'])); ?></p><?php endif; ?>
<div><?php echo nl2br(esc($m['content'])); ?></div>
<?php if ($m['image_path']): ?><img class="img-fluid rounded mt-3" src="<?php echo base_url('uploads/' . $m['image_path']); ?>"><?php endif; ?>
<?php if ($m['pdf_path']): ?><p class="mt-3"><a class="btn btn-sm btn-secondary" target="_blank" href="<?php echo base_url('uploads/' . $m['pdf_path']); ?>"><i class="fas fa-file-pdf mr-1"></i>Buka PDF</a></p><?php endif; ?>
<?php if ($m['video_url']): ?><p><a class="btn btn-sm btn-danger" target="_blank" href="<?php echo esc($m['video_url']); ?>"><i class="fas fa-video mr-1"></i>Tonton Video</a></p><?php endif; ?>
</div></div>
<a class="btn btn-secondary" href="<?php echo site_url('siswa/materi'); ?>">Kembali</a>
</div></section></div>