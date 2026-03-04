<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

check_login();
check_role('petugas');

$title = 'Transaksi Parkir';
$action = $_GET['action'] ?? 'list';
$msg = $_SESSION['msg'] ?? '';
$msg_type = $_SESSION['msg_type'] ?? '';
unset($_SESSION['msg'], $_SESSION['msg_type']);

// Handle Entry (IN)
if (isset($_POST['proses_masuk'])) {
    $plat_nomor = strtoupper(escape($_POST['plat_nomor']));
    $id_kendaraan = (int)$_POST['id_kendaraan'];
    $id_area = (int)$_POST['id_area'];
    $kode_transaksi = "TRX-" . date('YmdHis');
    $waktu_masuk = date('Y-m-d H:i:s');
    $id_petugas = $_SESSION['id_user'];

    // Check if area is full
    $area = query("SELECT * FROM area_parkir WHERE id_area = $id_area")[0];
    if ($area['terisi'] < $area['kapasitas']) {
        $query = "INSERT INTO transaksi (kode_transaksi, plat_nomor, id_kendaraan, id_area, id_user, waktu_masuk, status, id_petugas) 
                  VALUES ('$kode_transaksi', '$plat_nomor', $id_kendaraan, $id_area, $id_petugas, '$waktu_masuk', 'parkir', $id_petugas)";
        if (mysqli_query($conn, $query)) {
            // Update area occupancy
            mysqli_query($conn, "UPDATE area_parkir SET terisi = terisi + 1 WHERE id_area = $id_area");
            add_log($_SESSION['id_user'], "Registered vehicle entry: $plat_nomor");
            redirect('transaksi.php', 'Kendaraan berhasil masuk. Kode: ' . $kode_transaksi);
        }
    }
    else {
        redirect('transaksi.php?action=masuk', 'Area Parkir Penuh!', 'danger');
    }
}

// Handle Exit (OUT)
if (isset($_GET['proses_keluar_id'])) {
    $id_transaksi = (int)$_GET['proses_keluar_id'];
    $waktu_keluar = date('Y-m-d H:i:s');

    // Get transaction and rate info
    $trx = query("SELECT t.*, tp.tarif_per_jam 
                 FROM transaksi t 
                 JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                 JOIN tarif_parkir tp ON k.nama_kendaraan = tp.tipe_kendaraan 
                 WHERE t.id_transaksi = $id_transaksi")[0];

    // Calculate duration in hours (ceil)
    $ts_masuk = strtotime($trx['waktu_masuk']);
    $ts_keluar = strtotime($waktu_keluar);
    $diff_seconds = $ts_keluar - $ts_masuk;
    $duration_hours = ceil($diff_seconds / 3600);
    if ($duration_hours < 1)
        $duration_hours = 1;

    $total_biaya = $duration_hours * $trx['tarif_per_jam'];

    $query = "UPDATE transaksi SET waktu_keluar = '$waktu_keluar', biaya = $total_biaya, status = 'selesai' WHERE id_transaksi = $id_transaksi";
    if (mysqli_query($conn, $query)) {
        // Update area occupancy
        mysqli_query($conn, "UPDATE area_parkir SET terisi = terisi - 1 WHERE id_area = {$trx['id_area']}");
        add_log($_SESSION['id_user'], "Registered vehicle exit: {$trx['plat_nomor']}, Fee: $total_biaya");
        redirect('print.php?id=' . $id_transaksi); // Redirect to print receipt
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<?php if ($msg): ?>
    <div class="alert alert-<?= $msg_type; ?>"><?= $msg; ?></div>
<?php
endif; ?>

<?php if ($action == 'masuk'): ?>
    <!-- Form Entry -->
    <div class="table-card" style="max-width: 600px; padding: 2rem; margin: 0 auto;">
        <div class="table-header" style="border:none; padding: 0; margin-bottom: 2rem;">
            <h2>Input Kendaraan Masuk</h2>
        </div>
        <form action="" method="POST">
            <div class="form-group">
                <label>Plat Nomor</label>
                <input type="text" name="plat_nomor" class="input-field" required placeholder="Contoh: B 1234 ABC" style="text-transform: uppercase;">
            </div>
            <div class="form-group">
                <label>Tipe Kendaraan</label>
                <select name="id_kendaraan" class="input-field" required>
                    <?php
    $kendaraans = query("SELECT * FROM kendaraan");
    foreach ($kendaraans as $k): ?>
                        <option value="<?= $k['id_kendaraan']; ?>"><?= $k['nama_kendaraan']; ?></option>
                    <?php
    endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Pilih Area Parkir</label>
                <select name="id_area" class="input-field" required>
                    <?php
    $areas = query("SELECT * FROM area_parkir");
    foreach ($areas as $a):
        $available = $a['kapasitas'] - $a['terisi'];
?>
                        <option value="<?= $a['id_area']; ?>" <?= $available <= 0 ? 'disabled' : ''; ?>>
                            <?= $a['nama_area']; ?> (Tersedia: <?= $available; ?>)
                        </option>
                    <?php
    endforeach; ?>
                </select>
            </div>
            <button type="submit" name="proses_masuk" class="btn btn-primary">Simpan & Masuk</button>
            <a href="transaksi.php" class="btn" style="display: block; text-align: center; margin-top: 10px; background: #eee;">Batal</a>
        </form>
    </div>

<?php
elseif ($action == 'keluar' && isset($_GET['id'])): ?>
    <!-- Modal/Confirm Exit -->
    <?php
    $id = (int)$_GET['id'];
    $trx = query("SELECT t.*, k.nama_kendaraan FROM transaksi t JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan WHERE id_transaksi = $id")[0];
?>
    <div class="table-card" style="max-width: 500px; padding: 2rem; margin: 0 auto; text-align: center;">
        <h2 style="margin-bottom: 1rem;">Proses Keluar</h2>
        <p style="margin-bottom: 2rem;">Apakah kendaraan dengan plat <strong><?= $trx['plat_nomor']; ?></strong> (<?= $trx['nama_kendaraan']; ?>) akan keluar?</p>
        <a href="?proses_keluar_id=<?= $id; ?>" class="btn btn-primary" style="display: block; margin-bottom: 10px;">Ya, Selesaikan Transaksi</a>
        <a href="transaksi.php" class="btn" style="display: block; background: #eee;">Batal</a>
    </div>

<?php
else: ?>
    <!-- List Transactions -->
    <div class="table-card">
        <div class="table-header">
            <h2>Riwayat Pembayaran Parkir</h2>
            <a href="?action=masuk" class="btn btn-primary" style="width: auto; padding: 0.5rem 1.5rem;">+ Masuk Baru</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Plat Nomor</th>
                    <th>Kendaraan</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Biaya</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
    $transactions = query("SELECT t.*, k.nama_kendaraan FROM transaksi t JOIN kendaraan k ON t.id_kendaraan = k.id_kendaraan ORDER BY id_transaksi DESC LIMIT 50");
    foreach ($transactions as $t):
?>
                <tr>
                    <td><code><?= $t['kode_transaksi']; ?></code></td>
                    <td><strong><?= $t['plat_nomor']; ?></strong></td>
                    <td><?= $t['nama_kendaraan']; ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($t['waktu_masuk'])); ?></td>
                    <td><?= $t['waktu_keluar'] ? date('d/m/Y H:i', strtotime($t['waktu_keluar'])) : '-'; ?></td>
                    <td><?= $t['biaya'] > 0 ? 'Rp ' . number_format($t['biaya'], 0, ',', '.') : '-'; ?></td>
                    <td>
                        <span class="badge badge-<?= $t['status'] == 'parkir' ? 'primary' : 'success'; ?>">
                            <?= ucfirst($t['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($t['status'] == 'parkir'): ?>
                            <a href="?action=keluar&id=<?= $t['id_transaksi']; ?>" style="color: var(--danger);">Keluar</a>
                        <?php
        else: ?>
                            <a href="print.php?id=<?= $t['id_transaksi']; ?>" style="color: var(--primary);">Struk</a>
                        <?php
        endif; ?>
                    </td>
                </tr>
                <?php
    endforeach; ?>
            </tbody>
        </table>
    </div>
<?php
endif; ?>

<?php include '../includes/footer.php'; ?>
