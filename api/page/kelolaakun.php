<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salt It</title>
    <link rel="stylesheet" href="../css/stylepage2.css">
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
    include '../koneksi.php';
    session_start();
        if (!isset($_SESSION['status'])) {
            header("location:../login/index.php");
            exit();
        }

    // Ambil semua data user
    $query_user = "SELECT * FROM user ORDER BY role ASC";
    $result_user = mysqli_query($connect, $query_user);
    ?>

        <nav class="top-nav container-mobile">
            <a href="dashboard_inventaris.php" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <h6 class="m-0 fw-bold text-center">Kelola Akun</h6>
            <div style="width: 24px;"></div>
        </nav>

    <div class="container-mobile">
        <div class="header-section">
            <button class="btn-add-user" data-bs-toggle="modal" data-bs-target="#modalUser" onclick="resetForm()">
                <i class="bi bi-person-plus-fill"></i>
                Tambah Akun Baru
            </button>

            <h6 class="text-secondary fw-bold small text-uppercase mb-3" style="letter-spacing: 0.05em;">Daftar Pengguna</h6>

            <?php if ($result_user && mysqli_num_rows($result_user) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result_user)): ?>
                    <div class="user-card shadow-sm">
                        <div class="avatar-box">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['email']); ?></div>
                            <span class="role-badge badge-<?php echo $row['role']; ?>">
                                <?php echo $row['role']; ?>
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <?php if ($row['role'] !== 'owner'): ?>
                                <button class="action-btn btn-edit" 
                                        onclick="editUser('<?php echo $row['id']; ?>', '<?php echo $row['email']; ?>', '<?php echo $row['role']; ?>')"
                                        data-bs-toggle="modal" data-bs-target="#modalUser">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                
                                <a href="../proses/hapus_user.php?id=<?php echo $row['id']; ?>" 
                                class="action-btn btn-delete" 
                                onclick="return confirm('Hapus akun ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            <?php else: ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Belum ada user terdaftar.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Tambah/Edit User -->
    <div class="modal fade" id="modalUser" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered px-3">
            <div class="modal-content shadow-lg">
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0" id="modalTitle">Tambah Akun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="../proses/prosesakun.php" method="POST">
                        <input type="hidden" name="id" id="userId">
                        <input type="hidden" name="aksi" id="formAksi" value="tambah">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Alamat Email</label>
                            <input type="email" name="email" id="userEmail" class="form-control form-control-lg" placeholder="contoh@gmail.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Password</label>
                            <input type="password" name="password" id="userPass" class="form-control form-control-lg" placeholder="Masukkan password baru" required>
                            <div id="passHint" class="form-text small" style="display:none;">Kosongkan jika tidak ingin mengubah password.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Role Akses</label>
                            <select name="role" id="userRole" class="form-select form-control-lg" required>
                                <option value="inventaris">Inventaris (Staf)</option>
                                <option value="owner">Owner (Pemilik)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-4 fw-bold shadow-sm">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function resetForm() {
            document.getElementById('modalTitle').innerText = "Tambah Akun";
            document.getElementById('formAksi').value = "tambah";
            document.getElementById('userId').value = "";
            document.getElementById('userEmail').value = "";
            document.getElementById('userPass').required = true;
            document.getElementById('passHint').style.display = "none";
        }

        function editUser(id, email, role) {
            document.getElementById('modalTitle').innerText = "Edit Akun";
            document.getElementById('formAksi').value = "edit";
            document.getElementById('userId').value = id;
            document.getElementById('userEmail').value = email;
            document.getElementById('userRole').value = role;
            document.getElementById('userPass').required = false;
            document.getElementById('passHint').style.display = "block";
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>