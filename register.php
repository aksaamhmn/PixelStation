<?php
    
    include 'server/connection.php';

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nama = trim($_POST['nama']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($nama) || empty($username) || empty($email) || empty($password)) {
            echo "<script>
                    alert('Semua kolom wajib diisi!');
                    window.location.href = 'register.php';
                </script>";
            exit;
        }

        $check = "SELECT * FROM users WHERE email = '$email' OR username = '$username'";
        $result = mysqli_query($conn, $check);

        if (mysqli_num_rows($result) > 0) {
            echo "<script>
                    alert('Email atau Username sudah digunakan!');
                    window.location.href = 'register.php';
                </script>";
            exit;
        }

        $insert = "INSERT INTO users (nama, username, email, password)
                VALUES ('$nama', '$username', '$email', '$password')";

        if (mysqli_query($conn, $insert)) {
            echo "
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Registrasi berhasil!',
                            text: 'Anda akan diarahkan ke halaman login.',
                            icon: 'success'
                        }).then(function() {
                            window.location.href = 'login.php';
                        });
                    });
                </script>";
        } else {
            echo 
            "<script>
                    alert('Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
                    window.location.href = 'register.php';
                </script>";
        }
    }
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Page</title>
  <link rel="stylesheet" href="sweetalert2.min.css">
  <link rel="stylesheet" href="assets/css/register.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  
</head>
<body>
    <section class="container">
        <div class="login-container">
            <div class="circle circle-one"></div>
            <div class="form-container">
                <img src="assets/images/controllerlogin.png" alt="illustration" class="illustration"/>
                <h1 class="opacity">REGISTRASI</h1>
                <form action="register.php" method="POST">
                    <div class="form-grid">
                    <input type="text" name="nama" placeholder="NAMA LENGKAP"/>
                    <input type="text" name="username" placeholder="USERNAME"/>
                    <input type="email" name="email" placeholder="EMAIL"/>
                    <input type="password" name="password" placeholder="PASSWORD"/>
                    </div>
                    <button type="submit" class="opacity">SUBMIT</button>
                </form>
                <div class="register-forget opacity">
                    <a href="">SUDAH PUNYA AKUN?</a> <br>
                    <a href="login.php">LOGIN</a>
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