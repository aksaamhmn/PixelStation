<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    header('location: adminLogin.php');
    exit;
}
include ('./layout/sidebar.php');
include '../server/connection.php';

// Ambil semua data users untuk DataTables
$query = "SELECT * FROM users ORDER BY id_user ASC";
$result = $conn->query($query);
$users_data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users_data[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola User</title>
    <link rel="shortcut icon" href="../dist/assets/compiled/svg/profile.svg" type="image/x-icon">
    
    <!-- CSS DataTables Bootstrap 5 sesuai template resmi -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
    
    <!-- CSS asli Anda -->
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

            <!-- DataTables section -->
            <section class="section">
                <div class="row" id="table-striped">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Customers Table</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <!-- Tabel dengan template DataTables Bootstrap 5 -->
                                    <table id="example" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>NAMA LENGKAP</th>
                                                <th>USERNAME</th>
                                                <th>EMAIL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users_data as $index => $row): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td class="text-bold-500">
                                                    <img src="../assets/images/profile.png" alt="Profile" width="32" height="32" class="rounded-circle me-2">
                                                    <?= htmlspecialchars($row['nama']) ?>
                                                </td>
                                                <td><?= htmlspecialchars($row['username']) ?></td>
                                                <td><?= htmlspecialchars($row['email']) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <?php include ('./layout/adminFooter.php'); ?>
        </div>
    </div>

    <!-- JavaScript sesuai template resmi DataTables Bootstrap 5 -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>
    
    <!-- Script asli Anda -->
    <script src="../dist/assets/static/js/components/dark.js"></script>
    <script src="../dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>

    <script>
        // Inisialisasi DataTables sesuai template resmi
        new DataTable('#example');
    </script>
</body>

</html>