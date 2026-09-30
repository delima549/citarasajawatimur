<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include 'includes/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: masuk.php');
    exit;
}

  function save_profile_image(array $file, string $prefix, array &$errors, bool $required = false): ?string
  {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
      if ($required) {
        $errors[] = 'Pilih gambar untuk diunggah.';
      }
      return null;
    }

    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $imageInfo = !empty($file['tmp_name']) && is_uploaded_file($file['tmp_name']) ? getimagesize($file['tmp_name']) : false;
    $mime = $imageInfo['mime'] ?? '';
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !isset($allowedTypes[$mime])) {
      $errors[] = 'Gunakan gambar JPG, PNG, atau WEBP yang valid.';
      return null;
    }
    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
      $errors[] = 'Ukuran gambar maksimal 5 MB.';
      return null;
    }

    $fileName = $prefix . bin2hex(random_bytes(8)) . '.' . $allowedTypes[$mime];
    $uploadPath = __DIR__ . '/img/user_uploads/' . $fileName;
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
      $errors[] = 'Gambar belum berhasil disimpan. Silakan coba lagi.';
      return null;
    }
    return 'img/user_uploads/' . $fileName;
  }

  $userId = (int) $_SESSION['user_id'];
  $userStmt = mysqli_prepare($conn, 'SELECT id, nama, username, email, password, profile_photo FROM users WHERE id = ? LIMIT 1');
  mysqli_stmt_bind_param($userStmt, 'i', $userId);
  mysqli_stmt_execute($userStmt);
  $user = mysqli_stmt_get_result($userStmt)->fetch_assoc();
  if (!$user) {
    unset($_SESSION['user_id'], $_SESSION['user_nama'], $_SESSION['user_email'], $_SESSION['user_foto']);
    header('Location: masuk.php');
    exit;
  }
  $_SESSION['user_nama'] = $user['nama'];
  $_SESSION['user_username'] = $user['username'];
  $_SESSION['user_foto'] = $user['profile_photo'];

  $csrfToken = $_SESSION['profile_csrf'] ??= bin2hex(random_bytes(32));
$wilayahValid = [];
$wilayahResult = mysqli_query($conn, "SELECT nama FROM daerah ORDER BY jenis ASC, nama ASC");
while ($wilayahRow = mysqli_fetch_assoc($wilayahResult)) {
  $wilayahValid[] = $wilayahRow['nama'];
}
$kategoriValid = ['Makanan Berat', 'Makanan Ringan', 'Makanan Berkuah', 'Minuman', 'Jajanan'];
$errors = [];
$success = '';
$activeTab = 'recommendations';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $postedAction = $_POST['action'] ?? '';
  $activeTab = match ($postedAction) {
    'update_profile', 'change_password' => 'account',
    'submit_article' => 'writing',
    'add_recommendation' => 'recommendations',
    default => 'recommendations'
  };
  if (!hash_equals($csrfToken, $_POST['csrf_token'] ?? '')) {
    $errors[] = 'Sesi formulir kedaluwarsa. Muat ulang halaman dan coba lagi.';
  } else {
    $action = $postedAction;
    if ($action === 'update_profile') {
      $nama = trim($_POST['nama'] ?? '');
      $username = trim($_POST['username'] ?? '');
      if (strlen($nama) < 3 || strlen($nama) > 100) {
        $errors[] = 'Nama lengkap harus terdiri dari 3 sampai 100 karakter.';
      }
      if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
        $errors[] = 'Username harus 3 sampai 50 karakter dan hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.';
      } else {
        $usernameCheck = mysqli_prepare($conn, 'SELECT id FROM users WHERE username = ? AND id <> ? LIMIT 1');
        mysqli_stmt_bind_param($usernameCheck, 'si', $username, $userId);
        mysqli_stmt_execute($usernameCheck);
        if (mysqli_stmt_get_result($usernameCheck)->num_rows > 0) {
          $errors[] = 'Username tersebut sudah digunakan.';
        }
      }

      $newPhoto = null;
      if (!$errors && isset($_FILES['foto_profil'])) {
        $newPhoto = save_profile_image($_FILES['foto_profil'], 'profil_', $errors);
      }
      if (!$errors) {
        $photoPath = $newPhoto ?? $user['profile_photo'];
        $update = mysqli_prepare($conn, 'UPDATE users SET nama = ?, username = ?, profile_photo = ? WHERE id = ?');
        mysqli_stmt_bind_param($update, 'sssi', $nama, $username, $photoPath, $userId);
        if (mysqli_stmt_execute($update)) {
          $user['nama'] = $nama;
          $user['username'] = $username;
          $user['profile_photo'] = $photoPath;
          $_SESSION['user_nama'] = $nama;
                    $_SESSION['user_username'] = $username;
          $_SESSION['user_foto'] = $photoPath;
          $success = 'Profil berhasil diperbarui.';
        } else {
          $errors[] = 'Profil belum berhasil diperbarui. Silakan coba lagi.';
        }
      }
    } elseif ($action === 'change_password') {
      $currentPassword = $_POST['current_password'] ?? '';
      $newPassword = $_POST['new_password'] ?? '';
      $confirmPassword = $_POST['confirm_password'] ?? '';
      if (!password_verify($currentPassword, $user['password'])) {
        $errors[] = 'Kata sandi saat ini tidak sesuai.';
      }
      if (strlen($newPassword) < 8) {
        $errors[] = 'Kata sandi baru minimal 8 karakter.';
      }
      if ($newPassword !== $confirmPassword) {
        $errors[] = 'Konfirmasi kata sandi baru tidak sama.';
      }
      if (!$errors) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = mysqli_prepare($conn, 'UPDATE users SET password = ? WHERE id = ?');
        mysqli_stmt_bind_param($update, 'si', $hashedPassword, $userId);
        if (mysqli_stmt_execute($update)) {
          $success = 'Kata sandi berhasil diganti.';
        } else {
          $errors[] = 'Kata sandi belum berhasil diganti. Silakan coba lagi.';
        }
      }
    } elseif ($action === 'add_recommendation') {
      $namaKuliner = trim($_POST['nama_kuliner'] ?? '');
      $kategori = $_POST['kategori'] ?? '';
      $wilayah = $_POST['wilayah'] ?? '';
      $lokasi = trim($_POST['lokasi'] ?? '');
      $hargaMin = (int) ($_POST['harga_min'] ?? 0);
      $hargaMax = (int) ($_POST['harga_max'] ?? 0);
      $rating = (float) ($_POST['rating'] ?? 0);
      $deskripsi = trim($_POST['deskripsi'] ?? '');

      if ($namaKuliner === '') $errors[] = 'Nama kuliner wajib diisi.';
      if (!in_array($kategori, $kategoriValid, true)) $errors[] = 'Pilih kategori kuliner yang tersedia.';
      if (!in_array($wilayah, $wilayahValid, true)) $errors[] = 'Pilih daerah Jawa Timur yang sesuai.';
      if ($lokasi === '') $errors[] = 'Lokasi atau alamat wajib diisi.';
      if ($hargaMin < 0 || $hargaMax < 0 || $hargaMin > $hargaMax) $errors[] = 'Kisaran harga tidak valid. Pastikan harga awal tidak melebihi harga akhir.';
      if ($rating < 1 || $rating > 5) $errors[] = 'Penilaian harus antara 1 sampai 5.';
      if (strlen($deskripsi) < 20) $errors[] = 'Deskripsi minimal 20 karakter.';

      $gambar = !$errors ? save_profile_image($_FILES['gambar'] ?? [], 'kuliner_', $errors) : null;
      if (!$errors) {
        $imagePath = $gambar ?? '';
        $stmt = mysqli_prepare($conn, 'INSERT INTO user_recommendations (user_id, nama_kuliner, kategori, wilayah, lokasi, harga_min, harga_max, rating, deskripsi, gambar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'issssiidss', $userId, $namaKuliner, $kategori, $wilayah, $lokasi, $hargaMin, $hargaMax, $rating, $deskripsi, $imagePath);
        if (mysqli_stmt_execute($stmt)) {
          $success = 'Rekomendasi berhasil dikirim dan sedang menunggu pemeriksaan administrator.';
        } else {
          $errors[] = 'Rekomendasi belum berhasil dikirim. Silakan coba lagi.';
        }
      }
    } elseif ($action === 'submit_article') {
      $jenis = $_POST['jenis'] ?? '';
      $judul = trim($_POST['judul'] ?? '');
      $ringkasan = trim($_POST['ringkasan'] ?? '');
      $isi = trim($_POST['isi'] ?? '');
      if (!in_array($jenis, ['Artikel', 'Resep'], true)) $errors[] = 'Pilih jenis kiriman.';
      if (strlen($judul) < 8 || strlen($judul) > 255) $errors[] = 'Judul harus terdiri dari 8 sampai 255 karakter.';
      if (strlen($ringkasan) < 20) $errors[] = 'Ringkasan minimal 20 karakter.';
      if (strlen($isi) < 80) $errors[] = 'Isi tulisan minimal 80 karakter.';
      $articleImage = !$errors ? save_profile_image($_FILES['gambar_artikel'] ?? [], 'kiriman_', $errors, true) : null;
      if (!$errors && $articleImage) {
        $stmt = mysqli_prepare($conn, 'INSERT INTO user_articles (user_id, jenis, judul, ringkasan, isi, gambar) VALUES (?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'isssss', $userId, $jenis, $judul, $ringkasan, $isi, $articleImage);
        if (mysqli_stmt_execute($stmt)) {
          $success = 'Tulisan berhasil dikirim dan menunggu pemeriksaan administrator.';
        } else {
          $errors[] = 'Tulisan belum berhasil dikirim. Silakan coba lagi.';
        }
      }
    }
  }
}

$stmt = mysqli_prepare($conn, 'SELECT * FROM user_recommendations WHERE user_id = ? ORDER BY dibuat_pada DESC');
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$submissions = mysqli_stmt_get_result($stmt);
$articleStmt = mysqli_prepare($conn, 'SELECT * FROM user_articles WHERE user_id = ? ORDER BY dibuat_pada DESC');
mysqli_stmt_bind_param($articleStmt, 'i', $userId);
mysqli_stmt_execute($articleStmt);
$articleSubmissions = mysqli_stmt_get_result($articleStmt);
$totalSubmissions = mysqli_num_rows($submissions) + mysqli_num_rows($articleSubmissions);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Pengguna — CitaRasaJawaTimur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="admin-page">
  <?php include 'includes/header.php'; ?>
  <main class="profile-shell">
    <div class="profile-heading">
      <div><span class="eyebrow">Profil Pengguna</span><h1>Halo, <?php echo htmlspecialchars($user['nama']); ?></h1><p>Kelola akun dan kiriman kulinermu dari satu tempat.</p></div>
      <a href="keluar.php" class="link-arrow">Keluar akun &rarr;</a>
    </div>
    <?php if ($success): ?><div class="admin-alert admin-alert--success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <?php if ($errors): ?><div class="admin-alert admin-alert--error"><ul><?php foreach ($errors as $error): ?><li><?php echo htmlspecialchars($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <nav class="profile-tabs" aria-label="Menu profil pengguna" role="tablist">
      <button class="profile-tab <?php echo $activeTab === 'account' ? 'is-active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo $activeTab === 'account' ? 'true' : 'false'; ?>" aria-controls="profile-account" data-profile-tab="account">Profil &amp; keamanan</button>
      <button class="profile-tab <?php echo $activeTab === 'recommendations' ? 'is-active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo $activeTab === 'recommendations' ? 'true' : 'false'; ?>" aria-controls="profile-recommendations" data-profile-tab="recommendations">Tambah rekomendasi</button>
      <button class="profile-tab <?php echo $activeTab === 'writing' ? 'is-active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo $activeTab === 'writing' ? 'true' : 'false'; ?>" aria-controls="profile-writing" data-profile-tab="writing">Tulis artikel / resep</button>
      <button class="profile-tab <?php echo $activeTab === 'submissions' ? 'is-active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo $activeTab === 'submissions' ? 'true' : 'false'; ?>" aria-controls="profile-submissions" data-profile-tab="submissions">Kiriman saya (<?php echo $totalSubmissions; ?>)</button>
    </nav>

    <section class="profile-tab-panel" id="profile-account" role="tabpanel" data-profile-panel="account" <?php echo $activeTab === 'account' ? '' : 'hidden'; ?>>
      <div class="profile-grid">
        <div class="admin-panel">
          <div class="admin-panel__heading"><h2>Informasi profil</h2><span>Akun pengguna</span></div>
          <form method="post" enctype="multipart/form-data" class="admin-form profile-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <input type="hidden" name="action" value="update_profile">
            <div class="profile-avatar-row">
              <?php if (!empty($user['profile_photo'])): ?>
                <img class="profile-avatar" src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Foto profil">
              <?php else: ?>
                <span class="profile-avatar profile-avatar--empty" aria-label="Belum ada foto profil"><?php echo htmlspecialchars(strtoupper(substr($user['nama'], 0, 1))); ?></span>
              <?php endif; ?>
              <label>Foto profil<input type="file" name="foto_profil" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG, atau WEBP. Maksimal 5 MB.</small></label>
            </div>
            <label>Nama lengkap<input type="text" name="nama" required maxlength="100" autocomplete="name" value="<?php echo htmlspecialchars($_POST['nama'] ?? $user['nama']); ?>"></label>
            <div class="profile-form__two-col">
              <label>Username<input type="text" name="username" required minlength="3" maxlength="50" pattern="[A-Za-z0-9._-]+" autocomplete="username" value="<?php echo htmlspecialchars($_POST['username'] ?? $user['username']); ?>"></label>
              <label>Email<input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly></label>
            </div>
            <button class="btn btn--primary" type="submit">Simpan profil</button>
          </form>
        </div>
        <div class="admin-panel">
          <div class="admin-panel__heading"><h2>Ganti kata sandi</h2><span>Keamanan akun</span></div>
          <form method="post" class="admin-form profile-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <input type="hidden" name="action" value="change_password">
            <label>Kata sandi saat ini<span class="password-field"><input id="profile-current-password" type="password" name="current_password" required autocomplete="current-password"><button type="button" class="password-toggle" data-password-toggle aria-controls="profile-current-password" aria-label="Tampilkan kata sandi" title="Tampilkan kata sandi"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg></button></span></label>
            <label>Kata sandi baru<span class="password-field"><input id="profile-new-password" type="password" name="new_password" minlength="8" required autocomplete="new-password"><button type="button" class="password-toggle" data-password-toggle aria-controls="profile-new-password" aria-label="Tampilkan kata sandi" title="Tampilkan kata sandi"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg></button></span><small>Minimal 8 karakter.</small></label>
            <label>Ulangi kata sandi baru<input type="password" name="confirm_password" minlength="8" required autocomplete="new-password"></label>
            <button class="btn btn--primary" type="submit">Ganti kata sandi</button>
          </form>
        </div>
      </div>
    </section>

    <section class="profile-tab-panel" id="profile-recommendations" role="tabpanel" data-profile-panel="recommendations" <?php echo $activeTab === 'recommendations' ? '' : 'hidden'; ?>>
      <div class="admin-panel profile-panel-narrow">
        <div class="admin-panel__heading"><h2>Tambah rekomendasi kuliner</h2><span>Kiriman diperiksa sebelum tampil publik</span></div>
        <form method="post" enctype="multipart/form-data" class="admin-form profile-form">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
          <input type="hidden" name="action" value="add_recommendation">
          <label>Nama kuliner<input type="text" name="nama_kuliner" required value="<?php echo htmlspecialchars($_POST['nama_kuliner'] ?? ''); ?>"></label>
          <div class="profile-form__two-col">
            <label>Kategori<select name="kategori" required><option value="">Pilih kategori</option><?php foreach ($kategoriValid as $kategori): ?><option value="<?php echo htmlspecialchars($kategori); ?>" <?php echo ($_POST['kategori'] ?? '') === $kategori ? 'selected' : ''; ?>><?php echo htmlspecialchars($kategori); ?></option><?php endforeach; ?></select></label>
            <label>Wilayah<select name="wilayah" required><option value="">Pilih wilayah</option><?php foreach ($wilayahValid as $wilayah): ?><option value="<?php echo htmlspecialchars($wilayah); ?>" <?php echo ($_POST['wilayah'] ?? '') === $wilayah ? 'selected' : ''; ?>><?php echo htmlspecialchars($wilayah); ?></option><?php endforeach; ?></select></label>
          </div>
          <label>Lokasi atau alamat<input type="text" name="lokasi" required placeholder="Contoh: Jl. Tunjungan, Kota Surabaya" value="<?php echo htmlspecialchars($_POST['lokasi'] ?? ''); ?>"></label>
          <div class="profile-form__two-col">
            <label>Harga mulai (Rp)<input type="number" name="harga_min" min="0" step="1000" required value="<?php echo htmlspecialchars($_POST['harga_min'] ?? ''); ?>"></label>
            <label>Harga sampai (Rp)<input type="number" name="harga_max" min="0" step="1000" required value="<?php echo htmlspecialchars($_POST['harga_max'] ?? ''); ?>"></label>
            <label>Penilaian kamu<select name="rating" required><option value="">Pilih rating</option><?php for ($score = 5; $score >= 1; $score--): ?><option value="<?php echo $score; ?>" <?php echo (float) ($_POST['rating'] ?? 0) === (float) $score ? 'selected' : ''; ?>><?php echo $score; ?> / 5</option><?php endfor; ?></select></label>
          </div>
          <label>Deskripsi dan alasan rekomendasi<textarea name="deskripsi" rows="5" required placeholder="Ceritakan rasa, porsi, suasana, atau alasan kuliner ini layak dicoba."><?php echo htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea></label>
          <label>Foto kuliner (opsional)<input type="file" name="gambar" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG, atau WEBP. Maksimal 5 MB.</small></label>
          <button class="btn btn--primary" type="submit">Kirim untuk ditinjau</button>
        </form>
      </div>
    </section>

    <section class="profile-tab-panel" id="profile-writing" role="tabpanel" data-profile-panel="writing" <?php echo $activeTab === 'writing' ? '' : 'hidden'; ?>>
      <div class="admin-panel profile-panel-narrow">
        <div class="admin-panel__heading"><h2>Tulis artikel atau resep</h2><span>Kiriman diterbitkan setelah disetujui</span></div>
        <form method="post" enctype="multipart/form-data" class="admin-form profile-form">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
          <input type="hidden" name="action" value="submit_article">
          <label>Jenis tulisan<select name="jenis" required><option value="">Pilih jenis</option><option value="Artikel" <?php echo ($_POST['jenis'] ?? '') === 'Artikel' ? 'selected' : ''; ?>>Artikel kuliner</option><option value="Resep" <?php echo ($_POST['jenis'] ?? '') === 'Resep' ? 'selected' : ''; ?>>Resep</option></select></label>
          <label>Judul tulisan<input type="text" name="judul" minlength="8" maxlength="255" required value="<?php echo htmlspecialchars($_POST['judul'] ?? ''); ?>" placeholder="Contoh: Cerita di Balik Rawon Khas Jawa Timur"></label>
          <label>Ringkasan<textarea name="ringkasan" rows="3" minlength="20" required placeholder="Ringkasan singkat yang akan tampil pada daftar artikel."><?php echo htmlspecialchars($_POST['ringkasan'] ?? ''); ?></textarea></label>
          <label>Isi artikel atau resep<textarea name="isi" rows="10" minlength="80" required placeholder="Untuk resep, tuliskan bahan dan langkah memasak dengan jelas."><?php echo htmlspecialchars($_POST['isi'] ?? ''); ?></textarea></label>
          <label>Foto artikel/resep<input type="file" name="gambar_artikel" accept="image/jpeg,image/png,image/webp" required><small>JPG, PNG, atau WEBP. Maksimal 5 MB.</small></label>
          <button class="btn btn--primary" type="submit">Kirim tulisan untuk ditinjau</button>
        </form>
      </div>
    </section>

    <section class="profile-tab-panel" id="profile-submissions" role="tabpanel" data-profile-panel="submissions" <?php echo $activeTab === 'submissions' ? '' : 'hidden'; ?>>
      <div class="profile-grid">
        <div class="admin-panel">
          <div class="admin-panel__heading"><h2>Rekomendasi saya</h2><span><?php echo mysqli_num_rows($submissions); ?> kiriman</span></div>
          <div class="submission-list">
            <?php if (mysqli_num_rows($submissions) === 0): ?><p class="profile-empty">Belum ada rekomendasi yang dikirim.</p><?php endif; ?>
            <?php while ($submission = mysqli_fetch_assoc($submissions)): ?>
              <article class="submission-item">
                <div><h3><?php echo htmlspecialchars($submission['nama_kuliner']); ?></h3><p><?php echo htmlspecialchars($submission['wilayah']); ?> · Rp <?php echo number_format((int) $submission['harga_min'], 0, ',', '.'); ?> - Rp <?php echo number_format((int) $submission['harga_max'], 0, ',', '.'); ?> · ★ <?php echo number_format((float) $submission['rating'], 1); ?>/5</p></div>
                <span class="submission-status submission-status--<?php echo strtolower($submission['status']); ?>"><?php echo htmlspecialchars($submission['status']); ?></span>
                <?php if (!empty($submission['catatan_admin'])): ?><small><?php echo htmlspecialchars($submission['catatan_admin']); ?></small><?php endif; ?>
              </article>
            <?php endwhile; ?>
          </div>
        </div>
        <div class="admin-panel">
          <div class="admin-panel__heading"><h2>Artikel dan resep saya</h2><span><?php echo mysqli_num_rows($articleSubmissions); ?> kiriman</span></div>
          <div class="submission-list">
            <?php if (mysqli_num_rows($articleSubmissions) === 0): ?><p class="profile-empty">Belum ada artikel atau resep yang dikirim.</p><?php endif; ?>
            <?php while ($articleSubmission = mysqli_fetch_assoc($articleSubmissions)): ?>
              <article class="submission-item submission-item--article">
                <?php if (!empty($articleSubmission['gambar'])): ?><img src="<?php echo htmlspecialchars($articleSubmission['gambar']); ?>" alt="<?php echo htmlspecialchars($articleSubmission['judul']); ?>"><?php endif; ?>
                <div><h3><?php echo htmlspecialchars($articleSubmission['judul']); ?></h3><p><?php echo htmlspecialchars($articleSubmission['jenis']); ?> · <?php echo date('d M Y', strtotime($articleSubmission['dibuat_pada'])); ?></p></div>
                <span class="submission-status submission-status--<?php echo strtolower($articleSubmission['status']); ?>"><?php echo htmlspecialchars($articleSubmission['status']); ?></span>
                <?php if (!empty($articleSubmission['catatan_admin'])): ?><small><?php echo htmlspecialchars($articleSubmission['catatan_admin']); ?></small><?php endif; ?>
                <?php if ($articleSubmission['status'] === 'Disetujui' && !empty($articleSubmission['slug'])): ?><a class="link-arrow link-arrow--sm" href="artikel.php?slug=<?php echo urlencode($articleSubmission['slug']); ?>">Baca tulisan &rarr;</a><?php endif; ?>
              </article>
            <?php endwhile; ?>
          </div>
        </div>
      </div>
    </section>
  </main>
  <script src="js/password-toggle.js"></script>
  <script>
    document.querySelectorAll('[data-profile-tab]').forEach((tab) => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('[data-profile-tab]').forEach((item) => {
          const active = item === tab;
          item.classList.toggle('is-active', active);
          item.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        document.querySelectorAll('[data-profile-panel]').forEach((panel) => {
          panel.hidden = panel.dataset.profilePanel !== tab.dataset.profileTab;
        });
      });
    });
  </script>
</body>
</html>
