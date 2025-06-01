<?php
include 'server/connection.php';
session_start();

if (isset($_SESSION['log_in']) && $_SESSION['log_in'] === true) {
    header('location: index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        echo "<script>
                alert('Semua kolom wajib diisi!');
                window.location.href = 'login.php';
              </script>";
        exit;
    }

    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND password = ? LIMIT 1");
    $stmt->bind_param("sss", $username, $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['log_in'] = true;
        $_SESSION['swal_success'] = true;
        } else {
        $_SESSION['swal_error'] = true;
        }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <section class="container">
        <div class="login-container">
            <div class="circle circle-one"></div>
            <div class="form-container">
                <img src="assets/images/controllerlogin.png" alt="illustration" class="illustration"/>
                <h1 class="opacity">LOGIN</h1>
                <form action="login.php" method="POST">
                    <input type="text" name="username" placeholder="USERNAME"  />
                    <input type="password" name="password" placeholder="PASSWORD"  />
                    <button type="submit" class="opacity">SUBMIT</button>
                </form>
                <div class="register-forget opacity">
                    <a href="">BELUM PUNYA AKUN?</a> <br>
                    <a href="register.php">REGISTER</a>
                </div>
            </div>
            <div class="circle circle-two"></div>
        </div>
        <div class="theme-btn-container"></div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="sweetalert2.min.js"></script>
</body>
</html>

<?php if (isset($_SESSION['swal_success'])): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Login Berhasil!',
         text: 'Selamat datang, <?= $_SESSION['nama'] ?? '' ?>!',
        confirmButtonText: 'OK',
        confirmButtonColor: '#967AA1'
    }).then(function() {
        window.location.href = 'index.php';
    });
    <?php unset($_SESSION['swal_success']); ?>
</script>
<?php endif; ?>

<?php if (isset($_SESSION['swal_error'])): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Login gagal!',
        text: 'Username atau passsword salah!',
        confirmButtonText: 'OK',
        confirmButtonColor: '#967AA1'
    });
    <?php unset($_SESSION['swal_error']); ?>
</script>
<?php endif; ?>