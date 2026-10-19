<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Live: <?php echo esc($kelas); ?></h1><?php if ($topic !== ''): ?><p class="text-muted mb-0"><?php echo esc($topic); ?></p><?php endif; ?></div></div><section class="content"><div class="container-fluid">
<div class="card"><div class="card-body p-1"><div id="jitsiBox" style="height:72vh;min-height:420px"></div></div>
<div class="card-footer">
<?php echo form_open('guru/live', array('style' => 'display:inline')); ?><input type="hidden" name="act" value="stop"><input type="hidden" name="class_id" value="<?php echo (int)$cid; ?>"><button class="btn btn-secondary" onclick="return confirm('Tutup kelas live? Murid akan terputus.')">Tutup Kelas</button><?php echo form_close(); ?>
<a class="btn btn-outline-primary" target="_blank" href="https://meet.jit.si/<?php echo esc($room); ?>">Buka di tab baru</a>
<a class="btn btn-outline-secondary" href="<?php echo site_url('guru/live'); ?>">Kembali</a>
</div></div>
</div></section></div>
<script src="https://meet.jit.si/external_api.js"></script>
<script>(function(){if(typeof JitsiMeetExternalAPI==='undefined'){document.getElementById('jitsiBox').innerHTML='<div class="alert alert-danger m-3">Video tidak bisa dimuat (butuh internet). <a target="_blank" href="https://meet.jit.si/<?php echo esc($room); ?>">Buka lewat link ini</a>.</div>';return;}new JitsiMeetExternalAPI('meet.jit.si',{roomName:'<?php echo esc($room); ?>',parentNode:document.getElementById('jitsiBox'),userInfo:{displayName:'<?php echo esc($me['full_name'] . ' (Guru)'); ?>'},configOverwrite:{prejoinPageEnabled:false,startWithAudioMuted:false,startWithVideoMuted:false},interfaceConfigOverwrite:{SHOW_JITSI_WATERMARK:false}});})();</script>
