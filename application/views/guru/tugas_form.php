<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Buat Tugas</h1></div></div><section class="content"><div class="container-fluid"><?php if (!empty($msg)): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
<?php if (!empty($err)): ?><div class="alert alert-danger"><?php echo esc($err); ?></div><?php endif; ?>
<div class="card"><div class="card-body">
<?php echo form_open_multipart('guru/tugas_form' . ($edit ? '/' . $edit['id'] : '')); ?>
<?php if ($edit): ?><input type="hidden" name="id" value="<?php echo $edit['id']; ?>"><?php endif; ?>
<div class="form-group"><input name="title" class="form-control" placeholder="Judul" value="<?php echo esc($edit['title']); ?>" required></div>
<div class="form-group"><textarea name="instructions" class="form-control" rows="4" placeholder="Instruksi (jelaskan halaman buku yang dikerjakan & jumlah foto)"><?php echo esc($edit['instructions']); ?></textarea></div>
<div class="card"><div class="card-header"><b>Foto Soal</b> <small class="text-muted">maks 10 foto, JPG/PNG/WebP — langsung terlihat oleh siswa</small></div><div class="card-body">
<?php if (!empty($soal_photos)): ?><div class="row mb-2">
<?php foreach ($soal_photos as $sp): ?><div class="col-4 col-md-2 mb-2 text-center"><a href="<?php echo base_url('uploads/' . $sp['file_path']); ?>" target="_blank"><img class="photo-thumb" loading="lazy" src="<?php echo base_url('uploads/' . $sp['file_path']); ?>"></a><br><label class="small"><input type="checkbox" name="keep_soal[]" value="<?php echo $sp['id']; ?>" checked> Simpan</label></div><?php endforeach; ?>
</div><?php endif; ?>
<div class="form-group"><label>Ambil Foto (kamera HP)</label><input type="file" name="soal_photos[]" accept="image/*" capture="environment" class="form-control-file"></div>
<div class="form-group mb-0"><label>Pilih dari Galeri (bisa beberapa)</label><input type="file" name="soal_photos[]" accept="image/*" multiple class="form-control-file"></div>
</div></div>
<div class="card"><div class="card-header"><b>Foto Contoh Mengerjakan</b> <small class="text-muted">maks 10 foto — contoh jawaban agar murid meniru caranya</small></div><div class="card-body">
<?php if (!empty($contoh_photos)): ?><div class="row mb-2">
<?php foreach ($contoh_photos as $sp): ?><div class="col-4 col-md-2 mb-2 text-center"><a href="<?php echo base_url('uploads/' . $sp['file_path']); ?>" target="_blank"><img class="photo-thumb" loading="lazy" src="<?php echo base_url('uploads/' . $sp['file_path']); ?>"></a><br><label class="small"><input type="checkbox" name="keep_contoh[]" value="<?php echo $sp['id']; ?>" checked> Simpan</label></div><?php endforeach; ?>
</div><?php endif; ?>
<div class="form-group"><label>Ambil Foto (kamera HP)</label><input type="file" name="contoh_photos[]" accept="image/*" capture="environment" class="form-control-file"></div>
<div class="form-group mb-0"><label>Pilih dari Galeri (bisa beberapa)</label><input type="file" name="contoh_photos[]" accept="image/*" multiple class="form-control-file"></div>
</div></div>
<div class="form-row">
<div class="form-group col-md-4"><select name="class_id" class="form-control" required><option value="">- Kelas -</option><?php foreach ($kelas as $k): ?><option value="<?php echo $k['id']; ?>" <?php echo ($edit['class_id'] == $k['id']) ? 'selected' : ''; ?>><?php echo esc($k['name']); ?></option><?php endforeach; ?></select></div>
<div class="form-group col-md-4"><select name="subject_id" class="form-control" required><option value="">- Mapel -</option><?php foreach ($mapel as $m): ?><option value="<?php echo $m['id']; ?>" <?php echo ($edit['subject_id'] == $m['id']) ? 'selected' : ''; ?>><?php echo esc($m['name']); ?></option><?php endforeach; ?></select></div>
<div class="form-group col-md-4"><select name="status" class="form-control"><option value="draft">Draf</option><option value="published" <?php echo ($edit['status'] == 'published') ? 'selected' : ''; ?>>Diterbitkan</option></select></div></div>
<div class="form-row">
<div class="form-group col-md-4"><label>Mulai</label><input type="datetime-local" name="start_at" class="form-control" value="<?php echo esc(isset($edit['start_at']) ? str_replace(' ', 'T', substr($edit['start_at'], 0, 16)) : ''); ?>"></div>
<div class="form-group col-md-4"><label>Tenggat</label><input type="datetime-local" name="due_at" class="form-control" value="<?php echo esc(isset($edit['due_at']) ? str_replace(' ', 'T', substr($edit['due_at'], 0, 16)) : ''); ?>"></div>
<div class="form-group col-md-4"><label>Nilai maks</label><input type="number" name="max_score" class="form-control" value="<?php echo esc($edit['max_score']); ?>"></div></div>
<div class="form-row"><div class="form-group col-md-6"><label>Maks foto</label><input type="number" name="max_photos" min="1" max="10" class="form-control" value="<?php echo esc($edit['max_photos']); ?>"></div>
<div class="form-group col-md-6"><label>Maks MB/foto</label><input type="number" name="max_file_mb" min="1" max="10" class="form-control" value="<?php echo esc($edit['max_file_mb']); ?>"></div></div>
<label><input type="checkbox" name="allow_late" value="1" <?php echo !empty($edit['allow_late']) ? 'checked' : ''; ?>> Izinkan terlambat</label><br><br>
<button class="btn btn-primary">Simpan</button> <a class="btn btn-secondary" href="<?php echo site_url('guru/tugas'); ?>">Kembali</a><?php echo form_close(); ?></div></div>
</div></section></div>