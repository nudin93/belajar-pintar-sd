<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
<footer class="main-footer text-sm">
  <strong>Belajar Pintar SD</strong> &copy; <?php echo date('Y'); ?> &middot; <?php echo esc(app_setting('school_name', 'Sekolah')); ?> <span class="text-muted">&middot; CodeIgniter 3 &middot; Pengembang: Zainudin Larau</span>
  <span class="float-right d-none d-sm-inline">Zona waktu: Asia/Jakarta</span>
</footer>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?php echo base_url('assets/js/app.js'); ?>"></script>
<script>$(function(){ $('.datatable').DataTable({language:{url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'}}); });</script>
<?php $bp_fmsg = $this->session->flashdata('msg'); $bp_ferr = $this->session->flashdata('err'); ?>
<?php if ($bp_fmsg || $bp_ferr): ?><script>document.addEventListener('DOMContentLoaded', function(){ setTimeout(function(){ if (window.bpToast) bpToast(<?php echo json_encode(strip_tags($bp_fmsg ? $bp_fmsg : $bp_ferr)); ?>, <?php echo $bp_fmsg ? "'success'" : "'danger'"; ?>); }, 400); });</script><?php endif; ?>
</body>
</html>
