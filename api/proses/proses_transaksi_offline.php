<?php
session_start();
include __DIR__ . '/../koneksi.php';

// 1. Tangkap data dari form
$platform     = $_POST['platform']; // Nilainya 'offline'
$id_user      = (int)$_POST['id_user']; // ID user admin yang menginput
$jumlah_butir = (int)$_POST['jumlah_butir'];
$total_harga  = (float)$_POST['total_harga'];
$metode_bayar = mysqli_real_escape_string($connect, $_POST['metode_bayar']);
$keterangan   = mysqli_real_escape_string($connect, $_POST['keterangan']);

// Validasi dasar
if ($jumlah_butir <= 0 || $total_harga <= 0) {
    echo "<script>alert('Transaksi gagal: Jumlah butir atau total harga tidak valid!'); window.history.back();</script>";
    exit;
}

// 2. Keamanan Lapis Kedua: Cek Ulang Sisa Stok Offline di Database
// Mengambil total alokasi offline
$q_alokasi = mysqli_query($connect, "SELECT SUM(jumlah) as total FROM alokasi_offline");
$total_alokasi = mysqli_fetch_assoc($q_alokasi)['total'] ?? 0;

// Mengambil total yang sudah terjual di offline
$q_terjual = mysqli_query($connect, "SELECT SUM(jumlah_butir) as total FROM transaksi WHERE platform = 'offline'");
$total_terjual = mysqli_fetch_assoc($q_terjual)['total'] ?? 0;

$sisa_stok = $total_alokasi - $total_terjual;

// Cegah input jika jumlah yang dibeli melebihi stok di toko
if ($jumlah_butir > $sisa_stok) {
    echo "<script>alert('Transaksi gagal: Stok offline tidak mencukupi! Sisa stok saat ini: $sisa_stok butir.'); window.history.back();</script>";
    exit;
}

// 3. Proses Insert ke Tabel Transaksi
// Tanggal tidak perlu diinput manual karena di database sudah menggunakan DEFAULT current_timestamp()
$query_insert = "INSERT INTO transaksi (platform, jumlah_butir, total_harga, metode_bayar, id_user, keterangan) 
                 VALUES ('$platform', '$jumlah_butir', '$total_harga', '$metode_bayar', '$id_user', '$keterangan')";

if (mysqli_query($connect, $query_insert)) {
    // Berhasil. Arahkan kembali ke halaman kasir offline
    // Catatan: Ganti 'kasir_offline.php' dengan nama file asli halaman kasir Anda
    header("Location: ../page/dashboard_offline.php");
    exit;
} else {
    echo "Gagal menyimpan transaksi: " . mysqli_error($connect);
}
?>