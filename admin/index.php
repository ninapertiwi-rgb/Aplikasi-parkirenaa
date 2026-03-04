<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

check_login();
check_role('admin');

$title = 'Dashboard Admin';

// Get some stats
$total_users = query("SELECT COUNT(*) as count FROM user")[0]['count'];
$total_kendaraan = query("SELECT COUNT(*) as count FROM kendaraan")[0]['count'];
$total_parking = query("SELECT COUNT(*) as count FROM transaksi WHERE status = 'parkir'")[0]['count'];
$revenue_today = query("SELECT SUM(biaya) as total FROM transaksi WHERE DATE(waktu_keluar) = CURDATE()")[0]['total'] ?? 0;

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <i class='bx bxs-user-account'></i>
        <h3>Total Pengguna</h3>
        <div class="value"><?= $total_users; ?></div>
    </div>
    <div class="stat-card">
        <i class='bx bxs-car'></i>
        <h3>Tipe Kendaraan</h3>
        <div class="value"><?= $total_kendaraan; ?></div>
    </div>
    <div class="stat-card">
        <i class='bx bxs-car-garage'></i>
        <h3>Kendaraan Parkir</h3>
        <div class="value"><?= $total_parking; ?></div>
    </div>
    <div class="stat-card">
        <i class='bx bxs-wallet'></i>
        <h3>Pendapatan Hari Ini</h3>
        <div class="value">Rp <?= number_format($revenue_today, 0, ',', '.'); ?></div>
    </div>
</div>

<div class="table-card">
    <div class="table-header">
        <h2>Aktivitas Terbaru</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>User</th>
                <th>Aktivitas</th>
            </tr>
        </thead>
        <tbody>
            <?php
$logs = query("SELECT l.*, u.username FROM log_aktivitas l JOIN user u ON l.id_user = u.id_user ORDER BY waktu DESC LIMIT 5");
foreach ($logs as $log):
?>
            <tr>
                <td><?= date('d/m/Y H:i', strtotime($log['waktu'])); ?></td>
                <td><strong><?= $log['username']; ?></strong></td>
                <td><?= $log['aktivitas']; ?></td>
            </tr>
            <?php
endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
