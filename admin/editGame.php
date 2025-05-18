<?php
session_start();
include '../server/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_game = $_POST['id_game'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $tahun_rilis = mysqli_real_escape_string($conn, $_POST['tahun_rilis']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $mode_game = mysqli_real_escape_string($conn, $_POST['mode_game']);

    // Ambil data lama untuk gambar
    $query = mysqli_query($conn, "SELECT gambar FROM games WHERE id_game='$id_game'");
    $data = mysqli_fetch_assoc($query);
    $gambar_lama = $data['gambar'];

    // Proses upload gambar jika ada file baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        $ext = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowed_ext)) {
            $nama_gambar_baru = uniqid('game_', true) . '.' . $ext;
            move_uploaded_file($tmp, "../assets/images/" . $nama_gambar_baru);

            // Hapus gambar lama jika ada dan berbeda
            if (!empty($gambar_lama) && file_exists("../assets/images/" . $gambar_lama)) {
                unlink("../assets/images/" . $gambar_lama);
            }
        } else {
            // Ekstensi tidak valid
            $_SESSION['alert'] = "Gagal: Ekstensi gambar tidak valid";
            header("Location: dataGame.php");
            exit;
        }
    } else {
        // Tidak upload gambar baru, gunakan gambar lama
        $nama_gambar_baru = $gambar_lama;
    }

    // Update data game
    $sql = "UPDATE games SET 
                nama='$nama',
                tahun_rilis='$tahun_rilis',
                kategori='$kategori',
                mode_game='$mode_game',
                gambar='$nama_gambar_baru'
            WHERE id_game='$id_game'";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['alert'] = "edit_success";
        header("Location: dataGame.php");
    } else {
        $_SESSION['alert'] = "edit_fail";
        header("Location: dataGame.php");
    }
    exit;
}
?>