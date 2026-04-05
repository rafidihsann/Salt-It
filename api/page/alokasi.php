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

        
    </style>
</head>
<body>
    <?php
    include '../koneksi.php';
    session_start();
        if (!isset($_SESSION['status'])) {
            header("location:../login/login.php");
            exit();
        }
    
        // 1. Menghitung Total Produksi
        $q_prod = mysqli_query($connect, "SELECT SUM(jumlah) as total FROM produksi");
        $total_produksi = mysqli_fetch_assoc($q_prod)['total'] ?? 0;

        // 2. Menghitung Total Alokasi yang pernah dilakukan
        $q_on = mysqli_query($connect, "SELECT SUM(jumlah) as total FROM alokasi_online");
        $total_alloc_online = mysqli_fetch_assoc($q_on)['total'] ?? 0;

        $q_off = mysqli_query($connect, "SELECT SUM(jumlah) as total FROM alokasi_offline");
        $total_alloc_offline = mysqli_fetch_assoc($q_off)['total'] ?? 0;

        // 3. Menghitung Total Terjual dari tabel transaksi
        $q_sold_on = mysqli_query($connect, "SELECT SUM(jumlah_butir) as total FROM transaksi WHERE platform = 'online'");
        $total_sold_online = mysqli_fetch_assoc($q_sold_on)['total'] ?? 0;

        $q_sold_off = mysqli_query($connect, "SELECT SUM(jumlah_butir) as total FROM transaksi WHERE platform = 'offline'");
        $total_sold_offline = mysqli_fetch_assoc($q_sold_off)['total'] ?? 0;

        // 4. Kalkulasi Stok Akhir
        // Stok di Gudang Produksi (Belum dialokasikan ke toko manapun)
        $ready_stock = $total_produksi - ($total_alloc_online + $total_alloc_offline);

        // Stok yang saat ini ada di Toko Online (Alokasi - Terjual)
        $current_online_stock = $total_alloc_online - $total_sold_online;

        // Stok yang saat ini ada di Toko Offline (Alokasi - Terjual)
        $current_offline_stock = $total_alloc_offline - $total_sold_offline;
    ?>

    <nav class="top-nav container-mobile">
        <a href="dashboard_inventaris.php" class="btn-back">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali</span>
        </a>
        <h6 class="m-0 fw-bold ms-3">Input Alokasi Stok Telur Asin</h6>
    </nav>

    <div class="container-mobile">
        <!-- Informasi Stok Siap Alokasi -->
        <div class="container-mobile">
        <div class="ready-stock-card shadow-sm text-center p-5" style="background: linear-gradient(135deg, #052c65 0%, #0d6efd 100%); color: white; border-radius: 1.5rem;">
            <span class="small fw-bold text-uppercase opacity-75">Stok Siap Alokasi (Gudang)</span>
            <h1 class="display-4 fw-bold m-0 mt-1"><?php echo number_format($ready_stock, 0, ',', '.'); ?></h1>
            <span class="fs-6">Butir Telur</span>
        </div>

        <div class="row g-3 mt-1 mb-4">
            <div class="col-6">
                <div class="p-3 bg-white border rounded-4 shadow-sm text-center">
                    <div class="small text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Stok Online</div>
                    <div class="h4 fw-bold mb-0 text-primary"><?php echo number_format($current_online_stock, 0, ',', '.'); ?></div>
                    <div class="small text-muted" style="font-size: 0.7rem;">Butir</div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 bg-white border rounded-4 shadow-sm text-center">
                    <div class="small text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Stok Offline</div>
                    <div class="h4 fw-bold mb-0 text-success"><?php echo number_format($current_offline_stock, 0, ',', '.'); ?></div>
                    <div class="small text-muted" style="font-size: 0.7rem;">Butir</div>
                </div>
            </div>
        </div>

        <div class="action-section">
            <h6 class="text-secondary fw-bold small text-uppercase mb-3" style="letter-spacing: 0.05em;">Pilih Tujuan Alokasi</h6>
            
            <!-- Tombol Alokasi Online -->
            <div class="allocation-card" data-bs-toggle="modal" data-bs-target="#modalAlokasi" onclick="setTarget('online')">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-online">
                        <i class="bi bi-globe"></i>
                    </div>
                    <div>
                        <div class="fw-bold">Toko Online</div>
                        <div class="small text-muted">Marketplace, WhatsApp, Web</div>
                    </div>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
            </div>

            <!-- Tombol Alokasi Offline -->
            <div class="allocation-card" data-bs-toggle="modal" data-bs-target="#modalAlokasi" onclick="setTarget('offline')">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-offline">
                        <i class="bi bi-shop"></i>
                    </div>
                    <div>
                        <div class="fw-bold">Toko Offline</div>
                        <div class="small text-muted">Toko Fisik, Reseller, Agen</div>
                    </div>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
            </div>

    <!-- Modal Alokasi -->
    <div class="modal fade" id="modalAlokasi" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered px-3">
            <div class="modal-content shadow-lg">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0" id="modalTitle">Alokasi Stok</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="../proses/prosesalokasi.php" method="POST">
                        <input type="hidden" name="target" id="inputTarget">
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Jumlah Alokasi (Butir)</label>
                            <input type="number" name="jumlah" id="inputJumlah" class="form-control form-control-lg" placeholder="Maks: <?php echo $ready_stock; ?>" required max="<?php echo $ready_stock; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Keterangan</label>
                            <input type="text" name="keterangan" class="form-control form-control-lg" placeholder="Contoh: Stok Mingguan Toko A">
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Tanggal</label>
                            <input type="date" name="waktu" class="form-control form-control-lg" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <button type="submit" class="btn-submit shadow-sm">
                            Konfirmasi Alokasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setTarget(target) {
            const title = document.getElementById('modalTitle');
            const inputTarget = document.getElementById('inputTarget');
            
            inputTarget.value = target;
            if(target === 'online') {
                title.innerHTML = '<i class="bi bi-globe text-primary me-2"></i>Alokasi Online';
            } else {
                title.innerHTML = '<i class="bi bi-shop text-success me-2"></i>Alokasi Offline';
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>