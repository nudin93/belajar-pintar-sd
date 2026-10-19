<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * BELAJAR PINTAR SD — Edisi CodeIgniter 3
 * Pengembang: Zainudin Larau
 * Kegunaan: Aplikasi pembelajaran SD kelas 1-6 (materi, tugas foto, pemeriksaan, nilai, laporan).
 */
$prot = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$dir = str_replace('\\','/', dirname(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/'));
$dir = ($dir === '/' || $dir === '.') ? '' : $dir;
$config['base_url'] = $prot . '://' . $host . $dir . '/';
$config['index_page'] = 'index.php'; // works with AND without .htaccess; set '' only after .htaccess rename is proven
$config['uri_protocol'] = 'REQUEST_URI';
$config['url_suffix'] = '';
$config['language'] = 'english';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = FALSE;
$config['subclass_prefix'] = 'MY_';
$config['composer_autoload'] = FALSE;
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
$config['allow_get_array'] = TRUE;
$config['enable_query_strings'] = FALSE;
$config['log_threshold'] = 1;
$config['log_path'] = '';
$config['log_file_extension'] = '';
$config['log_date_format'] = 'Y-m-d H:i:s';
$config['error_views_path'] = '';
$config['cache_path'] = '';
$config['cache_query_string'] = FALSE;
$config['encryption_key'] = 'c8137e760536c8bc6253ab25e7851a88e4657db0d494638cfb5f141160399dfb';
$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'bpsd_session';
$config['sess_expiration'] = 7200;
// Folder sessions bawaan paket; bila tidak writable di hosting (login mental kembali
// ke form tanpa pesan), otomatis pakai folder temp sistem agar login tetap jalan.
$_bp_sesdir = APPPATH . 'cache/sessions/';
$config['sess_save_path'] = (is_dir($_bp_sesdir) && is_writable($_bp_sesdir)) ? $_bp_sesdir : sys_get_temp_dir();
unset($_bp_sesdir);
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = TRUE;
$config['cookie_prefix'] = '';
$config['cookie_domain'] = '';
$config['cookie_path'] = '/';
$config['cookie_secure'] = FALSE;
$config['cookie_httponly'] = TRUE;
$config['standardize_newlines'] = FALSE;
$config['global_xss_filtering'] = FALSE;
$config['csrf_protection'] = TRUE;
$config['csrf_token_name'] = 'bp_csrf';
$config['csrf_cookie_name'] = 'bp_csrf_cookie';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array();
$config['compress_output'] = FALSE;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = FALSE;
$config['proxy_ips'] = '';
date_default_timezone_set('Asia/Jakarta');
