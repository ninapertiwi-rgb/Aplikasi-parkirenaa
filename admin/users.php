<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

check_login();
check_role('admin');

$title = 'Manajemen User';
$msg = $_SESSION['msg'] ?? '';
$msg_type = $_SESSION['msg_type'] ?? '';
unset($_SESSION['msg'], $_SESSION['msg_type']);

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id !== $_SESSION['id_user']) { // Prevent deleting self
        mysqli_query($conn, "DELETE FROM user WHERE id_user = $id");
        add_log($_SESSION['id_user'], "Deleted user ID $id");
        redirect('users.php', 'User berhasil dihapus');
    }
    else {
        redirect('users.php', 'Tidak bisa menghapus akun sendiri', 'danger');
    }
}

// Handle Insert/Update
if (isset($_POST['save'])) {
    $id_user = (int)$_POST['id_user'];
    $username = escape($_POST['username']);
    $nama_lengkap = escape($_POST['nama_lengkap']);
    $role = escape($_POST['role']);
    $password = $_POST['password'];

    if ($id_user > 0) {
        // Update
        if (!empty($password)) {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE user SET username='$username', password='$hashed_pass', nama_lengkap='$nama_lengkap', role='$role' WHERE id_user=$id_user";
        }
        else {
            $query = "UPDATE user SET username='$username', nama_lengkap='$nama_lengkap', role='$role' WHERE id_user=$id_user";
        }
        mysqli_query($conn, $query);
        add_log($_SESSION['id_user'], "Updated user $username");
        redirect('users.php', 'User berhasil diupdate');
    }
    else {
        // Insert
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO user (username, password, nama_lengkap, role) VALUES ('$username', '$hashed_pass', '$nama_lengkap', '$role')";
        mysqli_query($conn, $query);
        add_log($_SESSION['id_user'], "Created user $username");
        redirect('users.php', 'User berhasil ditambah');
    }
}

$edit_user = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit_user = query("SELECT * FROM user WHERE id_user = $id")[0] ?? null;
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
    <!-- Table List -->
    <div class="table-card">
        <div class="table-header">
            <h2>Daftar User</h2>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
$users = query("SELECT * FROM user ORDER BY id_user DESC");
foreach ($users as $u):
?>
                <tr>
                    <td><?= $u['username']; ?></td>
                    <td><?= $u['nama_lengkap']; ?></td>
                    <td><span class="badge badge-primary"><?= $u['role']; ?></span></td>
                    <td>
                        <a href="?edit=<?= $u['id_user']; ?>" style="color: var(--primary); margin-right: 10px;">Edit</a>
                        <a href="?delete=<?= $u['id_user']; ?>" style="color: var(--danger);" onclick="return confirm('Hapus user ini?')">Hapus</a>
                    </td>
                </tr>
                <?php
endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Form Add/Edit -->
    <div class="table-card" style="padding: 1.5rem;">
        <div class="table-header" style="padding: 0 0 1rem 0; margin-bottom: 1.5rem;">
            <h2><?= $edit_user ? 'Edit User' : 'Tambah User'; ?></h2>
        </div>
        
        <?php if ($msg): ?>
            <div class="alert alert-<?= $msg_type; ?>"><?= $msg; ?></div>
        <?php
endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="id_user" value="<?= $edit_user['id_user'] ?? 0; ?>">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="input-field" value="<?= $edit_user['username'] ?? ''; ?>" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="input-field" value="<?= $edit_user['nama_lengkap'] ?? ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" class="input-field" required>
                    <option value="admin" <?=(isset($edit_user['role']) && $edit_user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="petugas" <?=(isset($edit_user['role']) && $edit_user['role'] == 'petugas') ? 'selected' : ''; ?>>Petugas</option>
                    <option value="owner" <?=(isset($edit_user['role']) && $edit_user['role'] == 'owner') ? 'selected' : ''; ?>>Owner</option>
                </select>
            </div>
            <div class="form-group">
                <label>Password <?= $edit_user ? '<small>(Kosongkan jika tidak ganti)</small>' : ''; ?></label>
                <input type="password" name="password" class="input-field" <?= $edit_user ? '' : 'required'; ?>>
            </div>
            <button type="submit" name="save" class="btn btn-primary"><?= $edit_user ? 'Update' : 'Simpan'; ?></button>
            <?php if ($edit_user): ?>
                <a href="users.php" class="btn" style="display: block; text-align: center; margin-top: 10px; background: #eee;">Batal</a>
            <?php
endif; ?>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
