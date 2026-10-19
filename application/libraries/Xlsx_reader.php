<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
// Pembaca XLSX minimal TANPA composer (untuk impor Dapodik).
// Butuh ekstensi PHP: zip + simplexml (bawaan di hampir semua cPanel).
// Sengaja memakai xpath local-name() agar tahan varian namespace
// (transitional maupun strict) dan tahan sel rich-text/wrap Dapodik.
// Tanggal serial Excel dikonversi ke 'Y-m-d' / 'Y-m-d H:i:s'.
class Xlsx_reader {
  public $error = '';

  private function zip_get($zip, $name) {
    $idx = $zip->locateName($name);
    if ($idx === false) return null;
    return $zip->getFromIndex($idx);
  }

  private function xml($data) {
    if ($data === null || $data === false || $data === '') return null;
    if (!function_exists('simplexml_load_string')) { $this->error = 'Ekstensi PHP simplexml belum aktif di hosting.'; return null; }
    libxml_use_internal_errors(true);
    $x = simplexml_load_string($data);
    libxml_clear_errors();
    return $x ? $x : null;
  }

  private function q($el, $path) {
    if (!$el) return array();
    $r = $el->xpath($path);
    return $r ? $r : array();
  }

  private function attr_id($el) {
    // Cari atribut r:id di semua namespace yang dikenal (transitional + strict).
    foreach (array('', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships', 'http://purl.oclc.org/ooxml/officeDocument/relationships') as $ns) {
      $a = ($ns === '') ? $el->attributes() : $el->attributes($ns);
      if ($a) foreach ($a as $k => $v) if ($k === 'id') return (string)$v;
    }
    foreach ($el->getNamespaces(true) as $ns) {
      $a = $el->attributes($ns);
      if ($a) foreach ($a as $k => $v) if ($k === 'id') return (string)$v;
    }
    return '';
  }

  // Daftar nama sheet.
  public function sheets($path) {
    $z = $this->open_zip($path);
    if (!$z) return array();
    $wb = $this->xml($this->zip_get($z, 'xl/workbook.xml'));
    $z->close();
    $out = array();
    foreach ($this->q($wb, '//*[local-name()="sheet"]') as $sh) $out[] = (string)$sh['name'];
    return $out;
  }

  // Baca sheet ke-$idx (0 = pertama) -> array baris (array kolom 0-based; sel kosong = null).
  public function read($path, $idx = 0) {
    $z = $this->open_zip($path);
    if (!$z) return null;
    $target = $this->sheet_target($z, $idx);
    if (!$target) { $z->close(); if ($this->error === '') $this->error = 'Sheet ke-' . ($idx + 1) . ' tidak ditemukan.'; return null; }
    $shared = $this->load_shared($z);
    list($xfs, $dateFmt) = $this->load_styles($z);
    $is1904 = $this->is_1904($z);
    $ws = $this->xml($this->zip_get($z, $target));
    $z->close();
    if (!$ws) { if ($this->error === '') $this->error = 'Isi sheet rusak/tak dikenal.'; return null; }
    $rows = array();
    foreach ($this->q($ws, '//*[local-name()="row"]') as $row) {
      $cells = array(); $max = -1;
      foreach ($this->q($row, './*[local-name()="c"]') as $c) {
        $col = $this->col_index((string)$c['r']);
        if ($col < 0) continue;
        $cells[$col] = $this->cell_value($c, $shared, $xfs, $dateFmt, $is1904);
        if ($col > $max) $max = $col;
      }
      if ($max < 0) continue;
      $out = array();
      for ($i = 0; $i <= $max; $i++) $out[$i] = array_key_exists($i, $cells) ? $cells[$i] : null;
      $rows[] = $out;
    }
    if (!$rows) $this->error = 'Sheet kosong (tidak ada baris terbaca).';
    return $rows;
  }

  private function open_zip($path) {
    if (!class_exists('ZipArchive')) { $this->error = 'Ekstensi PHP zip belum aktif di hosting ini.'; return null; }
    if (!is_file($path) || !is_readable($path)) { $this->error = 'File upload tidak ditemukan di server.'; return null; }
    $fh = @fopen($path, 'rb');
    $head = $fh ? @fread($fh, 4) : '';
    if ($fh) @fclose($fh);
    if ($head !== "PK\x03\x04" && $head !== "PK\x05\x06" && $head !== "PK\x07\x08") {
      $this->error = 'Bukan file .xlsx asli (mungkin .xls lama atau HTML yang diganti nama — buka di Excel/WPS lalu Save As .xlsx).';
      return null;
    }
    $z = new ZipArchive();
    if ($z->open($path) !== true) { $this->error = 'File rusak sehingga tidak bisa dibuka.'; return null; }
    return $z;
  }

  private function sheet_target($z, $idx) {
    $wb = $this->xml($this->zip_get($z, 'xl/workbook.xml'));
    if (!$wb) { $this->error = 'Struktur workbook tak dikenal.'; return null; }
    $sheets = $this->q($wb, '//*[local-name()="sheet"]');
    if (!isset($sheets[$idx])) return null;
    $rid = $this->attr_id($sheets[$idx]);
    $rels = $this->xml($this->zip_get($z, 'xl/_rels/workbook.xml.rels'));
    foreach ($this->q($rels, '//*[local-name()="Relationship"]') as $rel) {
      if ((string)$rel['Id'] === $rid) {
        $type = (string)$rel['Type'];
        if ($type !== '' && strpos($type, '/worksheet') === false) continue;
        $t = (string)$rel['Target'];
        if (strpos($t, 'worksheets/') === 0) $t = 'xl/' . $t;
        return $t;
      }
    }
    return 'xl/worksheets/sheet' . ($idx + 1) . '.xml'; // fallback umum
  }

  private function load_shared($z) {
    $shared = array();
    $x = $this->xml($this->zip_get($z, 'xl/sharedStrings.xml'));
    foreach ($this->q($x, '//*[local-name()="si"]') as $si) {
      $t = '';
      foreach ($this->q($si, './/*[local-name()="t"]') as $tn) $t .= (string)$tn;
      $shared[] = $t;
    }
    return $shared;
  }

  // return array(xf_list, fmtId_yang_tanggal=>true)
  private function load_styles($z) {
    $xfs = array(); $dateFmt = array();
    $x = $this->xml($this->zip_get($z, 'xl/styles.xml'));
    $custom = array();
    foreach ($this->q($x, '//*[local-name()="numFmt"]') as $nf) $custom[(int)$nf['numFmtId']] = (string)$nf['formatCode'];
    foreach ($this->q($x, '//*[local-name()="cellXfs"]/*[local-name()="xf"]') as $xf) $xfs[] = (int)$xf['numFmtId'];
    $builtin_date = array(14,15,16,17,18,19,20,21,22,27,28,29,30,31,32,33,34,35,36,45,46,47,50,57);
    foreach ($xfs as $i => $fid) {
      if (in_array($fid, $builtin_date, true)) $dateFmt[$i] = true;
      elseif (isset($custom[$fid]) && $this->code_is_date($custom[$fid])) $dateFmt[$i] = true;
    }
    return array($xfs, $dateFmt);
  }

  private function code_is_date($code) {
    $code = trim((string)$code);
    if ($code === '' || $code === '@' || $code === 'General') return false;
    $code = preg_replace('/"[^"]*"/', '', $code); // abaikan teks dalam kutip
    return (bool)preg_match('/[dmy]/i', $code);
  }

  private function is_1904($z) {
    $wb = $this->xml($this->zip_get($z, 'xl/workbook.xml'));
    foreach ($this->q($wb, '//*[local-name()="workbookPr"]') as $pr) {
      $a = $pr->attributes();
      if (isset($a['date1904']) && ((string)$a['date1904'] === '1' || (string)$a['date1904'] === 'true')) return true;
    }
    return false;
  }

  private function col_index($ref) {
    if (!preg_match('/^([A-Z]+)/', strtoupper($ref), $m)) return -1;
    $letters = $m[1]; $n = 0;
    for ($i = 0; $i < strlen($letters); $i++) $n = $n * 26 + (ord($letters[$i]) - 64);
    return $n - 1;
  }

  private function cell_value($c, $shared, $xfs, $dateFmt, $is1904) {
    $t = (string)$c['t'];
    $s = isset($c['s']) ? (int)$c['s'] : -1;
    if ($t === 'inlineStr') {
      $iss = $this->q($c, './*[local-name()="is"]');
      $txt = '';
      if ($iss) foreach ($this->q($iss[0], './/*[local-name()="t"]') as $tn) $txt .= (string)$tn;
      return $txt === '' ? null : $txt;
    }
    $v = '';
    foreach ($this->q($c, './*[local-name()="v"]') as $vv) { $v = trim((string)$vv); break; }
    if ($v === '') return null;
    if ($t === 's') { $i = (int)$v; return isset($shared[$i]) && $shared[$i] !== '' ? $shared[$i] : null; }
    if ($t === 'b') return ((int)$v === 1 ? '1' : '0');
    if ($t === 'e') return null;
    if ($t === 'str') return $v;
    if (is_numeric($v) && $s >= 0 && isset($dateFmt[$s])) {
      $serial = (float)$v;
      $base = $is1904 ? 24107 : 25569;
      $ts = (int)round(($serial - $base) * 86400);
      if ($ts < -62135596800) return null;
      $fid = isset($xfs[$s]) ? $xfs[$s] : 0;
      $hasTime = in_array($fid, array(18,19,20,21,45,46,47), true) || (($serial - floor($serial)) > 0.000001 && $fid !== 14);
      // date() (zona Asia/Jakarta dari config) agar tanggal lahir tak geser hari.
      return $hasTime ? date('Y-m-d H:i:s', $ts) : date('Y-m-d', $ts);
    }
    return $v;
  }
}
