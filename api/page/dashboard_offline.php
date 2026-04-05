<?php 
    include __DIR__ . '/../koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salt It</title>
    <link rel="stylesheet" href="../css/stylepage3.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-blue: #0d6efd;
            --dark-blue: #052c65;
            --soft-bg: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--soft-bg);
            color: #334155;
        }

        /* Sembunyikan elemen tertentu saat mencetak nota */
        @media print {
            body { background: white; }
            .container-mobile { width: 100%; max-width: none; box-shadow: none; margin: 0; padding: 0; }
            .top-nav, .chart-container, .btn-submit, .btn-print, .stok-card, .text-muted, h6, .card-custom form { display: none !important; }
            .nota-print { display: block !important; }
        }
    </style>
</head>
<body>

    <?php
    // 1. Hitung Stok Offline Tersedia
    $q_alokasi = mysqli_query($connect, "SELECT SUM(jumlah) as total FROM alokasi_offline");
    $total_alokasi = mysqli_fetch_assoc($q_alokasi)['total'] ?? 0;

    $q_terjual = mysqli_query($connect, "SELECT SUM(jumlah_butir) as total FROM transaksi WHERE platform = 'offline'");
    $total_terjual = mysqli_fetch_assoc($q_terjual)['total'] ?? 0;

    $sisa_stok = $total_alokasi - $total_terjual;
    ?>

    <nav class="top-nav container-mobile">
        <a href="../login/login.php" class="btn-back">
            <i class="bi bi-arrow-left"></i>
            <span>Keluar</span>
        </a>
        <h6 class="m-0 fw-bold ms-3">Kasir Toko Offline</h6>
    </nav>

    <div class="container-mobile">
        <div class="stok-card shadow-sm">
            <span class="small fw-bold text-uppercase opacity-75">Stok Tersedia di Toko</span>
            <h1 class="display-4 fw-bold m-0 mt-1"><?php echo number_format($sisa_stok, 0, ',', '.'); ?></h1>
            <span class="fs-6">Butir Telur Asin</span>
        </div>

        <div class="main-section">
            <div class="nota-print" id="notaArea">
                <div style="text-align: center; margin-bottom: 15px;">
                    <strong>SALT IT - OFFLINE STORE</strong><br>
                    <small>Struk Pembelian Telur Asin</small><br>
                    <small><?php echo date('d-m-Y H:i:s'); ?></small>
                </div>
                <div style="border-bottom: 1px dashed #000; margin-bottom: 10px;"></div>
                <div>
                    Jml Butir : <span id="notaJml">0</span><br>
                    Hrg Satuan: Rp <span id="notaHrgSatuan">0</span><br>
                    Metode    : <span id="notaMetode">-</span>
                </div>
                <div style="border-bottom: 1px dashed #000; margin-bottom: 10px; margin-top: 10px;"></div>
                <div>
                    <strong>TOTAL: Rp <span id="notaTotal">0</span></strong>
                </div>
                <div style="text-align: center; margin-top: 15px; font-size: 0.8em;">
                    Terima Kasih Atas Pembelian Anda!
                </div>
            </div>

            <div class="card-custom">
                <h6 class="fw-bold mb-4"><i class="bi bi-cart-plus me-2 text-success"></i>Input Transaksi Baru</h6>
                <form action="../proses/proses_transaksi_offline.php" method="POST" id="formTransaksi">
                    <input type="hidden" name="platform" value="offline">
                    <input type="hidden" name="id_user" value="4"> 
                    
                    <input type="hidden" name="total_harga" id="inputTotalHarga">

                    <div class="mb-3">
                        <label class="form-label">Jumlah Butir</label>
                        <input type="number" name="jumlah_butir" id="inputJumlah" class="form-control form-control-lg" placeholder="Maks: <?php echo $sisa_stok; ?>" required max="<?php echo $sisa_stok; ?>" min="1" oninput="kalkulasiTotal()">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga Satuan (Rp)</label>
                        <input type="number" id="inputHargaSatuan" class="form-control form-control-lg" placeholder="Contoh: 3500" required oninput="kalkulasiTotal()">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total Harga (Rp)</label>
                        <div class="p-3 bg-light rounded-3 fw-bold text-dark border">
                            Rp <span id="displayTotalHarga">0</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="metode_bayar" id="inputMetode" class="form-select form-select-lg" required onchange="updateNota()">
                            <option value="Tunai">Tunai</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Transfer">Transfer Bank</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Keterangan (Opsional)</label>
                        <textarea name="keterangan" class="form-control form-control-lg" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>

                    <button type="submit" class="btn-submit shadow-sm">
                        <i class="bi bi-receipt me-2"></i>Selesaikan Transaksi
                    </button>
                    <button type="button" class="btn-print shadow-sm" onclick="cetakNota()">
                        <i class="bi bi-printer me-2"></i>Cetak Nota Sementara
                    </button>
                </form>
            </div>

            <div class="p-2 text-center text-muted small">
                <i class="bi bi-shield-check me-1"></i> Transaksi akan otomatis memotong stok alokasi offline.
            </div>
        </div>
    </div>

    <script>
        // Logika Kalkulasi Total Harga dan Update Nota
        function kalkulasiTotal() {
            let jumlah = parseInt(document.getElementById('inputJumlah').value) || 0;
            let hargaSatuan = parseInt(document.getElementById('inputHargaSatuan').value) || 0;
            let total = jumlah * hargaSatuan;

            // Tampilkan di UI
            document.getElementById('displayTotalHarga').innerText = total.toLocaleString('id-ID');
            // Simpan di input hidden untuk disubmit ke PHP
            document.getElementById('inputTotalHarga').value = total;

            updateNota();
        }

        function updateNota() {
            let jumlah = parseInt(document.getElementById('inputJumlah').value) || 0;
            let hargaSatuan = parseInt(document.getElementById('inputHargaSatuan').value) || 0;
            let metode = document.getElementById('inputMetode').value;
            let total = jumlah * hargaSatuan;

            document.getElementById('notaJml').innerText = jumlah;
            document.getElementById('notaHrgSatuan').innerText = hargaSatuan.toLocaleString('id-ID');
            document.getElementById('notaMetode').innerText = metode;
            document.getElementById('notaTotal').innerText = total.toLocaleString('id-ID');
        }

        function cetakNota() {
            updateNota(); // Pastikan data terbaru
            window.print();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>