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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-blue: #0d6efd;
            --dark-blue: #052c65;
            --soft-bg: #f8fafc;
            --text-dark: #1e293b;
            --text-secondary: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--soft-bg);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
        }

        @media print {
            .no-print { display: none !important; }
            .container-mobile { max-width: 100%; border: none; }
            .section-card { border: 1px solid #ddd; break-inside: avoid; }
        }
    </style>
</head>
<body>
    <?php
    // PBI039: Integration Testing - Mengambil data dari tabel produksi & transaksi
    // 1. Ringkasan Statistik
    $q_prod_total = mysqli_query($connect, "SELECT SUM(jumlah) as total FROM produksi");
    $total_produksi = mysqli_fetch_assoc($q_prod_total)['total'] ?? 0;

    $q_sales_total = mysqli_query($connect, "SELECT SUM(jumlah_butir) as butir, SUM(total_harga) as revenue FROM transaksi");
    $sales_summary = mysqli_fetch_assoc($q_sales_total);
    $total_terjual = $sales_summary['butir'] ?? 0;
    $total_pendapatan = $sales_summary['revenue'] ?? 0;

    // PBI035: Data Grafik Tren (7 Hari Terakhir)
    $chart_labels = [];
    $data_produksi = [];
    $data_penjualan = [];

    for ($i = 6; $i >= 0; $i--) {
        $tgl = date('Y-m-d', strtotime("-$i days"));
        $chart_labels[] = date('d M', strtotime($tgl));

        // Get daily production
        $qp = mysqli_query($connect, "SELECT SUM(jumlah) as j FROM produksi WHERE waktu = '$tgl'");
        $data_produksi[] = mysqli_fetch_assoc($qp)['j'] ?? 0;

        // Get daily sales
        $qs = mysqli_query($connect, "SELECT SUM(jumlah_butir) as j FROM transaksi WHERE DATE(tanggal) = '$tgl'");
        $data_penjualan[] = mysqli_fetch_assoc($qs)['j'] ?? 0;
    }

    // PBI036: Laporan Detail Penjualan (Filter)
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'harian';
    $where_sql = "WHERE DATE(tanggal) = CURDATE()";
    
    if ($filter == 'mingguan') {
        $where_sql = "WHERE tanggal >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    } elseif ($filter == 'bulanan') {
        $where_sql = "WHERE tanggal >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    }

    $res_laporan = mysqli_query($connect, "SELECT * FROM transaksi $where_sql ORDER BY tanggal DESC");
    ?>

    <nav class="top-nav no-print container-mobile">
            <div class="brand-name">SALT IT <span class="text-primary">OWNER</span></div>
        </nav>
\
    <div class="container-mobile">
        <!-- Export -->
        <div class="d-flex gap-3">
                <!-- PBI037: Export PDF -->
                <button onclick="window.print()" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm border">
                    <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>PDF
                </button>
                <!-- PBI037: Export XLS -->
                <button onclick="exportToExcel()" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm border">
                    <i class="bi bi-file-earmark-excel-fill text-success me-1"></i>XLS
                </button>
            </div>

        <!-- Stats Grid -->
        <div class="stats-grid no-print">
            <div class="card-stat">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-egg-fill"></i>
                </div>
                <div class="stat-value"><?php echo number_format($total_produksi, 0, ',', '.'); ?></div>
                <div class="stat-label">Total Produksi</div>
            </div>
            <div class="card-stat">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cart-check-fill"></i>
                </div>
                <div class="stat-value"><?php echo number_format($total_terjual, 0, ',', '.'); ?></div>
                <div class="stat-label">Total Terjual</div>
            </div>
        </div>

        <div class="section-card no-print">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Akumulasi Pendapatan</div>
                    <div class="stat-value text-primary" style="font-size: 1.5rem;">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></div>
                </div>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning m-0" style="width: 55px; height: 55px; font-size: 1.8rem;">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>

        <!-- PBI035: Grafik Visual Tren -->
        <div class="section-card">
            <div class="section-title">
                <i class="bi bi-graph-up-arrow text-primary"></i> Tren Produksi & Penjualan
            </div>
            <div style="height: 220px;">
                <canvas id="ownerChart"></canvas>
            </div>
        </div>

        <!-- PBI036: Laporan Detail Penjualan -->
        <div class="section-card">
            <div class="section-title no-print">
                <i class="bi bi-file-earmark-spreadsheet text-primary"></i> Laporan Transaksi
            </div>

            <div class="filter-container no-print">
                <a href="?filter=harian" class="filter-btn <?php echo $filter == 'harian' ? 'active' : ''; ?>">Harian</a>
                <a href="?filter=mingguan" class="filter-btn <?php echo $filter == 'mingguan' ? 'active' : ''; ?>">Mingguan</a>
                <a href="?filter=bulanan" class="filter-btn <?php echo $filter == 'bulanan' ? 'active' : ''; ?>">Bulanan</a>
            </div>

            <div class="table-responsive">
                <table class="table custom-table" id="reportTable">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Toko</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($res_laporan) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($res_laporan)): ?>
                            <tr>
                                <td class="text-muted" style="font-size: 0.7rem;">
                                    <?php echo date('d/m/y', strtotime($row['tanggal'])); ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $row['platform'] == 'online' ? 'bg-primary' : 'bg-info'; ?> rounded-pill" style="font-size: 0.6rem;">
                                        <?php echo strtoupper($row['platform']); ?>
                                    </span>
                                </td>
                                <td class="fw-bold"><?php echo $row['jumlah_butir']; ?></td>
                                <td class="fw-bold">Rp<?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data transaksi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // PBI035: Inisialisasi Grafik Visual
        const ctx = document.getElementById('ownerChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [
                    {
                        label: 'Produksi',
                        data: <?php echo json_encode($data_produksi); ?>,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2
                    },
                    {
                        label: 'Penjualan',
                        data: <?php echo json_encode($data_penjualan); ?>,
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { 
                            usePointStyle: true, 
                            padding: 15,
                            font: { size: 10, weight: '600' }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: { font: { size: 9 } }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { size: 9 } }
                    }
                }
            }
        });

        // PBI037: Fitur Ekspor Excel (CSV Format)
        function exportToExcel() {
            let table = document.getElementById("reportTable");
            let rows = Array.from(table.rows);
            let csvContent = rows.map(r => Array.from(r.cells).map(c => c.innerText).join(",")).join("\n");
            
            let blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
            let url = URL.createObjectURL(blob);
            let link = document.createElement("a");
            link.setAttribute("href", url);
            link.setAttribute("download", "Laporan_SaltIt_Owner_<?php echo $filter; ?>.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>