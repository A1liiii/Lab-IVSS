<?php
$title = "Beranda | IVSS";
$active = "home";

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect(); // PDO

// =========================
// 1. Ambil 1 data lab
// =========================
$sql = "SELECT * FROM lab_info LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();

$lab = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$lab) {
    $lab = [];
}

// =========================
// 2. Ambil semua fasilitas
// =========================
$sql = "SELECT * FROM fasilitas ORDER BY fasilitas_id DESC LIMIT 9";
$stmt = $conn->prepare($sql);
$stmt->execute();

$fasilitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =========================
// 3. Ambil semua mata kuliah
// =========================
$sql = "SELECT * FROM mata_kuliah ORDER BY semester ASC LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->execute();

$matkul = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ============================
// 4. Ambil dokumentasi (foto)
// ============================
$sql = "SELECT * FROM act_documentation 
        WHERE type_file LIKE '%.jpg' 
           OR type_file LIKE '%.jpeg'
           OR type_file LIKE '%.png'
           OR type_file LIKE '%.webp'
        ORDER BY documentation_id DESC LIMIT 8";

$stmt = $conn->prepare($sql);
$stmt->execute();

$dokumentasi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil 3 berita terbaru pakai PDO
$sql = "SELECT * FROM berita ORDER BY tgl_post DESC LIMIT 3";
$stmt = $conn->prepare($sql);
$stmt->execute();
$berita = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil 9 profil dosen
$sql = "SELECT * FROM dosen LIMIT 9";
$stmt = $conn->prepare($sql);
$stmt->execute();
$dosen = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>
    <!-- Hero Section -->
    <section id="hero" class="hero section">

      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
            <h1 data-aos="fade-up" style="color:#FFFFFF;">
              <?= htmlspecialchars(isset($lab['nama']) ? $lab['nama'] : 'Nama Lab Belum Diisi'); ?>
            </h1>
            <p data-aos="fade-up" data-aos-delay="100" style="color:#FFFFFF;">
              <?= htmlspecialchars(isset($lab['deskripsi']) ? $lab['deskripsi'] : 'Deskripsi belum diisi.'); ?>
            </p>
            <div class="d-flex flex-column flex-md-row" data-aos="fade-up" data-aos-delay="200">
            <a href="#form-pendaftaran" class="btn-get-started">Bergabung <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </div>
    </section><!-- /Hero Section -->
        <!-- tentang Section -->
    <section id="about" class="about section">
      <div class="container" data-aos="fade-up">
        <div class="row gx-0">
          <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-up" data-aos-delay="200">
            <div class="content">
              <h3>Kami adalah</h3>
              <h2>
                <?= !empty($lab['nama']) ? htmlspecialchars($lab['nama']) : 'Nama lab belum diisi' ?>
              </h2>
              <p>
                <?= !empty($lab['deskripsi']) ? htmlspecialchars($lab['deskripsi']) : 'Deskripsi lab belum diisi.' ?>
              </p>
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
    </section><!-- /About Section -->


    <!-- Clients Section -->
    <section id="clients" class="clients section">
      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Aktivitas Kami</h2>
      </div><!-- End Section Title -->
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              },
              "breakpoints": {
                "320": {
                  "slidesPerView": 2,
                  "spaceBetween": 40
                },
                "480": {
                  "slidesPerView": 3,
                  "spaceBetween": 60
                },
                "640": {
                  "slidesPerView": 4,
                  "spaceBetween": 80
                },
                "992": {
                  "slidesPerView": 6,
                  "spaceBetween": 120
                }
              }
            }
          </script>
            <div class="swiper-wrapper align-items-center">

              <?php if (!empty($dokumentasi)): ?>
                  <?php foreach ($dokumentasi as $dok): ?>
                      <div class="swiper-slide">
                        <img src="../../../public/uploads/dokumentasi/<?= htmlspecialchars($dok['type_file']) ?>" 
                          class="img-fluid">
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
        </div>
      </section><!-- /Clients Section -->

    <!-- Values Section -->
      <!-- Bootstrap Icons (kalau belum ada di <head>) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
/* ===== Visi & Misi Style ===== */
.vm-card{
  position: relative;
  background: #fff;
  border-radius: 26px;
  padding: 34px 26px 28px;
  box-shadow: 0 16px 40px rgba(0,0,0,.10);
  overflow: visible;
  min-height: 320px;
}

/* base warna bawah */
.vm-card::after{
  content:"";
  position:absolute;
  left: 18px;
  right: 18px;
  bottom: -18px;
  height: 54px;
  border-radius: 0 0 26px 26px;
  filter: drop-shadow(0 12px 18px rgba(0,0,0,.12));
  z-index: 0;
}

/* ikon atas (dekoratif, bukan panah) */
.vm-top{
  position: absolute;
  top: -34px;
  left: 22px;
  right: 22px;
  display:flex;
  align-items:center;
  justify-content: flex-start; /* biar cuma ikon */
  z-index: 3;
}

.vm-icon{
  width: 78px;
  height: 78px;
  border-radius: 50%;
  display:flex;
  align-items:center;
  justify-content:center;
  box-shadow: 0 14px 24px rgba(0,0,0,.12);
}

.vm-icon i{ font-size: 32px; }

/* isi card */
.vm-body{
  position: relative;
  z-index: 2;
  padding-top: 26px;
  text-align: center;
}

.vm-title{
  letter-spacing: 3px;
  font-weight: 800;
  margin: 10px 0 12px;
}

.vm-text{
  margin: 0;
  font-size: 15.5px;
  line-height: 1.75;
  color: #444;
  word-break: break-word;
  text-align: center;
}

/* ===== Variasi warna Visi (orange) ===== */
.vm-visi .vm-icon{
  background: radial-gradient(circle at 30% 30%, #ffcc66 0%, #ff9f1a 45%, #ff7a00 100%);
  color:#fff;
}
.vm-visi::after{
  background: linear-gradient(90deg, #ffb100 0%, #ff7a00 100%);
}
.vm-visi .vm-title{ color:#ff9a0a; }

/* ===== Variasi warna Misi (blue) ===== */
.vm-misi .vm-icon{
  background: radial-gradient(circle at 30% 30%, #56d4ff 0%, #1f8cff 45%, #0b57d0 100%);
  color:#fff;
}
.vm-misi::after{
  background: linear-gradient(90deg, #35d2ff 0%, #0b57d0 100%);
}
.vm-misi .vm-title{ color:#1f84ff; }

/* ===== LIST MISI: bullet biru + justify ===== */
.vm-misi-list{
  margin: 0;
  padding-left: 0;
  list-style: none;
  text-align: justify;          /* rata kanan-kiri */
}

.vm-misi-list li{
  position: relative;
  padding-left: 22px;
  margin-bottom: 10px;
  font-size: 15.5px;
  line-height: 1.75;
  color: #333;
}

/* bullet biru */
.vm-misi-list li::before{
  content: "";
  width: 8px;
  height: 8px;
  background: #1f84ff;
  border-radius: 50%;
  position: absolute;
  left: 0;
  top: 9px;
}

/* responsif */
@media (max-width: 576px){
  .vm-card{ padding: 34px 18px 24px; min-height: auto; }
  .vm-top{ left: 14px; right: 14px; }
  .vm-icon{ width: 70px; height: 70px; }
}
</style>

<!-- Section Title -->
<div class="container section-title" data-aos="fade-up">
  <h2>Visi & Misi</h2>
  <p>Visi & Misi<br></p>
</div><!-- End Section Title -->

<div class="container">
  <div class="row gy-4 justify-content-center">

    <!-- VISI -->
    <div class="col-lg-5 col-md-10" data-aos="fade-up" data-aos-delay="100">
      <div class="vm-card vm-visi">

        <div class="vm-top">
          <div class="vm-icon">
            <i class="bi bi-stars"></i>
          </div>
        </div>

        <div class="vm-body">
          <h3 class="vm-title">VISI</h3>
          <p class="vm-text">
            <?= !empty($lab['visi']) ? htmlspecialchars($lab['visi']) : 'Visi lab belum diisi.' ?>
          </p>
        </div>

      </div>
    </div><!-- End VISI -->

    <!-- MISI -->
    <div class="col-lg-5 col-md-10" data-aos="fade-up" data-aos-delay="200">
      <div class="vm-card vm-misi">

        <div class="vm-top">
          <div class="vm-icon">
            <i class="bi bi-list-check"></i>
          </div>
        </div>

        <div class="vm-body">
          <h3 class="vm-title">MISI</h3>

          <?php
            $misiText = !empty($lab['misi']) ? $lab['misi'] : '';
            // pecah per kalimat berdasarkan titik.
            $misiList = array_filter(array_map('trim', preg_split('/\.(\s|$)/', $misiText)));
          ?>

          <?php if (!empty($misiList)): ?>
            <ul class="vm-misi-list">
              <?php foreach ($misiList as $item): ?>
                <li><?= htmlspecialchars($item) ?>.</li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="vm-text">Misi lab belum diisi.</p>
          <?php endif; ?>

        </div>

      </div>
    </div><!-- End MISI -->

  </div>
</div>
    </section><!-- /Values Section -->

    <!-- Services Section -->
    <section id="services" class="services section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Riset</h2>
        <p>Fokus riset<br></p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4 d-flex justify-content-center">

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="service-item item-cyan position-relative">
            <div class="icon">
              <i class="bi bi-eye"></i>
            </div>
              <h3>Intelligence Vision</h3>
              <p>Teknologi yang memungkinkan mesin “melihat” dan memahami lingkungan, untuk pengenalan objek, deteksi gerakan, dan sistem cerdas.</p>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item item-orange position-relative">
            <div class="icon">
              <i class="bi bi-cpu"></i>
            </div>
              <h3>Smart System</h3>
              <p>Sistem adaptif yang pintar, mampu 
                mengambil keputusan otomatis menggunakan sensor, AI, dan IoT, untuk smart home, industri, dan layanan inovatif.</p>
            </div>
          </div><!-- End Service Item -->

        </div>
      </div>
    </section><!-- /Services Section -->

    <!-- Recent Posts Section -->
    <section id="recent-posts" class="recent-posts section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Recent Posts</h2>
        <p>Berita</p>
      </div><!-- End Section Title -->
      <div class="container">
        <div class="row gy-5">
          <?php if (!empty($berita)): ?>
          <?php foreach ($berita as $b): ?>
                <div class="col-xl-4 col-md-6">
                    <div class="post-item position-relative h-100">
                        <div class="post-img position-relative overflow-hidden">
                            <img src="../../../public/uploads/berita/<?= htmlspecialchars($b['foto']) ?>" 
                                class="img-fluid" 
                                alt="<?= htmlspecialchars($b['judul']) ?>">
                            <span class="post-date">
                                <?= date('F d', strtotime($b['tgl_post'])) ?>
                            </span>
                        </div>
                        <div class="post-content d-flex flex-column">
                            <h3 class="post-title">
                                <?= htmlspecialchars($b['judul']) ?>
                            </h3>
                            <div class="meta d-flex align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person"></i> 
                                    <span class="ps-2">
                                        <?= htmlspecialchars($b['user_id']) ?>
                                    </span>
                                </div>
                                <span class="px-3 text-black-50">/</span>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-folder2"></i> 
                                    <span class="ps-2">
                                        <?= htmlspecialchars($b['kategori']) ?>
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <a href="berita_detail.php?id=<?= $b['berita_id'] ?>" 
                              class="readmore stretched-link">
                              <span>Read More</span><i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
        </div>
            <?php endforeach; ?><?php else: ?>
              <p class="text-center">Belum ada berita.</p>
            <?php endif; ?>

          </div>
      </div>
    </section><!-- /Recent Posts Section -->

    <!-- Team Section -->
    <section id="team" class="team section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Team</h2>
        <p>Anggota Lab</p>
      </div><!-- End Section Title -->
      <div class="container">
          <div class="swiper mySwiper">
            <div class="swiper-wrapper">
            <?php if (!empty($dosen)): ?>
              <?php foreach ($dosen as $d): ?>
                  <div class="swiper-slide">
                      <div class="team-member">
                          <div class="member-img">
                              <img src="public/uploads/dosen/<?php echo htmlspecialchars($d['foto']); ?>"
                                  class="img-fluid"
                                  alt="<?php echo htmlspecialchars($d['nama']); ?>">
                          </div>
                          <div class="member-info">
                              <h4><?php echo htmlspecialchars($d['nama']); ?></h4>
                              <span><?php echo htmlspecialchars($d['jabatan']); ?></span>
                              <p><?php echo htmlspecialchars($d['nidn']); ?></p>
                              <p><?php echo htmlspecialchars($d['email']); ?></p>
                          </div>
                      </div>
                  </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-center">Belum ada dosen.</p>
            <?php endif; ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
          </div>
          <div class="text-center mt-3">
            <a href="anggota.php" class="btn btn-primary">Lihat Semua Anggota</a>
          </div>
      </div>
    </section> <!-- /Team Section -->

    <!-- Portfolio Section -->
    <section id="portfolio" class="portfolio section">
      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Fasilitas</h2>
        <p>Fasilitas & Peralatan</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

          <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
            <li data-filter="*" class="filter-active">All</li>
            <li data-filter=".filter-app">Peralatan</li>
            <li data-filter=".filter-product">Fasilitas</li>
            <li data-filter=".filter-branding">Ruangan</li>
          </ul><!-- End Portfolio Filters -->

          <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">

            <?php foreach ($fasilitas as $f): ?>

              <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-<?=
                htmlspecialchars($f['kategori'])
              ?>">
                <div class="portfolio-content h-100">

                  <img src="<?= htmlspecialchars($f['foto']) ?>" class="img-fluid" alt="">

                  <div class="portfolio-info">
                    <h4><?= htmlspecialchars($f['nama']) ?></h4>
                    <p><?= htmlspecialchars($f['deskripsi']) ?></p>

                    <a href="<?= htmlspecialchars($f['foto']) ?>"
                        title="<?= htmlspecialchars($f['nama']) ?>"
                        data-gallery="portfolio-gallery-<?= htmlspecialchars($f['kategori']) ?>"
                        class="glightbox preview-link">
                        <i class="bi bi-zoom-in"></i>
                    </a>

                    <a href="portfolio-details.php?id=<?= $f['fasilitas_id'] ?>"
                        title="More Details"
                        class="details-link">
                        <i class="bi bi-link-45deg"></i>
                    </a>
                    </div>

                  </div>
              </div>

              <?php endforeach; ?>

          </div>
          <!-- End Portfolio Container -->

        </div>

      </div>

    </section><!-- /Portfolio Section -->

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Perkuliahan</h2>
        <p>Mata Kuliah terkait<br></p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              },
              "breakpoints": {
                "320": {
                  "slidesPerView": 1,
                  "spaceBetween": 40
                },
                "1200": {
                  "slidesPerView": 3,
                  "spaceBetween": 1
                }
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
                  Mata kuliah <strong><?= htmlspecialchars($m['nama_matkul']) ?></strong>  
                  ditawarkan pada semester <strong><?= htmlspecialchars($m['semester']) ?></strong>  
                  untuk prodi <strong><?= htmlspecialchars($m['prodi']) ?></strong>.  
                  Total SKS: <strong><?= htmlspecialchars($m['sks']) ?></strong>.
                </p>
                <div class="profile mt-auto">
                  <img src="assets/img/default-profile.png"
                      class="testimonial-img" alt="">
                  <h3><?= htmlspecialchars($m['nama_matkul']) ?></h3>
                  <h4>Dosen: <?= htmlspecialchars($m['nip'] ?: 'Belum diisi') ?></h4>
                </div>
              </div>
            </div>

          <?php endforeach; ?>

          </div>
          <div class="swiper-pagination"></div>
        </div>
      </div>
    </section><!-- /Testimonials Section -->
    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Contact</h2>
        <p>Contact Us</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <!-- LEFT SIDE — CONTACT INFO -->
      <div class="col-lg-6">

        <!-- Left Title -->
        <div class="mb-4" data-aos="fade-right" data-aos-delay="150">
          <h3 class="fw-bold">Informasi Kontak</h3>
          <p class="text-muted">Anda dapat menghubungi kami melalui kontak berikut.</p>
        </div>

        <div class="row gy-4">

          <div class="col-md-6">
            <div class="info-item" data-aos="fade" data-aos-delay="200">
              <i class="bi bi-geo-alt"></i>
              <h3>Address</h3>
              <p>
                <?php 
                if (!empty($lab['alamat'])) {
                    echo htmlspecialchars($lab['alamat']);
                } else {
                    echo 'Belum ada alamat';
                }
                ?>
              </p>
            </div>
          </div><!-- End Info Item -->

          <div class="col-md-6">
            <div class="info-item" data-aos="fade" data-aos-delay="300">
              <i class="bi bi-telephone"></i>
              <h3>Call Us</h3>
              <p>
              <?php 
                if (!empty($lab['no_telp'])) {
                    echo htmlspecialchars($lab['no_telp']);
                } else {
                    echo 'Belum ada nomor telepon';
                }
                ?>
              </p>
            </div>
          </div><!-- End Info Item -->

          <div class="col-md-6">
            <div class="info-item" data-aos="fade" data-aos-delay="400">
              <i class="bi bi-envelope"></i>
              <h3>Email Us</h3>
              <p>
              <?php 
                if (!empty($lab['email'])) {
                    echo htmlspecialchars($lab['email']);
                } else {
                    echo 'Belum ada email';
                }
                ?>
              </p>
            </div>
          </div><!-- End Info Item -->

          <div class="col-md-6">
            <div class="info-item" data-aos="fade" data-aos-delay="500">
              <i class="bi bi-clock"></i>
              <h3>Open Hours</h3>
              <p>Monday - Friday</p>
              <p>8:00AM - 04:00PM</p>
            </div>
          </div><!-- End Info Item -->

        </div>

      </div><!-- End Left Column -->

      <!-- RIGHT SIDE — CONTACT FORM -->
      <div class="col-lg-6" id="form-pendaftaran">

        <!-- Right Title -->
        <div class="mb-4" data-aos="fade-left" data-aos-delay="150">
          <h3 class="fw-bold">Form Pendaftaran</h3>
          <p class="text-muted">Silakan bergabung dengan mengisi form berikut.</p>
        </div>

        <form action="registrasi_add.php" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="200">
          <div class="row gy-4">

            <!-- NAMA (required) -->
            <div class="col-md-6">
              <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
            </div>

            <!-- NIM (opsional) -->
            <div class="col-md-6">
              <input type="text" name="nim" class="form-control" placeholder="NIM">
            </div>

            <!-- EMAIL (required) -->
            <div class="col-md-6">
              <input type="email" name="email" class="form-control" placeholder="Email Aktif" required>
            </div>

            <!-- PRODI (opsional) -->
            <div class="col-md-6">
              <input type="text" name="prodi" class="form-control" placeholder="Program Studi">
            </div>

            <!-- ANGKATAN (opsional) -->
            <div class="col-12">
              <input type="number" name="angkatan" class="form-control"
                    placeholder="Angkatan (contoh: 2022)"
                    min="2020" max="2050">
            </div>
            <!-- ALASAN (opsional, sesuai tabel bisa null) -->
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
      </div><!-- End Right Column -->
        </div>
      </div>
    </section><!-- /Contact Section -->
<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>