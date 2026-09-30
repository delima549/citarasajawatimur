<?php
session_start();
include 'includes/koneksi.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];
$nama = '';
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
  $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';

    if ($nama === '' || strlen($nama) < 3) {
        $errors[] = 'Nama lengkap minimal 3 karakter.';
    }
    if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
      $errors[] = 'Username harus 3 sampai 50 karakter dan hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Masukkan alamat email yang valid.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Kata sandi minimal 6 karakter.';
    }
    if ($password !== $konfirmasi) {
        $errors[] = 'Konfirmasi kata sandi tidak sama.';
    }

    if (!$errors) {
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1");
        mysqli_stmt_bind_param($check, "ss", $email, $username);
        mysqli_stmt_execute($check);
        if (mysqli_stmt_get_result($check)->num_rows > 0) {
          $errors[] = 'Email atau username tersebut sudah digunakan.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
          $stmt = mysqli_prepare($conn, "INSERT INTO users (nama, username, email, password) VALUES (?, ?, ?, ?)");
          mysqli_stmt_bind_param($stmt, "ssss", $nama, $username, $email, $hashedPassword);
            if (mysqli_stmt_execute($stmt)) {
                header('Location: masuk.php?daftar=berhasil');
                exit;
            }
            $errors[] = 'Registrasi belum berhasil. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrasi — CitaRasaJawaTimur</title>
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
        <span class="eyebrow eyebrow--light">Bergabung dengan kami</span>
        <h1>Temukan rasa Jawa Timur lebih dekat.</h1>
        <p>Simpan kuliner favoritmu, ikuti rekomendasi terbaru, dan jelajahi cerita UMKM dari berbagai daerah Jawa Timur.</p>
      </div>
    </section>
    <section class="account-form-wrap">
      <div class="account-form-box">
        <span class="eyebrow">Buat Akun</span>
        <h2>Daftar di CitaRasaJawaTimur</h2>
        <p class="account-form-box__lead">Buat akun untuk menikmati pengalaman jelajah kuliner yang lebih personal.</p>
        <?php if ($errors): ?>
          <div class="account-alert account-alert--error"><ul><?php foreach ($errors as $error): ?><li><?php echo htmlspecialchars($error); ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <form method="post" class="account-form">
          <label>Nama lengkap<input type="text" name="nama" value="<?php echo htmlspecialchars($nama); ?>" required autocomplete="name"></label>
          <label>Username<input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required minlength="3" maxlength="50" pattern="[A-Za-z0-9._-]+" autocomplete="username"></label>
          <label>Email<input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required autocomplete="email"></label>
          <label>Kata sandi<span class="password-field"><input id="registrasi-password" type="password" name="password" minlength="6" required autocomplete="new-password"><button type="button" class="password-toggle" data-password-toggle aria-controls="registrasi-password" aria-label="Tampilkan kata sandi" title="Tampilkan kata sandi"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg></button></span></label>
          <label>Konfirmasi kata sandi<span class="password-field"><input id="registrasi-konfirmasi" type="password" name="konfirmasi_password" minlength="6" required autocomplete="new-password"><button type="button" class="password-toggle" data-password-toggle aria-controls="registrasi-konfirmasi" aria-label="Tampilkan kata sandi" title="Tampilkan kata sandi"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg></button></span></label>
          <button type="submit" class="btn btn--primary">Buat Akun</button>
        </form>
        <p class="account-switch">Sudah punya akun? <a href="masuk.php">Masuk sekarang</a></p>
        <a href="index.php" class="account-back">&larr; Kembali ke beranda</a>
      </div>
    </section>
  </main>
  <script src="js/theme-toggle.js"></script>
  <script src="js/password-toggle.js"></script>
</body>
</html>
