<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

check_login();
check_role('admin');

$title = 'Area Parkir';
$msg = $_SESSION['msg'] ?? '';
$msg_type = $_SESSION['msg_type'] ?? '';
unset($_SESSION['msg'], $_SESSION['msg_type']);

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM area_parkir WHERE id_area = $id");
    add_log($_SESSION['id_user'], "Deleted parking area ID $id");
    redirect('area.php', 'Area berhasil dihapus');
}

// Handle Insert/Update
if (isset($_POST['save'])) {
    $id_area = (int)$_POST['id_area'];
    $nama_area = escape($_POST['nama_area']);
    $kapasitas = (int)$_POST['kapasitas'];

    if ($id_area > 0) {
        mysqli_query($conn, "UPDATE area_parkir SET nama_area='$nama_area', kapasitas=$kapasitas WHERE id_area=$id_area");
        add_log($_SESSION['id_user'], "Updated parking area $nama_area");
        redirect('area.php', 'Area berhasil diupdate');
    }
    else {
        mysqli_query($conn, "INSERT INTO area_parkir (nama_area, kapasitas) VALUES ('$nama_area', $kapasitas)");
        add_log($_SESSION['id_user'], "Created parking area $nama_area");
        redirect('area.php', 'Area berhasil ditambah');
    }
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_data = query("SELECT * FROM area_parkir WHERE id_area = $id")[0] ?? null;
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
    <div class="table-card">
        <div class="table-header">
            <h2>Daftar Area Parkir</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nama Area</th>
                    <th>Kapasitas</th>
                    <th>Terisi</th>
                    <th>Tersedia</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
$data = query("SELECT * FROM area_parkir ORDER BY nama_area ASC");
foreach ($data as $item):
    $tersedia = $item['kapasitas'] - $item['terisi'];
?>
                <tr>
                    <td><strong><?= $item['nama_area']; ?></strong></td>
                    <td><?= $item['kapasitas']; ?></td>
                    <td><span class="badge badge-danger"><?= $item['terisi']; ?></span></td>
                    <td><span class="badge badge-success"><?= $tersedia; ?></span></td>
                    <td>
                        <a href="?edit=<?= $item['id_area']; ?>" style="color: var(--primary); margin-right: 10px;">Edit</a>
                        <a href="?delete=<?= $item['id_area']; ?>" style="color: var(--danger);" onclick="return confirm('Hapus ini?')">Hapus</a>
                    </td>
                </tr>
                <?php
endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="table-card" style="padding: 1.5rem;">
        <div class="table-header" style="padding: 0 0 1rem 0; margin-bottom: 1.5rem;">
            <h2><?= $edit_data ? 'Edit Area' : 'Tambah Area'; ?></h2>
        </div>
        
        <?php if ($msg): ?>
            <div class="alert alert-<?= $msg_type; ?>"><?= $msg; ?></div>
        <?php
endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="id_area" value="<?= $edit_data['id_area'] ?? 0; ?>">
            <div class="form-group">
                <label>Nama Area</label>
                <input type="text" name="nama_area" class="input-field" value="<?= $edit_data['nama_area'] ?? ''; ?>" required placeholder="Misal: Blok A, Lantai 1">
            </div>
            <div class="form-group">
                <label>Kapasitas</label>
                <input type="number" name="kapasitas" class="input-field" value="<?= $edit_data['kapasitas'] ?? ''; ?>" required placeholder="Misal: 50">
            </div>
            <button type="submit" name="save" class="btn btn-primary"><?= $edit_data ? 'Update' : 'Simpan'; ?></button>
            <?php if ($edit_data): ?>
                <a href="area.php" class="btn" style="display: block; text-align: center; margin-top: 10px; background: #eee;">Batal</a>
            <?php
endif; ?>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
