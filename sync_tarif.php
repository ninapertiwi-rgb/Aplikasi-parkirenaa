<?php
require_once 'C:/xampp/htdocs/APP-ena/config/database.php';
echo "Syncing tarif_parkir values...\n";
mysqli_query($conn, "UPDATE tarif_parkir SET tarif_per_jam = harga_per_jam");
echo "Done.\n";
?>
