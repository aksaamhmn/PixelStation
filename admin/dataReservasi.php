<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('location: adminLogin.php');
    exit;
}
include ('./layout/sidebar.php');
include '../server/connection.php';

// Pagination setup
$limit = 5; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query untuk mengambil semua data reservasi dengan informasi terkait termasuk status pembayaran dengan pagination
$reservations = [];
try {
    // Hitung total data untuk pagination
    $totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM reservasi r
        JOIN room rm ON r.id_room = rm.id_room
        LEFT JOIN payments p ON r.id_payments = p.id_payments");
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result();
    $total_data = $totalResult->fetch_assoc()['total'];
    $total_pages = ceil($total_data / $limit);
    $totalStmt->close();

    // Query data dengan pagination
    $stmt = $conn->prepare("SELECT 
    r.id_reservasi,
    r.nama AS customer_name,
    r.username AS customer_username,
    r.telp AS customer_phone,
    r.reservation_date,
    r.start_time,
    r.end_time,
    r.keterangan_penolakan,
    rm.section_room,
    rm.type_room,
    p.payment_status,
    p.id_payments AS payment_id,
    p.payment_proof,
    p.payment_date,
    p.amount AS price
FROM reservasi r
JOIN room rm ON r.id_room = rm.id_room
LEFT JOIN payments p ON r.id_payments = p.id_payments
ORDER BY r.id_reservasi DESC
LIMIT ? OFFSET ?");
    
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    echo "<script>console.error('Error query: " . $e->getMessage() . "');</script>";
}

// Query untuk statistik (tetap mengambil semua data untuk perhitungan)
$allReservations = [];
try {
    $allStmt = $conn->prepare("SELECT 
    r.id_reservasi,
    r.reservation_date,
    p.payment_status,
    p.amount AS price
FROM reservasi r
JOIN room rm ON r.id_room = rm.id_room
LEFT JOIN payments p ON r.id_payments = p.id_payments");
    
    $allStmt->execute();
    $allResult = $allStmt->get_result();
    
    while ($row = $allResult->fetch_assoc()) {
        $allReservations[] = $row;
    }
    $allStmt->close();
} catch (Exception $e) {
    echo "<script>console.error('Error query statistics: " . $e->getMessage() . "');</script>";
}

// Function untuk memformat tanggal ke format Indonesia
function formatDate($date) {
    return date('d M Y', strtotime($date));
}

// Function untuk memformat waktu
function formatTime($time) {
    return date('H:i', strtotime($time));
}

// Process payment confirmation if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_payment'])) {
    $payment_id = $_POST['payment_id'];
    try {
        $updateStmt = $conn->prepare("UPDATE payments SET payment_status = 'confirmed' WHERE id_payments = ?");
        $updateStmt->bind_param("i", $payment_id);
        $updateStmt->execute();
        if ($updateStmt->affected_rows > 0) {
            $swalType = "success";
            $swalMessage = "Pembayaran berhasil dikonfirmasi!";
        } else {
            $swalType = "error";
            $swalMessage = "Gagal mengkonfirmasi pembayaran!";
        }
        $updateStmt->close();
    } catch (Exception $e) {
        $swalType = "error";
        $swalMessage = "Error: " . $e->getMessage();
    }
}

// Process payment rejection if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reject_payment'])) {
    $payment_id = $_POST['payment_id'];
    $reservation_id = $_POST['reservation_id'];
    $reject_reason = $_POST['reject_reason'];
    
    try {
        // Update payment status
        $updatePaymentStmt = $conn->prepare("UPDATE payments SET payment_status = 'rejected' WHERE id_payments = ?");
        $updatePaymentStmt->bind_param("i", $payment_id);
        $updatePaymentStmt->execute();
        
        // Update reservation with rejection reason
        $updateReservationStmt = $conn->prepare("UPDATE reservasi SET keterangan_penolakan = ? WHERE id_reservasi = ?");
        $updateReservationStmt->bind_param("si", $reject_reason, $reservation_id);
        $updateReservationStmt->execute();
        
        if ($updatePaymentStmt->affected_rows > 0 || $updateReservationStmt->affected_rows > 0) {
            $swalType = "info";
            $swalMessage = "Pembayaran berhasil ditolak dengan alasan!";
        } else {
            $swalType = "error";
            $swalMessage = "Gagal menolak pembayaran!";
        }
        
        $updatePaymentStmt->close();
        $updateReservationStmt->close();
    } catch (Exception $e) {
        $swalType = "error";
        $swalMessage = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Reservations</title>
    <link rel="shortcut icon" href="../dist/assets/compiled/svg/profile.svg" type="image/x-icon">
    <link rel="stylesheet" href="../dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="../dist/assets/compiled/css/app-dark.css">
    <script src="https://kit.fontawesome.com/5f166431bc.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none"><i class="bi bi-justify fs-3"></i></a>
            </header>
            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Reservations</h3>
                            <p class="text-subtitle text-muted">All reservations data available here</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="adminDashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Reservations</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                
                <!-- ALERT PHP DIHAPUS, SEMUA NOTIFIKASI PAKAI SWEETALERT -->

                <!-- Summary Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Reservations</h5>
                                <h3 class="text-primary"><?= count($allReservations) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Revenue</h5>
                                <h3 class="text-success">
                                    <?php 
                                    $totalRevenue = 0;
                                    foreach ($allReservations as $r) {
                                        if (isset($r['payment_status']) && ($r['payment_status'] === 'confirmed' || $r['payment_status'] === 'expired')) {
                                            $totalRevenue += $r['price'];
                                        }
                                    }
                                    echo 'Rp ' . number_format($totalRevenue, 0, ',', '.'); 
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Today's Reservations</h5>
                                <h3 class="text-warning">
                                    <?php 
                                    $today = date('Y-m-d');
                                    $todayReservations = array_filter($allReservations, function($r) use ($today) {
                                        return $r['reservation_date'] == $today;
                                    });
                                    echo count($todayReservations);
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Pending Payments</h5>
                                <h3 class="text-danger">
                                    <?php 
                                    $pendingPayments = array_filter($allReservations, function($r) {
                                        return isset($r['payment_status']) && $r['payment_status'] == 'pending';
                                    });
                                    echo count($pendingPayments);
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="row" id="table-striped">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Reservations Table</h4>
                                </div>
                                <div class="card-content">
                                    <!-- Info pagination -->
                                    <div class="px-4 py-2">
                                        <medium class="text-muted">
                                            Menampilkan <?php echo min($offset + 1, $total_data); ?> - <?php echo min($offset + $limit, $total_data); ?> dari <?php echo $total_data; ?> data
                                        </medium>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>CUSTOMER</th>
                                                    <th>NO TELP</th>
                                                    <th>TIPE RUANGAN</th>
                                                    <th>TANGGAL</th>
                                                    <th>JAM</th>
                                                    <th>TOTAL</th>
                                                    <th>STATUS PEMBAYARAN</th>
                                                    <th>AKSI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($reservations)): ?>
                                                    <tr>
                                                        <td colspan="9" class="text-center">Tidak ada data reservasi</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php 
                                                    $no = $offset + 1; // Mulai nomor sesuai halaman
                                                    foreach ($reservations as $reservation): ?>
                                                        <tr>
                                                            <td><?= $no++ ?></td>
                                                            <td><?= htmlspecialchars($reservation['customer_name']) ?></td>
                                                            <td><?= htmlspecialchars($reservation['customer_phone']) ?></td>
                                                            <td><?= htmlspecialchars($reservation['type_room']) ?> (<?= htmlspecialchars($reservation['section_room']) ?>)</td>
                                                            <td><?= formatDate($reservation['reservation_date']) ?></td>
                                                            <td><?= formatTime($reservation['start_time']) ?> - <?= formatTime($reservation['end_time']) ?></td>
                                                            <td>Rp <?= number_format($reservation['price'], 0, ',', '.') ?></td>
                                                            <td>
                                                                <?php if (isset($reservation['payment_status'])): ?>
                                                                    <?php if ($reservation['payment_status'] == 'pending'): ?>
                                                                        <span class="badge bg-warning">Menunggu Konfirmasi</span>
                                                                    <?php elseif ($reservation['payment_status'] == 'confirmed'): ?>
                                                                        <span class="badge bg-success">Dikonfirmasi</span>
                                                                    <?php elseif ($reservation['payment_status'] == 'rejected'): ?>
                                                                        <span class="badge bg-danger">Ditolak</span>
                                                                    <?php elseif ($reservation['payment_status'] == 'expired'): ?>
                                                                        <span class="badge bg-secondary">Selesai</span>
                                                                    <?php endif; ?>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary">Belum Dibayar</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-sm bg-secondary text-white"  
                                                                        data-bs-toggle="modal" 
                                                                        data-bs-target="#detailModal"
                                                                        data-id="<?= $reservation['id_reservasi'] ?>"
                                                                        data-nama="<?= htmlspecialchars($reservation['customer_name']) ?>"
                                                                        data-username="<?= htmlspecialchars($reservation['customer_username']) ?>"
                                                                        data-telp="<?= htmlspecialchars($reservation['customer_phone']) ?>"
                                                                        data-date="<?= formatDate($reservation['reservation_date']) ?>"
                                                                        data-time="<?= formatTime($reservation['start_time']) ?> - <?= formatTime($reservation['end_time']) ?>"
                                                                        data-room="<?= htmlspecialchars($reservation['section_room']) ?> (<?= htmlspecialchars($reservation['type_room']) ?>)"
                                                                        data-total="Rp <?= number_format($reservation['price'], 0, ',', '.') ?>"
                                                                        data-payment-status="<?= isset($reservation['payment_status']) ? $reservation['payment_status'] : 'unpaid' ?>"
                                                                        data-payment-id="<?= isset($reservation['payment_id']) ? $reservation['payment_id'] : '' ?>"
                                                                        data-payment-proof="<?= isset($reservation['payment_proof']) ? $reservation['payment_proof'] : '' ?>"
                                                                        data-payment-date="<?= isset($reservation['payment_date']) ? formatDate($reservation['payment_date']) : '' ?>"
                                                                        data-reject-reason="<?= htmlspecialchars($reservation['keterangan_penolakan']) ?>">
                                                                    <i class="fas fa-eye"></i> 
                                                                </button>
                                                                
                                                                <?php if (isset($reservation['payment_status']) && $reservation['payment_status'] == 'pending'): ?>
                                                                <button class="btn btn-sm btn-success mt-1"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#confirmModal"
                                                                        data-payment-id="<?= $reservation['payment_id'] ?>"
                                                                        data-reservation-id="<?= $reservation['id_reservasi'] ?>"
                                                                        data-nama="<?= htmlspecialchars($reservation['customer_name']) ?>"
                                                                        data-id-reservasi="<?= $reservation['id_reservasi'] ?>"
                                                                        data-payment-proof="<?= isset($reservation['payment_proof']) ? $reservation['payment_proof'] : '' ?>">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Pagination -->
                                    <?php if ($total_pages > 1): ?>
                                    <div class="d-flex justify-content-between align-items-center px-4 py-3">
                                        <div>
                                            <small class="text-muted">Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?></small>
                                        </div>
                                        <nav aria-label="Page navigation">
                                            <ul class="pagination pagination-sm mb-0">
                                                <!-- Previous Button -->
                                                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                                    <a class="page-link text-primary" href="<?php echo ($page <= 1) ? '#' : '?page=' . ($page - 1); ?>" 
                                                       <?php echo ($page <= 1) ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                                                        <i class="bi bi-chevron-left"></i>
                                                    </a>
                                                </li>
                                                
                                                <!-- Page Numbers -->
                                                <?php
                                                $start_page = max(1, $page - 2);
                                                $end_page = min($total_pages, $page + 2);
                                                
                                                // Tampilkan halaman pertama jika tidak termasuk dalam range
                                                if ($start_page > 1) {
                                                    echo '<li class="page-item"><a class="page-link text-primary" href="?page=1">1</a></li>';
                                                    if ($start_page > 2) {
                                                        echo '<li class="page-item disabled"><span class="page-link text-muted">...</span></li>';
                                                    }
                                                }
                                                
                                                // Tampilkan range halaman
                                                for ($i = $start_page; $i <= $end_page; $i++) {
                                                    $active = ($i == $page) ? 'active' : '';
                                                    if ($active) {
                                                        echo '<li class="page-item active"><a class="page-link bg-primary text-white" href="?page=' . $i . '">' . $i . '</a></li>';
                                                    } else {
                                                        echo '<li class="page-item"><a class="page-link text-primary" href="?page=' . $i . '">' . $i . '</a></li>';
                                                    }
                                                }
                                                
                                                // Tampilkan halaman terakhir jika tidak termasuk dalam range
                                                if ($end_page < $total_pages) {
                                                    if ($end_page < $total_pages - 1) {
                                                        echo '<li class="page-item disabled"><span class="page-link text-muted">...</span></li>';
                                                    }
                                                    echo '<li class="page-item"><a class="page-link text-primary" href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                                                }
                                                ?>
                                                
                                                <!-- Next Button -->
                                                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                                    <a class="page-link text-primary" href="<?php echo ($page >= $total_pages) ? '#' : '?page=' . ($page + 1); ?>"
                                                       <?php echo ($page >= $total_pages) ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                                                        <i class="bi bi-chevron-right"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Modal Detail -->
                <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Reservasi</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="idReservasi" class="form-label">ID Reservasi</label>
                                    <input type="text" class="form-control" id="idReservasi" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="namaCustomer" class="form-label">Nama Customer</label>
                                    <input type="text" class="form-control" id="namaCustomer" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="usernameCustomer" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="usernameCustomer" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="telpCustomer" class="form-label">No. Telp</label>
                                    <input type="text" class="form-control" id="telpCustomer" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="roomInfo" class="form-label">Ruangan</label>
                                    <input type="text" class="form-control" id="roomInfo" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="dateReservation" class="form-label">Tanggal</label>
                                    <input type="text" class="form-control" id="dateReservation" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="timeReservation" class="form-label">Jam</label>
                                    <input type="text" class="form-control" id="timeReservation" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="totalPayment" class="form-label">Total</label>
                                    <input type="text" class="form-control" id="totalPayment" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="paymentStatus" class="form-label">Status Pembayaran</label>
                                    <input type="text" class="form-control" id="paymentStatus" disabled>
                                </div>
                                <div class="mb-3 payment-info" style="display: none;">
                                    <label for="paymentDate" class="form-label">Tanggal Pembayaran</label>
                                    <input type="text" class="form-control" id="paymentDate" disabled>
                                </div>
                                <div class="mb-3 reject-reason" style="display: none;">
                                    <label for="rejectReason" class="form-label">Alasan Penolakan</label>
                                    <textarea class="form-control" id="rejectReason" rows="3" disabled></textarea>
                                </div>
                                <div class="mb-3 payment-proof" style="display: none;">
                                    <label class="form-label">Bukti Pembayaran</label>
                                    <div class="text-center">
                                        <img id="proofImage" src="" alt="Bukti Pembayaran" class="img-fluid rounded" style="max-height: 300px;">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Konfirmasi -->
                <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="confirmModalLabel">Konfirmasi Pembayaran</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Anda akan mengkonfirmasi pembayaran untuk reservasi dengan ID: <span id="confirmReservationId"></span></p>
                                <p>Atas nama: <span id="confirmCustomerName"></span></p>
                                <div class="mb-3">
                                    <label class="form-label">Bukti Pembayaran</label>
                                    <div class="text-center">
                                        <img id="confirmProofImage" src="" alt="Bukti Pembayaran" class="img-fluid rounded" style="max-height: 300px;">
                                    </div>
                                </div>
                                <form id="confirmForm" method="POST">
                                    <input type="hidden" name="payment_id" id="confirmPaymentId">
                                    <input type="hidden" name="reservation_id" id="confirmReservationIdHidden">
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="button" class="btn btn-danger" id="rejectButton">Tolak Pembayaran</button>
                                <button type="button" class="btn btn-success" id="confirmButton">Konfirmasi Pembayaran</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Penolakan -->
                <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="rejectModalLabel">Tolak Pembayaran</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Anda akan menolak pembayaran untuk reservasi dengan ID: <span id="rejectReservationId"></span></p>
                                <p>Atas nama: <span id="rejectCustomerName"></span></p>
                                <div class="mb-3">
                                    <label for="rejectReasonInput" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="rejectReasonInput" name="reject_reason" rows="4" 
                                            placeholder="Masukkan alasan penolakan pembayaran..." required></textarea>
                                    <div class="form-text">Alasan ini akan ditampilkan kepada customer.</div>
                                </div>
                                <form id="rejectForm" method="POST">
                                    <input type="hidden" name="payment_id" id="rejectPaymentId">
                                    <input type="hidden" name="reservation_id" id="rejectReservationIdHidden">
                                    <input type="hidden" name="reject_reason" id="rejectReasonHidden">
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="button" class="btn btn-danger" id="submitRejectButton">Tolak Pembayaran</button>
                            </div>
                        </div>
                    </div>
                </div>

                <?php include ('./layout/adminFooter.php'); ?>
            </div>
        </div>
    </div>
    <script src="../dist/assets/static/js/components/dark.js"></script>
    <script src="../dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../dist/assets/compiled/js/app.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var detailModal = document.getElementById('detailModal');
        var confirmModal = document.getElementById('confirmModal');
        var rejectModal = document.getElementById('rejectModal');
        
        // Detail modal handling
        detailModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var idReservasi = button.getAttribute('data-id');
            var namaCustomer = button.getAttribute('data-nama');
            var usernameCustomer = button.getAttribute('data-username');
            var telpCustomer = button.getAttribute('data-telp');
            var roomInfo = button.getAttribute('data-room');
            var dateReservation = button.getAttribute('data-date');
            var timeReservation = button.getAttribute('data-time');
            var totalPayment = button.getAttribute('data-total');
            var paymentStatus = button.getAttribute('data-payment-status');
            var paymentDate = button.getAttribute('data-payment-date');
            var paymentProof = button.getAttribute('data-payment-proof');
            var rejectReason = button.getAttribute('data-reject-reason');

            // Set values in modal
            detailModal.querySelector('#idReservasi').value = idReservasi;
            detailModal.querySelector('#namaCustomer').value = namaCustomer;
            detailModal.querySelector('#usernameCustomer').value = usernameCustomer;
            detailModal.querySelector('#telpCustomer').value = telpCustomer;
            detailModal.querySelector('#roomInfo').value = roomInfo;
            detailModal.querySelector('#dateReservation').value = dateReservation;
            detailModal.querySelector('#timeReservation').value = timeReservation;
            detailModal.querySelector('#totalPayment').value = totalPayment;
            
            // Handle payment status
            var paymentStatusText = paymentStatus === 'pending' ? "Menunggu Konfirmasi" :
                                   paymentStatus === 'confirmed' ? "Pembayaran Dikonfirmasi" :
                                   paymentStatus === 'expired' ? "Pembayaran Dikonfirmasi & Selesai" :
                                   paymentStatus === 'rejected' ? "Pembayaran Ditolak" : 
                                   "Belum Dibayar";
            
            detailModal.querySelector('#paymentStatus').value = paymentStatusText;
            detailModal.querySelector('.payment-info').style.display = (paymentStatus === 'confirmed' || paymentStatus === 'expired') ? 'block' : 'none';
            detailModal.querySelector('.payment-proof').style.display = paymentStatus !== 'unpaid' ? 'block' : 'none';
            detailModal.querySelector('#paymentDate').value = paymentDate;

            // Handle reject reason
            if (paymentStatus === 'rejected' && rejectReason) {
                detailModal.querySelector('.reject-reason').style.display = 'block';
                detailModal.querySelector('#rejectReason').value = rejectReason;
            } else {
                detailModal.querySelector('.reject-reason').style.display = 'none';
            }

            // Handle proof image
            var proofImage = detailModal.querySelector('#proofImage');
            if (paymentProof) {
                proofImage.src = '../uploads/payment_proofs/' + paymentProof;
                proofImage.style.display = 'block';
            } else {
                proofImage.style.display = 'none';
            }
        });

        // Confirm modal handling
        confirmModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            confirmModal.querySelector('#confirmPaymentId').value = button.getAttribute('data-payment-id');
            confirmModal.querySelector('#confirmReservationIdHidden').value = button.getAttribute('data-reservation-id');
            confirmModal.querySelector('#confirmReservationId').textContent = button.getAttribute('data-id-reservasi');
            confirmModal.querySelector('#confirmCustomerName').textContent = button.getAttribute('data-nama');
            
            var proofImage = confirmModal.querySelector('#confirmProofImage');
            var paymentProof = button.getAttribute('data-payment-proof');
            if (paymentProof) {
                proofImage.src = '../uploads/payment_proofs/' + paymentProof;
                proofImage.style.display = 'block';
            } else {
                proofImage.style.display = 'none';
            }
        });

        // Handle confirm button
        document.getElementById('confirmButton').addEventListener('click', function() {
            var form = document.getElementById('confirmForm');
            var input = document.createElement("input");
            input.type = "hidden";
            input.name = "confirm_payment";
            input.value = "1";
            form.appendChild(input);
            form.submit();
        });

        // Handle reject button - show reject modal
        document.getElementById('rejectButton').addEventListener('click', function() {
            // Get data from confirm modal
            var paymentId = document.getElementById('confirmPaymentId').value;
            var reservationId = document.getElementById('confirmReservationIdHidden').value;
            var reservationIdText = document.getElementById('confirmReservationId').textContent;
            var customerName = document.getElementById('confirmCustomerName').textContent;
            
            // Set data to reject modal
            document.getElementById('rejectPaymentId').value = paymentId;
            document.getElementById('rejectReservationIdHidden').value = reservationId;
            document.getElementById('rejectReservationId').textContent = reservationIdText;
            document.getElementById('rejectCustomerName').textContent = customerName;
            
            // Hide confirm modal and show reject modal
            var confirmModalInstance = bootstrap.Modal.getInstance(confirmModal);
            confirmModalInstance.hide();
            
            setTimeout(function() {
                var rejectModalInstance = new bootstrap.Modal(rejectModal);
                rejectModalInstance.show();
            }, 300);
        });

        // Handle submit reject button
        document.getElementById('submitRejectButton').addEventListener('click', function() {
            var rejectReason = document.getElementById('rejectReasonInput').value.trim();
            
            if (!rejectReason) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Alasan Diperlukan',
                    text: 'Silakan masukkan alasan penolakan pembayaran.',
                    showConfirmButton: true
                });
                return;
            }
            
            // Set the reason to hidden input
            document.getElementById('rejectReasonHidden').value = rejectReason;
            
            var form = document.getElementById('rejectForm');
            var input = document.createElement("input");
            input.type = "hidden";
            input.name = "reject_payment";
            input.value = "1";
            form.appendChild(input);
            form.submit();
        });

        // Reset reject modal when hidden
        rejectModal.addEventListener('hidden.bs.modal', function() {
            document.getElementById('rejectReasonInput').value = '';
        });

        // SweetAlert for payment actions
        <?php if (isset($swalType) && isset($swalMessage)): ?>
            Swal.fire({
                icon: '<?= $swalType ?>',
                title: '<?= $swalMessage ?>',
                showConfirmButton: true,
                timer: 3000
            }).then(() => {
                window.location.href = "dataReservasi.php";
            });
        <?php endif; ?>
    });
    </script>
</body>
</html>