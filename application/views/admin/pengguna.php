<!-- Pengembang: Zainudin Larau — Belajar Pintar SD -->
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col"><h1 class="m-0">Pengguna & Hak Akses</h1></div></div></div></div><section class="content"><div class="container-fluid">
<div class="mb-2"><a class="btn btn-sm btn-secondary" href="<?php echo site_url('admin/pengguna'); ?>">Semua</a>
<a class="btn btn-sm btn-secondary" href="<?php echo site_url('admin/pengguna?role=admin'); ?>">Admin</a>
<a class="btn btn-sm btn-secondary" href="<?php echo site_url('admin/pengguna?role=guru'); ?>">Guru</a>
<a class="btn btn-sm btn-secondary" href="<?php echo site_url('admin/pengguna?role=siswa'); ?>">Siswa</a></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-striped datatable"><thead><tr><th>Nama</th><th>Username</th><th>Peran</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?php echo esc($r['full_name']); ?></td><td><?php echo esc($r['username']); ?></td><td><span class="badge badge-info"><?php echo esc($r['role']); ?></span></td>
<td><?php echo $r['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>'; ?></td><td class="text-nowrap">
<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#roleModal<?php echo $r['id']; ?>">Peran</button>
<?php echo form_open('admin/pengguna', array('class' => 'd-inline')); ?><input type="hidden" name="act" value="toggle"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button class="btn btn-sm btn-secondary">On/Off</button><?php echo form_close(); ?>
<?php echo form_open('admin/pengguna', array('class' => 'd-inline')); ?><input type="hidden" name="act" value="reset"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button class="btn btn-sm btn-warning" onclick="return confirm('Reset password?')">Reset PW</button><?php echo form_close(); ?>
<div class="modal fade" id="roleModal<?php echo $r['id']; ?>" tabindex="-1" role="dialog"><div class="modal-dialog modal-sm" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Ubah Peran</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open('admin/pengguna'); ?><input type="hidden" name="act" value="role"><input type="hidden" name="id" value="<?php echo $r['id']; ?>">
<div class="modal-body"><p class="small"><?php echo esc($r['full_name']); ?> (<?php echo esc($r['username']); ?>)</p>
<select name="role" class="form-control"><?php foreach (array('admin', 'guru', 'siswa') as $ro): ?><option value="<?php echo $ro; ?>" <?php echo ($r['role'] == $ro) ? 'selected' : ''; ?>><?php echo $ro; ?></option><?php endforeach; ?></select></div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
<?php echo form_close(); ?>
</div></div></div>
</td></tr><?php endforeach; ?></tbody></table></div></div></div>
</div></section></div>