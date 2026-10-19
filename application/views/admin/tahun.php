<!-- Pengembang: Zainudin Larau — Belajar Pintar SD -->
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col"><h1 class="m-0">Tahun Ajaran</h1></div></div></div></div><section class="content"><div class="container-fluid">
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#tambahModal"><i class="fas fa-plus mr-1"></i>Tambah Tahun</button>
<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Tambah Tahun Ajaran</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open('admin/tahun'); ?><input type="hidden" name="act" value="save">
<div class="modal-body">
<div class="form-group"><label>Tahun (mis. 2025/2026)</label><input name="name" class="form-control" required></div>
<div class="form-group"><label>Semester</label><select name="semester" class="form-control"><option value="ganjil">Ganjil</option><option value="genap">Genap</option></select></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
<?php echo form_close(); ?>
</div></div></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table"><thead><tr><th>Tahun</th><th>Semester</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?php echo esc($r['name']); ?></td><td><?php echo esc($r['semester']); ?></td>
<td><?php echo $r['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '-'; ?></td><td class="text-nowrap">
<?php echo form_open('admin/tahun', array('class' => 'd-inline')); ?><input type="hidden" name="act" value="active"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button class="btn btn-sm btn-success">Aktifkan</button><?php echo form_close(); ?>
<?php echo form_open('admin/tahun', array('class' => 'd-inline')); ?><input type="hidden" name="act" value="del"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button class="btn btn-sm btn-danger" onclick="return confirm('Hapus tahun ini?')">Hapus</button><?php echo form_close(); ?></td></tr>
<?php endforeach; ?></tbody></table></div></div></div>
</div></section></div>