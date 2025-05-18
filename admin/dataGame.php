<?php
session_start();
include '../server/connection.php';

// Ambil data games untuk ditampilkan di tabel
$query = "SELECT * FROM games";
$games = $conn->query($query);

// Alert handler (letakkan di atas <!DOCTYPE html>)
if (isset($_SESSION['alert'])) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {";
    $alert = $_SESSION['alert'];
    if ($alert == 'success') {
        echo "Swal.fire({icon:'success',title:'Berhasil!',text:'Data game berhasil ditambahkan!',confirmButtonText:'OK'});";
    } elseif ($alert == 'fail') {
        echo "Swal.fire({icon:'error',title:'Gagal!',text:'Gagal menambah data game!',confirmButtonText:'OK'});";
    } elseif ($alert == 'uploadfail') {
        echo "Swal.fire({icon:'error',title:'Gagal!',text:'Gagal upload gambar!',confirmButtonText:'OK'});";
    } elseif ($alert == 'ext') {
        echo "Swal.fire({icon:'warning',title:'Peringatan!',text:'Ekstensi file tidak diizinkan!',confirmButtonText:'OK'});";
    } elseif ($alert == 'edit_success') {
        echo "Swal.fire({icon:'success',title:'Berhasil!',text:'Data game berhasil diupdate!',confirmButtonText:'OK'});";
    } elseif ($alert == 'edit_fail') {
        echo "Swal.fire({icon:'error',title:'Gagal!',text:'Gagal mengupdate data game!',confirmButtonText:'OK'});";
    } elseif ($alert == 'delete_success') {
        echo "Swal.fire({icon:'success',title:'Berhasil!',text:'Data game berhasil dihapus!',confirmButtonText:'OK'});";
    } elseif ($alert == 'delete_fail') {
        echo "Swal.fire({icon:'error',title:'Gagal!',text:'Gagal menghapus data game!',confirmButtonText:'OK'});";
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
    <title>Pixel Station Admin</title>
    <link rel="shortcut icon" href="../dist/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="../dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="../dist/assets/compiled/css/app-dark.css">
</head>

<body>
    <script src="../dist/assets/static/js/initTheme.js"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="logo">
                            <a href="dashboard.php">Pixel Station</a>
                        </div>
                        <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--system-uicons" width="20" height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                                <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2" opacity=".3"></path>
                                    <g transform="translate(-210 -1)">
                                        <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                        <circle cx="220.5" cy="11.5" r="4"></circle>
                                        <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2">
                                        </path>
                                    </g>
                                </g>
                            </svg>
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input  me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                                <label class="form-check-label"></label>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--mdi" width="20" height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                <path fill="currentColor" d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                                </path>
                            </svg>
                        </div>
                        <div class="sidebar-toggler  x">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item">
                            <a href="adminDashboard.html" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item active">
                            <a href="dataGame.html" class='sidebar-link'>
                                <i class="bi bi-controller"></i>
                                <span>Kelola Game</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="dataCustomer.html" class='sidebar-link'>
                                <i class="bi bi-people-fill"></i>
                                <span>Customers</span>
                            </a>
                        </li>

                        <li class="sidebar-item ">
                            <a href="dataReservasi.html" class='sidebar-link'>
                                <i class="bi bi-basket3-fill"></i>
                                <span>Reservation</span>
                            </a>
                        </li>

                        <li class="sidebar-item ">
                            <a href="adminLogout.html" class='sidebar-link'>
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
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
                            <h3>Games Table</h3>
                            <p class="text-subtitle text-muted">All data games available here</p>
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
                                    <h4 class="card-title">Games Table</h4>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddGame">
                                        <i class="bi bi-plus"></i>
                                    </button>

                                    <!-- Modal Tambah Data Game -->
                                    <div class="modal fade" id="modalAddGame" tabindex="-1" aria-labelledby="modalAddGameLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="addGame.php" method="POST" enctype="multipart/form-data">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalAddGameLabel">Tambah Data Game</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="nama" class="form-label">Nama Game</label>
                                                            <input type="text" class="form-control" id="nama" name="nama" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="tahun_rilis" class="form-label">Tahun Rilis</label>
                                                            <input type="text" class="form-control" id="tahun_rilis" name="tahun_rilis" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="kategori" class="form-label">Kategori</label>
                                                            <select class="form-select" id="kategori" name="kategori" required>
                                                                <option value="" disabled selected>Pilih Kategori</option>
                                                                <option value="Action">Action</option>
                                                                <option value="Adventure">Adventure</option>
                                                                <option value="FPS">FPS</option>
                                                                <option value="RPG">RPG</option>
                                                                <option value="Strategy">Strategy</option>
                                                                <option value="Simulation">Simulation</option>
                                                                <option value="Sports">Sports</option>
                                                                <option value="Racing">Racing</option>
                                                                <option value="Puzzle">Puzzle</option>
                                                                <option value="Shooter">Shooter</option>
                                                                <option value="Horror">Horror</option>
                                                                <option value="Fighting">Fighting</option>
                                                                <option value="Platformer">Platformer</option>
                                                                <option value="MMO">MMO</option>
                                                                <option value="Stealth">Stealth</option>
                                                                <option value="Sandbox">Sandbox</option>
                                                                <option value="Battle Royale">Battle Royale</option>
                                                                <option value="Card Game">Card Game</option>
                                                                <option value="Music/Rhythm">Music/Rhythm</option>
                                                                <option value="Party">Party</option>
                                                                <option value="Educational">Educational</option>
                                                                <option value="Open World">Open World</option>
                                                                <option value="Visual Novel">Visual Novel</option>
                                                                <option value="Indie">Indie</option>
                                                                <option value="Arcade">Arcade</option>
                                                                <option value="Other">Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="mode_game" class="form-label">Mode Game</label>
                                                            <select class="form-select" id="mode_game" name="mode_game" required>
                                                                <option value="" disabled selected>Pilih Mode</option>
                                                                <option value="Single Player">Single Player</option>
                                                                <option value="Multi Player">Multi Player</option>
                                                            </select>
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
                                                    <th>ID</th>
                                                    <th>NAMA GAME</th>
                                                    <th>TAHUN RILIS</th>
                                                    <th>KATEGORI</th>
                                                    <th>MODE GAME</th>
                                                    <th>FOTO</th>
                                                    <th>ACTIONS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if ($games->num_rows > 0): ?>
                                                <?php while ($row = $games->fetch_assoc()) { ?>
                                                    <tr>
                                                        <td><?php echo $row['id_game']; ?></td>
                                                        <td><?php echo $row['nama']; ?></td>
                                                        <td><?php echo $row['tahun_rilis']; ?></td>
                                                        <td><?php echo $row['kategori']; ?></td>
                                                        <td><?php echo $row['mode_game']; ?></td>
                                                        <td>
                                                            <?php if (!empty($row['gambar'])): ?>
                                                                <img src="../assets/images/<?php echo htmlspecialchars($row['gambar']); ?>" width="50" height="50" class="rounded">
                                                            <?php else: ?>
                                                                <span class="text-muted">Tidak ada gambar</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <button 
                                                                class="btn btn-sm btn-warning me-1" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalEditGame<?php echo $row['id_game']; ?>">
                                                                Edit
                                                            </button>
                                                            <button 
                                                                type="button" 
                                                                class="btn btn-sm btn-danger"
                                                                onclick="praDelete(<?php echo $row['id_game']; ?>)">
                                                                Delete
                                                            </button>
                                                            
                                                        </td>
                                                    </tr>

                                                    <!-- Modal Edit Data Game -->
                                                    <div class="modal fade" id="modalEditGame<?php echo $row['id_game']; ?>" tabindex="-1" aria-labelledby="modalEditGameLabel<?php echo $row['id_game']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form action="editGame.php" method="POST" enctype="multipart/form-data">
                                                                    <input type="hidden" name="id_game" value="<?php echo $row['id_game']; ?>">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="modalEditGameLabel<?php echo $row['id_game']; ?>">Edit Data Game</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <label for="nama<?php echo $row['id_game']; ?>" class="form-label">Nama Game</label>
                                                                            <input type="text" class="form-control" id="nama<?php echo $row['id_game']; ?>" name="nama" value="<?php echo htmlspecialchars($row['nama']); ?>" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="tahun_rilis<?php echo $row['id_game']; ?>" class="form-label">Tahun Rilis</label>
                                                                            <input type="text" class="form-control" id="tahun_rilis<?php echo $row['id_game']; ?>" name="tahun_rilis" value="<?php echo htmlspecialchars($row['tahun_rilis']); ?>" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="kategori<?php echo $row['id_game']; ?>" class="form-label">Kategori</label>
                                                                            <select class="form-select" id="kategori<?php echo $row['id_game']; ?>" name="kategori" required>
                                                                                <option value="" disabled>Pilih Kategori</option>
                                                                                <?php
                                                                                    $kategoriList = [
                                                                                        "Action","Adventure","FPS","RPG","Strategy","Simulation","Sports","Racing","Puzzle","Shooter","Horror","Fighting","Platformer","MMO","Stealth","Sandbox","Battle Royale","Card Game","Music/Rhythm","Party","Educational","Open World","Visual Novel","Indie","Arcade","Other"
                                                                                    ];
                                                                                    foreach ($kategoriList as $kategori) {
                                                                                        $selected = ($row['kategori'] == $kategori) ? 'selected' : '';
                                                                                        echo "<option value=\"$kategori\" $selected>$kategori</option>";
                                                                                    }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="mode_game<?php echo $row['id_game']; ?>" class="form-label">Mode Game</label>
                                                                            <select class="form-select" id="mode_game<?php echo $row['id_game']; ?>" name="mode_game" required>
                                                                                <option value="" disabled>Pilih Mode</option>
                                                                                <option value="Single Player" <?php if($row['mode_game'] == 'Single Player') echo 'selected'; ?>>Single Player</option>
                                                                                <option value="Multi Player" <?php if($row['mode_game'] == 'Multi Player') echo 'selected'; ?>>Multi Player</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="gambar<?php echo $row['id_game']; ?>" class="form-label">Gambar</label>
                                                                            <input type="file" class="form-control" id="gambar<?php echo $row['id_game']; ?>" name="gambar" accept="image/*">
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
                                                    <td colspan="7" class="text-center">Tidak ada data game</td>
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

                <footer>
                    <div class="footer clearfix mb-0 text-muted">
                        <div class="float-start">
                            <p>2023 &copy; Mazer</p>
                        </div>
                        <div class="float-end">
                            <p>Crafted with <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span>
                                by <a href="https://saugi.me">Saugi</a></p>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="../dist/assets/static/js/components/dark.js"></script>
        <script src="../dist/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script src="../dist/assets/compiled/js/app.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function praDelete(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus data game ini?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                window.location.href = 'deleteGame.php?id_game=' + id;
                }
            });
            }
        </script>
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