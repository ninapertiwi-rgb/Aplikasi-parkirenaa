<?php
require_once 'C:/xampp/htdocs/APP-ena/config/database.php';
echo "TARIF_PARKIR DATA:\n";
$res = mysqli_query($conn, "SELECT * FROM tarif_parkir");
while ($row = mysqli_fetch_assoc($res))
    print_r($row);

echo "\nKENDARAAN DATA:\n";
$res = mysqli_query($conn, "SELECT * FROM kendaraan LIMIT 5");
while ($row = mysqli_fetch_assoc($res))
    print_r($row);

echo "\nTRANSAKSI DATA:\n";
$res = mysqli_query($conn, "SELECT id_transaksi, id_kendaraan FROM transaksi LIMIT 5");
while ($row = mysqli_fetch_assoc($res))
    print_r($row);
?>
