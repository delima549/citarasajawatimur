<?php
$active_page = 'katalog';
include 'includes/koneksi.php';

$wilayah = $_GET['nama'] ?? '';
$wilayah_valid = [
    'Surabaya Pusat',
    'Surabaya Utara',
    'Surabaya Timur',
    'Surabaya Selatan',
    'Surabaya Barat'
];
if (!in_array($wilayah, $wilayah_valid, true)) {
    header('Location: katalog.php');
    exit;
}

$stmt = mysqli_prepare($conn, "
    SELECT k.*, MIN(kt.alamat) AS lokasi
    FROM kuliner k
    JOIN daerah d ON d.id = k.daerah_id
    LEFT JOIN kuliner_tempat kt ON kt.kuliner_id = k.id
    WHERE d.slug = 'kota-surabaya' AND k.wilayah = ?
      AND (k.publikasi LIKE '%Katalog Kuliner%' OR k.publikasi LIKE '%Lainnya%' OR k.publikasi LIKE '%Rekomendasi%' OR k.publikasi LIKE '%Informasi Terbaru%' OR k.is_rekomendasi = 1)
    GROUP BY k.id
    ORDER BY k.rating DESC, k.nama_makanan ASC
");
mysqli_stmt_bind_param($stmt, "s", $wilayah);
mysqli_stmt_execute($stmt);
$kulinerResult = mysqli_stmt_get_result($stmt);
$total = mysqli_num_rows($kulinerResult);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kuliner <?php echo htmlspecialchars($wilayah); ?> — CitaRasaJawaTimur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <div class="breadcrumb">
    <a href="index.php">Beranda</a>
    <span>/</span>
    <a href="katalog.php">Katalog Kuliner</a>
    <span>/</span>
    <span class="breadcrumb__current"><?php echo htmlspecialchars($wilayah); ?></span>
  </div>

  <section class="page-banner page-banner--sm page-banner--katalog">
    <div class="page-banner__overlay"></div>
    <div class="page-banner__inner">
      <span class="eyebrow eyebrow--light">Wilayah Kuliner Surabaya</span>
      <h1>Kuliner <?php echo htmlspecialchars($wilayah); ?></h1>
      <p><?php echo $total; ?> rekomendasi makanan untuk kamu jelajahi di wilayah ini.</p>
    </div>
  </section>

  <section class="section">
    <div class="section__head">
      <div>
        <span class="eyebrow">Rekomendasi Wilayah</span>
        <h2>Temukan Kuliner Favorit</h2>
      </div>
      <a href="katalog.php" class="link-arrow">Pilih wilayah lain &rarr;</a>
    </div>

    <?php if ($total > 0): ?>
      <div class="card-grid">
        <?php while ($row = mysqli_fetch_assoc($kulinerResult)): ?>
          <a href="kuliner.php?slug=<?php echo urlencode($row['slug']); ?>" class="card card--link recommendation-card">
            <div class="card__media">
              <img src="<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_makanan']); ?>">
              <span class="badge badge--rekomendasi">REKOMENDASI</span>
            </div>
            <div class="card__body">
              <span class="card__tag"><?php echo htmlspecialchars($row['kategori']); ?></span>
              <h3><?php echo htmlspecialchars($row['nama_makanan']); ?></h3>
              <p class="recommendation-location"><?php echo htmlspecialchars($row['lokasi'] ?: 'Surabaya'); ?></p>
              <div class="recommendation-meta">
                <strong><?php echo htmlspecialchars(price_range_label($row)); ?></strong>
                <span>&#9733; <?php echo number_format((float) $row['rating'], 1); ?> (<?php echo (int) $row['jumlah_penilai']; ?>)</span>
              </div>
            </div>
          </a>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <h3>Belum Ada Rekomendasi</h3>
        <p>Rekomendasi kuliner untuk wilayah ini akan segera ditambahkan.</p>
      </div>
    <?php endif; ?>
  </section>

  <?php include 'includes/footer.php'; ?>

</body>
</html>
