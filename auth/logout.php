<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

if (isset($_SESSION['id_user'])) {
    add_log($_SESSION['id_user'], "User {$_SESSION['username']} logged out");
}

session_destroy();
header("Location: login.php");
exit;
?>
