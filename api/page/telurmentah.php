<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salt It</title>
    <link rel="stylesheet" href="../css/stylepage.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<style>
    body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #f8fafc;
    min-height: 100vh;
    }
</style>
<body>
<?php
    include '../koneksi.php';
    session_start();
        if (!isset($_SESSION['status'])) {
            header("location:../login/index.php");
            exit();
        }

    // Query Hitung Stok Real-time
    // Stok Akhir = (Semua jumlah masuk - semua tidak lolos qc) - semua jumlah keluar
    $query_total = "SELECT 
        SUM(CASE WHEN jenis = 'masuk' THEN (jumlah - tidak_lolos) ELSE 0 END) - 
        SUM(CASE WHEN jenis = 'keluar' THEN jumlah ELSE 0 END) as stok_akhir,
        SUM(tidak_lolos) as total_bad_qc
        FROM stokmentah";

    $result_stok = mysqli_query($connect, $query_total);
    $data_stok = mysqli_fetch_assoc($result_stok);
    $total_stok = $data_stok['stok_akhir'] ?? 0;
    $total_tidak_lolos = $data_stok['total_bad_qc'] ?? 0;
    $query_history = "SELECT * FROM stokmentah ORDER BY id DESC LIMIT 5";
    $result_history = mysqli_query($connect, $query_history);
    ?>

        <nav class="top-nav container-mobile">
        <a href="dashboard_inventaris.php" class="btn-back">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali</span>
        </a>
        <h6 class="m-0 fw-bold ms-3">Kelola Stok Telur Bebek</h6>
    </nav>

    <div class="container-mobile">
        
        <div class="container my-3">
            <div class="d-flex justify-content-end">
                <a href="produksi.php" class="btn btn-success px-1 py-3">
                    <i class="bi bi-plus-lg"></i> Input Produksi
                </a>
            </div>
        </div>

        <div class="stok-card text-center">
            <span class="opacity-75 small">Total Stok Layak (QC Pass)</span>
            <h1 class="display-4 fw-bold m-0"><?php echo number_format($total_stok, 0, ',', '.'); ?></h1>
            <span class="fs-5">Butir</span>
        </div>

        <div class="stok-card2 text-center">
            <span class="opacity-75 small">Total Butir Tidak Layak</span>
            <h1 class="display-4 fw-bold m-0"><?php echo number_format($total_tidak_lolos, 0, ',', '.'); ?></h1>
            <span class="fs-5">Butir</span>
        </div>

        <h6 class="fw-bold text-secondary mb-3 text-uppercase small">Kelola Stok</h6>
        
        <!-- Tombol Tambah -->
        <div class="action-card" data-bs-toggle="modal" data-bs-target="#modalStok" onclick="setMode('masuk')">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-plus"><i class="bi bi-plus-lg"></i></div>
                <div>
                    <div class="fw-bold">Tambah Stok</div>
                    <div class="small text-muted">Input stok telur masuk baru</div>
                </div>
            </div>
            <i class="bi bi-chevron-right text-muted"></i>
        </div>

        <!-- Tombol Kurang -->
        <div class="action-card" data-bs-toggle="modal" data-bs-target="#modalStok" onclick="setMode('keluar')">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-minus"><i class="bi bi-dash-lg"></i></div>
                <div>
                    <div class="fw-bold">Kurangi Stok</div>
                    <div class="small text-muted">Produksi, pecah, atau susut</div>
                </div>
            </div>
            <i class="bi bi-chevron-right text-muted"></i>
        </div>

        <!-- Riwayat -->
        <div class="mt-4">
            <h6 class="fw-bold text-secondary text-uppercase small mb-3">Aktivitas Terakhir</h6>
            <div class="bg-white p-3 rounded-4 border shadow-sm">
                <?php if ($result_history && mysqli_num_rows($result_history) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result_history)): ?>
                        <div class="history-item">
                            <div class="<?php echo $row['jenis'] == 'masuk' ? 'text-success' : 'text-danger'; ?> me-3">
                                <i class="bi <?php echo $row['jenis'] == 'masuk' ? 'bi-plus-circle-fill' : 'bi-dash-circle-fill'; ?> fs-5"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold small"><?php echo htmlspecialchars($row['keterangan'] ?: 'Update Stok'); ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    <?php echo date('d M Y', strtotime($row['waktu'])); ?> &bull; 
                                    <span class="text-dark fw-bold">
                                        <?php echo ($row['jenis'] == 'masuk' ? '+' : '-'); ?>
                                        <?php echo number_format($row['jumlah'], 0, ',', '.'); ?> Butir
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-3 text-muted small">Belum ada data di database.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Universal -->
    <div class="modal fade" id="modalStok" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered px-3">
        <div class="modal-content">
            <div class="modal-body p-4">
                <h5 class="fw-bold mb-3" id="modalTitle">Update Stok</h5>
                <form action="../proses/prosesinventory.php" method="POST">
                    <input type="hidden" name="jenis" id="inputJenis" value="masuk">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold" id="labelJumlah">Jumlah Datang (Butir)</label>
                        <input type="number" name="jumlah" class="form-control form-control-lg" required>
                    </div>

                    <div id="qcField" class="mb-3">
                        <label class="form-label small fw-bold text-danger">Tidak Lolos QC (Butir)</label>
                        <input type="number" name="tidak_lolos" id="inputQC" class="form-control form-control-lg" value="0">
                        <div class="form-text" style="font-size: 0.7rem;">Jumlah ini akan otomatis memotong stok masuk.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keterangan</label>
                        <input type="text" name="keterangan" id="inputKet" class="form-control form-control-lg">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Tanggal</label>
                        <input type="date" name="waktu" class="form-control form-control-lg" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <button type="submit" id="btnSubmit" class="btn btn-primary w-100 py-3 rounded-4 fw-bold">Simpan Data</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function setMode(mode) {
    const title = document.getElementById('modalTitle');
    const inputJenis = document.getElementById('inputJenis');
    const btn = document.getElementById('btnSubmit');
    const qcField = document.getElementById('qcField');
    const inputQC = document.getElementById('inputQC');
    const labelJumlah = document.getElementById('labelJumlah');

    if(mode === 'masuk') {
        title.innerText = "Input Penambahan Stok";
        title.className = "fw-bold mb-3 text-success";
        labelJumlah.innerText = "Jumlah Datang (Butir)";
        inputJenis.value = "masuk";
        btn.className = "btn btn-success w-100 py-3 rounded-4 fw-bold";
        qcField.style.display = "block"; // Tampilkan input QC
        inputQC.value = "0";
    } else {
        title.innerText = "Input Pengurangan Stok";
        title.className = "fw-bold mb-3 text-danger";
        labelJumlah.innerText = "Jumlah Keluar (Butir)";
        inputJenis.value = "keluar";
        btn.className = "btn btn-danger w-100 py-3 rounded-4 fw-bold";
        qcField.style.display = "none"; // Sembunyikan input QC
        inputQC.value = "0"; 
    }
}
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>