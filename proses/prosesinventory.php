<?php
session_start();
include '../koneksi.php';

// Menangkap data dari form
$jenis = $_POST['jenis'];
$jumlah = (int) $_POST['jumlah'];
$keterangan = mysqli_real_escape_string($connect, $_POST['keterangan']);
$waktu = $_POST['waktu'];

// Menangkap input tidak_lolos hanya jika jenisnya 'masuk'
// Jika 'keluar', kita set otomatis ke 0 agar konsisten di database
$tidak_lolos = ($jenis === 'masuk') ? (int) $_POST['tidak_lolos'] : 0;


if ($jumlah <= 0) {
    echo "Jumlah harus lebih dari 0.";
    exit;
}

if ($jenis !== 'masuk' && $jenis !== 'keluar') {
    echo "Jenis tidak valid.";
    exit;
}

// Validasi Logika QC: Tidak mungkin telur rusak lebih banyak dari telur yang datang
if ($jenis === 'masuk' && $tidak_lolos > $jumlah) {
    echo "Error: Jumlah tidak lolos QC tidak boleh melebihi total telur yang masuk.";
    exit;
}

$query = "INSERT INTO stokmentah (jumlah, tidak_lolos, jenis, keterangan, waktu) 
          VALUES ('$jumlah', '$tidak_lolos', '$jenis', '$keterangan', '$waktu')";

if (mysqli_query($connect, $query)) {
    // Kembali ke halaman inventaris dengan status sukses
    header("Location: ../page/telurmentah.php");
    exit;
} else {
    echo "Gagal menyimpan data: " . mysqli_error($connect);
}
?>