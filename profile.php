<?php
    session_start();
    include('layout/navbar.php');
    include 'server/connection.php';

    // Debug: Cek session
    if (!isset($_SESSION['id_user'])) {
        echo "<script>alert('User belum login!'); window.location.href='login.php';</script>";
        exit();
    }

    // Ambil user id dari session
    $user_id = $_SESSION['id_user'];

    // Handle review submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review'])) {
        $order_id = $_POST['order_id'];
        $review_text = $_POST['review'];
        $rating = $_POST['rating'];
        
        try {
            // Cek apakah sudah ada review untuk order ini
            $check_stmt = $conn->prepare("SELECT id_review FROM review WHERE id_reservasi = ? AND id_user = ?");
            $check_stmt->bind_param("ii", $order_id, $user_id);
            $check_stmt->execute();
            $existing_review = $check_stmt->get_result();
            
            if ($existing_review->num_rows > 0) {
                // Update existing review
                $update_stmt = $conn->prepare("UPDATE review SET review_text = ?, rating = ?, created_at = NOW() WHERE id_reservasi = ? AND id_user = ?");
                $update_stmt->bind_param("siii", $review_text, $rating, $order_id, $user_id);
                
                if ($update_stmt->execute()) {
                    $_SESSION['success_message'] = 'Review berhasil diperbarui!';
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    throw new Exception("Gagal memperbarui review");
                }
                $update_stmt->close();
            } else {
                // Insert new review
                $insert_stmt = $conn->prepare("INSERT INTO review (id_reservasi, id_user, review_text, rating, created_at) VALUES (?, ?, ?, ?, NOW())");
                $insert_stmt->bind_param("iisi", $order_id, $user_id, $review_text, $rating);
                
                if ($insert_stmt->execute()) {
                    $_SESSION['success_message'] = 'Review berhasil disimpan!';
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    throw new Exception("Gagal menyimpan review");
                }
                $insert_stmt->close();
            }
            $check_stmt->close();
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // Display messages if they exist
    if (isset($_SESSION['success_message'])) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Berhasil!',
                    text: '" . $_SESSION['success_message'] . "',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
        unset($_SESSION['success_message']);
    }

    if (isset($_SESSION['error_message'])) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error!',
                    text: '" . $_SESSION['error_message'] . "',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        </script>";
        unset($_SESSION['error_message']);
    }

    // Query order berdasarkan user id dengan review dan payment information
    $orders = [];
    try {
        $stmt = $conn->prepare("SELECT
    r.id_reservasi,
    r.reservation_date,
    r.keterangan_penolakan,
    rm.section_room,
    rm.type_room,
    r.start_time,
    r.end_time,
    p.amount AS price,
    rv.id_review,
    rv.review_text,
    rv.rating,
    rv.created_at AS review_date,
    p.payment_status,
    p.payment_method,
    p.payment_proof,
    p.payment_date
FROM reservasi r
JOIN room rm ON r.id_room = rm.id_room
LEFT JOIN review rv ON r.id_reservasi = rv.id_reservasi AND rv.id_user = ?
LEFT JOIN payments p ON r.id_payments = p.id_payments
WHERE r.id_user = ?
ORDER BY r.reservation_date DESC;

                ");
        
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            //$now = new DateTime(); // waktu saat ini
            $now = new DateTime("2025-05-30 19:30:00"); // waktu testing
            $endDatetime = new DateTime($row['reservation_date'] . ' ' . $row['end_time']);

            // Jika waktu sekarang sudah melewati waktu selesai dan status masih "Dikonfirmasi"
            if ($now > $endDatetime && $row['payment_status'] == 'confirmed') {
                // Update status di database jadi expired
                $updateStmt = $conn->prepare("UPDATE payments SET payment_status = 'expired' WHERE id_payments = (
                    SELECT id_payments FROM reservasi WHERE id_reservasi = ?
                )");
                $updateStmt->bind_param("i", $row['id_reservasi']);
                $updateStmt->execute();
                $updateStmt->close();

                // Update tampilan juga
                $row['payment_status'] = 'expired';
            }

            $orders[] = $row;
        }

        $stmt->close();
        
        // Debug: Cek apakah ada data
        if (empty($orders)) {
            echo "<script>console.log('Tidak ada data reservasi untuk user ID: " . $user_id . "');</script>";
        }
    } catch (Exception $e) {
        echo "<script>console.error('Error query: " . $e->getMessage() . "');</script>";
    }

    // Function to get payment status badge
    function getPaymentStatusBadge($status) {
        switch(strtolower($status)) {
            case 'confirmed':
                return '<span class="badge bg-success">Pembayaran Dikonfirmasi</span>';
            case 'pending':
                return '<span class="badge bg-warning">Menunggu Konfirmasi Admin</span>';
            case 'rejected':
                return '<span class="badge bg-danger">Pembayaran Ditolak</span>';
            default:
                return '<span class="badge bg-secondary">Expired</span>';
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title>Pixel Station - Profile Page</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/profile.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/styleIndex.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    

</head>

<body>
    <div class="page-heading header-text">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <img src="assets/images/profile.png" alt="Foto Profil" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    <h3 class="mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        <?php echo isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'Nama Lengkap'; ?>
                    </h3>
                    <p class="text-white mb-0" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        @<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'username'; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="contact-page section mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="section-heading text-center">
                        <h2>RIWAYAT ORDERAN</h2>
                    </div>
                    <div class="right-content">
                        <?php if (empty($orders)): ?>
                            <div class="alert alert-info text-center">
                                <h5>Belum ada riwayat reservasi</h5>
                                <p>Silakan lakukan reservasi terlebih dahulu.</p>
                            </div>
                        <?php else: ?>
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
                                <?php foreach($orders as $index => $order): ?>
                                <div class="col">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-body">
                                            <h5 class="card-title mb-2">
                                                <?= htmlspecialchars($order['section_room']) ?>
                                                <span class="badge text-white ms-2" style="background-color: #967AA1;"><?= htmlspecialchars($order['type_room']) ?></span>
                                            </h5>
                                            <p class="card-text mb-1">
                                                <strong>Tanggal:</strong> <?= htmlspecialchars($order['reservation_date']) ?><br>
                                                <strong>Waktu:</strong> <?= htmlspecialchars($order['start_time']) ?> - <?= htmlspecialchars($order['end_time']) ?><br>
                                            </p>
                                            <p class="card-text"><strong>Price:</strong> Rp <?= number_format($order['price'], 0, ',', '.') ?></p>
                                            
                                            <!-- Payment Status -->
                                            <div class="mb-3">
                                                <strong>Status:</strong><br>
                                                <?= getPaymentStatusBadge($order['payment_status']) ?>
                                                <?php if ($order['payment_method']): ?>
                                                    <br><small class="text-muted">Via: <?= htmlspecialchars($order['payment_method']) ?></small>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($order['payment_status'] == 'expired' && $order['id_review']): ?>
                                                <!-- Tampilkan review yang sudah ada (hanya jika pembayaran sudah dikonfirmasi) -->
                                                <div class="mt-3 p-3 bg-light rounded">
                                                    <h6>Review Anda:</h6>
                                                    <div class="mb-2">
                                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                                            <i class="fas fa-star <?= $i <= $order['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                                        <?php endfor; ?>
                                                        <span class="ms-2">(<?= $order['rating'] ?>/5)</span>
                                                    </div>
                                                    <p class="mb-1"><?= htmlspecialchars($order['review_text']) ?></p>
                                                    <small class="text-muted">Direview pada: <?= date('d/m/Y H:i', strtotime($order['review_date'])) ?></small>
                                                </div>
                                                <div class="d-grid mt-3">
                                                    <button class="btn btn-outline-primary beri-rating-btn" type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#reviewModal"
                                                        data-order="<?= htmlspecialchars($order['id_reservasi']) ?>"
                                                        data-ruangan="<?= htmlspecialchars($order['section_room']) ?>"
                                                        data-existing-review="<?= htmlspecialchars($order['review_text']) ?>"
                                                        data-existing-rating="<?= $order['rating'] ?>"
                                                        data-index="<?= $index ?>">
                                                        Edit Review
                                                    </button>
                                                </div>
                                            <?php elseif ($order['payment_status'] == 'expired' && !$order['id_review']): ?>
                                                <!-- Tombol untuk memberi review (hanya jika pembayaran sudah dikonfirmasi) -->
                                                <div class="d-grid mt-3">
                                                    <button class="btn btn-primary beri-rating-btn" type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#reviewModal"
                                                        data-order="<?= htmlspecialchars($order['id_reservasi']) ?>"
                                                        data-ruangan="<?= htmlspecialchars($order['section_room']) ?>"
                                                        data-index="<?= $index ?>">
                                                        Beri Rating
                                                    </button>
                                                </div>
                                            <?php elseif ($order['payment_status'] == 'rejected'): ?>
                                                <div class="alert alert-danger mt-3">
                                                    <h6 class="mb-2"><i class="fas fa-exclamation-triangle"></i> Pembayaran Ditolak</h6>
                                                    <?php if (!empty($order['keterangan_penolakan'])): ?>
                                                        <p class="mb-2 small"><strong>Alasan:</strong> <?= htmlspecialchars($order['keterangan_penolakan']) ?></p>
                                                    <?php endif; ?>
                                                    <p class="mb-0 small">
                                                        <i class="fas fa-phone"></i> Silakan hubungi 
                                                        <a href="https://wa.me/6282123456789" target="_blank" class="text-decoration-none">
                                                            <strong>+6282123456789</strong>
                                                        </a> 
                                                        (admin) untuk informasi lebih lanjut
                                                    </p>
                                                </div>
                                            <?php else: ?>
                                                <!-- Pesan jika pembayaran belum dikonfirmasi -->
                                                <div class="alert alert-info mt-3">
                                                    <?php if ($order['payment_status'] == 'pending'): ?>
                                                        <small><i class="fas fa-info-circle"></i> Review dapat diberikan setelah pembayaran dikonfirmasi admin</small>
                                                    <?php elseif ($order['payment_status'] == 'confirmed'): ?>
                                                        <small><i class="fas fa-info-circle"></i> Review dapat diberikan setelah anda bermain</small>
                                                    <?php else: ?>
                                                        <small><i class="fas fa-exclamation-circle"></i> Silakan upload bukti pembayaran untuk melanjutkan</small>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Modal Review -->
                    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="modalReviewLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="ReviewLabel">Review Order</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="reviewForm" action="profile.php" method="POST">
                                        <input type="hidden" id="orderId" name="order_id">
                                        <div class="mb-3">
                                            <label for="reviewText" class="form-label">Testimony</label>
                                            <textarea class="form-control" id="reviewText" name="review" rows="3" required placeholder="Bagikan pengalaman Anda..."></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Rating</label>
                                            <br>
                                            <div class="star-rating" id="rating">
                                                <input type="radio" id="star5" name="rating" value="5" required />
                                                <label for="star5" title="5 stars"></label>
                                                <input type="radio" id="star4" name="rating" value="4" />
                                                <label for="star4" title="4 stars"></label>
                                                <input type="radio" id="star3" name="rating" value="3" />
                                                <label for="star3" title="3 stars"></label>
                                                <input type="radio" id="star2" name="rating" value="2" />
                                                <label for="star2" title="2 stars"></label>
                                                <input type="radio" id="star1" name="rating" value="1" />
                                                <label for="star1" title="1 star"></label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary" id="submitReviewBtn">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Modal Review -->
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Reset form state when modal is hidden
                            var reviewModal = document.getElementById('reviewModal');
                            reviewModal.addEventListener('hidden.bs.modal', function () {
                                document.getElementById('submitReviewBtn').innerHTML = 'Submit Review';
                                document.getElementById('submitReviewBtn').disabled = false;
                                document.getElementById('reviewForm').reset();
                            });

                            // Modal Event Handler
                            reviewModal.addEventListener('show.bs.modal', function(event) {
                                var button = event.relatedTarget;
                                var orderId = button.getAttribute('data-order');
                                var ruangan = button.getAttribute('data-ruangan');
                                var existingReview = button.getAttribute('data-existing-review');
                                var existingRating = button.getAttribute('data-existing-rating');
                                
                                document.getElementById('orderId').value = orderId;
                                document.getElementById('ReviewLabel').textContent = 'Review Order - ' + ruangan;
                                
                                // Set existing review data if editing
                                if (existingReview) {
                                    document.getElementById('reviewText').value = existingReview;
                                    document.getElementById('submitReviewBtn').textContent = 'Update Review';
                                    
                                    // Set existing rating
                                    if (existingRating) {
                                        var ratingInput = document.querySelector('input[name="rating"][value="' + existingRating + '"]');
                                        if (ratingInput) {
                                            ratingInput.checked = true;
                                        }
                                    }
                                } else {
                                    // Reset form for new review
                                    document.getElementById('reviewText').value = '';
                                    document.getElementById('submitReviewBtn').textContent = 'Submit Review';
                                    
                                    // Clear rating selection
                                    var stars = document.querySelectorAll('input[name="rating"]');
                                    stars.forEach(function(star) { star.checked = false; });
                                }
                            });

                            // Form Submit Handler
                            var reviewForm = document.getElementById('reviewForm');
                            reviewForm.addEventListener('submit', function(event) {
                                var ratingChecked = document.querySelector('input[name="rating"]:checked');
                                var reviewText = document.getElementById('reviewText').value.trim();
                                
                                if (!ratingChecked) {
                                    event.preventDefault();
                                    Swal.fire({
                                        title: 'Peringatan!',
                                        text: 'Silakan pilih rating terlebih dahulu.',
                                        icon: 'warning',
                                        confirmButtonText: 'OK',
                                        customClass: {
                                            popup: 'swal-popup-centered',
                                            icon: 'swal-icon-centered'
                                        }
                                    });
                                    return false;
                                }
                                
                                if (!reviewText) {
                                    event.preventDefault();
                                    Swal.fire({
                                        title: 'Peringatan!',
                                        text: 'Silakan tulis review terlebih dahulu.',
                                        icon: 'warning',
                                        confirmButtonText: 'OK',
                                        customClass: {
                                            popup: 'swal-popup-centered',
                                            icon: 'swal-icon-centered'
                                        }
                                    });
                                    return false;
                                }
                                
                                // Show loading
                                document.getElementById('submitReviewBtn').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
                                document.getElementById('submitReviewBtn').disabled = true;
                            });
                        });
                    </script>
                    
                    <script src="vendor/jquery/jquery.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                    <script src="assets/js/isotope.min.js"></script>
                    <script src="assets/js/owl-carousel.js"></script>
                    <script src="assets/js/counter.js"></script>
                    <script src="assets/js/custom.js"></script>
                    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
                </div>
            </div>
        </div>
    </div>
</body>
</html>