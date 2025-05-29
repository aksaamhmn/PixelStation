<?php
session_start();
include '../server/connection.php';

if (isset($_GET['id_room'])) {
    $id_room = (int)$_GET['id_room'];
    
    // Ambil data gambar sebelum dihapus
    $query = mysqli_query($conn, "SELECT gambar FROM room WHERE id_room='$id_room'");
    $data = mysqli_fetch_assoc($query);
    
    // Hapus data dari database
    $sql = "DELETE FROM room WHERE id_room = $id_room";
    
    if (mysqli_query($conn, $sql)) {
        // Hapus file gambar jika ada
        if (!empty($data['gambar']) && file_exists("../assets/images/" . $data['gambar'])) {
            unlink("../assets/images/" . $data['gambar']);
        }
        $_SESSION['alert'] = "delete_success";
    } else {
        $_SESSION['alert'] = "delete_fail";
    }
} else {
    $_SESSION['alert'] = "delete_fail";
}

header("Location: dataRoom.php");
exit;
?>