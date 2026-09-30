<?php
$active_page = 'tentang';
include 'includes/koneksi.php';
$aboutBanner = get_site_content($conn, 'about', 'banner');
$totalDaerah = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM daerah"))['total'];
$totalKuliner = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM kuliner"))['total'];
$totalTempat = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM kuliner_tempat"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($aboutBanner['label']); ?> — CitaRasaJawaTimur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css?v=20260924">
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <!-- PAGE BANNER -->
  <section class="page-banner page-banner--about">
    <div class="page-banner__overlay"></div>
    <div class="page-banner__inner">
      <span class="eyebrow eyebrow--light"><?php echo htmlspecialchars($aboutBanner['label']); ?></span>
      <h1><?php echo content_title($aboutBanner['title']); ?></h1>
      <p><?php echo htmlspecialchars($aboutBanner['description']); ?></p>
    </div>
  </section>

  <!-- CERITA KAMI -->
  <section class="section about-story">
    <div class="about-story__media">
      <img src="https://i.pinimg.com/736x/a6/c2/28/a6c22823c94080307e24362f05eaa082.jpg" alt="Proses memasak kuliner tradisional Jawa Timur">
      <div class="about-story__stat">
        <strong><?php echo $totalDaerah; ?></strong>
        <span>Kota &amp; Kabupaten Terdata</span>
      </div>
    </div>
    <div class="about-story__text">
      <span class="eyebrow">Cerita Kami</span>
      <h2>Satu direktori untuk menemukan rasa Jawa Timur</h2>
      <p>Jawa Timur tumbuh dari resep keluarga, warung sederhana, dan usaha kuliner yang menjaga rasa dari satu generasi ke generasi berikutnya. CitaRasaJawaTimur membantu pengunjung menemukan cerita dan tempat di balik hidangan tersebut.</p>
      <p>Telusuri kuliner berdasarkan kota atau kabupaten, bandingkan pilihan berdasarkan harga dan rating, lalu temukan alamat tempatnya melalui peta dan Google Maps.</p>
      <div class="about-story__facts" aria-label="Ringkasan direktori">
        <div><strong><?php echo $totalKuliner; ?></strong><span>Kuliner terdata</span></div>
        <div><strong><?php echo $totalTempat; ?></strong><span>Tempat kuliner</span></div>
        <div><strong>Jawa Timur</strong><span>Fokus wilayah</span></div>
      </div>
    </div>
  </section>

  <!-- VISI & MISI -->
  <section class="section section--muted">
    <div class="section__head section__head--center">
      <div>
        <span class="eyebrow">Arah Kami</span>
        <h2>Visi &amp; Misi</h2>
      </div>
    </div>

    <div class="vm-grid">
      <div class="vm-card vm-card--visi">
        <span class="vm-card__icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7Z"></path>
          </svg>
        </span>
        <h3>Visi</h3>
        <p>Menjadi platform rujukan kuliner Jawa Timur yang menghubungkan penikmat makanan dengan UMKM lokal secara mudah, informatif, dan berkelanjutan.</p>
      </div>

      <div class="vm-card">
        <span class="vm-card__icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M12 2 2 7l10 5 10-5-10-5Z"></path>
            <path d="M2 17l10 5 10-5"></path>
            <path d="M2 12l10 5 10-5"></path>
          </svg>
        </span>
        <h3>Misi</h3>
        <ul class="vm-list">
          <li>Menghadirkan katalog kuliner berdasarkan kabupaten dan kota di Jawa Timur.</li>
          <li>Menyediakan informasi makanan, lokasi, harga, dan penilaian dalam satu tempat.</li>
          <li>Membantu pengunjung menemukan kuliner sesuai kebutuhan dan minatnya.</li>
          <li>Mendukung UMKM lokal agar lebih dikenal oleh pelanggan baru.</li>
          <li>Menjaga cerita dan kekayaan rasa Jawa Timur tetap hidup.</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- KENAPA KAMI -->
  <section class="section">
    <div class="section__head">
      <div>
        <span class="eyebrow">Kenapa CitaRasaJawaTimur</span>
        <h2>Semua yang dibutuhkan untuk menjelajah kuliner</h2>
      </div>
    </div>

    <div class="feature-grid">
      <div class="feature">
        <span class="feature__icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z"></path>
            <circle cx="12" cy="10" r="3"></circle>
          </svg>
        </span>
        <h3>Jelajah Wilayah</h3>
        <p>Pilih kota atau kabupaten untuk menemukan kuliner khas, cerita lokal, dan rekomendasi tempat makan di sekitarnya.</p>
      </div>

      <div class="feature">
        <span class="feature__icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="m9 11 3 3L22 4"></path>
            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
          </svg>
        </span>
        <h3>Detail yang Lengkap</h3>
        <p>Setiap kuliner dilengkapi kategori, harga, rating, cerita, resep, lokasi, dan informasi tempat yang dapat dikunjungi.</p>
      </div>

      <div class="feature">
        <span class="feature__icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="m21 21-4.3-4.3"></path>
          </svg>
        </span>
        <h3>Peta &amp; Lokasi</h3>
        <p>Gunakan peta kuliner dan tautan Google Maps untuk menemukan lokasi tempat makan dengan lebih mudah.</p>
      </div>

      <div class="feature">
        <span class="feature__icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
        </span>
        <h3>Ruang untuk UMKM</h3>
        <p>Membantu kuliner lokal dikenal lebih luas melalui direktori yang rapi, informatif, dan mudah diakses.</p>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta-band">
    <div class="cta-band__inner">
      <div>
        <h2>Siap menemukan rasa berikutnya?</h2>
        <p>Jelajahi katalog kuliner Jawa Timur dan temukan sajian lokal yang ingin kamu coba.</p>
      </div>
      <a href="katalog.php" class="btn btn--light">Jelajahi Katalog</a>
    </div>
  </section>

  <?php include 'includes/footer.php'; ?>

</body>
</html>
