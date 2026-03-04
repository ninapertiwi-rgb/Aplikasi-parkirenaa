<?php
require_once 'C:/xampp/htdocs/APP-ena/config/database.php';
echo "KENDARAAN TABLE:\n";
$res = mysqli_query($conn, "DESCRIBE kendaraan");
while ($row = mysqli_fetch_assoc($res))
    print_r($row);

echo "\nTARIF_PARKIR TABLE:\n";
$res = mysqli_query($conn, "DESCRIBE tarif_parkir");
while ($row = mysqli_fetch_assoc($res))
    print_r($row);

echo "\nTRANSAKSI TABLE:\n";
$res = mysqli_query($conn, "DESCRIBE transaksi");
while ($row = mysqli_fetch_assoc($res))
    print_r($row);
?>
