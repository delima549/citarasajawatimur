<?php
$active_page = 'katalog';
include 'includes/koneksi.php';
$catalogBanner = get_site_content($conn, 'catalog', 'banner');

$daerah_dipilih = trim($_GET['daerah'] ?? '');
$kata_kunci = trim($_GET['q'] ?? '');

$daerahFilter = '';
if ($daerah_dipilih !== '') {
  $daerahFilter = " AND d.slug = '" . mysqli_real_escape_string($conn, $daerah_dipilih) . "'";
}
$kataKunciFilter = '';
if ($kata_kunci !== '') {
  $kataKunciEscaped = mysqli_real_escape_string($conn, $kata_kunci);
  $kataKunciFilter = " AND (d.nama LIKE '%{$kataKunciEscaped}%' OR d.slug LIKE '%{$kataKunciEscaped}%' OR d.deskripsi LIKE '%{$kataKunciEscaped}%' OR EXISTS (SELECT 1 FROM kuliner k_search WHERE k_search.daerah_id = d.id AND k_search.nama_makanan LIKE '%{$kataKunciEscaped}%'))";
}

$regionResult = mysqli_query($conn, "
    SELECT d.id, d.nama, d.jenis, d.slug, d.gambar, d.deskripsi, COUNT(k.id) AS jumlah_kuliner
    FROM daerah d
    LEFT JOIN kuliner k ON k.daerah_id = d.id
    WHERE 1=1 {$daerahFilter}{$kataKunciFilter}
    GROUP BY d.id
    ORDER BY d.jenis ASC, d.nama ASC
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($catalogBanner['label']); ?> — CitaRasaJawaTimur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css?v=20260924">
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <!-- PAGE BANNER -->
  <section class="page-banner page-banner--sm page-banner--katalog">
    <div class="page-banner__overlay"></div>
    <div class="page-banner__inner">
      <span class="eyebrow eyebrow--light"><?php echo htmlspecialchars($catalogBanner['label']); ?></span>
      <h1><?php echo content_title($catalogBanner['title']); ?></h1>
      <p><?php echo htmlspecialchars($catalogBanner['description']); ?></p>
      <form class="search-panel search-panel--flat" action="katalog.php" method="get">
        <div class="search-panel__filter">
          <label class="search-panel__label" for="katalog-daerah">CARI BERDASARKAN KOTA/KABUPATEN:</label>
          <select class="search-panel__select" id="katalog-daerah" name="daerah">
            <option value="">Semua daerah Jawa Timur</option>
            <?php
            $daerahOptions = mysqli_query($conn, "SELECT nama, slug FROM daerah ORDER BY jenis ASC, nama ASC");
            while ($daerahOption = mysqli_fetch_assoc($daerahOptions)):
            ?>
              <option value="<?php echo htmlspecialchars($daerahOption['slug']); ?>" <?php echo $daerah_dipilih === $daerahOption['slug'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($daerahOption['nama']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="search-panel__bar">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="m21 21-4.3-4.3"></path>
          </svg>
          <input id="katalog-q" type="search" name="q" value="<?php echo htmlspecialchars($kata_kunci); ?>" placeholder="Ketik nama kuliner atau daerah Jawa Timur...">
          <button type="submit">Cari Kuliner</button>
        </div>
      </form>
      <?php if ($daerah_dipilih !== '' || $kata_kunci !== ''): ?><a class="catalog-search__reset page-banner__reset" href="katalog.php">Reset pencarian</a><?php endif; ?>
    </div>
  </section>

  <!-- MENU WILAYAH -->
  <section class="section">
    <div class="section__head">
      <div>
        <span class="eyebrow">Pilih Wilayah</span>
        <h2>Jelajahi Kuliner Berdasarkan Wilayah</h2>
        <p><?php echo ($daerah_dipilih !== '' || $kata_kunci !== '') ? 'Hasil pencarian kota/kabupaten dan kuliner yang sesuai.' : 'Pilih kabupaten atau kota untuk melihat seluruh kuliner yang terdata di sana.'; ?></p>
      </div>
    </div>

    <?php $jumlahDaerah = 0; ?>
    <div class="region-menu">
      <?php while ($daerah = mysqli_fetch_assoc($regionResult)):
          $jumlahDaerah++;
          $jumlah = (int) $daerah['jumlah_kuliner'];
      ?>
        <a href="daerah.php?slug=<?php echo urlencode($daerah['slug']); ?>" class="region-menu__item">
          <img src="<?php echo htmlspecialchars($daerah['gambar']); ?>" alt="Kuliner <?php echo htmlspecialchars($daerah['nama']); ?>">
          <span class="region-menu__content">
            <strong><?php echo htmlspecialchars($daerah['nama']); ?></strong>
            <small><?php echo $jumlah; ?> rekomendasi kuliner</small>
          </span>
          <span class="region-menu__arrow" aria-hidden="true">&rarr;</span>
        </a>
      <?php endwhile; ?>
    </div>
    <?php if ($jumlahDaerah === 0): ?>
      <div class="empty-state"><h3>Daerah Tidak Ditemukan</h3><p>Coba gunakan nama kota/kabupaten atau nama kuliner yang lain.</p></div>
    <?php endif; ?>

  </section>

  <?php include 'includes/footer.php'; ?>

</body>
</html>
