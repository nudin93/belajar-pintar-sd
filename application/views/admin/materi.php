<!-- Pengembang: Zainudin Larau — Belajar Pintar SD -->
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col"><h1 class="m-0">Materi Pembelajaran</h1></div></div></div></div><section class="content"><div class="container-fluid">
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#tambahModal"><i class="fas fa-plus mr-1"></i>Tambah Materi</button>
<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Tambah Materi</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open_multipart('admin/materi'); ?><input type="hidden" name="act" value="save">
<div class="modal-body">
<div class="form-row"><div class="form-group col-md-6"><label>Judul</label><input name="title" class="form-control" required></div>
<div class="form-group col-md-3"><label>Kelas</label><select name="class_id" class="form-control"><option value="">- Semua -</option><?php foreach ($kelas as $k): ?><option value="<?php echo $k['id']; ?>"><?php echo esc($k['name']); ?></option><?php endforeach; ?></select></div>
<div class="form-group col-md-3"><label>Mapel</label><select name="subject_id" class="form-control"><option value="">- Pilih -</option><?php foreach ($mapel as $m): ?><option value="<?php echo $m['id']; ?>"><?php echo esc($m['name']); ?></option><?php endforeach; ?></select></div></div>
<div class="form-row"><div class="form-group col-md-4"><label>Topik/Bab</label><input name="topic" class="form-control"></div>
<div class="form-group col-md-4"><label>Link video</label><input name="video_url" class="form-control"></div>
<div class="form-group col-md-4"><label>Status</label><select name="status" class="form-control"><option value="draft">Draf</option><option value="published">Diterbitkan</option></select></div></div>
<div class="form-group"><label>Deskripsi</label><textarea name="description" class="form-control" rows="2"></textarea></div>
<div class="form-group"><label>Isi materi / latihan</label><textarea name="content" class="form-control" rows="3"></textarea></div>
<div class="form-row"><div class="form-group col-md-6"><label>Gambar</label><input type="file" name="image" accept="image/*" class="form-control-file"></div>
<div class="form-group col-md-6"><label>PDF</label><input type="file" name="pdf" accept="application/pdf" class="form-control-file"></div></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
<?php echo form_close(); ?>
</div></div></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table datatable"><thead><tr><th>Judul</th><th>Kelas</th><th>Mapel</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?php echo esc($r['title']); ?></td><td><?php echo esc($r['kelas']); ?></td><td><?php echo esc($r['mapel']); ?></td><td><?php echo status_label($r['status']); ?></td><td class="text-nowrap">
<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editModal<?php echo $r['id']; ?>">Edit</button>
<?php echo form_open('admin/materi', array('class' => 'd-inline')); ?><input type="hidden" name="act" value="pub"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button class="btn btn-sm btn-success">Draf/Publikasi</button><?php echo form_close(); ?>
<?php echo form_open('admin/materi', array('class' => 'd-inline')); ?><input type="hidden" name="act" value="del"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button class="btn btn-sm btn-danger" onclick="return confirm('Hapus materi ini?')">Hapus</button><?php echo form_close(); ?>
<div class="modal fade" id="editModal<?php echo $r['id']; ?>" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Edit Materi</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open_multipart('admin/materi'); ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?php echo $r['id']; ?>">
<div class="modal-body">
<div class="form-row"><div class="form-group col-md-6"><label>Judul</label><input name="title" class="form-control" value="<?php echo esc($r['title']); ?>" required></div>
<div class="form-group col-md-3"><label>Kelas</label><select name="class_id" class="form-control"><option value="">- Semua -</option><?php foreach ($kelas as $k): ?><option value="<?php echo $k['id']; ?>" <?php echo ($r['class_id'] == $k['id']) ? 'selected' : ''; ?>><?php echo esc($k['name']); ?></option><?php endforeach; ?></select></div>
<div class="form-group col-md-3"><label>Mapel</label><select name="subject_id" class="form-control"><option value="">- Pilih -</option><?php foreach ($mapel as $m): ?><option value="<?php echo $m['id']; ?>" <?php echo ($r['subject_id'] == $m['id']) ? 'selected' : ''; ?>><?php echo esc($m['name']); ?></option><?php endforeach; ?></select></div></div>
<div class="form-row"><div class="form-group col-md-4"><label>Topik/Bab</label><input name="topic" class="form-control" value="<?php echo esc($r['topic']); ?>"></div>
<div class="form-group col-md-4"><label>Link video</label><input name="video_url" class="form-control" value="<?php echo esc($r['video_url']); ?>"></div>
<div class="form-group col-md-4"><label>Status</label><select name="status" class="form-control"><option value="draft" <?php echo ($r['status'] == 'draft') ? 'selected' : ''; ?>>Draf</option><option value="published" <?php echo ($r['status'] == 'published') ? 'selected' : ''; ?>>Diterbitkan</option></select></div></div>
<div class="form-group"><label>Deskripsi</label><textarea name="description" class="form-control" rows="2"><?php echo esc($r['description']); ?></textarea></div>
<div class="form-group"><label>Isi materi / latihan</label><textarea name="content" class="form-control" rows="3"><?php echo esc($r['content']); ?></textarea></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
<?php echo form_close(); ?>
</div></div></div>
</td></tr><?php endforeach; ?></tbody></table></div></div></div>
</div></section></div>