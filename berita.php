<?php
$active_page = 'beranda';
include 'includes/koneksi.php';
require_once 'includes/berita_data.php';
$beritaList = site_news_items();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Berita Kuliner Jawa Timur — CitaRasaJawaTimur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <section class="page-banner page-banner--sm">
    <div class="page-banner__overlay"></div>
    <div class="page-banner__inner">
      <span class="eyebrow eyebrow--light">Informasi Terbaru</span>
      <h1>Berita Kuliner Jawa Timur</h1>
      <p>Temukan kisah, tren, dan kabar terbaru dari pelaku UMKM kuliner di Jawa Timur.</p>
    </div>
  </section>

  <section class="section">
    <div class="card-grid">
      <?php foreach ($beritaList as $berita): ?>
        <article class="card">
          <div class="card__media">
            <img src="<?php echo htmlspecialchars($berita['image']); ?>" alt="<?php echo htmlspecialchars($berita['title']); ?>">
            <span class="badge badge--berita"><?php echo htmlspecialchars($berita['category']); ?></span>
          </div>
          <div class="card__body">
            <span class="card__date">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                <path d="M16 2v4M8 2v4M3 10h18"></path>
              </svg>
              <?php echo htmlspecialchars($berita['date']); ?>
            </span>
            <h3><?php echo htmlspecialchars($berita['title']); ?></h3>
            <p><?php echo htmlspecialchars($berita['excerpt']); ?></p>
            <a href="berita_detail.php?slug=<?php echo urlencode($berita['slug']); ?>" class="link-arrow link-arrow--sm">Baca Selengkapnya &rarr;</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>
</body>
</html>
