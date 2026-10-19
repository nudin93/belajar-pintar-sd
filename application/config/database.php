<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
$active_group = 'default';
$query_builder = TRUE;
$db['default'] = array(
  'dsn' => '',
  'hostname' => 'localhost',
  'username' => 'ISI_USER_CPANEL',
  'password' => 'ISI_PASSWORD_CPANEL',
  'database' => 'ISI_NAMA_DATABASE',
  'dbdriver' => 'mysqli',
  'dbprefix' => '',
  'pconnect' => FALSE,
  // FALSE agar halaman installer (/install) bisa menampilkan diagnosis ramah
  // bila kredensial salah/belum diisi, bukan halaman error fatal. Aktifkan TRUE
  // sementara bila butuh debug query saat development.
  'db_debug' => FALSE,
  'cache_on' => FALSE,
  'char_set' => 'utf8mb4',
  'dbcollat' => 'utf8mb4_general_ci',
  'swap_pre' => '',
  'encrypt' => FALSE,
  'compress' => FALSE,
  'stricton' => FALSE,
  'failover' => array(),
  'save_queries' => TRUE
);
