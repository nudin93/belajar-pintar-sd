<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Bank Soal</h1></div></div><section class="content"><div class="container-fluid"><?php if (!empty($msg)): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
<?php if (!empty($err)): ?><div class="alert alert-danger"><?php echo esc($err); ?></div><?php endif; ?>
<?php echo form_open('guru/bank_soal', array('method' => 'get', 'class' => 'form-inline mb-3')); ?><select name="assignment" class="form-control mr-2" onchange="this.form.submit()"><option value="">- pilih tugas/kuis -</option>
<?php foreach ($asg as $a): ?><option value="<?php echo $a['id']; ?>" <?php echo $aid == $a['id'] ? 'selected' : ''; ?>><?php echo esc($a['title'] . ' (' . $a['kelas'] . ')'); ?></option><?php endforeach; ?></select><?php echo form_close(); ?>
<?php if ($aid): ?>
<?php if (!empty($worked)): ?><div class="alert alert-warning"><b>Tugas ini sudah dikerjakan/dinilai murid (<?php echo (int)$worked; ?>).</b> Soal baru yang ditambahkan di sini tidak bisa dikerjakan murid karena terkunci. Centang soal baru di bawah lalu tekan "Salin ke tugas baru".</div><?php endif; ?>
<div class="card card-primary"><div class="card-header"><b>Tambah pertanyaan</b> <small>(ala Google Form)</small></div><div class="card-body">
<?php echo form_open('guru/bank_soal?assignment=' . $aid, array('id' => 'soalForm')); ?><input type="hidden" name="assignment_id" value="<?php echo $aid; ?>">
<div class="form-row">
<div class="form-group col-md-8"><textarea name="qtext" class="form-control" rows="2" placeholder="Tulis pertanyaan di sini..." required></textarea></div>
<div class="form-group col-md-2"><select name="qtype" id="qtype" class="form-control" onchange="gantiTipe()">
<option value="pg">Pilihan ganda</option><option value="bs">Benar / Salah</option><option value="isian">Isian singkat</option><option value="uraian">Uraian</option><option value="foto">Tugas foto buku</option></select></div>
<div class="form-group col-md-2"><input name="points" type="number" min="1" class="form-control" value="10" title="Poin"></div>
</div>
<div class="form-row"><div class="form-group col-md-6"><label>Masuk ke bagian</label><select name="section_id" class="form-control"><option value="0">- Umum (tanpa bagian) -</option><?php foreach ($secs as $s): ?><option value="<?php echo $s['id']; ?>">Bagian: <?php echo esc($s['title']); ?></option><?php endforeach; ?></select></div><div class="form-group col-md-6"><label>&nbsp;</label><p class="form-text small text-muted">Soal baru masuk bagian terpilih. Buat bagian baru dulu pada kartu "Bagian Soal" bila perlu.</p></div></div>
<div id="pgBox">
<div id="optList">
<?php for ($i = 0; $i < 4; $i++): ?><div class="input-group mb-2 opt-row"><div class="input-group-prepend"><div class="input-group-text" title="Jawaban benar"><input type="radio" name="correct" value="<?php echo $i; ?>" <?php echo $i === 0 ? 'checked' : ''; ?>></div></div><input name="opt_text[]" class="form-control" placeholder="Opsi <?php echo chr(65 + $i); ?>"><div class="input-group-append"><button type="button" class="btn btn-outline-danger" onclick="hapusOpsi(this)" title="Hapus opsi">&times;</button></div></div><?php endfor; ?>
</div>
<button type="button" class="btn btn-sm btn-outline-primary mb-2" onclick="tambahOpsi()">+ Tambah opsi</button>
<p class="small text-muted mb-0">Bulatkan radio pada opsi yang benar. Nilai otomatis dikoreksi sistem.</p>
</div>
<div id="bsBox" hidden>
<label class="mr-3"><input type="radio" name="answer_key" value="Benar" checked> Benar</label>
<label><input type="radio" name="answer_key" value="Salah"> Salah</label>
<p class="small text-muted">Pilih jawaban yang benar.</p>
</div>
<div id="isianBox" hidden>
<input name="answer_key" id="isianKey" class="form-control" placeholder="Kunci jawaban (pisahkan alternatif dengan | )">
<p class="small text-muted mb-0">Contoh: <code>jakarta|DKI Jakarta</code> — tidak peka huruf besar/kecil.</p>
</div>
<p class="small text-muted" id="manualNote" hidden>Soal ini dikoreksi manual oleh guru (nilai + komentar saat pemeriksaan).</p>
<button class="btn btn-primary">Tambah Soal</button><?php echo form_close(); ?></div></div>
<div class="card"><div class="card-header"><b>Bagian Soal</b> <small class="text-muted">pisahkan tiap tambahan soal agar murid mengisi per bagian</small></div><div class="card-body">
<?php echo form_open('guru/bank_soal?assignment=' . $aid, array('class' => 'form-inline mb-2')); ?><input type="hidden" name="assignment_id" value="<?php echo $aid; ?>"><input type="hidden" name="act" value="sec_add"><input name="sectitle" class="form-control mr-2" placeholder="Nama bagian baru (mis. Latihan 2)"><button class="btn btn-outline-primary">+ Bagian</button><?php echo form_close(); ?>
<?php if ($secs): ?><ul class="list-group">
<?php foreach ($secs as $s): ?><li class="list-group-item"><b><?php echo esc($s['title']); ?></b>
<?php echo form_open('guru/bank_soal?assignment=' . $aid, array('style' => 'display:inline')); ?><input type="hidden" name="assignment_id" value="<?php echo $aid; ?>"><input type="hidden" name="act" value="sec_del"><input type="hidden" name="id" value="<?php echo $s['id']; ?>"><button class="btn btn-sm btn-outline-danger float-right" onclick="return confirm('Hapus bagian ini beserta semua soalnya?')">Hapus</button><?php echo form_close(); ?></li><?php endforeach; ?>
</ul><?php else: ?><p class="small text-muted mb-0">Belum ada bagian — semua soal masuk "Umum".</p><?php endif; ?>
</div></div>
<?php $nq = 0; ?>
<?php echo form_open('guru/bank_soal?assignment=' . $aid); ?><input type="hidden" name="assignment_id" value="<?php echo $aid; ?>"><input type="hidden" name="act" value="copy_new"><button class="btn btn-dark mb-2"><i class="fas fa-copy mr-1"></i>Salin yang dicentang ke tugas baru</button>
<?php foreach ($groups as $g): ?>
<?php if (!empty($g['section'])): ?><div class="alert alert-primary py-2 mb-2"><b>Bagian: <?php echo esc($g['section']['title']); ?></b> <span class="small">(<?php echo count($g['qs']); ?> soal)</span></div><?php endif; ?>
<?php foreach ($g['qs'] as $q): $nq++; ?><div class="card"><div class="card-body">
<span class="badge badge-dark">#<?php echo $nq; ?></span>
<input type="checkbox" name="qids[]" value="<?php echo $q['id']; ?>" title="Centang untuk disalin ke tugas baru">
<span class="badge badge-info"><?php echo esc($q['question_type']); ?></span>
<span class="badge badge-secondary"><?php echo (int)$q['points']; ?> poin</span>
<a class="btn btn-sm btn-danger float-right" href="<?php echo site_url('guru/bank_soal?assignment=' . $aid . '&del=' . $q['id']); ?>" onclick="return confirm('Hapus soal ini?')">Hapus</a>
<p class="mt-2 mb-1"><b><?php echo esc($q['question_text']); ?></b></p>
<?php if (!empty($q['opts'])): ?><ul class="small mb-0"><?php foreach ($q['opts'] as $o): ?><li><?php echo esc($o['option_label'] . '. ' . $o['option_text']); ?><?php echo ((int)$o['is_correct'] === 1) ? ' <i class="fas fa-check text-success"></i>' : ''; ?></li><?php endforeach; ?></ul>
<?php elseif ($q['answer_key'] !== null && $q['answer_key'] !== ''): ?><p class="small text-muted mb-0">Kunci: <code><?php echo esc($q['answer_key']); ?></code></p>
<?php endif; ?>
</div></div><?php endforeach; ?><?php endforeach; ?>
<button class="btn btn-dark mb-3"><i class="fas fa-copy mr-1"></i>Salin yang dicentang ke tugas baru</button><?php echo form_close(); ?>
<?php endif; ?>
</div></section></div>
<script>
function gantiTipe() {
  var t = document.getElementById('qtype').value;
  document.getElementById('pgBox').hidden = (t !== 'pg');
  document.getElementById('bsBox').hidden = (t !== 'bs');
  document.getElementById('isianBox').hidden = (t !== 'isian');
  document.getElementById('manualNote').hidden = !(t === 'uraian' || t === 'foto');
  var ik = document.getElementById('isianKey');
  if (ik) ik.disabled = (t !== 'isian');
  var bs = document.querySelectorAll('#bsBox input[name="answer_key"]');
  for (var i = 0; i < bs.length; i++) bs[i].disabled = (t !== 'bs');
}
function labelOpsi(i) { return String.fromCharCode(65 + i); }
function susunUlangOpsi() {
  var rows = document.querySelectorAll('#optList .opt-row');
  for (var i = 0; i < rows.length; i++) {
    rows[i].querySelector('input[type="radio"]').value = i;
    rows[i].querySelector('input[name="opt_text[]"]').placeholder = 'Opsi ' + labelOpsi(i);
  }
}
function tambahOpsi() {
  var box = document.getElementById('optList');
  if (box.querySelectorAll('.opt-row').length >= 10) return;
  var d = document.createElement('div');
  d.className = 'input-group mb-2 opt-row';
  d.innerHTML = '<div class="input-group-prepend"><div class="input-group-text" title="Jawaban benar"><input type="radio" name="correct" value="0"></div></div><input name="opt_text[]" class="form-control" placeholder="Opsi"><div class="input-group-append"><button type="button" class="btn btn-outline-danger" onclick="hapusOpsi(this)" title="Hapus opsi">&times;</button></div>';
  box.appendChild(d);
  susunUlangOpsi();
}
function hapusOpsi(btn) {
  var box = document.getElementById('optList');
  if (box.querySelectorAll('.opt-row').length <= 2) { alert('Minimal 2 opsi.'); return; }
  var row = btn.closest('.opt-row');
  var wasChecked = row.querySelector('input[type="radio"]').checked;
  row.remove();
  if (wasChecked) box.querySelector('input[type="radio"]').checked = true;
  susunUlangOpsi();
}
gantiTipe();
</script>
