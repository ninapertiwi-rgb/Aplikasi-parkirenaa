<?php
require_once 'C:/xampp/htdocs/APP-ena/config/database.php';
echo "TARIF_PARKIR TABLE:\n";
$res = mysqli_query($conn, "DESCRIBE tarif_parkir");
while ($row = mysqli_fetch_assoc($res))
    print_r($row);
?>
