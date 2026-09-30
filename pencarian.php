<?php
$active_page = '';
include 'includes/koneksi.php';
require_once 'includes/berita_data.php';

function search_site_rows(mysqli $conn, string $sql, string $types, array $values): array
{
  $stmt = mysqli_prepare($conn, $sql);
  $bindings = [$stmt, $types];
  foreach ($values as $index => &$value) {
    $bindings[] = &$value;
  }
  unset($value);
  call_user_func_array('mysqli_stmt_bind_param', $bindings);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }
  return $rows;
}

$rawQuery = $_GET['q'] ?? '';
$query = is_string($rawQuery) ? trim(mb_substr($rawQuery, 0, 100)) : '';
$results = [];
if ($query !== '') {
  $like = '%' . $query . '%';
  $recipeOnly = mb_stripos($query, 'resep') !== false;
  if (!$recipeOnly) {
  $culinaries = search_site_rows($conn, "SELECT k.nama_makanan, k.slug, k.kategori, k.deskripsi, k.gambar, k.harga, k.harga_min, k.harga_max, k.rating, k.jumlah_penilai, k.is_rekomendasi, k.publikasi, d.nama AS nama_daerah
      FROM kuliner k
      JOIN daerah d ON d.id = k.daerah_id
      WHERE k.nama_makanan LIKE ? OR k.kategori LIKE ? OR k.wilayah LIKE ?
        OR k.deskripsi LIKE ? OR k.sejarah LIKE ? OR k.resep_bahan LIKE ? OR k.resep_langkah LIKE ?
        OR d.nama LIKE ? OR d.deskripsi LIKE ?
        OR EXISTS (SELECT 1 FROM kuliner_tempat t WHERE t.kuliner_id = k.id AND (t.nama_tempat LIKE ? OR t.alamat LIKE ? OR t.catatan LIKE ?))
      ORDER BY k.nama_makanan ASC LIMIT 20", str_repeat('s', 12), array_fill(0, 12, $like));
  foreach ($culinaries as $item) {
    $isRecommendation = (int) $item['is_rekomendasi'] === 1
      || stripos($item['publikasi'], 'Rekomendasi') !== false
      || stripos($item['publikasi'], 'Informasi Terbaru') !== false;
    $results['Kuliner'][] = [
      'title' => $item['nama_makanan'],
      'detail' => $item['nama_daerah'] . ' · ' . $item['kategori'],
      'summary' => $item['deskripsi'],
      'image' => $item['gambar'],
      'meta' => price_range_label($item) . ' · ★ ' . number_format((float) $item['rating'], 1) . ' (' . number_format((int) $item['jumlah_penilai'], 0, ',', '.') . ' penilaian)',
      'page' => $isRecommendation ? 'Rekomendasi Kuliner' : 'Katalog Kuliner',
      'url' => $isRecommendation
        ? 'rekomendasi.php?cari=' . rawurlencode($query)
        : 'kuliner.php?slug=' . rawurlencode($item['slug'])
    ];
  }

  $regions = search_site_rows($conn, "SELECT nama, jenis, slug, deskripsi, gambar FROM daerah
      WHERE nama LIKE ? OR jenis LIKE ? OR slug LIKE ? OR deskripsi LIKE ?
      ORDER BY nama ASC LIMIT 20", 'ssss', array_fill(0, 4, $like));
  foreach ($regions as $item) {
    $results['Daerah'][] = [
      'title' => $item['nama'],
      'detail' => $item['jenis'],
      'summary' => $item['deskripsi'],
      'image' => $item['gambar'],
      'meta' => 'Wilayah Jawa Timur',
      'page' => 'Katalog Kuliner',
      'url' => 'daerah.php?slug=' . rawurlencode($item['slug'])
    ];
  }

  ensure_site_content_table($conn);
  $pageUrls = [
    'home' => 'index.php',
    'about' => 'tentang.php',
    'catalog' => 'katalog.php',
    'recommendation' => 'rekomendasi.php',
    'map' => 'peta_kuliner.php'
  ];
  $pageTitles = [
    'home' => 'Beranda',
    'about' => 'Tentang Kami',
    'catalog' => 'Katalog Kuliner',
    'recommendation' => 'Rekomendasi Kuliner',
    'map' => 'Peta Kuliner'
  ];
  foreach (site_content_defaults() as $contentKey => $defaultContent) {
    [$pageKey, $sectionKey] = explode('.', $contentKey, 2);
    $item = array_merge(
      ['page_key' => $pageKey, 'section_key' => $sectionKey],
      get_site_content($conn, $pageKey, $sectionKey)
    );
    $searchableContent = implode(' ', array_filter([
      $item['label'] ?? '',
      $item['title'] ?? '',
      $item['description'] ?? '',
      $item['placeholder'] ?? '',
      $item['button_label'] ?? ''
    ], static fn($value) => is_string($value) && $value !== ''));
    if (mb_stripos($searchableContent, $query) === false) {
      continue;
    }
    $pageTitle = mb_stripos((string) ($item['label'] ?? ''), $query) !== false
      ? trim((string) $item['label'])
      : trim((string) ($item['title'] ?: $item['label'] ?: ucfirst($pageKey)));
    $summary = trim((string) ($item['description'] ?: $item['placeholder'] ?: $item['button_label']));
    $results['Halaman Situs'][] = [
      'title' => $pageTitle,
      'detail' => ucfirst($pageKey),
      'summary' => $summary,
      'image' => '',
      'meta' => 'Informasi halaman',
      'page' => $pageTitles[$pageKey] ?? 'Beranda',
      'url' => $pageUrls[$pageKey] ?? 'index.php'
    ];
  }
  }

  ensure_site_articles_table($conn);
  $articles = search_site_rows($conn, "SELECT title, category, excerpt, body, slug, image_url FROM site_articles
      WHERE title LIKE ? OR category LIKE ? OR excerpt LIKE ? OR body LIKE ?
      ORDER BY sort_order ASC, title ASC LIMIT 20", 'ssss', array_fill(0, 4, $like));
  foreach ($articles as $item) {
    $results['Artikel & Resep'][] = [
      'title' => $item['title'],
      'detail' => $item['category'] === 'unik' ? 'Artikel Kuliner' : 'Resep Kuliner',
      'summary' => $item['excerpt'],
      'image' => $item['image_url'],
      'meta' => 'Artikel & Resep Jawa Timur',
      'page' => 'Artikel & Resep',
      'url' => 'artikel.php?slug=' . rawurlencode($item['slug'])
    ];
  }

  if (!$recipeOnly) {
  $recommendations = search_site_rows($conn, "SELECT id, nama_kuliner, kategori, wilayah, lokasi, deskripsi, gambar, harga_min, harga_max, rating
      FROM user_recommendations WHERE status = 'Disetujui'
        AND (nama_kuliner LIKE ? OR kategori LIKE ? OR wilayah LIKE ? OR lokasi LIKE ? OR deskripsi LIKE ?)
      ORDER BY dibuat_pada DESC LIMIT 20", 'sssss', array_fill(0, 5, $like));
  foreach ($recommendations as $item) {
    $results['Rekomendasi Pengguna'][] = [
      'title' => $item['nama_kuliner'],
      'detail' => $item['wilayah'] . ' · ' . $item['kategori'],
      'summary' => $item['deskripsi'] . ' Lokasi: ' . $item['lokasi'],
      'image' => $item['gambar'] ?? '',
      'meta' => 'Rp ' . number_format((int) $item['harga_min'], 0, ',', '.') . ' - Rp ' . number_format((int) $item['harga_max'], 0, ',', '.') . ' · ★ ' . number_format((float) $item['rating'], 1),
      'page' => 'Rekomendasi Kuliner',
      'url' => 'rekomendasi_pengguna.php?id=' . (int) $item['id']
    ];
  }

  foreach (site_news_items() as $item) {
    $searchableNews = implode(' ', array_merge(
      [$item['title'], $item['excerpt'], $item['date'], $item['category'], $item['author']],
      $item['content']
    ));
    if (mb_stripos($searchableNews, $query) !== false) {
      $results['Berita'][] = [
        'title' => $item['title'],
        'detail' => $item['category'] . ' · ' . $item['date'],
        'summary' => $item['excerpt'],
        'image' => $item['image'],
        'meta' => $item['author'],
        'page' => 'Berita Kuliner',
        'url' => 'berita_detail.php?slug=' . rawurlencode($item['slug'])
      ];
    }
  }
  }
}
$resultCount = array_sum(array_map('count', $results));
if (($_GET['live'] ?? '') === '1') {
  $suggestions = [];
  foreach ($results as $items) {
    foreach ($items as $item) {
      $titlePosition = mb_stripos($item['title'], $query);
      $item['rank'] = $titlePosition === false ? 2 : ($titlePosition === 0 ? 0 : 1);
      $suggestions[] = $item;
    }
  }
  usort($suggestions, static fn($left, $right) => ($left['rank'] <=> $right['rank']) ?: strcasecmp($left['title'], $right['title']));
  foreach ($suggestions as &$suggestion) {
    unset($suggestion['rank']);
  }
  unset($suggestion);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(array_slice($suggestions, 0, 8), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pencarian — CitaRasaJawaTimur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css?v=20260924">
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <section class="page-banner page-banner--sm page-banner--rekomendasi">
    <div class="page-banner__overlay"></div>
    <div class="page-banner__inner">
      <span class="eyebrow eyebrow--light">Pencarian Situs</span>
      <h1><?php echo $query !== '' ? 'Hasil Pencarian' : 'Cari Informasi'; ?></h1>
      <p><?php echo $query !== '' ? htmlspecialchars($resultCount . ' hasil untuk “' . $query . '”', ENT_QUOTES, 'UTF-8') : 'Cari kuliner, daerah, artikel, resep, dan rekomendasi pengguna.'; ?></p>
      <form class="search-panel search-panel--flat" action="pencarian.php" method="get" role="search">
        <div class="search-panel__bar">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="m21 21-4.3-4.3"></path>
          </svg>
          <input type="search" name="q" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Cari rawon, Surabaya, resep..." aria-label="Kata kunci pencarian" required>
          <button type="submit">Cari</button>
        </div>
      </form>
    </div>
  </section>

  <?php if ($query === ''): ?>
    <section class="section"><div class="empty-state"><h2>Mulai Pencarian</h2><p>Ketik kata kunci untuk menemukan informasi di situs.</p></div></section>
  <?php elseif ($resultCount === 0): ?>
    <section class="section"><div class="empty-state"><h2>Belum Ada Hasil</h2><p>Coba kata kunci lain atau periksa ejaannya.</p></div></section>
  <?php else: ?>
    <?php foreach ($results as $group => $items): ?>
      <section class="section search-results">
        <div class="section__head"><div><span class="eyebrow"><?php echo htmlspecialchars($group, ENT_QUOTES, 'UTF-8'); ?></span><h2><?php echo htmlspecialchars($group, ENT_QUOTES, 'UTF-8'); ?></h2></div></div>
        <div class="card-grid">
          <?php foreach ($items as $item): ?>
            <a class="card card--link" href="<?php echo htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>">
              <?php if (!empty($item['image'])): ?>
                <div class="card__media"><img src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy"></div>
              <?php endif; ?>
              <div class="card__body">
                <span class="card__tag"><?php echo htmlspecialchars($item['detail'], ENT_QUOTES, 'UTF-8'); ?></span>
                <small class="search-result-page">Halaman: <?php echo htmlspecialchars($item['page'], ENT_QUOTES, 'UTF-8'); ?></small>
                <h3><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p><?php echo htmlspecialchars($item['summary'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php if (!empty($item['meta'])): ?><strong class="search-result-meta"><?php echo htmlspecialchars($item['meta'], ENT_QUOTES, 'UTF-8'); ?></strong><?php endif; ?>
                <span class="link-arrow link-arrow--sm">Lihat informasi &rarr;</span>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php include 'includes/footer.php'; ?>
</body>
</html>