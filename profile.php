<?php
    session_start();
    include('layout/navbar.php');
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
    <link rel="stylesheet" href="assets/css/templatemo-lugx-gaming.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
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

    <?php
    // Dummy data riwayat orderan (10 data)
    $orders = [
        [
            'tanggal' => '2024-06-01',
            'ruangan' => 'Reguler 1',
            'tipe' => 'Reguler',
            'waktu' => '10:00 - 12:00',
            'status' => 'Selesai',
            'total' => 'Rp 100.000'
        ],
        [
            'tanggal' => '2024-06-03',
            'ruangan' => 'Reguler 2',
            'tipe' => 'Reguler',
            'waktu' => '13:00 - 15:00',
            'status' => 'Selesai',
            'total' => 'Rp 120.000'
        ],
        [
            'tanggal' => '2024-06-05',
            'ruangan' => 'VIP 2',
            'tipe' => 'VIP',
            'waktu' => '15:00 - 18:00',
            'status' => 'Dibatalkan',
            'total' => 'Rp 200.000'
        ],
        [
            'tanggal' => '2024-06-07',
            'ruangan' => 'VIP 1',
            'tipe' => 'VIP',
            'waktu' => '18:00 - 21:00',
            'status' => 'Selesai',
            'total' => 'Rp 250.000'
        ],
        [
            'tanggal' => '2024-06-10',
            'ruangan' => 'Private 1',
            'tipe' => 'Private',
            'waktu' => '09:00 - 12:00',
            'status' => 'Menunggu',
            'total' => 'Rp 150.000'
        ],
        [
            'tanggal' => '2024-06-12',
            'ruangan' => 'Private 2',
            'tipe' => 'Private',
            'waktu' => '12:00 - 15:00',
            'status' => 'Selesai',
            'total' => 'Rp 180.000'
        ],
        [
            'tanggal' => '2024-06-14',
            'ruangan' => 'Reguler 3',
            'tipe' => 'Reguler',
            'waktu' => '15:00 - 17:00',
            'status' => 'Menunggu',
            'total' => 'Rp 110.000'
        ],
        [
            'tanggal' => '2024-06-16',
            'ruangan' => 'VIP 3',
            'tipe' => 'VIP',
            'waktu' => '17:00 - 20:00',
            'status' => 'Dibatalkan',
            'total' => 'Rp 220.000'
        ],
        [
            'tanggal' => '2024-06-18',
            'ruangan' => 'Private 3',
            'tipe' => 'Private',
            'waktu' => '08:00 - 11:00',
            'status' => 'Selesai',
            'total' => 'Rp 170.000'
        ],
        [
            'tanggal' => '2024-06-20',
            'ruangan' => 'Reguler 4',
            'tipe' => 'Reguler',
            'waktu' => '11:00 - 13:00',
            'status' => 'Menunggu',
            'total' => 'Rp 130.000'
        ],
    ];
    ?>

    <div class="contact-page section mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="section-heading text-center">
                        <h2>RIWAYAT ORDERAN</h2>
                    </div>
                    <div class="right-content">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
                            <?php foreach($orders as $index => $order): ?>
                            <div class="col">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title mb-2">
                                            <?= htmlspecialchars($order['ruangan']) ?>
                                            <span class="badge bg-info text-dark ms-2"><?= htmlspecialchars($order['tipe']) ?></span>
                                        </h5>
                                        <p class="card-text mb-1">
                                            <strong>Tanggal:</strong> <?= htmlspecialchars($order['tanggal']) ?><br>
                                            <strong>Waktu:</strong> <?= htmlspecialchars($order['waktu']) ?><br>
                                            <strong>Status:</strong>
                                            <?php
                                                $badge = 'secondary';
                                                if ($order['status'] == 'Selesai') $badge = 'success';
                                                elseif ($order['status'] == 'Dibatalkan') $badge = 'danger';
                                                elseif ($order['status'] == 'Menunggu') $badge = 'warning';
                                            ?>
                                            <span class="badge bg-<?= $badge ?>"><?= htmlspecialchars($order['status']) ?></span>
                                        </p>
                                        <p class="card-text"><strong>Total:</strong> <?= htmlspecialchars($order['total']) ?></p>
                                        <div class="d-grid mt-3">
                                            <button class="btn btn-primary beri-rating-btn" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#reviewModal"
                                                data-order="<?= htmlspecialchars($order['ruangan']) ?>"
                                                data-index="<?= $index ?>">
                                                Beri Rating
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Modal Review -->
                    <div class="modal fade" id="reviewModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalReviewLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="ReviewLabel">Review Order</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="reviewForm" action="reviewOrder.php" method="POST">
                                        <input type="hidden" id="orderId" name="order_id">
                                        <div class="mb-3">
                                            <label for="reviewText" class="form-label">Testimony</label>
                                            <textarea class="form-control" id="reviewText" name="review" rows="3" required></textarea>
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
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <style>
                        .star-rating {
                            direction: rtl;
                            display: inline-flex;
                            font-size: 2rem;
                            gap: 0.2em;
                        }
                        .star-rating input[type="radio"] {
                            display: none;
                        }
                        .star-rating label {
                            color: #ddd;
                            cursor: pointer;
                            transition: color 0.2s;
                            position: relative;
                        }
                        .star-rating label:before {
                            content: "\f005";
                            font-family: "Font Awesome 5 Free";
                            font-weight: 900;
                            position: relative;
                            display: inline-block;
                        }
                        .star-rating input[type="radio"]:checked ~ label:before,
                        .star-rating label:hover:before,
                        .star-rating label:hover ~ label:before {
                            color: #ffc107;
                        }
                    </style>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var reviewModal = document.getElementById('reviewModal');
                            reviewModal.addEventListener('show.bs.modal', function(event) {
                                var button = event.relatedTarget;
                                var orderId = button.getAttribute('data-order');
                                document.getElementById('orderId').value = orderId;
                                document.getElementById('reviewText').value = '';
                                var stars = document.querySelectorAll('input[name="rating"]');
                                stars.forEach(function(star) { star.checked = false; });
                            });

                            var reviewForm = document.getElementById('reviewForm');
                            reviewForm.addEventListener('submit', function(event) {
                                var ratingChecked = document.querySelector('input[name="rating"]:checked');
                                if (!ratingChecked) {
                                    event.preventDefault();
                                    alert('Please select a rating.');
                                }
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
