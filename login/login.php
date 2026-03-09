<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salt It</title>
    <link rel="stylesheet" href="../css/stylelogin.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<style>
    body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    }
</style>
<body>

    <div class="container-mobile d-flex flex-column justify-content-center  min-vh-100">
        <div class="login-card text-center">
            <h2 class="fw-bold mb-1 text-dark">Selamat Datang</h2>
            <p class="text-muted mb-4 small">Silakan masuk untuk mengelola produksi telur asin.</p>

            <form action="../proses/validasilogin.php" method="POST">
                <!-- Hidden input untuk mengirim role ke proses selanjutnya -->
                <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">
                <div class="mb-3 text-start">
                    <label class="form-label small fw-bold text-secondary">Alamat Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control has-icon" required>
                    </div>
                </div>
                <div class="mb-4 text-start">
                    <label class="form-label small fw-bold text-secondary">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control has-icon" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-login w-100">
                    Masuk Sekarang
                </button>

                <?php
                if(isset($_GET['pesan'])) {
                if ($_GET['pesan'] == "gagal") {
                    ?><div class="alert alert-warning my-3" role="alert">
                        Username atau password salah!
                    </div><?php
                } }
                ?>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>