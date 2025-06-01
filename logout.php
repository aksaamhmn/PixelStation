<?php
session_start();
session_destroy();

// Clear cookies
foreach ($_COOKIE as $name => $value) {
    setcookie($name, '', time() - 3600, '/');
}

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Logout</title>

    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        html, body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .swal2-popup {
            font-family: 'Poppins', sans-serif !important;
            font-size: 0.95rem !important;
        }

        .swal2-confirm {
            background-color: #967AA1 !important;
        }
    </style>
</head>
<body>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
Swal.fire({
    icon: 'success',
    title: 'Logout Berhasil!',
    text: 'Sampai jumpa kembali!',
    confirmButtonText: 'OK',
    confirmButtonColor: '#967AA1'
}).then(() => {
    window.location.href = 'index.php';
});
</script>

</body>
</html>
