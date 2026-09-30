<?php
$active_page = 'rekomendasi';
include 'includes/koneksi.php';
$recommendationBanner = get_site_content($conn, 'recommendation', 'banner');

$daerah_dipilih = $_GET['daerah'] ?? '';
$rawKeyword = $_GET['cari'] ?? '';
$kata_kunci = is_string($rawKeyword) ? trim(mb_substr($rawKeyword, 0, 100)) : '';
$loadRecommendationItems = static function (string $conditions, string $ordering) use ($conn, $daerah_dipilih, $kata_kunci): array {
  $sql = "SELECT k.*, d.nama AS nama_daerah, d.slug AS slug_daerah
      FROM kuliner k
      JOIN daerah d ON d.id = k.daerah_id
      WHERE ($conditions)";
  $types = '';
  $parameters = [];
  if ($daerah_dipilih !== '') {
    $sql .= ' AND d.slug = ?';
    $types .= 's';
    $parameters[] = $daerah_dipilih;
  }
  if ($kata_kunci !== '') {
    $sql .= " AND (k.nama_makanan LIKE ? OR k.kategori LIKE ? OR k.wilayah LIKE ?
        OR k.deskripsi LIKE ? OR k.sejarah LIKE ? OR k.resep_bahan LIKE ? OR k.resep_langkah LIKE ?
        OR d.nama LIKE ? OR d.deskripsi LIKE ?
        OR EXISTS (SELECT 1 FROM kuliner_tempat t WHERE t.kuliner_id = k.id AND (t.nama_tempat LIKE ? OR t.alamat LIKE ? OR t.catatan LIKE ?)) )";
    $types .= str_repeat('s', 12);
    $parameters = array_merge($parameters, array_fill(0, 12, '%' . $kata_kunci . '%'));
  }
  $limit = $kata_kunci === '' ? 7 : 20;
  $sql .= " ORDER BY $ordering, k.nama_makanan ASC LIMIT $limit";

  $stmt = mysqli_prepare($conn, $sql);
  if ($parameters) {
    $bindings = [$stmt, $types];
    foreach ($parameters as $index => &$parameter) {
      $bindings[] = &$parameter;
    }
    unset($parameter);
    call_user_func_array('mysqli_stmt_bind_param', $bindings);
  }
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $items = [];
  while ($item = mysqli_fetch_assoc($result)) {
    $items[] = $item;
  }
  return $items;
};

$recommendationOrder = 'k.is_rekomendasi DESC, k.rating DESC, k.jumlah_penilai DESC';
$recommendationSets = [
  [
    'title' => '7 Rekomendasi Kuliner Minggu Ini',
    'description' => 'Pilihan kuliner unggulan minggu ini berdasarkan rekomendasi, rating, dan jumlah penilaian.',
    'items' => $loadRecommendationItems("k.is_rekomendasi = 1 OR k.publikasi LIKE '%Rekomendasi%' OR k.publikasi LIKE '%Informasi Terbaru%'", 'k.rating DESC, k.jumlah_penilai DESC')
  ],
  [
    'title' => 'Kuliner Surabaya yang Wajib Dicoba',
    'description' => 'Jelajahi sajian khas Surabaya yang populer dan layak masuk daftar kuliner Anda.',
    'items' => $loadRecommendationItems("d.slug = 'kota-surabaya'", $recommendationOrder)
  ],
  [
    'title' => 'Rekomendasi Makanan Ringan & Jajanan',
    'description' => 'Temukan camilan dan jajanan khas Jawa Timur untuk teman bersantai atau oleh-oleh.',
    'items' => $loadRecommendationItems("k.kategori IN ('Makanan Ringan', 'Jajanan')", $recommendationOrder)
  ],
  [
    'title' => 'Rekomendasi Minuman',
    'description' => 'Pilihan minuman khas yang segar maupun hangat dari berbagai daerah.',
    'items' => $loadRecommendationItems("k.kategori = 'Minuman'", $recommendationOrder)
  ],
  [
    'title' => 'Rekomendasi Makanan Berat',
    'description' => 'Sajian utama khas Jawa Timur untuk pilihan makan yang lebih mengenyangkan.',
    'items' => $loadRecommendationItems("k.kategori = 'Makanan Berat'", $recommendationOrder)
  ]
];
if ($kata_kunci !== '') {
  $recommendationSets[0]['title'] = 'Hasil Rekomendasi: ' . $kata_kunci;
  $recommendationSets[0]['description'] = 'Kuliner pilihan yang sesuai dengan kata kunci pencarian Anda.';
}
$initialRecommendationSet = $recommendationSets[0];
$total = count($initialRecommendationSet['items']);

$daerahList = mysqli_query($conn, "
    SELECT DISTINCT d.nama, d.slug
    FROM daerah d
    JOIN kuliner k ON k.daerah_id = d.id
    WHERE (k.is_rekomendasi = 1 OR k.publikasi LIKE '%Rekomendasi%' OR k.publikasi LIKE '%Informasi Terbaru%')
    ORDER BY d.nama ASC
");

  $userRecommendationSql = "SELECT r.*, u.nama AS nama_pengguna
    FROM user_recommendations r
    JOIN users u ON u.id = r.user_id
    WHERE r.status = 'Disetujui'";
  $userRecommendationSql .= " ORDER BY r.rating DESC, r.dibuat_pada DESC";
  $userRecommendations = mysqli_query($conn, $userRecommendationSql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($recommendationBanner['label']); ?> — CitaRasaJawaTimur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css?v=20260924">
</head>
<body>

  <?php include 'includes/header.php'; ?>

  <!-- PAGE BANNER -->
  <section class="page-banner page-banner--sm page-banner--rekomendasi">
    <div class="page-banner__overlay"></div>
    <div class="page-banner__inner">
      <span class="eyebrow eyebrow--light"><?php echo htmlspecialchars($recommendationBanner['label']); ?></span>
      <h1><?php echo content_title($recommendationBanner['title']); ?></h1>
      <p><?php echo htmlspecialchars($recommendationBanner['description']); ?></p>
    </div>
  </section>

  <!-- FILTER DAERAH -->
  <section class="filter-section filter-section--sm">
    <div class="chip-row">
      <a href="rekomendasi.php" class="chip <?php echo $daerah_dipilih === '' ? 'chip--active' : ''; ?>">Semua Daerah</a>
      <?php while ($d = mysqli_fetch_assoc($daerahList)): ?>
        <a href="rekomendasi.php?daerah=<?php echo urlencode($d['slug']); ?>"
           class="chip <?php echo $daerah_dipilih === $d['slug'] ? 'chip--active' : ''; ?>">
          <?php echo htmlspecialchars($d['nama']); ?>
        </a>
      <?php endwhile; ?>
    </div>
  </section>

  <!-- HASIL -->
  <section class="section">
    <div class="section__head">
      <div>
        <span class="eyebrow">Kuliner Pilihan</span>
        <h2 id="recommendation-title"><?php echo htmlspecialchars($initialRecommendationSet['title']); ?></h2>
        <p id="recommendation-description"><?php echo htmlspecialchars($initialRecommendationSet['description']); ?></p>
      </div>
      <div class="recommendation-controls" aria-label="Navigasi pilihan rekomendasi">
        <span class="recommendation-position" id="recommendation-position" aria-live="polite">1 / <?php echo count($recommendationSets); ?></span>
        <button class="recommendation-next recommendation-next--previous" id="recommendation-prev" type="button" aria-label="Pilihan rekomendasi sebelumnya" title="Pilihan sebelumnya">&lt;</button>
        <button class="recommendation-next" id="recommendation-next" type="button" aria-label="Pilihan rekomendasi berikutnya" title="Pilihan berikutnya">&gt;</button>
      </div>
    </div>

      <div class="card-grid" id="recommendation-grid" aria-live="polite"></div>
      <div class="empty-state" id="recommendation-empty" hidden>
        <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M12 2 2 7l10 5 10-5-10-5Z"></path>
          <path d="M2 17l10 5 10-5"></path>
        </svg>
        <h3>Belum Ada Rekomendasi</h3>
        <p>Belum ada kuliner pada pilihan ini untuk daerah yang dipilih.</p>
      </div>
  </section>

  <script>
    const recommendationSets = <?php echo json_encode($recommendationSets, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    let recommendationIndex = 0;

    function createRecommendationCard(item) {
      const card = document.createElement('a');
      card.href = `kuliner.php?slug=${encodeURIComponent(item.slug)}`;
      card.className = 'card card--link';

      const media = document.createElement('div');
      media.className = 'card__media';
      const image = document.createElement('img');
      image.src = item.gambar;
      image.alt = item.nama_makanan;
      const badge = document.createElement('span');
      badge.className = 'badge badge--rekomendasi';
      badge.textContent = 'REKOMENDASI';
      media.append(image, badge);

      const body = document.createElement('div');
      body.className = 'card__body';
      const tag = document.createElement('span');
      tag.className = 'card__tag';
      tag.textContent = `Kuliner ${item.nama_daerah} · ${item.kategori}`;
      const title = document.createElement('h3');
      title.textContent = item.nama_makanan;
      const description = document.createElement('p');
      description.textContent = item.deskripsi;
      const meta = document.createElement('div');
      meta.className = 'recommendation-meta';
      const price = document.createElement('strong');
      const minimum = Number(item.harga_min || item.harga || 0);
      const maximum = Number(item.harga_max || item.harga || 0);
      const formatPrice = (amount) => `Rp ${amount.toLocaleString('id-ID')}`;
      price.textContent = minimum === maximum ? formatPrice(minimum) : `${formatPrice(minimum)} - ${formatPrice(maximum)}`;
      const rating = document.createElement('span');
      rating.textContent = `★ ${Number(item.rating).toFixed(1)} (${Number(item.jumlah_penilai).toLocaleString('id-ID')})`;
      meta.append(price, rating);
      body.append(tag, title, description, meta);
      card.append(media, body);
      return card;
    }

    function renderRecommendations() {
      const set = recommendationSets[recommendationIndex];
      const grid = document.getElementById('recommendation-grid');
      const emptyState = document.getElementById('recommendation-empty');
      const selectedArea = new URLSearchParams(window.location.search).get('daerah');
      const items = selectedArea ? set.items.filter((item) => item.slug_daerah === selectedArea) : set.items;

      document.getElementById('recommendation-title').textContent = set.title;
      document.getElementById('recommendation-description').textContent = set.description;
      document.getElementById('recommendation-position').textContent = `${recommendationIndex + 1} / ${recommendationSets.length}`;
      grid.replaceChildren(...items.map(createRecommendationCard));
      grid.hidden = items.length === 0;
      emptyState.hidden = items.length !== 0;
    }

    function changeRecommendation(direction) {
      recommendationIndex = (recommendationIndex + direction + recommendationSets.length) % recommendationSets.length;
      renderRecommendations();
    }

    document.getElementById('recommendation-prev').addEventListener('click', () => changeRecommendation(-1));
    document.getElementById('recommendation-next').addEventListener('click', () => changeRecommendation(1));
    renderRecommendations();
  </script>

  <section class="section section--muted community-recommendations">
    <div class="section__head">
      <div>
        <span class="eyebrow">Dari Komunitas</span>
        <h2>Rekomendasi Pengguna</h2>
        <p>Pilihan kuliner yang dibagikan dan sudah disetujui oleh tim CitaRasaJawaTimur.</p>
      </div>
    </div>
    <?php if ($userRecommendations && mysqli_num_rows($userRecommendations) > 0): ?>
      <div class="card-grid">
        <?php while ($row = mysqli_fetch_assoc($userRecommendations)): ?>
          <a class="card card--link community-card" href="rekomendasi_pengguna.php?id=<?php echo (int) $row['id']; ?>">
            <div class="card__media">
              <img src="<?php echo htmlspecialchars($row['gambar'] ?: 'https://images.unsplash.com/photo-15150031972-19c99c088f9d?w=900&q=80'); ?>" alt="<?php echo htmlspecialchars($row['nama_kuliner']); ?>">
              <span class="badge badge--rekomendasi">PILIHAN PENGGUNA</span>
            </div>
            <div class="card__body">
              <span class="card__tag"><?php echo htmlspecialchars($row['wilayah']); ?> · <?php echo htmlspecialchars($row['kategori']); ?></span>
              <h3><?php echo htmlspecialchars($row['nama_kuliner']); ?></h3>
              <p><?php echo htmlspecialchars($row['deskripsi']); ?></p>
              <p class="recommendation-location"><?php echo htmlspecialchars($row['lokasi']); ?></p>
              <div class="recommendation-meta"><strong>Rp <?php echo number_format((int) $row['harga_min'], 0, ',', '.'); ?> - Rp <?php echo number_format((int) $row['harga_max'], 0, ',', '.'); ?></strong><span>&#9733; <?php echo number_format((float) $row['rating'], 1); ?> / 5</span></div>
              <small class="community-card__author">Dibagikan oleh <?php echo htmlspecialchars($row['nama_pengguna']); ?></small>
            </div>
          </a>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="empty-state"><h3>Belum Ada Rekomendasi Komunitas</h3><p>Jadilah pengguna pertama yang membagikan kuliner favoritmu.</p></div>
    <?php endif; ?>
  </section>

  <?php include 'includes/footer.php'; ?>

</body>
</html>
