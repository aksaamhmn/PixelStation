<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    header('location: adminLogin.php');
    exit;
}
include ('./layout/sidebar.php');
include '../server/connection.php';

// Ambil data rooms untuk ditampilkan di tabel
$query = "SELECT * FROM room";
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
                                <div class="card-content">
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
                                                $no = 1;
                                                while ($row = $rooms->fetch_assoc()) { ?>
                                                    <tr>
                                                        <td><?php echo $no++; ?></td>
                                                        <td><?php echo $row['section_room']; ?></td>
                                                        <td><?php echo ucfirst($row['type_room']); ?></td>
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
                                                    <td colspan="6" class="text-center">Tidak ada data room</td>
                                                </tr>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
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
                        window.location.href = 'deleteRoom.php?id_room=' + id;
                    }
                });
            }
        </script>
</body>

</html>