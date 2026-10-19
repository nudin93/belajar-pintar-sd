<!-- Pengembang: Zainudin Larau — Belajar Pintar SD -->
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col"><h1 class="m-0">Mata Pelajaran</h1></div></div></div></div><section class="content"><div class="container-fluid">
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#tambahModal"><i class="fas fa-plus mr-1"></i>Tambah Mapel</button>
<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Tambah Mapel</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open('admin/mapel'); ?><input type="hidden" name="act" value="save">
<div class="modal-body">
<div class="form-row"><div class="form-group col-md-4"><label>Kode</label><input name="code" class="form-control" placeholder="mis. MTK"></div>
<div class="form-group col-md-8"><label>Nama mapel</label><input name="name" class="form-control" required></div></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
<?php echo form_close(); ?>
</div></div></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table datatable"><thead><tr><th>Kode</th><th>Nama</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?php echo esc($r['code']); ?></td><td><?php echo esc($r['name']); ?></td>
<td><?php echo $r['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>'; ?></td><td class="text-nowrap">
<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editModal<?php echo $r['id']; ?>">Edit</button>
<?php echo form_open('admin/mapel', array('class' => 'd-inline')); ?><input type="hidden" name="act" value="del"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button class="btn btn-sm btn-danger" onclick="return confirm('Hapus mapel ini?')">Hapus</button><?php echo form_close(); ?>
<div class="modal fade" id="editModal<?php echo $r['id']; ?>" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Edit Mapel</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open('admin/mapel'); ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?php echo $r['id']; ?>">
<div class="modal-body">
<div class="form-row"><div class="form-group col-md-4"><label>Kode</label><input name="code" class="form-control" value="<?php echo esc($r['code']); ?>"></div>
<div class="form-group col-md-8"><label>Nama mapel</label><input name="name" class="form-control" value="<?php echo esc($r['name']); ?>" required></div></div>
<div class="form-check"><input type="checkbox" class="form-check-input" name="is_active" value="1" <?php echo $r['is_active'] ? 'checked' : ''; ?>><label class="form-check-label">Aktif</label></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
<?php echo form_close(); ?>
</div></div></div>
</td></tr><?php endforeach; ?></tbody></table></div></div></div>
</div></section></div>