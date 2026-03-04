<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

// If already logged in, redirect to index
if (isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (login($username, $password)) {
        add_log($_SESSION['id_user'], "User {$_SESSION['username']} logged in");
        redirect('../index.php');
    }
    else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Parkir UKK</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-header">
            <div style="width: 64px; height: 64px; background: var(--primary); border-radius: 1.25rem; display: flex; align-items: center; justify-content: center; color: white; margin: 0 auto 1.5rem; font-size: 2rem; box-shadow: 0 10px 20px rgba(67, 56, 202, 0.2);">
                <i class='bx bxs-parking'></i>
            </div>
            <h1>E-Parking</h1>
            <p>Silakan masuk ke akun Anda</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger" style="display: flex; align-items: center; gap: 0.5rem;">
                <i class='bx bx-error-circle'></i> <?= $error; ?>
            </div>
        <?php
endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <div style="position: relative;">
                    <i class='bx bx-user' style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;"></i>
                    <input type="text" name="username" id="username" class="input-field" placeholder="Username" required autocomplete="off" style="padding-left: 3rem;">
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div style="position: relative;">
                    <i class='bx bx-lock-alt' style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.25rem;"></i>
                    <input type="password" name="password" id="password" class="input-field" placeholder="Password" required style="padding-left: 3rem;">
                </div>
            </div>
            <button type="submit" name="login" class="btn btn-primary" style="width: 100%; height: 3.5rem; font-size: 1.1rem; border-radius: 1rem; margin-top: 1rem;">
                Masuk ke Sistem <i class='bx bx-right-arrow-alt'></i>
            </button>
        </form>
        
        <div style="margin-top: 2.5rem; text-align: center; color: var(--text-muted); font-size: 0.8rem; font-weight: 500;">
            &copy; <?= date('Y'); ?> UKK RPL Paket 2 • SMK Unggulan
        </div>
    </div>
</body>
</html>
