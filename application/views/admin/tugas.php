<!-- Pengembang: Zainudin Larau — Belajar Pintar SD -->
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col"><h1 class="m-0">Tugas & Ujian</h1></div></div></div></div><section class="content"><div class="container-fluid">
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#tambahModal"><i class="fas fa-plus mr-1"></i>Buat Tugas</button>
<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Buat Tugas</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open('admin/tugas'); ?><input type="hidden" name="act" value="save">
<div class="modal-body">
<div class="form-group"><label>Judul tugas</label><input name="title" class="form-control" required></div>
<div class="form-group"><label>Instruksi untuk siswa</label><textarea name="instructions" class="form-control" rows="3"></textarea></div>
<div class="form-row">
<div class="form-group col-md-3"><label>Kelas</label><select name="class_id" class="form-control" required><option value="">- Kelas -</option><?php foreach ($kelas as $k): ?><option value="<?php echo $k['id']; ?>"><?php echo esc($k['name']); ?></option><?php endforeach; ?></select></div>
<div class="form-group col-md-3"><label>Mapel</label><select name="subject_id" class="form-control" required><option value="">- Mapel -</option><?php foreach ($mapel as $m): ?><option value="<?php echo $m['id']; ?>"><?php echo esc($m['name']); ?></option><?php endforeach; ?></select></div>
<div class="form-group col-md-3"><label>Guru</label><select name="teacher_id" class="form-control" required><option value="">- Guru -</option><?php foreach ($guru as $g): ?><option value="<?php echo $g['id']; ?>"><?php echo esc($g['full_name']); ?></option><?php endforeach; ?></select></div>
<div class="form-group col-md-3"><label>Status</label><select name="status" class="form-control"><option value="draft">Draf</option><option value="published">Diterbitkan</option><option value="closed">Ditutup</option></select></div></div>
<div class="form-row">
<div class="form-group col-md-4"><label>Mulai</label><input type="datetime-local" name="start_at" class="form-control"></div>
<div class="form-group col-md-4"><label>Tenggat</label><input type="datetime-local" name="due_at" class="form-control"></div>
<div class="form-group col-md-4"><label>Nilai maks</label><input type="number" name="max_score" class="form-control" value="100"></div></div>
<div class="form-row"><div class="form-group col-md-6"><label>Maks foto</label><input type="number" name="max_photos" min="1" max="10" class="form-control" value="5"></div>
<div class="form-group col-md-6"><label>Maks MB/foto</label><input type="number" name="max_file_mb" min="1" max="10" class="form-control" value="5"></div></div>
<div class="form-check"><input type="checkbox" class="form-check-input" name="allow_late" value="1"><label class="form-check-label">Izinkan terlambat</label></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
<?php echo form_close(); ?>
</div></div></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table datatable"><thead><tr><th>Judul</th><th>Kelas</th><th>Mapel</th><th>Tenggat</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?php echo esc($r['title']); ?><br><small class="text-muted"><?php echo esc($r['guru']); ?></small></td><td><?php echo esc($r['kelas']); ?></td><td><?php echo esc($r['mapel']); ?></td><td class="small"><?php echo esc(fmt_tgl($r['due_at'])); ?></td><td><?php echo status_label($r['status']); ?></td><td class="text-nowrap">
<a class="btn btn-sm btn-info" href="<?php echo site_url('admin/bank_soal?assignment=' . $r['id']); ?>">Soal</a>
<button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#editModal<?php echo $r['id']; ?>">Edit</button>
<?php echo form_open('admin/tugas', array('class' => 'd-inline')); ?><input type="hidden" name="act" value="del"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button class="btn btn-sm btn-danger" onclick="return confirm('Hapus tugas + jawaban?')">Hapus</button><?php echo form_close(); ?>
<div class="modal fade" id="editModal<?php echo $r['id']; ?>" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Edit Tugas</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open('admin/tugas'); ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?php echo $r['id']; ?>">
<div class="modal-body">
<div class="form-group"><label>Judul tugas</label><input name="title" class="form-control" value="<?php echo esc($r['title']); ?>" required></div>
<div class="form-group"><label>Instruksi untuk siswa</label><textarea name="instructions" class="form-control" rows="3"><?php echo esc($r['instructions']); ?></textarea></div>
<div class="form-row">
<div class="form-group col-md-3"><label>Kelas</label><select name="class_id" class="form-control" required><?php foreach ($kelas as $k): ?><option value="<?php echo $k['id']; ?>" <?php echo ($r['class_id'] == $k['id']) ? 'selected' : ''; ?>><?php echo esc($k['name']); ?></option><?php endforeach; ?></select></div>
<div class="form-group col-md-3"><label>Mapel</label><select name="subject_id" class="form-control" required><?php foreach ($mapel as $m): ?><option value="<?php echo $m['id']; ?>" <?php echo ($r['subject_id'] == $m['id']) ? 'selected' : ''; ?>><?php echo esc($m['name']); ?></option><?php endforeach; ?></select></div>
<div class="form-group col-md-3"><label>Guru</label><select name="teacher_id" class="form-control" required><?php foreach ($guru as $g): ?><option value="<?php echo $g['id']; ?>" <?php echo ($r['teacher_id'] == $g['id']) ? 'selected' : ''; ?>><?php echo esc($g['full_name']); ?></option><?php endforeach; ?></select></div>
<div class="form-group col-md-3"><label>Status</label><select name="status" class="form-control"><option value="draft" <?php echo ($r['status'] == 'draft') ? 'selected' : ''; ?>>Draf</option><option value="published" <?php echo ($r['status'] == 'published') ? 'selected' : ''; ?>>Diterbitkan</option><option value="closed" <?php echo ($r['status'] == 'closed') ? 'selected' : ''; ?>>Ditutup</option></select></div></div>
<div class="form-row">
<div class="form-group col-md-4"><label>Mulai</label><input type="datetime-local" name="start_at" class="form-control" value="<?php echo esc($r['start_at'] ? str_replace(' ', 'T', substr($r['start_at'], 0, 16)) : ''); ?>"></div>
<div class="form-group col-md-4"><label>Tenggat</label><input type="datetime-local" name="due_at" class="form-control" value="<?php echo esc($r['due_at'] ? str_replace(' ', 'T', substr($r['due_at'], 0, 16)) : ''); ?>"></div>
<div class="form-group col-md-4"><label>Nilai maks</label><input type="number" name="max_score" class="form-control" value="<?php echo esc($r['max_score']); ?>"></div></div>
<div class="form-row"><div class="form-group col-md-6"><label>Maks foto</label><input type="number" name="max_photos" min="1" max="10" class="form-control" value="<?php echo esc($r['max_photos']); ?>"></div>
<div class="form-group col-md-6"><label>Maks MB/foto</label><input type="number" name="max_file_mb" min="1" max="10" class="form-control" value="<?php echo esc($r['max_file_mb']); ?>"></div></div>
<div class="form-check"><input type="checkbox" class="form-check-input" name="allow_late" value="1" <?php echo !empty($r['allow_late']) ? 'checked' : ''; ?>><label class="form-check-label">Izinkan terlambat</label></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
<?php echo form_close(); ?>
</div></div></div>
</td></tr><?php endforeach; ?></tbody></table></div></div></div>
</div></section></div>