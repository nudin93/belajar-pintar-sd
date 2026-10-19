<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><title>Laporan Nilai</title>
<style>body{font-family:Arial}table{border-collapse:collapse;width:100%}th,td{border:1px solid #333;padding:6px;font-size:12px}@media print{.noprint{display:none}}</style></head><body>
<h3>Laporan Nilai — <?php echo esc(app_setting('school_name', 'Belajar Pintar SD')); ?></h3>
<p class="noprint"><button onclick="window.print()">Cetak / Simpan PDF</button> Gunakan "Save as PDF" di dialog cetak.</p>
<table><tr><th>Siswa</th><th>Kelas</th><th>Mapel</th><th>Tugas</th><th>Status</th><th>Nilai</th></tr>
<?php foreach ($rows as $r): ?><tr><td><?php echo esc($r['siswa']); ?></td><td><?php echo esc($r['kelas']); ?></td><td><?php echo esc($r['mapel']); ?></td><td><?php echo esc($r['tugas']); ?></td><td><?php echo esc($r['status']); ?></td><td><?php echo esc($r['score']); ?></td></tr><?php endforeach; ?>
</table><p><small>Dicetak <?php echo date('d/m/Y H:i'); ?> oleh <?php echo esc($me['full_name']); ?></small></p></body></html>
