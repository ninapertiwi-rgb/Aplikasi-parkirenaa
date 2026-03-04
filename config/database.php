<?php
// Configuration for Database Connection

$host = "localhost";
$user = "root";
$pass = "";
$db = "aplikasi_parkir";

$conn = mysqli_connect($host, $user, $pass, $db);
mysqli_set_charset($conn, "utf8");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to safely execute queries
function query($query)
{
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Function to prevent SQL Injection
function escape($data)
{
    global $conn;
    return mysqli_real_escape_string($conn, $data);
}

// Redirect with message
function redirect($url, $message = "", $type = "success")
{
    if (!empty($message)) {
        $_SESSION['msg'] = $message;
        $_SESSION['msg_type'] = $type;
    }
    header("Location: $url");
    exit;
}
?>
