<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

check_login();
check_role('petugas');

$title = 'Dashboard Petugas';

// Stats for Petugas
$current_parking = query("SELECT COUNT(*) as count FROM transaksi WHERE status = 'parkir'")[0]['count'];
$entry_today = query("SELECT COUNT(*) as count FROM transaksi WHERE DATE(waktu_masuk) = CURDATE()")[0]['count'];
$exit_today = query("SELECT COUNT(*) as count FROM transaksi WHERE DATE(waktu_keluar) = CURDATE()")[0]['count'];

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <i class='bx bxs-car-garage'></i>
        <h3>Kendaraan Parkir Saat Ini</h3>
        <div class="value"><?= $current_parking; ?></div>
    </div>
    <div class="stat-card">
        <i class='bx bxs-log-in-circle'></i>
        <h3>Total Masuk Hari Ini</h3>
        <div class="value"><?= $entry_today; ?></div>
    </div>
    <div class="stat-card">
        <i class='bx bxs-log-out-circle' style="color: var(--danger);"></i>
        <h3>Total Keluar Hari Ini</h3>
        <div class="value"><?= $exit_today; ?></div>
    </div>
</div>

<div style="display: flex; gap: 1.5rem; margin-bottom: 2.5rem;">
    <a href="transaksi.php?action=masuk" class="btn btn-primary" style="flex: 1; text-align: center; text-decoration: none; padding: 2.5rem 1.5rem; font-size: 1.25rem; border-radius: 1.5rem; flex-direction: column;">
        <i class='bx bxs-door-open' style="font-size: 3rem; margin-bottom: 1rem;"></i>
        <span>Kendaraan Masuk (IN)</span>
    </a>
    <a href="transaksi.php" class="btn btn-secondary" style="flex: 1; text-align: center; text-decoration: none; padding: 2.5rem 1.5rem; font-size: 1.25rem; background: #64748b; color: white; border-radius: 1.5rem; flex-direction: column;">
        <i class='bx bxs-exit' style="font-size: 3rem; margin-bottom: 1rem;"></i>
        <span>Kendaraan Keluar (OUT)</span>
    </a>
</div>

<div class="table-card">
    <div class="table-header">
        <h2>Kendaraan Parkir Terkini</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Plat Nomor</th>
                <th>Tipe</th>
                <th>Area</th>
                <th>Waktu Masuk</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
$parking = query("SELECT t.*, k.nama_kendaraan, a.nama_area 
                             FROM transaksi t 
                             JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                             JOIN area_parkir a ON t.id_area = a.id_area 
                             WHERE t.status = 'parkir' 
                             ORDER BY t.waktu_masuk DESC LIMIT 10");
foreach ($parking as $p):
?>
            <tr>
                <td><strong><?= $p['plat_nomor']; ?></strong></td>
                <td><?= $p['nama_kendaraan']; ?></td>
                <td><?= $p['nama_area']; ?></td>
                <td><?= date('d/m/Y H:i', strtotime($p['waktu_masuk'])); ?></td>
                <td>
                    <a href="transaksi.php?action=keluar&id=<?= $p['id_transaksi']; ?>" style="color: var(--danger);">Proses Keluar</a>
                </td>
            </tr>
            <?php
endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
