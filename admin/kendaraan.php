<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

check_login();
check_role('admin');

$title = 'Manajemen Kendaraan';
$msg = $_SESSION['msg'] ?? '';
$msg_type = $_SESSION['msg_type'] ?? '';
unset($_SESSION['msg'], $_SESSION['msg_type']);

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM kendaraan WHERE id_kendaraan = $id");
    add_log($_SESSION['id_user'], "Deleted vehicle type ID $id");
    redirect('kendaraan.php', 'Tipe kendaraan berhasil dihapus');
}

// Handle Insert/Update
if (isset($_POST['save'])) {
    $id_kendaraan = (int)$_POST['id_kendaraan'];
    $nama_kendaraan = escape($_POST['nama_kendaraan']);
    $keterangan = escape($_POST['keterangan']);

    if ($id_kendaraan > 0) {
        mysqli_query($conn, "UPDATE kendaraan SET nama_kendaraan='$nama_kendaraan', keterangan='$keterangan' WHERE id_kendaraan=$id_kendaraan");
        add_log($_SESSION['id_user'], "Updated vehicle type $nama_kendaraan");
        redirect('kendaraan.php', 'Tipe kendaraan berhasil diupdate');
    }
    else {
        mysqli_query($conn, "INSERT INTO kendaraan (nama_kendaraan, keterangan) VALUES ('$nama_kendaraan', '$keterangan')");
        add_log($_SESSION['id_user'], "Created vehicle type $nama_kendaraan");
        redirect('kendaraan.php', 'Tipe kendaraan berhasil ditambah');
    }
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_data = query("SELECT * FROM kendaraan WHERE id_kendaraan = $id")[0] ?? null;
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
    <div class="table-card">
        <div class="table-header">
            <h2>Daftar Tipe Kendaraan</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nama Kendaraan</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
$data = query("SELECT * FROM kendaraan ORDER BY id_kendaraan ASC");
foreach ($data as $item):
?>
                <tr>
                    <td><strong><?= $item['nama_kendaraan']; ?></strong></td>
                    <td><?= $item['keterangan']; ?></td>
                    <td>
                        <a href="?edit=<?= $item['id_kendaraan']; ?>" style="color: var(--primary); margin-right: 10px;">Edit</a>
                        <a href="?delete=<?= $item['id_kendaraan']; ?>" style="color: var(--danger);" onclick="return confirm('Hapus ini?')">Hapus</a>
                    </td>
                </tr>
                <?php
endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="table-card" style="padding: 1.5rem;">
        <div class="table-header" style="padding: 0 0 1rem 0; margin-bottom: 1.5rem;">
            <h2><?= $edit_data ? 'Edit Tipe' : 'Tambah Tipe'; ?></h2>
        </div>
        
        <?php if ($msg): ?>
            <div class="alert alert-<?= $msg_type; ?>"><?= $msg; ?></div>
        <?php
endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="id_kendaraan" value="<?= $edit_data['id_kendaraan'] ?? 0; ?>">
            <div class="form-group">
                <label>Nama Kendaraan</label>
                <input type="text" name="nama_kendaraan" class="input-field" value="<?= $edit_data['nama_kendaraan'] ?? ''; ?>" required placeholder="Misal: Mobil, Motor">
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="input-field" style="height: 100px;"><?= $edit_data['keterangan'] ?? ''; ?></textarea>
            </div>
            <button type="submit" name="save" class="btn btn-primary"><?= $edit_data ? 'Update' : 'Simpan'; ?></button>
            <?php if ($edit_data): ?>
                <a href="kendaraan.php" class="btn" style="display: block; text-align: center; margin-top: 10px; background: #eee;">Batal</a>
            <?php
endif; ?>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
