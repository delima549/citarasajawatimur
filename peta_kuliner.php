<?php
$active_page = 'peta';
include 'includes/koneksi.php';
$mapBanner = get_site_content($conn, 'map', 'banner');

$sql = "SELECT kt.nama_tempat, kt.alamat, k.nama_makanan, d.nama AS nama_daerah
        FROM kuliner_tempat kt
        JOIN kuliner k ON k.id = kt.kuliner_id
        JOIN daerah d ON d.id = k.daerah_id
        ORDER BY d.nama ASC, k.nama_makanan ASC";

$result = mysqli_query($conn, $sql);
$locations = [];

while ($row = mysqli_fetch_assoc($result)) {
  $mapsQuery = $row['nama_tempat'] . ', ' . $row['alamat'] . ', ' . $row['nama_daerah'] . ', Jawa Timur, Indonesia';
    $locations[] = [
        'nama_tempat' => $row['nama_tempat'],
        'alamat' => $row['alamat'],
        'nama_makanan' => $row['nama_makanan'],
    'nama_daerah' => $row['nama_daerah'],
    'google_maps_url' => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($mapsQuery)
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Peta UMKM — CitaRasaJawaTimur</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <link rel="stylesheet" href="css/style.css?v=20260924">
  <style>
    .peta-page {
      max-width: 1280px;
      margin: 0 auto;
      padding: 32px 32px 80px;
    }
    .peta-shell {
      display: grid;
      grid-template-columns: 320px 1fr;
      gap: 24px;
      align-items: start;
    }
    .peta-sidebar {
      background: #fff;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 20px;
      position: sticky;
      top: 24px;
    }
    .peta-sidebar h3 {
      font-size: 1.2rem;
      margin-bottom: 14px;
    }
    .peta-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
      max-height: 620px;
      overflow-y: auto;
      padding-right: 4px;
    }
    .peta-item {
      display: block;
      color: inherit;
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 12px 14px;
      background: #fbfffc;
      text-decoration: none;
      transition: border-color .15s ease, transform .15s ease, box-shadow .15s ease;
    }
    .peta-item:hover {
      border-color: var(--green-600);
      box-shadow: 0 5px 14px rgba(16, 94, 52, .1);
      transform: translateY(-1px);
    }
    .peta-item strong {
      display: block;
      margin-bottom: 6px;
      color: var(--green-800);
      font-size: 0.96rem;
    }
    .peta-item small {
      display: block;
      color: var(--ink-soft);
      line-height: 1.5;
    }
    #petaKulinerMap {
      width: 100%;
      height: 680px;
      min-height: 500px;
      border-radius: var(--radius);
      overflow: hidden;
      border: 1px solid var(--line);
      box-shadow: var(--shadow);
      background: #edf8f1;
    }
    @media (max-width: 980px) {
      .peta-shell {
        grid-template-columns: 1fr;
      }
      .peta-sidebar {
        position: static;
      }
      #petaKulinerMap {
        height: 500px;
      }
    }
  </style>
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <section class="page-banner page-banner--sm page-banner--rekomendasi">
    <div class="page-banner__overlay"></div>
    <div class="page-banner__inner">
      <span class="eyebrow eyebrow--light"><?php echo htmlspecialchars($mapBanner['label']); ?></span>
      <h1><?php echo content_title($mapBanner['title']); ?></h1>
      <p><?php echo htmlspecialchars($mapBanner['description']); ?></p>
    </div>
  </section>

  <main class="peta-page">
    <div class="peta-shell">
      <aside class="peta-sidebar">
        <span class="eyebrow">Daftar Lokasi</span>
        <h3>Tempat UMKM Pilihan</h3>
        <div class="peta-list">
          <?php foreach ($locations as $item): ?>
            <a class="peta-item" href="<?php echo htmlspecialchars($item['google_maps_url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="Buka lokasi <?php echo htmlspecialchars($item['nama_makanan']); ?> di Google Maps">
              <strong><?php echo htmlspecialchars($item['nama_makanan']); ?></strong>
              <small>
                <?php echo htmlspecialchars($item['nama_tempat']); ?><br>
                <?php echo htmlspecialchars($item['alamat']); ?><br>
                <?php echo htmlspecialchars($item['nama_daerah']); ?>
              </small>
              <small class="peta-item__maps-link">Buka di Google Maps &rarr;</small>
            </a>
          <?php endforeach; ?>
        </div>
      </aside>

      <div id="petaKulinerMap" aria-label="Peta lokasi kuliner"></div>
    </div>
  </main>

  <?php include 'includes/footer.php'; ?>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    const locations = <?php echo json_encode($locations, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP); ?>;
    const mapElement = document.getElementById('petaKulinerMap');

    const fallbackCoordinates = {
      'Kota Surabaya': [-7.2575, 112.7521],
      'Kota Malang': [-7.9819, 112.6274],
      'Kota Kediri': [-7.8169, 112.0135],
      'Kota Blitar': [-8.0956, 112.1659],
      'Kota Madiun': [-7.6293, 111.5230],
      'Kota Mojokerto': [-7.4703, 112.4407],
      'Kota Pasuruan': [-7.6453, 112.9042],
      'Kota Probolinggo': [-7.7528, 113.2111],
      'Kota Batu': [-7.8722, 112.5270],
      'Kabupaten Bangkalan': [-7.0459, 112.7355],
      'Kabupaten Banyuwangi': [-8.2192, 114.3691],
      'Kabupaten Blitar': [-8.1015, 112.1629],
      'Kabupaten Bojonegoro': [-7.1505, 111.8813],
      'Kabupaten Bondowoso': [-7.9140, 113.8215],
      'Kabupaten Gresik': [-7.1558, 112.6568],
      'Kabupaten Jember': [-8.1845, 113.6681],
      'Kabupaten Jombang': [-7.5453, 112.2349],
      'Kabupaten Kediri': [-7.8312, 112.0154],
      'Kabupaten Lamongan': [-7.1186, 112.4142],
      'Kabupaten Lumajang': [-8.1335, 113.2245],
      'Kabupaten Madiun': [-7.6158, 111.6533],
      'Kabupaten Magetan': [-7.6467, 111.3275],
      'Kabupaten Malang': [-8.1734, 112.6417],
      'Kabupaten Mojokerto': [-7.4738, 112.4367],
      'Kabupaten Nganjuk': [-7.6058, 111.9029],
      'Kabupaten Ngawi': [-7.4109, 111.4164],
      'Kabupaten Pacitan': [-8.1333, 111.0942],
      'Kabupaten Pamekasan': [-7.1566, 113.4724],
      'Kabupaten Pasuruan': [-7.7849, 112.7555],
      'Kabupaten Ponorogo': [-7.8682, 111.4627],
      'Kabupaten Probolinggo': [-7.7767, 113.2037],
      'Kabupaten Sampang': [-7.1969, 113.2418],
      'Kabupaten Sidoarjo': [-7.4477, 112.7189],
      'Kabupaten Situbondo': [-7.7098, 113.9971],
      'Kabupaten Sumenep': [-7.0195, 114.3264],
      'Kabupaten Trenggalek': [-8.0530, 111.7017],
      'Kabupaten Tuban': [-6.8978, 112.0640],
      'Kabupaten Tulungagung': [-8.0659, 111.9031],
      'Jawa Timur': [-7.5, 112.5]
    };

    if (mapElement && Array.isArray(locations) && locations.length) {
      const map = L.map('petaKulinerMap', {
        scrollWheelZoom: true,
        zoomControl: true,
        preferCanvas: true
      }).setView([-7.5, 112.5], 7);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);

      const preparedLocations = locations.map((item) => {
        const coords = fallbackCoordinates[item.nama_daerah] || fallbackCoordinates['Jawa Timur'];
        return {
          ...item,
          lat: coords[0],
          lon: coords[1]
        };
      });

      const markerLayer = L.layerGroup().addTo(map);

      preparedLocations.forEach((item) => {
        const marker = L.marker([item.lat, item.lon]).addTo(markerLayer);
        marker.bindPopup(`
          <strong>${item.nama_makanan}</strong><br>
          ${item.nama_tempat}<br>
          <small>${item.alamat}</small><br>
          <a href="${item.google_maps_url}" target="_blank" rel="noopener noreferrer">Buka di Google Maps &rarr;</a>
        `);
      });

      if (preparedLocations.length === 1) {
        map.setView([preparedLocations[0].lat, preparedLocations[0].lon], 12);
      } else {
        const bounds = L.latLngBounds(preparedLocations.map((item) => [item.lat, item.lon]));
        map.fitBounds(bounds.pad(0.25));
      }

      setTimeout(() => {
        map.invalidateSize();
      }, 200);
    }
  </script>
</body>
</html>
