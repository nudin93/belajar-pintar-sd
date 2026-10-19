<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Data Sekolah</h1></div></div><section class="content"><div class="container-fluid"><?php if (!empty($msg)): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
<?php if (!empty($err)): ?><div class="alert alert-danger"><?php echo esc($err); ?></div><?php endif; ?>
<div class="card col-md-8"><div class="card-body">
<?php echo form_open('admin/sekolah'); ?>
<div class="form-group"><label>Nama sekolah</label><input name="name" class="form-control" value="<?php echo esc($sch['name']); ?>" required></div>
<div class="form-row"><div class="form-group col"><label>NPSN</label><input name="npsn" class="form-control" value="<?php echo esc($sch['npsn']); ?>"></div>
<div class="form-group col"><label>Kepala sekolah</label><input name="principal" class="form-control" value="<?php echo esc($sch['principal']); ?>"></div></div>
<div class="form-group"><label>Alamat</label><textarea name="address" class="form-control"><?php echo esc($sch['address']); ?></textarea></div>
<div class="form-row"><div class="form-group col"><label>Telepon</label><input name="phone" class="form-control" value="<?php echo esc($sch['phone']); ?>"></div>
<div class="form-group col"><label>Email</label><input name="email" class="form-control" value="<?php echo esc($sch['email']); ?>"></div></div>
<div class="form-group"><label>Tahun ajaran</label><select name="academic_year_id" class="form-control"><option value="">-</option>
<?php foreach ($years as $y): ?><option value="<?php echo $y['id']; ?>" <?php echo ($sch['academic_year_id'] == $y['id']) ? 'selected' : ''; ?>><?php echo esc($y['name'] . ' - ' . $y['semester']); ?></option><?php endforeach; ?></select></div>
<button class="btn btn-primary">Simpan</button><?php echo form_close(); ?></div></div>
</div></section></div>