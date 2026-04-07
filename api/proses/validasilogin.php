<?php
session_start(); // HARUS DI BARIS 1
include __DIR__ . '/../koneksi.php';

// Ambil data
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// 1. GUNAKAN PREPARED STATEMENT (Biar gak kena blokir Firewall Vercel)
$stmt = $connect->prepare("SELECT * FROM user WHERE email = ? AND password = ?");
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$hasil = $stmt->get_result();

if ($hasil->num_rows > 0) {
    $row = $hasil->fetch_assoc();
    
    $_SESSION['email'] = $email;
    $_SESSION['status'] = "login";
    $_SESSION['role'] = $row['role'];

    // 2. Gunakan path yang lebih "pasti"
    // Dari /api/proses/ ke /api/page/
    $role = $row['role'];
    if ($role === 'owner') {
        header("Location: ../page/dashboard_owner.php");
    } elseif ($role === 'inventaris') {
        header("Location: ../page/dashboard_inventaris.php");
    } else {
        header("Location: ../page/dashboard_online.php");
    }
    exit();
} else {
    // 3. JIKA GAGAL: Arahkan balik ke login
    // Pastikan path ini benar dari sudut pandang FOLDER 'proses'
    header("Location: ../login/login.php?pesan=gagal");
    exit();
}
