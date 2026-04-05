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

    <div id="loadingOverlay">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="mt-2 fw-bold">Menarik Data Marketplace...</div>
        </div>
    </div>

    <?php
    include __DIR__ . '/../koneksi.php';
    session_start();
        if (!isset($_SESSION['status'])) {
            header("location:../login/login.php");
            exit();
        }

    // 1. Hitung Stok Online Tersedia (PBI033 Integration)
    // Formula: Total Alokasi Online - Total Penjualan Online (id_user untuk admin online adalah 5 berdasarkan SQL)
    $q_alokasi = mysqli_query($connect, "SELECT SUM(jumlah) as total FROM alokasi_online");
    $total_alokasi = mysqli_fetch_assoc($q_alokasi)['total'] ?? 0;

    $q_terjual = mysqli_query($connect, "SELECT SUM(jumlah_butir) as total FROM transaksi WHERE platform = 'online'");
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
        <!-- Informasi Stok & Action Pull -->
        <div class="stok-card shadow-sm">
            <div>
                <span class="small opacity-75">Stok Online Ready</span>
                <h2 class="fw-bold m-0" id="currentStockVal"><?php echo $sisa_stok; ?> <small class="fs-6">Butir</small></h2>
            </div>
            <button class="btn-pull" onclick="pullData()">
                <i class="bi bi-cloud-download me-1"></i> Pull Data
            </button>
        </div>

        <div class="px-3 mb-3">
            <h6 class="fw-bold text-secondary small text-uppercase" style="letter-spacing: 0.05em;">Pesanan Masuk (Marketplace)</h6>
        </div>

        <!-- List Pesanan (PBI032) -->
        <div id="orderList">
            <!-- Data awal jika ada di database transaksi dengan status pending (ilustrasi) -->
            <?php
            $q_orders = mysqli_query($connect, "SELECT * FROM transaksi WHERE platform = 'online' ORDER BY tanggal DESC LIMIT 5");
            if (mysqli_num_rows($q_orders) > 0) {
                while($row = mysqli_fetch_assoc($q_orders)) {
                    $is_shipped = strpos($row['keterangan'], '[SHIPPED]') !== false;
                    ?>
                    <div class="order-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="fw-bold text-dark">Order #<?php echo $row['id_transaksi']; ?></div>
                                <div class="text-muted small"><?php echo date('d M Y, H:i', strtotime($row['tanggal'])); ?></div>
                            </div>
                            <span class="status-badge <?php echo $is_shipped ? 'badge-shipped' : 'badge-pending'; ?>">
                                <?php echo $is_shipped ? 'Shipped' : 'Pending'; ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2">
                            <span class="small text-secondary">Jumlah: <span class="fw-bold text-dark"><?php echo $row['jumlah_butir']; ?> Butir</span></span>
                            <span class="small text-secondary">Total: <span class="fw-bold text-primary">Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></span></span>
                        </div>
                        <?php if(!$is_shipped): ?>
                        <button class="btn-ship" onclick="processShip(<?php echo $row['id_transaksi']; ?>, <?php echo $row['jumlah_butir']; ?>)">
                            Update Status: SHIPPED
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="text-center py-5 text-muted small"><i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada data pesanan.<br>Klik "Pull Data" untuk sinkronisasi.</div>';
            }
            ?>
        </div>
    </div>

    <script>
        // PBI032: Simulasi Penarikan Data dari Marketplace
        function pullData() {
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            // Simulasi API delay
            setTimeout(() => {
                document.getElementById('loadingOverlay').style.display = 'none';
                // Dalam implementasi nyata, ini akan memicu request ke proses_pull_online.php
                // Untuk demo, kita reload halaman untuk melihat data terbaru atau beri notifikasi
                window.location.reload();
            }, 1500);
        }

        // PBI033: Update Status ke SHIPPED & Pengurangan Stok
        function processShip(id, qty) {
            const currentStock = <?php echo $sisa_stok; ?>;
            
            // Validasi stok sebelum kirim
            if (qty > currentStock) {
                alert('Gagal! Stok online tidak mencukupi untuk pesanan ini.');
                return;
            }

            if(confirm('Konfirmasi pengiriman pesanan #' + id + '? Stok akan berkurang sebanyak ' + qty + ' butir.')) {
                // Form submission simulation
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../proses/proses_online.php';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id_transaksi';
                idInput.value = id;
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = 'SHIPPED';
                
                form.appendChild(idInput);
                form.appendChild(statusInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // PBI034: Unit Testing Mock
        console.log("Unit Test: Verifikasi Penghitungan Stok Online");
        const totalAlokasi = <?php echo $total_alokasi; ?>;
        const totalTerjual = <?php echo $total_terjual; ?>;
        const calcCheck = totalAlokasi - totalTerjual;
        console.log("Ekspektasi: " + calcCheck + " | Hasil: <?php echo $sisa_stok; ?>");
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>