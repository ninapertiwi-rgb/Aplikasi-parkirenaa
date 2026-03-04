<?php
// Core logic functions

function login($username, $password)
{
    global $conn;
    $username = escape($username);
    $query = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
    }
    return false;
}

function check_login()
{
    if (!isset($_SESSION['login'])) {
        header("Location: ../auth/login.php");
        exit;
    }
}

function check_role($role)
{
    if ($_SESSION['role'] !== $role) {
        header("Location: ../index.php");
        exit;
    }
}

function add_log($id_user, $aktivitas)
{
    global $conn;
    $aktivitas = escape($aktivitas);
    mysqli_query($conn, "INSERT INTO log_aktivitas (id_user, aktivitas) VALUES ($id_user, '$aktivitas')");
}
?>
