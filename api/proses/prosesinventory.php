<?php
include __DIR__ . '/../koneksi.php';

// Menangkap data dari form
$jenis = $_POST['jenis'];
$jumlah = (int) $_POST['jumlah'];
$keterangan = mysqli_real_escape_string($connect, $_POST['keterangan']);
$waktu = $_POST['waktu'];

$tidak_lolos = ($jenis === 'masuk') ? (int) $_POST['tidak_lolos'] : 0;

// Validasi Jumlah
if ($jumlah <= 0) {
    echo "<script>
        alert('Waduh! Jumlah telur harus lebih dari 0 ya.');
        window.history.back();
    </script>";
    exit;
}

// Validasi Jenis
if ($jenis !== 'masuk' && $jenis !== 'keluar') {
    echo "<script>
        alert('Jenis transaksi tidak valid!');
        window.history.back();
    </script>";
    exit;
}

// Validasi Logika QC
if ($jenis === 'masuk' && $tidak_lolos > $jumlah) {
    echo "<script>
        alert('Error: Jumlah tidak lolos QC tidak boleh lebih banyak dari total telur yang datang.');
        window.history.back();
    </script>";
    exit;
}

// Query Input
$query = "INSERT INTO stokmentah (jumlah, tidak_lolos, jenis, keterangan, waktu) 
          VALUES ('$jumlah', '$tidak_lolos', '$jenis', '$keterangan', '$waktu')";

if (mysqli_query($connect, $query)) {
    header("Location: ../page/telurmentah.php");
    exit;
} else {
    // Error Database pun kita kasih alert biar seragam
    $error = mysqli_error($connect);
    echo "<script>
        alert('Gagal menyimpan data ke database: $error');
        window.history.back();
    </script>";
}
?>