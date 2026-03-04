<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

check_login();
check_role('admin');

$title = 'Log Aktivitas';

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="table-card">
    <div class="table-header">
        <h2>Log Aktivitas Sistem</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Aktivitas</th>
            </tr>
        </thead>
        <tbody>
            <?php
$logs = query("SELECT l.*, u.username, u.nama_lengkap FROM log_aktivitas l JOIN user u ON l.id_user = u.id_user ORDER BY waktu DESC");
foreach ($logs as $log):
?>
            <tr>
                <td><?= date('d/m/Y H:i:s', strtotime($log['waktu'])); ?></td>
                <td><strong><?= $log['username']; ?></strong></td>
                <td><?= $log['nama_lengkap']; ?></td>
                <td><?= $log['aktivitas']; ?></td>
            </tr>
            <?php
endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
