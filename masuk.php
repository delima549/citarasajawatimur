<?php
session_start();
include 'includes/koneksi.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$email = '';
$success = isset($_GET['daftar']) && $_GET['daftar'] === 'berhasil';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT id, nama, username, email, password, profile_photo, status FROM users WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $user = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if (!$user || !password_verify($password, $user['password'])) {
        $error = 'Email atau kata sandi tidak sesuai.';
    } elseif ($user['status'] !== 'Aktif') {
        $error = 'Akun ini sedang nonaktif. Hubungi administrator untuk bantuan.';
    } else {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_foto'] = $user['profile_photo'];
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk Akun — CitaRasaJawaTimur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="account-page">
  <?php include 'includes/theme_toggle.php'; ?>
  <main class="account-layout">
    <section class="account-intro">
      <a href="index.php" class="account-brand">CITARASAJAWATIMUR</a>
      <div class="account-intro__content">
        <span class="eyebrow eyebrow--light">Selamat datang kembali</span>
        <h1>Jelajah rasa favoritmu.</h1>
        <p>Masuk untuk melanjutkan perjalanan kuliner dan menemukan rekomendasi UMKM Jawa Timur pilihanmu.</p>
      </div>
    </section>
    <section class="account-form-wrap">
      <div class="account-form-box">
        <span class="eyebrow">Akun Pengunjung</span>
        <h2>Masuk ke CitaRasaJawaTimur</h2>
        <p class="account-form-box__lead">Gunakan email dan kata sandi yang sudah terdaftar.</p>
        <?php if ($success): ?><div class="account-alert account-alert--success">Registrasi berhasil. Silakan masuk dengan akunmu.</div><?php endif; ?>
        <?php if ($error): ?><div class="account-alert account-alert--error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="post" class="account-form">
          <label>Email<input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required autocomplete="email"></label>
          <label>Kata sandi<span class="password-field"><input id="masuk-password" type="password" name="password" required autocomplete="current-password"><button type="button" class="password-toggle" data-password-toggle aria-controls="masuk-password" aria-label="Tampilkan kata sandi" title="Tampilkan kata sandi"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg></button></span></label>
          <button type="submit" class="btn btn--primary">Masuk</button>
        </form>
        <p class="account-switch">Belum punya akun? <a href="registrasi.php">Daftar sekarang</a></p>
        <a href="index.php" class="account-back">&larr; Kembali ke beranda</a>
      </div>
    </section>
  </main>
  <script src="js/theme-toggle.js"></script>
  <script src="js/password-toggle.js"></script>
</body>
</html>
