<?php
$active_page = 'katalog';
include 'includes/koneksi.php';

$slug = $_GET['slug'] ?? '';

$stmt = mysqli_prepare($conn, "
    SELECT k.*, d.nama AS nama_daerah, d.slug AS slug_daerah, d.jenis AS jenis_daerah
    FROM kuliner k
    JOIN daerah d ON d.id = k.daerah_id
    WHERE k.slug = ?
");
mysqli_stmt_bind_param($stmt, "s", $slug);
mysqli_stmt_execute($stmt);
$kuliner = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$kuliner) {
    header("Location: katalog.php");
    exit;
}

// Tempat rekomendasi
$stmtTempat = mysqli_prepare($conn, "SELECT * FROM kuliner_tempat WHERE kuliner_id = ?");
mysqli_stmt_bind_param($stmtTempat, "i", $kuliner['id']);
mysqli_stmt_execute($stmtTempat);
$tempatResult = mysqli_stmt_get_result($stmtTempat);

// Kuliner lain dari daerah yang sama
$stmtLain = mysqli_prepare($conn, "SELECT * FROM kuliner WHERE daerah_id = ? AND id != ? ORDER BY RAND() LIMIT 3");
mysqli_stmt_bind_param($stmtLain, "ii", $kuliner['daerah_id'], $kuliner['id']);
mysqli_stmt_execute($stmtLain);
$lainResult = mysqli_stmt_get_result($stmtLain);

$bahanList = array_filter(explode("\n", $kuliner['resep_bahan']));
$langkahList = array_filter(array_map(
  static fn($langkah) => preg_replace('/^\s*\d+[.)]\s*/', '', trim($langkah)),
  explode("\n", $kuliner['resep_langkah'])
));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($kuliner['nama_makanan']); ?> — CitaRasaJawaTimur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <!-- BREADCRUMB -->
  <div class="breadcrumb">
    <a href="index.php">Beranda</a>
    <span>/</span>
    <a href="katalog.php">Katalog Kuliner</a>
    <span>/</span>
    <a href="daerah.php?slug=<?php echo urlencode($kuliner['slug_daerah']); ?>"><?php echo htmlspecialchars($kuliner['nama_daerah']); ?></a>
    <span>/</span>
    <span class="breadcrumb__current"><?php echo htmlspecialchars($kuliner['nama_makanan']); ?></span>
  </div>

  <!-- HERO KULINER -->
  <section class="kuliner-hero">
    <div class="kuliner-hero__media">
      <img src="<?php echo htmlspecialchars($kuliner['gambar']); ?>" alt="<?php echo htmlspecialchars($kuliner['nama_makanan']); ?>">
    </div>
    <div class="kuliner-hero__text">
      <span class="card__tag">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 8a6 6 0 0 0-12 0c0 4-2 6-2 6h16s-2-2-2-6"></path>
          <path d="M13.7 21a2 2 0 0 1-3.4 0"></path>
        </svg>
        Kuliner <?php echo htmlspecialchars($kuliner['nama_daerah']); ?>
      </span>
      <h1><?php echo htmlspecialchars($kuliner['nama_makanan']); ?></h1>
      <p><?php echo htmlspecialchars($kuliner['deskripsi']); ?></p>
      <div class="kuliner-hero__meta">
        <span class="chip chip--static"><?php echo htmlspecialchars($kuliner['kategori']); ?></span>
        <span class="chip chip--static"><?php echo htmlspecialchars(price_range_label($kuliner)); ?></span>
        <span class="chip chip--static">&#9733; <?php echo number_format((float) $kuliner['rating'], 1); ?> (<?php echo (int) $kuliner['jumlah_penilai']; ?> penilaian)</span>
        <?php if ($kuliner['is_rekomendasi']): ?>
          <span class="chip chip--active">Kuliner Pilihan</span>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <div class="kuliner-layout">
    <!-- KONTEN UTAMA -->
    <div class="kuliner-main">

      <!-- SEJARAH -->
      <article class="content-block">
        <h2>Sejarah &amp; Cerita di Baliknya</h2>
        <p><?php echo nl2br(htmlspecialchars($kuliner['sejarah'])); ?></p>
      </article>

      <!-- RESEP -->
      <article class="content-block">
        <h2>Resep <?php echo htmlspecialchars($kuliner['nama_makanan']); ?></h2>

        <div class="resep-grid">
          <div class="resep-bahan">
            <h3>Bahan-Bahan</h3>
            <ul class="checklist">
              <?php foreach ($bahanList as $bahan): ?>
                <li><?php echo htmlspecialchars(trim($bahan)); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="resep-langkah">
            <h3>Cara Membuat</h3>
            <ol class="steplist">
              <?php foreach ($langkahList as $langkah): ?>
                <li><?php echo htmlspecialchars(trim($langkah)); ?></li>
              <?php endforeach; ?>
            </ol>
          </div>
        </div>
      </article>

      <!-- TEMPAT REKOMENDASI -->
      <article class="content-block">
        <h2>Tempat Rekomendasi untuk Menikmati</h2>
        <div class="tempat-list">
          <?php while ($t = mysqli_fetch_assoc($tempatResult)): ?>
            <?php $mapsQuery = $t['nama_tempat'] . ', ' . $t['alamat'] . ', ' . $kuliner['nama_daerah'] . ', Jawa Timur, Indonesia'; ?>
            <div class="tempat-item">
              <span class="tempat-item__icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
              </span>
              <div>
                <h4><?php echo htmlspecialchars($t['nama_tempat']); ?></h4>
                <p><a href="https://www.google.com/maps/search/?api=1&amp;query=<?php echo rawurlencode($mapsQuery); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($t['alamat']); ?> &rarr;</a></p>
                <?php if (!empty($t['catatan'])): ?>
                  <span class="tempat-item__note"><?php echo htmlspecialchars($t['catatan']); ?></span>
                <?php endif; ?>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </article>

    </div>

    <!-- SIDEBAR -->
    <aside class="kuliner-sidebar">
      <div class="sidebar-card">
        <h3>Kuliner Lain dari <?php echo htmlspecialchars($kuliner['nama_daerah']); ?></h3>
        <?php if (mysqli_num_rows($lainResult) > 0): ?>
          <div class="sidebar-list">
            <?php while ($l = mysqli_fetch_assoc($lainResult)): ?>
              <a href="kuliner.php?slug=<?php echo urlencode($l['slug']); ?>" class="sidebar-item">
                <img src="<?php echo htmlspecialchars($l['gambar']); ?>" alt="<?php echo htmlspecialchars($l['nama_makanan']); ?>">
                <span><?php echo htmlspecialchars($l['nama_makanan']); ?></span>
              </a>
            <?php endwhile; ?>
          </div>
        <?php else: ?>
          <p class="sidebar-empty">Belum ada kuliner lain dari daerah ini.</p>
        <?php endif; ?>
        <a href="daerah.php?slug=<?php echo urlencode($kuliner['slug_daerah']); ?>" class="link-arrow link-arrow--sm">Lihat Semua Kuliner <?php echo htmlspecialchars($kuliner['nama_daerah']); ?> &rarr;</a>
      </div>

      <div class="sidebar-card sidebar-card--muted">
        <h3>Jelajahi Daerah Lain</h3>
        <p>Masih banyak UMKM Jawa Timur dengan cerita kulinernya masing-masing.</p>
        <a href="katalog.php" class="btn btn--primary" style="width:100%;">Lihat Katalog Kuliner</a>
      </div>
    </aside>
  </div>

  <?php include 'includes/footer.php'; ?>

</body>
</html>
