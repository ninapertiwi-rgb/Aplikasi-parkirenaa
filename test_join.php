<?php
require_once 'C:/xampp/htdocs/APP-ena/config/database.php';

$id_transaksi = 7; // Use an existing ID from check_data.php

$query = "SELECT t.*, tp.tarif_per_jam 
          FROM transaksi t 
          JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan 
          JOIN tarif_parkir tp ON k.nama_kendaraan = tp.tipe_kendaraan 
          WHERE t.id_transaksi = $id_transaksi";

echo "Running query: $query\n";
$res = mysqli_query($conn, $query);
if ($res) {
    $row = mysqli_fetch_assoc($res);
    print_r($row);
}
else {
    echo "QUERY FAILED: " . mysqli_error($conn) . "\n";
}
?>
