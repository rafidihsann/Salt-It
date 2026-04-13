<?php 
    include __DIR__ . '/../koneksi.php';
	include __DIR__ . '/../proses/cek_login.php';
	if ($_SESSION['role'] !== 'inventaris' && $_SESSION['role'] !== 'owner') {
    header("location:../login/login.php?pesan=hak_akses");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salt It</title>
    <link rel="stylesheet" href="../css/stylepage4.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--soft-bg);
            color: #334155;
        }
    </style>
</head>
<body>

    <?php
    // 1. Fungsi Mengambil Sisa Stok Telur Mentah Lolos QC (Real-time)
    // Rumus: (Jumlah Masuk - Tidak Lolos QC) - Jumlah Keluar
    $query_stok = "SELECT 
        SUM(CASE WHEN jenis = 'masuk' THEN (jumlah - tidak_lolos) ELSE 0 END) - 
        SUM(CASE WHEN jenis = 'keluar' THEN jumlah ELSE 0 END) as stok_akhir 
        FROM stokmentah";
    
    $result_stok = mysqli_query($connect, $query_stok);
    $data_stok = mysqli_fetch_assoc($result_stok);
    $current_stok = $data_stok['stok_akhir'] ?? 0;
    ?>

    <nav class="top-nav container-mobile">
        <a href="telurmentah.php" class="btn-back">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali</span>
        </a>
        <h6 class="m-0 fw-bold ms-3">Input Produksi Harian</h6>
    </nav>

    <div class="container-mobile">
        <!-- Informasi Stok Real-time -->
        <div class="stok-info-card shadow-sm">
            <div>
                <div class="small text-muted fw-bold text-uppercase">Stok Mentah Saat Ini</div>
                <div class="stok-value" id="displayStok"><?php echo number_format($current_stok, 0, ',', '.'); ?> <small class="text-muted fs-6">Butir</small></div>
            </div>
            <div class="icon-box bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                <i class="bi bi-box-seam fs-4"></i>
            </div>
        </div>

        <!-- Form Produksi -->
        <div class="form-section">
            <div class="card-form">
                <form action="../proses/inputproduksi.php" method="POST" id="formProduksi">
                    
                    <div class="mb-3">
                        <label class="form-label">Tanggal Produksi</label>
                        <input type="date" name="waktu" class="form-control form-control-lg" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telur Berhasil (Produk Jadi)</label>
                        <div class="input-group">
                            <input type="number" name="berhasil" id="inputBerhasil" class="form-control form-control-lg" placeholder="0" required oninput="calculateTotal()">
                            <span class="input-group-text">Butir</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telur Gagal (Rusak/Pecah)</label>
                        <div class="input-group">
                            <input type="number" name="gagal" id="inputGagal" class="form-control form-control-lg" placeholder="0" oninput="calculateTotal()">
                            <span class="input-group-text">Butir</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control form-control-lg" rows="2" placeholder="Misal: Batch Pagi / Catatan khusus"></textarea>
                    </div>

                    <!-- Ringkasan Kalkulasi -->
                    <div class="summary-box">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small text-secondary">Total Bahan Digunakan:</span>
                            <span class="fw-bold" id="totalUsed">0 Butir</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small text-secondary">Estimasi Sisa Stok:</span>
                            <span class="fw-bold text-primary" id="finalStock"><?php echo number_format($current_stok, 0, ',', '.'); ?></span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary text-white shadow-sm container" id="btnSubmit"
                        onclick="return confirm('Apakah Anda yakin ingin menyimpan?')">
                        <i class="bi bi-check-circle-fill me-2"></i> Konfirmasi Produksi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const initialStok = <?php echo $current_stok; ?>;

        function calculateTotal() {
            const berhasil = parseInt(document.getElementById('inputBerhasil').value) || 0;
            const gagal = parseInt(document.getElementById('inputGagal').value) || 0;
            const total = berhasil + gagal;
            const sisa = initialStok - total;

            document.getElementById('totalUsed').innerText = total + " Butir";
            document.getElementById('finalStock').innerText = sisa.toLocaleString('id-ID');

            // Validasi: Jangan biarkan input melebihi stok yang ada
            const btn = document.getElementById('btnSubmit');
            if (total > initialStok) {
                document.getElementById('totalUsed').classList.add('text-danger');
                btn.disabled = true;
                btn.innerText = "Stok Tidak Mencukupi";
                btn.style.backgroundColor = "#94a3b8";
            } else {
                document.getElementById('totalUsed').classList.remove('text-danger');
                btn.disabled = false;
                btn.innerText = "Konfirmasi Produksi";
                btn.style.backgroundColor = "#052c65";
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>