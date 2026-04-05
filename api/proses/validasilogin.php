<?php 
    session_start();
    include __DIR__ . '/../koneksi.php';

    $email = $_POST['email'];
    $password = $_POST['password'];

    $hasil = $connect->query("SELECT * FROM user WHERE email='$email' AND password='$password'");

    if ($hasil->num_rows > 0) {
        $row = $hasil->fetch_assoc();
        $_SESSION['email'] = $email;
        $_SESSION['password'] = $password;
        $_SESSION['status'] = "login";
        $_SESSION['role'] = $row['role'];

        if ($row['role'] === 'owner') {
        header("location:../page/dashboard_owner.php");
        exit();
        } elseif ($row['role'] === 'inventaris') {
            header("location:../page/dashboard_inventaris.php");
            exit();
        } elseif ($row['role'] === 'online') {
            header("location:../page/dashboard_online.php");
            exit();
        } elseif ($row['role'] === 'offline') {
            header("location:../page/dashboard_offline.php");
            exit();
        }
    }

    else {
            header("location:login.php?pesan=gagal");
        }
?>