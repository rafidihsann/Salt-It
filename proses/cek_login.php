<?php 
session_start();

if ($_SESSION['status'] != "login") {
    // Kalau belum login, tendang balik ke halaman login
    header("location:../login/login.php?pesan=gagal");
    exit();
}
?>