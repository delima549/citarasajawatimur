<?php
$active_page = 'beranda';
include 'includes/koneksi.php';
$daerahSearchResult = mysqli_query($conn, "SELECT nama, slug FROM daerah ORDER BY jenis ASC, nama ASC");
$homeHero = get_site_content($conn, 'home', 'hero');
$homeLatest = get_site_content($conn, 'home', 'latest');
$uniqueArticles = get_site_articles($conn, 'unik');
$authenticArticles = get_site_articles($conn, 'autentik');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CitaRasaJawaTimur — <?php echo htmlspecialchars($homeHero['title']); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css?v=20260924">
</head>
<body id="top">

  <?php include 'includes/header.php'; ?>

  <!-- HERO -->
  <section class="hero">
    <div class="hero__overlay"></div>
    <div class="hero__inner">
      <h1 class="hero__title-special"><?php echo content_title($homeHero['title']); ?></h1>
      <p class="hero__description"><?php echo htmlspecialchars($homeHero['description']); ?></p>

      <form class="search-panel" action="katalog.php" method="get">
        <div class="search-panel__filter">
          <label class="search-panel__label" for="wilayah-beranda">CARI BERDASARKAN KOTA/KABUPATEN:</label>
          <select class="search-panel__select" id="wilayah-beranda" name="daerah">
            <option value="">Semua daerah Jawa Timur</option>
            <?php while ($daerahSearch = mysqli_fetch_assoc($daerahSearchResult)): ?>
              <option value="<?php echo htmlspecialchars($daerahSearch['slug']); ?>"><?php echo htmlspecialchars($daerahSearch['nama']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="search-panel__bar">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="m21 21-4.3-4.3"></path>
          </svg>
          <input type="text" name="q" placeholder="<?php echo htmlspecialchars($homeHero['placeholder']); ?>">
          <button type="submit"><?php echo htmlspecialchars($homeHero['button_label']); ?></button>
        </div>
      </form>
    </div>
  </section>

  <?php
    $kulinerQuery = mysqli_query($conn, "
      SELECT k.*, d.nama AS nama_daerah, MIN(kt.alamat) AS lokasi
      FROM kuliner k
      JOIN daerah d ON d.id = k.daerah_id
      LEFT JOIN kuliner_tempat kt ON kt.kuliner_id = k.id
      GROUP BY k.id
      ORDER BY k.id ASC
    ");
    $kulinerJawaTimur = [];
    while ($row = mysqli_fetch_assoc($kulinerQuery)) {
      $kulinerJawaTimur[] = $row;
    }

    $takeThree = function (array $items) use ($kulinerJawaTimur): array {
      $selected = [];
      $selectedIds = [];
      foreach (array_merge($items, $kulinerJawaTimur) as $item) {
        if (isset($selectedIds[$item['id']])) {
          continue;
        }
        $selected[] = $item;
        $selectedIds[$item['id']] = true;
        if (count($selected) === 3) {
          break;
        }
      }
      return $selected;
    };

    $popularSlugs = ['rawon-setan', 'soto-ayam-lamongan', 'bebek-sinjay-bangkalan'];
    $popularItems = array_values(array_filter($kulinerJawaTimur, fn($item) => in_array($item['slug'], $popularSlugs, true)));
    usort($popularItems, fn($a, $b) => array_search($a['slug'], $popularSlugs, true) <=> array_search($b['slug'], $popularSlugs, true));
    $cheapItems = $kulinerJawaTimur;
    usort($cheapItems, fn($a, $b) => ((int) $a['harga'] <=> (int) $b['harga']) ?: ((float) $b['rating'] <=> (float) $a['rating']));
    $uniqueItems = array_values(array_filter($kulinerJawaTimur, fn($row) => in_array($row['kategori'], ['Makanan Ringan', 'Minuman', 'Jajanan'], true)));
    $authenticItems = array_values(array_filter($kulinerJawaTimur, fn($row) => trim($row['resep_bahan']) !== '' && trim($row['resep_langkah']) !== ''));
    $articleCard = static fn($article): array => [
        'id' => 'article-' . $article['id'],
        'jenis' => 'artikel',
        'slug' => $article['slug'],
        'gambar' => $article['image_url'],
        'nama_makanan' => $article['title'],
        'nama_daerah' => $article['category'] === 'unik' ? 'Artikel Kuliner Jawa Timur' : 'Resep Kuliner Jawa Timur',
        'lokasi' => $article['excerpt'],
        'harga' => 0,
        'harga_min' => 0,
        'harga_max' => 0,
        'rating' => 0,
        'jumlah_penilai' => 0
    ];

    $rekomendasiHome = [
      'populer' => [
        'label' => $homeLatest['title'],
        'deskripsi' => $homeLatest['description'],
        'items' => $takeThree($popularItems)
      ],
      'murah' => [
        'label' => 'Rekomendasi Kuliner Murah',
        'deskripsi' => 'Pilihan kuliner lezat dengan harga yang ramah di kantong.',
        'items' => $takeThree($cheapItems)
      ],
      'unik' => [
        'label' => 'Artikel Kuliner Jawa Timur',
        'deskripsi' => 'Temukan sajian khas dan menarik dari berbagai daerah di Jawa Timur.',
          'items' => array_map($articleCard, $uniqueArticles)
      ],
      'autentik' => [
        'label' => 'Resep Kuliner Jawa Timur',
        'deskripsi' => 'Jelajahi kuliner dengan resep dan cerita yang dapat dibaca lengkap.',
          'items' => array_map($articleCard, $authenticArticles)
      ]
    ];
  ?>

  <!-- REKOMENDASI BERGANTI -->
  <section class="section">
    <div class="section__head">
      <div>
        <span class="eyebrow"><?php echo htmlspecialchars($homeLatest['label']); ?></span>
        <h2 id="rekomendasi-title"><?php echo htmlspecialchars($homeLatest['title']); ?></h2>
        <p id="rekomendasi-description"><?php echo htmlspecialchars($homeLatest['description']); ?></p>
      </div>
      <div class="recommendation-controls" aria-label="Navigasi informasi terbaru">
        <button class="recommendation-next recommendation-next--previous" id="rekomendasi-prev" type="button" aria-label="Tampilkan informasi sebelumnya" title="Tampilkan informasi sebelumnya">&lt;</button>
        <button class="recommendation-next" id="rekomendasi-next" type="button" aria-label="Tampilkan informasi berikutnya" title="Tampilkan informasi berikutnya">&gt;</button>
      </div>
    </div>

    <div class="card-grid recommendation-grid" id="recommendation-grid">
      <?php foreach ($rekomendasiHome['populer']['items'] as $row): ?>
        <a href="kuliner.php?slug=<?php echo urlencode($row['slug']); ?>" class="card card--link recommendation-card">
          <div class="card__media">
            <img src="<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_makanan']); ?>">
            <span class="badge badge--rekomendasi">REKOMENDASI</span>
          </div>
          <div class="card__body">
            <span class="card__tag"><?php echo htmlspecialchars($row['nama_daerah']); ?></span>
            <h3><?php echo htmlspecialchars($row['nama_makanan']); ?></h3>
            <p class="recommendation-location"><?php echo htmlspecialchars($row['lokasi'] ?: $row['nama_daerah']); ?></p>
            <div class="recommendation-meta">
              <strong><?php echo htmlspecialchars(price_range_label($row)); ?></strong>
              <span>&#9733; <?php echo number_format((float) $row['rating'], 1); ?> (<?php echo (int) $row['jumlah_penilai']; ?>)</span>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <script>
    const recommendationSets = <?php echo json_encode($rekomendasiHome, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const recommendationKeys = Object.keys(recommendationSets);
    let recommendationIndex = 0;

    function renderRecommendations() {
      const set = recommendationSets[recommendationKeys[recommendationIndex]];
      document.getElementById('rekomendasi-title').textContent = set.label;
      document.getElementById('rekomendasi-description').textContent = set.deskripsi;
      document.getElementById('recommendation-grid').innerHTML = set.items.map((item) => `
        <a href="${item.jenis === 'artikel' ? `artikel.php?slug=${encodeURIComponent(item.slug)}` : `kuliner.php?slug=${encodeURIComponent(item.slug)}`}" class="card card--link recommendation-card">
          <div class="card__media">
            <img src="${item.gambar}" alt="${item.nama_makanan}">
            <span class="badge badge--rekomendasi">${item.jenis === 'artikel' ? 'ARTIKEL' : 'REKOMENDASI'}</span>
          </div>
          <div class="card__body">
            <span class="card__tag">${item.nama_daerah}</span>
            <h3>${item.nama_makanan}</h3>
            <p class="recommendation-location">${item.jenis === 'artikel' ? item.lokasi : (item.lokasi || item.nama_daerah)}</p>
            ${item.jenis === 'artikel' ? '<span class="link-arrow link-arrow--sm">Baca artikel lengkap &rarr;</span>' : `<div class="recommendation-meta"><strong>${item.harga_min === item.harga_max ? `Rp ${Number(item.harga_min).toLocaleString('id-ID')}` : `Rp ${Number(item.harga_min).toLocaleString('id-ID')} - Rp ${Number(item.harga_max).toLocaleString('id-ID')}`}</strong><span>&#9733; ${Number(item.rating).toFixed(1)} (${item.jumlah_penilai})</span></div>`}
          </div>
        </a>`).join('');
    }

    function changeRecommendation(direction) {
      recommendationIndex = (recommendationIndex + direction + recommendationKeys.length) % recommendationKeys.length;
      renderRecommendations();
    }

    document.getElementById('rekomendasi-prev').addEventListener('click', () => {
      changeRecommendation(-1);
    });

    document.getElementById('rekomendasi-next').addEventListener('click', () => {
      changeRecommendation(1);
    });
  </script>

  <!-- REKOMENDASI -->
  <section class="section section--muted">
    <div class="section__head">
      <div>
        <span class="eyebrow">Kuliner Pilihan</span>
        <h2>Rekomendasi Kuliner Enak Menurut Pengguna</h2>
      </div>
      <a href="rekomendasi.php" class="link-arrow">Lihat Semua Rekomendasi &rarr;</a>
    </div>

    <?php
      $rekomHome = mysqli_query($conn, "
          SELECT k.*, d.nama AS nama_daerah, MIN(kt.alamat) AS lokasi
          FROM kuliner k
          JOIN daerah d ON d.id = k.daerah_id
          LEFT JOIN kuliner_tempat kt ON kt.kuliner_id = k.id
          WHERE (k.is_rekomendasi = 1 OR k.publikasi LIKE '%Informasi Terbaru%' OR k.publikasi LIKE '%Rekomendasi%')
          GROUP BY k.id
          ORDER BY RAND()
      ");
      $rekomendasiPilihan = [];
      while ($row = mysqli_fetch_assoc($rekomHome)) {
        $rekomendasiPilihan[] = $row;
      }
      $rekomendasiAwal = array_slice($rekomendasiPilihan, 0, 3);
    ?>
    <div class="card-grid" id="rekomendasi-pengguna-grid">
      <?php
      foreach ($rekomendasiAwal as $row):
      ?>
        <a href="kuliner.php?slug=<?php echo urlencode($row['slug']); ?>" class="card card--link">
          <div class="card__media">
            <img src="<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_makanan']); ?>">
            <span class="badge badge--rekomendasi">REKOMENDASI</span>
          </div>
          <div class="card__body">
            <span class="card__tag">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8a6 6 0 0 0-12 0c0 4-2 6-2 6h16s-2-2-2-6"></path>
                <path d="M13.7 21a2 2 0 0 1-3.4 0"></path>
              </svg>
              <?php echo htmlspecialchars($row['nama_daerah'] . ' · ' . $row['kategori']); ?>
            </span>
            <h3><?php echo htmlspecialchars($row['nama_makanan']); ?></h3>
            <p><?php echo htmlspecialchars($row['deskripsi']); ?></p>
            <p class="recommendation-location" title="<?php echo htmlspecialchars($row['lokasi'] ?: $row['nama_daerah']); ?>">
              Lokasi: <?php echo htmlspecialchars($row['lokasi'] ?: $row['nama_daerah']); ?>
            </p>
            <div class="recommendation-meta">
              <strong><?php echo htmlspecialchars(price_range_label($row)); ?></strong>
              <span>&#9733; <?php echo number_format((float) $row['rating'], 1); ?> (<?php echo number_format((int) $row['jumlah_penilai'], 0, ',', '.'); ?> penilaian)</span>
            </div>
            <span class="link-arrow link-arrow--sm">Lihat detail kuliner &rarr;</span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <script>
    const recommendationItems = <?php echo json_encode($rekomendasiPilihan, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    const recommendationGrid = document.getElementById('rekomendasi-pengguna-grid');

    function escapeRecommendationHtml(value) {
      return String(value ?? '').replace(/[&<>"']/g, (character) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
      })[character]);
    }

    function renderUserRecommendations() {
      const shuffled = [...recommendationItems].sort(() => Math.random() - 0.5).slice(0, 3);
      recommendationGrid.innerHTML = shuffled.map((item) => `
        <a href="kuliner.php?slug=${encodeURIComponent(item.slug)}" class="card card--link">
          <div class="card__media">
            <img src="${escapeRecommendationHtml(item.gambar)}" alt="${escapeRecommendationHtml(item.nama_makanan)}">
            <span class="badge badge--rekomendasi">REKOMENDASI</span>
          </div>
          <div class="card__body">
            <span class="card__tag">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8a6 6 0 0 0-12 0c0 4-2 6-2 6h16s-2-2-2-6"></path>
                <path d="M13.7 21a2 2 0 0 1-3.4 0"></path>
              </svg>
              ${escapeRecommendationHtml(item.nama_daerah)} · ${escapeRecommendationHtml(item.kategori)}
            </span>
            <h3>${escapeRecommendationHtml(item.nama_makanan)}</h3>
            <p>${escapeRecommendationHtml(item.deskripsi)}</p>
            <p class="recommendation-location" title="${escapeRecommendationHtml(item.lokasi || item.nama_daerah)}">Lokasi: ${escapeRecommendationHtml(item.lokasi || item.nama_daerah)}</p>
            <div class="recommendation-meta">
              <strong>${Number(item.harga_min) === Number(item.harga_max) ? `Rp ${Number(item.harga_min).toLocaleString('id-ID')}` : `Rp ${Number(item.harga_min).toLocaleString('id-ID')} - Rp ${Number(item.harga_max).toLocaleString('id-ID')}`}</strong>
              <span>&#9733; ${Number(item.rating).toFixed(1)} (${Number(item.jumlah_penilai).toLocaleString('id-ID')} penilaian)</span>
            </div>
            <span class="link-arrow link-arrow--sm">Lihat detail kuliner &rarr;</span>
          </div>
        </a>`).join('');
    }

    if (recommendationItems.length > 3) {
      setInterval(renderUserRecommendations, 5000);
    }
  </script>

  <?php include 'includes/footer.php'; ?>

</body>
</html>
