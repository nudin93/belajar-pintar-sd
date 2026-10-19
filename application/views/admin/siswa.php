<!-- Pengembang: Zainudin Larau — Belajar Pintar SD -->
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><div class="row mb-2"><div class="col"><h1 class="m-0">Data Siswa</h1></div></div></div></div><section class="content"><div class="container-fluid">
<div class="mb-3">
<button class="btn btn-primary mr-2" data-toggle="modal" data-target="#tambahModal"><i class="fas fa-plus mr-1"></i>Tambah Siswa</button>
<button class="btn btn-secondary mr-2" data-toggle="modal" data-target="#imporModal"><i class="fas fa-file-import mr-1"></i>Impor CSV/XLSX</button>
<?php echo form_open('admin/siswa', array('id' => 'bulkForm', 'class' => 'd-inline')); ?><input type="hidden" name="act" value="bulkdel"><?php echo form_close(); ?>
<button class="btn btn-danger" form="bulkForm" onclick="return confirm('Hapus SEMUA siswa yang dicentang? Data nilai & jawabannya ikut terhapus.')"><i class="fas fa-trash mr-1"></i>Hapus Centang</button>
</div>
<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Tambah Siswa</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open('admin/siswa'); ?><input type="hidden" name="act" value="save">
<div class="modal-body">
<div class="form-group"><label>Nama lengkap</label><input name="full_name" class="form-control" required></div>
<div class="form-row"><div class="form-group col-md-6"><label>Username</label><input name="username" class="form-control" required></div>
<div class="form-group col-md-6"><label>Password</label><input name="password" type="password" class="form-control" required></div></div>
<div class="form-row"><div class="form-group col-md-6"><label>NIS</label><input name="nis" class="form-control"></div>
<div class="form-group col-md-6"><label>NIK (16 digit)</label><input name="nik" class="form-control" maxlength="20"></div></div>
<div class="form-row"><div class="form-group col-md-6"><label>Nama Ibu Kandung</label><input name="nama_ibu" class="form-control" maxlength="150"></div>
<div class="form-group col-md-6"><label>Kelas</label><select name="class_id" class="form-control"><option value="">- Pilih -</option><?php foreach ($kelas as $k): ?><option value="<?php echo $k['id']; ?>"><?php echo esc($k['name']); ?></option><?php endforeach; ?></select></div></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
<?php echo form_close(); ?>
</div></div></div>
<div class="modal fade" id="imporModal" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Impor Siswa (CSV / Excel Dapodik)</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open_multipart('admin/siswa'); ?><input type="hidden" name="act" value="import">
<div class="modal-body">
<div class="form-group"><label>File (.csv atau .xlsx, maks 5 MB)</label><input type="file" name="upfile" accept=".csv,.xlsx" class="form-control-file" required></div>
<div class="form-group"><label>Password awal akun baru</label><input name="pw_awal" class="form-control" value="Siswa1234" required minlength="6"></div>
<p class="small text-muted mb-0">CSV: per baris <code>nama,username,password</code>.<br>
XLSX Dapodik (Lampiran Peserta Didik): kolom <code>Nama</code>, <code>L/P</code>, <code>Tgl Lahir</code>, <code>NISN</code>, <code>Tingkat</code>/<code>Kelas</code> terbaca otomatis (baris lanjutan/wrap digabung). Username = NISN (bila valid) atau nama; siswa ditempatkan ke kelas sesuai Tingkat (kelas dibuat otomatis bila belum ada). Duplikat dilewati.</p>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Impor</button></div>
<?php echo form_close(); ?>
</div></div></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-striped datatable"><thead><tr><th><input type="checkbox" id="cekSemua" title="Centang semua"></th><th>Nama</th><th>Username</th><th>NIS</th><th>NISN</th><th>NIK</th><th>Ibu</th><th>Kelas</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><input type="checkbox" class="cekSiswa" name="ids[]" value="<?php echo $r['id']; ?>" form="bulkForm"></td><td><?php echo esc($r['full_name']); ?></td><td><?php echo esc($r['username']); ?></td><td><?php echo esc($r['nis']); ?></td><td><?php echo esc(isset($r['nisn']) ? $r['nisn'] : ''); ?></td><td class="small"><?php echo esc(isset($r['nik']) ? $r['nik'] : ''); ?></td><td class="small"><?php echo esc(isset($r['nama_ibu']) ? $r['nama_ibu'] : ''); ?></td><td><?php echo esc($r['kelas']); ?></td>
<td><?php echo $r['is_active'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>'; ?></td>
<td class="text-nowrap">
<button class="btn btn-sm btn-info" data-toggle="modal" data-target="#editModal<?php echo $r['id']; ?>">Edit</button>
<?php echo form_open('admin/siswa', array('class' => 'd-inline')); ?><input type="hidden" name="act" value="toggle"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button class="btn btn-sm btn-secondary">On/Off</button><?php echo form_close(); ?>
<?php echo form_open('admin/siswa', array('class' => 'd-inline')); ?><input type="hidden" name="act" value="reset"><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><button class="btn btn-sm btn-warning" onclick="return confirm('Reset password siswa ini?')">Reset PW</button><?php echo form_close(); ?>
<div class="modal fade" id="editModal<?php echo $r['id']; ?>" tabindex="-1" role="dialog"><div class="modal-dialog" role="document"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Edit Siswa</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
<?php echo form_open('admin/siswa'); ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?php echo $r['id']; ?>">
<div class="modal-body">
<div class="form-group"><label>Nama lengkap</label><input name="full_name" class="form-control" value="<?php echo esc($r['full_name']); ?>" required></div>
<div class="form-row"><div class="form-group col-md-6"><label>NIS</label><input name="nis" class="form-control" value="<?php echo esc($r['nis']); ?>"></div>
<div class="form-group col-md-6"><label>NIK</label><input name="nik" class="form-control" maxlength="20" value="<?php echo esc(isset($r['nik']) ? $r['nik'] : ''); ?>"></div></div>
<div class="form-row"><div class="form-group col-md-6"><label>Nama Ibu Kandung</label><input name="nama_ibu" class="form-control" maxlength="150" value="<?php echo esc(isset($r['nama_ibu']) ? $r['nama_ibu'] : ''); ?>"></div>
<div class="form-group col-md-6"><label>Pindah kelas</label><select name="class_id" class="form-control"><option value="">- Tidak pindah -</option><?php foreach ($kelas as $k): ?><option value="<?php echo $k['id']; ?>" <?php echo ($r['kelas'] == $k['name']) ? 'selected' : ''; ?>><?php echo esc($k['name']); ?></option><?php endforeach; ?></select></div></div>
<p class="small text-muted mb-0">Username <b><?php echo esc($r['username']); ?></b> tidak dapat diubah.</p>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
<?php echo form_close(); ?>
</div></div></div>
</td></tr><?php endforeach; ?></tbody></table></div></div></div>
</div></section></div>
<script>
document.getElementById('cekSemua').addEventListener('change', function () {
  var boxes = document.querySelectorAll('.cekSiswa');
  for (var i = 0; i < boxes.length; i++) boxes[i].checked = this.checked;
});
</script>