<?php
session_start();
include '../koneksi.php';

// 1. Tangkap data dari form
$waktu = $_POST['waktu'];
$berhasil = (int)$_POST['berhasil'];
$gagal = (int)$_POST['gagal'];
$keterangan = mysqli_real_escape_string($connect, $_POST['keterangan']);

// Kalkulasi total bahan yang diambil dari stok mentah
$total_digunakan = $berhasil + $gagal;

// Validasi dasar
if ($total_digunakan <= 0) {
    echo "<script>alert('Jumlah produksi tidak valid!'); window.history.back();</script>";
    exit;
}

// 2. Cek ketersediaan stok mentah (Safety Check)
$query_cek = "SELECT 
    SUM(CASE WHEN jenis = 'masuk' THEN jumlah ELSE 0 END) - 
    SUM(CASE WHEN jenis = 'keluar' THEN jumlah ELSE 0 END) as stok_akhir 
    FROM stokmentah";
$result_cek = mysqli_query($connect, $query_cek);
$data_cek = mysqli_fetch_assoc($result_cek);
$stok_saat_ini = $data_cek['stok_akhir'] ?? 0;

if ($total_digunakan > $stok_saat_ini) {
    echo "<script>alert('Gagal! Stok mentah tidak mencukupi. Sisa stok: $stok_saat_ini'); window.history.back();</script>";
    exit;
}

// 3. Masukkan data ke tabel produksi (Mencatat hasil jadi)
$query_produksi = "INSERT INTO produksi (jumlah, keterangan, waktu) 
                   VALUES ('$berhasil', '$keterangan', '$waktu')";

if (mysqli_query($connect, $query_produksi)) {
    
    // 4. Jika simpan produksi berhasil, baru kurangi stok mentah
    $ket_stok = "Produksi: " . $keterangan . " (Gagal QC: $gagal)";
    $query_stok = "INSERT INTO stokmentah (jumlah, tidak_lolos, jenis, keterangan, waktu) 
                   VALUES ('$total_digunakan', 0, 'keluar', '$ket_stok', '$waktu')";
    
    if (mysqli_query($connect, $query_stok)) {
        // Berhasil semua
        header("Location: ../page/dashboard_produksi.php");
        exit;
    } else {
        echo "Gagal mengurangi stok mentah: " . mysqli_error($connect);
    }

} else {
    echo "Gagal mencatat data produksi: " . mysqli_error($connect);
}
?>