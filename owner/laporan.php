<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

check_login();
check_role('owner');

$title = 'Laporan Transaksi';

$tgl_mulai = $_GET['tgl_mulai'] ?? date('Y-m-01');
$tgl_selesai = $_GET['tgl_selesai'] ?? date('Y-m-d');

$query_str = "SELECT t.*, k.nama_kendaraan, a.nama_area, u.nama_lengkap as petugas 
              FROM transaksi t 
              JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan 
              JOIN area_parkir a ON t.id_area = a.id_area 
              JOIN user u ON t.id_petugas = u.id_user 
              WHERE t.status = 'selesai' 
              AND DATE(t.waktu_keluar) BETWEEN '$tgl_mulai' AND '$tgl_selesai' 
              ORDER BY t.waktu_keluar DESC";

$laporan = query($query_str);
$total_pendapatan = 0;
foreach ($laporan as $l)
    $total_pendapatan += $l['biaya'];

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="table-card" style="padding: 1.5rem; margin-bottom: 2rem;">
    <form action="" method="GET" style="display: flex; gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="margin:0; flex: 1;">
            <label>Tanggal Mulai</label>
            <input type="date" name="tgl_mulai" class="input-field" value="<?= $tgl_mulai; ?>">
        </div>
        <div class="form-group" style="margin:0; flex: 1;">
            <label>Tanggal Selesai</label>
            <input type="date" name="tgl_selesai" class="input-field" value="<?= $tgl_selesai; ?>">
        </div>
        <button type="submit" class="btn btn-primary" style="width: auto; padding: 0.75rem 2rem;">Filter</button>
        <button type="button" onclick="window.print()" class="btn" style="width: auto; padding: 0.75rem 2rem; background: #64748b; color: white;">Cetak Laporan</button>
    </form>
</div>

<div class="table-card">
    <div class="table-header">
        <h2>Data Laporan: <?= date('d/m/Y', strtotime($tgl_mulai)); ?> - <?= date('d/m/Y', strtotime($tgl_selesai)); ?></h2>
        <div style="font-size: 1.25rem; font-weight: 700; color: var(--success);">
            Total: Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu Keluar</th>
                <th>Plat Nomor</th>
                <th>Kendaraan</th>
                <th>Area</th>
                <th>Petugas</th>
                <th>Biaya</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($laporan)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">Tidak ada data transaksi pada rentang waktu ini.</td>
                </tr>
            <?php
endif; ?>
            <?php
$no = 1;
foreach ($laporan as $item): ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= date('d/m/Y H:i', strtotime($item['waktu_keluar'])); ?></td>
                <td><strong><?= $item['plat_nomor']; ?></strong></td>
                <td><?= $item['nama_kendaraan']; ?></td>
                <td><?= $item['nama_area']; ?></td>
                <td><?= $item['petugas']; ?></td>
                <td>Rp <?= number_format($item['biaya'], 0, ',', '.'); ?></td>
            </tr>
            <?php
endforeach; ?>
        </tbody>
    </table>
</div>

<style>
    @media print {
        .sidebar, .top-bar, .btn, form { display: none !important; }
        .main-content { padding: 0; }
        .table-card { border: none; box-shadow: none; }
        .table-header { padding: 1rem 0; }
    }
</style>

<?php include '../includes/footer.php'; ?>
