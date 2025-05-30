<?php
session_start();
include './server/connection.php';
include('layout/navbar.php');

// Query untuk mengambil semua review dengan informasi terkait
$reviews = [];
try {
    $stmt = $conn->prepare("SELECT 
    rv.id_review,
    rv.review_text,
    rv.rating,
    rv.created_at,
    u.nama as customer_name,
    u.username as customer_username,
    r.telp as customer_phone,
    r.id_reservasi,
    r.reservation_date,
    r.start_time,
    r.end_time,
    p.amount as price,
    rm.section_room,
    rm.type_room
FROM review rv
JOIN reservasi r ON rv.id_reservasi = r.id_reservasi
JOIN users u ON rv.id_user = u.id_user
JOIN room rm ON r.id_room = rm.id_room
JOIN payments p ON r.id_payments = p.id_payments
ORDER BY rv.created_at DESC");
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    echo "<script>console.error('Error query: " . $e->getMessage() . "');</script>";
}

// Function untuk menampilkan bintang rating
function displayStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '<i class="bi bi-star-fill text-warning"></i>';
        } else {
            $stars .= '<i class="bi bi-star text-muted"></i>';
        }
    }
    return $stars;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <title>PixelStation</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <!-- Additional CSS Files -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/templatemo-lugx-gaming.css">
  <link rel="stylesheet" href="assets/css/owl.css">
  <link rel="stylesheet" href="assets/css/animate.css">
  <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
</head>

<body>
  <div class="main-banner">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 align-self-center">
          <div class="caption header-text">
            <h6>Welcome to Pixel Station</h6>
            <h2>BEST GAMING SITE EVER!</h2>
            <p>
              Pixel Station adalah tempat gaming modern yang menyediakan berbagai fasilitas gaming berkualitas tinggi. Nikmati pengalaman bermain game terbaik dengan perangkat gaming terkini dan suasana yang nyaman.
              <span class="d-flex align-items-center mt-2">
                <i class="fas fa-map-marker-alt me-2" style="color: #ffffff; font-size: 20px;"></i>
                <span>Jl. KHP Hasan Mustafa, Bandung</span>
              </span>
              <a href="reservasi.php" class="btn btn-primary mt-3 reserve-btn">Reserve Now</a>
            </p>
          </div>
        </div>
        <div class="col-lg-4 offset-lg-2">
          <div class="right-image">
            <img src="assets/images/controllerbanner.png" alt="Gaming Controller">
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .reserve-btn {
      background: #967AA1;
      padding: 12px 30px;
      border-color: transparent;
      border-radius: 25px;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .reserve-btn:hover {
      border-color: #fff;
      background: #192A51;
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(25,42,81,0.2);
    }
  </style>

  <div class="features">
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-6">
          <a href="#">
            <div class="item">
                    <div class="image">
                <img src="/assets/images/feature-gamepad.png" alt="" style="max-width: 44px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); transition: transform 0.3s ease;">
              </div>
              <h4>Rent Game</h4>
              <p>Sewa game favorit anda</p>
            </div>
          </a>
        </div>
        <div class="col-lg-3 col-md-6">
          <a href="#">
            <div class="item">
              <div class="image">
                <img src="assets/images/feature-dating.png" alt="" style="max-width: 44px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); transition: transform 0.3s ease;">
              </div>
              <h4>Dating</h4>
              <p>Tempat dating yang nyaman</p>
            </div>
          </a>
        </div>
        <div class="col-lg-3 col-md-6">
          <a href="#">
            <div class="item">
              <div class="image">
                <img src="assets/images/feature-dinner.png" alt="" style="max-width: 44px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); transition: transform 0.3s ease;">
              </div>
              <h4>Launch</h4>
              <p>Makanan dan minuman tersedia</p>
            </div>
          </a>
        </div>
        <div class="col-lg-3 col-md-6">
          <a href="#">
            <div class="item">
              <div class="image">
                <img src="assets/images/feature-sports.png" alt="" style="max-width: 44px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); transition: transform 0.3s ease;">
              </div>
              <h4>Sports</h4>
              <p>Turnamen game sport</p>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Trending Section -->
  <div class="section trending">
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center mt-5">
          <div class="section-heading">
            <h6>Trending</h6>
            <h2>Trending Games</h2>
          </div>
        </div>
        <div class="w-full h-screen flex items-center justify-center bg-gray-100">
          <div class="swiper mySwiper w-full max-w-6xl px-4">
            <div class="swiper-wrapper">
              <div class="swiper-slide">
                <img src="./assets/images/gta.avif" class="rounded-xl w-full h-auto object-cover" />
              </div>
              <div class="swiper-slide">
                <img src="./assets/images/fifa.avif" class="rounded-xl w-full h-auto object-cover" />
              </div>
              <div class="swiper-slide">
                <img src="./assets/images/wuwa.avif" class="rounded-xl w-full h-auto object-cover" />
              </div>
              <div class="swiper-slide">
                <img src="./assets/images/nba.avif" class="rounded-xl w-full h-auto object-cover" />
              </div>
              <div class="swiper-slide">
                <img src="./assets/images/genshin.avif" class="rounded-xl w-full h-auto object-cover" />
              </div>
            </div>
            <div class="swiper-pagination mt-4"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Room Section -->
  <div class="col-lg-12 text-center mt-5">
    <div class="section-heading">
      <h6>Room</h6>
      <h2>Choice Section</h2>
    </div>
  </div>

  <div class="section">
    <div class="container">
      <div class="d-flex flex-column align-items-center">
        <div class="swiper roomPreviewSwiper" style="max-width: 700px;">
          <div class="swiper-wrapper">
            <!-- REGULAR SPOT -->
            <div class="swiper-slide position-relative">
              <img src="assets/images/room.png" alt="Regular Spot" class="w-100 rounded-4 shadow" style="object-fit:cover; height:400px;">
              <button type="button" class="btn btn-info-custom position-absolute bottom-0 start-50 translate-middle-x mb-3" data-bs-toggle="modal" data-bs-target="#modalRegular">
              Info
              </button>
            </div>
            <!-- VIP SPOT -->
            <div class="swiper-slide position-relative">
              <img src="assets/images/room.png" alt="VIP Spot" class="w-100 rounded-4 shadow" style="object-fit:cover; height:400px;">
              <button type="button" class="btn btn-info-custom position-absolute bottom-0 start-50 translate-middle-x mb-3" data-bs-toggle="modal" data-bs-target="#modalVIP">
              Info
              </button>
            </div>
            <!-- PRIVATE SPOT -->
            <div class="swiper-slide position-relative">
              <img src="assets/images/room.png" alt="Private Spot" class="w-100 rounded-4 shadow" style="object-fit:cover; height:400px;">
              <button type="button" class="btn btn-info-custom position-absolute bottom-0 start-50 translate-middle-x mb-3" data-bs-toggle="modal" data-bs-target="#modalPrivate">
              Info
              </button>
            </div>
          </div>
          <div class="swiper-pagination room-preview-pagination mt-3"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Room Info Modals -->
  <div class="modal fade" id="modalRegular" tabindex="-1" aria-labelledby="modalRegularLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-sm" style="border-radius: 18px;">
        <div class="modal-body p-4 text-center">
          <h5 class="fw-bold mb-2" id="modalRegularLabel">REGULAR SPOT</h5>
          <div class="mb-3 text-muted small">Tempat nyaman untuk bermain bersama teman dengan harga terjangkau dan fasilitas standar.</div>
          <ul class="list-unstyled text-start mb-3" style="font-size: 0.97rem;">
            <li><strong>Harga:</strong> Rp 10.000/jam</li>
            <li><strong>Fasilitas:</strong> PC Standar, WiFi, Snack Ringan</li>
            <li><strong>Kapasitas:</strong> 1-4 orang</li>
          </ul>
          <span class="badge px-3 py-1 rounded-pill" style="background: #192A51; color: #fff; font-size: 13px;">Popular</span>
          <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalVIP" tabindex="-1" aria-labelledby="modalVIPLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-sm" style="border-radius: 18px;">
        <div class="modal-body p-4 text-center">
          <h5 class="fw-bold mb-2" id="modalVIPLabel">VIP SPOT</h5>
          <div class="mb-3 text-muted small">Nikmati fasilitas premium, kursi lebih nyaman, dan suasana eksklusif untuk pengalaman bermain terbaik.</div>
          <ul class="list-unstyled text-start mb-3" style="font-size: 0.97rem;">
            <li><strong>Harga:</strong> Rp 25.000/jam</li>
            <li><strong>Fasilitas:</strong> PC High-End, Kursi Gaming, Minuman Gratis</li>
            <li><strong>Kapasitas:</strong> 1-6 orang</li>
          </ul>
          <span class="badge px-3 py-1 rounded-pill" style="background: #2d4fa2; color: #fff; font-size: 13px;">Best Value</span>
          <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalPrivate" tabindex="-1" aria-labelledby="modalPrivateLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-sm" style="border-radius: 18px;">
        <div class="modal-body p-4 text-center">
          <h5 class="fw-bold mb-2" id="modalPrivateLabel">PRIVATE SPOT</h5>
          <div class="mb-3 text-muted small">Ruang privat untuk grup atau event spesial, dengan layanan eksklusif dan privasi penuh.</div>
          <ul class="list-unstyled text-start mb-3" style="font-size: 0.97rem;">
            <li><strong>Harga:</strong> Rp 50.000/jam</li>
            <li><strong>Fasilitas:</strong> Ruang Tertutup, Konsol & PC, Snack & Minuman, Layanan Khusus</li>
            <li><strong>Kapasitas:</strong> 2-10 orang</li>
          </ul>
          <span class="badge px-3 py-1 rounded-pill" style="background: #2d4fa2; color: #fff; font-size: 13px;">Luxury</span>
          <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
    </div>
  </div>

  <style>
    .btn-info-custom {
      background: #192A51;
      color: #fff;
      border: none;
      border-radius: 22px;
      font-weight: 500;
      font-size: 1rem;
      padding: 8px 28px;
      transition: background 0.2s;
      box-shadow: 0 2px 8px rgba(25,42,81,0.08);
    }
    .btn-info-custom:hover, .btn-info-custom:focus {
      background: #2d4fa2;
      color: #fff;
    }
    .modal-content {
      background: #fff;
      border-radius: 18px;
    }
    .modal-body {
      position: relative;
    }
    .modal .btn-close {
      background: none;
      border: none;
      font-size: 1.2rem;
      opacity: 0.7;
      transition: opacity 0.2s;
    }
    .modal .btn-close:hover {
      opacity: 1;
    }
  </style>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      var roomSwiper = new Swiper(".roomPreviewSwiper", {
        effect: "coverflow",
        grabCursor: true,
        centeredSlides: true,
        loop: false,
        slidesPerView: 1,
        spaceBetween: 30,
        coverflowEffect: {
          rotate: 30,
          stretch: 0,
          depth: 150,
          modifier: 1.5,
          slideShadows: true,
        },
        pagination: {
          el: ".room-preview-pagination",
          clickable: true
        },
        autoplay: {
          delay: 2500,
          disableOnInteraction: false
        },
        breakpoints: {
          640: { slidesPerView: 1 },
          768: { slidesPerView: 1 },
          1024: { slidesPerView: 1 }
        }
      });
    });
  </script>

  <!-- Review Section -->
  <div class="col-lg-12 text-center mt-5">
    <div class="section-heading">
      <h6>Review</h6>
      <h2>Any Customer</h2>
    </div>
  </div>

  <!-- Carousel Review -->
  <div id="carouselExampleControls" class="carousel slide carousel-dark" data-bs-ride="carousel" data-bs-interval="4000" style="max-width: 900px; margin: 0 auto; position: relative;">
    <div class="carousel-inner">
      <?php 
      if (!empty($reviews)):
      // Sort reviews by created_at in descending order (newest first)
      usort($reviews, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
      });
      
      $totalReviews = count($reviews);
      $cardsPerSlide = 3;
      $totalSlides = ceil($totalReviews / $cardsPerSlide);
      
      for ($i = 0; $i < $totalSlides; $i++):
        $startIndex = $i * $cardsPerSlide;
      ?>
      <div class="carousel-item <?php echo ($i === 0) ? 'active' : ''; ?>">
        <div class="row justify-content-center">
        <?php 
        for ($j = 0; $j < $cardsPerSlide; $j++):
          $index = $startIndex + $j;
          if ($index < $totalReviews):
          $review = $reviews[$index];
          // Format dates
          $reservationDate = date('d M Y', strtotime($review['reservation_date']));
          $reviewDate = date('d M Y H:i', strtotime($review['created_at']));
        ?>
          <div class="col-md-4 mb-4">
          <div class="card shadow h-100 text-center">
        <div class="card-body">
        <img class="rounded-circle shadow-1-strong mb-3"
          src="/assets/images/profile.png" alt="avatar"
          style="width: 100px;" />
        <h5 class="mb-1"><?php echo htmlspecialchars($review['customer_name']); ?></h5>
        <p class="mb-1 text-muted"><?php echo htmlspecialchars($review['customer_username']); ?></p>
        <p class="text-muted small mb-2">
          <?php echo htmlspecialchars($review['review_text']); ?>
        </p>
        <div class="d-flex justify-content-center mb-2">
          <?php 
          for($star = 1; $star <= 5; $star++): ?>
            <?php if($star <= $review['rating']): ?>
            <i class="bi bi-star-fill mx-1" style="color: #FFD700; text-shadow: 0 0 3px rgba(255,215,0,0.5); font-size: 1.2rem; transition: all 0.3s"></i>
            <?php else: ?>
            <i class="bi bi-star mx-1" style="color: #D3D3D3; font-size: 1.2rem; transition: all 0.3s"></i>
            <?php endif; ?>
          <?php endfor; ?>
        </div>
        <p class="text-muted small mb-0">
          Visited on: <?php echo $reservationDate; ?><br>
          Reviewed on: <?php echo $reviewDate; ?><br>
          Room: <?php echo htmlspecialchars($review['section_room']); ?> - <?php echo htmlspecialchars($review['type_room']); ?>
        </p>
        </div>
          </div>
          </div>
        <?php 
          endif;
        endfor; 
        ?>
        </div>
      </div>
      <?php 
      endfor;
      else: 
      ?>
      <div class="carousel-item active">
        <div class="row justify-content-center">
        <div class="col-12 text-center">
          <p>No reviews available yet.</p>
        </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
    <!-- Carousel controls -->
    <button class="carousel-control-prev custom-carousel-btn" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev"
      style="width:48px; height:48px; top:50%; transform:translateY(-50%); left:-60px; z-index:2; position:absolute;">
      <span class="carousel-control-prev-icon custom-btn-bg" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next custom-carousel-btn" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next"
      style="width:48px; height:48px; top:50%; transform:translateY(-50%); right:-60px; z-index:2; position:absolute;">
      <span class="carousel-control-next-icon custom-btn-bg" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
  <style>
    @media (max-width: 991.98px) {
      #carouselExampleControls .carousel-control-prev,
      #carouselExampleControls .carousel-control-next {
        left: 0 !important;
        right: 0 !important;
        top: auto !important;
        bottom: -56px !important;
        transform: none !important;
        margin: 0 10px;
        position: relative !important;
        display: inline-block;
      }
      #carouselExampleControls .carousel-control-prev {
        float: left;
      }
      #carouselExampleControls .carousel-control-next {
        float: right;
      }
    }
    .custom-btn-bg {
      background-color: #192A51 !important;
      border-radius: 50%;
      background-size: 60% 60%;
      background-position: center;
      background-repeat: no-repeat;
      width: 48px;
      height: 48px;
      box-shadow: 0 2px 8px rgba(25,42,81,0.15);
      transition: background 0.2s;
    }
    .custom-carousel-btn:focus .custom-btn-bg,
    .custom-carousel-btn:hover .custom-btn-bg {
      background-color: #2d4fa2 !important;
    }
    .carousel-control-prev-icon.custom-btn-bg,
    .carousel-control-next-icon.custom-btn-bg {
      filter: invert(1) brightness(5);
    }
  </style>

  

  <!-- Scripts -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/counter.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <style>
    .swiper-pagination-bullet {
      margin: 0 12px !important;
      background-color: #192A51;
    }
    .swiper-pagination {
      position: relative;
      z-index: 10;
      margin-top: 24px;
    }
  </style>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const totalSlides = document.querySelectorAll('.mySwiper .swiper-slide').length;
      const middleIndex = Math.floor(totalSlides / 2);

      const swiper = new Swiper(".mySwiper", {
        effect: "coverflow",
        grabCursor: true,
        centeredSlides: true,
        loop: false,
        slidesPerView: 3,
        spaceBetween: 30,
        initialSlide: middleIndex,
        coverflowEffect: {
          rotate: 30,
          stretch: 0,
          depth: 150,
          modifier: 1.5,
          slideShadows: true,
        },
        pagination: {
          el: ".swiper-pagination",
          clickable: true
        },
        breakpoints: {
          640: { slidesPerView: 1 },
          768: { slidesPerView: 3 },
          1024: { slidesPerView: 5 },
        }
      });
    });
  </script>

  
<?php include('layout/footer.php')?>
</body>
</html>
