<?php
session_start();
include 'server/connection.php';

// Redirect jika user belum login atau tidak ada data reservasi
if (!isset($_SESSION['username']) || !isset($_SESSION['reservation_data'])) {
    header('Location: reservasi.php');
    exit;
}

$reservationData = $_SESSION['reservation_data'];

// Proses upload bukti pembayaran - BARU DISINI DATA MASUK KE DATABASE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_payment'])) {
    $payment_method = $_POST['payment_method'];
    
    // Handle file upload
    $upload_dir = 'uploads/payment_proofs/';
    $payment_proof = null;
    
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
        $file_extension = pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            $filename = 'payment_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $upload_path)) {
                $payment_proof = $filename;
            }
        }
    }
    
    if (!$payment_proof) {
        $error_message = "Gagal mengupload file bukti pembayaran.";
    } else {
        // VALIDASI ULANG OVERLAP WAKTU SEBELUM INSERT
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
            $reservationData['id_room'], $reservationData['reservation_date'], 
            $reservationData['end_time'], $reservationData['start_time'],
            $reservationData['end_time'], $reservationData['start_time'],
            $reservationData['start_time'], $reservationData['end_time']
        );
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $overlap = mysqli_fetch_assoc($result);

        if ($overlap['count'] > 0) {
            $error_message = "Maaf, waktu yang Anda pilih sudah dipesan oleh orang lain. Silakan pilih waktu lain.";
        } else {
            // Mulai transaction - BARU SEKARANG DATA MASUK DATABASE
            mysqli_autocommit($conn, FALSE);
            
            try {
                // 1. Insert ke tabel payments
                $insertPayment = "INSERT INTO payments (amount, payment_method, payment_proof, payment_status, payment_date) 
                                 VALUES (?, ?, ?, 'pending', NOW())";
                $stmtPayment = mysqli_prepare($conn, $insertPayment);
                mysqli_stmt_bind_param($stmtPayment, 'dss', $reservationData['price'], $payment_method, $payment_proof);
                
                if (!mysqli_stmt_execute($stmtPayment)) {
                    throw new Exception("Gagal membuat record pembayaran");
                }
                
                // 2. Ambil ID payment yang baru dibuat
                $id_payments = mysqli_insert_id($conn);
                
                // 3. Insert ke tabel reservasi dengan id_payments
                $insertReservasi = "INSERT INTO reservasi (id_room, id_user, id_payments, nama, username, telp, reservation_date, start_time, end_time)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtReservasi = mysqli_prepare($conn, $insertReservasi);
                mysqli_stmt_bind_param($stmtReservasi, 'iiissssss', 
                    $reservationData['id_room'], $reservationData['id_user'], $id_payments, 
                    $reservationData['nama'], $reservationData['username'], $reservationData['telp'], 
                    $reservationData['reservation_date'], $reservationData['start_time'], $reservationData['end_time']
                );
                
                if (!mysqli_stmt_execute($stmtReservasi)) {
                    throw new Exception("Gagal membuat reservasi");
                }
                
                // Commit transaction
                mysqli_commit($conn);
                
                // Clear session data
                unset($_SESSION['reservation_data']);
                
                echo "<script>
                    window.paymentSuccess = true;
                </script>";
                
            } catch (Exception $e) {
                // Rollback transaction jika ada error
                mysqli_rollback($conn);
                $error_message = "Gagal melakukan reservasi: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title>Pixel Station - Payment</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-lugx-gaming.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        .back-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            margin-bottom: 20px;
        }
        
        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
            color: white;
            text-decoration: none;
        }
        
        .back-button i {
            font-size: 16px;
            transition: transform 0.3s ease;
        }
        
        .back-button:hover i {
            transform: translateX(-3px);
        }
    </style>
</head>

<body>
    <div class="payment-page section mt-5 mb-5">
        <div class="container">
            <!-- Back Button -->
            <a href="#" onclick="showCancelConfirmation(); return false;" class="back-button">
                <i class="fa fa-arrow-left"></i>
                Kembali ke Reservasi
            </a>
            
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="section-heading text-center">
                        <h6>PEMBAYARAN</h6>
                        <h2>COMPLETE YOUR PAYMENT</h2>
                    </div>

                    <!-- Detail Reservasi -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Detail Reservasi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Ruangan:</strong> <?php echo htmlspecialchars($reservationData['room_name']); ?></p>
                                    <p><strong>Tipe:</strong> <?php echo ucfirst($reservationData['room_type']); ?></p>
                                    <p><strong>Tanggal:</strong> <?php echo date('d F Y', strtotime($reservationData['reservation_date'])); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Waktu:</strong> <?php echo substr($reservationData['start_time'], 0, 5) . ' - ' . substr($reservationData['end_time'], 0, 5); ?></p>
                                    <p><strong>Durasi:</strong> <?php echo $reservationData['duration']; ?> jam</p>
                                    <p><strong>Total Harga:</strong> <span class="text-primary fw-bold">Rp <?php echo number_format($reservationData['price'], 0, ',', '.'); ?></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Pembayaran -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Informasi Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Bank Transfer</h6>
                                    <p><strong>BCA:</strong> 1234567890<br>
                                    <strong>BRI:</strong> 0987654321<br>
                                    <strong>Mandiri:</strong> 1122334455</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>E-Wallet</h6>
                                    <p><strong>GoPay:</strong> 081234567890<br>
                                    <strong>OVO:</strong> 081234567890<br>
                                    <strong>DANA:</strong> 081234567890</p>
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <strong>Penting:</strong> Transfer sesuai dengan jumlah yang tertera. Upload bukti pembayaran untuk konfirmasi.
                            </div>
                        </div>
                    </div>

                    <!-- Form Upload Bukti Pembayaran -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Upload Bukti Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data" id="paymentForm">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Metode Pembayaran</label>
                                    <select name="payment_method" id="payment_method" class="form-select" required>
                                        <option value="">Pilih Metode Pembayaran</option>
                                        <option value="BCA">Bank BCA</option>
                                        <option value="BRI">Bank BRI</option>
                                        <option value="Mandiri">Bank Mandiri</option>
                                        <option value="GoPay">GoPay</option>
                                        <option value="OVO">OVO</option>
                                        <option value="DANA">DANA</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="payment_proof" class="form-label">Bukti Pembayaran</label>
                                    <input type="file" name="payment_proof" id="payment_proof" 
                                           class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                                    <div class="form-text">Format yang diterima: JPG, PNG, PDF (Max 5MB)</div>
                                </div>

                                <div class="text-center">
                                    <button type="button" onclick="submitPayment()" class="btn btn-primary btn-lg me-2">
                                        <i class="fa fa-upload me-2"></i>
                                        Upload Bukti Pembayaran & Konfirmasi Reservasi
                                    </button>
                                    <button type="button" onclick="showCancelConfirmation()" class="btn btn-secondary btn-lg">
                                        <i class="fa fa-times me-2"></i>
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script>
        // Check if payment was successful
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($error_message)): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '<?php echo addslashes($error_message); ?>',
                    confirmButtonColor: '#dc3545'
                });
            <?php endif; ?>

            if (window.paymentSuccess) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Bukti pembayaran berhasil diupload! Anda akan diarahkan ke halaman profil...',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                timer: 1200,
                timerProgressBar: true,
                didClose: () => {
                    window.location.href = 'profile.php';
                }
            });
        }


        });

        // Submit payment with confirmation
        function submitPayment() {
            const paymentMethod = document.getElementById('payment_method').value;
            const paymentProof = document.getElementById('payment_proof').files[0];

            if (!paymentMethod) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Silakan pilih metode pembayaran terlebih dahulu.',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }

            if (!paymentProof) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Silakan pilih file bukti pembayaran terlebih dahulu.',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Upload',
                text: 'Apakah Anda yakin ingin mengupload bukti pembayaran dan mengkonfirmasi reservasi ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Upload!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Remove beforeunload event listener
                    window.removeEventListener('beforeunload', beforeUnloadHandler);
                    
                    // Add hidden submit button and click it
                    const hiddenSubmit = document.createElement('input');
                    hiddenSubmit.type = 'hidden';
                    hiddenSubmit.name = 'submit_payment';
                    hiddenSubmit.value = '1';
                    document.getElementById('paymentForm').appendChild(hiddenSubmit);
                    
                    // Submit form
                    document.getElementById('paymentForm').submit();
                }
            });
        }

        // Show cancel confirmation
        function showCancelConfirmation() {
            Swal.fire({
                title: 'Konfirmasi Pembatalan',
                text: 'Data reservasi belum tersimpan. Yakin ingin kembali ke halaman reservasi?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kembali!',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.removeEventListener('beforeunload', beforeUnloadHandler);
                    window.location.href = 'reservasi.php';
                }
            });
        }

        // File size validation with SweetAlert
        document.getElementById('payment_proof').addEventListener('change', function() {
            const file = this.files[0];
            if (file && file.size > 5 * 1024 * 1024) { // 5MB
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar!',
                    text: 'Ukuran file maksimal 5MB. Silakan pilih file yang lebih kecil.',
                    confirmButtonColor: '#dc3545'
                });
                this.value = '';
            }
        });

        // Before unload handler
        function beforeUnloadHandler(e) {
            e.preventDefault();
            e.returnValue = 'Data reservasi belum tersimpan. Yakin ingin meninggalkan halaman ini?';
            return 'Data reservasi belum tersimpan. Yakin ingin meninggalkan halaman ini?';
        }

        // Add beforeunload event listener
        window.addEventListener('beforeunload', beforeUnloadHandler);

        // Handle browser back button
        window.addEventListener('popstate', function(e) {
            e.preventDefault();
            showCancelConfirmation();
            history.pushState(null, null, window.location.pathname);
        });

        // Push initial state to handle back button
        history.pushState(null, null, window.location.pathname);
    </script>
</body>
</html>