<?php
session_start();
include '../server/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_room = (int)$_POST['id_room'];
    $section_room = mysqli_real_escape_string($conn, $_POST['section_room']);
    $type_room = mysqli_real_escape_string($conn, $_POST['type_room']);
    $harga = (int)$_POST['harga'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // Ambil data lama untuk gambar
    $query = mysqli_query($conn, "SELECT gambar FROM room WHERE id_room='$id_room'");
    $data = mysqli_fetch_assoc($query);
    $gambar_lama = $data['gambar'];

    // Proses upload gambar jika ada file baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        $ext = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowed_ext)) {
            $nama_gambar_baru = uniqid('room_', true) . '.' . $ext;
            move_uploaded_file($tmp, "../assets/images/" . $nama_gambar_baru);

            // Hapus gambar lama jika ada dan berbeda
            if (!empty($gambar_lama) && file_exists("../assets/images/" . $gambar_lama)) {
                unlink("../assets/images/" . $gambar_lama);
            }
        } else {
            // Ekstensi tidak valid
            $_SESSION['alert'] = "ext";
            header("Location: dataRoom.php");
            exit;
        }
    } else {
        // Tidak upload gambar baru, gunakan gambar lama
        $nama_gambar_baru = $gambar_lama;
    }

    // Update data room
    $sql = "UPDATE room SET 
                section_room='$section_room',
                type_room='$type_room',
                harga='$harga',
                keterangan='$keterangan',
                gambar='$nama_gambar_baru'
            WHERE id_room='$id_room'";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['alert'] = "edit_success";
        header("Location: dataRoom.php");
    } else {
        $_SESSION['alert'] = "edit_fail";
        header("Location: dataRoom.php");
    }
    exit;
}
?>