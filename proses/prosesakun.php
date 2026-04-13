<?php
include __DIR__ . '/../koneksi.php';

// Ambil data dari form
$id       = isset($_POST['id']) ? $_POST['id'] : '';
$aksi     = $_POST['aksi'];
$email    = mysqli_real_escape_string($connect, $_POST['email']);
$password = $_POST['password'];
$role     = $_POST['role'];

if ($aksi == "tambah") {
    // Validasi apakah email sudah terdaftar
    $cek_email = mysqli_query($connect, "SELECT * FROM user WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        echo "<script>alert('Email sudah digunakan!'); window.location='../pages/kelola_akun.php';</script>";
        exit;
    }

    // Query Tambah (Password tidak dienkripsi sesuai database Anda yang ada)
    $query = "INSERT INTO user (email, password, role) VALUES ('$email', '$password', '$role')";
    
} else if ($aksi == "edit") {
    // Jika password diisi, maka update password juga. Jika tidak, update email & role saja.
    if (!empty($password)) {
        $query = "UPDATE user SET email = '$email', password = '$password', role = '$role' WHERE id = '$id'";
    } else {
        $query = "UPDATE user SET email = '$email', role = '$role' WHERE id = '$id'";
    }
}

// Eksekusi Query
if (mysqli_query($connect, $query)) {
    header("location: ../page/kelolaakun.php");
    exit();
} else {
    echo "Gagal memproses data: " . mysqli_error($connect);
}
?>