<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include 'includes/koneksi.php';

$loginError = '';
$actionMessage = '';

if (isset($_GET['keluar'])) {
    unset($_SESSION['admin_id'], $_SESSION['admin_username']);
    header('Location: admin.php');
    exit;
}

if (!isset($_SESSION['admin_id']) && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM admin_users WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $admin = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: admin.php');
        exit;
    }
    $loginError = 'Username atau kata sandi administrator salah.';
}

if (!isset($_SESSION['admin_id'])):
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk Administrator — CitaRasaJawaTimur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css?v=20260924">
</head>
<body class="admin-page">
  <main class="admin-login">
    <a href="index.php" class="admin-login__brand">CITARASAJAWATIMUR</a>
    <?php include 'includes/theme_toggle.php'; ?>
    <div class="admin-login__panel">
      <span class="eyebrow">Portal Administrator</span>
      <h1>Kelola pengguna dengan aman</h1>
      <p>Masuk untuk mengatur akun terdaftar dan data pengguna CitaRasaJawaTimur.</p>
      <?php if ($loginError): ?><div class="admin-alert admin-alert--error"><?php echo htmlspecialchars($loginError); ?></div><?php endif; ?>
      <form method="post" class="admin-form">
        <input type="hidden" name="action" value="login">
        <label>Username<input type="text" name="username" required autocomplete="username"></label>
        <label>Kata sandi<span class="password-field"><input id="admin-login-password" type="password" name="password" required autocomplete="current-password"><button type="button" class="password-toggle" data-password-toggle aria-controls="admin-login-password" aria-label="Tampilkan kata sandi" title="Tampilkan kata sandi"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg></button></span></label>
        <button class="btn btn--primary" type="submit">Masuk ke Portal</button>
      </form>
      <a class="admin-login__back" href="index.php">&larr; Kembali ke website</a>
    </div>
  </main>
  <script src="js/theme-toggle.js"></script>
  <script src="js/password-toggle.js"></script>
</body>
</html>
<?php
exit;
endif;

$validWilayah = [];
$wilayahResult = mysqli_query($conn, "SELECT nama FROM daerah ORDER BY jenis ASC, nama ASC");
while ($wilayahRow = mysqli_fetch_assoc($wilayahResult)) {
  $validWilayah[] = $wilayahRow['nama'];
}
$editableContents = [
  ['page' => 'home', 'section' => 'hero', 'name' => 'Beranda - Hero'],
  ['page' => 'home', 'section' => 'latest', 'name' => 'Beranda - Informasi Terbaru'],
  ['page' => 'about', 'section' => 'banner', 'name' => 'Tentang Kami'],
  ['page' => 'catalog', 'section' => 'banner', 'name' => 'Katalog Kuliner'],
  ['page' => 'recommendation', 'section' => 'banner', 'name' => 'Rekomendasi Kuliner'],
  ['page' => 'map', 'section' => 'banner', 'name' => 'Peta Kuliner']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add_user') {
        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($nama !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) >= 6) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $nama, $email, $hashedPassword);
            if (mysqli_stmt_execute($stmt)) {
                $actionMessage = 'Akun pengguna berhasil ditambahkan.';
            } else {
                $actionMessage = mysqli_errno($conn) === 1062 ? 'Email tersebut sudah terdaftar.' : 'Akun belum dapat ditambahkan.';
            }
        } else {
            $actionMessage = 'Isi nama, email valid, dan kata sandi minimal 6 karakter.';
        }
    }
      if ($action === 'save_site_content') {
        $pageKey = $_POST['page_key'] ?? '';
        $sectionKey = $_POST['section_key'] ?? '';
        $isEditable = false;
        foreach ($editableContents as $editableContent) {
          if ($editableContent['page'] === $pageKey && $editableContent['section'] === $sectionKey) {
            $isEditable = true;
            break;
          }
        }

        if (!$isEditable) {
          $actionMessage = 'Bagian konten yang dipilih tidak tersedia.';
        } elseif (trim($_POST['title'] ?? '') === '' || trim($_POST['description'] ?? '') === '') {
          $actionMessage = 'Judul dan deskripsi konten wajib diisi.';
        } elseif (save_site_content($conn, $pageKey, $sectionKey, $_POST, (int) $_SESSION['admin_id'])) {
          $actionMessage = 'Konten halaman berhasil diperbarui.';
        } else {
          $actionMessage = 'Konten halaman belum berhasil diperbarui.';
        }
      }
      if ($action === 'save_site_article') {
        $articleId = (int) ($_POST['article_id'] ?? 0);
        $articleTitle = trim($_POST['title'] ?? '');
        $articleExcerpt = trim($_POST['excerpt'] ?? '');
        $articleBody = trim($_POST['body'] ?? '');
        $articleImage = trim($_POST['image_url'] ?? '');
        $articleSort = (int) ($_POST['sort_order'] ?? 0);
        if ($articleId < 1 || $articleTitle === '' || $articleExcerpt === '' || $articleBody === '' || !filter_var($articleImage, FILTER_VALIDATE_URL)) {
          $actionMessage = 'Judul, ringkasan, narasi, dan URL gambar artikel wajib diisi dengan benar.';
        } elseif (save_site_article($conn, ['id' => $articleId, 'title' => $articleTitle, 'excerpt' => $articleExcerpt, 'body' => $articleBody, 'image_url' => $articleImage, 'sort_order' => $articleSort], (int) $_SESSION['admin_id'])) {
          $actionMessage = 'Artikel berhasil diperbarui.';
        } else {
          $actionMessage = 'Artikel belum berhasil diperbarui.';
        }
      }
      if ($action === 'save_kuliner') {
        $kulinerId = (int) ($_POST['kuliner_id'] ?? 0);
        $namaKuliner = trim($_POST['nama_makanan'] ?? '');
        $kategori = $_POST['kategori'] ?? '';
        $wilayah = $_POST['wilayah'] ?? '';
        $lokasi = trim($_POST['lokasi'] ?? '');
        $hargaMin = (int) ($_POST['harga_min'] ?? 0);
        $hargaMax = (int) ($_POST['harga_max'] ?? 0);
        $rating = (float) ($_POST['rating'] ?? 0);
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $sejarah = trim($_POST['sejarah'] ?? '');
        $resepBahan = trim($_POST['resep_bahan'] ?? '');
        $resepLangkah = trim($_POST['resep_langkah'] ?? '');
        $gambar = trim($_POST['gambar'] ?? '');
        $publikasi = $_POST['publikasi'] ?? [];
        $validKategori = ['Makanan Berat', 'Makanan Ringan', 'Makanan Berkuah', 'Minuman', 'Jajanan'];
        if (!is_array($publikasi)) {
          $publikasi = [$publikasi];
        }
        $publikasi = array_values(array_unique(array_filter(array_map('trim', $publikasi), fn($item) => $item !== '')));
        if ($publikasi === []) {
          $publikasi = ['Katalog Kuliner'];
        }
        if ($kulinerId < 1 || $namaKuliner === '' || !in_array($kategori, $validKategori, true) || !in_array($wilayah, $validWilayah, true) || $lokasi === '' || $hargaMin < 0 || $hargaMax < $hargaMin || $rating < 1 || $rating > 5 || $deskripsi === '' || $gambar === '') {
          $actionMessage = 'Lengkapi informasi kuliner dengan data yang valid.';
        } else {
          $daerahStmt = mysqli_prepare($conn, "SELECT id FROM daerah WHERE nama = ? LIMIT 1");
          mysqli_stmt_bind_param($daerahStmt, "s", $wilayah);
          mysqli_stmt_execute($daerahStmt);
          $daerah = mysqli_stmt_get_result($daerahStmt)->fetch_assoc();
          $daerahId = $daerah ? (int) $daerah['id'] : 0;
          $harga = (int) round(($hargaMin + $hargaMax) / 2);
          $isRekomendasi = in_array('Informasi Terbaru', $publikasi, true) || in_array('Rekomendasi', $publikasi, true) ? 1 : 0;
          $publikasiString = implode(', ', $publikasi);
          if ($daerahId === 0) {
            $actionMessage = 'Daerah yang dipilih tidak ditemukan.';
          } else {
            mysqli_begin_transaction($conn);
            $stmt = mysqli_prepare($conn, "UPDATE kuliner SET daerah_id = ?, wilayah = ?, harga = ?, harga_min = ?, harga_max = ?, rating = ?, kategori = ?, deskripsi = ?, sejarah = ?, resep_bahan = ?, resep_langkah = ?, gambar = ?, is_rekomendasi = ?, publikasi = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "isiiidssssssisi", $daerahId, $wilayah, $harga, $hargaMin, $hargaMax, $rating, $kategori, $deskripsi, $sejarah, $resepBahan, $resepLangkah, $gambar, $isRekomendasi, $publikasiString, $kulinerId);
            $updated = mysqli_stmt_execute($stmt);
            $placeStmt = mysqli_prepare($conn, "SELECT id FROM kuliner_tempat WHERE kuliner_id = ? ORDER BY id ASC LIMIT 1");
            mysqli_stmt_bind_param($placeStmt, "i", $kulinerId);
            mysqli_stmt_execute($placeStmt);
            $place = mysqli_stmt_get_result($placeStmt)->fetch_assoc();
            $placeName = $namaKuliner;
            $placeNote = 'Tempat rekomendasi yang dikelola oleh administrator.';
            if ($place) {
              $updatePlace = mysqli_prepare($conn, "UPDATE kuliner_tempat SET nama_tempat = ?, alamat = ? WHERE id = ?");
              mysqli_stmt_bind_param($updatePlace, "ssi", $placeName, $lokasi, $place['id']);
              $updated = $updated && mysqli_stmt_execute($updatePlace);
            } else {
              $insertPlace = mysqli_prepare($conn, "INSERT INTO kuliner_tempat (kuliner_id, nama_tempat, alamat, catatan) VALUES (?, ?, ?, ?)");
              mysqli_stmt_bind_param($insertPlace, "isss", $kulinerId, $placeName, $lokasi, $placeNote);
              $updated = $updated && mysqli_stmt_execute($insertPlace);
            }
            if ($updated) {
              mysqli_commit($conn);
              $actionMessage = 'Informasi kuliner berhasil diperbarui.';
            } else {
              mysqli_rollback($conn);
              $actionMessage = 'Informasi kuliner belum berhasil diperbarui.';
            }
          }
        }
      }
    if ($action === 'add_manual_kuliner') {
        $namaKuliner = trim($_POST['nama_makanan'] ?? '');
        $kategori = $_POST['kategori'] ?? '';
        $wilayah = $_POST['wilayah'] ?? '';
        $lokasi = trim($_POST['lokasi'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $sejarah = trim($_POST['sejarah'] ?? '');
        $resepBahan = trim($_POST['resep_bahan'] ?? '');
        $resepLangkah = trim($_POST['resep_langkah'] ?? '');
        $hargaMin = (int) ($_POST['harga_min'] ?? 0);
        $hargaMax = (int) ($_POST['harga_max'] ?? 0);
        $harga = (int) round(($hargaMin + $hargaMax) / 2);
        $rating = (float) ($_POST['rating'] ?? 0);
        $publikasi = $_POST['publikasi'] ?? [];
        $gambarPath = '';

        $validKategori = ['Makanan Berat', 'Makanan Ringan', 'Makanan Berkuah', 'Minuman', 'Jajanan'];
        if (!is_array($publikasi)) {
            $publikasi = [$publikasi];
        }
        $publikasi = array_values(array_unique(array_filter(array_map('trim', $publikasi), fn($item) => $item !== '')));
        if ($publikasi === []) {
            $publikasi = ['Katalog Kuliner'];
        }

        if ($namaKuliner === '' || !in_array($kategori, $validKategori, true) || !in_array($wilayah, $validWilayah, true) || $lokasi === '' || $deskripsi === '' || $hargaMin < 0 || $hargaMax < $hargaMin || $rating < 1 || $rating > 5) {
          $actionMessage = 'Semua data kuliner wajib diisi dengan benar. Harga mulai tidak boleh melebihi harga sampai.';
        } elseif (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadedImage = $_FILES['gambar'];
            $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $mime = $uploadedImage['tmp_name'] && is_uploaded_file($uploadedImage['tmp_name']) ? (getimagesize($uploadedImage['tmp_name'])['mime'] ?? '') : '';

            if ($uploadedImage['error'] !== UPLOAD_ERR_OK || !isset($allowedTypes[$mime])) {
                $actionMessage = 'Unggah gambar valid dengan format JPG, PNG, atau WEBP.';
            } elseif ($uploadedImage['size'] > 5 * 1024 * 1024) {
                $actionMessage = 'Ukuran gambar maksimal 5 MB.';
            } else {
                $fileName = 'kuliner_' . bin2hex(random_bytes(8)) . '.' . $allowedTypes[$mime];
                $uploadPath = __DIR__ . '/img/user_uploads/' . $fileName;
                if (move_uploaded_file($uploadedImage['tmp_name'], $uploadPath)) {
                    $gambarPath = 'img/user_uploads/' . $fileName;
                } else {
                    $actionMessage = 'Gambar gagal disimpan. Silakan coba lagi.';
                }
            }
        } else {
            $actionMessage = 'Gambar kuliner wajib diunggah.';
        }

        if ($gambarPath !== '' && $actionMessage === '') {
            $daerahStmt = mysqli_prepare($conn, "SELECT id FROM daerah WHERE nama = ? LIMIT 1");
            mysqli_stmt_bind_param($daerahStmt, "s", $wilayah);
            mysqli_stmt_execute($daerahStmt);
            $daerah = mysqli_stmt_get_result($daerahStmt)->fetch_assoc();
            $daerahId = $daerah ? (int) $daerah['id'] : 0;

            if ($daerahId === 0) {
              $actionMessage = 'Daerah yang dipilih tidak ditemukan.';
            }

            $baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $namaKuliner), '-'));
            $slug = $baseSlug !== '' ? $baseSlug : 'kuliner-' . time();
            $slugCounter = 2;
            while (mysqli_num_rows(mysqli_query($conn, "SELECT 1 FROM kuliner WHERE slug = '" . mysqli_real_escape_string($conn, $slug) . "' LIMIT 1")) > 0) {
                $slug = $baseSlug !== '' ? $baseSlug . '-' . $slugCounter : 'kuliner-' . time() . '-' . $slugCounter;
                $slugCounter++;
            }

            $isRekomendasi = in_array('Informasi Terbaru', $publikasi, true) || in_array('Rekomendasi', $publikasi, true) ? 1 : 0;
            $publikasiString = implode(', ', $publikasi);
            $sejarah = $sejarah !== '' ? $sejarah : 'Kuliner ini menjadi bagian dari warisan rasa Jawa Timur yang terus dilestarikan oleh para pelaku UMKM lokal.';
            $resepBahan = $resepBahan !== '' ? $resepBahan : 'Bahan utama sesuai resep khas warung lokal';
            $resepLangkah = $resepLangkah !== '' ? $resepLangkah : 'Masak dengan teknik tradisional dan sajikan dengan rasa yang khas untuk setiap porsi.';

            if ($daerahId !== 0) {
              mysqli_begin_transaction($conn);
              $stmt = mysqli_prepare($conn, "INSERT INTO kuliner (daerah_id, nama_makanan, slug, wilayah, harga, harga_min, harga_max, rating, jumlah_penilai, kategori, deskripsi, sejarah, resep_bahan, resep_langkah, gambar, is_rekomendasi, publikasi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
              $jumlahPenilai = 0;
              mysqli_stmt_bind_param($stmt, "isssiiidissssssis", $daerahId, $namaKuliner, $slug, $wilayah, $harga, $hargaMin, $hargaMax, $rating, $jumlahPenilai, $kategori, $deskripsi, $sejarah, $resepBahan, $resepLangkah, $gambarPath, $isRekomendasi, $publikasiString);

              if (mysqli_stmt_execute($stmt)) {
                $kulinerId = mysqli_insert_id($conn);
                $tempatStmt = mysqli_prepare($conn, "INSERT INTO kuliner_tempat (kuliner_id, nama_tempat, alamat, catatan) VALUES (?, ?, ?, ?)");
                $catatanTempat = 'Tempat rekomendasi yang ditambahkan oleh administrator.';
                mysqli_stmt_bind_param($tempatStmt, "isss", $kulinerId, $namaKuliner, $lokasi, $catatanTempat);

                if (mysqli_stmt_execute($tempatStmt)) {
                  mysqli_commit($conn);
                  $actionMessage = 'Kuliner berhasil ditambahkan ke katalog dan tempat rekomendasinya.';
                } else {
                  mysqli_rollback($conn);
                  $actionMessage = 'Data kuliner belum berhasil disimpan. Silakan coba lagi.';
                }
              } else {
                mysqli_rollback($conn);
                $actionMessage = 'Kuliner belum berhasil ditambahkan. Silakan coba lagi.';
              }
            }
        }
    }
    if ($action === 'toggle_status') {
        $userId = (int) ($_POST['user_id'] ?? 0);
        $stmt = mysqli_prepare($conn, "UPDATE users SET status = IF(status = 'Aktif', 'Nonaktif', 'Aktif') WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $actionMessage = 'Status akun berhasil diperbarui.';
    }
    if ($action === 'delete_user') {
        $userId = (int) ($_POST['user_id'] ?? 0);
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $actionMessage = 'Akun pengguna berhasil dihapus.';
    }
      if ($action === 'review_recommendation') {
        $recommendationId = (int) ($_POST['recommendation_id'] ?? 0);
        $reviewStatus = $_POST['review_status'] ?? '';
        $catatanAdmin = trim($_POST['catatan_admin'] ?? '');
        if (in_array($reviewStatus, ['Disetujui', 'Ditolak'], true)) {
          $stmt = mysqli_prepare($conn, "UPDATE user_recommendations SET status = ?, catatan_admin = ? WHERE id = ?");
          mysqli_stmt_bind_param($stmt, "ssi", $reviewStatus, $catatanAdmin, $recommendationId);
          mysqli_stmt_execute($stmt);
          $actionMessage = $reviewStatus === 'Disetujui' ? 'Rekomendasi disetujui dan akan tampil di menu rekomendasi.' : 'Rekomendasi ditolak.';
        }
      }
      if ($action === 'review_user_article') {
        $submissionId = (int) ($_POST['submission_id'] ?? 0);
        $reviewStatus = $_POST['review_status'] ?? '';
        $adminNote = trim($_POST['catatan_admin'] ?? '');
        if (in_array($reviewStatus, ['Disetujui', 'Ditolak'], true)) {
          $submissionStmt = mysqli_prepare($conn, "SELECT * FROM user_articles WHERE id = ? AND status = 'Menunggu' LIMIT 1");
          mysqli_stmt_bind_param($submissionStmt, 'i', $submissionId);
          mysqli_stmt_execute($submissionStmt);
          $submission = mysqli_stmt_get_result($submissionStmt)->fetch_assoc();
          if ($submission) {
            mysqli_begin_transaction($conn);
            $reviewed = true;
            $articleSlug = null;
            if ($reviewStatus === 'Disetujui') {
              $slugBase = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $submission['judul']), '-'));
              $articleSlug = substr($slugBase !== '' ? $slugBase : 'tulisan-kuliner', 0, 130) . '-komunitas-' . $submissionId;
              $articleCategory = $submission['jenis'] === 'Resep' ? 'autentik' : 'unik';
              $sortOrder = -$submissionId;
              $adminId = (int) $_SESSION['admin_id'];
              $publishStmt = mysqli_prepare($conn, 'INSERT INTO site_articles (slug, category, title, excerpt, body, image_url, sort_order, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
              mysqli_stmt_bind_param($publishStmt, 'ssssssii', $articleSlug, $articleCategory, $submission['judul'], $submission['ringkasan'], $submission['isi'], $submission['gambar'], $sortOrder, $adminId);
              $reviewed = mysqli_stmt_execute($publishStmt);
            }
            if ($reviewed) {
              $updateSubmission = mysqli_prepare($conn, 'UPDATE user_articles SET status = ?, catatan_admin = ?, slug = ? WHERE id = ?');
              mysqli_stmt_bind_param($updateSubmission, 'sssi', $reviewStatus, $adminNote, $articleSlug, $submissionId);
              $reviewed = mysqli_stmt_execute($updateSubmission);
            }
            if ($reviewed) {
              mysqli_commit($conn);
              $actionMessage = $reviewStatus === 'Disetujui' ? 'Tulisan disetujui dan diterbitkan pada informasi kuliner.' : 'Tulisan ditolak dan catatan disimpan.';
            } else {
              mysqli_rollback($conn);
              $actionMessage = 'Status tulisan belum berhasil diperbarui.';
            }
          }
        }
      }
}

$totalUsers = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
$activeUsers = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE status = 'Aktif'"))['total'];
$inactiveUsers = $totalUsers - $activeUsers;
$pendingRecommendations = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user_recommendations WHERE status = 'Menunggu'"))['total'];
$pendingArticleSubmissions = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM user_articles WHERE status = 'Menunggu'"))['total'];
$pendingModeration = $pendingRecommendations + $pendingArticleSubmissions;
$users = mysqli_query($conn, "SELECT id, nama, email, status, dibuat_pada FROM users ORDER BY dibuat_pada DESC LIMIT 5");
$recommendations = mysqli_query($conn, "SELECT r.*, u.nama AS nama_pengguna, u.email FROM user_recommendations r JOIN users u ON u.id = r.user_id WHERE r.status = 'Menunggu' ORDER BY r.dibuat_pada ASC");
$articleSubmissions = mysqli_query($conn, "SELECT a.*, u.nama AS nama_pengguna, u.email FROM user_articles a JOIN users u ON u.id = a.user_id WHERE a.status = 'Menunggu' ORDER BY a.dibuat_pada ASC");
$contentValues = [];
foreach ($editableContents as $editableContent) {
  $contentKey = $editableContent['page'] . '.' . $editableContent['section'];
  $contentValues[$contentKey] = get_site_content($conn, $editableContent['page'], $editableContent['section']);
}
$editableArticles = array_merge(get_site_articles($conn, 'unik'), get_site_articles($conn, 'autentik'));
$editKulinerId = (int) ($_GET['edit_kuliner'] ?? 0);
$editableKuliner = mysqli_query($conn, "
  SELECT k.*, d.nama AS nama_daerah, MIN(kt.nama_tempat) AS nama_tempat, MIN(kt.alamat) AS alamat
  FROM kuliner k
  JOIN daerah d ON d.id = k.daerah_id
  LEFT JOIN kuliner_tempat kt ON kt.kuliner_id = k.id
  GROUP BY k.id
  ORDER BY d.nama ASC, k.nama_makanan ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Administrator — CitaRasaJawaTimur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css?v=20260924">
</head>
<body class="admin-page">
  <header class="admin-console-header">
    <a href="index.php" class="admin-console-brand">
      <span class="admin-console-brand__mark" aria-hidden="true">CR</span>
      <span><strong>CitaRasaJawaTimur</strong><small>ADMIN PANEL</small></span>
    </a>
    <div class="admin-console-actions">
      <a href="index.php" class="admin-console-link">Lihat Website</a>
      <?php include 'includes/theme_toggle.php'; ?>
      <span class="admin-console-user">Admin: <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
      <a href="admin.php?keluar=1" class="admin-console-logout">Keluar</a>
    </div>
  </header>
  <main class="admin-shell">
    <div class="admin-heading">
      <div><span class="eyebrow">Dashboard Administrator</span><h1>Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h1><p>Informasi akun pengguna dan pengajuan yang menunggu tinjauan.</p></div>
    </div>
    <?php if ($actionMessage): ?><div class="admin-alert admin-alert--success"><?php echo htmlspecialchars($actionMessage); ?></div><?php endif; ?>
    <section class="admin-panel admin-panel--wide admin-workspace">
      <div class="admin-panel__heading"><h2>Admin Panel</h2><span>Kelola isi website dan data platform</span></div>
      <div class="admin-dashboard-layout">
        <nav class="admin-sidebar" aria-label="Menu administrasi">
        <?php
        $menuLabels = [
            'home' => 'Beranda',
            'about' => 'Tentang Kami',
            'catalog' => 'Katalog Kuliner',
            'recommendation' => 'Rekomendasi Kuliner',
            'map' => 'Peta Kuliner'
        ];
        ?>
          <div class="admin-sidebar__group">
            <span class="admin-sidebar__heading">Utama</span>
            <button class="admin-sidebar__link is-active" type="button" data-admin-menu="overview" aria-controls="admin-panel-overview" aria-selected="true">
              <span class="admin-sidebar__icon" aria-hidden="true">D</span>
              <span class="admin-sidebar__label"><strong>Dashboard</strong><small>Ringkasan aktivitas</small></span>
              <span class="admin-sidebar__arrow" aria-hidden="true">&rarr;</span>
            </button>
          </div>
          <div class="admin-sidebar__group">
            <span class="admin-sidebar__heading">Konten Website</span>
            <?php foreach ($menuLabels as $menuKey => $menuLabel): ?>
              <button class="admin-sidebar__link" type="button" data-admin-menu="<?php echo htmlspecialchars($menuKey); ?>" aria-controls="admin-panel-<?php echo htmlspecialchars($menuKey); ?>" aria-selected="false">
                <span class="admin-sidebar__icon" aria-hidden="true"><?php echo strtoupper(substr($menuLabel, 0, 1)); ?></span>
                <span class="admin-sidebar__label"><strong><?php echo htmlspecialchars($menuLabel); ?></strong><small>Kelola konten</small></span>
                <span class="admin-sidebar__arrow" aria-hidden="true">&rarr;</span>
              </button>
            <?php endforeach; ?>
          </div>
        </nav>
        <div class="admin-menu-list">
        <section class="admin-menu admin-menu-panel admin-overview-panel" id="admin-panel-overview" data-admin-panel="overview">
          <div class="admin-menu__body">
            <div class="admin-overview-intro"><span class="eyebrow">Ringkasan hari ini</span><h2>Akun pengguna &amp; pengajuan masuk</h2><p>Pantau status akun dan tinjau rekomendasi, artikel, serta resep dari pengguna.</p></div>
            <div class="admin-overview-layout">
              <div class="admin-overview-account">
            <div class="admin-stats">
              <div class="admin-stat"><span>Total akun</span><strong><?php echo $totalUsers; ?></strong></div>
              <div class="admin-stat"><span>Akun aktif</span><strong><?php echo $activeUsers; ?></strong></div>
              <div class="admin-stat"><span>Akun nonaktif</span><strong><?php echo $inactiveUsers; ?></strong></div>
              <div class="admin-stat"><span>Menunggu moderasi</span><strong><?php echo $pendingModeration; ?></strong></div>
            </div>
            <section class="admin-overview-users">
              <div class="admin-overview-review__heading">
                <div><span class="eyebrow">Akun terbaru</span><h3>Informasi Akun Pengguna</h3></div>
                <span>5 akun terakhir</span>
              </div>
              <div class="admin-table-wrap">
                <table class="admin-table">
                  <thead><tr><th>Nama</th><th>Email</th><th>Status</th><th>Terdaftar</th></tr></thead>
                  <tbody>
                    <?php if (mysqli_num_rows($users) === 0): ?><tr><td class="admin-empty" colspan="4">Belum ada akun pengguna.</td></tr><?php endif; ?>
                    <?php while ($user = mysqli_fetch_assoc($users)): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($user['nama']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><span class="admin-status admin-status--<?php echo strtolower($user['status']) === 'aktif' ? 'aktif' : 'nonaktif'; ?>"><?php echo htmlspecialchars($user['status']); ?></span></td>
                        <td><?php echo date('d/m/Y', strtotime($user['dibuat_pada'])); ?></td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </section>
              </div>
            <?php include 'includes/admin_reviews.php'; ?>
            </div>
          </div>
        </section>
        <?php foreach ($menuLabels as $menuKey => $menuLabel): ?>
          <section class="admin-menu admin-menu-panel" id="admin-panel-<?php echo htmlspecialchars($menuKey); ?>" data-admin-panel="<?php echo htmlspecialchars($menuKey); ?>" hidden>
            <div class="admin-menu__body">
              <div class="admin-content-editor">
                <?php foreach ($editableContents as $editableContent):
                    if ($editableContent['page'] !== $menuKey) {
                        continue;
                    }
                    $contentKey = $editableContent['page'] . '.' . $editableContent['section'];
                    $content = $contentValues[$contentKey];
                ?>
                  <form method="post" class="admin-content-editor__item">
                    <input type="hidden" name="action" value="save_site_content">
                    <input type="hidden" name="page_key" value="<?php echo htmlspecialchars($editableContent['page']); ?>">
                    <input type="hidden" name="section_key" value="<?php echo htmlspecialchars($editableContent['section']); ?>">
                    <div class="admin-panel__heading"><h3><?php echo htmlspecialchars($editableContent['name']); ?></h3><span>Konten publik</span></div>
                    <label>Label atau eyebrow<input type="text" name="label" value="<?php echo htmlspecialchars($content['label'] ?? ''); ?>"></label>
                    <label>Judul<textarea name="title" rows="2" required><?php echo htmlspecialchars($content['title'] ?? ''); ?></textarea></label>
                    <label>Deskripsi<textarea name="description" rows="3" required><?php echo htmlspecialchars($content['description'] ?? ''); ?></textarea></label>
                    <?php if ($editableContent['page'] === 'home' && $editableContent['section'] === 'hero'): ?>
                      <label>Placeholder pencarian<input type="text" name="placeholder" value="<?php echo htmlspecialchars($content['placeholder'] ?? ''); ?>"></label>
                      <label>Teks tombol pencarian<input type="text" name="button_label" value="<?php echo htmlspecialchars($content['button_label'] ?? ''); ?>"></label>
                      <small class="admin-form__hint">Gunakan tanda <code>|</code> pada judul untuk membuat baris baru.</small>
                    <?php endif; ?>
                    <button class="btn btn--primary" type="submit">Simpan Konten</button>
                  </form>
                <?php endforeach; ?>
              </div>
              <?php if ($menuKey === 'catalog'): ?>
                <div class="admin-culinary-editor">
                  <div class="admin-menu__subheading"><div><h3>Daftar Kuliner</h3><p class="admin-form__hint">Pilih satu kartu untuk membuka dan mengedit informasi lengkapnya.</p></div><span><?php echo mysqli_num_rows($editableKuliner); ?> kuliner</span></div>
                  <?php while ($editableItem = mysqli_fetch_assoc($editableKuliner)): ?>
                    <?php $publishedPlaces = array_map('trim', explode(',', $editableItem['publikasi'] ?? '')); ?>
                    <details class="admin-culinary-card" id="kuliner-editor-<?php echo (int) $editableItem['id']; ?>">
                      <summary>
                        <span class="admin-culinary-card__title"><strong><?php echo htmlspecialchars($editableItem['nama_makanan']); ?></strong><small><?php echo htmlspecialchars($editableItem['nama_daerah']); ?> &middot; <?php echo htmlspecialchars($editableItem['kategori']); ?></small></span>
                        <span class="admin-culinary-card__action">Edit informasi <span aria-hidden="true">&darr;</span></span>
                      </summary>
                      <form method="post" class="admin-form admin-culinary-card__form">
                        <input type="hidden" name="action" value="save_kuliner">
                        <input type="hidden" name="kuliner_id" value="<?php echo (int) $editableItem['id']; ?>">
                        <div class="profile-form__two-col">
                          <label>Nama kuliner<input type="text" name="nama_makanan" value="<?php echo htmlspecialchars($editableItem['nama_makanan']); ?>" required></label>
                          <label>Kategori<select name="kategori" required><?php foreach (['Makanan Berat', 'Makanan Ringan', 'Makanan Berkuah', 'Minuman', 'Jajanan'] as $kategori): ?><option value="<?php echo htmlspecialchars($kategori); ?>" <?php echo $editableItem['kategori'] === $kategori ? 'selected' : ''; ?>><?php echo htmlspecialchars($kategori); ?></option><?php endforeach; ?></select></label>
                        </div>
                        <div class="profile-form__two-col">
                          <label>Daerah<select name="wilayah" required><?php foreach ($validWilayah as $wilayah): ?><option value="<?php echo htmlspecialchars($wilayah); ?>" <?php echo $editableItem['nama_daerah'] === $wilayah ? 'selected' : ''; ?>><?php echo htmlspecialchars($wilayah); ?></option><?php endforeach; ?></select></label>
                          <label>Lokasi atau alamat<input type="text" name="lokasi" value="<?php echo htmlspecialchars($editableItem['alamat'] ?? ''); ?>" required></label>
                        </div>
                        <div class="profile-form__two-col">
                          <label>Harga mulai (Rp)<input type="number" name="harga_min" min="0" step="1000" value="<?php echo (int) $editableItem['harga_min']; ?>" required></label>
                          <label>Harga sampai (Rp)<input type="number" name="harga_max" min="0" step="1000" value="<?php echo (int) $editableItem['harga_max']; ?>" required></label>
                        </div>
                        <label>Rating<input type="number" name="rating" min="1" max="5" step="0.1" value="<?php echo htmlspecialchars($editableItem['rating']); ?>" required></label>
                        <label>Deskripsi kuliner<textarea name="deskripsi" rows="4" required><?php echo htmlspecialchars($editableItem['deskripsi']); ?></textarea></label>
                        <label>Sejarah singkat<textarea name="sejarah" rows="3"><?php echo htmlspecialchars($editableItem['sejarah']); ?></textarea></label>
                        <label>Bahan utama<textarea name="resep_bahan" rows="3"><?php echo htmlspecialchars($editableItem['resep_bahan']); ?></textarea></label>
                        <label>Langkah pembuatan<textarea name="resep_langkah" rows="4"><?php echo htmlspecialchars($editableItem['resep_langkah']); ?></textarea></label>
                        <label>URL atau path gambar<input type="text" name="gambar" value="<?php echo htmlspecialchars($editableItem['gambar']); ?>" required></label>
                        <fieldset class="checkbox-group">
                          <legend>Lokasi publikasi</legend>
                          <?php foreach (['Katalog Kuliner', 'Informasi Terbaru', 'Rekomendasi', 'Lainnya'] as $publication): ?><label><input type="checkbox" name="publikasi[]" value="<?php echo htmlspecialchars($publication); ?>" <?php echo in_array($publication, $publishedPlaces, true) ? 'checked' : ''; ?>> <?php echo htmlspecialchars($publication); ?></label><?php endforeach; ?>
                        </fieldset>
                        <button class="btn btn--primary" type="submit">Simpan Perubahan</button>
                      </form>
                    </details>
                  <?php endwhile; ?>
                </div>
              <?php endif; ?>
              <?php if ($menuKey === 'home'): ?>
                <div class="admin-article-editor">
                  <div class="admin-menu__subheading"><h3>Artikel Makanan Unik &amp; Resep Kuliner Jawa Timur</h3><span><?php echo count($editableArticles); ?> artikel</span></div>
                  <?php foreach ($editableArticles as $editableArticle): ?>
                    <form method="post" class="admin-article-editor__item">
                      <input type="hidden" name="action" value="save_site_article">
                      <input type="hidden" name="article_id" value="<?php echo (int) $editableArticle['id']; ?>">
                      <strong><?php echo htmlspecialchars($editableArticle['category'] === 'unik' ? 'Makanan Unik' : 'Resep Autentik'); ?></strong>
                      <label>Judul artikel<input type="text" name="title" value="<?php echo htmlspecialchars($editableArticle['title']); ?>" required></label>
                      <label>Ringkasan<textarea name="excerpt" rows="2" required><?php echo htmlspecialchars($editableArticle['excerpt']); ?></textarea></label>
                      <label>Narasi lengkap<textarea name="body" rows="6" required><?php echo htmlspecialchars($editableArticle['body']); ?></textarea></label>
                      <label>URL gambar<input type="url" name="image_url" value="<?php echo htmlspecialchars($editableArticle['image_url']); ?>" placeholder="https://contoh.com/gambar.jpg" required><small class="admin-form__hint">Gunakan alamat gambar langsung yang berakhiran JPG, PNG, atau WEBP.</small></label>
                      <label>Urutan tampil<input type="number" name="sort_order" min="1" value="<?php echo (int) $editableArticle['sort_order']; ?>" required></label>
                      <button class="btn btn--primary" type="submit">Simpan Artikel</button>
                    </form>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </section>
        <?php endforeach; ?>
        </div>
      </div>
    </section>
    <script>
      const adminSidebarButtons = document.querySelectorAll('.admin-sidebar [data-admin-menu]');
      const showAdminPanel = (target, activeButton = null) => {
        adminSidebarButtons.forEach((button) => {
          const isActive = activeButton ? button === activeButton : button.dataset.adminMenu === target;
          button.classList.toggle('is-active', isActive);
          button.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
        document.querySelectorAll('[data-admin-panel]').forEach((panel) => {
          panel.hidden = panel.dataset.adminPanel !== target;
        });
      };

      adminSidebarButtons.forEach((menuButton) => {
        menuButton.addEventListener('click', () => showAdminPanel(menuButton.dataset.adminMenu, menuButton));
      });
      document.querySelectorAll('.admin-quick-link[data-admin-menu]').forEach((shortcut) => {
        shortcut.addEventListener('click', () => showAdminPanel(shortcut.dataset.adminMenu));
      });
      const requestedKulinerId = <?php echo $editKulinerId; ?>;
      if (requestedKulinerId > 0) {
        showAdminPanel('catalog');
        const requestedCard = document.getElementById(`kuliner-editor-${requestedKulinerId}`);
        if (requestedCard) {
          requestedCard.open = true;
          requestedCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    </script>
  </main>
  <?php include 'includes/footer.php'; ?>
  <script src="js/theme-toggle.js"></script>
</body>
</html>
