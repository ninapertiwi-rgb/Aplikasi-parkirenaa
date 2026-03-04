<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

check_login();

if (!isset($_GET['id'])) {
    header("Location: transaksi.php");
    exit;
}

$id = (int)$_GET['id'];
$data = query("SELECT t.*, k.nama_kendaraan, a.nama_area, u.nama_lengkap as petugas 
               FROM transaksi t 
               JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan 
               JOIN area_parkir a ON t.id_area = a.id_area 
               JOIN user u ON t.id_petugas = u.id_user 
               WHERE t.id_transaksi = $id")[0];

if (!$data) {
    die("Transaksi tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Parkir - <?= $data['kode_transaksi']; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 0 auto; color: #000; padding: 20px; }
        .text-center { text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .flex { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .header h2 { margin-bottom: 5px; }
        .footer { margin-top: 20px; font-size: 0.8rem; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header text-center">
        <h2>E-PARKING</h2>
        <p>SMK RPL PARKING SYSTEM</p>
    </div>
    
    <div class="divider"></div>
    
    <div class="flex"><span>Nota:</span> <span><?= $data['kode_transaksi']; ?></span></div>
    <div class="flex"><span>Plat:</span> <span><?= $data['plat_nomor']; ?></span></div>
    <div class="flex"><span>Kendaraan:</span> <span><?= $data['nama_kendaraan']; ?></span></div>
    <div class="flex"><span>Area:</span> <span><?= $data['nama_area']; ?></span></div>
    
    <div class="divider"></div>
    
    <div class="flex"><span>Masuk:</span> <span><?= date('d/m/y H:i', strtotime($data['waktu_masuk'])); ?></span></div>
    <?php if ($data['waktu_keluar']): ?>
        <div class="flex"><span>Keluar:</span> <span><?= date('d/m/y H:i', strtotime($data['waktu_keluar'])); ?></span></div>
        <div class="divider"></div>
        <div class="flex" style="font-weight: bold; font-size: 1.2rem;">
            <span>TOTAL:</span> 
            <span>Rp <?= number_format($data['biaya'], 0, ',', '.'); ?></span>
        </div>
    <?php
else: ?>
        <div class="text-center" style="margin: 10px 0;">-- SIMPAN STRUK INI --</div>
    <?php
endif; ?>
    
    <div class="divider"></div>
    
    <div class="footer text-center">
        <p>Petugas: <?= $data['petugas']; ?></p>
        <p>Terima kasih atas kunjungan Anda</p>
        <div class="no-print" style="margin-top: 20px;">
            <button onclick="window.history.back()">Kembali</button>
        </div>
    </div>
</body>
</html>
