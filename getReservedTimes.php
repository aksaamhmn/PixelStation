<?php
include 'server/connection.php';

// Ambil parameter tanggal dari query string
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Validasi format tanggal
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date format']);
    exit;
}

// Query untuk mengambil waktu terpakai berdasarkan tanggal
// HANYA dari reservasi dengan payment_status yang bukan 'rejected'
$reservedTimes = [];
$sqlReserved = "
    SELECT r.id_room, r.start_time, r.end_time
    FROM reservasi r
    JOIN payments p ON r.id_payments = p.id_payments
    WHERE p.payment_status NOT IN ('rejected', 'expired')
      AND r.reservation_date = ?";

$stmt = mysqli_prepare($conn, $sqlReserved);
mysqli_stmt_bind_param($stmt, 's', $date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id_room'];
    if (!isset($reservedTimes[$id])) {
        $reservedTimes[$id] = [];
    }
    $reservedTimes[$id][] = [
        'start' => $row['start_time'], 
        'end' => $row['end_time']
    ];
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

// Return JSON response
header('Content-Type: application/json');
echo json_encode($reservedTimes);
?>