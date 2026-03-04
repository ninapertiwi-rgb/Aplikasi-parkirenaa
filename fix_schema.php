<?php
require_once 'config/database.php';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>
<title>Fix DB Schema</title>
<style>
body{font-family:monospace;padding:30px;background:#1a1a2e;color:#eee;}
h2{color:#00d4ff;} h3{color:#ffd700;}
.ok{color:#00ff88;font-weight:bold;}
.err{color:#ff4466;font-weight:bold;}
.warn{color:#ffa500;font-weight:bold;}
.box{background:#16213e;padding:15px;border-radius:8px;margin:10px 0;border:1px solid #333;}
code{background:#0f3460;padding:3px 8px;border-radius:4px;color:#00d4ff;}
table{border-collapse:collapse;width:100%;margin-bottom:10px;}
th,td{border:1px solid #444;padding:8px 12px;text-align:left;}
th{background:#0f3460;}
a.btn{display:inline-block;background:#00d4ff;color:#000;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:bold;margin-top:15px;margin-right:10px;}
a.btn2{display:inline-block;background:#00ff88;color:#000;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:bold;margin-top:15px;}
hr{border-color:#333;margin:20px 0;}
</style></head><body>
<h2>🔧 FULL DATABASE SCHEMA FIX</h2>";

$errors = 0;
$fixes = 0;

// Helper: get existing columns for a table
function get_columns($conn, $table)
{
    $cols = [];
    $r = mysqli_query($conn, "DESCRIBE `$table`");
    if ($r)
        while ($row = mysqli_fetch_assoc($r))
            $cols[] = $row['Field'];
    return $cols;
}

// Helper: run ALTER and report
function alter($conn, $label, $sql, &$fixes, &$errors)
{
    $r = mysqli_query($conn, $sql);
    if ($r) {
        echo "<span class='ok'>✅ $label</span><br>";
        $fixes++;
    }
    else {
        echo "<span class='err'>❌ Gagal $label : " . mysqli_error($conn) . "</span><br>";
        $errors++;
    }
}

// =============================================
// TABLE: kendaraan
// =============================================
echo "<h3>Tabel: <code>kendaraan</code></h3><div class='box'>";
$r = mysqli_query($conn, "CREATE TABLE IF NOT EXISTS kendaraan (
    id_kendaraan INT AUTO_INCREMENT PRIMARY KEY,
    nama_kendaraan VARCHAR(50) NOT NULL,
    keterangan TEXT
) ENGINE=InnoDB CHARACTER SET utf8mb4");
echo $r ? "<span class='ok'>✅ Tabel ada / dibuat</span><br>" : "<span class='err'>❌ Gagal buat tabel: " . mysqli_error($conn) . "</span><br>";

$cols = get_columns($conn, 'kendaraan');
if (!in_array('nama_kendaraan', $cols))
    alter($conn, "Tambah kolom 'nama_kendaraan'", "ALTER TABLE kendaraan ADD COLUMN nama_kendaraan VARCHAR(50) NOT NULL AFTER id_kendaraan", $fixes, $errors);
else
    echo "<span class='ok'>✅ kolom 'nama_kendaraan' sudah ada</span><br>";

if (!in_array('keterangan', $cols))
    alter($conn, "Tambah kolom 'keterangan'", "ALTER TABLE kendaraan ADD COLUMN keterangan TEXT", $fixes, $errors);
else
    echo "<span class='ok'>✅ kolom 'keterangan' sudah ada</span><br>";

// Seed jika kosong
$cnt = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM kendaraan"))['c'];
if ($cnt == 0) {
    mysqli_query($conn, "INSERT INTO kendaraan (nama_kendaraan, keterangan) VALUES ('Motor','Roda dua'),('Mobil','Roda empat')");
    echo "<span class='ok'>✅ Data awal kendaraan ditambahkan (Motor, Mobil)</span><br>";
}
echo "</div>";

// =============================================
// TABLE: area_parkir
// =============================================
echo "<h3>Tabel: <code>area_parkir</code></h3><div class='box'>";
$r = mysqli_query($conn, "CREATE TABLE IF NOT EXISTS area_parkir (
    id_area INT AUTO_INCREMENT PRIMARY KEY,
    nama_area VARCHAR(50) NOT NULL,
    kapasitas INT NOT NULL DEFAULT 50,
    terisi INT DEFAULT 0
) ENGINE=InnoDB CHARACTER SET utf8mb4");
echo $r ? "<span class='ok'>✅ Tabel ada / dibuat</span><br>" : "<span class='err'>❌ " . mysqli_error($conn) . "</span><br>";

$cols = get_columns($conn, 'area_parkir');
if (!in_array('nama_area', $cols))
    alter($conn, "Tambah kolom 'nama_area'", "ALTER TABLE area_parkir ADD COLUMN nama_area VARCHAR(50) NOT NULL AFTER id_area", $fixes, $errors);
else
    echo "<span class='ok'>✅ kolom 'nama_area' sudah ada</span><br>";
if (!in_array('kapasitas', $cols))
    alter($conn, "Tambah kolom 'kapasitas'", "ALTER TABLE area_parkir ADD COLUMN kapasitas INT NOT NULL DEFAULT 50", $fixes, $errors);
else
    echo "<span class='ok'>✅ kolom 'kapasitas' sudah ada</span><br>";
if (!in_array('terisi', $cols))
    alter($conn, "Tambah kolom 'terisi'", "ALTER TABLE area_parkir ADD COLUMN terisi INT DEFAULT 0", $fixes, $errors);
else
    echo "<span class='ok'>✅ kolom 'terisi' sudah ada</span><br>";

$cnt2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM area_parkir"))['c'];
if ($cnt2 == 0) {
    mysqli_query($conn, "INSERT INTO area_parkir (nama_area, kapasitas) VALUES ('Blok A',50),('Blok B',30)");
    echo "<span class='ok'>✅ Data awal area parkir ditambahkan (Blok A, Blok B)</span><br>";
}
echo "</div>";

// =============================================
// TABLE: tarif_parkir
// =============================================
echo "<h3>Tabel: <code>tarif_parkir</code></h3><div class='box'>";
$r = mysqli_query($conn, "CREATE TABLE IF NOT EXISTS tarif_parkir (
    id_tarif INT AUTO_INCREMENT PRIMARY KEY,
    id_kendaraan INT NOT NULL,
    tarif_per_jam DECIMAL(10,2) NOT NULL DEFAULT 2000
) ENGINE=InnoDB CHARACTER SET utf8mb4");
echo $r ? "<span class='ok'>✅ Tabel ada / dibuat</span><br>" : "<span class='err'>❌ " . mysqli_error($conn) . "</span><br>";

$cols = get_columns($conn, 'tarif_parkir');
if (!in_array('tarif_per_jam', $cols))
    alter($conn, "Tambah kolom 'tarif_per_jam'", "ALTER TABLE tarif_parkir ADD COLUMN tarif_per_jam DECIMAL(10,2) NOT NULL DEFAULT 2000", $fixes, $errors);
else
    echo "<span class='ok'>✅ kolom 'tarif_per_jam' sudah ada</span><br>";

$cnt3 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM tarif_parkir"))['c'];
if ($cnt3 == 0) {
    mysqli_query($conn, "INSERT INTO tarif_parkir (id_kendaraan, tarif_per_jam) VALUES (1,2000),(2,5000)");
    echo "<span class='ok'>✅ Data awal tarif ditambahkan</span><br>";
}
echo "</div>";

// =============================================
// TABLE: transaksi
// =============================================
echo "<h3>Tabel: <code>transaksi</code></h3><div class='box'>";
$r = mysqli_query($conn, "CREATE TABLE IF NOT EXISTS transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(20) NOT NULL UNIQUE,
    plat_nomor VARCHAR(15) NOT NULL,
    id_kendaraan INT NOT NULL,
    id_area INT NOT NULL,
    waktu_masuk DATETIME NOT NULL,
    waktu_keluar DATETIME DEFAULT NULL,
    biaya DECIMAL(10,2) DEFAULT 0,
    status ENUM('parkir','selesai') DEFAULT 'parkir',
    id_petugas INT NOT NULL
) ENGINE=InnoDB CHARACTER SET utf8mb4");
echo $r ? "<span class='ok'>✅ Tabel ada / dibuat</span><br>" : "<span class='err'>❌ " . mysqli_error($conn) . "</span><br>";

$cols = get_columns($conn, 'transaksi');
$trx_needed = [
    'kode_transaksi' => "ALTER TABLE transaksi ADD COLUMN kode_transaksi VARCHAR(20) NOT NULL DEFAULT '' AFTER id_transaksi",
    'plat_nomor' => "ALTER TABLE transaksi ADD COLUMN plat_nomor VARCHAR(15) NOT NULL DEFAULT '' AFTER kode_transaksi",
    'id_kendaraan' => "ALTER TABLE transaksi ADD COLUMN id_kendaraan INT NOT NULL DEFAULT 1 AFTER plat_nomor",
    'id_area' => "ALTER TABLE transaksi ADD COLUMN id_area INT NOT NULL DEFAULT 1 AFTER id_kendaraan",
    'waktu_masuk' => "ALTER TABLE transaksi ADD COLUMN waktu_masuk DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER id_area",
    'waktu_keluar' => "ALTER TABLE transaksi ADD COLUMN waktu_keluar DATETIME DEFAULT NULL AFTER waktu_masuk",
    'biaya' => "ALTER TABLE transaksi ADD COLUMN biaya DECIMAL(10,2) DEFAULT 0 AFTER waktu_keluar",
    'status' => "ALTER TABLE transaksi ADD COLUMN status ENUM('parkir','selesai') DEFAULT 'parkir' AFTER biaya",
    'id_petugas' => "ALTER TABLE transaksi ADD COLUMN id_petugas INT NOT NULL DEFAULT 1 AFTER status",
];
foreach ($trx_needed as $col => $sql) {
    if (!in_array($col, $cols))
        alter($conn, "Tambah kolom '$col' di transaksi", $sql, $fixes, $errors);
    else {
        // Special check for 'status' enum values
        if ($col == 'status') {
            $check_status = mysqli_fetch_assoc(mysqli_query($conn, "SHOW COLUMNS FROM transaksi LIKE 'status'"));
            if (strpos($check_status['Type'], "'parkir'") === false) {
                alter($conn, "Update ENUM status di transaksi", "ALTER TABLE transaksi MODIFY COLUMN status ENUM('parkir','selesai') DEFAULT 'parkir'", $fixes, $errors);
            }
            else {
                echo "<span class='ok'>✅ kolom '$col' sudah sesuai</span><br>";
            }
        }
        else {
            echo "<span class='ok'>✅ kolom '$col' sudah ada</span><br>";
        }
    }
}
echo "</div>";

// =============================================
// TABLE: log_aktivitas
// =============================================
echo "<h3>Tabel: <code>log_aktivitas</code></h3><div class='box'>";
$r = mysqli_query($conn, "CREATE TABLE IF NOT EXISTS log_aktivitas (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    aktivitas TEXT NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB CHARACTER SET utf8mb4");
echo $r ? "<span class='ok'>✅ Tabel ada / dibuat</span><br>" : "<span class='err'>❌ " . mysqli_error($conn) . "</span><br>";
echo "</div>";

// =============================================
// SUMMARY
// =============================================
echo "<hr><h3>📊 Ringkasan</h3><div class='box'>";
echo "<span class='ok'>✅ $fixes perbaikan diterapkan</span><br>";
if ($errors > 0)
    echo "<span class='err'>❌ $errors error (lihat detail di atas)</span><br>";
else
    echo "<span class='ok'>✅ Tidak ada error!</span><br>";
echo "<br>";
echo "<a class='btn' href='fix_password.php'>➜ Reset Password</a>";
echo "<a class='btn' href='auth/login.php'>➜ Halaman Login</a>";
echo "<a class='btn2' href='admin/index.php'>➜ Dashboard Admin</a>";
echo "</div></body></html>";
?>
