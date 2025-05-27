<?php
session_start();
$_SESSION['logout_success'] = true;
$_SESSION = array();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Clear all cookies
foreach ($_COOKIE as $name => $value) {
    setcookie($name, '', time() - 3600, '/');
}

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .custom-popup-class {
            font-size: 1rem !important;
        }
        .swal2-popup {
            font-size: 0.9rem !important;
        }
        .swal2-confirm {
            background-color: #967AA1 !important;
        }
    </style>
</head>
<body>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Logout Berhasil!',
        text: 'Sampai jumpa admin!',
        confirmButtonText: 'OK',
        confirmButtonColor: '#967AA1'
    }).then((result) => {
        window.location.href = 'adminLogin.php';
    });
    </script>
</body>
</html>
<?php exit(); ?>
