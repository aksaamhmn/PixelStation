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
            echo "<script>
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            box-sizing: border-box;
            font-family: "poppins";
            background: #192A51;
            color: #FFFFFF;
            letter-spacing: 1px;
            transition: background 0.2s ease;
            -webkit-transition: background 0.2s ease;
            -moz-transition: background 0.2s ease;
            -ms-transition: background 0.2s ease;
            -o-transition: background 0.2s ease;
        }
        a { text-decoration: none; color: #FFFFFF; }
        h1 { font-size: 2.5rem; }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container { position: relative; width: 50rem; }
        .form-container {
            border: 1px solid hsla(0, 0%, 65%, 0.158);
            box-shadow: 0 0 36px 1px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            backdrop-filter: blur(20px);
            z-index: 99;
            padding: 2rem;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            -ms-border-radius: 10px;
            -o-border-radius: 10px;
        }
        .login-container form input {
            display: block;
            padding: 14.5px;
            width: 100%;
            margin: 0.5rem 0;
            color: #FFFFFF;
            outline: none;
            background-color: #9191911f;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            letter-spacing: 0.8px;
            font-size: 15px;
            backdrop-filter: blur(15px);
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            -ms-border-radius: 5px;
            -o-border-radius: 5px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .login-container form input:focus {
            box-shadow: 0 0 16px 1px rgba(0, 0, 0, 0.2);
            animation: wobble 0.3s ease-in;
            -webkit-animation: wobble 0.3s ease-in;
        }
        .login-container form button {
            margin-top: 2rem;
            background-color: #967AA1;
            color: #FFFFFF;
            display: block;
            padding: 13px;
            border-radius: 5px;
            outline: none;
            font-size: 18px;
            letter-spacing: 1.5px;
            font-weight: bold;
            width: 100%;
            cursor: pointer;
            margin-bottom: 2rem;
            transition: all 0.1s ease-in-out;
            border: none;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            -ms-border-radius: 5px;
            -o-border-radius: 5px;
            -webkit-transition: all 0.1s ease-in-out;
            -moz-transition: all 0.1s ease-in-out;
            -ms-transition: all 0.1s ease-in-out;
            -o-transition: all 0.1s ease-in-out;
        }
        .login-container form button:hover {
            box-shadow: 0 0 10px 1px rgba(0, 0, 0, 0.15);
            transform: scale(1.02);
            -webkit-transform: scale(1.02);
            -moz-transform: scale(1.02);
            -ms-transform: scale(1.02);
            -o-transform: scale(1.02);
        }
        .circle {
            width: 8rem;
            height: 8rem;
            background: #967AA1;
            border-radius: 50%;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            -ms-border-radius: 50%;
            -o-border-radius: 50%;
            position: absolute;
        }
        .illustration {
            position: absolute;
            top: -7%;
            right: -2px;
            width: 28%;
            opacity: 70%;
            transform: rotate(10deg);
        }
        .circle-one {
            top: 0;
            left: 0;
            z-index: -1;
            transform: translate(-45%, -45%);
            -webkit-transform: translate(-45%, -45%);
            -moz-transform: translate(-45%, -45%);
            -ms-transform: translate(-45%, -45%);
            -o-transform: translate(-45%, -45%);
        }
        .circle-two {
            bottom: 0;
            right: 0;
            z-index: -1;
            transform: translate(45%, 45%);
            -webkit-transform: translate(45%, 45%);
            -moz-transform: translate(45%, 45%);
            -ms-transform: translate(45%, 45%);
            -o-transform: translate(45%, 45%);
        }
        .register-forget {
            margin: 1rem 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .opacity { opacity: 0.6; }
        .theme-btn-container {
            position: absolute;
            left: 0;
            bottom: 2rem;
        }
        .theme-btn {
            cursor: pointer;
            transition: all 0.3s ease-in;
        }
        .theme-btn:hover { width: 40px !important; }
        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
        }
        @keyframes wobble {
            0% { transform: scale(1.025); -webkit-transform: scale(1.025); -moz-transform: scale(1.025); -ms-transform: scale(1.025); -o-transform: scale(1.025);}
            25% { transform: scale(1); -webkit-transform: scale(1); -moz-transform: scale(1); -ms-transform: scale(1); -o-transform: scale(1);}
            75% { transform: scale(1.025); -webkit-transform: scale(1.025); -moz-transform: scale(1.025); -ms-transform: scale(1.025); -o-transform: scale(1.025);}
            100% { transform: scale(1); -webkit-transform: scale(1); -moz-transform: scale(1); -ms-transform: scale(1); -o-transform: scale(1);}
        }
    </style>
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
</body>
</html>
