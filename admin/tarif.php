<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

check_login();
check_role('admin');

$title = 'Manajemen Tarif Parkir';
$msg = $_SESSION['msg'] ?? '';
$msg_type = $_SESSION['msg_type'] ?? '';
unset($_SESSION['msg'], $_SESSION['msg_type']);

// Handle Update
if (isset($_POST['save'])) {
    $id_tarif = (int)$_POST['id_tarif'];
    $tarif = (int)$_POST['tarif'];

    mysqli_query($conn, "UPDATE tarif_parkir SET harga_per_jam=$tarif, tarif_per_jam=$tarif WHERE id_tarif=$id_tarif");
    add_log($_SESSION['id_user'], "Updated parking rate for ID $id_tarif to $tarif");
    redirect('tarif.php', 'Tarif berhasil diupdate');
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_data = query("SELECT * FROM tarif_parkir WHERE id_tarif = $id")[0] ?? null;
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
    <div class="table-card">
        <div class="table-header">
            <h2>Daftar Tarif Parkir</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Tipe Kendaraan</th>
                    <th>Tarif / Jam</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
$data = query("SELECT * FROM tarif_parkir ORDER BY id_tarif ASC");
foreach ($data as $item):
?>
                <tr>
                    <td><strong><?= $item['tipe_kendaraan']; ?></strong></td>
                    <td>Rp <?= number_format($item['harga_per_jam'], 0, ',', '.'); ?></td>
                    <td>
                        <a href="?edit=<?= $item['id_tarif']; ?>" style="color: var(--primary); margin-right: 10px;">Edit</a>
                    </td>
                </tr>
                <?php
endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="table-card" style="padding: 1.5rem;">
        <div class="table-header" style="padding: 0 0 1rem 0; margin-bottom: 1.5rem;">
            <h2><?= $edit_data ? 'Edit Tarif' : 'Pilih Tipe'; ?></h2>
        </div>
        
        <?php if ($msg): ?>
            <div class="alert alert-<?= $msg_type; ?>"><?= $msg; ?></div>
        <?php
endif; ?>

        <?php if ($edit_data): ?>
        <form action="" method="POST">
            <input type="hidden" name="id_tarif" value="<?= $edit_data['id_tarif']; ?>">
            <div class="form-group">
                <label>Tipe Kendaraan</label>
                <input type="text" class="input-field" value="<?= $edit_data['tipe_kendaraan']; ?>" disabled>
            </div>
            <div class="form-group">
                <label>Tarif per Jam (Rp)</label>
                <input type="number" name="tarif" class="input-field" value="<?= $edit_data['harga_per_jam']; ?>" required>
            </div>
            <button type="submit" name="save" class="btn btn-primary">Update Tarif</button>
            <a href="tarif.php" class="btn" style="display: block; text-align: center; margin-top: 10px; background: #eee;">Batal</a>
        </form>
        <?php
else: ?>
            <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">Pilih tipe kendaraan di tabel untuk mengedit tarif.</p>
        <?php
endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
