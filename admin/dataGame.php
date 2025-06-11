<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    header('location: adminLogin.php');
    exit;
}
include ('./layout/sidebar.php');
include '../server/connection.php';

// Ambil semua data games untuk DataTables
$query = "SELECT * FROM games ORDER BY id_game ASC";
$games = $conn->query($query);
$games_data = [];

if ($games->num_rows > 0) {
    while ($row = $games->fetch_assoc()) {
        $games_data[] = $row;
    }
}

// Alert handler
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
        .game-image {
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
                        <h3>Games Table</h3>
                        <p class="text-subtitle text-muted">All data games available here</p>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Games</li>
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
                                <h4 class="card-title">Games Table</h4>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddGame">
                                    <i class="fas fa-plus me-1"></i> Tambah Game
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
                                                <th>TAHUN RILIS</th>
                                                <th>KATEGORI</th>
                                                <th>MODE GAME</th>
                                                <th>FOTO</th>
                                                <th>ACTIONS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($games_data as $index => $row): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td class="text-bold-500"><?= htmlspecialchars($row['nama']) ?></td>
                                                <td><?= htmlspecialchars($row['tahun_rilis']) ?></td>
                                                <td><?= htmlspecialchars($row['kategori']) ?></td>
                                                <td><?= htmlspecialchars($row['mode_game']) ?></td>
                                                <td>
                                                    <?php if (!empty($row['gambar'])): ?>
                                                        <img src="../assets/images/<?= htmlspecialchars($row['gambar']) ?>" 
                                                             alt="Game Image" 
                                                             class="game-image">
                                                    <?php else: ?>
                                                        <span class="text-muted">No Image</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="action-buttons">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-warning" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalEditGame<?= $row['id_game'] ?>"
                                                            title="Edit Game">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger"
                                                            onclick="praDelete(<?= $row['id_game'] ?>)"
                                                            title="Delete Game">
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

            <!-- Modal Edit untuk setiap game -->
            <?php foreach ($games_data as $row): ?>
            <div class="modal fade" id="modalEditGame<?= $row['id_game'] ?>" tabindex="-1" aria-labelledby="modalEditGameLabel<?= $row['id_game'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="editGame.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_game" value="<?= $row['id_game'] ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalEditGameLabel<?= $row['id_game'] ?>">Edit Data Game</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nama<?= $row['id_game'] ?>" class="form-label">Nama Game</label>
                                    <input type="text" class="form-control" id="nama<?= $row['id_game'] ?>" name="nama" value="<?= htmlspecialchars($row['nama']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tahun_rilis<?= $row['id_game'] ?>" class="form-label">Tahun Rilis</label>
                                    <input type="text" class="form-control" id="tahun_rilis<?= $row['id_game'] ?>" name="tahun_rilis" value="<?= htmlspecialchars($row['tahun_rilis']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="kategori<?= $row['id_game'] ?>" class="form-label">Kategori</label>
                                    <select class="form-select" id="kategori<?= $row['id_game'] ?>" name="kategori" required>
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
                                    <label for="mode_game<?= $row['id_game'] ?>" class="form-label">Mode Game</label>
                                    <select class="form-select" id="mode_game<?= $row['id_game'] ?>" name="mode_game" required>
                                        <option value="" disabled>Pilih Mode</option>
                                        <option value="Single Player" <?= ($row['mode_game'] == 'Single Player') ? 'selected' : '' ?>>Single Player</option>
                                        <option value="Multi Player" <?= ($row['mode_game'] == 'Multi Player') ? 'selected' : '' ?>>Multi Player</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="gambar<?= $row['id_game'] ?>" class="form-label">Gambar</label>
                                    <input type="file" class="form-control" id="gambar<?= $row['id_game'] ?>" name="gambar" accept="image/*">
                                    <?php if (!empty($row['gambar'])): ?>
                                        <div class="mt-2">
                                            <img src="../assets/images/<?= htmlspecialchars($row['gambar']) ?>" width="50" height="50" class="rounded">
                                            <small class="text-muted d-block">Biarkan kosong jika tidak ingin mengubah gambar</small>
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
            <?php endforeach; ?>

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
                        targets: [5, 6], // Kolom FOTO (index 5) dan ACTIONS (index 6)
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