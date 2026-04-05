<?php
session_start();
include __DIR__ . '/../koneksi.php';

// Pastikan ID ada
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM user WHERE id = '$id'";

    if (mysqli_query($connect, $query)) {
        header("Location: ../page/kelolaakun.php");
    } else {
        echo "Gagal menghapus data: " . mysqli_error($connect);
    }
} else {
    header("Location: ../page/kelolaakun.php");
}
?>