<?php
$current_page = basename($_SERVER['PHP_SELF'], ".php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ... (head sama seperti sebelumnya) ... -->
    <style>
        * {
            user-select: none;
        }
        .nav a {
            position: relative;
            transition: color 0.3s;
            overflow: hidden;
        }
        .nav a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0;
            height: 2px;
            background: #ff6600;
            transition: width 0.4s cubic-bezier(0.4,0,0.2,1);
        }
        .nav a:hover::after,
        .nav a.active::after {
            width: 100%;
        }
        .nav a:hover {
            color: #ff6600;
        }
        #sign-in {
            background-color: #ee626b;
            text-transform: uppercase;
            font-weight: 500;
            padding: 8px 18px;
            border-radius: 2rem;
            margin-top: 10px;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            height: 45px;
            justify-content: center;
            position: sticky;
        }


        #sign-in:hover {
            background-color: transparent;
            border-color: #fff;
            color: #ee626b;
        }


        .header-area #sign-in,
        .header-area.header-sticky #sign-in {
            margin-bottom: 0;
            margin-top: 8px;
            display: flex;
            align-items: center;
            height: 45px;
            justify-content: center;
        }

    </style>
    <!-- Make sure Bootstrap CSS is included in your <head> (add if missing) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Preloader -->
    <div id="preloader" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;background:#fff;display:flex;align-items:center;justify-content:center;">
        <span style="font-size:2rem;color:#ff6600;">Loading...</span>
    </div>
    <!-- Header -->
    <header class="header-area header-sticky">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <!-- Logo -->
                        <a href="index.php" class="logo">
                            <img src="assets/images/PIXEL STATION.png" alt="" style="width: 158px; margin-top: -25px;">
                        </a>
                        <!-- Menu -->
                        <ul class="nav">
                            <li>
                                <a href="home.php" class="<?php echo ($current_page == 'home') ? 'active' : ''; ?>">Home</a>
                            </li>
                            <li>
                                <a href="gamelist.php" class="<?php echo ($current_page == 'gamelist') ? 'active' : ''; ?>">Game List</a>
                            </li>
                            <li>
                                <a href="reservasi.php" class="<?php echo ($current_page == 'reservasi') ? 'active' : ''; ?>">Reservation</a>
                            </li>
                        </ul>
                        <ul class="navbar-nav justify-content-end">
                            <li class="nav-item mr-3"></li>
                        </ul>
                        <div class="ms-auto p-2">
                            <?php
                            if (isset($_SESSION['log_in']) && $_SESSION['log_in'] === true) {
                                // Hide dropdown if on profile.php
                                if ($current_page !== 'profile') { ?>
                                    <ul class="navbar-nav align-items-center">
                                        <li class="nav-item dropdown">
                                            <a class="nav-link p-0 mb-0 d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="height: 100%;">
                                                <img src="../assets/images/profile.png" alt="Profile" width="45" height="45" class="rounded-circle">
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-white text-small shadow p-2" aria-labelledby="profileDropdown" id="profileDropdownMenu">
                                                <li class="d-flex flex-row align-items-center">
                                                    <div class="col m-2">
                                                        <img src="../assets/images/profile.png" alt="mdo" width="55" height="55" class="rounded-circle">
                                                    </div>
                                                    <div class="col m-2">
                                                        <h5 class="text-dark mb-0">Nama</h5>
                                                        <p class="text-dark mb-0">Email</p>
                                                        <p class="text-dark mb-0">Status</p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-dark" href="profile.php">Profile</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-dark" href="logout.php">Sign out</a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                <?php }
                            } else {
                                echo '<li><a id="sign-in" class="btn text-white" href="login.php">Login</a></li>';
                            }
                            ?>
                        </div>
                        <a class='menu-trigger'>
                            <span>Menu</span>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    
    <!-- End Header -->
    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap 5 (pastikan urutan seperti ini) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
