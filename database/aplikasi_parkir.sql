-- Database: aplikasi_parkir

CREATE DATABASE IF NOT EXISTS aplikasi_parkir;
USE aplikasi_parkir;

-- 1. Table User
CREATE TABLE user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'petugas', 'owner') NOT NULL
) ENGINE=InnoDB;

-- 2. Table Kendaraan (Tipe Kendaraan)
CREATE TABLE kendaraan (
    id_kendaraan INT AUTO_INCREMENT PRIMARY KEY,
    nama_kendaraan VARCHAR(50) NOT NULL, -- e.g., Motor, Mobil, Truck
    keterangan TEXT
) ENGINE=InnoDB;

-- 3. Table Tarif Parkir
CREATE TABLE tarif_parkir (
    id_tarif INT AUTO_INCREMENT PRIMARY KEY,
    id_kendaraan INT NOT NULL,
    tarif_per_jam DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_kendaraan) REFERENCES kendaraan(id_kendaraan) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Table Area Parkir
CREATE TABLE area_parkir (
    id_area INT AUTO_INCREMENT PRIMARY KEY,
    nama_area VARCHAR(50) NOT NULL, -- e.g., Blok A, Lantai 1
    kapasitas INT NOT NULL,
    terisi INT DEFAULT 0
) ENGINE=InnoDB;

-- 5. Table Transaksi
CREATE TABLE transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(20) NOT NULL UNIQUE,
    plat_nomor VARCHAR(15) NOT NULL,
    id_kendaraan INT NOT NULL,
    id_area INT NOT NULL,
    waktu_masuk DATETIME NOT NULL,
    waktu_keluar DATETIME DEFAULT NULL,
    biaya DECIMAL(10,2) DEFAULT 0,
    status ENUM('parkir', 'selesai') DEFAULT 'parkir',
    id_petugas INT NOT NULL,
    FOREIGN KEY (id_kendaraan) REFERENCES kendaraan(id_kendaraan),
    FOREIGN KEY (id_area) REFERENCES area_parkir(id_area),
    FOREIGN KEY (id_petugas) REFERENCES user(id_user)
) ENGINE=InnoDB;

-- 6. Table Log Aktivitas
CREATE TABLE log_aktivitas (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    aktivitas TEXT NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed Data for Testing
INSERT INTO user (username, password, nama_lengkap, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin'), -- password: password
('petugas', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Petugas Unit 1', 'petugas'),
('owner', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bapak Owner', 'owner');

INSERT INTO kendaraan (nama_kendaraan, keterangan) VALUES 
('Motor', 'Kendaraan roda dua'),
('Mobil', 'Kendaraan roda empat');

INSERT INTO tarif_parkir (id_kendaraan, tarif_per_jam) VALUES 
(1, 2000.00),
(2, 5000.00);

INSERT INTO area_parkir (nama_area, kapasitas) VALUES 
('Blok A', 50),
('Blok B', 30);
