<?php
    session_start();
    include '../server/connection.php';

    $id = $_GET['id'];
    
    // Ambil nama file gambar sebelum menghapus record
    $query = "SELECT gambar FROM trending_games WHERE id = $id";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $gambar = $row['gambar'];
        
        // Hapus file gambar dari folder jika ada
        if (!empty($gambar) && file_exists("../assets/images/" . $gambar)) {
            unlink("../assets/images/" . $gambar);
        }
    }
    
    // Hapus record dari database
    $sql = "DELETE FROM trending_games WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['alert'] = "delete_success";
    } else {
        $_SESSION['alert'] = "delete_fail";
    }
    header("Location: dataTrending.php");
    exit;
?>