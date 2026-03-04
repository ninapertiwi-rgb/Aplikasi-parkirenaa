<?php
require_once 'C:/xampp/htdocs/APP-ena/config/database.php';

// Test simulation of the entire exit calculation
$id_transaksi = 7;
$waktu_keluar = date('Y-m-d H:i:s', strtotime('+2 hours'));

echo "Simulating exit process for transaction ID: $id_transaksi\n";

$query_trx = "SELECT t.*, tp.tarif_per_jam 
             FROM transaksi t 
             JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan 
             JOIN tarif_parkir tp ON k.nama_kendaraan = tp.tipe_kendaraan 
             WHERE t.id_transaksi = $id_transaksi";

$res = mysqli_query($conn, $query_trx);
if ($res) {
    $trx = mysqli_fetch_assoc($res);
    $ts_masuk = strtotime($trx['waktu_masuk']);
    $ts_keluar = strtotime($waktu_keluar);
    $diff_seconds = $ts_keluar - $ts_masuk;
    $duration_hours = ceil($diff_seconds / 3600);
    if ($duration_hours < 1)
        $duration_hours = 1;

    $total_biaya = $duration_hours * $trx['tarif_per_jam'];
    echo "SUCCESS: Calculated fee: Rp $total_biaya (Duration: $duration_hours hours, Hourly Rate: {$trx['tarif_per_jam']})\n";
}
else {
    echo "FAILURE: " . mysqli_error($conn) . "\n";
}
?>
