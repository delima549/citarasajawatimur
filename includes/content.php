<?php
function site_content_defaults(): array
{
    return [
        'home.hero' => [
            'label' => '',
            'title' => 'Temukan Kuliner Autentik|Jawa Timur dalam Satu Platform',
            'description' => 'Akses berbagai informasi kuliner autentik dari 38 kota/kabupaten di Jawa Timur',
            'placeholder' => 'Ketik nama kuliner atau daerah Jawa Timur...',
            'button_label' => 'Cari Kuliner'
        ],
        'home.latest' => [
            'label' => 'Informasi Terbaru',
            'title' => 'Pilihan Kuliner Paling Banyak Diminati Wisatawan',
            'description' => 'Kuliner Jawa Timur yang paling banyak diminati wisatawan.'
        ],
        'about.banner' => [
            'label' => 'Tentang Kami',
            'title' => 'Menghubungkan Rasa Lokal Jawa Timur dengan Lebih Banyak Penikmat',
            'description' => 'CitaRasaJawaTimur adalah direktori digital yang membantu warga dan wisatawan menemukan kuliner UMKM Jawa Timur berdasarkan daerah, rekomendasi, dan cerita di balik setiap hidangan.'
        ],
        'catalog.banner' => [
            'label' => 'Katalog Kuliner',
            'title' => 'Jelajahi Kuliner Jawa Timur',
            'description' => 'Temukan kuliner khas Jawa Timur, lengkap dengan cerita, resep, dan tempat untuk menikmatinya.'
        ],
        'recommendation.banner' => [
            'label' => 'Kuliner Pilihan',
            'title' => 'Rekomendasi Kuliner Enak Menurut Pengguna',
            'description' => 'Kumpulan kuliner lokal pilihan tim kami dari berbagai kabupaten dan kota di Jawa Timur.'
        ],
        'map.banner' => [
            'label' => 'Peta Kuliner',
            'title' => 'Peta Lokasi Kuliner Jawa Timur',
            'description' => 'Jelajahi lokasi warung dan tempat kuliner pilihan yang sudah terdokumentasi di Jawa Timur.'
        ]
    ];
}

function ensure_site_content_table(mysqli $conn): void
{
    static $ready = false;
    if ($ready) {
        return;
    }

    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS site_content (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $ready = true;
}

function get_site_content(mysqli $conn, string $pageKey, string $sectionKey): array
{
    ensure_site_content_table($conn);
    $defaults = site_content_defaults();
    $content = $defaults[$pageKey . '.' . $sectionKey] ?? [];

    $stmt = mysqli_prepare($conn, "SELECT label, title, description, placeholder, button_label FROM site_content WHERE page_key = ? AND section_key = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "ss", $pageKey, $sectionKey);
    mysqli_stmt_execute($stmt);
    $saved = mysqli_stmt_get_result($stmt)->fetch_assoc();

    return $saved ? array_merge($content, array_filter($saved, static fn($value) => $value !== null)) : $content;
}

function save_site_content(mysqli $conn, string $pageKey, string $sectionKey, array $content, int $adminId): bool
{
    ensure_site_content_table($conn);
    $label = trim($content['label'] ?? '');
    $title = trim($content['title'] ?? '');
    $description = trim($content['description'] ?? '');
    $placeholder = trim($content['placeholder'] ?? '');
    $buttonLabel = trim($content['button_label'] ?? '');

    $stmt = mysqli_prepare($conn, "INSERT INTO site_content (page_key, section_key, label, title, description, placeholder, button_label, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE label = VALUES(label), title = VALUES(title), description = VALUES(description), placeholder = VALUES(placeholder), button_label = VALUES(button_label), updated_by = VALUES(updated_by)");
    mysqli_stmt_bind_param($stmt, "sssssssi", $pageKey, $sectionKey, $label, $title, $description, $placeholder, $buttonLabel, $adminId);
    return mysqli_stmt_execute($stmt);
}

function site_article_defaults(): array
{
    return [
        [
            'slug' => '5-makanan-unik-jawa-timur-sejarah',
            'category' => 'unik',
            'title' => '5 Artikel Kuliner Jawa Timur dengan Sejarahnya',
            'excerpt' => 'Mengenal lima sajian khas Jawa Timur yang unik dari bahan, rasa, cara penyajian, serta cerita yang tumbuh di baliknya.',
            'body' => "Jawa Timur menyimpan banyak hidangan yang keunikannya lahir dari bahan pesisir, kebiasaan makan masyarakat, dan teknik memasak yang diwariskan di keluarga. Lima kuliner berikut mungkin tidak selalu mudah ditemukan di luar daerah asalnya, tetapi masing-masing memiliki rasa dan cerita yang kuat. Nama, bahan pendamping, serta cara penyajian dapat berbeda menurut kampung dan penjualnya.\n\n1. Rujak Soto\n[[image:https://images.unsplash.com/photo-1529563021893-cc83c992d75d?w=1000&q=80|Rujak soto khas Banyuwangi dengan kuah soto dan sayuran rujak]]\nRujak soto adalah hidangan khas Banyuwangi yang mempertemukan rujak sayur dengan soto daging dalam satu mangkuk. Isinya dapat berupa lontong, sayuran, tahu, potongan daging, babat, atau cingur. Bumbu rujaknya menggunakan kacang, cabai, petis, dan pisang klutuk, kemudian disiram kuah soto panas sehingga menghasilkan rasa gurih, pedas, segar, dan sedikit manis.\n\nSejarah rujak soto tumbuh dari kebiasaan masyarakat Banyuwangi menggabungkan sajian yang sudah akrab di meja makan. Rujak memberi tekstur dan rasa bumbu yang kuat, sedangkan kuah soto menambah aroma rempah serta kehangatan. Perpaduan ini memperlihatkan kreativitas kuliner Using dan karakter Banyuwangi sebagai wilayah pesisir yang memiliki perjumpaan budaya yang beragam.\n\n2. Lentho Kacang Tolo\n[[image:https://images.unsplash.com/photo-1626804475297-41608ea09aeb?w=1000&q=80|Lentho kacang tolo sebagai gorengan pendamping kuliner Surabaya]]\nLentho kacang tolo merupakan gorengan berbentuk lonjong atau bulat yang dibuat dari kacang tolo, singkong parut, kelapa, bawang putih, ketumbar, dan bumbu sederhana. Bagian luarnya garing, sedangkan bagian dalamnya padat, gurih, dan sedikit bertekstur. Lentho biasa hadir sebagai pelengkap lontong balap, lontong kupang, atau dimakan sebagai camilan dengan sambal.\n\nLentho berkembang dari bahan pangan yang mudah didapat di lingkungan Jawa Timur, terutama singkong dan kacang-kacangan. Kehadirannya sebagai lauk pendamping membuat semangkuk makanan berkuah terasa lebih mengenyangkan. Sampai sekarang, lentho tetap penting dalam sajian kaki lima Surabaya karena pembuatannya sederhana, harganya terjangkau, dan rasanya mampu menyerap kuah petis maupun kaldu.\n\n3. Rujak Cingur\n[[image:https://images.unsplash.com/photo-1512058564366-18510be2db19?w=1000&q=80|Rujak cingur Surabaya dengan cingur, buah, sayuran, dan bumbu petis]]\nRujak cingur adalah salah satu ikon Surabaya. Satu porsinya memadukan irisan cingur sapi, lontong, tahu, tempe, sayuran rebus, timun, bengkuang, nanas, dan mangga muda. Semua bahan disatukan dengan bumbu petis udang, kacang tanah, cabai, gula merah, garam, dan pisang klutuk. Rasa gurih, pedas, manis, asam, dan sepat hadir bersamaan dengan tekstur yang beragam.\n\nNama cingur merujuk pada moncong sapi yang menjadi bahan pembeda hidangan ini. Rujak cingur tumbuh sebagai makanan rakyat di Surabaya dan sekitarnya, lalu diwariskan melalui warung, pasar, serta acara keluarga. Bumbu yang diulek langsung dan penyajian sesaat sebelum makan menjadi bagian penting dari pengalaman menikmati versi tradisionalnya.\n\n4. Lontong Kupang\n[[image:https://images.unsplash.com/photo-1547592166-23ac45744acd?w=1000&q=80|Lontong kupang dengan kuah petis dan lentho khas Jawa Timur]]\nLontong kupang dibuat dari kupang, kerang kecil yang banyak diolah di kawasan pesisir Sidoarjo dan Pasuruan. Kupang yang sudah direbus disajikan bersama lontong, kuah kaldu, petis, bawang putih, cabai, perasan jeruk, dan lentho. Rasanya gurih, segar, serta memiliki aroma laut yang khas. Es degan sering menjadi pendamping untuk menyeimbangkan rasa kuah yang kuat.\n\nHidangan ini lahir dari kedekatan masyarakat pesisir dengan hasil tangkapan laut. Penjual biasanya mengolah kupang pada hari yang sama agar rasa dan teksturnya tetap baik. Karena bahan laut perlu ditangani dengan benar, kebersihan proses pencucian dan perebusan menjadi bagian penting dari mutu lontong kupang, selain racikan petisnya.\n\n5. Lorjuk Khas Madura\n[[image:https://images.unsplash.com/photo-1535140728325-a4d3707eef39?w=1000&q=80|Olahan lorjuk atau kerang bambu khas Madura]]\nLorjuk adalah kerang bambu yang hidup di pasir pantai Madura. Bentuknya memanjang seperti bambu dan dagingnya dapat diolah menjadi campur lorjuk, tumisan, pepes, gorengan, rengginang, atau dimasak bersama kuah. Campur lorjuk biasanya memadukan daging lorjuk dengan kuah berbumbu, lontong atau nasi, serta pelengkap yang membuat rasa manis-gurihnya semakin kuat.\n\nLorjuk menjadi bagian dari pengetahuan pangan masyarakat pesisir Madura karena pengolahannya membantu memperpanjang daya simpan hasil laut. Dagingnya perlu dibersihkan dari pasir dan direbus dengan tepat sebelum dimasak lebih lanjut. Selain menghadirkan rasa yang tidak umum, olahan lorjuk juga memperlihatkan hubungan antara musim, lingkungan pantai, dan mata pencaharian warga setempat.\n\nKelima hidangan tersebut layak dikenali bukan hanya karena rasanya berbeda, tetapi juga karena membawa cerita tentang bahan lokal dan orang-orang yang merawat resepnya. Saat mencicipi, pilih penjual yang menjaga kebersihan, tanyakan bahan bila memiliki alergi, dan hargai variasi resep dari setiap daerah.",
            'image_url' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=1000&q=80',
            'sort_order' => 1
        ],
        [
            'slug' => 'kuliner-unik-jawa-timur-bahan-lokal',
            'category' => 'unik',
            'title' => 'Daftar Minuman Menyegarkan Khas Jawa Timur',
            'excerpt' => 'Lima minuman khas Jawa Timur dengan rasa segar, manis, dan rempah yang cocok dinikmati dalam berbagai suasana.',
            'body' => "Jawa Timur tidak hanya kaya akan makanan berat, tetapi juga memiliki minuman tradisional yang menyegarkan dan sarat cerita. Bahan seperti asam jawa, kunyit, cincau, tape singkong, dan rempah mudah ditemukan dalam resep rumahan maupun usaha minuman lokal. Berikut lima minuman khas yang dapat dikenali dari rasa dan cara penyajiannya.\n\n1. Es Sinom\n[[image:https://images.unsplash.com/photo-1544145945-f90425340c7e?w=1000&q=80|Es sinom khas Jawa Timur dengan rasa asam manis yang menyegarkan]]\nEs sinom adalah minuman berwarna kuning kehijauan yang dibuat dari daun asam muda, kunyit, asam jawa, gula merah, dan air. Rasanya asam, manis, dan ringan dengan aroma kunyit yang khas. Minuman ini biasanya disajikan dingin sehingga cocok menemani cuaca panas atau makanan dengan rasa gurih dan pedas.\n\nSinom memiliki hubungan erat dengan tradisi jamu dan pengetahuan masyarakat dalam memanfaatkan tanaman di sekitar rumah. Daun asam muda dipilih karena menghasilkan rasa segar yang berbeda dari buah asam jawa. Dalam perkembangannya, sinom tidak hanya diminum sebagai racikan rumahan, tetapi juga dijual dalam botol sebagai minuman tradisional yang praktis.\n\n2. Beras Kencur\n[[image:https://images.unsplash.com/photo-1547592180-85f173990554?w=1000&q=80|Beras kencur sebagai minuman tradisional berbahan beras dan rempah]]\nBeras kencur dibuat dari beras yang direndam, kencur, jahe, gula merah, dan air. Teksturnya sedikit lebih pekat dibanding minuman biasa, dengan rasa manis, hangat, dan aroma kencur yang kuat. Es batu dapat ditambahkan saat penyajian, tetapi beras kencur juga nikmat diminum pada suhu ruang.\n\nMinuman ini berkembang dari tradisi jamu Jawa yang diwariskan antargenerasi. Beras memberi rasa lembut, sedangkan kencur dan jahe memberikan aroma serta sensasi hangat. Penjual jamu gendong turut membuat beras kencur dikenal luas sebagai minuman sehari-hari, bukan hanya racikan untuk acara tertentu.\n\n3. Wedang Jaselang\n[[image:https://images.unsplash.com/photo-1544145945-f90425340c7e?w=1000&q=80|Wedang jaselang hangat dengan racikan jahe dan rempah]]\nWedang jaselang merupakan minuman hangat berbahan rempah yang umum diracik dari jahe, serai, gula, dan bahan tambahan sesuai kebiasaan keluarga. Jahe dimemarkan lalu direbus agar aromanya keluar, kemudian kuahnya disajikan panas. Rasanya manis dan hangat dengan wangi rempah yang menenangkan.\n\nNama dan komposisi wedang dapat memiliki variasi di berbagai daerah Jawa Timur. Tradisi minum wedang berkembang karena rempah mudah disimpan dan minuman hangat cocok diminum pada malam hari atau saat udara dingin. Di rumah maupun warung, wedang menjadi bagian dari kebiasaan berkumpul dan berbincang.\n\n4. Es Cao\n[[image:https://images.unsplash.com/photo-1544145945-f90425340c7e?w=1000&q=80|Es cao atau cincau hitam dengan kuah gula merah yang menyegarkan]]\nEs cao menggunakan potongan cincau hitam yang disajikan dengan kuah gula merah, santan, susu, atau es batu. Teksturnya kenyal dan lembut, sementara rasa manis gula merah berpadu dengan gurih santan. Beberapa penjual menambahkan sirup, nangka, atau biji selasih untuk memperkaya rasa.\n\nCincau dikenal sebagai bahan pangan yang dibuat dari daun atau bahan tanaman yang diolah hingga membentuk gel. Es cao kemudian berkembang sebagai minuman pasar dan jajanan yang mudah ditemukan ketika cuaca panas. Kesederhanaan bahan membuatnya mudah dibuat di rumah, tetapi kualitas gula merah dan kesegaran kuah sangat menentukan hasil akhirnya.\n\n5. Es Tape\n[[image:https://images.unsplash.com/photo-1544145945-f90425340c7e?w=1000&q=80|Es tape singkong dengan rasa manis dan aroma fermentasi khas Jawa Timur]]\nEs tape dibuat dari tape singkong yang dipadukan dengan sirup atau gula, santan, susu, dan es. Tape memberikan rasa manis legit serta aroma fermentasi yang khas. Potongan tape dapat dibiarkan utuh agar teksturnya terasa, atau dihaluskan menjadi minuman yang lebih lembut.\n\nTape singkong merupakan hasil fermentasi yang sudah lama dikenal di Jawa Timur, terutama sebagai oleh-oleh dan bahan kudapan. Mengolahnya menjadi minuman menunjukkan cara masyarakat mengembangkan bahan yang sama ke dalam sajian baru. Es tape sebaiknya disajikan segar dan diberi keterangan kepada orang yang sensitif terhadap makanan hasil fermentasi.\n\nKelima minuman tersebut memperlihatkan bahwa kesegaran dapat hadir dari bahan sederhana dan teknik yang diwariskan. Saat membuat atau membelinya, gunakan air matang, bahan yang bersih, dan simpan minuman bersantan dalam kondisi dingin agar tetap aman dikonsumsi.",
            'image_url' => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=1000&q=80',
            'sort_order' => 2
        ],
        [
            'slug' => 'cerita-di-balik-makanan-khas-jawa-timur',
            'category' => 'unik',
            'title' => 'Kue Khas Jawa Timur yang Manis dan Beragam',
            'excerpt' => 'Mengenal lima kue khas Jawa Timur yang hadir dalam tradisi pasar, oleh-oleh, dan perayaan keluarga.',
            'body' => "Jawa Timur memiliki beragam kue tradisional yang dibuat dari bahan seperti ketan, singkong, pisang, kacang hijau, santan, dan gula kelapa. Teksturnya beragam, mulai dari kenyal dan legit hingga renyah dan rapuh. Lima kue berikut menunjukkan bagaimana bahan sederhana dapat diolah menjadi kudapan yang lekat dengan kehidupan masyarakat.\n\n1. Onde-Onde Mojokerto\n[[image:https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1000&q=80|Onde-onde berlapis wijen dengan isian kacang hijau khas Mojokerto]]\nOnde-onde Mojokerto berbentuk bulat dengan lapisan wijen di bagian luar dan isian kumbu kacang hijau yang manis. Kulitnya dibuat dari tepung ketan sehingga terasa kenyal, sementara permukaan wijen menjadi renyah setelah digoreng. Kue ini biasa dinikmati sebagai camilan atau dibawa sebagai oleh-oleh.\n\nMojokerto dikenal sebagai Kota Onde-Onde karena kue ini telah lama menjadi bagian dari identitas perdagangan dan oleh-oleh daerah. Pembuatannya diwariskan melalui toko keluarga dan perajin rumahan. Walaupun onde-onde juga dikenal di daerah lain, ukuran, tingkat kemanisan, dan tekstur isiannya dapat menjadi ciri khas setiap pembuat.\n\n2. Wajik Kletik Blitar\n[[image:https://images.unsplash.com/photo-1567337710282-00832b415979?w=1000&q=80|Wajik kletik Blitar yang legit dari ketan dan gula kelapa]]\nWajik kletik dibuat dari beras ketan, gula merah atau gula kelapa, santan, daun pandan, dan sedikit garam. Adonan dimasak hingga kalis lalu dibungkus atau dicetak. Rasanya manis legit dengan aroma karamel gula kelapa, sementara teksturnya padat dan kenyal.\n\nWajik kletik menjadi salah satu oleh-oleh yang lekat dengan Blitar dan sering hadir dalam acara keluarga. Proses mengaduk ketan dan gula membutuhkan kesabaran agar adonan tidak cepat gosong serta dapat mencapai tekstur yang tepat. Nama kletik juga mengingatkan pada sensasi butiran atau cara kudapan ini dinikmati.\n\n3. Ledre Bojonegoro\n[[image:https://images.unsplash.com/photo-1596797038530-2c107229654b?w=1000&q=80|Ledre Bojonegoro berbentuk gulungan tipis dengan aroma pisang raja]]\nLedre adalah kue tipis berbentuk gulungan yang dibuat dari tepung beras, pisang raja, santan, gula, dan sedikit garam. Adonan dituangkan sangat tipis di atas wajan, lalu digulung saat masih hangat. Hasilnya ringan, renyah, dan memiliki aroma pisang yang jelas.\n\nLedre berkembang di Bojonegoro sebagai cara mengolah pisang raja yang melimpah menjadi kudapan tahan simpan. Keterampilan membuat lapisan tipis dan menggulungnya tanpa patah menjadi bagian penting dari keahlian perajin. Kue ini kemudian dikenal sebagai buah tangan karena ringan dibawa dan dapat dinikmati bersama teh atau kopi.\n\n4. Getuk Pisang Kediri\n[[image:https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1000&q=80|Getuk pisang Kediri yang lembut, manis, dan dibungkus daun pisang]]\nGetuk pisang dibuat dari pisang matang yang dikukus, dihaluskan, dicampur gula, lalu dibungkus dengan daun pisang. Teksturnya lembut dan padat dengan rasa manis alami serta aroma pisang yang kuat. Warna dan tingkat kemanisannya dapat berbeda bergantung pada jenis pisang dan lama pengukusan.\n\nKediri dikenal sebagai salah satu daerah penghasil getuk pisang yang sering dijadikan oleh-oleh. Pembungkus daun pisang membantu menjaga bentuk sekaligus memberi aroma tradisional. Dari dapur rumahan, kue ini berkembang menjadi produk UMKM yang dikemas lebih rapi tanpa meninggalkan bahan utamanya.\n\n5. Tape Bakar Bondowoso\n[[image:https://images.unsplash.com/photo-1547592180-85f173990554?w=1000&q=80|Tape singkong bakar Bondowoso dengan rasa manis dan aroma karamel]]\nTape bakar merupakan kudapan yang memanfaatkan tape singkong sebagai bahan utama. Tape dipanggang hingga permukaannya sedikit karamel, lalu dapat disajikan dengan kelapa parut, susu kental manis, atau gula merah cair. Rasanya manis, legit, dan memiliki aroma fermentasi yang semakin harum setelah dipanaskan.\n\nBondowoso dikenal sebagai Kota Tape karena tradisi pengolahan singkongnya kuat. Tape yang semula dijual sebagai kudapan kemudian diolah menjadi berbagai produk, termasuk tape bakar dan kue oleh-oleh. Proses fermentasi menentukan rasa akhir, sehingga tape perlu dipilih dalam kondisi matang, tidak terlalu berair, dan tidak memiliki aroma yang menyimpang.\n\nKelima kue tersebut menunjukkan kekayaan jajanan Jawa Timur dari sisi bahan, teknik, dan fungsi sosial. Ada yang hadir sebagai oleh-oleh, teman minum, hidangan hajatan, maupun produk usaha keluarga. Menjaga resep, memilih bahan yang aman, dan membeli dari perajin lokal membantu kue tradisional tetap dikenal oleh generasi berikutnya.",
            'image_url' => 'https://images.unsplash.com/photo-1544025162-d76694265947?w=1000&q=80',
            'sort_order' => 3
        ],
        [
            'slug' => '5-resep-minuman-menyegarkan-jawa-timur',
            'category' => 'autentik',
            'title' => '10 Resep Makanan Khas Jawa Timur yang Menggugah Selera',
            'excerpt' => 'Kumpulan sepuluh resep makanan Jawa Timur lengkap dengan bahan utama, bumbu, langkah memasak, dan saran penyajiannya.',
            'body' => "Jawa Timur memiliki resep makanan yang kuat dari bumbu, rempah, dan cara penyajian. Kumpulan ini menghadirkan sepuluh hidangan dari berbagai daerah, mulai dari makanan berkuah hingga sajian nasi dan lauk. Takaran dapat disesuaikan, tetapi gunakan bahan segar dan masak sampai matang sempurna.\n\n1. Rawon\n[[image:https://images.unsplash.com/photo-1529563021893-cc83c992d75d?w=1000&q=80|Rawon daging dengan kuah hitam kluwek khas Jawa Timur]]\nBahan: 500 gram daging sapi, 1,5 liter air, 4 buah kluwek, 6 bawang merah, 4 bawang putih, 2 butir kemiri, 2 cm kunyit, 2 cm jahe, serai, daun salam, garam, dan gula.\nCara membuat: Rebus daging sampai empuk lalu potong. Haluskan bawang, kemiri, kunyit, jahe, dan kluwek, kemudian tumis bersama serai serta daun salam sampai matang. Masukkan bumbu ke kaldu, masak bersama daging, koreksi rasa, lalu sajikan dengan tauge pendek, telur asin, sambal, dan bawang goreng.\n\n2. Soto Lamongan\n[[image:https://images.unsplash.com/photo-1547592166-23ac45744acd?w=1000&q=80|Soto Lamongan dengan ayam, koya, dan kuah rempah]]\nBahan: 500 gram ayam, 2 liter air, bawang merah, bawang putih, kemiri, kunyit, merica, serai, daun jeruk, kol, soun, tomat, seledri, dan jeruk nipis. Untuk koya, siapkan kerupuk udang dan bawang putih goreng.\nCara membuat: Rebus ayam sampai matang dan ambil kaldunya. Tumis bumbu halus bersama serai dan daun jeruk, masukkan ke kaldu, lalu bumbui. Suwir ayam, tata bersama soun, kol, dan tomat, siram kuah panas, kemudian taburi koya dan seledri.\n\n3. Pecel Madiun\n[[image:https://images.unsplash.com/photo-1476124369491-e7addf5db371?w=1000&q=80|Pecel Madiun dengan sayuran rebus, sambal kacang, dan rempeyek]]\nBahan: bayam, kenikir, tauge, kacang panjang, 250 gram kacang tanah goreng, cabai, kencur, daun jeruk, gula merah, asam jawa, garam, dan rempeyek.\nCara membuat: Rebus sayuran sebentar lalu tiriskan. Ulek kacang, cabai, kencur, daun jeruk, gula merah, garam, dan asam jawa. Seduh bumbu dengan air panas secukupnya, siram di atas sayuran dan nasi, lalu sajikan dengan rempeyek.\n\n4. Rujak Cingur\n[[image:https://images.unsplash.com/photo-1512058564366-18510be2db19?w=1000&q=80|Rujak cingur dengan buah, sayuran, lontong, dan bumbu petis]]\nBahan: 400 gram cingur rebus, lontong, tahu, tempe, kangkung, tauge, timun, bengkuang, nanas, mangga muda, petis udang, kacang tanah, cabai, gula merah, garam, dan pisang klutuk.\nCara membuat: Rebus cingur sampai empuk lalu iris. Rebus sayuran, goreng tahu dan tempe, kemudian ulek kacang, cabai, petis, gula, garam, dan pisang klutuk. Campur semua bahan dengan bumbu sesaat sebelum disajikan agar buah dan tauge tetap segar.\n\n5. Lontong Balap\n[[image:https://images.unsplash.com/photo-1583224964978-2257b960c3d3?w=1000&q=80|Lontong balap dengan tauge, lentho, tahu, dan kuah gurih]]\nBahan: lontong, 200 gram tauge, tahu goreng, lentho, bawang putih, bawang merah, daun bawang, kaldu, kecap, petis, dan sambal.\nCara membuat: Buat kuah dari kaldu dan tumisan bawang. Susun lontong, tauge, tahu, dan lentho dalam mangkuk, lalu siram kuah panas. Tambahkan petis, kecap, sambal, dan bawang goreng sesuai selera.\n\n6. Nasi Pecel Kediri\n[[image:https://images.unsplash.com/photo-1596797038530-2c107229654b?w=1000&q=80|Nasi pecel Kediri dengan sayuran dan sambal kacang]]\nBahan: nasi hangat, bayam, kacang panjang, tauge, kenikir, kacang tanah goreng, cabai, kencur, gula merah, asam jawa, dan peyek.\nCara membuat: Rebus sayuran hingga matang tetapi tidak lembek. Haluskan bahan sambal, encerkan dengan air hangat, lalu tuang di atas nasi dan sayuran. Sajikan bersama peyek atau lauk goreng.\n\n7. Nasi Tempong Banyuwangi\n[[image:https://images.unsplash.com/photo-1547592166-23ac45744acd?w=1000&q=80|Nasi tempong dengan lauk goreng, sayuran, dan sambal pedas]]\nBahan: nasi putih, ayam atau ikan, tahu, tempe, kenikir, terong, kemangi, cabai rawit, tomat, terasi, dan jeruk limau.\nCara membuat: Goreng lauk dan rebus sayuran. Ulek cabai, tomat, terasi, garam, serta perasan jeruk saat akan makan agar sambal tetap segar. Sajikan nasi dengan lauk, sayuran, dan sambal tempong.\n\n8. Ayam Lodho Tulungagung\n[[image:https://images.unsplash.com/photo-1585032226651-759b368d7246?w=1000&q=80|Ayam lodho dengan kuah santan pedas dan rempah]]\nBahan: 1 ekor ayam, santan, bawang merah, bawang putih, cabai, kemiri, kunyit, kencur, serai, daun jeruk, dan garam.\nCara membuat: Bakar ayam sampai harum, lalu potong. Tumis bumbu halus bersama rempah, masukkan ayam dan santan, kemudian masak dengan api kecil sampai bumbu meresap. Sajikan bersama nasi gurih atau nasi putih hangat.\n\n9. Tahu Campur Lamongan\n[[image:https://images.unsplash.com/photo-1626804475297-41608ea09aeb?w=1000&q=80|Tahu campur Lamongan dengan kuah gurih, tahu, mie, dan perkedel singkong]]\nBahan: tahu goreng, daging sapi, mie kuning, selada, tauge, perkedel singkong, petis, bawang putih, cabai, dan kaldu sapi.\nCara membuat: Rebus daging sampai empuk dan gunakan kaldunya sebagai kuah. Ulek petis, bawang putih, dan cabai dalam mangkuk, tata mie, selada, tauge, tahu, serta perkedel, lalu siram dengan kuah dan irisan daging.\n\n10. Bebek Sinjay\n[[image:https://images.unsplash.com/photo-1583224964978-2257b960c3d3?w=1000&q=80|Bebek goreng dengan sambal pencit khas Bangkalan]]\nBahan: 1 ekor bebek, bawang putih, kunyit, ketumbar, lengkuas, daun salam, serai, garam, mangga muda, cabai, dan terasi.\nCara membuat: Ungkep bebek bersama bumbu sampai empuk dan meresap, lalu goreng hingga kulitnya garing. Serut mangga muda dan campur dengan cabai serta terasi untuk sambal pencit. Sajikan panas bersama nasi dan lalapan.",
            'image_url' => 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=1000&q=80',
            'sort_order' => 1
        ],
        [
            'slug' => 'resep-autentik-rujak-cingur-rumahan',
            'category' => 'autentik',
            'title' => 'Resep Makanan Ringan Jawa Timur yang Mudah Dibuat',
            'excerpt' => 'Lima resep camilan khas Jawa Timur dengan bahan sederhana, langkah praktis, dan rasa yang cocok untuk teman minum teh.',
            'body' => "Makanan ringan Jawa Timur hadir dalam bentuk gorengan, kue, dan kudapan yang cocok untuk sarapan atau teman minum. Bahan seperti singkong, tahu, pisang, kacang hijau, dan kelapa diolah dengan teknik sederhana tetapi menghasilkan rasa yang khas. Berikut lima resep yang dapat dibuat di rumah.\n\n1. Lentho Kacang Tolo\n[[image:https://images.unsplash.com/photo-1626804475297-41608ea09aeb?w=1000&q=80|Lentho kacang tolo yang gurih dan renyah sebagai camilan Jawa Timur]]\nBahan: 200 gram kacang tolo rebus, 250 gram singkong parut, 100 gram kelapa parut, bawang putih, ketumbar, daun jeruk, garam, dan minyak goreng.\nCara membuat: Haluskan sebagian kacang tolo bersama bumbu, campur dengan singkong, kelapa, dan sisa kacang. Bentuk lonjong atau bulat, lalu goreng dalam minyak panas dengan api sedang sampai kecokelatan. Tiriskan dan sajikan hangat.\n\n2. Tahu Pong Kediri\n[[image:https://images.unsplash.com/photo-1555126634-323283e090fa?w=1000&q=80|Tahu pong goreng dengan bagian dalam berongga khas Kediri]]\nBahan: 10 buah tahu putih, air garam, minyak goreng, petis, cabai rawit, bawang putih, kecap, dan irisan timun.\nCara membuat: Rendam tahu dalam air garam, tiriskan, lalu goreng sampai kulitnya kering dan bagian dalamnya berongga. Ulek cabai dan bawang putih, campur dengan petis serta kecap, kemudian sajikan sebagai cocolan tahu dan timun.\n\n3. Onde-Onde Mojokerto\n[[image:https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1000&q=80|Onde-onde wijen dengan isian kacang hijau khas Mojokerto]]\nBahan: 250 gram tepung ketan, 100 gram kacang hijau kupas, gula, wijen, air hangat, dan minyak goreng.\nCara membuat: Kukus kacang hijau, haluskan bersama gula, lalu bentuk menjadi bulatan kecil. Campur tepung ketan dengan air hangat, bungkus isian, bulatkan, dan basahi permukaannya. Gulingkan pada wijen, kemudian goreng mulai dari minyak hangat dengan api kecil agar matang merata.\n\n4. Pisang Molen\n[[image:https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1000&q=80|Pisang molen renyah sebagai jajanan pasar Jawa Timur]]\nBahan: pisang raja atau pisang kepok, 250 gram tepung terigu, 2 sendok makan margarin, gula, garam, air, dan minyak.\nCara membuat: Campur tepung, margarin, gula, garam, dan air sampai menjadi adonan kalis. Giling tipis lalu potong memanjang, lilitkan pada potongan pisang, dan goreng hingga kulitnya renyah keemasan. Jangan gunakan api terlalu besar agar pisang tidak cepat gosong.\n\n5. Getuk Pisang Kediri\n[[image:https://images.unsplash.com/photo-1567337710282-00832b415979?w=1000&q=80|Getuk pisang Kediri yang lembut dan manis dibungkus daun pisang]]\nBahan: 1 kilogram pisang matang, 100 gram gula, sedikit garam, dan daun pisang.\nCara membuat: Kukus pisang sampai lunak, kupas, lalu haluskan bersama gula dan garam. Bungkus adonan dengan daun pisang, padatkan, dan kukus kembali selama 20 menit. Dinginkan sebelum dipotong agar bentuknya rapi.\n\nCamilan akan terasa lebih baik jika minyak, bahan, dan peralatan bersih. Sajikan gorengan segera agar tetap renyah, sedangkan kue kukus sebaiknya disimpan dalam wadah tertutup setelah dingin.",
            'image_url' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=1000&q=80',
            'sort_order' => 2
        ],
        [
            'slug' => 'resep-autentik-wedang-angsle',
            'category' => 'autentik',
            'title' => '5 Resep Minuman Menyegarkan Khas Jawa Timur',
            'excerpt' => 'Lima resep minuman khas Jawa Timur yang segar atau hangat, lengkap dengan bahan dan cara membuatnya.',
            'body' => "Minuman khas Jawa Timur memiliki rasa yang beragam, mulai dari asam manis dan segar hingga hangat dengan aroma rempah. Resep berikut menggunakan bahan yang mudah ditemukan dan dapat disesuaikan tingkat manisnya. Gunakan air matang serta sajikan minuman bersantan segera setelah dibuat.\n\n1. Es Sinom\n[[image:https://images.unsplash.com/photo-1544145945-f90425340c7e?w=1000&q=80|Es sinom dengan rasa asam manis dan aroma kunyit]]\nBahan: 1 genggam daun asam muda, 2 cm kunyit, 2 sendok makan asam jawa, 150 gram gula merah, 1 liter air, dan es batu.\nCara membuat: Cuci daun asam dan kunyit, lalu rebus bersama air, asam jawa, serta gula merah. Setelah aromanya keluar, saring dan dinginkan. Sajikan dengan es batu.\n\n2. Beras Kencur\n[[image:https://images.unsplash.com/photo-1547592180-85f173990554?w=1000&q=80|Beras kencur dengan aroma rempah yang manis dan hangat]]\nBahan: 100 gram beras, 100 gram gula merah, 50 gram kencur, 2 cm jahe, 800 ml air matang, dan sedikit garam.\nCara membuat: Rendam beras selama beberapa jam, tiriskan, lalu haluskan bersama kencur dan jahe. Rebus gula merah dengan air, dinginkan, campur dengan bahan halus, kemudian saring dan sajikan dingin atau suhu ruang.\n\n3. Wedang Jaselang\n[[image:https://images.unsplash.com/photo-1547592180-85f173990554?w=1000&q=80|Wedang jaselang hangat dengan jahe, serai, dan gula]]\nBahan: 2 ruas jahe, 2 batang serai, 700 ml air, gula merah secukupnya, dan perasan jeruk nipis bila suka.\nCara membuat: Bakar atau memarkan jahe, kemudian rebus bersama serai dan air selama 10-15 menit. Tambahkan gula merah, saring, dan tuang ke gelas. Beri sedikit jeruk nipis setelah minuman tidak terlalu panas.\n\n4. Es Cao\n[[image:https://images.unsplash.com/photo-1544145945-f90425340c7e?w=1000&q=80|Es cao atau cincau hitam dengan kuah gula merah]]\nBahan: 300 gram cincau hitam, 500 ml santan atau susu, 150 gram gula merah, 100 ml air, daun pandan, dan es batu.\nCara membuat: Masak gula merah, air, dan pandan sampai larut, lalu saring. Potong cincau, masukkan ke gelas, tambahkan kuah gula, santan atau susu, dan es batu. Sajikan segera agar teksturnya tetap segar.\n\n5. Es Tape\n[[image:https://images.unsplash.com/photo-1544145945-f90425340c7e?w=1000&q=80|Es tape singkong dengan rasa manis legit dan aroma fermentasi]]\nBahan: 250 gram tape singkong, 500 ml santan atau susu, sirup secukupnya, es batu, dan potongan nangka bila suka.\nCara membuat: Buang serat tape lalu potong atau haluskan kasar. Masukkan tape ke gelas, tambahkan sirup, santan atau susu, dan es batu. Aduk perlahan agar tekstur tape masih terasa.\n\nMinuman berbahan santan sebaiknya disimpan dalam lemari pendingin bila tidak langsung diminum. Tape merupakan hasil fermentasi, sehingga aroma dan tingkat kemanisannya dapat berbeda menurut lama proses fermentasi.",
            'image_url' => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=1000&q=80',
            'sort_order' => 3
        ]
    ];
}

function ensure_site_articles_table(mysqli $conn): void
{
    static $ready = false;
    if ($ready) {
        return;
    }

    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS site_articles (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $versionColumn = mysqli_query($conn, "SHOW COLUMNS FROM site_articles LIKE 'content_version'");
    if ($versionColumn && mysqli_num_rows($versionColumn) === 0) {
        mysqli_query($conn, "ALTER TABLE site_articles ADD content_version TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER sort_order");
    }

    $countResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM site_articles");
    $count = $countResult ? (int) mysqli_fetch_assoc($countResult)['total'] : 0;
    if ($count === 0) {
        $stmt = mysqli_prepare($conn, "INSERT INTO site_articles (slug, category, title, excerpt, body, image_url, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach (site_article_defaults() as $article) {
            $slug = $article['slug'];
            $category = $article['category'];
            $title = $article['title'];
            $excerpt = $article['excerpt'];
            $body = $article['body'];
            $imageUrl = $article['image_url'];
            $sortOrder = $article['sort_order'];
            mysqli_stmt_bind_param($stmt, "ssssssi", $slug, $category, $title, $excerpt, $body, $imageUrl, $sortOrder);
            mysqli_stmt_execute($stmt);
        }
    }

    $migrationStmt = mysqli_prepare($conn, "UPDATE site_articles SET title = ?, excerpt = ?, body = ?, image_url = ?, sort_order = ?, content_version = 4 WHERE slug = ? AND content_version < 4");
    foreach (site_article_defaults() as $article) {
        $title = $article['title'];
        $excerpt = $article['excerpt'];
        $body = $article['body'];
        $imageUrl = $article['image_url'];
        $sortOrder = (int) $article['sort_order'];
        $slug = $article['slug'];
        mysqli_stmt_bind_param($migrationStmt, "ssssis", $title, $excerpt, $body, $imageUrl, $sortOrder, $slug);
        mysqli_stmt_execute($migrationStmt);
    }
    $ready = true;
}

function get_site_articles(mysqli $conn, string $category): array
{
    ensure_site_articles_table($conn);
    $stmt = mysqli_prepare($conn, "SELECT id, slug, category, title, excerpt, body, image_url, sort_order FROM site_articles WHERE category = ? ORDER BY sort_order ASC, id ASC LIMIT 3");
    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $articles = [];
    while ($article = mysqli_fetch_assoc($result)) {
        $articles[] = $article;
    }
    return $articles;
}

function get_site_article(mysqli $conn, string $slug): ?array
{
    ensure_site_articles_table($conn);
    $stmt = mysqli_prepare($conn, "SELECT id, slug, category, title, excerpt, body, image_url, sort_order FROM site_articles WHERE slug = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $slug);
    mysqli_stmt_execute($stmt);
    $article = mysqli_stmt_get_result($stmt)->fetch_assoc();
    return $article ?: null;
}

function save_site_article(mysqli $conn, array $article, int $adminId): bool
{
    ensure_site_articles_table($conn);
    $stmt = mysqli_prepare($conn, "UPDATE site_articles SET title = ?, excerpt = ?, body = ?, image_url = ?, sort_order = ?, updated_by = ? WHERE id = ? AND category IN ('unik', 'autentik')");
    $title = trim($article['title'] ?? '');
    $excerpt = trim($article['excerpt'] ?? '');
    $body = trim($article['body'] ?? '');
    $imageUrl = trim($article['image_url'] ?? '');
    $sortOrder = (int) ($article['sort_order'] ?? 0);
    $articleId = (int) ($article['id'] ?? 0);
    mysqli_stmt_bind_param($stmt, "ssssiii", $title, $excerpt, $body, $imageUrl, $sortOrder, $adminId, $articleId);
    return mysqli_stmt_execute($stmt);
}

function content_lines(string $text): string
{
    $lines = preg_split('/\r\n|\r|\n/', $text);
    $output = [];
    foreach ($lines as $line) {
        if (preg_match('/^(\d+)\.\s+(.+)$/', trim($line), $matches)) {
            $sectionNumber = htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8');
            $sectionTitle = htmlspecialchars($matches[2], ENT_QUOTES, 'UTF-8');
            $output[] = '<h2 class="article-section-title">' . $sectionNumber . '. ' . $sectionTitle . '</h2>';
            continue;
        }
        if (preg_match('/^\[\[image:(https?:\/\/[^|]+)\|([^\]]+)\]\]$/', trim($line), $matches) || preg_match('/^\[(https?:\/\/[^|]+)\|([^\]]+)\]$/', trim($line), $matches)) {
            $imageUrl = htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8');
            $imageAlt = htmlspecialchars($matches[2], ENT_QUOTES, 'UTF-8');
            $output[] = '<figure class="article-inline-media"><img src="' . $imageUrl . '" alt="' . $imageAlt . '"><figcaption>' . $imageAlt . '</figcaption></figure>';
            continue;
        }
        $output[] = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
    }
    return implode("<br>\n", $output);
}

function content_title(string $title): string
{
    return implode('<br>', array_map(static fn($line) => htmlspecialchars(trim($line), ENT_QUOTES, 'UTF-8'), explode('|', $title)));
}

function price_range_label(array $item): string
{
    $minimum = (int) ($item['harga_min'] ?? 0);
    $maximum = (int) ($item['harga_max'] ?? 0);
    $legacyPrice = (int) ($item['harga'] ?? 0);
    if ($minimum === 0 && $maximum === 0) {
        $minimum = $legacyPrice;
        $maximum = $legacyPrice;
    }
    $minimumLabel = number_format($minimum, 0, ',', '.');
    $maximumLabel = number_format($maximum, 0, ',', '.');
    return $minimum === $maximum ? 'Rp ' . $minimumLabel : 'Rp ' . $minimumLabel . ' - Rp ' . $maximumLabel;
}

function ensure_regional_culinary_catalog(mysqli $conn): void
{
    static $ready = false;
    if ($ready) {
        return;
    }

    $catalog = [
        'kab-bangkalan' => ['Nasi Serpang', 'Soto Bangkalan', 'Tajin Sobih', 'Topak Ladeh', 'Apem Madura'],
        'kab-banyuwangi' => ['Sego Cawuk', 'Pecel Pitik', 'Ayam Kesrut', 'Kue Bagiak'],
        'kab-blitar' => ['Nasi Ampok', 'Pecel Blitar', 'Sambal Tumpang', 'Geti Wijen', 'Es Pleret'],
        'kab-bojonegoro' => ['Sego Buwohan', 'Nasi Flambe', 'Sate Samin', 'Serabi Malang', 'Wedang Tape'],
        'kab-bondowoso' => ['Tape Bondowoso', 'Prol Tape', 'Suwar-suwir', 'Nasi Mamong', 'Tapai Bakar', 'Kopi Arabika Ijen-Raung'],
        'kab-gresik' => ['Nasi Krawu', 'Pudak', 'Otak-otak Bandeng', 'Jubung', 'Bonggolan', 'Sego Roomo'],
        'kab-jember' => ['Tape Proll', 'Pecel Gudeg', 'Nasi Langgi', 'Pia Tape', 'Wedang Cor'],
        'kab-jombang' => ['Sego Sadukan', 'Nasi Kikil', 'Pecel Jombang', 'Onde-onde Jombang', 'Es Degan Siwalan', 'Jenang Kupas'],
        'kab-kediri' => ['Pecel Tumpang', 'Soto Kediri', 'Sate Bekicot', 'Kerupuk Upil', 'Gethuk Pisang'],
        'kab-lamongan' => ['Nasi Boranan', 'Tahu Campur Lamongan', 'Pecel Lele', 'Wingko Babat', 'Jumbrek'],
        'kab-lumajang' => ['Kue Latok', 'Tape Pisang', 'Pisang Agung Lumajang', 'Keripik Pisang', 'Lupis Lumajang', 'Nasi Menir'],
        'kab-madiun' => ['Pecel Madiun', 'Sambal Pecel', 'Nasi Jotos', 'Roti Bluder', 'Kue Semprong'],
        'kab-magetan' => ['Tepo Tahu', 'Tepo Baron', 'Sate Lawu', 'Pecel Magetan', 'Getuk Lendri', 'Jenang Candi'],
        'kab-malang' => ['Bakso Malang', 'Mendol', 'Orem-orem', 'Rawon', 'Apel Malang'],
        'kab-mojokerto' => ['Onde-onde Mojokerto', 'Kerupuk Rambak', 'Keripik Ceker', 'Tahu Campur', 'Es Dawet'],
        'kab-nganjuk' => ['Dumbleg', 'Tepo Mbah Sabar', 'Pecel Nganjuk', 'Onde-onde Ketumbar', 'Getuk Pisang'],
        'kab-ngawi' => ['Lethok', 'Pecel Ngawi', 'Wedang Cemue', 'Ledre', 'Keripik Tempe'],
        'kab-pacitan' => ['Sayur Kalakan', 'Soto Pacitan', 'Sale Pisang', 'Jenang Pacitan', 'Putri Gunung'],
        'kab-pamekasan' => ['Soto Pamekasan', 'Kaldu Kokot', 'Rujak Bubur', 'Campor Lorjuk', 'Apem Madura', 'Kue Apen'],
        'kab-pasuruan' => ['Bipang Jangkar', 'Nasi Punel', 'Kupang Lontong', 'Sate Komoh', 'Klepon Bangil'],
        'kab-ponorogo' => ['Nasi Pecel', 'Dawet Jabung', 'Jenang Mirah', 'Gethuk Golan', 'Tiwul Ponorogo'],
        'kab-probolinggo' => ['Nasi Glepungan', 'Soto Kraksaan', 'Ketan Kratok', 'Tape Probolinggo', 'Keripik Kentang', 'Sirup Pokak'],
        'kab-sampang' => ['Kaldu Sumsum', 'Soto Sampang', 'Tajin Sobih', 'Apem Madura', 'Dhun Adhun', 'Rujak Cingur Madura'],
        'kab-sidoarjo' => ['Bandeng Presto', 'Otak-otak Bandeng', 'Petis Udang', 'Kerupuk Udang'],
        'kab-situbondo' => ['Nasi Karak', 'Tajin Palappa', 'Rujak Gobet', 'Nasi Sodu', 'Sate Ote', 'Kue Klepon'],
        'kab-sumenep' => ['Soto Sabreng', 'Campor Lorjuk', 'Rujak Selingkuh', 'Apem Sumenep', 'Kue Jubada'],
        'kab-trenggalek' => ['Ayam Lodho', 'Nasi Gegog', 'Sego Tiwul', 'Sale Pisang', 'Alen-alen', 'Tempe Kripik'],
        'kab-tuban' => ['Kue Dumbek', 'Ampo', 'Kare Rajungan', 'Legen', 'Nasi Boranan Tuban', 'Sate Menthok'],
        'kab-tulungagung' => ['Nasi Tumpang', 'Sompil', 'Jenang Grendul', 'Geti', 'Sate Kambing Lodho'],
        'kota-malang' => ['Cwie Mie', 'Bakso Malang', 'Rawon', 'Angsle', 'Pia Mangkok'],
        'kota-mojokerto' => ['Onde-onde Mojokerto', 'Keciput Wijen', 'Tahu Tek', 'Sambal Wader', 'Kerupuk Rambak', 'Es Cendol Dawet'],
        'kota-pasuruan' => ['Bipang Jangkar', 'Rawon Pasuruan', 'Kupang Lontong', 'Sate Komoh', 'Klepon'],
        'kota-probolinggo' => ['Ketan Kratok', 'Soto Probolinggo', 'Nasi Glepungan', 'Sirup Pokak', 'Tape Probolinggo', 'Keripik Kentang'],
        'kota-surabaya' => ['Rawon', 'Rujak Cingur', 'Lontong Balap', 'Tahu Tek', 'Sate Klopo', 'Semanggi Surabaya']
    ];

    $detailPath = __DIR__ . '/../database/kuliner_details.json';
    $culinaryDetails = is_file($detailPath)
        ? json_decode(file_get_contents($detailPath), true)
        : [];
    if (!is_array($culinaryDetails)) {
        $culinaryDetails = [];
    }
    $imagePath = __DIR__ . '/../database/kuliner_images.json';
    $culinaryImages = is_file($imagePath)
        ? json_decode(file_get_contents($imagePath), true)
        : [];
    if (!is_array($culinaryImages)) {
        $culinaryImages = [];
    }

    $areaStmt = mysqli_prepare($conn, 'SELECT id, nama FROM daerah WHERE slug = ? LIMIT 1');
    $existsStmt = mysqli_prepare($conn, 'SELECT id, deskripsi, sejarah, resep_bahan, resep_langkah, gambar FROM kuliner WHERE daerah_id = ? AND nama_makanan = ? LIMIT 1');
    $updateStmt = mysqli_prepare($conn, 'UPDATE kuliner SET kategori = ?, deskripsi = ?, sejarah = ?, resep_bahan = ?, resep_langkah = ? WHERE id = ?');
    $updateImageStmt = mysqli_prepare($conn, 'UPDATE kuliner SET gambar = ? WHERE id = ?');
    $insertStmt = mysqli_prepare($conn, 'INSERT INTO kuliner (daerah_id, nama_makanan, slug, wilayah, harga, harga_min, harga_max, rating, jumlah_penilai, kategori, deskripsi, sejarah, resep_bahan, resep_langkah, gambar, is_rekomendasi, publikasi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)');
    $placeStmt = mysqli_prepare($conn, 'INSERT INTO kuliner_tempat (kuliner_id, nama_tempat, alamat, catatan) VALUES (?, ?, ?, ?)');

    foreach ($catalog as $areaSlug => $names) {
        mysqli_stmt_bind_param($areaStmt, 's', $areaSlug);
        mysqli_stmt_execute($areaStmt);
        $area = mysqli_stmt_get_result($areaStmt)->fetch_assoc();
        if (!$area) {
            continue;
        }
        $areaId = (int) $area['id'];
        $areaName = $area['nama'];

        foreach ($names as $name) {
            $detailKey = $areaSlug . '|' . strtolower($name);
            $details = $culinaryDetails[$detailKey] ?? null;
            $customImage = $culinaryImages[$detailKey] ?? null;
            mysqli_stmt_bind_param($existsStmt, 'is', $areaId, $name);
            mysqli_stmt_execute($existsStmt);
            $existing = mysqli_stmt_get_result($existsStmt)->fetch_assoc();
            if ($existing) {
                if ($customImage && $existing['gambar'] !== $customImage) {
                    mysqli_stmt_bind_param($updateImageStmt, 'si', $customImage, $existing['id']);
                    mysqli_stmt_execute($updateImageStmt);
                }
                $hasGeneratedContent = str_starts_with($existing['deskripsi'], $name . ' adalah kuliner khas ')
                    || str_starts_with($existing['resep_bahan'], 'Bahan utama ');
                if ($details && $hasGeneratedContent) {
                    mysqli_stmt_bind_param(
                        $updateStmt,
                        'sssssi',
                        $details['kategori'],
                        $details['deskripsi'],
                        $details['sejarah'],
                        $details['resep_bahan'],
                        $details['resep_langkah'],
                        $existing['id']
                    );
                    mysqli_stmt_execute($updateStmt);
                }
                continue;
            }

            $lowerName = strtolower($name);
            $isDrink = str_contains($lowerName, 'es ') || str_contains($lowerName, 'wedang') || str_contains($lowerName, 'kopi') || str_contains($lowerName, 'sirup') || str_contains($lowerName, 'legen');
            $isSnack = !$isDrink && preg_match('/kue|kerupuk|keripik|onde|getuk|jenang|brem|tape|apem|ledre|suwar|pudak|jubung|wingko|klepon|pia|dumbleg|amp[o]?|alen|sale|prol|bonggolan/i', $name);
            $category = $isDrink ? 'Minuman' : ($isSnack ? 'Makanan Ringan' : 'Makanan Berat');
            $baseSlug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name . '-' . $areaSlug), '-'));
            $imageId = ['1512058564366-18510be2db19', '1547592166-23ac45744acd', '1596797038530-2c107229654b', '1567337710282-00832b415979'][strlen($name) % 4];
            $imageUrl = $customImage ?? ('https://images.unsplash.com/photo-' . $imageId . '?w=900&q=80');
            $priceMin = $isDrink ? 5000 : ($isSnack ? 7000 : 15000);
            $priceMax = $isDrink ? 15000 : ($isSnack ? 25000 : 45000);
            $price = (int) round(($priceMin + $priceMax) / 2);
            $rating = 4.5;
            $reviewCount = 50 + (($areaId * 29 + strlen($name)) % 251);
            $description = $details['deskripsi'] ?? ($name . ' adalah kuliner khas ' . $areaName . ' dengan cita rasa lokal dan cara penyajian yang menjadi bagian dari tradisi masyarakat setempat.');
            $history = $details['sejarah'] ?? ($name . ' dikenal sebagai salah satu sajian yang berkembang dari bahan dan kebiasaan makan masyarakat ' . $areaName . '. Resepnya dapat memiliki variasi di setiap keluarga atau penjual, tetapi karakter utamanya tetap dijaga.');
            $ingredients = $details['resep_bahan'] ?? ('Bahan utama ' . $name . ', bumbu dasar, rempah pilihan, garam, gula, dan bahan pelengkap sesuai selera.');
            $steps = $details['resep_langkah'] ?? 'Siapkan bahan dan bersihkan dengan baik. Olah menggunakan teknik tradisional hingga matang dan bumbu meresap. Sajikan hangat atau dingin sesuai karakter hidangan.';
            $category = $details['kategori'] ?? $category;
            $publication = 'Katalog Kuliner';
            mysqli_stmt_bind_param($insertStmt, 'isssiiidisssssss', $areaId, $name, $baseSlug, $areaName, $price, $priceMin, $priceMax, $rating, $reviewCount, $category, $description, $history, $ingredients, $steps, $imageUrl, $publication);
            if (mysqli_stmt_execute($insertStmt)) {
                $kulinerId = mysqli_insert_id($conn);
                $placeName = $name;
                $address = $areaName . ' dan sekitarnya';
                $note = 'Informasi kuliner khas yang dapat ditemukan di wilayah setempat.';
                mysqli_stmt_bind_param($placeStmt, 'isss', $kulinerId, $placeName, $address, $note);
                mysqli_stmt_execute($placeStmt);
            }
        }
    }
    $ready = true;
}
?>
