<?php
// Configuration for Database Connection

// Configuration for Database Connection from Environment Variables
$host = getenv('DB_HOST') ?: "localhost";
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') ?: "";
$db = getenv('DB_NAME') ?: "aplikasi_parkir";
$port = getenv('DB_PORT') ?: "3306";

$conn = mysqli_connect($host, $user, $pass, $db, $port);
mysqli_set_charset($conn, "utf8");

if (!$conn) {
    error_log("Connection failed: " . mysqli_connect_error());
    die("Database connection error. Please check your configuration.");
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
