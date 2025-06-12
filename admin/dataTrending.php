<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    header('location: adminLogin.php');
    exit;
}
include ('./layout/sidebar.php');
include '../server/connection.php';

// Ambil semua data trending games untuk DataTables
$query = "SELECT * FROM trending_games ORDER BY id ASC";
$trending = $conn->query($query);
$trending_data = [];

if ($trending->num_rows > 0) {
    while ($row = $trending->fetch_assoc()) {
        $trending_data[] = $row;
    }
}

// Alert handler - REMOVED EDIT ALERTS
if (isset($_SESSION['alert'])) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {";
    $alert = $_SESSION['alert'];
    if ($alert == 'success') {
        echo "Swal.fire({icon:'success',title:'Berhasil!',text:'Data trending game berhasil ditambahkan!',confirmButtonText:'OK'});";
    } elseif ($alert == 'fail') {
        echo "Swal.fire({icon:'error',title:'Gagal!',text:'Gagal menambah data trending game!',confirmButtonText:'OK'});";
    } elseif ($alert == 'uploadfail') {
        echo "Swal.fire({icon:'error',title:'Gagal!',text:'Gagal upload gambar!',confirmButtonText:'OK'});";
    } elseif ($alert == 'ext') {
        echo "Swal.fire({icon:'warning',title:'Peringatan!',text:'Ekstensi file tidak diizinkan!',confirmButtonText:'OK'});";
    } elseif ($alert == 'delete_success') {
        echo "Swal.fire({icon:'success',title:'Berhasil!',text:'Data trending game berhasil dihapus!',confirmButtonText:'OK'});";
    } elseif ($alert == 'delete_fail') {
        echo "Swal.fire({icon:'error',title:'Gagal!',text:'Gagal menghapus data trending game!',confirmButtonText:'OK'});";
    }
    echo "});
    </script>";
    unset($_SESSION['alert']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Trending Games</title>
    <link rel="shortcut icon" href="../dist/assets/compiled/svg/profile.svg" type="image/x-icon">
    
    <!-- CSS DataTables Bootstrap 5 sesuai template resmi -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
    
    <!-- CSS asli Anda -->
    <link rel="stylesheet" href="../dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="../dist/assets/compiled/css/app-dark.css">
    
    <script src="https://kit.fontawesome.com/5f166431bc.js" crossorigin="anonymous"></script>
    
    <style>
        /* Styling untuk action buttons agar rapi */
        .action-buttons {
            white-space: nowrap;
        }
        .action-buttons .btn {
            margin: 0 2px;
            padding: 0.25rem 0.5rem;
        }
        .trending-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 0.375rem;
        }

        /* CSS sederhana untuk pagination primary - hanya 1 rule! */
        .paginate_button.current {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #ffffff !important;
        }
    </style>
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
                        <h3>Trending Games Table</h3>
                        <p class="text-subtitle text-muted">All trending games data available here</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Trending Games</li>
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
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Trending Games Table</h4>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddTrending">
                                    <i class="fas fa-plus me-1"></i> Tambah Trending Game
                                </button>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <!-- Tabel dengan template DataTables Bootstrap 5 -->
                                    <table id="example" class="table table-striped" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>NAMA GAME</th>
                                                <th>GAMBAR</th>
                                                <th>ACTIONS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($trending_data as $index => $row): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td class="text-bold-500"><?= htmlspecialchars($row['nama']) ?></td>
                                                <td>
                                                    <?php if (!empty($row['gambar'])): ?>
                                                        <img src="../assets/images/<?= htmlspecialchars($row['gambar']) ?>" 
                                                             alt="Trending Game Image" 
                                                             class="trending-image">
                                                    <?php else: ?>
                                                        <span class="text-muted">No Image</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="action-buttons">
                                                    <!-- REMOVED EDIT BUTTON -->
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger"
                                                            onclick="praDelete(<?= $row['id'] ?>)"
                                                            title="Delete Trending Game">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
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

            <!-- Modal Tambah Data Trending Game -->
            <div class="modal fade" id="modalAddTrending" tabindex="-1" aria-labelledby="modalAddTrendingLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="addTrending.php" method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalAddTrendingLabel">Tambah Data Trending Game</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Game</label>
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                </div>
                                <div class="mb-3">
                                    <label for="gambar" class="form-label">Gambar</label>
                                    <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Tambah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- REMOVED ALL EDIT MODALS -->

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables dengan konfigurasi yang lebih spesifik
            $('#example').DataTable({
                columnDefs: [
                    {
                        targets: [2, 3], // Kolom GAMBAR (index 2) dan ACTIONS (index 3)
                        searchable: false, // Disable search/filter
                        orderable: false   // Disable sorting juga sekalian
                    }
                ],
                // Konfigurasi lainnya bisa ditambah di sini jika diperlukan
                destroy: true // Pastikan tidak ada konflik dengan inisialisasi sebelumnya
            });
        });

        // Fungsi delete dengan SweetAlert
        function praDelete(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus data trending game ini?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'deleteTrending.php?id=' + id;
                }
            });
        }
    </script>
</body>
</html>