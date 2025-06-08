<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    header('location: adminLogin.php');
    exit;
}
include ('./layout/sidebar.php');
include '../server/connection.php';

// Pagination setup
$limit = 5; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ambil filter dari parameter GET
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Hitung total data (dengan filter)
if ($filter == 'all') {
    $total_query = "SELECT COUNT(*) as total FROM room";
} else {
    $total_query = "SELECT COUNT(*) as total FROM room WHERE type_room = '$filter'";
}
$total_result = $conn->query($total_query);
$total_data = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data rooms dengan filter, limit dan offset
if ($filter == 'all') {
    $query = "SELECT * FROM room ORDER BY id_room DESC LIMIT $limit OFFSET $offset";
} else {
    $query = "SELECT * FROM room WHERE type_room = '$filter' ORDER BY id_room DESC LIMIT $limit OFFSET $offset";
}

$rooms = $conn->query($query);

// Alert handler (letakkan di atas <!DOCTYPE html>)
if (isset($_SESSION['alert'])) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {";
    $alert = $_SESSION['alert'];
    if ($alert == 'success') {
        echo "Swal.fire({icon:'success',title:'Berhasil!',text:'Data room berhasil ditambahkan!',confirmButtonText:'OK'});";
    } elseif ($alert == 'fail') {
        echo "Swal.fire({icon:'error',title:'Gagal!',text:'Gagal menambah data room!',confirmButtonText:'OK'});";
    } elseif ($alert == 'uploadfail') {
        echo "Swal.fire({icon:'error',title:'Gagal!',text:'Gagal upload gambar!',confirmButtonText:'OK'});";
    } elseif ($alert == 'ext') {
        echo "Swal.fire({icon:'warning',title:'Peringatan!',text:'Ekstensi file tidak diizinkan!',confirmButtonText:'OK'});";
    } elseif ($alert == 'edit_success') {
        echo "Swal.fire({icon:'success',title:'Berhasil!',text:'Data room berhasil diupdate!',confirmButtonText:'OK'});";
    } elseif ($alert == 'edit_fail') {
        echo "Swal.fire({icon:'error',title:'Gagal!',text:'Gagal mengupdate data room!',confirmButtonText:'OK'});";
    } elseif ($alert == 'delete_success') {
        echo "Swal.fire({icon:'success',title:'Berhasil!',text:'Data room berhasil dihapus!',confirmButtonText:'OK'});";
    } elseif ($alert == 'delete_fail') {
        echo "Swal.fire({icon:'error',title:'Gagal!',text:'Gagal menghapus data room!',confirmButtonText:'OK'});";
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
    <title>Admin - Kelola Room</title>
    <link rel="shortcut icon" href="../dist/assets/compiled/svg/profile.svg" type="image/x-icon">
    <link rel="stylesheet" href="../dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="../dist/assets/compiled/css/app-dark.css">
    <script src="https://kit.fontawesome.com/5f166431bc.js" crossorigin="anonymous"></script>
    <style>
        @media (max-width: 768px) {
            .filter-container {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 10px;
            }
            .filter-container label {
                margin-bottom: 5px !important;
                margin-right: 0 !important;
            }
            .filter-container select {
                width: 100% !important;
                max-width: 200px;
            }
        }
        @media (max-width: 576px) {
            .filter-container select {
                max-width: 100%;
            }
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
                            <h3>Rooms Table</h3>
                            <p class="text-subtitle text-muted">All data rooms available here</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Table</li>
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
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Rooms Table</h4>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddRooms">
                                        <i class="bi bi-plus"></i>
                                    </button>

                                    <!-- Modal Tambah Data Room -->
                                    <div class="modal fade" id="modalAddRooms" tabindex="-1" aria-labelledby="modalAddRoomsLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="addRoom.php" method="POST" enctype="multipart/form-data">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalAddRoomsLabel">Tambah Data Room</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="section_room" class="form-label">Room Section</label>
                                                            <input type="text" class="form-control" id="section_room" name="section_room" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="type_room" class="form-label">Room Type</label>
                                                            <select class="form-select" id="type_room" name="type_room" required>
                                                                <option value="" disabled selected>Pilih Type</option>
                                                                <option value="reguler">Reguler</option>
                                                                <option value="vip">VIP</option>
                                                                <option value="private">Private</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="harga" class="form-label">Harga</label>
                                                            <input type="number" class="form-control" id="harga" name="harga" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="keterangan" class="form-label">Keterangan</label>
                                                            <textarea class="form-control" id="keterangan" name="keterangan" rows="4" required></textarea>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="gambar" class="form-label">Foto</label>
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
                                </div>

                                <!-- Filter Dropdown - Responsive -->
                                <div class="card-header border-0 pb-0">
                                    <div class="d-flex align-items-center filter-container">
                                        <label for="roomTypeFilter" class="form-label me-2 mb-0 text-nowrap">Filter berdasarkan Tipe:</label>
                                        <select class="form-select form-select-sm" id="roomTypeFilter" style="min-width: 150px; max-width: 200px;" onchange="filterRooms(this.value)">
                                            <option value="all" <?php echo ($filter == 'all') ? 'selected' : ''; ?>>All Rooms</option>
                                            <option value="reguler" <?php echo ($filter == 'reguler') ? 'selected' : ''; ?>>Reguler</option>
                                            <option value="vip" <?php echo ($filter == 'vip') ? 'selected' : ''; ?>>VIP</option>
                                            <option value="private" <?php echo ($filter == 'private') ? 'selected' : ''; ?>>Private</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="card-content">
                                    <!-- Info pagination -->
                                    <div class="px-4 py-2">
                                        <medium class="text-muted">
                                            Menampilkan <?php echo min($offset + 1, $total_data); ?> - <?php echo min($offset + $limit, $total_data); ?> dari <?php echo $total_data; ?> data
                                            <?php if ($filter != 'all'): ?>
                                                (difilter berdasarkan <?php echo ucfirst($filter); ?>)
                                            <?php endif; ?>
                                        </medium>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>ROOM SECTION</th>
                                                    <th>ROOM TYPE</th>
                                                    <th>HARGA</th>
                                                    <th>KETERANGAN</th>
                                                    <th>FOTO</th>
                                                    <th>ACTIONS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if ($rooms->num_rows > 0): ?>
                                                <?php 
                                                $no = $offset + 1; // Mulai nomor sesuai halaman
                                                while ($row = $rooms->fetch_assoc()) { ?>
                                                    <tr>
                                                        <td><?php echo $no++; ?></td>
                                                        <td><?php echo $row['section_room']; ?></td>
                                                        <td>
                                                            <span class="badge 
                                                                <?php 
                                                                switch($row['type_room']) {
                                                                    case 'reguler': echo 'bg-success'; break;
                                                                    case 'vip': echo 'bg-warning'; break;
                                                                    case 'private': echo 'bg-danger'; break;
                                                                    default: echo 'bg-secondary';
                                                                }
                                                                ?>">
                                                                <?php echo ucfirst($row['type_room']); ?>
                                                            </span>
                                                        </td>
                                                        <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                                        <td style="max-width: 200px; white-space: normal; word-wrap: break-word;">
                                                            <?php echo $row['keterangan']; ?>
                                                        </td>

                                                        <td>
                                                            <?php if (!empty($row['gambar'])): ?>
                                                                <img src="../assets/images/<?php echo htmlspecialchars($row['gambar']); ?>" width="160" height="85" class="rounded">
                                                            <?php else: ?>
                                                                <span class="text-muted">Tidak ada gambar</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <button 
                                                                class="btn btn-sm btn-warning me-1" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalEditRoom<?php echo $row['id_room']; ?>">
                                                                Edit
                                                            </button>
                                                            <button 
                                                                type="button" 
                                                                class="btn btn-sm btn-danger"
                                                                onclick="praDelete(<?php echo $row['id_room']; ?>)">
                                                                Delete
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <!-- Modal Edit Data Room -->
                                                    <div class="modal fade" id="modalEditRoom<?php echo $row['id_room']; ?>" tabindex="-1" aria-labelledby="modalEditRoomLabel<?php echo $row['id_room']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form action="editRoom.php" method="POST" enctype="multipart/form-data">
                                                                    <input type="hidden" name="id_room" value="<?php echo $row['id_room']; ?>">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="modalEditRoomLabel<?php echo $row['id_room']; ?>">Edit Data Room</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <label for="section_room<?php echo $row['id_room']; ?>" class="form-label">Room Section</label>
                                                                            <input type="text" class="form-control" id="section_room<?php echo $row['id_room']; ?>" name="section_room" value="<?php echo htmlspecialchars($row['section_room']); ?>" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="type_room<?php echo $row['id_room']; ?>" class="form-label">Room Type</label>
                                                                            <select class="form-select" id="type_room<?php echo $row['id_room']; ?>" name="type_room" required>
                                                                                <option value="" disabled>Pilih Type</option>
                                                                                <option value="reguler" <?php if($row['type_room'] == 'reguler') echo 'selected'; ?>>Reguler</option>
                                                                                <option value="vip" <?php if($row['type_room'] == 'vip') echo 'selected'; ?>>VIP</option>
                                                                                <option value="private" <?php if($row['type_room'] == 'private') echo 'selected'; ?>>Private</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="harga<?php echo $row['id_room']; ?>" class="form-label">Harga</label>
                                                                            <input type="number" class="form-control" id="harga<?php echo $row['id_room']; ?>" name="harga" value="<?php echo $row['harga']; ?>" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="keterangan<?php echo $row['id_room']; ?>" class="form-label">Keterangan</label>
                                                                            <textarea class="form-control" id="keterangan<?php echo $row['id_room']; ?>" name="keterangan" required><?php echo htmlspecialchars($row['keterangan']); ?></textarea>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="gambar<?php echo $row['id_room']; ?>" class="form-label">Gambar</label>
                                                                            <input type="file" class="form-control" id="gambar<?php echo $row['id_room']; ?>" name="gambar" accept="image/*">
                                                                            <?php if (!empty($row['gambar'])): ?>
                                                                                <div class="mt-2">
                                                                                    <img src="../assets/images/<?php echo htmlspecialchars($row['gambar']); ?>" width="50" height="50" class="rounded">
                                                                                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">
                                                        <div class="py-4">
                                                            <i class="fas fa-bed fa-3x text-muted mb-3"></i>
                                                            <p class="text-muted">
                                                                <?php 
                                                                if ($filter == 'all') {
                                                                    echo 'Tidak ada data room';
                                                                } else {
                                                                    echo 'Tidak ada room dengan tipe ' . ucfirst($filter);
                                                                }
                                                                ?>
                                                            </p>
                                                        </div>
                                                    </td>
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
                                                    <a class="page-link text-primary" href="<?php echo ($page <= 1) ? '#' : '?page=' . ($page - 1) . ($filter != 'all' ? '&filter=' . $filter : ''); ?>" 
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
                                                    echo '<li class="page-item"><a class="page-link text-primary" href="?page=1' . ($filter != 'all' ? '&filter=' . $filter : '') . '">1</a></li>';
                                                    if ($start_page > 2) {
                                                        echo '<li class="page-item disabled"><span class="page-link text-muted">...</span></li>';
                                                    }
                                                }
                                                
                                                // Tampilkan range halaman
                                                for ($i = $start_page; $i <= $end_page; $i++) {
                                                    $active = ($i == $page) ? 'active' : '';
                                                    if ($active) {
                                                        echo '<li class="page-item active"><a class="page-link bg-primary text-white" href="?page=' . $i . ($filter != 'all' ? '&filter=' . $filter : '') . '">' . $i . '</a></li>';
                                                    } else {
                                                        echo '<li class="page-item"><a class="page-link text-primary" href="?page=' . $i . ($filter != 'all' ? '&filter=' . $filter : '') . '">' . $i . '</a></li>';
                                                    }
                                                }
                                                
                                                // Tampilkan halaman terakhir jika tidak termasuk dalam range
                                                if ($end_page < $total_pages) {
                                                    if ($end_page < $total_pages - 1) {
                                                        echo '<li class="page-item disabled"><span class="page-link text-muted">...</span></li>';
                                                    }
                                                    echo '<li class="page-item"><a class="page-link text-primary" href="?page=' . $total_pages . ($filter != 'all' ? '&filter=' . $filter : '') . '">' . $total_pages . '</a></li>';
                                                }
                                                ?>
                                                
                                                <!-- Next Button -->
                                                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                                    <a class="page-link text-primary" href="<?php echo ($page >= $total_pages) ? '#' : '?page=' . ($page + 1) . ($filter != 'all' ? '&filter=' . $filter : ''); ?>"
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

                <?php
                include ('./layout/adminFooter.php');
                ?>
            </div>
        </div>
        <script src="../dist/assets/static/js/components/dark.js"></script>
        <script src="../dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script src="../dist/assets/compiled/js/app.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="../dist/assets/extensions/apexcharts/apexcharts.min.js"></script>
        <script src="../dist/assets/static/js/pages/dashboard.js"></script>
        <script>
            function praDelete(id) {
                Swal.fire({
                    title: 'Yakin ingin menghapus data room ini?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect dengan mempertahankan halaman dan filter saat ini
                        var currentFilter = '<?php echo $filter; ?>';
                        var currentPage = '<?php echo $page; ?>';
                        var url = 'deleteRoom.php?id_room=' + id + '&page=' + currentPage;
                        if (currentFilter !== 'all') {
                            url += '&filter=' + currentFilter;
                        }
                        window.location.href = url;
                    }
                });
            }

            function filterRooms(type) {
                // Redirect langsung tanpa loading/preloader
                if (type === 'all') {
                    window.location.href = '?';
                } else {
                    window.location.href = '?filter=' + type;
                }
            }
        </script>
</body>

</html>