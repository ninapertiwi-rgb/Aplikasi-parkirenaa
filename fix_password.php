<?php
require_once 'config/database.php';

$new_password = 'admin123';
$new_hash = password_hash($new_password, PASSWORD_BCRYPT);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>
<title>Fix Password</title>
<style>
body{font-family:monospace;padding:30px;background:#1a1a2e;color:#eee;}
h2{color:#00d4ff;} h3{color:#ffd700;}
.ok{color:#00ff88;font-weight:bold;}
.err{color:#ff4466;font-weight:bold;}
.box{background:#16213e;padding:15px;border-radius:8px;margin:10px 0;border:1px solid #333;}
code{background:#0f3460;padding:3px 8px;border-radius:4px;color:#00d4ff;}
table{border-collapse:collapse;width:100%;}
th,td{border:1px solid #444;padding:8px 12px;text-align:left;}
th{background:#0f3460;}
a.btn{display:inline-block;background:#00d4ff;color:#000;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:bold;margin-top:15px;}
</style></head><body>";

echo "<h2>🔧 PASSWORD FIX TOOL</h2>";

// =============================================
// 1. CEK STRUKTUR KOLOM PASSWORD
// =============================================
echo "<h3>1. Struktur Kolom Password</h3><div class='box'>";
$desc = mysqli_query($conn, "DESCRIBE user");
if ($desc) {
    echo "<table><tr><th>Field</th><th>Type</th><th>Status</th></tr>";
    while ($row = mysqli_fetch_assoc($desc)) {
        $status = '';
        if ($row['Field'] === 'password') {
            if (strpos($row['Type'], '255') !== false) {
                $status = "<span class='ok'>✅ OK (cukup panjang)</span>";
            }
            else {
                $status = "<span class='err'>❌ TERLALU PENDEK! Harus VARCHAR(255)</span>";
            }
        }
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>$status</td></tr>";
    }
    echo "</table>";
}
else {
    echo "<span class='err'>❌ Gagal baca struktur tabel: " . mysqli_error($conn) . "</span>";
}

// Paksa fix kolom jadi VARCHAR(255)
mysqli_query($conn, "ALTER TABLE user MODIFY password VARCHAR(255) NOT NULL");
echo "<p class='ok'>✅ Kolom password dipastikan VARCHAR(255)</p>";
echo "</div>";

// =============================================
// 2. RESET PASSWORD SEMUA USER
// =============================================
echo "<h3>2. Reset Password Semua User → <code>$new_password</code></h3><div class='box'>";
echo "<p>Hash baru: <code>$new_hash</code> (panjang: " . strlen($new_hash) . " karakter)</p>";

$users_q = mysqli_query($conn, "SELECT id_user, username, role FROM user");
if (mysqli_num_rows($users_q) === 0) {
    echo "<span class='err'>❌ Tidak ada user di database! Jalankan SQL seed dulu.</span>";
}
else {
    echo "<table><tr><th>Username</th><th>Role</th><th>Status</th></tr>";
    while ($u = mysqli_fetch_assoc($users_q)) {
        $safe_hash = mysqli_real_escape_string($conn, $new_hash);
        $upd = mysqli_query($conn, "UPDATE user SET password='$safe_hash' WHERE id_user={$u['id_user']}");
        $status = $upd
            ? "<span class='ok'>✅ Berhasil direset</span>"
            : "<span class='err'>❌ Gagal: " . mysqli_error($conn) . "</span>";
        echo "<tr><td>{$u['username']}</td><td>{$u['role']}</td><td>$status</td></tr>";
    }
    echo "</table>";
}
echo "</div>";

// =============================================
// 3. VERIFIKASI HASH TERSIMPAN
// =============================================
echo "<h3>3. Verifikasi Password Tersimpan</h3><div class='box'>";
$verify_q = mysqli_query($conn, "SELECT username, password FROM user");
echo "<table><tr><th>Username</th><th>Hash (20 char)</th><th>Panjang</th><th>Verifikasi '$new_password'</th></tr>";
while ($v = mysqli_fetch_assoc($verify_q)) {
    $len = strlen($v['password']);
    $ok = password_verify($new_password, $v['password']);
    $len_status = $len >= 60
        ? "<span class='ok'>$len ✅</span>"
        : "<span class='err'>$len ❌ TERPOTONG</span>";
    $verify_status = $ok
        ? "<span class='ok'>✅ COCOK</span>"
        : "<span class='err'>❌ TIDAK COCOK</span>";
    echo "<tr>
        <td>{$v['username']}</td>
        <td><code>" . substr($v['password'], 0, 20) . "...</code></td>
        <td>$len_status</td>
        <td>$verify_status</td>
    </tr>";
}
echo "</table></div>";

// =============================================
// 4. RINGKASAN
// =============================================
echo "<h3>4. Ringkasan Login</h3><div class='box'>";
echo "<table><tr><th>Username</th><th>Password</th></tr>";
$list = mysqli_query($conn, "SELECT username, role FROM user");
while ($r = mysqli_fetch_assoc($list)) {
    echo "<tr><td>{$r['username']} ({$r['role']})</td><td><code>$new_password</code></td></tr>";
}
echo "</table>";
echo "<br><a class='btn' href='auth/login.php'>➜ Pergi ke Halaman Login</a>";
echo "</div>";

echo "</body></html>";
?>
