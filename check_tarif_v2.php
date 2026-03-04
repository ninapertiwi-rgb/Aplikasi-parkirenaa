<?php
require_once 'C:/xampp/htdocs/APP-ena/config/database.php';
$res = mysqli_query($conn, "DESCRIBE tarif_parkir");
while ($row = mysqli_fetch_assoc($res))
    print_r($row);
$res = mysqli_query($conn, "SELECT * FROM tarif_parkir");
while ($row = mysqli_fetch_assoc($res))
    print_r($row);
?>
