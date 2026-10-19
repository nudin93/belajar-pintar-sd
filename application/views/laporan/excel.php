<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<?php
echo '<table border="1"><tr><th>Siswa</th><th>Kelas</th><th>Mapel</th><th>Tugas</th><th>Status</th><th>Nilai</th><th>Publik</th><th>Dikumpulkan</th></tr>';
foreach ($rows as $r) { echo '<tr><td>' . esc($r['siswa']) . '</td><td>' . esc($r['kelas']) . '</td><td>' . esc($r['mapel']) . '</td><td>' . esc($r['tugas']) . '</td><td>' . esc($r['status']) . '</td><td>' . esc($r['score']) . '</td><td>' . ($r['is_published'] ? 'Ya' : 'Belum') . '</td><td>' . esc($r['submitted_at']) . '</td></tr>'; }
echo '</table>';
