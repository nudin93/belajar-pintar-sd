<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<div class="content-wrapper"><div class="content-header"><div class="container-fluid"><h1 class="m-0">Hasil & Nilai</h1></div></div><section class="content"><div class="container-fluid">
<p class="text-muted small">Nilai hanya muncul setelah guru mempublikasikan. Grafik di bawah tidak mempermalukan — hanya tren pribadimu.</p>
<div class="card"><div class="card-body"><canvas id="chNil" height="100"></canvas></div></div>
<div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table datatable"><thead><tr><th>Tugas</th><th>Mapel</th><th>Dikumpulkan</th><th>Status</th><th>Nilai</th><th>Komentar</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?php echo esc($r['title']); ?></td><td><?php echo esc($r['mapel']); ?></td><td class="small"><?php echo esc(fmt_tgl($r['submitted_at'])); ?></td><td><?php echo status_label($r['status']); ?></td><td><?php echo $r['score'] === null ? '<span class="text-muted">-</span>' : '<b>' . esc($r['score']) . '</b> / ' . esc($r['max_score']); ?></td><td class="small"><?php echo esc($r['comment']); ?></td></tr><?php endforeach; ?>
</tbody></table></div></div></div>
<script>document.addEventListener('DOMContentLoaded', function(){ var d = <?php echo json_encode(array_values(array_filter(array_column($rows, 'score'), function ($v) { return $v !== null; }))); ?>; if (window.Chart) { new Chart(document.getElementById('chNil'), {type:'line', data:{labels:d.map(function (_, i) { return 'Tugas ' + (i + 1); }), datasets:[{label:'Nilai', data:d, borderColor:'#2f6fed', fill:false, tension:.3}]}, options:{plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}}}}); } });</script>
</div></section></div>