<?php
session_start();


if (!isset($_SESSION['logged_in'])) {
    header('location: adminLogin.php');
    exit;
}
include ('./layout/sidebar.php');
include '../server/connection.php';

$result = $conn->query("SELECT * FROM users");

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
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>NAMA LENGKAP</th>
                                                    <th>USERNAME</th>
                                                    <th>EMAIL</th>
                                                    <th>PASSWORD</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $row['id_user'] ?></td>
                                                    <td class="text-bold-500">
                                                        <img src="../assets/images/profile.png" alt="Profile" width="32" height="32" class="rounded-circle me-2">
                                                        <?= $row['nama'] ?>
                                                    </td>
                                                    <td><?= $row['username'] ?></td>
                                                    <td><?= $row['email'] ?></td>
                                                    <td><?= substr(password_hash($row['password'], PASSWORD_DEFAULT), 0, 10) ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
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