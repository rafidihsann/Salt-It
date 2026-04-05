<?php
session_start();
include '../koneksi.php';

// Pastikan hanya user yang login yang bisa akses
if (!isset($_SESSION['status'])) {
    header("location:../login/index.php");
    exit();
}

// Menangani Update Status SHIPPED (PBI033)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_transaksi'])) {
    $id = mysqli_real_escape_string($connect, $_POST['id_transaksi']);
    
    // 1. Ambil keterangan lama dari database
    $query_get = mysqli_query($connect, "SELECT keterangan FROM transaksi WHERE id_transaksi = '$id'");
    $data = mysqli_fetch_assoc($query_get);
    $old_keterangan = $data['keterangan'];

    // 2. Tambahkan tag [SHIPPED] jika belum ada
    if (strpos($old_keterangan, '[SHIPPED]') === false) {
        $new_keterangan = "[SHIPPED] " . $old_keterangan;
        
        $update = mysqli_query($connect, "UPDATE transaksi SET keterangan = '$new_keterangan' WHERE id_transaksi = '$id'");

        if ($update) {
            // Berhasil, arahkan kembali ke halaman online
            header("location:../page/dashboard_online.php?status=shipped_ok");
        } else {
            echo "Gagal memperbarui status: " . mysqli_error($connect);
        }
    } else {
        // Sudah berstatus SHIPPED
        header("location:../page/dashboard_online.php?status=already_shipped");
    }
} else {
    // Jika diakses tanpa POST, kembalikan ke dashboard
    header("location:../page/dashboard_online.php");
}
?>