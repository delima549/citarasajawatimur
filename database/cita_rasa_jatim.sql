-- =========================================================
-- Database: cita_rasa_jatim
-- Berisi 38 Kabupaten/Kota Jawa Timur beserta kuliner khasnya
-- (deskripsi, sejarah, resep, dan tempat rekomendasi).
--
-- CATATAN: Sejarah & resep pada data contoh ini ditulis secara
-- umum untuk keperluan demo. Silakan sesuaikan dengan referensi
-- lokal yang akurat sebelum dipublikasikan resmi.
--
-- Cara pakai: buat database 'cita_rasa_jatim' di phpMyAdmin,
-- lalu import file ini.
-- =========================================================

DROP TABLE IF EXISTS kuliner_tempat;
DROP TABLE IF EXISTS kuliner;
DROP TABLE IF EXISTS daerah;
DROP TABLE IF EXISTS user_recommendations;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS admin_users;
DROP TABLE IF EXISTS site_content;
DROP TABLE IF EXISTS site_articles;

CREATE TABLE daerah (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  jenis ENUM('Kota','Kabupaten') NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  gambar VARCHAR(500) NOT NULL,
  deskripsi TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO daerah (id, nama, jenis, slug, gambar, deskripsi) VALUES
(1, 'Kota Surabaya', 'Kota', 'kota-surabaya', 'https://images.unsplash.com/photo-1596895111956-bf1cf0599ce5?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kota Surabaya, salah satu kota di Jawa Timur.'),
(2, 'Kota Malang', 'Kota', 'kota-malang', 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kota Malang, salah satu kota di Jawa Timur.'),
(3, 'Kota Kediri', 'Kota', 'kota-kediri', 'https://images.unsplash.com/photo-1541089404510-5c9a779841fc?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kota Kediri, salah satu kota di Jawa Timur.'),
(4, 'Kota Blitar', 'Kota', 'kota-blitar', 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kota Blitar, salah satu kota di Jawa Timur.'),
(5, 'Kota Madiun', 'Kota', 'kota-madiun', 'https://images.unsplash.com/photo-1596895111956-bf1cf0599ce5?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kota Madiun, salah satu kota di Jawa Timur.'),
(6, 'Kota Mojokerto', 'Kota', 'kota-mojokerto', 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kota Mojokerto, salah satu kota di Jawa Timur.'),
(7, 'Kota Pasuruan', 'Kota', 'kota-pasuruan', 'https://images.unsplash.com/photo-1541089404510-5c9a779841fc?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kota Pasuruan, salah satu kota di Jawa Timur.'),
(8, 'Kota Probolinggo', 'Kota', 'kota-probolinggo', 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kota Probolinggo, salah satu kota di Jawa Timur.'),
(9, 'Kota Batu', 'Kota', 'kota-batu', 'https://images.unsplash.com/photo-1596895111956-bf1cf0599ce5?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kota Batu, salah satu kota di Jawa Timur.'),
(10, 'Kabupaten Bangkalan', 'Kabupaten', 'kab-bangkalan', 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Bangkalan, salah satu kabupaten di Jawa Timur.'),
(11, 'Kabupaten Banyuwangi', 'Kabupaten', 'kab-banyuwangi', 'https://images.unsplash.com/photo-1541089404510-5c9a779841fc?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Banyuwangi, salah satu kabupaten di Jawa Timur.'),
(12, 'Kabupaten Blitar', 'Kabupaten', 'kab-blitar', 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Blitar, salah satu kabupaten di Jawa Timur.'),
(13, 'Kabupaten Bojonegoro', 'Kabupaten', 'kab-bojonegoro', 'https://images.unsplash.com/photo-1596895111956-bf1cf0599ce5?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Bojonegoro, salah satu kabupaten di Jawa Timur.'),
(14, 'Kabupaten Bondowoso', 'Kabupaten', 'kab-bondowoso', 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Bondowoso, salah satu kabupaten di Jawa Timur.'),
(15, 'Kabupaten Gresik', 'Kabupaten', 'kab-gresik', 'https://images.unsplash.com/photo-1541089404510-5c9a779841fc?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Gresik, salah satu kabupaten di Jawa Timur.'),
(16, 'Kabupaten Jember', 'Kabupaten', 'kab-jember', 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Jember, salah satu kabupaten di Jawa Timur.'),
(17, 'Kabupaten Jombang', 'Kabupaten', 'kab-jombang', 'https://images.unsplash.com/photo-1596895111956-bf1cf0599ce5?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Jombang, salah satu kabupaten di Jawa Timur.'),
(18, 'Kabupaten Kediri', 'Kabupaten', 'kab-kediri', 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Kediri, salah satu kabupaten di Jawa Timur.'),
(19, 'Kabupaten Lamongan', 'Kabupaten', 'kab-lamongan', 'https://images.unsplash.com/photo-1541089404510-5c9a779841fc?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Lamongan, salah satu kabupaten di Jawa Timur.'),
(20, 'Kabupaten Lumajang', 'Kabupaten', 'kab-lumajang', 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Lumajang, salah satu kabupaten di Jawa Timur.'),
(21, 'Kabupaten Madiun', 'Kabupaten', 'kab-madiun', 'https://images.unsplash.com/photo-1596895111956-bf1cf0599ce5?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Madiun, salah satu kabupaten di Jawa Timur.'),
(22, 'Kabupaten Magetan', 'Kabupaten', 'kab-magetan', 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Magetan, salah satu kabupaten di Jawa Timur.'),
(23, 'Kabupaten Malang', 'Kabupaten', 'kab-malang', 'https://images.unsplash.com/photo-1541089404510-5c9a779841fc?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Malang, salah satu kabupaten di Jawa Timur.'),
(24, 'Kabupaten Mojokerto', 'Kabupaten', 'kab-mojokerto', 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Mojokerto, salah satu kabupaten di Jawa Timur.'),
(25, 'Kabupaten Nganjuk', 'Kabupaten', 'kab-nganjuk', 'https://images.unsplash.com/photo-1596895111956-bf1cf0599ce5?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Nganjuk, salah satu kabupaten di Jawa Timur.'),
(26, 'Kabupaten Ngawi', 'Kabupaten', 'kab-ngawi', 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Ngawi, salah satu kabupaten di Jawa Timur.'),
(27, 'Kabupaten Pacitan', 'Kabupaten', 'kab-pacitan', 'https://images.unsplash.com/photo-1541089404510-5c9a779841fc?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Pacitan, salah satu kabupaten di Jawa Timur.'),
(28, 'Kabupaten Pamekasan', 'Kabupaten', 'kab-pamekasan', 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Pamekasan, salah satu kabupaten di Jawa Timur.'),
(29, 'Kabupaten Pasuruan', 'Kabupaten', 'kab-pasuruan', 'https://images.unsplash.com/photo-1596895111956-bf1cf0599ce5?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Pasuruan, salah satu kabupaten di Jawa Timur.'),
(30, 'Kabupaten Ponorogo', 'Kabupaten', 'kab-ponorogo', 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Ponorogo, salah satu kabupaten di Jawa Timur.'),
(31, 'Kabupaten Probolinggo', 'Kabupaten', 'kab-probolinggo', 'https://images.unsplash.com/photo-1541089404510-5c9a779841fc?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Probolinggo, salah satu kabupaten di Jawa Timur.'),
(32, 'Kabupaten Sampang', 'Kabupaten', 'kab-sampang', 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Sampang, salah satu kabupaten di Jawa Timur.'),
(33, 'Kabupaten Sidoarjo', 'Kabupaten', 'kab-sidoarjo', 'https://images.unsplash.com/photo-1596895111956-bf1cf0599ce5?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Sidoarjo, salah satu kabupaten di Jawa Timur.'),
(34, 'Kabupaten Situbondo', 'Kabupaten', 'kab-situbondo', 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Situbondo, salah satu kabupaten di Jawa Timur.'),
(35, 'Kabupaten Sumenep', 'Kabupaten', 'kab-sumenep', 'https://images.unsplash.com/photo-1541089404510-5c9a779841fc?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Sumenep, salah satu kabupaten di Jawa Timur.'),
(36, 'Kabupaten Trenggalek', 'Kabupaten', 'kab-trenggalek', 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Trenggalek, salah satu kabupaten di Jawa Timur.'),
(37, 'Kabupaten Tuban', 'Kabupaten', 'kab-tuban', 'https://images.unsplash.com/photo-1596895111956-bf1cf0599ce5?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Tuban, salah satu kabupaten di Jawa Timur.'),
(38, 'Kabupaten Tulungagung', 'Kabupaten', 'kab-tulungagung', 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=900&q=80', 'Telusuri kekayaan rasa dan cerita kuliner khas dari Kabupaten Tulungagung, salah satu kabupaten di Jawa Timur.');

CREATE TABLE kuliner (
  id INT AUTO_INCREMENT PRIMARY KEY,
  daerah_id INT NOT NULL,
  nama_makanan VARCHAR(150) NOT NULL,
  slug VARCHAR(150) NOT NULL UNIQUE,
  wilayah VARCHAR(100) DEFAULT NULL,
  harga INT UNSIGNED NOT NULL DEFAULT 0,
  harga_min INT UNSIGNED NOT NULL DEFAULT 0,
  harga_max INT UNSIGNED NOT NULL DEFAULT 0,
  rating DECIMAL(2,1) NOT NULL DEFAULT 0.0,
  jumlah_penilai INT UNSIGNED NOT NULL DEFAULT 0,
  kategori VARCHAR(50) NOT NULL DEFAULT 'Makanan Berat',
  deskripsi TEXT NOT NULL,
  sejarah TEXT NOT NULL,
  resep_bahan TEXT NOT NULL,
  resep_langkah TEXT NOT NULL,
  gambar VARCHAR(500) NOT NULL,
  is_rekomendasi TINYINT(1) NOT NULL DEFAULT 0,
  publikasi VARCHAR(255) NOT NULL DEFAULT 'Katalog Kuliner',
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (daerah_id) REFERENCES daerah(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO kuliner (id, daerah_id, nama_makanan, slug, kategori, deskripsi, sejarah, resep_bahan, resep_langkah, gambar, is_rekomendasi) VALUES
(1, 1, 'Rujak Cingur', 'rujak-cingur', 'Makanan Berat', 'Irisan buah, sayuran, lontong, dan cingur (moncong sapi) yang disiram bumbu petis udang kental.', 'Rujak cingur dipercaya sudah ada sejak masa kolonial dan menjadi identitas kuliner Kota Surabaya. Namanya berasal dari bahan utamanya, cingur, yang berarti moncong dalam bahasa Jawa.', '200 g cingur sapi, rebus & potong
Timun, bengkuang, mangga muda, nanas
Kangkung & tauge rebus
3 sdm petis udang
2 buah pisang klutuk
Cabai rawit, kacang tanah goreng, gula merah, garam', 'Ulek kacang tanah, gula merah, cabai, dan petis hingga halus.
Tambahkan pisang klutuk, ulek rata hingga kental.
Campurkan seluruh buah, sayur, dan cingur ke dalam bumbu.
Aduk rata, sajikan dengan lontong dan kerupuk.', 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=900&q=80', 1),
(2, 1, 'Tahu Tek', 'tahu-tek', 'Makanan Ringan', 'Tahu dan lontong yang dipotong dengan gunting, disiram saus petis kacang gurih pedas.', 'Nama \'tahu tek\' konon berasal dari bunyi gunting \'tek tek tek\' saat pedagang memotong tahu dan lontong langsung di hadapan pembeli.', '2 buah tahu goreng
1 buah lontong
Tauge rebus
2 sdm petis udang
3 sdm kacang tanah goreng
Cabai rawit, bawang putih, kerupuk', 'Haluskan kacang tanah, bawang putih, cabai, dan petis dengan sedikit air.
Potong tahu dan lontong dengan gunting langsung di atas piring.
Tambahkan tauge, siram dengan bumbu kacang petis.
Taburi kerupuk, sajikan segera.', 'https://images.unsplash.com/photo-1626804475297-41608ea09aeb?w=900&q=80', 0),
(3, 1, 'Sate Klopo', 'sate-klopo', 'Makanan Berat', 'Sate daging sapi yang dibalur parutan kelapa sangrai sebelum dibakar, menghasilkan aroma gurih khas.', 'Sate klopo mulai populer di Surabaya sekitar pertengahan abad ke-20 dan menjadi salah satu variasi sate yang jarang ditemukan di daerah lain.', '300 g daging sapi has
150 g kelapa parut sangrai
Bumbu kuning: kunyit, ketumbar, bawang putih
Kecap manis
Sambal kecap sebagai pelengkap', 'Lumuri daging dengan bumbu kuning, diamkan 30 menit.
Balur dengan kelapa parut sangrai.
Tusuk dan bakar di atas bara sambil diolesi kecap.
Sajikan dengan lontong dan sambal kecap.', 'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=900&q=80', 0),
(4, 1, 'Lontong Balap', 'lontong-balap', 'Makanan Berat', 'Lontong dengan tauge, lentho (perkedel kacang tolo), dan tahu goreng disiram kuah gurih.', 'Nama \'balap\' konon muncul karena dulu para pedagang saling bergegas atau \'balapan\' memikul dagangannya menuju pasar.', 'Lontong secukupnya
100 g tauge rebus
2 buah lentho (perkedel kacang tolo)
2 buah tahu goreng
Kuah kaldu bawang gurih
Kecap manis, sambal petis', 'Susun lontong, tauge, tahu, dan lentho di mangkuk.
Siram dengan kuah kaldu panas.
Tambahkan kecap manis dan sambal petis sesuai selera.
Sajikan dengan sate kerang sebagai pelengkap.', 'https://images.unsplash.com/photo-1583224964978-2257b960c3d3?w=900&q=80', 0),
(5, 1, 'Rawon Setan', 'rawon-setan', 'Makanan Berkuah', 'Rawon dengan kuah hitam pekat dari kluwek, disajikan panas dengan sambal, tauge, dan kerupuk udang.', 'Julukan \'setan\' disematkan karena warung ini identik buka hingga larut malam dan rasa pedasnya yang menyengat.', '300 g daging sandung lamur
5 buah kluwek
Bawang merah, bawang putih, lengkuas, serai
Ketumbar, kunyit
Tauge pendek, telur asin, kerupuk udang', 'Rebus daging hingga empuk, sisihkan kaldunya.
Haluskan bumbu termasuk kluwek, tumis hingga harum.
Masukkan bumbu ke kaldu, masak bersama daging hingga meresap.
Sajikan panas dengan tauge, telur asin, dan kerupuk.', 'https://images.unsplash.com/photo-1529563021893-cc83c992d75d?w=900&q=80', 0),
(6, 2, 'Bakso Malang', 'bakso-malang', 'Makanan Berkuah', 'Semangkuk bakso dengan aneka isian seperti tahu, pangsit goreng, dan siomay, disiram kuah kaldu sapi gurih.', 'Bakso Malang berkembang dari akulturasi kuliner Tionghoa-Jawa dan menjadi salah satu ikon kuliner Kota Malang sejak pertengahan abad ke-20.', 'Bakso sapi halus & kasar
Tahu isi bakso
Pangsit goreng & pangsit rebus
Mi kuning & bihun
Kaldu sapi, daun bawang, seledri
Sambal & kecap', 'Didihkan kaldu sapi dengan daun bawang.
Rebus bakso hingga mengapung.
Susun mi, bihun, tahu, pangsit dalam mangkuk.
Siram kuah panas, taburi seledri dan sambal.', 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=900&q=80', 1),
(7, 2, 'Orem-Orem Malang', 'orem-orem-malang', 'Makanan Berkuah', 'Tempe dan kecambah kedelai dimasak dalam kuah santan encer sedikit pedas, disajikan dengan lontong.', 'Orem-orem merupakan sajian sarapan tradisional khas Malang yang mulai jarang ditemukan namun masih bertahan di beberapa warung legendaris.', '200 g tempe, potong dadu
Kecambah kedelai
Santan encer
Bumbu: bawang merah, bawang putih, cabai, lengkuas
Lontong sebagai pelengkap', 'Tumis bumbu halus hingga harum.
Masukkan tempe, aduk rata.
Tuang santan encer, masak hingga mendidih.
Sajikan bersama lontong dan taburan bawang goreng.', 'https://images.unsplash.com/photo-1567337710282-00832b415979?w=900&q=80', 0),
(8, 3, 'Nasi Pecel Kediri', 'nasi-pecel-kediri', 'Makanan Berat', 'Nasi pecel dengan sambal kacang khas Kediri yang cenderung lebih manis dan kental dibanding daerah lain.', 'Pecel Kediri berkembang sebagai sajian sarapan rumahan yang kemudian menjadi kuliner andalan kota ini, biasa disantap dengan nasi hangat dan peyek.', 'Sayur rebus: kenikir, kangkung, tauge, kacang panjang
200 g kacang tanah goreng
Gula merah, asam jawa, cabai
Bawang putih, kencur
Peyek kacang atau rempeyek udang', 'Haluskan kacang tanah, cabai, kencur, bawang putih, gula merah, dan asam jawa.
Tambahkan air matang secukupnya hingga kekentalan pas.
Siram sambal pecel di atas sayur rebus dan nasi.
Sajikan dengan peyek sebagai pelengkap.', 'https://images.unsplash.com/photo-1596797038530-2c107229654b?w=900&q=80', 0),
(9, 3, 'Tahu Pong Kediri', 'tahu-pong-kediri', 'Makanan Ringan', 'Tahu goreng kopong tanpa isi yang renyah di luar dan lembut berongga di dalam, disantap dengan bumbu petis.', 'Tahu pong menjadi camilan khas yang berkembang seiring tradisi produksi tahu di Kediri, salah satu sentra tahu terkenal di Jawa Timur.', '10 buah tahu pong
Petis udang
Cabai rawit, bawang putih
Kecap manis
Timun sebagai pelengkap', 'Goreng tahu pong hingga renyah keemasan.
Haluskan cabai, bawang putih, campur dengan petis dan kecap.
Sajikan tahu goreng dengan sambal petis dan irisan timun.', 'https://images.unsplash.com/photo-1555126634-323283e090fa?w=900&q=80', 0),
(10, 4, 'Pecel Pincuk Blitar', 'pecel-pincuk-blitar', 'Makanan Berat', 'Pecel yang disajikan beralaskan daun pisang berbentuk pincuk, dengan sambal kacang gurih pedas.', 'Penyajian dengan pincuk daun pisang dipercaya menjaga cita rasa pecel tetap otentik sejak dulu hingga sekarang.', 'Sayur rebus: bayam, kacang panjang, tauge
Sambal kacang khas Blitar
Rempeyek
Nasi hangat
Daun pisang untuk pincuk', 'Rebus seluruh sayuran hingga matang.
Buat sambal kacang dengan cabai, gula merah, dan bumbu pelengkap.
Alasi piring dengan daun pisang berbentuk pincuk.
Susun nasi, sayur, siram sambal, sajikan dengan rempeyek.', 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=900&q=80', 0),
(11, 5, 'Pecel Madiun', 'pecel-madiun', 'Makanan Berat', 'Perpaduan sayur rebus dengan sambal kacang kental yang menjadikan pecel Madiun salah satu kuliner paling ikonik di Jawa Timur.', 'Pecel Madiun dikenal luas berkat rasa sambal kacangnya yang khas serta banyaknya penjual pecel yang tersebar di seluruh penjuru kota, menjadikannya identitas kuliner Madiun.', 'Sayur rebus: kenikir, bayam, tauge, kacang panjang
250 g kacang tanah goreng
Cabai merah, kencur, daun jeruk
Gula merah, garam, asam jawa
Rempeyek kacang', 'Haluskan kacang tanah bersama cabai, kencur, dan daun jeruk.
Tambahkan gula merah, garam, dan asam jawa, ulek rata.
Seduh sedikit air panas agar sambal tercampur sempurna.
Siram di atas sayur rebus dan nasi, sajikan dengan rempeyek.', 'https://images.unsplash.com/photo-1476124369491-e7addf5db371?w=900&q=80', 1),
(12, 6, 'Onde-Onde Mojokerto', 'onde-onde-mojokerto', 'Makanan Ringan', 'Bola tepung ketan berlapis wijen dengan isian kumbu kacang hijau manis, digoreng hingga renyah.', 'Mojokerto dijuluki \'Kota Onde-Onde\' karena kudapan ini telah menjadi ciri khas dan oleh-oleh utama kota tersebut sejak lama.', '250 g tepung ketan
100 g kacang hijau kupas, kukus & haluskan
Gula pasir untuk isian
Wijen putih
Minyak untuk menggoreng', 'Campur tepung ketan dengan air hangat hingga kalis.
Isi adonan dengan kumbu kacang hijau manis, bulatkan.
Gulingkan pada wijen hingga menempel merata.
Goreng dengan api kecil hingga matang keemasan.', 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=900&q=80', 0),
(13, 7, 'Nasi Punel Pasuruan', 'nasi-punel-pasuruan', 'Makanan Berat', 'Nasi pulen yang disajikan dengan empal, rempeyek, sambal, dan sayur lodeh khas Pasuruan.', 'Disebut \'punel\' karena tekstur nasinya yang pulen dan lembut, menjadi menu sarapan favorit warga Pasuruan secara turun-temurun.', 'Nasi putih pulen
200 g empal daging sapi
Rempeyek kacang
Sayur lodeh atau sambal goreng
Sambal terasi', 'Masak nasi hingga pulen sempurna.
Goreng empal daging yang telah direbus berbumbu.
Siapkan sayur lodeh dan sambal terasi.
Sajikan nasi bersama seluruh lauk dan rempeyek.', 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=900&q=80', 0),
(14, 8, 'Nasi Petis Probolinggo', 'nasi-petis-probolinggo', 'Makanan Berat', 'Nasi dengan lauk daging suwir berbumbu petis kental serta sambal khas pesisir Probolinggo.', 'Sebagai kota pesisir, cita rasa kuliner Probolinggo banyak dipengaruhi olahan petis udang yang menjadi bumbu andalan warganya.', 'Nasi putih
200 g daging suwir
3 sdm petis udang
Bawang merah, bawang putih, cabai
Kerupuk sebagai pelengkap', 'Tumis bumbu halus hingga harum.
Masukkan daging suwir dan petis, aduk rata.
Masak hingga bumbu meresap dan sedikit mengering.
Sajikan di atas nasi hangat dengan kerupuk.', 'https://images.unsplash.com/photo-1626804475297-41608ea09aeb?w=900&q=80', 0),
(15, 9, 'Sate Kelinci Batu', 'sate-kelinci-batu', 'Makanan Berat', 'Sate berbahan daging kelinci empuk berbumbu kacang atau kecap, khas kawasan wisata dataran tinggi Batu.', 'Sate kelinci populer seiring berkembangnya Kota Batu sebagai kawasan peternakan kelinci dan destinasi wisata pegunungan sejak beberapa dekade lalu.', '300 g daging kelinci, potong dadu
Bumbu kacang atau bumbu kecap
Bawang putih, ketumbar
Kecap manis
Lontong sebagai pelengkap', 'Marinasi daging kelinci dengan bumbu halus selama 30 menit.
Tusuk daging pada tusuk sate.
Bakar sambil diolesi kecap hingga matang merata.
Sajikan dengan bumbu kacang atau kecap dan lontong.', 'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=900&q=80', 0),
(16, 10, 'Bebek Sinjay Bangkalan', 'bebek-sinjay-bangkalan', 'Makanan Berat', 'Bebek goreng renyah khas Bangkalan dengan sambal pencit (mangga muda) yang segar dan pedas.', 'Bebek Sinjay mulai dirintis sebagai usaha rumahan di Bangkalan dan berkembang menjadi salah satu kuliner bebek paling terkenal di Jawa Timur.', '1 ekor bebek, potong
Bumbu rebus: kunyit, lengkuas, daun salam
Sambal pencit: mangga muda, cabai rawit, terasi
Minyak untuk menggoreng', 'Rebus bebek dengan bumbu hingga empuk dan bumbu meresap.
Goreng bebek hingga kulit renyah keemasan.
Ulek sambal pencit dari mangga muda, cabai, dan terasi.
Sajikan bebek goreng dengan sambal pencit dan nasi hangat.', 'https://images.unsplash.com/photo-1583224964978-2257b960c3d3?w=900&q=80', 1),
(17, 11, 'Rujak Soto Banyuwangi', 'rujak-soto-banyuwangi', 'Makanan Berkuah', 'Perpaduan unik antara rujak sayur berbumbu kacang dengan siraman kuah soto daging, khas Banyuwangi.', 'Rujak soto lahir dari kreativitas warga Banyuwangi yang menggabungkan dua sajian populer, rujak dan soto, menjadi satu mangkuk yang khas.', 'Sayur & buah untuk rujak: kacang panjang, tauge, mangga muda
Bumbu kacang rujak
Kuah soto daging sapi
Jeroan atau daging suwir
Lontong', 'Siapkan rujak dengan bumbu kacang seperti biasa.
Masak kuah soto dengan bumbu rempah dan daging.
Tuang kuah soto panas ke atas rujak yang telah disiapkan.
Tambahkan lontong dan daging suwir, sajikan segera.', 'https://images.unsplash.com/photo-1529563021893-cc83c992d75d?w=900&q=80', 1),
(18, 11, 'Sego Tempong Banyuwangi', 'sego-tempong-banyuwangi', 'Makanan Berat', 'Nasi dengan aneka lauk dan sayur rebus, disiram sambal super pedas yang membuat penikmatnya seolah \'ditempong\' (ditampar).', 'Nama \'tempong\' diambil dari bahasa Osing yang berarti \'tampar\', menggambarkan sensasi pedas sambalnya yang menampar lidah.', 'Nasi putih
Sayur rebus: kenikir, kemangi, terong
Ikan asin atau ayam goreng
Tahu tempe goreng
Sambal tempong: cabai rawit, tomat, terasi', 'Goreng lauk pauk seperti ikan asin, tahu, dan tempe.
Rebus sayuran hingga matang.
Ulek sambal tempong dari cabai rawit, tomat, dan terasi hingga kasar.
Sajikan nasi dengan lauk, sayur, dan sambal tempong pedas.', 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=900&q=80', 0),
(19, 12, 'Wajik Kletik Blitar', 'wajik-kletik-blitar', 'Makanan Ringan', 'Camilan manis dari ketan dan gula kelapa yang dimasak hingga kalis dan legit, oleh-oleh khas Blitar.', 'Wajik kletik telah menjadi oleh-oleh turun-temurun dari Kabupaten Blitar, terutama di kawasan sekitar makam Bung Karno.', '500 g beras ketan, rendam & kukus
300 g gula merah
200 ml santan kental
Daun pandan
Sedikit garam', 'Masak gula merah dengan santan dan pandan hingga larut.
Masukkan ketan kukus, aduk hingga bumbu meresap.
Masak dengan api kecil sambil terus diaduk hingga kalis.
Cetak dan potong setelah dingin.', 'https://images.unsplash.com/photo-1567337710282-00832b415979?w=900&q=80', 0),
(20, 13, 'Ledre Bojonegoro', 'ledre-bojonegoro', 'Makanan Ringan', 'Kue gulung tipis renyah berbahan tepung beras dan pisang raja, beraroma khas dan manis legit.', 'Ledre menjadi oleh-oleh khas Bojonegoro yang dipercaya sudah dibuat sejak lama oleh masyarakat lokal sebagai camilan saat panen pisang raja melimpah.', '200 g tepung beras
2 buah pisang raja, haluskan
100 ml santan
Gula pasir, vanili
Sedikit garam', 'Campur tepung beras, santan, pisang halus, gula, dan garam hingga rata.
Tuang adonan tipis di atas wajan datar panas.
Masak hingga matang lalu gulung selagi hangat.
Keringkan hingga renyah sebelum dikemas.', 'https://images.unsplash.com/photo-1596797038530-2c107229654b?w=900&q=80', 0),
(21, 14, 'Tape Bondowoso', 'tape-bondowoso', 'Makanan Ringan', 'Fermentasi singkong khas Bondowoso yang manis dan legit, menjadi oleh-oleh favorit dari Jawa Timur bagian timur.', 'Bondowoso dijuluki \'Kota Tape\' karena hampir seluruh wilayahnya memproduksi tape singkong berkualitas yang sudah dikenal sejak lama.', '1 kg singkong, kupas & kukus
Ragi tape secukupnya
Daun pisang untuk membungkus', 'Kukus singkong hingga matang, dinginkan hingga suam-suam kuku.
Taburi ragi tape secara merata.
Bungkus dengan daun pisang, simpan dalam wadah tertutup.
Fermentasikan selama 2-3 hari hingga manis dan lembut.', 'https://images.unsplash.com/photo-1555126634-323283e090fa?w=900&q=80', 0),
(22, 15, 'Nasi Krawu', 'nasi-krawu', 'Makanan Berat', 'Nasi dengan suwiran daging sapi berbumbu khas, disajikan bersama sambal terasi dan serundeng kelapa.', 'Nasi krawu berasal dari tradisi masyarakat Gresik yang biasa disajikan pada acara-acara khusus sebelum akhirnya menjadi kuliner harian yang populer.', 'Nasi putih pulen
200 g daging sapi suwir
Serundeng kelapa merah & putih
Sambal terasi
Bumbu rempah: ketumbar, kunyit, lengkuas', 'Rebus daging dengan bumbu rempah hingga empuk lalu suwir.
Buat serundeng kelapa dengan bumbu manis gurih.
Ulek sambal terasi segar.
Sajikan nasi dengan daging suwir, serundeng, dan sambal.', 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=900&q=80', 1),
(23, 16, 'Suwar-Suwir Jember', 'suwar-suwir-jember', 'Makanan Ringan', 'Camilan manis kenyal berbahan dasar tape singkong yang dipadukan dengan kacang, oleh-oleh khas Jember.', 'Suwar-suwir dikembangkan sebagai olahan lanjutan dari tape Jember, memanfaatkan hasil fermentasi singkong menjadi camilan tahan lama.', '500 g tape singkong
200 g gula pasir
100 g kacang tanah cincang
Vanili secukupnya', 'Haluskan tape singkong hingga lembut.
Masak bersama gula sambil terus diaduk hingga kalis.
Tambahkan kacang cincang dan vanili, aduk rata.
Cetak memanjang, potong setelah agak dingin.', 'https://images.unsplash.com/photo-1476124369491-e7addf5db371?w=900&q=80', 0),
(24, 17, 'Sate Bebek Jombang', 'sate-bebek-jombang', 'Makanan Berat', 'Sate daging bebek empuk berbumbu rempah dan kecap manis, khas kuliner Jombang.', 'Sate bebek berkembang di Jombang seiring banyaknya peternakan bebek lokal, menjadikan sajian ini akrab dengan warga sekitar.', '1 ekor bebek, potong dadu
Bumbu rebus: kunyit, lengkuas, daun salam
Kecap manis
Bawang merah, bawang putih
Lontong sebagai pelengkap', 'Rebus daging bebek dengan bumbu hingga empuk dan tidak amis.
Tusuk daging pada tusuk sate.
Bakar sambil diolesi kecap manis hingga matang.
Sajikan dengan sambal kecap dan lontong.', 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=900&q=80', 0),
(25, 18, 'Tahu Takwa Kediri', 'tahu-takwa-kediri', 'Makanan Ringan', 'Tahu kuning padat khas Kediri dengan tekstur lebih kenyal, cocok dimakan langsung atau digoreng.', 'Tahu takwa telah diproduksi turun-temurun di Kediri dan menjadi oleh-oleh khas yang identik dengan kota tersebut.', '10 buah tahu takwa
Minyak untuk menggoreng
Cabai rawit dan kecap sebagai cocolan', 'Goreng tahu takwa hingga permukaan kecoklatan.
Tiriskan hingga minyak berkurang.
Sajikan hangat dengan cabai rawit dan kecap manis.', 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=900&q=80', 0),
(26, 19, 'Soto Ayam Lamongan', 'soto-ayam-lamongan', 'Makanan Berkuah', 'Soto ayam khas Lamongan dengan kuah bening yang gurih, suwiran ayam, dan taburan koya yang membuatnya menjadi favorit di Jawa Timur.', 'Soto Lamongan dikenal luas berkat taburan koya (bubuk kerupuk udang dan bawang putih) yang menjadi ciri khasnya dan kini populer hingga ke luar Jawa Timur.', '1 ekor ayam kampung
Bumbu: kunyit, ketumbar, bawang putih, jahe
Koya: kerupuk udang & bawang putih goreng, haluskan
Soun, kol, tauge
Jeruk nipis, sambal', 'Rebus ayam dengan bumbu halus hingga matang, suwir dagingnya.
Saring kaldu agar bening, panaskan kembali.
Susun soun, kol, tauge, dan ayam suwir dalam mangkuk.
Siram kuah panas, taburi koya, sajikan dengan jeruk nipis dan sambal.', 'https://images.unsplash.com/photo-1626804475297-41608ea09aeb?w=900&q=80', 1),
(27, 20, 'Sale Pisang Lumajang', 'sale-pisang-lumajang', 'Makanan Ringan', 'Pisang yang dikeringkan lalu digoreng dengan balutan tepung tipis, renyah dan manis alami.', 'Lumajang dikenal sebagai penghasil pisang berkualitas, sehingga olahan sale pisang berkembang menjadi camilan khas daerah ini.', '1 sisir pisang raja/kepok, iris tipis memanjang
100 g tepung beras
50 g tepung terigu
Gula pasir, garam
Minyak untuk menggoreng', 'Jemur irisan pisang hingga agak layu.
Campur tepung beras, terigu, gula, garam, dan air hingga jadi adonan celup.
Celupkan pisang ke adonan tepung.
Goreng hingga keemasan dan renyah.', 'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=900&q=80', 0),
(28, 21, 'Brem Madiun', 'brem-madiun', 'Makanan Ringan', 'Camilan manis legit dari fermentasi sari ketan yang dicetak dan dikeringkan hingga meleleh di mulut.', 'Brem Madiun merupakan hasil olahan tradisional yang telah diproduksi turun-temurun oleh masyarakat Kabupaten Madiun sebagai oleh-oleh khas.', '1 kg beras ketan, rendam & kukus
Ragi tape
Gula pasir secukupnya', 'Fermentasikan ketan kukus dengan ragi selama beberapa hari hingga berair.
Peras dan saring sarinya, masak hingga mengental.
Aduk terus dengan gula hingga adonan mengeras di pinggir wajan.
Cetak memanjang dan keringkan hingga padat.', 'https://images.unsplash.com/photo-1583224964978-2257b960c3d3?w=900&q=80', 0),
(29, 22, 'Sate Ayam Magetan', 'sate-ayam-magetan', 'Makanan Berat', 'Sate ayam berbumbu kacang gurih dengan potongan daging yang empuk, khas kawasan Telaga Sarangan.', 'Sate ayam Magetan populer seiring berkembangnya kawasan wisata Telaga Sarangan sebagai destinasi wisata dataran tinggi di Jawa Timur.', '300 g daging ayam, potong dadu
Bumbu kacang: kacang tanah goreng, cabai, gula merah
Kecap manis
Bawang merah, bawang putih
Lontong sebagai pelengkap', 'Tusuk daging ayam pada tusuk sate.
Bakar sambil diolesi kecap hingga matang merata.
Haluskan bumbu kacang dengan sedikit air hingga kental.
Sajikan sate dengan siraman bumbu kacang dan lontong.', 'https://images.unsplash.com/photo-1529563021893-cc83c992d75d?w=900&q=80', 0),
(30, 23, 'Keripik Tempe Sanan Malang', 'keripik-tempe-sanan-malang', 'Makanan Ringan', 'Tempe iris tipis yang digoreng renyah dengan balutan tepung berbumbu, oleh-oleh khas Malang.', 'Kampung Sanan di Malang dikenal sebagai sentra produksi tempe dan keripik tempe sejak beberapa generasi, menjadikannya ikon oleh-oleh kota ini.', '10 lembar tempe, iris tipis
100 g tepung beras
50 g tepung terigu
Bawang putih, ketumbar, kemiri
Daun jeruk', 'Haluskan bumbu, campur dengan tepung dan air hingga jadi adonan encer.
Iris daun jeruk halus, campurkan ke adonan.
Celupkan irisan tempe ke adonan tepung.
Goreng hingga kering dan renyah keemasan.', 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=900&q=80', 0),
(31, 24, 'Sambel Wader Mojokerto', 'sambel-wader-mojokerto', 'Makanan Berat', 'Ikan wader goreng renyah disajikan bersama sambal pedas segar, khas kuliner rumahan Mojokerto.', 'Ikan wader banyak ditemukan di sungai-sungai kawasan Mojokerto, sehingga masyarakat setempat mengolahnya menjadi lauk sehari-hari yang digemari.', '250 g ikan wader
Tepung untuk melapisi
Cabai rawit, tomat, terasi
Bawang merah, bawang putih
Nasi hangat sebagai pelengkap', 'Lumuri ikan wader dengan sedikit tepung, goreng hingga kering.
Ulek cabai, tomat, terasi, bawang merah, dan bawang putih.
Tumis sambal sebentar hingga harum.
Sajikan ikan wader goreng dengan sambal dan nasi hangat.', 'https://images.unsplash.com/photo-1567337710282-00832b415979?w=900&q=80', 0),
(32, 25, 'Nasi Becek Nganjuk', 'nasi-becek-nganjuk', 'Makanan Berkuah', 'Nasi dengan kuah gulai kambing atau ayam yang gurih pedas, khas kuliner Nganjuk.', 'Nasi becek menjadi hidangan khas yang biasa disajikan dalam acara hajatan warga Nganjuk sebelum berkembang menjadi kuliner harian.', '300 g daging kambing atau ayam
Bumbu gulai: ketumbar, kunyit, jahe, lengkuas
Santan
Nasi putih
Bawang goreng sebagai taburan', 'Tumis bumbu halus hingga harum.
Masukkan daging, aduk hingga berubah warna.
Tuang santan, masak dengan api kecil hingga daging empuk dan kuah mengental.
Sajikan kuah dan daging di atas nasi, taburi bawang goreng.', 'https://images.unsplash.com/photo-1596797038530-2c107229654b?w=900&q=80', 0),
(33, 26, 'Tepo Tahu Ngawi', 'tepo-tahu-ngawi', 'Makanan Berat', 'Ketupat dan tahu goreng disiram bumbu kacang gurih pedas khas Ngawi, mirip lontong tahu namun dengan cita rasa lokal.', 'Tepo tahu menjadi menu sarapan favorit masyarakat Ngawi yang berkembang dari tradisi kuliner rumahan setempat.', 'Ketupat, potong-potong
4 buah tahu goreng
Bumbu kacang: kacang tanah, cabai, gula merah
Tauge rebus
Kecap manis, bawang goreng', 'Haluskan kacang tanah, cabai, dan gula merah hingga kental.
Susun potongan ketupat, tahu, dan tauge di piring.
Siram dengan bumbu kacang dan kecap manis.
Taburi bawang goreng, sajikan segera.', 'https://images.unsplash.com/photo-1555126634-323283e090fa?w=900&q=80', 0),
(34, 27, 'Nasi Tiwul Pacitan', 'nasi-tiwul-pacitan', 'Makanan Berat', 'Nasi pengganti dari singkong yang dikeringkan dan dikukus, disajikan dengan lauk sederhana khas pesisir selatan.', 'Tiwul awalnya merupakan makanan pokok pengganti nasi pada masa paceklik, kini menjadi kuliner khas yang dilestarikan di Pacitan.', '500 g gaplek (singkong kering), rendam
Kelapa parut kukus
Gula merah secukupnya
Garam secukupnya', 'Haluskan gaplek yang telah direndam hingga menjadi butiran kasar.
Kukus selama 30-40 menit hingga matang.
Campur dengan kelapa parut dan sedikit garam.
Sajikan dengan gula merah cair atau lauk pauk sederhana.', 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=900&q=80', 0),
(35, 28, 'Nasi Serpang Pamekasan', 'nasi-serpang-pamekasan', 'Makanan Berat', 'Nasi dengan aneka lauk khas Madura seperti serundeng, telur asin, dan sate kerang dalam satu piring lengkap.', 'Nasi serpang merupakan sajian sarapan khas Pamekasan yang menyatukan berbagai lauk kecil khas Madura dalam satu porsi.', 'Nasi putih
Serundeng kelapa
Telur asin
Sate kerang atau daging
Sambal petis khas Madura', 'Siapkan nasi putih hangat.
Susun serundeng, telur asin, dan sate kerang di sekeliling nasi.
Tambahkan sambal petis sesuai selera.
Sajikan dalam satu piring lengkap layaknya nasi campur.', 'https://images.unsplash.com/photo-1476124369491-e7addf5db371?w=900&q=80', 0),
(36, 29, 'Rawon Nguling Pasuruan', 'rawon-nguling-pasuruan', 'Makanan Berkuah', 'Rawon dengan kuah kluwek hitam pekat khas kawasan Nguling, gurih dengan aroma rempah yang kuat.', 'Nama rawon Nguling diambil dari kecamatan asalnya di Pasuruan yang terkenal dengan racikan bumbu rawon turun-temurun.', '300 g daging sandung lamur
5 buah kluwek
Bawang merah, bawang putih, lengkuas, serai
Ketumbar, kunyit
Tauge pendek, kerupuk udang', 'Rebus daging hingga empuk, sisihkan kaldu.
Haluskan bumbu termasuk kluwek, tumis hingga harum.
Masukkan bumbu ke kaldu, masak bersama daging hingga meresap.
Sajikan panas dengan tauge dan kerupuk.', 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=900&q=80', 0),
(37, 30, 'Sate Ayam Ponorogo', 'sate-ayam-ponorogo', 'Makanan Berat', 'Potongan daging ayam yang dibakar dengan bumbu kacang khas, dikenal dengan potongan dagingnya yang besar dan empuk.', 'Sate Ponorogo dikenal berbeda dari sate pada umumnya karena daging dipotong memanjang menyerupai fillet, bukan dadu kecil, menjadikannya lebih terasa gurih dan empuk.', '400 g daging ayam fillet, iris memanjang
Bumbu kacang: kacang tanah, cabai, gula merah
Kecap manis
Bawang putih, ketumbar
Lontong sebagai pelengkap', 'Marinasi daging ayam dengan bumbu kecap dan ketumbar.
Tusuk daging pada tusuk sate memanjang.
Bakar sambil diolesi kecap hingga matang merata.
Sajikan dengan siraman bumbu kacang kental dan lontong.', 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=900&q=80', 1),
(38, 31, 'Nasi Jagung Probolinggo', 'nasi-jagung-probolinggo', 'Makanan Berat', 'Nasi dari campuran jagung dan beras yang disajikan dengan lauk sederhana khas pedesaan Probolinggo.', 'Nasi jagung merupakan makanan pokok masyarakat pesisir dan pegunungan Probolinggo sejak lama, sebagai alternatif nasi beras saat panen jagung melimpah.', '200 g beras jagung (jagung pipil giling kasar)
100 g beras putih
Ikan asin goreng
Sayur urap
Sambal terasi', 'Rendam beras jagung selama beberapa jam.
Campur dengan beras putih, kukus hingga matang.
Siapkan ikan asin goreng, sayur urap, dan sambal terasi.
Sajikan nasi jagung hangat bersama seluruh lauk pelengkap.', 'https://images.unsplash.com/photo-1626804475297-41608ea09aeb?w=900&q=80', 0),
(39, 32, 'Bebek Songkem Sampang', 'bebek-songkem-sampang', 'Makanan Berat', 'Bebek utuh yang dibumbui dan dikukus dalam bungkusan daun pisang hingga empuk, khas kuliner Madura.', 'Nama \'songkem\' berasal dari posisi bebek yang ditata menunduk seperti sungkem sebelum dikukus, menjadi ciri khas penyajian kuliner ini.', '1 ekor bebek utuh
Cabai rawit utuh
Bawang merah, bawang putih
Garam, sedikit air asam
Daun pisang untuk membungkus', 'Lumuri bebek dengan bumbu halus hingga merata.
Tata bebek menunduk, bungkus rapat dengan daun pisang.
Kukus selama 2-3 jam hingga daging empuk.
Sajikan utuh dengan sambal sebagai pelengkap.', 'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=900&q=80', 0),
(40, 33, 'Kupang Lontong Sidoarjo', 'kupang-lontong-sidoarjo', 'Makanan Berkuah', 'Kerang kupang kecil dimasak kuah petis, disajikan dengan lontong, lentho, dan sate kerang.', 'Kupang lontong berkembang dari melimpahnya hasil laut kupang di kawasan pesisir Sidoarjo, menjadi kuliner khas yang digemari hingga kini.', '250 g kupang (kerang kecil)
3 sdm petis udang
Lontong secukupnya
2 buah lentho
Sate kerang, jeruk nipis, cabai rawit', 'Rebus kupang sebentar hingga matang, jangan terlalu lama agar tidak alot.
Larutkan petis dengan sedikit air kaldu kupang.
Susun lontong dan lentho dalam mangkuk, tuang kupang berkuah petis.
Sajikan dengan sate kerang, jeruk nipis, dan sambal.', 'https://images.unsplash.com/photo-1583224964978-2257b960c3d3?w=900&q=80', 0),
(41, 34, 'Sate Lalat Situbondo', 'sate-lalat-situbondo', 'Makanan Berat', 'Sate daging berukuran kecil-kecil menyerupai lalat, dibakar dengan bumbu kecap khas Situbondo.', 'Nama \'sate lalat\' merujuk pada ukuran potongan dagingnya yang sangat kecil, sehingga satu tusuk berisi banyak potongan mini.', '300 g daging ayam atau kambing, potong sangat kecil
Kecap manis
Bawang merah, bawang putih
Sambal kecap sebagai pelengkap', 'Potong daging sekecil mungkin menyerupai dadu mini.
Tusuk banyak potongan kecil dalam satu tusuk sate.
Bakar sambil diolesi kecap hingga matang.
Sajikan dengan sambal kecap dan lontong.', 'https://images.unsplash.com/photo-1529563021893-cc83c992d75d?w=900&q=80', 0),
(42, 35, 'Kaldu Kokot Sumenep', 'kaldu-kokot-sumenep', 'Makanan Berkuah', 'Sup kaki sapi (kokot) dengan kuah kuning gurih berempah, khas kuliner Pulau Madura bagian timur.', 'Kaldu kokot menjadi hidangan khas yang biasa dinikmati sebagai menu penambah stamina oleh masyarakat Sumenep.', '500 g kikil/kaki sapi
Bumbu kuning: kunyit, ketumbar, jahe, lengkuas
Serai, daun jeruk
Bawang goreng sebagai taburan
Nasi atau lontong sebagai pelengkap', 'Rebus kikil hingga empuk, sisihkan kaldunya.
Tumis bumbu halus bersama serai dan daun jeruk hingga harum.
Masukkan bumbu ke kaldu, masak hingga kuah gurih dan meresap.
Sajikan panas dengan taburan bawang goreng.', 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=900&q=80', 0),
(43, 36, 'Sambal Tumpang Trenggalek', 'sambal-tumpang-trenggalek', 'Makanan Berat', 'Sambal berbahan tempe semangit (tempe yang difermentasi lebih lama) dimasak dengan santan pedas gurih.', 'Sambal tumpang menjadi hidangan khas rumahan di Trenggalek yang memanfaatkan tempe semangit agar tidak terbuang, diolah menjadi sajian lezat.', '200 g tempe semangit
Santan
Cabai merah, cabai rawit
Bawang merah, bawang putih, lengkuas
Nasi dan sayur rebus sebagai pelengkap', 'Rebus tempe semangit hingga lunak, haluskan kasar.
Tumis bumbu halus hingga harum.
Masukkan tempe dan santan, masak hingga mengental.
Sajikan dengan nasi hangat dan sayur rebus.', 'https://images.unsplash.com/photo-1567337710282-00832b415979?w=900&q=80', 0),
(44, 37, 'Pindang Bandeng Tuban', 'pindang-bandeng-tuban', 'Makanan Berkuah', 'Ikan bandeng yang dimasak dengan bumbu rempah dan belimbing wuluh, menghasilkan cita rasa asam segar khas pesisir.', 'Sebagai daerah pesisir utara, Tuban mengolah bandeng menjadi pindang berkuah segar yang menjadi sajian favorit masyarakat setempat.', '2 ekor ikan bandeng
5 buah belimbing wuluh
Cabai rawit, bawang merah, bawang putih
Kunyit, lengkuas, serai
Garam dan gula secukupnya', 'Bersihkan ikan bandeng, potong sesuai selera.
Rebus air bersama bumbu dan belimbing wuluh hingga mendidih.
Masukkan ikan, masak dengan api sedang hingga matang.
Sajikan panas dengan nasi putih.', 'https://images.unsplash.com/photo-1596797038530-2c107229654b?w=900&q=80', 1),
(45, 38, 'Lodho Ayam Tulungagung', 'lodho-ayam-tulungagung', 'Makanan Berkuah', 'Ayam kampung dimasak dengan santan dan bumbu rempah pedas, biasa disajikan saat acara syawalan.', 'Lodho ayam merupakan hidangan khas yang identik dengan tradisi \'kupatan\' atau syawalan di Tulungagung dan sekitarnya.', '1 ekor ayam kampung, potong
Santan kental
Cabai merah, cabai rawit
Bawang merah, bawang putih, kemiri
Serai, daun jeruk, lengkuas', 'Bakar sebentar ayam agar aromanya lebih harum, sisihkan.
Tumis bumbu halus bersama serai dan daun jeruk hingga harum.
Masukkan ayam dan santan, masak dengan api kecil hingga bumbu meresap.
Sajikan panas bersama lontong atau ketupat.', 'https://images.unsplash.com/photo-1555126634-323283e090fa?w=900&q=80', 0);

UPDATE kuliner
SET wilayah = CASE id
  WHEN 1 THEN 'Surabaya Pusat'
  WHEN 2 THEN 'Surabaya Utara'
  WHEN 3 THEN 'Surabaya Timur'
  WHEN 4 THEN 'Surabaya Selatan'
  WHEN 5 THEN 'Surabaya Barat'
END
WHERE id BETWEEN 1 AND 5;

UPDATE kuliner
SET harga = CASE id
  WHEN 1 THEN 25000
  WHEN 2 THEN 15000
  WHEN 3 THEN 22000
  WHEN 4 THEN 18000
  WHEN 5 THEN 30000
END,
rating = CASE id
  WHEN 1 THEN 4.8
  WHEN 2 THEN 4.6
  WHEN 3 THEN 4.7
  WHEN 4 THEN 4.5
  WHEN 5 THEN 4.9
END,
jumlah_penilai = CASE id
  WHEN 1 THEN 284
  WHEN 2 THEN 192
  WHEN 3 THEN 231
  WHEN 4 THEN 167
  WHEN 5 THEN 356
END
WHERE id BETWEEN 1 AND 5;

CREATE TABLE kuliner_tempat (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kuliner_id INT NOT NULL,
  nama_tempat VARCHAR(150) NOT NULL,
  alamat VARCHAR(255) NOT NULL,
  catatan VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (kuliner_id) REFERENCES kuliner(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO kuliner_tempat (id, kuliner_id, nama_tempat, alamat, catatan) VALUES
(1, 1, 'Rujak Cingur Genteng Durasim', 'Jl. Genteng Durasim, Surabaya', 'Salah satu yang paling legendaris sejak puluhan tahun lalu'),
(2, 1, 'Rujak Cingur Ahmad Jais', 'Jl. Ahmad Jais, Surabaya', 'Buka sore hingga malam hari'),
(3, 2, 'Tahu Tek Pak Jayen', 'Jl. Bogowonto, Surabaya', 'Berdiri sejak tahun 1990-an'),
(4, 3, 'Sate Klopo Ondomohen Bu Asih', 'Jl. Walikota Mustajab, Surabaya', 'Legendaris sejak tahun 1930-an'),
(5, 4, 'Lontong Balap Pak Gendut', 'Jl. Kranggan, Surabaya', 'Salah satu yang paling dikenal di Surabaya'),
(6, 5, 'Rawon Setan Pak Sadi', 'Jl. Embong Malang, Surabaya', 'Buka malam hari, khas dengan antrean panjang'),
(7, 6, 'Bakso President Malang', 'Jl. Batanghari, Malang', 'Berdiri sejak tahun 1977'),
(8, 6, 'Bakso Bakar Pahlawan Trip', 'Jl. Pahlawan Trip, Malang', 'Favorit untuk bakso bakar'),
(9, 7, 'Orem-Orem Pak Man', 'Kawasan Klojen, Malang', 'Salah satu warung orem-orem legendaris'),
(10, 8, 'Pecel Tumpang Simbok', 'Kawasan Kota Kediri', 'Terkenal dengan sambal pecel yang gurih manis'),
(11, 9, 'Sentra Tahu Kediri', 'Kawasan Pasar Kota Kediri', 'Bisa membeli tahu pong langsung dari perajinnya'),
(12, 10, 'Pecel Pincuk Bu Nur', 'Kawasan Kota Blitar', 'Menu sarapan favorit warga lokal'),
(13, 11, 'Pecel 05 Madiun', 'Kawasan Kota Madiun', 'Salah satu pecel legendaris dengan sambal kental khas'),
(14, 12, 'Onde-Onde Bo Liem', 'Jl. Niaga, Mojokerto', 'Toko onde-onde legendaris sejak 1943'),
(15, 13, 'Nasi Punel Bu Ali', 'Kawasan Kota Pasuruan', 'Legendaris dan ramai sejak pagi hari'),
(16, 14, 'Warung Nasi Petis Kraksaan', 'Kawasan Probolinggo', 'Terkenal dengan cita rasa petis yang kuat'),
(17, 15, 'Sentra Sate Kelinci Selecta', 'Kawasan Batu', 'Berjejer banyak warung sate kelinci di area wisata'),
(18, 16, 'Bebek Sinjay Pusat', 'Jl. Ketengan, Bangkalan', 'Cabang pertama dan paling terkenal, kerap antre panjang'),
(19, 17, 'Rujak Soto Cak Yudi', 'Kawasan Banyuwangi Kota', 'Salah satu yang paling direkomendasikan wisatawan'),
(20, 18, 'Sego Tempong Mbok Wah', 'Kawasan Banyuwangi Kota', 'Salah satu pelopor sego tempong paling dikenal'),
(21, 19, 'Sentra Oleh-Oleh Wajik Blitar', 'Kawasan Sentra Wajik, Blitar', 'Banyak toko oleh-oleh menjual wajik kletik segar'),
(22, 20, 'Sentra Ledre Padangan', 'Kecamatan Padangan, Bojonegoro', 'Pusat produksi ledre terbesar di Bojonegoro'),
(23, 21, 'Sentra Tape Tamansari', 'Kecamatan Tamansari, Bondowoso', 'Kawasan produksi tape rumahan terbesar'),
(24, 22, 'Nasi Krawu Bu Tiban', 'Kawasan Kota Gresik', 'Salah satu yang paling melegenda di Gresik'),
(25, 23, 'Sentra Oleh-Oleh Jalan Diponegoro', 'Kawasan Kota Jember', 'Berbagai toko oleh-oleh menjual suwar-suwir segar'),
(26, 24, 'Sate Bebek Pak Prapto', 'Kawasan Kota Jombang', 'Dikenal dengan daging bebek yang empuk'),
(27, 25, 'Sentra Tahu Takwa Kediri', 'Kawasan Pasar Kota Kediri', 'Bisa membeli langsung dari produsen tahu'),
(28, 26, 'Soto Lamongan Cak Har', 'Kawasan Kota Lamongan', 'Salah satu yang paling terkenal dan banyak cabang'),
(29, 27, 'Sentra Sale Pisang Senduro', 'Kecamatan Senduro, Lumajang', 'Kawasan penghasil pisang dan sale terkenal'),
(30, 28, 'Sentra Brem Kaliabu', 'Kecamatan Dolopo, Madiun', 'Pusat produksi brem tradisional terbesar'),
(31, 29, 'Sate Ayam Sarangan', 'Kawasan Telaga Sarangan, Magetan', 'Ramai dikunjungi wisatawan Telaga Sarangan'),
(32, 30, 'Kampung Sentra Industri Tempe Sanan', 'Kelurahan Purwantoro, Malang', 'Kawasan produksi tempe dan keripik terbesar di Malang'),
(33, 31, 'Warung Wader Trawas', 'Kecamatan Trawas, Mojokerto', 'Kawasan wisata dengan banyak warung wader'),
(34, 32, 'Nasi Becek Pak Tris', 'Kawasan Kota Nganjuk', 'Salah satu yang paling dikenal warga lokal'),
(35, 33, 'Tepo Tahu Bu Kasminah', 'Kawasan Kota Ngawi', 'Dikenal dengan bumbu kacang yang gurih legit'),
(36, 34, 'Sentra Tiwul Pacitan', 'Kawasan Kota Pacitan', 'Banyak dijual di pasar tradisional setempat'),
(37, 35, 'Nasi Serpang Ju Yan', 'Kawasan Kota Pamekasan', 'Salah satu penjual nasi serpang paling dikenal'),
(38, 36, 'Rawon Nguling Asli', 'Kecamatan Nguling, Pasuruan', 'Warung rawon legendaris yang telah berpuluh tahun berdiri'),
(39, 37, 'Sate Ayam Ponorogo Pak Tukri Sobikun', 'Kawasan Kota Ponorogo', 'Salah satu yang paling melegenda di Ponorogo'),
(40, 38, 'Warung Nasi Jagung Tongas', 'Kecamatan Tongas, Probolinggo', 'Dikenal dengan cita rasa nasi jagung yang otentik'),
(41, 39, 'Bebek Songkem H. Rifai', 'Kawasan Kota Sampang', 'Salah satu produsen bebek songkem paling dikenal'),
(42, 40, 'Kupang Lontong Pak Sholeh', 'Kawasan Kota Sidoarjo', 'Salah satu yang paling ramai dikunjungi'),
(43, 41, 'Sate Lalat Situbondo Bu Item', 'Kawasan Kota Situbondo', 'Ramai dikunjungi terutama saat malam hari'),
(44, 42, 'Kaldu Kokot Pak Har', 'Kawasan Kota Sumenep', 'Menu favorit warga lokal terutama saat malam hari'),
(45, 43, 'Sambal Tumpang Mbah Jayus', 'Kawasan Kota Trenggalek', 'Warung legendaris dengan resep turun-temurun'),
(46, 44, 'Pindang Bandeng Bu Har', 'Kawasan Kota Tuban', 'Salah satu warung pindang bandeng favorit warga pesisir'),
(47, 45, 'Lodho Ayam Bu Yuli', 'Kawasan Kota Tulungagung', 'Favorit saat musim syawalan tiba');

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  profile_photo VARCHAR(500) DEFAULT NULL,
  status ENUM('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE site_content (
  id INT AUTO_INCREMENT PRIMARY KEY,
  page_key VARCHAR(50) NOT NULL,
  section_key VARCHAR(80) NOT NULL,
  label VARCHAR(150) DEFAULT NULL,
  title TEXT DEFAULT NULL,
  description TEXT DEFAULT NULL,
  placeholder VARCHAR(255) DEFAULT NULL,
  button_label VARCHAR(150) DEFAULT NULL,
  updated_by INT DEFAULT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_site_content (page_key, section_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE site_articles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(180) NOT NULL UNIQUE,
  category VARCHAR(30) NOT NULL,
  title VARCHAR(255) NOT NULL,
  excerpt TEXT NOT NULL,
  body LONGTEXT NOT NULL,
  image_url VARCHAR(500) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  content_version TINYINT UNSIGNED NOT NULL DEFAULT 4,
  updated_by INT DEFAULT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE user_recommendations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  nama_kuliner VARCHAR(150) NOT NULL,
  kategori VARCHAR(50) NOT NULL DEFAULT 'Makanan Berat',
  wilayah VARCHAR(100) NOT NULL,
  lokasi VARCHAR(255) NOT NULL,
  harga_min INT UNSIGNED NOT NULL DEFAULT 0,
  harga_max INT UNSIGNED NOT NULL DEFAULT 0,
  rating DECIMAL(2,1) NOT NULL DEFAULT 0.0,
  deskripsi TEXT NOT NULL,
  gambar VARCHAR(500) DEFAULT NULL,
  status ENUM('Menunggu','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu',
  catatan_admin VARCHAR(255) DEFAULT NULL,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE user_articles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  jenis ENUM('Artikel','Resep') NOT NULL,
  judul VARCHAR(255) NOT NULL,
  ringkasan TEXT NOT NULL,
  isi LONGTEXT NOT NULL,
  gambar VARCHAR(500) NOT NULL,
  slug VARCHAR(180) DEFAULT NULL UNIQUE,
  status ENUM('Menunggu','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu',
  catatan_admin VARCHAR(255) DEFAULT NULL,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
