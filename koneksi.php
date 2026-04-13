<?php
$host     = "sql113.infinityfree.com"; // Lihat di vPanel
$user     = "if0_41651168";               // Username vPanel kamu
$password = "Jambuair6758";            // Password vPanel
$database = "if0_41651168_telurasin125";        // Nama DB yang kamu buat

$connect = mysqli_connect($host, $user, $password, $database);

if (!$connect) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>