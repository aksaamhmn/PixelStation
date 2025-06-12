<?php
session_start();
include '../server/connection.php';

// Proses insert jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $conn->real_escape_string($_POST['nama']);

    // Proses upload gambar
    $gambar      = $_FILES['gambar']['name'];
    $tmp_name    = $_FILES['gambar']['tmp_name'];
    $folder      = "../assets/images/"; 

    // Validasi ekstensi file gambar
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
    $ext = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) {
        $_SESSION['alert'] = 'ext';
        header("Location: dataTrending.php");
        exit;
    }

    // Rename file agar unik
    $new_gambar = uniqid('trending_', true) . '.' . $ext;

    if (move_uploaded_file($tmp_name, $folder . $new_gambar)) {
        $sql = "INSERT INTO trending_games (nama, gambar) VALUES ('$nama', '$new_gambar')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['alert'] = 'success';
            header("Location: dataTrending.php");
            exit;
        } else {
            $_SESSION['alert'] = 'fail';
            header("Location: dataTrending.php");
            exit;
        }
    } else {
        $_SESSION['alert'] = 'uploadfail';
        header("Location: dataTrending.php");
        exit;
    }
}
?>