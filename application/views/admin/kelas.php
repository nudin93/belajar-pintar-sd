<!-- Pengembang: Zainudin Larau — Belajar Pintar SD -->
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col"><h1 class="m-0">Data Kelas <small class="text-muted">mis. 1A, 1B</small></h1></div></div></div></div><section class="content"><div class="container-fluid">
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#tambahModal"><i class="fas fa-plus mr-1"></i>Tambah Kelas</button>
<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Tambah Kelas</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open('admin/kelas'); ?><input type="hidden" name="act" value="save">
<div class="modal-body">
<div class="form-group"><label>Nama kelas (mis. 1A)</label><input name="name" class="form-control" required></div>
<div class="form-row"><div class="form-group col-md-6"><label>Tingkat</label><select name="grade_level" class="form-control"><?php for ($i = 1; $i <= 6; $i++): ?><option value="<?php echo $i; ?>">Kelas <?php echo $i; ?></option><?php endfor; ?></select></div>
<div class="form-group col-md-6"><label>Wali kelas</label><select name="homeroom" class="form-control"><option value="">- Pilih -</option><?php foreach ($teachers as $t): ?><option value="<?php echo $t['id']; ?>"><?php echo esc($t['full_name']); ?></option><?php endforeach; ?></select></div></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
<?php echo form_close(); ?>
</div></div></div>
<?php foreach ($rows as $r): ?>
<div class="card"><div class="card-header d-flex flex-wrap justify-content-between align-items-center"><b><?php echo esc($r['name']); ?> <small class="text-muted">(Tingkat <?php echo (int)$r['grade_level']; ?> &middot; <?php echo count($r['members']); ?> siswa)</small></b>
<span class="mt-1"><button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editModal<?php echo $r['id']; ?>">Edit</button>
<button class="btn btn-sm btn-success" data-toggle="modal" data-target="#anggotaModal<?php echo $r['id']; ?>">Anggota</button>
<?php echo form_open('admin/kelas', array('class' => 'd-inline')); ?><input type="hidden" name="act" value="del"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button class="btn btn-sm btn-danger" onclick="return confirm('Hapus kelas <?php echo esc($r['name']); ?>?')">Hapus</button><?php echo form_close(); ?></span></div>
<div class="modal fade" id="editModal<?php echo $r['id']; ?>" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Edit Kelas</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open('admin/kelas'); ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?php echo $r['id']; ?>">
<div class="modal-body">
<div class="form-group"><label>Nama kelas</label><input name="name" class="form-control" value="<?php echo esc($r['name']); ?>" required></div>
<div class="form-row"><div class="form-group col-md-6"><label>Tingkat</label><select name="grade_level" class="form-control"><?php for ($i = 1; $i <= 6; $i++): ?><option value="<?php echo $i; ?>" <?php echo ($r['grade_level'] == $i) ? 'selected' : ''; ?>>Kelas <?php echo $i; ?></option><?php endfor; ?></select></div>
<div class="form-group col-md-6"><label>Wali kelas</label><select name="homeroom" class="form-control"><option value="">- Pilih -</option><?php foreach ($teachers as $t): ?><option value="<?php echo $t['id']; ?>" <?php echo ($r['homeroom_teacher_id'] == $t['id']) ? 'selected' : ''; ?>><?php echo esc($t['full_name']); ?></option><?php endforeach; ?></select></div></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
<?php echo form_close(); ?>
</div></div></div>
<div class="modal fade" id="anggotaModal<?php echo $r['id']; ?>" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Anggota <?php echo esc($r['name']); ?></h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open('admin/kelas'); ?><input type="hidden" name="act" value="member"><input type="hidden" name="id" value="<?php echo $r['id']; ?>">
<div class="modal-body"><div class="row" style="max-height:300px;overflow:auto"><?php foreach ($allS as $s): ?><div class="col-md-4 col-6"><label class="small"><input type="checkbox" name="students[]" value="<?php echo $s['id']; ?>" <?php echo in_array((int)$s['id'], $r['members']) ? 'checked' : ''; ?>> <?php echo esc($s['full_name']); ?></label></div><?php endforeach; ?></div></div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-success">Simpan Anggota</button></div>
<?php echo form_close(); ?>
</div></div></div>
</div>
<?php endforeach; ?>
</div></section></div>