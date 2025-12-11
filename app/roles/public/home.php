<?php
$title  = "Beranda | IVSS";
$active = "home";

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect(); // PDO

function safe($v){
    return htmlspecialchars((string)(isset($v) ? $v : ""), ENT_QUOTES, 'UTF-8');
}

// =========================
// 1. Ambil info lab
// =========================
$sql  = "SELECT * FROM lab_info LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$lab = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$lab) $lab = array();

// =========================
// 2. Ambil fasilitas
// =========================
$sql  = "SELECT * FROM fasilitas ORDER BY fasilitas_id DESC LIMIT 9";
$stmt = $conn->prepare($sql);
$stmt->execute();
$fasilitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =========================
// 3. Ambil mata kuliah
// =========================
$sql  = "SELECT * FROM mata_kuliah ORDER BY semester ASC LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->execute();
$matkul = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ============================
// 4. Ambil dokumentasi (foto)
//   type_file diisi nama file
// ============================
$sql = "
    SELECT *
    FROM act_documentation
    WHERE type_file ILIKE '%.jpg'
       OR type_file ILIKE '%.jpeg'
       OR type_file ILIKE '%.png'
       OR type_file ILIKE '%.webp'
    ORDER BY documentation_id DESC
    LIMIT 8
";
$stmt = $conn->prepare($sql);
$stmt->execute();
$dokumentasi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ============================
// 5. Berita terbaru
// ============================
$sql = "
    SELECT b.*, u.username
    FROM berita b
    LEFT JOIN users u ON b.user_id = u.user_id
    ORDER BY b.tgl_post DESC
    LIMIT 3
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$berita = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ============================
// 6. Profil dosen
// ============================
$sql  = "SELECT * FROM dosen LIMIT 9";
$stmt = $conn->prepare($sql);
$stmt->execute();
$dosen = $stmt->fetchAll(PDO::FETCH_ASSOC);

function excerpt_home($text, $len = 70){
  $text = trim(strip_tags($text));
  if (function_exists('mb_strlen')) {
      if (mb_strlen($text, 'UTF-8') <= $len) return $text;
      return mb_substr($text, 0, $len, 'UTF-8') . '...';
  } else {
      if (strlen($text) <= $len) return $text;
      return substr($text, 0, $len) . '...';
  }
}

ob_start();
?>

<!-- Hero Section -->
<section id="hero" class="hero section">
  <div class="container">
    <div class="row gy-4">
      <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
        <h1 data-aos="fade-up" style="color:#FFFFFF;">
          <?= safe(isset($lab['nama']) ? $lab['nama'] : 'Nama Lab Belum Diisi'); ?>
        </h1>
        <p data-aos="fade-up" data-aos-delay="100" style="color:#FFFFFF;">
          <?= safe(isset($lab['deskripsi']) ? $lab['deskripsi'] : 'Deskripsi belum diisi.'); ?>
        </p>
        <div class="d-flex flex-column flex-md-row" data-aos="fade-up" data-aos-delay="200">
          <a href="#form-pendaftaran" class="btn-get-started">
            Bergabung <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /Hero Section -->

<!-- About Section -->
<section id="about" class="about section">
  <div class="container" data-aos="fade-up">
    <div class="row gx-0">
      <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-up" data-aos-delay="200">
        <div class="content">
          <h3>Kami adalah</h3>
          <h2><?= !empty($lab['nama']) ? safe($lab['nama']) : 'Nama lab belum diisi'; ?></h2>
          <p><?= !empty($lab['deskripsi']) ? safe($lab['deskripsi']) : 'Deskripsi lab belum diisi.'; ?></p>
          <div class="text-center text-lg-start">
            <a href="../public/about.php" class="btn-read-more d-inline-flex align-items-center justify-content-center align-self-center">
              <span>Read More</span>
              <i class="bi bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
      <div class="col-lg-6 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="200">
        <img src="../../../public/assets/img/about1.jpeg" class="img-fluid" alt="">
      </div>
    </div>
  </div>
</section>
<!-- /About Section -->

<!-- Aktivitas (Dokumentasi) -->
<section id="clients" class="clients section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Aktivitas Kami</h2>
    <p>Cuplikan dokumentasi kegiatan di laboratorium.</p>
  </div>
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="swiper init-swiper">
      <script type="application/json" class="swiper-config">
        {
          "loop": true,
          "speed": 600,
          "autoplay": { "delay": 4000 },
          "slidesPerView": 1,
          "spaceBetween": 30,
          "centeredSlides": true,
          "pagination": {
            "el": ".swiper-pagination",
            "type": "bullets",
            "clickable": true
          },
          "breakpoints": {
            "576": { "slidesPerView": 2, "spaceBetween": 30 },
            "768": { "slidesPerView": 3, "spaceBetween": 40 },
            "1200": { "slidesPerView": 4, "spaceBetween": 50 }
          }
        }
      </script>
      <div class="swiper-wrapper align-items-center">
        <?php if (!empty($dokumentasi)): ?>
          <?php
            $dokumentasiHome = array_slice($dokumentasi, 0, 10);
            foreach ($dokumentasiHome as $dok):
              $img = "../../../public/uploads/dokumentasi/" . safe($dok['type_file']);
          ?>
            <div class="swiper-slide">
              <img src="<?= $img; ?>" class="img-fluid" alt="Dokumentasi">
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="swiper-slide d-flex justify-content-center align-items-center" style="height: 200px;">
            <p class="text-center mb-0">Belum ada dokumentasi.</p>
          </div>
        <?php endif; ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
    <div class="text-center mt-3">
      <a href="dokumentasi.php" class="btn btn-outline-primary btn-sm">
        Lihat Semua Dokumentasi
      </a>
    </div>
  </div>
</section>
<!-- /Aktivitas Section -->

<!-- Values (Visi & Misi) -->
<section id="values" class="values section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Visi &amp; Misi</h2>
    <p>Visi &amp; Misi</p>
  </div>
  <div class="container">
    <div class="row gy-4 container d-flex justify-content-center">
      <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card">
          <h3>Visi</h3>
          <p><?= !empty($lab['visi']) ? safe($lab['visi']) : 'Visi lab belum diisi.'; ?></p>
        </div>
      </div>
      <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card">
          <h3>Misi</h3>
          <p><?= !empty($lab['misi']) ? safe($lab['misi']) : 'Misi lab belum diisi.'; ?></p>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /Values Section -->

<!-- Services (Riset) -->
<section id="services" class="services section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Riset</h2>
    <p>Fokus riset</p>
  </div>
  <div class="container">
    <div class="row gy-4 d-flex justify-content-center">
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="service-item item-cyan position-relative">
          <div class="icon"><i class="bi bi-eye"></i></div>
          <h3>Intelligence Vision</h3>
          <p>Teknologi yang memungkinkan mesin “melihat” dan memahami lingkungan, untuk pengenalan objek, deteksi gerakan, dan sistem cerdas.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="service-item item-orange position-relative">
          <div class="icon"><i class="bi bi-cpu"></i></div>
          <h3>Smart System</h3>
          <p>Sistem adaptif yang pintar, mampu mengambil keputusan otomatis menggunakan sensor, AI, dan IoT, untuk smart home, industri, dan layanan inovatif.</p>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /Services Section -->

<!-- Recent Posts (Berita) -->
<section id="recent-posts" class="recent-posts section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Recent Posts</h2>
    <p>Berita</p>
  </div>
  <div class="container">
    <div class="row gy-5">
      <?php if (!empty($berita)): ?>
        <?php foreach ($berita as $b): 
          $img = empty($b['foto'])
            ? "../../../public/assets/img/blog/blog-1.jpg"
            : "../../../public/uploads/berita/" . safe($b['foto']);
        ?>
          <div class="col-xl-4 col-md-6">
            <div class="post-item position-relative h-100">
              <div class="post-img position-relative overflow-hidden">
                <img src="<?= $img; ?>" class="img-fluid" alt="<?= safe($b['judul']); ?>">
                <span class="post-date">
                  <?= safe(date('F d', strtotime($b['tgl_post']))); ?>
                </span>
              </div>
              <div class="post-content d-flex flex-column">
                <h3 class="post-title"><?= safe($b['judul']); ?></h3>
                <div class="meta d-flex align-items-center">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-person"></i>
                    <span class="ps-2">
                      <?= safe($b['username'] ?: 'Admin'); ?>
                    </span>
                  </div>
                  <span class="px-3 text-black-50">/</span>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-folder2"></i>
                    <span class="ps-2"><?= safe($b['kategori']); ?></span>
                  </div>
                </div>
                <hr>
                <a href="berita_detail.php?id=<?= (int)$b['berita_id']; ?>" class="readmore stretched-link">
                  <span>Lihat berita selengkapnya</span><i class="bi bi-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-center">Belum ada berita.</p>
      <?php endif; ?>
    </div>
    <div class="text-center mt-3">
      <a href="berita.php" class="btn btn-outline-primary btn-sm">Lihat Semua Berita</a>
    </div>
  </div>
</section>
<!-- /Recent Posts Section -->

<!-- Team Section (Home) -->
<section id="team" class="team section team-home">
  <div class="container section-title" data-aos="fade-up">
    <h2>Team</h2>
    <p>Anggota Lab</p>
  </div>

  <div class="container">
    <div class="row gy-4">

      <?php if (!empty($dosen)): ?>
        <?php
          // Hanya tampilkan 4 dosen di home (bisa diganti 6, 8, dll)
          $dosenHome = array_slice($dosen, 0, 8);
          foreach ($dosenHome as $d):
            $img = "../../../public/uploads/dosen/" . safe($d['foto']);
        ?>
          <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="team-member">
              <div class="member-img">
                <img src="<?= $img; ?>" class="img-fluid" alt="<?= safe($d['nama']); ?>">
              </div>
              <div class="member-info">

                <!-- Nama jadi link ke detail profil -->
                <h4>
                  <a href="anggota_detail.php?nip=<?= urlencode($d['nip']); ?>"
                     class="member-link">
                    <?= safe($d['nama']); ?>
                  </a>
                </h4>

                <span><?= safe($d['jabatan']); ?></span>
                <p><?= safe($d['nidn']); ?></p>
                <p><?= safe($d['email']); ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-center">Belum ada dosen.</p>
      <?php endif; ?>

    </div>

    <div class="text-center mt-3">
      <a href="anggota.php" class="btn btn-primary">Lihat Semua Anggota</a>
    </div>
  </div>
</section>
<!-- /Team Section -->


<!-- Fasilitas Section (Portfolio) -->
<section id="portfolio" class="portfolio section portfolio-home">
  <div class="container section-title" data-aos="fade-up">
    <h2>Fasilitas</h2>
    <p>Fasilitas &amp; Peralatan</p>
  </div>

  <div class="container">
    <div class="isotope-layout" data-default-filter="*" data-layout="fitRows" data-sort="original-order">

      <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
        <li data-filter="*" class="filter-active">All</li>
        <li data-filter=".filter-app">Peralatan</li>
        <li data-filter=".filter-product">Fasilitas</li>
      </ul>

      <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">
        <?php if (!empty($fasilitas)): ?>

          <?php
          // Biar nanti kalau datanya banyak, yg di home cuma 12
          $fasilitasHome = array_slice($fasilitas, 0, 8);

          foreach ($fasilitasHome as $f):

            $kategori = strtolower(trim($f['kategori']));
            $filterClass = 'filter-product';
            if ($kategori === 'peralatan') {
              $filterClass = 'filter-app';
            } elseif ($kategori === 'fasilitas') {
              $filterClass = 'filter-product';
            }

            $img = empty($f['foto'])
              ? "../../../public/assets/img/facility-placeholder.jpg"
              : "../../../public/uploads/fasilitas/" . safe($f['foto']);
          ?>
            <!-- 4 kolom di layar besar -->
            <div class="col-xl-3 col-lg-4 col-md-6 portfolio-item isotope-item <?= $filterClass; ?>">
              <div class="portfolio-content h-100">
                <img src="<?= $img; ?>" class="img-fluid" alt="<?= safe($f['nama']); ?>">
                <div class="portfolio-info">
                  <h4><?= safe($f['nama']); ?></h4>
                  <p><?= htmlspecialchars(excerpt_home($f['deskripsi'], 60)) ?></p>

                  <a href="<?= $img; ?>"
                     title="<?= safe($f['nama']); ?>"
                     data-gallery="portfolio-gallery-<?= $filterClass; ?>"
                     class="glightbox preview-link">
                    <i class="bi bi-zoom-in"></i>
                  </a>

                  <a href="fasilitas.php?id=<?= (int)$f['fasilitas_id']; ?>"
                     title="More Details"
                     class="details-link">
                    <i class="bi bi-link-45deg"></i>
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center">Belum ada fasilitas.</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="text-center mt-3">
      <a href="fasilitas.php" class="btn btn-outline-primary btn-sm">
        Lihat Semua Fasilitas
      </a>
    </div>
  </div>
</section>
<!-- /Fasilitas Section -->


<!-- Perkuliahan (Mata Kuliah) -->
<section id="testimonials" class="testimonials section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Perkuliahan</h2>
    <p>Mata Kuliah terkait</p>
  </div>

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="swiper init-swiper">
      <script type="application/json" class="swiper-config">
        {
          "loop": true,
          "speed": 600,
          "autoplay": { "delay": 5000 },
          "slidesPerView": "auto",
          "pagination": {
            "el": ".swiper-pagination",
            "type": "bullets",
            "clickable": true
          },
          "breakpoints": {
            "320": { "slidesPerView": 1, "spaceBetween": 40 },
            "1200": { "slidesPerView": 3, "spaceBetween": 1 }
          }
        }
      </script>
      <div class="swiper-wrapper">
        <?php foreach ($matkul as $m): ?>
          <div class="swiper-slide">
            <div class="testimonial-item">
              <div class="stars">
                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
              </div>
              <p>
                Mata kuliah <strong><?= safe($m['nama_matkul']); ?></strong>
                ditawarkan pada semester <strong><?= safe($m['semester']); ?></strong>
                untuk prodi <strong><?= safe($m['prodi']); ?></strong>.
                Total SKS: <strong><?= safe($m['sks']); ?></strong>.
              </p>
              <div class="profile mt-auto">
                <img src="assets/img/default-profile.png" class="testimonial-img" alt="">
                <h3><?= safe($m['nama_matkul']); ?></h3>
                <h4>Dosen: <?= safe(isset($m['nip']) ? $m['nip'] : 'Belum diisi'); ?></h4>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>
</section>
<!-- /Perkuliahan Section -->

<!-- Contact / Pendaftaran -->
<section id="contact" class="contact section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Contact</h2>
    <p>Contact Us</p>
  </div>

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row gy-4">

      <!-- LEFT: info -->
      <div class="col-lg-6">
        <div class="mb-4" data-aos="fade-right" data-aos-delay="150">
          <h3 class="fw-bold">Informasi Kontak</h3>
          <p class="text-muted">Anda dapat menghubungi kami melalui kontak berikut.</p>
        </div>

        <div class="row gy-4">
          <div class="col-md-6">
            <div class="info-item" data-aos="fade" data-aos-delay="200">
              <i class="bi bi-geo-alt"></i>
              <h3>Address</h3>
              <p><?= !empty($lab['alamat']) ? safe($lab['alamat']) : 'Belum ada alamat'; ?></p>
            </div>
          </div>

          <div class="col-md-6">
            <div class="info-item" data-aos="fade" data-aos-delay="300">
              <i class="bi bi-telephone"></i>
              <h3>Call Us</h3>
              <p><?= !empty($lab['no_telp']) ? safe($lab['no_telp']) : 'Belum ada nomor telepon'; ?></p>
            </div>
          </div>

          <div class="col-md-6">
            <div class="info-item" data-aos="fade" data-aos-delay="400">
              <i class="bi bi-envelope"></i>
              <h3>Email Us</h3>
              <p><?= !empty($lab['email']) ? safe($lab['email']) : 'Belum ada email'; ?></p>
            </div>
          </div>

          <div class="col-md-6">
            <div class="info-item" data-aos="fade" data-aos-delay="500">
              <i class="bi bi-clock"></i>
              <h3>Open Hours</h3>
              <p>Monday - Friday</p>
              <p>8:00AM - 04:00PM</p>
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT: form pendaftaran -->
      <div class="col-lg-6" id="form-pendaftaran">
        <div class="mb-4" data-aos="fade-left" data-aos-delay="150">
          <h3 class="fw-bold">Form Pendaftaran</h3>
          <p class="text-muted">Silakan bergabung dengan mengisi form berikut.</p>
        </div>

        <form action="registrasi_add.php" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="200">
          <div class="row gy-4">
            <div class="col-md-6">
              <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
            </div>
            <div class="col-md-6">
              <input type="text" name="nim" class="form-control" placeholder="NIM">
            </div>
            <div class="col-md-6">
              <input type="email" name="email" class="form-control" placeholder="Email Aktif" required>
            </div>
            <div class="col-md-6">
              <input type="text" name="prodi" class="form-control" placeholder="Program Studi">
            </div>
            <div class="col-12">
              <input type="number" name="angkatan" class="form-control"
                     placeholder="Angkatan (contoh: 2022)"
                     min="2020" max="2050">
            </div>
            <div class="col-12">
              <textarea class="form-control" name="alasan" rows="5" placeholder="Alasan mendaftar"></textarea>
            </div>
            <div class="col-12 text-center">
              <div class="loading">Loading</div>
              <div class="error-message"></div>
              <div class="sent-message">Pendaftaran berhasil dikirim!</div>
              <button type="submit">Daftar Sekarang</button>
            </div>
          </div>
        </form>
      </div>

    </div>
  </div>
</section>
<!-- /Contact Section -->

<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>
