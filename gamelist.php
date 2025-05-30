<?php
session_start();

include('layout/navbar.php');
include 'server/connection.php';

// --- SECTION: Ambil Parameter URL & Pagination ---
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$mode = isset($_GET['mode_game']) ? trim($_GET['mode_game']) : '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 8;
$offset = ($page - 1) * $limit;

// --- SECTION: Build WHERE Clause ---
$where = [];
$params = [];
$types = '';

// Hanya tambahkan search jika tidak kosong
if ($search !== '') {
  $where[] = "nama LIKE ?";
  $params[] = '%' . $search . '%';
  $types .= 's';
}

if ($mode !== '') {
  $where[] = "mode_game = ?";
  $params[] = $mode;
  $types .= 's';
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// --- SECTION: Total Count for Pagination ---
$totalQuery = "SELECT COUNT(*) as total FROM games $whereClause";
if (!empty($params)) {
  $stmt = $conn->prepare($totalQuery);
  $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $totalResult = $stmt->get_result();
  $stmt->close();
} else {
  $totalResult = $conn->query($totalQuery);
}
$totalRow = $totalResult->fetch_assoc();
$totalGames = $totalRow['total'];
$totalPages = ceil($totalGames / $limit);

// --- SECTION: Fetch Data sesuai Page & Filter ---
$dataQuery = "SELECT * FROM games $whereClause LIMIT ? OFFSET ?";
$stmt = $conn->prepare($dataQuery);
$typesWithLimit = $types . 'ii';
$paramsWithLimit = [...$params, $limit, $offset];
$stmt->bind_param($typesWithLimit, ...$paramsWithLimit);
$stmt->execute();
$games = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- SECTION: Head Meta & CSS -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <title>Pixel Station - Gamelist Page</title>
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* SECTION: Custom Styles */
    .trending-box { transition: opacity 0.3s ease-in-out; }
    .button-filter form button {
    display: inline-block; text-align: center; font-size: 15px; text-transform: uppercase;
    font-weight: 600; color: #1e1e1e; background-color: #eee; padding: 8px 20px;
    border-radius: 25px; border: none; margin: 5px; transition: all 0.3s; cursor: pointer;
    }
    .button-filter form button.active { background-color: #967AA1; color: #fff; }
    .button-filter form button.active:hover { color: #fff; }
    .button-filter form button:hover { color: #fff; background-color: #967AA1; }
  </style>
  <link rel="stylesheet" href="assets/css/fontawesome.css">
  <link rel="stylesheet" href="assets/css/templatemo-lugx-gaming.css">
  <link rel="stylesheet" href="assets/css/owl.css">
  <link rel="stylesheet" href="assets/css/animate.css">
  <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
</head>
<body>

<!-- SECTION: Page Heading -->
<div class="page-heading header-text mb-5">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <h3>Game List</h3>
        <span class="breadcrumb"><a href="index.php">Home</a> > Game list</span>
      </div>
    </div>
  </div>
</div>

<!-- SECTION: Title & Subtitle -->
<div class="col-lg-12 text-center mt-5">
  <div class="section-heading">
    <h2>ALL GAME</h2>
    <h6>FIND YOUR FAVORIT GAME...</h6>
  </div>
</div>

<!-- SECTION: Search & Filter -->
<div class="section trending" >
  <div class="container">
    <!-- Search Form -->
    <div class="search-input">
      <form id="searchForm" method="get" action="">
        <input type="text" class="form-control" name="search" id="searchInput" placeholder="Cari nama game..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" required>
        <button type="submit">Cari</button>
      </form>
    </div>
    <!-- Filter Buttons -->
    <div class="button-filter">
      <form method="get" class="d-flex justify-content-start mt-3 mb-5 gap-2 flex-wrap">
        <button type="submit" name="mode_game" value="" class="btn <?php echo empty($_GET['mode_game']) ? 'active' : ''; ?>">Show All</button>
        <button type="submit" name="mode_game" value="Single Player" class="btn <?php echo ($_GET['mode_game'] ?? '') === 'Single Player' ? 'active' : ''; ?>">Singleplayer</button>
        <button type="submit" name="mode_game" value="Multi Player" class="btn <?php echo ($_GET['mode_game'] ?? '') === 'Multi Player' ? 'active' : ''; ?>">Multiplayer</button>
      </form>
    </div>

    <!-- SECTION: Game List -->
    <div class="row trending-box">
    <?php 
    if ($games->num_rows == 0): ?>
    <div class="col-12 text-center mb-5">
      <div class="gmbr"></div>
      <img src="/assets/images/gamelist-tidak-ditemukan.png" alt="" style="max-width:100px; height: auto;">
      <h5>Game tidak tersedia</h5>
    </div>
    <?php else:
      while ($row = $games->fetch_assoc()): 
      $modalId = 'gameModal' . $row['id_game'];
    ?>
      <!-- Game Card -->
      <div class="col-lg-3 col-md-6 align-self-center mb-30 trending-items adv">
      <div class="item">
        <div class="thumb">
        <a><img src="assets/images/<?php echo $row['gambar']; ?>" alt=""></a>
        </div>
        <div class="down-content">
        <span class="category"><?php echo $row['kategori']; ?></span>
        <h4><?php echo $row['nama']; ?></h4>
        <!-- Info icon (about) -->
        <a href="#" data-toggle="modal" data-target="#<?php echo $modalId; ?>"><i class="fa fa-info-circle"></i></a>
        </div>
      </div>
      </div>

      <!-- Modal for this Game -->
      <div class="modal fade" id="<?php echo $modalId; ?>" tabindex="-1" aria-labelledby="modalgame<?php echo $row['id_game']; ?>" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold" id="modalgame<?php echo $row['id_game']; ?>">
          <i class="fa fa-gamepad text-secondary mr-2"></i> <?php echo $row['nama']; ?>
          </h5>
          <button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="modal" aria-label="Close" style="font-size:1.5rem; background:#f0f0f0; border-radius:50%; width:36px; height:36px; border:1px solid #e0e0e0;">
          <span aria-hidden="true" style="color:#888;">&times;</span>
          </button>
        </div>
        <div class="modal-body p-4">
          <div class="row align-items-center">
          <div class="col-md-5 text-center mb-3 mb-md-0">
            <img src="assets/images/<?php echo $row['gambar']; ?>" alt="" class="img-fluid shadow-sm" style="max-height:260px; border-radius:14px; cursor:pointer;" onclick="showImageModal('assets/images/<?php echo $row['gambar']; ?>')">
          </div>
          <div class="col-md-7">
            <ul class="list-unstyled text-start mb-0" style="font-size: 1rem;">
            <li class="mb-3 d-flex align-items-center">
              <i class="fa fa-tag text-success mr-2" style="width:22px;"></i>
              <span><strong>Nama:</strong> <?php echo $row['nama']; ?></span>
            </li>
            <li class="mb-3 d-flex align-items-center">
              <i class="fa fa-calendar text-info mr-2" style="width:22px;"></i>
              <span><strong>Tahun Rilis:</strong> <?php echo $row['tahun_rilis']; ?></span>
            </li>
            <li class="mb-3 d-flex align-items-center">
              <i class="fa fa-th-large text-warning mr-2" style="width:22px;"></i>
              <span><strong>Kategori:</strong> <?php echo $row['kategori']; ?></span>
            </li>
            <li class="mb-2 d-flex align-items-center">
              <i class="fa fa-users text-danger mr-2" style="width:22px;"></i>
              <span><strong>Mode:</strong> <?php echo $row['mode_game']; ?></span>
            </li>
            </ul>
          </div>
          </div>
        </div>
        </div>
      </div>
      </div>
    <?php 
      endwhile;
    endif;
    ?>
    </div>

    <!-- SECTION: Pagination -->
    <?php if ($games->num_rows > 0): ?>
    <div class="row">
      <div class="col-lg-12 d-flex justify-content-center">
      <ul class="pagination">
      <?php
      $queryParams = $_GET;
      unset($queryParams['page']);
      $baseUrl = '?' . http_build_query($queryParams);

      // Previous
      if ($page > 1): ?>
        <li><a href="<?php echo $baseUrl . '&page=' . ($page - 1); ?>">&lt;</a></li>
      <?php else: ?>
        <li class="disabled"><span>&lt;</span></li>
      <?php endif;

      $start = max(1, $page - 2);
      $end = min($totalPages, $page + 2);

      if ($start > 1) {
        echo '<li><a href="' . $baseUrl . '&page=1">1</a></li>';
        if ($start > 2) echo '<li><span>...</span></li>';
      }

      for ($i = $start; $i <= $end; $i++): ?>
        <li>
        <a href="<?php echo $baseUrl . '&page=' . $i; ?>"<?php if ($i == $page) echo ' class="is_active"'; ?>><?php echo $i; ?></a>
        </li>
      <?php endfor;

      if ($end < $totalPages) {
        if ($end < $totalPages - 1) echo '<li><span>...</span></li>';
        echo '<li><a href="' . $baseUrl . '&page=' . $totalPages . '">' . $totalPages . '</a></li>';
      }

      // Next
      if ($page < $totalPages): ?>
        <li><a href="<?php echo $baseUrl . '&page=' . ($page + 1); ?>">&gt;</a></li>
      <?php else: ?>
        <li class="disabled"><span>&gt;</span></li>
      <?php endif; ?>
      </ul>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- SECTION: Scripts -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<script src="assets/js/isotope.min.js"></script>
<script src="assets/js/owl-carousel.js"></script>
<script src="assets/js/counter.js"></script>
<script src="assets/js/custom.js"></script>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<br><br><br>
<!-- SECTION: Footer -->
<?php include('layout/footer.php') ?>
</body>
</html>
