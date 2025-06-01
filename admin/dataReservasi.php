<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('location: adminLogin.php');
    exit;
}
include ('./layout/sidebar.php');
include '../server/connection.php';

// Query untuk mengambil semua data reservasi dengan informasi terkait termasuk status pembayaran
$reservations = [];
try {
    $stmt = $conn->prepare("SELECT 
    r.id_reservasi,
    r.nama AS customer_name,
    r.username AS customer_username,
    r.telp AS customer_phone,
    r.reservation_date,
    r.start_time,
    r.end_time,
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
ORDER BY r.id_reservasi DESC;
");
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    echo "<script>console.error('Error query: " . $e->getMessage() . "');</script>";
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
    try {
        $updateStmt = $conn->prepare("UPDATE payments SET payment_status = 'rejected' WHERE id_payments = ?");
        $updateStmt->bind_param("i", $payment_id);
        $updateStmt->execute();
        if ($updateStmt->affected_rows > 0) {
            $swalType = "info";
            $swalMessage = "Pembayaran berhasil ditolak!";
        } else {
            $swalType = "error";
            $swalMessage = "Gagal menolak pembayaran!";
        }
        $updateStmt->close();
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
                                <h3 class="text-primary"><?= count($reservations) ?></h3>
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
                                    foreach ($reservations as $r) {
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
                                    $todayReservations = array_filter($reservations, function($r) use ($today) {
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
                                    $pendingPayments = array_filter($reservations, function($r) {
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
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>ID RESERVASI</th>
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
                                                    <?php foreach ($reservations as $reservation): ?>
                                                        <tr>
                                                            <td><?= $reservation['id_reservasi'] ?></td>
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
                                                                        data-payment-date="<?= isset($reservation['payment_date']) ? formatDate($reservation['payment_date']) : '' ?>">
                                                                    <i class="bi bi-eye"></i> Detail
                                                                </button>
                                                                
                                                                <?php if (isset($reservation['payment_status']) && $reservation['payment_status'] == 'pending'): ?>
                                                                <button class="btn btn-sm btn-success mt-3"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#confirmModal"
                                                                        data-payment-id="<?= $reservation['payment_id'] ?>"
                                                                        data-nama="<?= htmlspecialchars($reservation['customer_name']) ?>"
                                                                        data-id-reservasi="<?= $reservation['id_reservasi'] ?>"
                                                                        data-payment-proof="<?= isset($reservation['payment_proof']) ? $reservation['payment_proof'] : '' ?>">
                                                                    <i class="bi bi-check-circle"></i> Konfirmasi
                                                                </button>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
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
            detailModal.querySelector('.payment-info').style.display = paymentStatus === 'confirmed' ? 'block' : 'none';
            detailModal.querySelector('.payment-proof').style.display = paymentStatus === 'confirmed' ? 'block' : 'none';
            detailModal.querySelector('#paymentDate').value = paymentDate;

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

        // Handle confirm/reject buttons
        document.getElementById('confirmButton').addEventListener('click', function() {
            var form = document.getElementById('confirmForm');
            var input = document.createElement("input");
            input.type = "hidden";
            input.name = "confirm_payment";
            input.value = "1";
            form.appendChild(input);
            form.submit();
        });

        document.getElementById('rejectButton').addEventListener('click', function() {
            var form = document.getElementById('confirmForm');
            var input = document.createElement("input");
            input.type = "hidden";
            input.name = "reject_payment";
            input.value = "1";
            form.appendChild(input);
            form.submit();
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