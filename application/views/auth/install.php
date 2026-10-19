<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
$info = isset($info) ? $info : array();
$checks = isset($checks) ? $checks : array();
$stage = isset($stage) ? $stage : 'db';
$cfg = isset($info['cfg']) && is_array($info['cfg']) ? $info['cfg'] : array('hostname' => 'localhost', 'username' => '', 'password' => '', 'database' => '');
foreach (array('hostname','username','password','database') as $k) if (!isset($cfg[$k])) $cfg[$k] = '';
if (strpos($cfg['username'], 'ISI_') === 0) $cfg['username'] = '';
if (strpos($cfg['database'], 'ISI_') === 0) $cfg['database'] = '';
$cfg['password'] = '';
?>
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Instalasi | Belajar Pintar SD</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"></head>
<body class="bg-light"><div class="container py-4"><div class="col-lg-7 mx-auto">
<div class="card shadow"><div class="card-body">
<h3>Instalasi Belajar Pintar SD</h3>
<p class="text-muted">Ikuti 3 langkah di bawah. Setelah admin dibuat, <b>hapus file <code>application/controllers/Install.php</code></b> via File Manager.</p>
<?php if (!empty($err)): ?><div class="alert alert-danger"><?php echo esc($err); ?></div><?php endif; ?>
<?php if (!empty($ok)): ?><div class="alert alert-success"><?php echo esc($ok); ?></div><?php endif; ?>

<h5>Diagnosis</h5>
<table class="table table-sm table-bordered">
<?php foreach ($checks as $c): ?>
<tr><td><?php echo esc($c['label']); ?></td>
<td class="text-center" style="width:90px"><?php
if (!empty($c['neutral'])) echo '<span class="badge badge-secondary">-</span>';
elseif (!empty($c['ok'])) echo '<span class="badge badge-success">OK</span>';
else echo '<span class="badge badge-danger">BELUM</span>';
if (!empty($c['detail'])) echo '<div class="small text-muted">' . esc($c['detail']) . '</div>';
?></td></tr>
<?php endforeach; ?>
</table>

<?php if ($stage === 'locked'): ?>
<div class="alert alert-warning">Installer <b>terkunci</b> karena sudah ada pengguna. Hapus file <code>application/controllers/Install.php</code> sekarang, lalu <a class="btn btn-primary btn-sm" href="<?php echo site_url('auth'); ?>">Ke Login</a></div>
<?php endif; ?>

<?php if ($stage === 'db' || !empty($err) && $stage !== 'locked'): ?>
<h5 class="mt-4">Langkah 1 — Kredensial database cPanel</h5>
<p class="small text-muted">Buat dulu database + user di cPanel (MySQL Databases, beri hak penuh), lalu isi di sini. Installer akan menyimpan ke <code>application/config/database.php</code>. Jika form ini gagal tersimpan (permission), isi manual via File Manager lalu refresh halaman ini.</p>
<?php echo form_open('install'); ?><input type="hidden" name="step" value="db_save">
<div class="form-group"><label>Hostname (biasanya <code>localhost</code>)</label><input name="hostname" class="form-control" value="<?php echo esc($cfg['hostname'] !== '' ? $cfg['hostname'] : 'localhost'); ?>" required></div>
<div class="form-group"><label>Username database</label><input name="username" class="form-control" value="<?php echo esc($cfg['username']); ?>" required></div>
<div class="form-group"><label>Password database</label><input name="password" type="password" class="form-control" autocomplete="new-password"></div>
<div class="form-group"><label>Nama database</label><input name="database" class="form-control" value="<?php echo esc($cfg['database']); ?>" required></div>
<button class="btn btn-primary">Simpan &amp; Tes Koneksi</button><?php echo form_close(); ?>
<?php endif; ?>

<?php if ($stage === 'import'): ?>
<h5 class="mt-4">Langkah 2 — Import tabel (database.sql)</h5>
<p class="small text-muted">Koneksi OK, tapi tabel belum lengkap. Klik tombol untuk import otomatis dari file <code>database.sql</code> di root aplikasi. Alternatif: phpMyAdmin &gt; pilih database &gt; Import <code>database.sql</code>, lalu refresh halaman ini.</p>
<?php echo form_open('install'); ?><input type="hidden" name="step" value="import">
<button class="btn btn-primary">Import Otomatis database.sql</button><?php echo form_close(); ?>
<?php endif; ?>

<?php if ($stage === 'admin'): ?>
<h5 class="mt-4">Langkah 3 — Buat admin pertama</h5>
<?php echo form_open('install'); ?><input type="hidden" name="step" value="admin">
<div class="form-group"><label>Nama lengkap</label><input name="full_name" class="form-control" required></div>
<div class="form-group"><label>Username</label><input name="username" class="form-control" required></div>
<div class="form-group"><label>Password (min 8)</label><input name="p1" type="password" class="form-control" required autocomplete="new-password"></div>
<div class="form-group"><label>Ulangi password</label><input name="p2" type="password" class="form-control" required autocomplete="new-password"></div>
<button class="btn btn-success">Buat Admin</button><?php echo form_close(); ?>
<?php endif; ?>

<?php if ($stage !== 'locked' && !empty($ok) && strpos($ok, 'Admin "') === 0): ?>
<a class="btn btn-primary mt-2" href="<?php echo site_url('auth'); ?>">Ke Login</a>
<?php endif; ?>

<?php
$here = site_url('install');
if (strpos($here, '/index.php/') !== false) $alt = str_replace('/index.php/', '/', $here);
else { $pp = parse_url($here); $alt = $pp['scheme'] . '://' . $pp['host'] . (isset($pp['port']) ? ':' . $pp['port'] : '') . '/index.php' . (isset($pp['path']) ? $pp['path'] : '/install'); }
?>
<hr><p class="small text-muted mb-0">Halaman ini terbuka via <code><?php echo esc($here); ?></code>. Alternatifnya <code><?php echo esc($alt); ?></code> (pakai yang tidak 404 — bila versi tanpa <code>index.php</code> yang jalan, berarti <code>.htaccess</code> sudah aktif).</p>
<p class="small text-muted mb-0">Pengembang: <b>Zainudin Larau</b> — Belajar Pintar SD.</p>
</div></div></div></div></body></html>
