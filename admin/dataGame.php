<?php
session_start();


if (!isset($_SESSION['logged_in'])) {
    header('location: adminLogin.php');
    exit;
}
include ('./layout/sidebar.php');
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
    <title>Admin - Kelola Game</title>
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
                                                    <th>NO</th>
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
                                                <?php 
                                                $no = 1;
                                                while ($row = $games->fetch_assoc()) { ?>
                                                    <tr>
                                                        <td><?php echo $no++; ?></td>
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
            var id_reservasi = button.getAttribute('data-id');

            document.getElementById('customerId').value = id_reservasi;
        });
    });

</script>