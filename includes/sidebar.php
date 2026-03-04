<?php
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'];
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo"><i class='bx bxs-parking'></i></div>
        <h2>E-Parking</h2>
    </div>
    <nav class="sidebar-nav">
        <?php if ($role === 'admin'): ?>
            <div class="nav-label">Menu Utama</div>
            <a href="index.php" class="nav-link <?= $current_page == 'index.php' ? 'active' : ''; ?>">
                <i class='bx bxs-dashboard'></i> Dashboard
            </a>
            
            <div class="nav-label">Data Master</div>
            <a href="users.php" class="nav-link <?= $current_page == 'users.php' ? 'active' : ''; ?>">
                <i class='bx bxs-user-account'></i> Manajemen User
            </a>
            <a href="kendaraan.php" class="nav-link <?= $current_page == 'kendaraan.php' ? 'active' : ''; ?>">
                <i class='bx bxs-car'></i> Tipe Kendaraan
            </a>
            <a href="tarif.php" class="nav-link <?= $current_page == 'tarif.php' ? 'active' : ''; ?>">
                <i class='bx bxs-badge-dollar'></i> Tarif Parkir
            </a>
            <a href="area.php" class="nav-link <?= $current_page == 'area.php' ? 'active' : ''; ?>">
                <i class='bx bxs-map-pin'></i> Area Parkir
            </a>
            
            <div class="nav-label">Audit</div>
            <a href="logs.php" class="nav-link <?= $current_page == 'logs.php' ? 'active' : ''; ?>">
                <i class='bx bx-history'></i> Log Aktivitas
            </a>
            
        <?php
elseif ($role === 'petugas'): ?>
            <div class="nav-label">Layanan</div>
            <a href="index.php" class="nav-link <?= $current_page == 'index.php' ? 'active' : ''; ?>">
                <i class='bx bxs-dashboard'></i> Dashboard
            </a>
            <a href="transaksi.php" class="nav-link <?= $current_page == 'transaksi.php' ? 'active' : ''; ?>">
                <i class='bx bxs-right-arrow-circle'></i> Transaksi Parkir
            </a>
            
        <?php
elseif ($role === 'owner'): ?>
            <div class="nav-label">Analisis</div>
            <a href="index.php" class="nav-link <?= $current_page == 'index.php' ? 'active' : ''; ?>">
                <i class='bx bxs-dashboard'></i> Dashboard
            </a>
            <a href="laporan.php" class="nav-link <?= $current_page == 'laporan.php' ? 'active' : ''; ?>">
                <i class='bx bxs-report'></i> Laporan Rekap
            </a>
        <?php
endif; ?>
        
        <div style="margin-top: auto; padding-top: 2rem;">
            <a href="../auth/logout.php" class="nav-link" style="color: var(--danger);">
                <i class='bx bx-log-out-circle'></i> Keluar Aplikasi
            </a>
        </div>
    </nav>
</aside>

<main class="main-content">
    <div class="top-bar">
        <h1><?= $title; ?></h1>
        <div class="user-profile">
            <i class='bx bxs-user-circle' style="font-size: 1.5rem; color: var(--primary);"></i>
            <div style="text-align: left;">
                <div style="font-weight: 700; font-size: 0.875rem; color: var(--text);"><?= $_SESSION['nama_lengkap']; ?></div>
                <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;"><?= $_SESSION['role']; ?></div>
            </div>
        </div>
    </div>
    <div class="fade-in">
