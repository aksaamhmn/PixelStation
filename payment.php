<?php
session_start();
include 'server/connection.php';

// Redirect jika user belum login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Pastikan id_user sudah ada di session
if (!isset($_SESSION['id_user']) && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $queryUser = "SELECT id_user FROM users WHERE username = '$username' LIMIT 1";
    $resultUser = mysqli_query($conn, $queryUser);
    if ($resultUser && $userRow = mysqli_fetch_assoc($resultUser)) {
        $_SESSION['id_user'] = $userRow['id_user'];
    }
}

// Proses data dari form reservasi - HANYA SIMPAN KE SESSION, BUKAN DATABASE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_room = $_POST['id_room'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $telp = $_POST['telp'];
    $reservation_date = $_POST['reservation_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $room_type = $_POST['room_type'];
    $room_name = $_POST['room_name'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];
    $id_user = $_SESSION['id_user'];

    // Validasi overlap waktu sebelum melanjutkan
    $checkOverlap = "
        SELECT COUNT(*) as count
        FROM reservasi r
        JOIN payments p ON r.id_payments = p.id_payments
        WHERE r.id_room = ? 
        AND r.reservation_date = ?
        AND p.payment_status NOT IN ('rejected', 'cancelled')
        AND (
            (r.start_time < ? AND r.end_time > ?) OR
            (r.start_time < ? AND r.end_time > ?) OR
            (r.start_time >= ? AND r.end_time <= ?)
        )";
    
    $stmt = mysqli_prepare($conn, $checkOverlap);
    mysqli_stmt_bind_param($stmt, 'isssssss', 
        $id_room, $reservation_date, 
        $end_time, $start_time,
        $end_time, $start_time,
        $start_time, $end_time
    );
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $overlap = mysqli_fetch_assoc($result);

    if ($overlap['count'] > 0) {
        echo "<script>
            alert('Waktu yang dipilih sudah dipesan oleh orang lain. Silakan pilih waktu lain.');
            window.history.back();
        </script>";
        exit;
    }

    // SIMPAN DATA KE SESSION SAJA, BUKAN KE DATABASE
    $_SESSION['reservation_data'] = [
        'id_room' => $id_room,
        'id_user' => $id_user,
        'nama' => $nama,
        'username' => $username,
        'telp' => $telp,
        'reservation_date' => $reservation_date,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'room_type' => $room_type,
        'room_name' => $room_name,
        'duration' => $duration,
        'price' => $price
    ];
    
    // Redirect ke halaman pembayaran
    header('Location: payment_form.php');
    exit;
        
} else {
    // Jika tidak ada data POST, redirect ke halaman reservasi
    header('Location: reservasi.php');
    exit;
}
?>