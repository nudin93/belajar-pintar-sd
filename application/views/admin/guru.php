<!-- Pengembang: Zainudin Larau — Belajar Pintar SD -->
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col"><h1 class="m-0">Data Guru</h1></div></div></div></div><section class="content"><div class="container-fluid">
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#tambahModal"><i class="fas fa-plus mr-1"></i>Tambah Guru</button>
<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Tambah Guru</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open('admin/guru'); ?><input type="hidden" name="act" value="save">
<div class="modal-body">
<div class="form-group"><label>Nama lengkap</label><input name="full_name" class="form-control" required></div>
<div class="form-row"><div class="form-group col-md-6"><label>Username</label><input name="username" class="form-control" required></div>
<div class="form-group col-md-6"><label>Password</label><input name="password" type="password" class="form-control" required></div></div>
<div class="form-row"><div class="form-group col-md-6"><label>NIP</label><input name="nip" class="form-control"></div>
<div class="form-group col-md-6"><label>No. HP</label><input name="phone" class="form-control"></div></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
<?php echo form_close(); ?>
</div></div></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-striped datatable"><thead><tr><th>Nama</th><th>Username</th><th>NIP</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?php echo esc($r['full_name']); ?></td><td><?php echo esc($r['username']); ?></td><td><?php echo esc($r['nip']); ?></td>
<td><?php echo $r['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>'; ?></td>
<td class="text-nowrap">
<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editModal<?php echo $r['id']; ?>">Edit</button>
<?php echo form_open('admin/guru', array('class' => 'd-inline')); ?><input type="hidden" name="act" value="toggle"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button class="btn btn-sm btn-secondary" title="Aktif/Nonaktif">On/Off</button><?php echo form_close(); ?>
<?php echo form_open('admin/guru', array('class' => 'd-inline')); ?><input type="hidden" name="act" value="reset"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button class="btn btn-sm btn-warning" onclick="return confirm('Reset password guru ini?')">Reset PW</button><?php echo form_close(); ?>
<?php echo form_open('admin/guru', array('class' => 'd-inline', 'onsubmit' => "return confirm('Hapus guru ini?')")); ?><input type="hidden" name="act" value="del"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button class="btn btn-sm btn-danger">Hapus</button><?php echo form_close(); ?>
<div class="modal fade" id="editModal<?php echo $r['id']; ?>" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Edit Guru</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open('admin/guru'); ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?php echo $r['id']; ?>">
<div class="modal-body">
<div class="form-group"><label>Nama lengkap</label><input name="full_name" class="form-control" value="<?php echo esc($r['full_name']); ?>" required></div>
<div class="form-row"><div class="form-group col-md-6"><label>NIP</label><input name="nip" class="form-control" value="<?php echo esc($r['nip']); ?>"></div>
<div class="form-group col-md-6"><label>No. HP</label><input name="phone" class="form-control" value="<?php echo esc($r['phone']); ?>"></div></div>
<p class="small text-muted mb-0">Username <b><?php echo esc($r['username']); ?></b> tidak dapat diubah.</p>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
<?php echo form_close(); ?>
</div></div></div>
</td></tr><?php endforeach; ?></tbody></table></div></div></div>
</div></section></div>