<?php
$active_page = 'rekomendasi';
include 'includes/koneksi.php';

$recommendationId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$recommendation = null;
if ($recommendationId) {
  $stmt = mysqli_prepare($conn, "SELECT r.*, u.nama AS nama_pengguna
      FROM user_recommendations r
      JOIN users u ON u.id = r.user_id
      WHERE r.id = ? AND r.status = 'Disetujui'");
  mysqli_stmt_bind_param($stmt, 'i', $recommendationId);
  mysqli_stmt_execute($stmt);
  $recommendation = mysqli_stmt_get_result($stmt)->fetch_assoc();
}

if (!$recommendation) {
  http_response_code(404);
}

$image = $recommendation && !empty($recommendation['gambar'])
  ? $recommendation['gambar']
  : 'https://images.unsplash.com/photo-15150031972-19c99c088f9d?w=1200&q=80';
$mapsUrl = $recommendation
  ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($recommendation['lokasi'] . ', ' . $recommendation['wilayah'] . ', Jawa Timur, Indonesia')
  : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($recommendation ? $recommendation['nama_kuliner'] : 'Rekomendasi tidak ditemukan'); ?> — CitaRasaJawaTimur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css?v=20260924">
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <?php if ($recommendation): ?>
    <div class="breadcrumb">
      <a href="index.php">Beranda</a>
      <span>/</span>
      <a href="rekomendasi.php">Rekomendasi Kuliner</a>
      <span>/</span>
      <span class="breadcrumb__current"><?php echo htmlspecialchars($recommendation['nama_kuliner']); ?></span>
    </div>

    <section class="kuliner-hero">
      <div class="kuliner-hero__media">
        <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($recommendation['nama_kuliner']); ?>">
      </div>
      <div class="kuliner-hero__text">
        <span class="badge badge--rekomendasi">PILIHAN PENGGUNA</span>
        <h1><?php echo htmlspecialchars($recommendation['nama_kuliner']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($recommendation['deskripsi'])); ?></p>
        <div class="kuliner-hero__meta">
          <span class="chip chip--static"><?php echo htmlspecialchars($recommendation['kategori']); ?></span>
          <span class="chip chip--static"><?php echo htmlspecialchars($recommendation['wilayah']); ?></span>
          <span class="chip chip--static">&#9733; <?php echo number_format((float) $recommendation['rating'], 1); ?> / 5</span>
          <span class="chip chip--static">Rp <?php echo number_format((int) $recommendation['harga_min'], 0, ',', '.'); ?> - Rp <?php echo number_format((int) $recommendation['harga_max'], 0, ',', '.'); ?></span>
        </div>
      </div>
    </section>

    <section class="section">
      <article class="content-block">
        <h2>Lokasi</h2>
        <p><a href="<?php echo htmlspecialchars($mapsUrl); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($recommendation['lokasi']); ?> &rarr;</a></p>
      </article>
      <p class="community-card__author">
        Dibagikan oleh <?php echo htmlspecialchars($recommendation['nama_pengguna']); ?>
        pada <?php echo date('d/m/Y', strtotime($recommendation['dibuat_pada'])); ?>.
      </p>
      <p><a class="link-arrow" href="rekomendasi.php">&larr; Kembali ke rekomendasi</a></p>
    </section>
  <?php else: ?>
    <section class="section">
      <div class="empty-state">
        <h1>Rekomendasi Tidak Ditemukan</h1>
        <p>Informasi ini tidak tersedia atau belum disetujui untuk ditampilkan.</p>
        <a class="btn btn--primary" href="rekomendasi.php">Kembali ke rekomendasi</a>
      </div>
    </section>
  <?php endif; ?>

  <?php include 'includes/footer.php'; ?>
</body>
</html>