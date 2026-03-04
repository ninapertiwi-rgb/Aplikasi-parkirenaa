<?php
session_start();
require_once 'config/database.php';
require_once 'config/functions.php';

// Check if logged in. If not, redirect to auth/login.php
if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit;
}

// Redirect based on role if already logged in
switch ($_SESSION['role']) {
    case 'admin':
        header("Location: admin/index.php");
        break;
    case 'petugas':
        header("Location: petugas/index.php");
        break;
    case 'owner':
        header("Location: owner/index.php");
        break;
    default:
        header("Location: auth/login.php");
        break;
}
exit;
?>
