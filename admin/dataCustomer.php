<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    header('location: adminLogin.php');
    exit;
}
include ('./layout/sidebar.php');
include '../server/connection.php';

// Pagination setup
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$total_query = "SELECT COUNT(*) as total FROM users";
$total_result = $conn->query($total_query);
$total_data = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data users dengan limit dan offset
$query = "SELECT * FROM users LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola User</title>
    <link rel="shortcut icon" href="../dist/assets/compiled/svg/profile.svg" type="image/x-icon">
    <link rel="stylesheet" href="../dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="../dist/assets/compiled/css/app-dark.css">
    <script src="https://kit.fontawesome.com/5f166431bc.js" crossorigin="anonymous"></script>
</head>

<body>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Customer Table</h3>
                            <p class="text-subtitle text-muted">All data customers available here</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Customer</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Striped rows start -->
                <section class="section">
                    <div class="row" id="table-striped">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Customers Table</h4>
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
                                                    <th>NAMA LENGKAP</th>
                                                    <th>USERNAME</th>
                                                    <th>EMAIL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if ($result->num_rows > 0): ?>
                                                <?php 
                                                $no = $offset + 1; // Mulai nomor sesuai halaman
                                                while ($row = $result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo $no++; ?></td>
                                                        <td class="text-bold-500">
                                                            <img src="../assets/images/profile.png" alt="Profile" width="32" height="32" class="rounded-circle me-2">
                                                            <?= $row['nama'] ?>
                                                        </td>
                                                        <td><?= $row['username'] ?></td>
                                                        <td><?= $row['email'] ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">Tidak ada data customer</td>
                                                </tr>
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
                <!-- Striped rows end -->

                <div class="modal fade" id="modalChange" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalReviewLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="ReviewLabel">Select Game Worker</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="changeForm" action="actionCustomerToWorker.php" method="POST">
                                    <input type="hidden" id="customerId" name="customer_id">
                                    <select class="form-select" name="game_id">
                                        <option selected>Select Game</option>
                                        <option value="1">Mobile Legends</option>
                                        <option value="2">Valorant</option>
                                        <option value="3">Counter Strike 2</option>
                                        <option value="4">Dota 2</option>
                                        <option value="5">League Of Legends</option>
                                        <option value="6">PUBG Mobile</option>
                                    </select>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            include ('./layout/adminFooter.php');
            ?>
            </div>
        </div>
        <script src="../dist/assets/static/js/components/dark.js"></script>
        <script src="../dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalChange = document.getElementById('modalChange');

        modalChange.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var orderId = button.getAttribute('data-id');

            document.getElementById('customerId').value = orderId;
        });
    });
</script>