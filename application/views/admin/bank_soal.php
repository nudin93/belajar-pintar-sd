<!-- Pengembang: Zainudin Larau — Belajar Pintar SD -->
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col"><h1 class="m-0">Bank Soal</h1></div></div></div></div><section class="content"><div class="container-fluid">
<?php echo form_open('admin/bank_soal', array('method' => 'get', 'class' => 'form-inline mb-3')); ?><label class="mr-2">Tugas:</label><select name="assignment" class="form-control mr-2" onchange="this.form.submit()"><option value="">- pilih -</option>
<?php foreach ($asg as $a): ?><option value="<?php echo $a['id']; ?>" <?php echo $aid == $a['id'] ? 'selected' : ''; ?>><?php echo esc($a['title'] . ' (' . $a['kelas'] . '/' . $a['mapel'] . ')'); ?></option><?php endforeach; ?></select><?php echo form_close(); ?>
<?php if ($aid): ?>
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#tambahSoalModal"><i class="fas fa-plus mr-1"></i>Tambah Soal</button>
<div class="modal fade" id="tambahSoalModal" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Tambah Soal</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open('admin/bank_soal'); ?><input type="hidden" name="assignment_id" value="<?php echo $aid; ?>">
<div class="modal-body">
<p class="small text-muted">PG &amp; Benar/Salah dinilai otomatis; isian cocok dengan kunci; uraian &amp; foto dinilai manual.</p>
<div class="form-row"><div class="form-group col-md-4"><label>Tipe</label><select name="qtype" class="form-control"><option value="pg">Pilihan ganda</option><option value="bs">Benar/Salah</option><option value="isian">Isian singkat</option><option value="uraian">Uraian</option><option value="foto">Tugas foto buku</option></select></div>
<div class="form-group col-md-2"><label>Bobot</label><input name="points" type="number" class="form-control" value="10"></div>
<div class="form-group col-md-6"><label>Kunci</label><input name="answer_key" class="form-control" placeholder="Isian: jawaban benar; BS: Benar/Salah"></div></div>
<div class="form-group"><label>Teks soal</label><textarea name="qtext" class="form-control" rows="2" required></textarea></div>
<div class="form-row"><div class="col-12 small text-muted mb-1">Opsi PG (pilih yang benar):</div>
<?php for ($i = 0; $i < 4; $i++): ?><div class="form-group col-md-3"><div class="input-group"><div class="input-group-prepend"><div class="input-group-text"><input type="radio" name="correct" value="<?php echo $i; ?>"></div></div><input name="opt_text[]" class="form-control" placeholder="Opsi <?php echo chr(65 + $i); ?>"></div></div><?php endfor; ?></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Tambah Soal</button></div>
<?php echo form_close(); ?>
</div></div></div>
<?php foreach ($qs as $q): ?><div class="card"><div class="card-body">
<b>[<?php echo esc(strtoupper($q['question_type'])); ?>, <?php echo (int)$q['points']; ?> poin]</b> <?php echo esc($q['question_text']); ?>
<?php if ($q['answer_key']): ?><br><small class="text-muted">Kunci: <?php echo esc($q['answer_key']); ?></small><?php endif; ?>
<?php foreach ($q['opts'] as $o): ?><br><small><?php echo esc($o['option_label']); ?>. <?php echo esc($o['option_text']); ?> <?php echo $o['is_correct'] ? '<b>(benar)</b>' : ''; ?></small><?php endforeach; ?>
<br><a class="btn btn-sm btn-danger mt-2" href="<?php echo site_url('admin/hapus_soal/' . $aid . '/' . $q['id']); ?>" onclick="return confirm('Hapus soal?')">Hapus</a>
</div></div><?php endforeach; ?>
<?php endif; ?>
</div></section></div>