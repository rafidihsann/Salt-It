<?php
if (!is_dir('/tmp/sessions')) {
    mkdir('/tmp/sessions', 0777, true);
}
session_save_path('/tmp/sessions');
$host     = getenv('MYSQLHOST');
$user     = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$db_name  = getenv('MYSQLDATABASE');
$port     = getenv('MYSQLPORT');

$connect = mysqli_connect($host, $user, $password, $db_name, $port);

if (!$connect) {
    die("Koneksi ke Gudang Railway Gagal!");
}
?>