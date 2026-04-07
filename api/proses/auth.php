<?php
// Karena session_start() sudah ada di koneksi.php, 

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    // Kalau belum login, tendang ke halaman login
    header("location:../login.php?pesan=gagal");
    exit();
}
?>