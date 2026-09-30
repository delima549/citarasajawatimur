<?php
$localConfigPath = __DIR__ . '/database.local.php';
$localConfig = is_file($localConfigPath) ? require $localConfigPath : [];
$localConfig = is_array($localConfig) ? $localConfig : [];

$host = $localConfig['host'] ?? (getenv('DB_HOST') ?: 'localhost');
$user = $localConfig['username'] ?? (getenv('DB_USER') ?: 'root');
$pass = $localConfig['password'] ?? (getenv('DB_PASSWORD') ?: '');
$db = $localConfig['database'] ?? (getenv('DB_NAME') ?: 'cita_rasa_jatim');
$initialAdminUsername = $localConfig['initial_admin_username'] ?? (getenv('SITE_INITIAL_ADMIN_USERNAME') ?: 'admin');
$initialAdminPassword = $localConfig['initial_admin_password'] ?? (getenv('SITE_INITIAL_ADMIN_PASSWORD') ?: '');

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error() .
        " — Pastikan sudah membuat database 'cita_rasa_jatim' dan mengimpor file database/cita_rasa_jatim.sql lewat phpMyAdmin.");
}
mysqli_set_charset($conn, "utf8mb4");
?>