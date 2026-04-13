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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/stylepage0.css">
</head>
<style>
    body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #f8fafc;
    min-height: 100vh;
    display: flex;
    align-items: center;
    }
</style>
<body>
    <div class="container-mobile d-flex flex-column justify-content-center  min-vh-100">
        <div class="welcome-section">
            <div class="logo-placeholder">
                <i class="bi bi-egg-fill"></i>
            </div>
            <h4 class="fw-bold text-dark">Halo!</h4>
            <p class="text-muted">Pilih menu untuk mulai bekerja hari ini.</p>
        </div>

        <!-- Menu Kelola Telur -->
        <a href="telurmentah.php" class="menu-card">
            <div class="icon-circle bg-inventory">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="menu-text">
                <h6>Kelola Stok Telur Bebek</h6>
                <p>Input stok masuk & produksi harian</p>
            </div>
            <i class="bi bi-chevron-right ms-auto text-muted"></i>
        </a>

        <!-- Menu Kelola Alokasi Telur -->
        <a href="alokasi.php" class="menu-card">
            <div class="icon-circle bg-inventory">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="menu-text">
                <h6>Kelola Alokasi Stok Telur Asin</h6>
                <p>Input alokasi stok telur asin untuk toko online & offline</p>
            </div>
            <i class="bi bi-chevron-right ms-auto text-muted"></i>
        </a>

        <!-- Menu Kelola Akun -->
        <a href="kelolaakun.php" class="menu-card">
            <div class="icon-circle bg-account">
                <i class="bi bi-person-gear"></i>
            </div>
            <div class="menu-text">
                <h6>Kelola Akun</h6>
                <p>Update password & informasi profil</p>
            </div>
            <i class="bi bi-chevron-right ms-auto text-muted"></i>
        </a>

        <!-- Tombol Logout -->
        <a href="../login/logout.php" class="menu-card mt-4" style="border-style: dashed;" 
            onclick="return confirm('Apakah Anda yakin ingin keluar dari aplikasi?')">
                <div class="icon-circle bg-logout">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <div class="menu-text">
                    <h6 class="text-danger">Logout</h6>
                    <p>Selesaikan sesi kerja Anda</p>
                </div>
        </a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>