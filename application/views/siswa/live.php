<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Kelas Live</h1></div></div><section class="content"><div class="container-fluid">
<?php if ($info): ?>
<div class="card"><div class="card-body text-center">
<span class="badge badge-danger mb-2"><i class="fas fa-circle mr-1"></i>SEDANG LIVE</span>
<h5><?php echo esc(!empty($info['topic']) ? $info['topic'] : 'Kelas ' . $kelas); ?></h5>
<p class="small text-muted">Guru: <?php echo esc(isset($info['by']) ? $info['by'] : ''); ?> &middot; izinkan kamera &amp; mikrofon saat diminta browser</p>
<button id="gabungBtn" class="btn btn-success btn-lg"><i class="fas fa-video mr-1"></i>Gabung Kelas</button>
<a class="btn btn-outline-primary ml-2" target="_blank" href="https://meet.jit.si/<?php echo esc($info['room']); ?>">Tab baru</a>
</div></div>
<div class="card d-none" id="liveCard"><div class="card-body p-1"><div id="jitsiBox" style="height:72vh;min-height:420px"></div></div></div>
<script src="https://meet.jit.si/external_api.js"></script>
<script>document.getElementById('gabungBtn').addEventListener('click',function(){document.getElementById('liveCard').classList.remove('d-none');this.disabled=true;this.innerHTML='Menghubungkan...';if(typeof JitsiMeetExternalAPI==='undefined'){document.getElementById('jitsiBox').innerHTML='<div class="alert alert-danger m-3">Video tidak bisa dimuat (butuh internet). <a target="_blank" href="https://meet.jit.si/<?php echo esc($info['room']); ?>">Buka lewat link ini</a>.</div>';return;}new JitsiMeetExternalAPI('meet.jit.si',{roomName:'<?php echo esc($info['room']); ?>',parentNode:document.getElementById('jitsiBox'),userInfo:{displayName:'<?php echo esc($me['full_name']); ?>'},configOverwrite:{prejoinPageEnabled:false,startWithAudioMuted:true,startWithVideoMuted:false},interfaceConfigOverwrite:{SHOW_JITSI_WATERMARK:false}});});</script>
<?php else: ?>
<div class="card"><div class="card-body text-center py-5">
<i class="fas fa-video-slash fa-3x text-muted mb-3"></i>
<h5>Belum ada kelas live</h5><p class="text-muted small">Tunggu guru membuka kelas. Kamu akan dapat notifikasi otomatis saat dimulai.</p>
</div></div>
<?php endif; ?>
</div></section></div>
