<?php
session_start();
include '../server/connection.php';

// Proses insert jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section_room = $conn->real_escape_string($_POST['section_room']);
    $type_room    = $conn->real_escape_string($_POST['type_room']);
    $harga        = (int)$_POST['harga'];
    $keterangan    = $conn->real_escape_string($_POST['keterangan']);

    // Proses upload gambar
    $gambar      = $_FILES['gambar']['name'];
    $tmp_name    = $_FILES['gambar']['tmp_name'];
    $folder      = "../assets/images/"; 

    // Validasi ekstensi file gambar
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) {
        $_SESSION['alert'] = 'ext';
        header("Location: dataRoom.php");
        exit;
    }

    // Rename file agar unik
    $new_gambar = uniqid('room_', true) . '.' . $ext;

    if (move_uploaded_file($tmp_name, $folder . $new_gambar)) {
        $sql = "INSERT INTO room (section_room, type_room, harga, keterangan, gambar) VALUES ('$section_room', '$type_room', '$harga', '$keterangan', '$new_gambar')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['alert'] = 'success';
            header("Location: dataRoom.php");
            exit;
        } else {
            $_SESSION['alert'] = 'fail';
            header("Location: dataRoom.php");
            exit;
        }
    } else {
        $_SESSION['alert'] = 'uploadfail';
        header("Location: dataRoom.php");
        exit;
    }
}
?>