<?php
// proses_pull_online.php
include '../koneksi.php';

// Simulasi data dari API Marketplace (Shopee/Tokopedia)
$id_user_online = 5; // Berdasarkan data user admin online Anda
$tgl_sekarang = date('Y-m-d H:i:s');

// Contoh data pesanan baru yang masuk
$order_baru = [
    ['qty' => 10, 'harga' => 35000, 'ket' => 'Pesanan Tokopedia - Budi'],
    ['qty' => 5,  'harga' => 17500, 'ket' => 'Pesanan Shopee - Ani']
];

foreach ($order_baru as $order) {
    $qty = $order['qty'];
    $total = $order['harga'];
    $ket = $order['ket'];
    
    // Masukkan ke tabel transaksi sebagai 'online'
    mysqli_query($connect, "INSERT INTO transaksi (tanggal, platform, jumlah_butir, total_harga, metode_bayar, id_user, keterangan) 
    VALUES ('$tgl_sekarang', 'online', '$qty', '$total', 'Marketplace', '$id_user_online', '$ket')");
}

// Kembali ke halaman
header("location:../page/dashboard_online.php?status=pull_success");
?>