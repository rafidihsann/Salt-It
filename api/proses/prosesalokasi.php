<?php
session_start();
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['status'])) {
    header("location:../login/login.php");
    exit();
}

// 1. Tangkap Data dari Form
$target     = $_POST['target']; // 'online' atau 'offline'
$jumlah     = (int)$_POST['jumlah'];
$keterangan = mysqli_real_escape_string($connect, $_POST['keterangan']);
$waktu      = $_POST['waktu'];

// 2. Validasi Input Dasar
if ($jumlah <= 0) {
    echo "<script>alert('Jumlah alokasi harus lebih dari 0!'); window.history.back();</script>";
    exit();
}

// 3. Verifikasi Sisa Stok Siap Alokasi (Safety Check di Sisi Server)
// Rumus: Total Produksi - (Total Alokasi Online + Total Alokasi Offline)
$q_prod = mysqli_query($connect, "SELECT SUM(jumlah) as total FROM produksi");
$total_produksi = mysqli_fetch_assoc($q_prod)['total'] ?? 0;

$q_on = mysqli_query($connect, "SELECT SUM(jumlah) as total FROM alokasi_online");
$total_online = mysqli_fetch_assoc($q_on)['total'] ?? 0;

$q_off = mysqli_query($connect, "SELECT SUM(jumlah) as total FROM alokasi_offline");
$total_offline = mysqli_fetch_assoc($q_off)['total'] ?? 0;

$ready_stock = $total_produksi - ($total_online + $total_offline);

if ($jumlah > $ready_stock) {
    echo "<script>alert('Gagal! Stok tidak mencukupi. Sisa: $ready_stock'); window.history.back();</script>";
    exit();
}

// 4. Tentukan Tabel Tujuan Berdasarkan Target
if ($target === 'online') {
    $query = "INSERT INTO alokasi_online (jumlah, keterangan, waktu) VALUES ('$jumlah', '$keterangan', '$waktu')";
} elseif ($target === 'offline') {
    $query = "INSERT INTO alokasi_offline (jumlah, keterangan, waktu) VALUES ('$jumlah', '$keterangan', '$waktu')";
} else {
    echo "<script>alert('Target alokasi tidak valid!'); window.history.back();</script>";
    exit();
}

// 5. Eksekusi Query
if (mysqli_query($connect, $query)) {
    header("Location: ../page/dashboard_inventaris.php");
    exit();
} else {
    echo "Gagal memproses alokasi: " . mysqli_error($connect);
}
?>