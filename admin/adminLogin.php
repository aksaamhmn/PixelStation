<?php
session_start();
include('../server/connection.php');

if (isset($_SESSION['logged_in'])) {
    header('Location: adminDashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $query = "SELECT id_admin, username FROM admin WHERE username = ? AND password = ? LIMIT 1";

        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('ss', $username, $password);

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $username);
                    $stmt->fetch();

                    $_SESSION['id_admin'] = $id;
                    $_SESSION['username'] = $username;
                    $_SESSION['logged_in'] = true;
                    $_SESSION['swal_success'] = true;
                    
                    
                } else {
                    $_SESSION['swal_error'] = true;
                    
                }
            } else {
                header('Location: adminLogin.php?error=Something went wrong!');
            }
            $stmt->close();
        } else {
            header('Location: adminLogin.php?error=Failed to prepare statement!');
        }
    } else {
        header('Location: adminLogin.php?error=Please fill both fields');
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
  <style>

* {
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

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

a {
    text-decoration: none;
    color: #FFFFFF;
}

h1 {
    font-size: 2.5rem;
}

.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.login-container {
    position: relative;
    width: 22.2rem;
}
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
    margin: 2rem 0;
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

.login-container form input:focus {
    box-shadow: 0 0 16px 1px rgba(0, 0, 0, 0.2);
    animation: wobble 0.3s ease-in;
    -webkit-animation: wobble 0.3s ease-in;
}

.login-container form button {
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
    top: -2%;
    right: -2px;
    width: 40%;
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

.opacity {
    opacity: 0.6;
}

.theme-btn-container {
    position: absolute;
    left: 0;
    bottom: 2rem;
}

.theme-btn {
    cursor: pointer;
    transition: all 0.3s ease-in;
}

.theme-btn:hover {
    width: 40px !important;
}

@keyframes wobble {
    0% {
        transform: scale(1.025);
        -webkit-transform: scale(1.025);
        -moz-transform: scale(1.025);
        -ms-transform: scale(1.025);
        -o-transform: scale(1.025);
    }
    25% {
        transform: scale(1);
        -webkit-transform: scale(1);
        -moz-transform: scale(1);
        -ms-transform: scale(1);
        -o-transform: scale(1);
    }
    75% {
        transform: scale(1.025);
        -webkit-transform: scale(1.025);
        -moz-transform: scale(1.025);
        -ms-transform: scale(1.025);
        -o-transform: scale(1.025);
    }
    100% {
        transform: scale(1);
        -webkit-transform: scale(1);
        -moz-transform: scale(1);
        -ms-transform: scale(1);
        -o-transform: scale(1);
    }
}

  </style>
</head>
<body>
    <section class="container">
        <div class="login-container">
            <div class="circle circle-one"></div>
            <div class="form-container">
                <h1 class="opacity">LOGIN ADMIN</h1>
                <form action="adminLogin.php" method="POST">
                    <input type="text" name="username" placeholder="USERNAME"  />
                    <input type="password" name="password" placeholder="PASSWORD"  />
                    <button type="submit" class="opacity">SUBMIT</button>
                </form>
            </div>
            <div class="circle circle-two"></div>
        </div>
        <div class="theme-btn-container"></div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="sweetalert2.min.js"></script>
    <script>
        import Swal from 'sweetalert2/dist/sweetalert2.js'
        import 'sweetalert2/src/sweetalert2.scss'
    </script>
</body>
</html>


<?php if (isset($_SESSION['swal_success'])): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Login Berhasil!',
        text: 'Selamat datang admin!',
        confirmButtonText: 'OK',
        confirmButtonColor: '#967AA1'
    }).then(function() {
        window.location.href = 'adminDashboard.php?message=Logged in successfully';
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
    }).then(function() {
        window.location.href = 'adminLogin.php?error=Something went wrong!';
    });
    <?php unset($_SESSION['swal_error']); ?>
</script>
<?php endif; ?>