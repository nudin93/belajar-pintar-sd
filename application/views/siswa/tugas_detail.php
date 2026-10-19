<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0"><?php echo esc($a["title"]); ?></h1></div></div><section class="content"><div class="container-fluid"><?php if (!empty($msg)): ?><div class="alert alert-success"><?php echo esc($msg); ?><br><small>Bukti pengiriman: <?php echo esc(fmt_tgl($sb['submitted_at'])); ?> &middot; Status <?php echo status_label($sb['status']); ?></small></div><?php endif; ?>
<?php if (!empty($err)): ?><div class="alert alert-danger"><?php echo esc($err); ?></div><?php endif; ?>
<div class="row"><div class="col-md-5">
<div class="card"><div class="card-body">
<p><span class="badge badge-info"><?php echo esc($a['mapel']); ?></span> <?php echo status_label($sb['status']); ?></p>
<p class="small">Tenggat: <b><?php echo esc(fmt_tgl($a['due_at'])); ?></b> &middot; Nilai maks: <?php echo (int)$a['max_score']; ?> &middot; Maks <?php echo (int)$a['max_photos']; ?> foto @ <?php echo (int)$a['max_file_mb']; ?> MB</p>
<p><b>Instruksi guru:</b><br><?php echo nl2br(esc($a['instructions'])); ?></p>
<?php if (!empty($soal_photos)): ?><hr><b>Foto Soal dari Guru</b> <small class="text-muted">klik untuk memperbesar</small><div class="row mt-2">
<?php foreach ($soal_photos as $sp): ?><div class="col-6 mb-2"><a href="<?php echo base_url('uploads/' . $sp['file_path']); ?>" target="_blank"><img class="photo-thumb" loading="lazy" src="<?php echo base_url('uploads/' . $sp['file_path']); ?>"></a></div><?php endforeach; ?>
</div><?php endif; ?>
<?php if (!empty($contoh_photos)): ?><hr><b>Foto Contoh Mengerjakan</b> <small class="text-muted">contoh dari guru — tiru caranya</small><div class="row mt-2">
<?php foreach ($contoh_photos as $sp): ?><div class="col-6 mb-2"><a href="<?php echo base_url('uploads/' . $sp['file_path']); ?>" target="_blank"><img class="photo-thumb" loading="lazy" src="<?php echo base_url('uploads/' . $sp['file_path']); ?>"></a></div><?php endforeach; ?>
</div><?php endif; ?>
<?php if ($fb): ?><hr><b>Komentar guru:</b><?php foreach ($fb as $f): ?><p class="small"><b><?php echo esc($f['full_name']); ?>:</b> <?php echo esc($f['message']); ?></p><?php endforeach; ?><?php endif; ?>
<?php if ($grade && $grade['is_published']): ?><div class="alert alert-success">Nilai: <b><?php echo esc($grade['score']); ?></b><br><small><?php echo esc($grade['comment']); ?></small></div><?php endif; ?>
</div></div>
</div><div class="col-md-7">
<?php echo form_open_multipart('siswa/tugas_detail/' . $a['id'], array('id' => 'jawabForm')); ?>
<?php $locked = ($sb['status'] === 'dinilai'); $redo = ($sb['status'] === 'perbaikan'); $dis = $locked ? 'disabled' : ''; ?>
<?php $needPhoto = true; foreach ((array)$qs as $qq) if (in_array($qq['question_type'], array('pg', 'bs', 'isian'), true)) { $needPhoto = false; break; } ?>
<?php $btnLock = ($needPhoto && !$photos) ? 'disabled' : ''; ?>
<?php if ($locked): ?><div class="alert alert-secondary"><i class="fas fa-lock mr-1"></i><b>Tugas ini sudah dinilai.</b> Jawaban dikunci dan tidak bisa dikirim ulang.</div>
<?php elseif ($redo): ?><div class="alert alert-danger"><i class="fas fa-redo mr-1"></i><b>Guru meminta kamu mengerjakan ulang.</b> Perbaiki jawabanmu lalu kirim lagi lewat tombol merah di bawah.</div><?php endif; ?>
<?php if ($groups): ?><div class="card"><div class="card-header"><b>Soal</b></div><div class="card-body">
<?php $no = 0; foreach ($groups as $g): ?>
<?php if (!empty($g['section'])): ?><div class="alert alert-info py-2"><b>Bagian: <?php echo esc($g['section']['title']); ?></b></div><?php endif; ?>
<?php foreach ($g['qs'] as $q): $no++; ?><div class="mb-3"><b><?php echo $no; ?>.</b> <?php echo esc($q['question_text']); ?> <small class="text-muted">(<?php echo (int)$q['points']; ?> poin)</small>
<?php if ($q['question_type'] === 'pg'): foreach ($q['opts'] as $o): ?>
<div class="form-check"><input class="form-check-input" type="radio" name="ans[<?php echo $q['id']; ?>]" value="<?php echo $o['id']; ?>" <?php echo $dis; ?> <?php echo ((isset($savedAns[$q['id']]['selected_option_id']) ? $savedAns[$q['id']]['selected_option_id'] : '') == $o['id']) ? 'checked' : ''; ?>><label class="form-check-label"><?php echo esc($o['option_label'] . '. ' . $o['option_text']); ?></label></div>
<?php endforeach; elseif ($q['question_type'] === 'bs'): ?>
<select name="ans[<?php echo $q['id']; ?>]" class="form-control col-md-4" <?php echo $dis; ?>><option value="">- pilih -</option><option value="Benar">Benar</option><option value="Salah">Salah</option></select>
<?php elseif ($q['question_type'] === 'foto'): ?><p class="small text-muted">Tulis di buku, lalu foto di bawah.</p>
<?php else: ?><input name="ans[<?php echo $q['id']; ?>]" class="form-control" <?php echo $dis; ?> value="<?php echo esc(isset($savedAns[$q['id']]['answer_text']) ? $savedAns[$q['id']]['answer_text'] : ''); ?>" placeholder="Jawaban..."><?php endif; ?></div><?php endforeach; ?>
<?php endforeach; ?></div></div><?php endif; ?>
<div class="card"><div class="card-header"><b>Foto Jawaban di Buku</b> <small class="text-muted">JPG/PNG/WebP</small></div><div class="card-body">
<?php if ($photos): ?><?php if ($locked): ?><div class="row"><?php foreach ($photos as $p): ?><div class="col-4 mb-2 text-center"><a href="<?php echo base_url('uploads/' . $p['file_path']); ?>" target="_blank"><img class="photo-thumb" loading="lazy" src="<?php echo base_url('uploads/' . $p['file_path']); ?>"></a></div><?php endforeach; ?></div>
<?php else: ?><p class="small">Foto terkirim (centang yang disimpan):</p><div class="row">
<?php foreach ($photos as $p): ?><div class="col-4 mb-2 text-center"><img class="photo-thumb" src="<?php echo base_url('uploads/' . $p['file_path']); ?>"><br><label class="small"><input type="checkbox" name="keep_photo[]" value="<?php echo $p['id']; ?>" checked> Simpan</label></div><?php endforeach; ?></div><?php endif; ?><?php endif; ?>
<?php if (!$locked): ?>
<div class="form-group"><label>Ambil Foto (kamera HP)</label><input type="file" name="photos[]" accept="image/*" capture="environment" class="form-control-file bp-photo" data-preview="#prevBox"></div>
<div class="form-group"><label>Pilih dari Galeri (bisa beberapa)</label><input type="file" name="photos[]" accept="image/*" multiple class="form-control-file bp-photo" data-preview="#prevBox"></div>
<div id="prevBox" class="row"></div>
<div class="progress upload-progress d-none" id="upProg"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width:100%">Mengunggah...</div></div>
<?php if ($redo): ?><button id="kirimBtn" class="btn btn-danger btn-block mt-2" <?php echo $btnLock; ?> onclick="document.getElementById('upProg').classList.remove('d-none')"><i class="fas fa-redo mr-1"></i>Kirim Ulang Perbaikan</button>
<p class="small text-muted mt-2">Tombol merah = perintah kerjakan ulang dari guru. Pastikan jawaban sudah diperbaiki sebelum dikirim.</p>
<?php else: ?><button id="kirimBtn" class="btn btn-success btn-block mt-2" <?php echo $btnLock; ?> onclick="document.getElementById('upProg').classList.remove('d-none')"><i class="fas fa-paper-plane mr-1"></i>Kirim Jawaban</button>
<?php if ($btnLock): ?><p class="small text-danger mt-2">Tombol aktif setelah kamu memilih minimal 1 foto jawaban di atas.</p>
<?php else: ?><p class="small text-muted mt-2">Pastikan tulisan jelas sebelum dikirim. Tombol terkunci otomatis anti-klik ganda.</p><?php endif; ?><?php endif; ?>
<?php endif; ?>
</div></div>
<?php if ($needPhoto && !$locked): ?>
<script>(function(){var b=document.getElementById('kirimBtn');if(!b)return;function u(){var ok=false;var ins=document.querySelectorAll('.bp-photo');for(var i=0;i<ins.length;i++){if(ins[i].files&&ins[i].files.length){ok=true;break;}}if(!ok){var kp=document.querySelectorAll('input[name="keep_photo[]"]');for(var k=0;k<kp.length;k++){if(kp[k].checked){ok=true;break;}}}b.disabled=!ok;}var all=document.querySelectorAll('.bp-photo,input[name="keep_photo[]"]');for(var j=0;j<all.length;j++){all[j].addEventListener('change',u);}u();})();</script>
<?php endif; ?>
<?php echo form_close(); ?>
</div></div>
</div></section></div>