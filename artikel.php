<?php
$active_page = 'katalog';
include 'includes/koneksi.php';

$slug = trim($_GET['slug'] ?? '');
$article = get_site_article($conn, $slug);
if (!$article) {
    header('Location: index.php');
    exit;
}
$categoryLabel = $article['category'] === 'unik' ? 'Artikel Kuliner Jawa Timur' : 'Resep Kuliner Jawa Timur';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($article['title']); ?> — CitaRasaJawaTimur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css?v=20260924">
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <main class="article-page">
    <a class="article-page__back" href="index.php">&larr; Kembali ke Informasi Terbaru</a>
    <article class="article-page__content">
      <span class="eyebrow"><?php echo htmlspecialchars($categoryLabel); ?></span>
      <h1><?php echo htmlspecialchars($article['title']); ?></h1>
      <p class="article-page__excerpt"><?php echo htmlspecialchars($article['excerpt']); ?></p>
      <img class="article-page__image" src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
      <div class="article-page__body"><?php echo content_lines($article['body']); ?></div>
    </article>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>
