<?php
require_once __DIR__ . '/../config/database.php';

$kotaBlitarCheck = mysqli_query($conn, "SELECT id FROM daerah WHERE slug = 'kota-blitar' LIMIT 1");
if ($kotaBlitarCheck && mysqli_num_rows($kotaBlitarCheck) === 0) {
    mysqli_query($conn, "INSERT INTO daerah (nama, jenis, slug, gambar, deskripsi) VALUES ('Kota Blitar', 'Kota', 'kota-blitar', 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kota Blitar, salah satu kota di Jawa Timur.')");
}

$wilayahColumn = mysqli_query($conn, "SHOW COLUMNS FROM kuliner LIKE 'wilayah'");
if ($wilayahColumn && mysqli_num_rows($wilayahColumn) === 0) {
    mysqli_query($conn, "ALTER TABLE kuliner ADD wilayah VARCHAR(100) DEFAULT NULL AFTER slug");
    mysqli_query($conn, "UPDATE kuliner SET wilayah = CASE id
        WHEN 1 THEN 'Surabaya Pusat'
        WHEN 2 THEN 'Surabaya Utara'
        WHEN 3 THEN 'Surabaya Timur'
        WHEN 4 THEN 'Surabaya Selatan'
        WHEN 5 THEN 'Surabaya Barat'
    END WHERE id BETWEEN 1 AND 5");
}
mysqli_query($conn, "ALTER TABLE kuliner MODIFY wilayah VARCHAR(100) DEFAULT NULL");

$kulinerPriceRangeColumns = mysqli_query($conn, "SHOW COLUMNS FROM kuliner LIKE 'harga_min'");
if ($kulinerPriceRangeColumns && mysqli_num_rows($kulinerPriceRangeColumns) === 0) {
    mysqli_query($conn, "ALTER TABLE kuliner
        ADD harga_min INT UNSIGNED NOT NULL DEFAULT 0 AFTER harga,
        ADD harga_max INT UNSIGNED NOT NULL DEFAULT 0 AFTER harga_min");
    mysqli_query($conn, "UPDATE kuliner SET harga_min = harga, harga_max = harga WHERE harga_min = 0 AND harga_max = 0");
}

$metadataColumns = mysqli_query($conn, "SHOW COLUMNS FROM kuliner LIKE 'harga'");
if ($metadataColumns && mysqli_num_rows($metadataColumns) === 0) {
    mysqli_query($conn, "ALTER TABLE kuliner
        ADD harga INT UNSIGNED NOT NULL DEFAULT 0 AFTER wilayah,
        ADD rating DECIMAL(2,1) NOT NULL DEFAULT 0.0 AFTER harga,
        ADD jumlah_penilai INT UNSIGNED NOT NULL DEFAULT 0 AFTER rating");
    mysqli_query($conn, "UPDATE kuliner SET
        harga = CASE id WHEN 1 THEN 25000 WHEN 2 THEN 15000 WHEN 3 THEN 22000 WHEN 4 THEN 18000 WHEN 5 THEN 30000 ELSE 0 END,
        rating = CASE id WHEN 1 THEN 4.8 WHEN 2 THEN 4.6 WHEN 3 THEN 4.7 WHEN 4 THEN 4.5 WHEN 5 THEN 4.9 ELSE 0.0 END,
        jumlah_penilai = CASE id WHEN 1 THEN 284 WHEN 2 THEN 192 WHEN 3 THEN 231 WHEN 4 THEN 167 WHEN 5 THEN 356 ELSE 0 END
        WHERE id BETWEEN 1 AND 5");
}
mysqli_query($conn, "UPDATE kuliner SET
    harga_min = CASE WHEN harga_min = 0 AND kategori = 'Minuman' THEN 5000 WHEN harga_min = 0 AND kategori IN ('Makanan Ringan','Jajanan') THEN 7000 WHEN harga_min = 0 THEN 15000 ELSE harga_min END,
    harga_max = CASE WHEN harga_max = 0 AND kategori = 'Minuman' THEN 15000 WHEN harga_max = 0 AND kategori IN ('Makanan Ringan','Jajanan') THEN 25000 WHEN harga_max = 0 THEN 45000 ELSE harga_max END,
    rating = CASE WHEN rating = 0 THEN ROUND(4.5 + MOD(id, 5) * 0.1, 1) ELSE rating END,
    jumlah_penilai = CASE WHEN jumlah_penilai = 0 THEN 50 + MOD(id * 29, 251) ELSE jumlah_penilai END
    WHERE harga_min = 0 OR harga_max = 0 OR rating = 0 OR jumlah_penilai = 0");
mysqli_query($conn, "UPDATE kuliner SET harga = ROUND((harga_min + harga_max) / 2) WHERE harga = 0");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) DEFAULT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_photo VARCHAR(500) DEFAULT NULL,
    status ENUM('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$usernameColumn = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'username'");
if ($usernameColumn && mysqli_num_rows($usernameColumn) === 0) {
    mysqli_query($conn, "ALTER TABLE users ADD username VARCHAR(50) DEFAULT NULL AFTER nama");
    mysqli_query($conn, "UPDATE users SET username = CONCAT('pengguna', id) WHERE username IS NULL OR username = ''");
    mysqli_query($conn, "ALTER TABLE users MODIFY username VARCHAR(50) NOT NULL");
} elseif ($usernameColumn) {
    $usernameDefinition = mysqli_fetch_assoc($usernameColumn);
    if (($usernameDefinition['Null'] ?? 'NO') === 'YES') {
        mysqli_query($conn, "UPDATE users SET username = CONCAT('pengguna', id) WHERE username IS NULL OR username = ''");
        mysqli_query($conn, "ALTER TABLE users MODIFY username VARCHAR(50) NOT NULL");
    }
}
$usernameIndex = mysqli_query($conn, "SHOW INDEX FROM users WHERE Key_name = 'uq_users_username'");
if ($usernameIndex && mysqli_num_rows($usernameIndex) === 0) {
    mysqli_query($conn, "ALTER TABLE users ADD UNIQUE KEY uq_users_username (username)");
}

$profilePhotoColumn = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'profile_photo'");
if ($profilePhotoColumn && mysqli_num_rows($profilePhotoColumn) === 0) {
    mysqli_query($conn, "ALTER TABLE users ADD profile_photo VARCHAR(500) DEFAULT NULL AFTER password");
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS user_recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_kuliner VARCHAR(150) NOT NULL,
    kategori VARCHAR(50) NOT NULL DEFAULT 'Makanan Berat',
    wilayah VARCHAR(100) NOT NULL,
    lokasi VARCHAR(255) NOT NULL,
    harga INT UNSIGNED NOT NULL DEFAULT 0,
    rating DECIMAL(2,1) NOT NULL DEFAULT 0.0,
    deskripsi TEXT NOT NULL,
    gambar VARCHAR(500) DEFAULT NULL,
    status ENUM('Menunggu','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu',
    catatan_admin VARCHAR(255) DEFAULT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS user_articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    jenis ENUM('Artikel','Resep') NOT NULL,
    judul VARCHAR(255) NOT NULL,
    ringkasan TEXT NOT NULL,
    isi LONGTEXT NOT NULL,
    gambar VARCHAR(500) NOT NULL,
    slug VARCHAR(180) DEFAULT NULL UNIQUE,
    status ENUM('Menunggu','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu',
    catatan_admin VARCHAR(255) DEFAULT NULL,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "ALTER TABLE user_recommendations MODIFY wilayah VARCHAR(100) NOT NULL");

$priceRangeColumns = mysqli_query($conn, "SHOW COLUMNS FROM user_recommendations LIKE 'harga_min'");
if ($priceRangeColumns && mysqli_num_rows($priceRangeColumns) === 0) {
    mysqli_query($conn, "ALTER TABLE user_recommendations
        ADD harga_min INT UNSIGNED NOT NULL DEFAULT 0 AFTER lokasi,
        ADD harga_max INT UNSIGNED NOT NULL DEFAULT 0 AFTER harga_min");
    $legacyPriceColumn = mysqli_query($conn, "SHOW COLUMNS FROM user_recommendations LIKE 'harga'");
    if ($legacyPriceColumn && mysqli_num_rows($legacyPriceColumn) > 0) {
        mysqli_query($conn, "UPDATE user_recommendations SET harga_min = harga, harga_max = harga WHERE harga_min = 0 AND harga_max = 0");
    }
}

$kulinerPublicationColumn = mysqli_query($conn, "SHOW COLUMNS FROM kuliner LIKE 'publikasi'");
if ($kulinerPublicationColumn && mysqli_num_rows($kulinerPublicationColumn) === 0) {
    mysqli_query($conn, "ALTER TABLE kuliner ADD publikasi VARCHAR(255) NOT NULL DEFAULT 'Katalog Kuliner' AFTER is_rekomendasi");
}

$adminCheck = mysqli_query($conn, "SELECT id FROM admin_users LIMIT 1");
if ($adminCheck && mysqli_num_rows($adminCheck) === 0 && $initialAdminPassword !== '') {
    $hashedAdminPassword = password_hash($initialAdminPassword, PASSWORD_DEFAULT);
    $adminStmt = mysqli_prepare($conn, "INSERT INTO admin_users (username, password) VALUES (?, ?)");
    mysqli_stmt_bind_param($adminStmt, "ss", $initialAdminUsername, $hashedAdminPassword);
    mysqli_stmt_execute($adminStmt);
}

require_once __DIR__ . '/content.php';
ensure_site_content_table($conn);
ensure_site_articles_table($conn);
ensure_regional_culinary_catalog($conn);
?>
