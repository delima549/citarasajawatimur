<?php
$active_page = 'beranda';
include 'includes/koneksi.php';

$slug = $_GET['slug'] ?? '';
require_once 'includes/berita_data.php';
$beritaList = site_news_items();

$berita = null;
foreach ($beritaList as $item) {
  if ($item['slug'] === $slug) {
    $berita = $item;
    break;
  }
}

if (!$berita) {
  header('Location: berita.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($berita['title']); ?> — CitaRasaJawaTimur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .article-wrap {
      max-width: 980px;
      margin: 0 auto;
      padding: 48px 32px 80px;
    }
    .article-hero {
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(18, 76, 58, 0.12);
      margin-bottom: 30px;
    }
    .article-hero img {
      width: 100%;
      height: 420px;
      object-fit: cover;
      display: block;
    }
    .article-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 14px;
      align-items: center;
      margin-bottom: 20px;
      color: #355d49;
      font-size: 0.9rem;
      font-weight: 600;
    }
    .article-meta .tag {
      background: #edf8f1;
      color: #0f6d34;
      padding: 7px 12px;
      border-radius: 999px;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .article-content {
      background: #fff;
      border: 1px solid #dfece3;
      border-radius: 16px;
      padding: 30px;
    }
    .article-content h1 {
      font-size: clamp(1.8rem, 3vw, 2.7rem);
      line-height: 1.2;
      margin-bottom: 18px;
    }
    .article-content p {
      margin-bottom: 18px;
      font-size: 1rem;
      line-height: 1.8;
      color: #355d49;
    }
    .article-back {
      display: inline-block;
      margin-top: 24px;
      font-weight: 700;
      color: #0f6d34;
    }
    @media (max-width: 760px) {
      .article-wrap { padding: 28px 20px 60px; }
      .article-content { padding: 20px; }
      .article-hero img { height: 280px; }
    }
  </style>
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <div class="breadcrumb">
    <a href="index.php">Beranda</a>
    <span>/</span>
    <a href="berita.php">Berita</a>
    <span>/</span>
    <span class="breadcrumb__current"><?php echo htmlspecialchars($berita['title']); ?></span>
  </div>

  <main class="article-wrap">
    <div class="article-hero">
      <img src="<?php echo htmlspecialchars($berita['image']); ?>" alt="<?php echo htmlspecialchars($berita['title']); ?>">
    </div>

    <div class="article-content">
      <div class="article-meta">
        <span class="tag"><?php echo htmlspecialchars($berita['category']); ?></span>
        <span><?php echo htmlspecialchars($berita['date']); ?></span>
        <span>•</span>
        <span><?php echo htmlspecialchars($berita['author']); ?></span>
      </div>

      <h1><?php echo htmlspecialchars($berita['title']); ?></h1>

      <?php foreach ($berita['content'] as $paragraph): ?>
        <p><?php echo htmlspecialchars($paragraph); ?></p>
      <?php endforeach; ?>

      <a href="index.php" class="article-back">&larr; Kembali ke beranda utama</a>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>
