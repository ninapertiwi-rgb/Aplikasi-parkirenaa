<?php
$conn = mysqli_connect('localhost', 'root', '', 'aplikasi_parkir');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "--- CONSTRAINTS FOR transaksi ---\n";
$res = mysqli_query($conn, "SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
                            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                            WHERE TABLE_NAME = 'transaksi' 
                            AND TABLE_SCHEMA = 'aplikasi_parkir' 
                            AND REFERENCED_TABLE_NAME IS NOT NULL");
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

echo "--- COLUMNS FOR transaksi ---\n";
$res2 = mysqli_query($conn, "DESCRIBE transaksi");
while ($row = mysqli_fetch_assoc($res2)) {
    print_r($row);
}
?>
