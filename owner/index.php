<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

check_login();
check_role('owner');

$title = 'Dashboard Owner';

// Stats for Owner
$today_revenue = query("SELECT SUM(biaya) as total FROM transaksi WHERE DATE(waktu_keluar) = CURDATE()")[0]['total'] ?? 0;
$month_revenue = query("SELECT SUM(biaya) as total FROM transaksi WHERE MONTH(waktu_keluar) = MONTH(CURDATE()) AND YEAR(waktu_keluar) = YEAR(CURDATE())")[0]['total'] ?? 0;
$total_transactions = query("SELECT COUNT(*) as count FROM transaksi WHERE status = 'selesai'")[0]['count'];

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <i class='bx bxs-calendar-star'></i>
        <h3>Pendapatan Hari Ini</h3>
        <div class="value">Rp <?= number_format($today_revenue, 0, ',', '.'); ?></div>
    </div>
    <div class="stat-card">
        <i class='bx bxs-bar-chart-alt-2'></i>
        <h3>Pendapatan Bulan Ini</h3>
        <div class="value">Rp <?= number_format($month_revenue, 0, ',', '.'); ?></div>
    </div>
    <div class="stat-card">
        <i class='bx bxs-pie-chart-alt-2'></i>
        <h3>Total Transaksi Selesai</h3>
        <div class="value"><?= $total_transactions; ?></div>
    </div>
</div>

<div class="table-card">
    <div class="table-header">
        <h2>Transaksi Terbaru</h2>
        <a href="laporan.php" class="btn btn-primary" style="width: auto; padding: 0.5rem 1.5rem;">Lihat Laporan Lengkap</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Plat Nomor</th>
                <th>Kendaraan</th>
                <th>Durasi</th>
                <th>Biaya</th>
            </tr>
        </thead>
        <tbody>
            <?php
$latest = query("SELECT t.*, k.nama_kendaraan 
                            FROM transaksi t 
                            JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                            WHERE t.status = 'selesai' 
                            ORDER BY waktu_keluar DESC LIMIT 10");
foreach ($latest as $l):
    $durasi = ceil((strtotime($l['waktu_keluar']) - strtotime($l['waktu_masuk'])) / 3600);
?>
            <tr>
                <td><?= date('d/m/Y H:i', strtotime($l['waktu_keluar'])); ?></td>
                <td><strong><?= $l['plat_nomor']; ?></strong></td>
                <td><?= $l['nama_kendaraan']; ?></td>
                <td><?= $durasi; ?> Jam</td>
                <td>Rp <?= number_format($l['biaya'], 0, ',', '.'); ?></td>
            </tr>
            <?php
endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
