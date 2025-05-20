<?php
session_start();
include('layout/navbar.php');
include 'server/connection.php';

// Check if form data was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['confirm_payment'])) {
    // Get form data from reservation form
    $id_room = $_POST['id_room'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $telp = $_POST['telp'];
    $reservation_date = $_POST['reservation_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $room_type = $_POST['room_type'];
    $room_name = $_POST['room_name'];
    $duration = floatval($_POST['duration']);
    $total_price = intval($_POST['price']);
    
    // Validate data
    if (empty($reservation_date)) {
        echo "<script>alert('Tanggal reservasi tidak boleh kosong!'); window.history.back();</script>";
        exit;
    }
    
    // Validate date format
    $date_check = DateTime::createFromFormat('Y-m-d', $reservation_date);
    if (!$date_check || $date_check->format('Y-m-d') !== $reservation_date) {
        echo "<script>alert('Format tanggal tidak valid!'); window.history.back();</script>";
        exit;
    }
    
    // Get room price per hour based on type
    $room_prices = [
        'reguler' => 13000,
        'vip' => 15000,
        'private' => 20000
    ];
    
    $price_per_hour = $room_prices[$room_type];
    
    // Calculate correct total price (duration * price per hour)
    $calculated_price = $duration * $price_per_hour;
    
    // Get id_user from session
    $id_user = (isset($_SESSION['id_user']) && !empty($_SESSION['id_user'])) ? $_SESSION['id_user'] : 1;
    
    // Store reservation data in session for payment processing
    $_SESSION['payment_data'] = [
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
        'price' => $calculated_price
    ];
} elseif (!isset($_SESSION['payment_data'])) {
    // Redirect back if no payment data in session
    echo "<script>alert('Data pembayaran tidak ditemukan!'); window.location.href='reservasi.php';</script>";
    exit;
}

// Process payment when payment form is submitted
if (isset($_POST['payment_method']) && isset($_POST['confirm_payment'])) {
    $payment_method = $_POST['payment_method'];
    $payment_data = $_SESSION['payment_data'];
    
    try {
        // Start transaction
        mysqli_autocommit($conn, FALSE);
        
        // Insert reservation with proper date formatting
        $insert_reservation = "INSERT INTO reservasi (id_room, id_user, nama, username, telp, reservation_date, start_time, end_time, price) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt1 = mysqli_prepare($conn, $insert_reservation);
        
        // Make sure all variables are properly defined
        $id_room = $payment_data['id_room'];
        $id_user = $payment_data['id_user'];
        $nama = $payment_data['nama'];
        $username = $payment_data['username'];
        $telp = $payment_data['telp'];
        $reservation_date = $payment_data['reservation_date'];
        $start_time = $payment_data['start_time'];
        $end_time = $payment_data['end_time'];
        $total_price = $payment_data['price'];
        
        // Validate date format again before insertion
        $date_check = DateTime::createFromFormat('Y-m-d', $reservation_date);
        if (!$date_check) {
            throw new Exception("Invalid date format: " . $reservation_date);
        }
        
        mysqli_stmt_bind_param($stmt1, "iissssssi", 
            $id_room,
            $id_user,
            $nama,
            $username,
            $telp,
            $reservation_date,
            $start_time,
            $end_time,
            $total_price
        );
        
        if (!mysqli_stmt_execute($stmt1)) {
            $error = mysqli_stmt_error($stmt1);
            throw new Exception("Error inserting reservation: " . $error);
        }
        
        // Get the reservation ID
        $reservation_id = mysqli_insert_id($conn);
        
        // Generate transaction ID
        $transaction_id = 'TXN' . date('Ymd') . str_pad($reservation_id, 5, '0', STR_PAD_LEFT);
        
        // Process payment proof image upload
        $payment_proof = null;
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            $file_type = $_FILES['payment_proof']['type'];
            
            if (in_array($file_type, $allowed_types)) {
                $file_extension = pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION);
                $file_name = 'payment_' . $transaction_id . '.' . $file_extension;
                $upload_dir = 'uploads/payment_proofs/';
                
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $target_file = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target_file)) {
                    $payment_proof = $target_file;
                } else {
                    throw new Exception("Error uploading file.");
                }
            } else {
                throw new Exception("File type not allowed. Please upload a JPG or PNG image.");
            }
        } else {
            throw new Exception("Payment proof is required. Please upload an image of your payment receipt.");
        }
        
        // Insert payment record with payment proof
        $insert_payment = "INSERT INTO payments (reservasi_id, transaction_id, amount, payment_method, payment_proof, payment_status, payment_date) 
                          VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
        $stmt2 = mysqli_prepare($conn, $insert_payment);
        mysqli_stmt_bind_param($stmt2, "isdss", 
            $reservation_id,
            $transaction_id,
            $payment_data['price'],
            $payment_method,
            $payment_proof
        );
        
        if (!mysqli_stmt_execute($stmt2)) {
            $error = mysqli_stmt_error($stmt2);
            throw new Exception("Error inserting payment: " . $error);
        }
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Clear session data
        unset($_SESSION['payment_data']);
        
        // Success
        echo "<script>
            alert('Pembayaran berhasil dikirim! Transaction ID: $transaction_id. Admin akan memvalidasi pembayaran Anda.');
            window.location.href = 'profile.php';
        </script>";
        exit;
        
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        $error_message = "Pembayaran gagal: " . $e->getMessage();
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
    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-lugx-gaming.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <style>
        .payment-card {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .payment-summary {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .payment-method {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-method:hover {
            border-color: #fe5722;
            background-color: #fff5f3;
        }
        .payment-method.selected {
            border-color: #fe5722;
            background-color: #fff5f3;
        }
        .payment-method input[type="radio"] {
            margin-right: 10px;
        }
        .price-breakdown {
            border-top: 2px solid #dee2e6;
            padding-top: 15px;
            margin-top: 15px;
        }
        .total-price {
            font-size: 1.5em;
            font-weight: bold;
            color: #fe5722;
        }
        .file-upload-container {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .file-upload-container:hover {
            border-color: #fe5722;
            background-color: #fff5f3;
        }
        .file-preview {
            max-width: 100%;
            max-height: 200px;
            margin-top: 15px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="page-heading header-text">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Payment</h3>
                    <span class="breadcrumb"><a href="index.php">Home</a> > <a href="reservasi.php">Reservation</a> > Payment</span>
                </div>
            </div>
        </div>
    </div>

    <div class="section mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <div class="payment-card">
                        <h4 class="mb-4">Konfirmasi Pembayaran</h4>
                        
                        <!-- Reservation Summary -->
                        <div class="payment-summary">
                            <h5 class="mb-3">Detail Reservasi</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($_SESSION['payment_data']['nama']); ?></p>
                                    <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['payment_data']['username']); ?></p>
                                    <p><strong>Telepon:</strong> <?php echo htmlspecialchars($_SESSION['payment_data']['telp']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Ruangan:</strong> <?php echo htmlspecialchars($_SESSION['payment_data']['room_name']); ?></p>
                                    <p><strong>Tipe:</strong> <?php echo ucfirst($_SESSION['payment_data']['room_type']); ?></p>
                                    <p><strong>Tanggal:</strong> <?php echo date('d F Y', strtotime($_SESSION['payment_data']['reservation_date'])); ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Waktu Mulai:</strong> <?php echo $_SESSION['payment_data']['start_time']; ?></p>
                                    <p><strong>Waktu Selesai:</strong> <?php echo $_SESSION['payment_data']['end_time']; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Durasi:</strong> <?php echo $_SESSION['payment_data']['duration']; ?> jam</p>
                                    <?php 
                                    $room_prices = [
                                        'reguler' => 13000,
                                        'vip' => 15000,
                                        'private' => 20000
                                    ];
                                    $price_per_hour = $room_prices[$_SESSION['payment_data']['room_type']];
                                    ?>
                                    <p><strong>Harga per jam:</strong> Rp <?php echo number_format($price_per_hour, 0, ',', '.'); ?></p>
                                </div>
                            </div>
                            
                            <div class="price-breakdown">
                                <div class="d-flex justify-content-between">
                                    <span>Subtotal (<?php echo $_SESSION['payment_data']['duration']; ?> jam × Rp <?php echo number_format($price_per_hour, 0, ',', '.'); ?>):</span>
                                    <span>Rp <?php echo number_format($_SESSION['payment_data']['price'], 0, ',', '.'); ?></span>
                                </div>
                                <div class="d-flex justify-content-between total-price">
                                    <span>Total:</span>
                                    <span>Rp <?php echo number_format($_SESSION['payment_data']['price'], 0, ',', '.'); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method Selection -->
                        <form method="POST" action="" enctype="multipart/form-data">
                            <h5 class="mb-3">Pilih Metode Pembayaran</h5>
                            
                            <div class="payment-method" onclick="selectPayment('bank')">
                                <input type="radio" name="payment_method" value="bank" id="bank" required>
                                <label for="bank" class="mb-0">
                                    <strong>Transfer Bank</strong>
                                    <br>
                                    <small class="text-muted">Transfer ke rekening bank yang tersedia</small>
                                </label>
                            </div>

                            <div class="payment-method" onclick="selectPayment('ewallet')">
                                <input type="radio" name="payment_method" value="ewallet" id="ewallet" required>
                                <label for="ewallet" class="mb-0">
                                    <strong>E-Wallet</strong>
                                    <br>
                                    <small class="text-muted">Pembayaran menggunakan OVO, GoPay, Dana, LinkAja</small>
                                </label>
                            </div>

                            <!-- Bank Transfer Details -->
                            <div id="bank-details" class="mt-3" style="display: none;">
                                <div class="alert alert-info">
                                    <h6>Informasi Transfer Bank:</h6>
                                    <p class="mb-1"><strong>Bank BCA:</strong> 1234567890 a.n. Pixel Station</p>
                                    <p class="mb-1"><strong>Bank Mandiri:</strong> 0987654321 a.n. Pixel Station</p>
                                    <p class="mb-0"><small>Silakan transfer sesuai nominal yang tertera dan konfirmasi pembayaran</small></p>
                                </div>
                            </div>

                            <!-- E-Wallet Details -->
                            <div id="ewallet-details" class="mt-3" style="display: none;">
                                <div class="alert alert-info">
                                    <h6>Informasi E-Wallet:</h6>
                                    <p class="mb-1"><strong>OVO:</strong> 085123456789</p>
                                    <p class="mb-1"><strong>GoPay:</strong> 085123456789</p>
                                    <p class="mb-1"><strong>Dana:</strong> 085123456789</p>
                                    <p class="mb-0"><small>Silakan transfer ke nomor yang sesuai dengan e-wallet pilihan Anda</small></p>
                                </div>
                            </div>

                            <!-- Payment Proof Upload -->
                            <div class="mt-4">
                                <h5 class="mb-3">Upload Bukti Pembayaran</h5>
                                <div class="file-upload-container">
                                    <p class="mb-2">Unggah bukti pembayaran (format JPG, PNG)</p>
                                    <input type="file" name="payment_proof" id="payment_proof" class="form-control" accept="image/jpeg, image/png, image/jpg" required>
                                    <img id="proof-preview" class="file-preview d-none" alt="Preview bukti pembayaran">
                                </div>
                                <small class="text-muted d-block mt-2">*Bukti pembayaran akan divalidasi oleh admin</small>
                            </div>

                            <div class="mt-4 text-center">
                                <button type="submit" name="confirm_payment" class="btn btn-primary btn-lg px-5">
                                    Konfirmasi Pembayaran
                                </button>
                                <a href="reservasi.php" class="btn btn-secondary btn-lg px-5 ms-3">
                                    Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-5">
        <div class="container">
            <div class="col-lg-12">
                <p>Copyright © 2025 Pixel Station. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/custom.js"></script>

    <script>
        function selectPayment(method) {
            // Remove selected class from all payment methods
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selected class to clicked payment method
            event.currentTarget.classList.add('selected');
            
            // Check the radio button
            document.getElementById(method).checked = true;
            
            // Hide all payment details
            document.getElementById('bank-details').style.display = 'none';
            document.getElementById('ewallet-details').style.display = 'none';
            
            // Show selected payment details
            if (method === 'bank') {
                document.getElementById('bank-details').style.display = 'block';
            } else if (method === 'ewallet') {
                document.getElementById('ewallet-details').style.display = 'block';
            }
        }

        // Add click event to payment method labels
        document.querySelectorAll('.payment-method label').forEach(label => {
            label.addEventListener('click', function(e) {
                e.preventDefault();
                const radio = this.previousElementSibling;
                radio.checked = true;
                selectPayment(radio.value);
            });
        });

        // Image preview for payment proof
        document.getElementById('payment_proof').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('proof-preview');
                    preview.src = event.target.result;
                    preview.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>