<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    header('location: adminLogin.php');
    exit;
}
include ('./layout/sidebar.php');
include '../server/connection.php';

// Pagination setup
$limit = 6; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rating_filter = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;
$offset = ($page - 1) * $limit;

// Query untuk mengambil semua review dengan informasi terkait dengan pagination dan filter
$reviews = [];
try {
    // Build WHERE clause untuk filter rating
    $whereClause = "";
    $params = [];
    $paramTypes = "";
    
    if ($rating_filter > 0) {
        $whereClause = " AND rv.rating = ?";
        $params[] = $rating_filter;
        $paramTypes .= "i";
    }
    
    // Hitung total data untuk pagination dengan filter
    $totalQuery = "SELECT COUNT(*) as total FROM review rv
        JOIN reservasi r ON rv.id_reservasi = r.id_reservasi
        JOIN users u ON rv.id_user = u.id_user
        JOIN room rm ON r.id_room = rm.id_room
        JOIN payments p ON r.id_payments = p.id_payments
        WHERE 1=1" . $whereClause;
    
    $totalStmt = $conn->prepare($totalQuery);
    if (!empty($params)) {
        $totalStmt->bind_param($paramTypes, ...$params);
    }
    $totalStmt->execute();
    $totalResult = $totalStmt->get_result();
    $total_data = $totalResult->fetch_assoc()['total'];
    $total_pages = ceil($total_data / $limit);
    $totalStmt->close();

    // Query data dengan pagination dan filter
    $dataQuery = "SELECT 
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
WHERE 1=1" . $whereClause . "
ORDER BY rv.created_at DESC
LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($dataQuery);
    $allParams = array_merge($params, [$limit, $offset]);
    $allParamTypes = $paramTypes . "ii";
    $stmt->bind_param($allParamTypes, ...$allParams);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    echo "<script>console.error('Error query: " . $e->getMessage() . "');</script>";
}

// Query untuk statistik (tetap mengambil semua data untuk perhitungan)
$allReviews = [];
try {
    $allStmt = $conn->prepare("SELECT 
    rv.id_review,
    rv.rating,
    rv.created_at
FROM review rv
JOIN reservasi r ON rv.id_reservasi = r.id_reservasi
JOIN users u ON rv.id_user = u.id_user
JOIN room rm ON r.id_room = rm.id_room
JOIN payments p ON r.id_payments = p.id_payments
ORDER BY rv.created_at DESC");
    
    $allStmt->execute();
    $allResult = $allStmt->get_result();
    
    while ($row = $allResult->fetch_assoc()) {
        $allReviews[] = $row;
    }
    $allStmt->close();
} catch (Exception $e) {
    echo "<script>console.error('Error query statistics: " . $e->getMessage() . "');</script>";
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

// Function untuk menghitung jumlah review per rating
function countReviewsByRating($reviews, $rating) {
    return count(array_filter($reviews, function($r) use ($rating) { 
        return $r['rating'] == $rating; 
    }));
}

// Function untuk generate pagination URL
function generatePageUrl($page, $rating = null) {
    $params = ['page' => $page];
    if ($rating !== null && $rating > 0) {
        $params['rating'] = $rating;
    }
    return '?' . http_build_query($params);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Review</title>
    <link rel="shortcut icon" href="../dist/assets/compiled/svg/profile.svg" type="image/x-icon">
    <link rel="stylesheet" href="../dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="../dist/assets/compiled/css/app-dark.css">
    <script src="https://kit.fontawesome.com/5f166431bc.js" crossorigin="anonymous"></script>
    <style>
        .review-card {
            transition: transform 0.2s;
        }
        .review-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .rating-display {
            font-size: 1.2em;
        }
        .review-text {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .customer-info {
            background-color: #e9ecef;
            padding: 8px;
            border-radius: 5px;
        }
        .reservation-info {
            font-size: 0.9em;
            color: #6c757d;
        }
        .filter-buttons {
            margin: 20px 0;
        }
        .filter-btn {
            margin: 5px;
            transition: all 0.3s ease;
        }
        .filter-btn.active {
            color: white;
            transform: scale(1.05);
        }
        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .hidden {
            display: none !important;
        }
    </style>
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
                            <h3>Customer Reviews</h3>
                            <p class="text-subtitle text-muted">All customer reviews for reservations</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="adminDashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Reviews</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Summary Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Reviews</h5>
                                <h3 class="text-primary"><?= count($allReviews) ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Average Rating</h5>
                                <h3 class="text-success">
                                    <?php 
                                    if (!empty($allReviews)) {
                                        $avgRating = array_sum(array_column($allReviews, 'rating')) / count($allReviews);
                                        echo number_format($avgRating, 1);
                                    } else {
                                        echo "0.0";
                                    }
                                    ?>
                                    <i class="bi bi-star-fill text-warning"></i>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">5 Star Reviews</h5>
                                <h3 class="text-warning">
                                    <?= count(array_filter($allReviews, function($r) { return $r['rating'] == 5; })) ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Latest Review</h5>
                                <h6 class="text-info">
                                    <?php 
                                    if (!empty($allReviews)) {
                                        echo date('d M Y', strtotime($allReviews[0]['created_at']));
                                    } else {
                                        echo "No reviews yet";
                                    }
                                    ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="filter-buttons text-center">
                    <h5 class="mb-3">Filter by Rating</h5>
                    <a href="<?= generatePageUrl(1, 0) ?>" class="btn btn-outline-primary filter-btn <?= $rating_filter == 0 ? 'active' : '' ?>">
                        <i class="bi bi-list"></i> All Reviews
                        <span class="badge bg-primary ms-2"><?= count($allReviews) ?></span>
                    </a>
                    <?php for($i = 5; $i >= 1; $i--): ?>
                        <a href="<?= generatePageUrl(1, $i) ?>" class="btn btn-outline-warning filter-btn <?= $rating_filter == $i ? 'active' : '' ?>">
                            <?php for($j = 1; $j <= $i; $j++): ?>
                                <i class="bi bi-star-fill"></i>
                            <?php endfor; ?>
                            <?php if($i < 5): ?>
                                <?php for($j = $i + 1; $j <= 5; $j++): ?>
                                    <i class="bi bi-star text-muted"></i>
                                <?php endfor; ?>
                            <?php endif; ?>
                            <span class="badge bg-warning ms-2"><?= countReviewsByRating($allReviews, $i) ?></span>
                        </a>
                    <?php endfor; ?>
                </div>

                <!-- Info pagination -->
                <div class="mb-3">
                    <medium class="text-muted">
                        Menampilkan <?php echo min($offset + 1, $total_data); ?> - <?php echo min($offset + $limit, $total_data); ?> dari <?php echo $total_data; ?> review
                        <?php if ($rating_filter > 0): ?>
                            (difilter berdasarkan <?= $rating_filter ?> bintang)
                        <?php endif; ?>
                    </medium>
                </div>

                <section class="section">
                    <?php if (empty($reviews)): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center py-5">
                                        <i class="bi bi-star display-1 text-muted"></i>
                                        <h4 class="mt-3">
                                            <?php if ($rating_filter > 0): ?>
                                                No <?= $rating_filter ?> Star Reviews
                                            <?php else: ?>
                                                No Reviews Yet
                                            <?php endif; ?>
                                        </h4>
                                        <p class="text-muted">
                                            <?php if ($rating_filter > 0): ?>
                                                No customer has submitted a <?= $rating_filter ?> star review yet.
                                            <?php else: ?>
                                                No customer has submitted a review yet.
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row" id="reviewsContainer">
                            <?php foreach ($reviews as $review): ?>
                                <div class="col-md-6 col-lg-4 mb-4 review-item" data-rating="<?= $review['rating'] ?>">
                                    <div class="card review-card h-100">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">
                                                    <i class="bi bi-person-circle"></i>
                                                    <?= htmlspecialchars($review['customer_name']) ?>
                                                </h6>
                                                <div class="rating-display">
                                                    <?= displayStars($review['rating']) ?>
                                                    <small class="text-muted">(<?= $review['rating'] ?>/5)</small>
                                                </div>
                                            </div>
                                            <small class="text-muted">@<?= htmlspecialchars($review['customer_username']) ?></small>
                                        </div>
                                        <div class="card-body">
                                            <div class="review-text mb-3 text-dark">
                                                <p class="mb-0"><?= htmlspecialchars($review['review_text']) ?></p>
                                            </div>
                                            
                                            <div class="customer-info mb-3 text-dark">
                                                <small>
                                                    <strong>Phone:</strong> <?= htmlspecialchars($review['customer_phone']) ?><br>
                                                    <strong>Review Date:</strong> <?= date('d M Y, H:i', strtotime($review['created_at'])) ?>
                                                </small>
                                            </div>
                                            
                                            <div class="reservation-info">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <small>
                                                            <strong>Room:</strong><br>
                                                            <?= htmlspecialchars($review['section_room']) ?><br>
                                                            <span class="badge bg-info"><?= htmlspecialchars($review['type_room']) ?></span>
                                                        </small>
                                                    </div>
                                                    <div class="col-6">
                                                        <small>
                                                            <strong>Reservation:</strong><br>
                                                            <?= date('d M Y', strtotime($review['reservation_date'])) ?><br>
                                                            <?= htmlspecialchars($review['start_time']) ?> - <?= htmlspecialchars($review['end_time']) ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <small>
                                                        <strong>Total:</strong> 
                                                        <span class="text-success">Rp <?= number_format($review['price'], 0, ',', '.') ?></span>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="viewDetailReview('<?= $review['id_review'] ?>')"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#reviewDetailModal"
                                                    data-review='<?= json_encode($review) ?>'>
                                                <i class="bi bi-eye"></i> View Details
                                            </button>
                                            <span class="badge bg-secondary ms-2">
                                                Order #<?= htmlspecialchars($review['id_reservasi']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                        <div class="d-flex justify-content-between align-items-center py-3">
                            <div>
                                <small class="text-muted">Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?></small>
                            </div>
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-sm mb-0">
                                    <!-- Previous Button -->
                                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                        <a class="page-link text-primary" href="<?php echo ($page <= 1) ? '#' : generatePageUrl($page - 1, $rating_filter); ?>" 
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
                                        echo '<li class="page-item"><a class="page-link text-primary" href="' . generatePageUrl(1, $rating_filter) . '">1</a></li>';
                                        if ($start_page > 2) {
                                            echo '<li class="page-item disabled"><span class="page-link text-muted">...</span></li>';
                                        }
                                    }
                                    
                                    // Tampilkan range halaman
                                    for ($i = $start_page; $i <= $end_page; $i++) {
                                        $active = ($i == $page) ? 'active' : '';
                                        if ($active) {
                                            echo '<li class="page-item active"><a class="page-link bg-primary text-white" href="' . generatePageUrl($i, $rating_filter) . '">' . $i . '</a></li>';
                                        } else {
                                            echo '<li class="page-item"><a class="page-link text-primary" href="' . generatePageUrl($i, $rating_filter) . '">' . $i . '</a></li>';
                                        }
                                    }
                                    
                                    // Tampilkan halaman terakhir jika tidak termasuk dalam range
                                    if ($end_page < $total_pages) {
                                        if ($end_page < $total_pages - 1) {
                                            echo '<li class="page-item disabled"><span class="page-link text-muted">...</span></li>';
                                        }
                                        echo '<li class="page-item"><a class="page-link text-primary" href="' . generatePageUrl($total_pages, $rating_filter) . '">' . $total_pages . '</a></li>';
                                    }
                                    ?>
                                    
                                    <!-- Next Button -->
                                    <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                        <a class="page-link text-primary" href="<?php echo ($page >= $total_pages) ? '#' : generatePageUrl($page + 1, $rating_filter); ?>"
                                           <?php echo ($page >= $total_pages) ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </section>

                <!-- Modal Review Detail -->
                <div class="modal fade" id="reviewDetailModal" tabindex="-1" aria-labelledby="reviewDetailModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="reviewDetailModalLabel">Review Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="reviewDetailContent">
                                    <!-- Content will be loaded here via JavaScript -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                  <?php
            include ('./layout/adminFooter.php');
            ?>
            </div>
        </div>
    </div>
    
    <script src="../dist/assets/static/js/components/dark.js"></script>
    <script src="../dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../dist/assets/compiled/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function viewDetailReview(reviewId) {
            // Find the button that triggered the modal
            const button = document.querySelector(`[onclick="viewDetailReview('${reviewId}')"]`);
            const reviewData = JSON.parse(button.getAttribute('data-review'));
            
            // Generate stars for display
            let starsHtml = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= reviewData.rating) {
                    starsHtml += '<i class="bi bi-star-fill text-warning"></i>';
                } else {
                    starsHtml += '<i class="bi bi-star text-muted"></i>';
                }
            }
            
            // Format the content
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Customer Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>${reviewData.customer_name}</td>
                            </tr>
                            <tr>
                                <td><strong>Username:</strong></td>
                                <td>@${reviewData.customer_username}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>${reviewData.customer_phone}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Reservation Details</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Order ID:</strong></td>
                                <td>#${reviewData.id_reservasi}</td>
                            </tr>
                            <tr>
                                <td><strong>Room:</strong></td>
                                <td>${reviewData.section_room} (${reviewData.type_room})</td>
                            </tr>
                            <tr>
                                <td><strong>Date:</strong></td>
                                <td>${new Date(reviewData.reservation_date).toLocaleDateString('id-ID')}</td>
                            </tr>
                            <tr>
                                <td><strong>Time:</strong></td>
                                <td>${reviewData.start_time} - ${reviewData.end_time}</td>
                            </tr>
                            <tr>
                                <td><strong>Total:</strong></td>
                                <td class="text-success">Rp ${parseInt(reviewData.price).toLocaleString('id-ID')}</td>
                                </tr>
                        </table>
                    </div>
                </div>
                <div class="mt-4">
                    <h6>Review</h6>
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="rating-display h5">
                                    ${starsHtml}
                                    <span class="ms-2 text-muted">(${reviewData.rating}/5)</span>
                                </div>
                                <small class="text-muted">
                                    Reviewed on ${new Date(reviewData.created_at).toLocaleDateString('id-ID', {
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })}
                                </small>
                            </div>
                            <blockquote class="blockquote">
                                <p class="mb-0">"${reviewData.review_text}"</p>
                            </blockquote>
                        </div>
                    </div>
                </div>
            `;
            
            // Set the content in modal
            document.getElementById('reviewDetailContent').innerHTML = content;
        }
    </script>
</body>

</html>