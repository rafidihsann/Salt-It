<?php
include __DIR__ . '/koneksi.php';

if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    // Kalau sudah login, arahkan ke dashboard masing-masing
    $role = $_SESSION['role'];
    header("location: page/dashboard_$role.php");
    exit();
} else {
    // Kalau belum login, lempar ke folder api
    header("location: api/login.php");
    exit();
}
?>