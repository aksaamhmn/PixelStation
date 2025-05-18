<?php
    session_start();
    include '../server/connection.php';

    $id = $_GET['id_game'];
    $sql = "DELETE FROM games WHERE id_game = $id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['alert'] = "delete_success";
    } else {
        $_SESSION['alert'] = "delete_fail";
    }
    header("Location: dataGame.php");
    exit;
?>
