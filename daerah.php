<?php
$active_page = 'katalog';
include 'includes/koneksi.php';

$slug = $_GET['slug'] ?? '';

$stmt = mysqli_prepare($conn, "SELECT * FROM daerah WHERE slug = ?");
mysqli_stmt_bind_param($stmt, "s", $slug);
mysqli_stmt_execute($stmt);
$daerah = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$daerah) {
    header("Location: katalog.php");
    exit;
}

$stmt2 = mysqli_prepare($conn, "SELECT * FROM kuliner WHERE daerah_id = ? ORDER BY nama_makanan ASC");
mysqli_stmt_bind_param($stmt2, "i", $daerah['id']);
mysqli_stmt_execute($stmt2);
$kulinerResult = mysqli_stmt_get_result($stmt2);
$totalKuliner = mysqli_num_rows($kulinerResult);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($daerah['nama']); ?> — CitaRasaJawaTimur</title>
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
    <span class="breadcrumb__current"><?php echo htmlspecialchars($daerah['nama']); ?></span>
  </div>

  <!-- DAERAH BANNER -->
  <section class="daerah-banner">
    <div class="daerah-banner__media">
      <img src="<?php echo htmlspecialchars($daerah['gambar']); ?>" alt="<?php echo htmlspecialchars($daerah['nama']); ?>">
    </div>
    <div class="daerah-banner__text">
      <span class="badge badge--kategori" style="position:static; display:inline-flex;"><?php echo htmlspecialchars($daerah['jenis']); ?></span>
      <h1><?php echo htmlspecialchars($daerah['nama']); ?></h1>
      <p><?php echo htmlspecialchars($daerah['deskripsi']); ?></p>
      <span class="daerah-banner__count">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 8a6 6 0 0 0-12 0c0 4-2 6-2 6h16s-2-2-2-6"></path>
          <path d="M13.7 21a2 2 0 0 1-3.4 0"></path>
        </svg>
        <?php echo $totalKuliner; ?> kuliner terdata di daerah ini
      </span>
    </div>
  </section>

  <!-- DAFTAR KULINER -->
  <section class="section">
    <div class="section__head">
      <div>
        <span class="eyebrow">Kuliner Khas</span>
        <h2>Kuliner dari <?php echo htmlspecialchars($daerah['nama']); ?></h2>
      </div>
    </div>

    <?php if ($totalKuliner > 0): ?>
      <div class="card-grid">
        <?php while ($row = mysqli_fetch_assoc($kulinerResult)): ?>
          <a href="kuliner.php?slug=<?php echo urlencode($row['slug']); ?>" class="card card--link">
              <div class="card__media">
                <img src="<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_makanan']); ?>">
                <span class="badge badge--kategori"><?php echo htmlspecialchars($row['kategori']); ?></span>
                <?php if ($row['is_rekomendasi']): ?>
                  <span class="badge badge--rekomendasi badge--top-right">REKOMENDASI</span>
                <?php endif; ?>
              </div>
              <div class="card__body">
                <h3><?php echo htmlspecialchars($row['nama_makanan']); ?></h3>
                <p><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                <span class="link-arrow link-arrow--sm">Lihat Resep &amp; Cerita &rarr;</span>
              </div>
          </a>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <h3>Belum Ada Kuliner Terdata</h3>
        <p>Kuliner untuk daerah ini akan segera ditambahkan.</p>
      </div>
    <?php endif; ?>
  </section>

  <?php include 'includes/footer.php'; ?>

</body>
</html>
