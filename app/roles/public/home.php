<?php
/* =====================================================
   HOME PAGE – FINAL STABLE VERSION
   CSS & LAYOUT GUARANTEED TO LOAD
   ===================================================== */

$title  = "Beranda | IVSS";
$active = "home";

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

// DEBUG (boleh dimatikan setelah fix)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ===================== HELPERS ===================== */
function safe($v){
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

if (!function_exists('excerpt_home')) {
    function excerpt_home($text, $limit = 60) {
        $text = trim(strip_tags((string)$text));
        if (mb_strlen($text) <= $limit) return $text;
        return mb_substr($text, 0, $limit) . '...';
    }
}

if (!function_exists('pick_best_role_from_agg')) {
    function pick_best_role_from_agg($rolesAgg, $default) {
        if ($rolesAgg !== null && trim($rolesAgg) !== '') {
            $priority = [
                'ketua lab' => 1,
                'admin' => 2,
                'operator' => 3,
                'dosen' => 4,
                'mahasiswa' => 5
            ];
            $roles = array_map('trim', explode(',', strtolower($rolesAgg)));
            foreach ($priority as $role => $rank) {
                if (in_array($role, $roles)) return $role;
            }
            return isset($roles[0]) ? $roles[0] : $default;
        }
        return $default;
    }
}

if (!function_exists('role_priority_for_sort')) {
    function role_priority_for_sort($role) {
      $roleLower = strtolower((string) $role);
      switch ($roleLower) {
          case 'ketua lab':
              return 1;
          case 'admin':
              return 2;
          case 'operator':
              return 3;
          case 'dosen':
              return 4;
          case 'mahasiswa':
              return 5;
          default:
              return 99;
      }      
    }
}

/* ===================== DATA ===================== */

// LAB
$stmt = $conn->prepare("SELECT * FROM lab_info LIMIT 1");
$stmt->execute();
$lab = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// FASILITAS
$stmt = $conn->prepare("SELECT * FROM fasilitas ORDER BY fasilitas_id DESC LIMIT 9");
$stmt->execute();
$fasilitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// MATA KULIAH
$stmt = $conn->prepare("SELECT * FROM mata_kuliah ORDER BY semester ASC LIMIT 5");
$stmt->execute();
$matkul = $stmt->fetchAll(PDO::FETCH_ASSOC);

// DOKUMENTASI
$stmt = $conn->prepare("
    SELECT * FROM act_documentation
    WHERE type_file ILIKE '%.jpg'
       OR type_file ILIKE '%.jpeg'
       OR type_file ILIKE '%.png'
       OR type_file ILIKE '%.webp'
    ORDER BY documentation_id DESC
    LIMIT 8
");
$stmt->execute();
$dokumentasi = $stmt->fetchAll(PDO::FETCH_ASSOC);

// BERITA
$stmt = $conn->prepare("
    SELECT b.*, u.username
    FROM berita b
    LEFT JOIN users u ON b.user_id = u.user_id
    ORDER BY b.tgl_post DESC
    LIMIT 3
");
$stmt->execute();
$berita = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===================== TEAM ===================== */

$rolesSubquery = "(
  SELECT STRING_AGG(r.role_name, ',') 
  FROM user_roles ur
  JOIN roles r ON r.role_id = ur.role_id
  WHERE ur.user_id = u.user_id
) AS roles_agg";

$dosen = $conn->query("
  SELECT d.nip AS id_anggota, d.nama, d.jabatan, d.nidn,
         u.user_id, d.foto AS foto_field, {$rolesSubquery}, 'dosen' AS tipe
  FROM dosen d
  LEFT JOIN users u ON u.nip = d.nip
")->fetchAll(PDO::FETCH_ASSOC);

$mahasiswa = $conn->query("
  SELECT m.nim AS id_anggota, m.nama, 'Mahasiswa' AS jabatan,
         NULL AS nidn, u.user_id, m.foto AS foto_field,
         {$rolesSubquery}, 'mahasiswa' AS tipe
  FROM mahasiswa m
  LEFT JOIN users u ON u.nim = m.nim
")->fetchAll(PDO::FETCH_ASSOC);

$staff = $conn->query("
  SELECT NULL::varchar AS id_anggota, u.username AS nama,
         'Staff' AS jabatan, NULL AS nidn, u.user_id,
         NULL AS foto_field, STRING_AGG(r.role_name, ',') AS roles_agg,
         'staff' AS tipe
  FROM users u
  JOIN user_roles ur ON ur.user_id = u.user_id
  JOIN roles r ON r.role_id = ur.role_id
  WHERE LOWER(r.role_name) IN ('admin','operator')
    AND u.nip IS NULL AND u.nim IS NULL
  GROUP BY u.user_id, u.username
")->fetchAll(PDO::FETCH_ASSOC);

$anggotaLab = array_merge($staff, $dosen, $mahasiswa);

/* NORMALISASI */
foreach ($anggotaLab as $i => $a) {

  $defaultRole = isset($a['tipe']) ? $a['tipe'] : 'mahasiswa';

  $anggotaLab[$i]['role_name'] = pick_best_role_from_agg(
      isset($a['roles_agg']) ? $a['roles_agg'] : null,
      $defaultRole
  );

  $anggotaLab[$i]['nama_normal'] = isset($a['nama']) ? $a['nama'] : 'Tidak diketahui';
  $anggotaLab[$i]['email'] = '-';
  $anggotaLab[$i]['foto_resolved'] = "../../../public/assets/img/default-user.png";
}


/* SORT */
usort($anggotaLab, function ($a, $b) {

  $pa = role_priority_for_sort(isset($a['role_name']) ? $a['role_name'] : '');
  $pb = role_priority_for_sort(isset($b['role_name']) ? $b['role_name'] : '');

  if ($pa === $pb) {
      return strcasecmp(
          isset($a['nama_normal']) ? $a['nama_normal'] : '',
          isset($b['nama_normal']) ? $b['nama_normal'] : ''
      );
  }

  return ($pa < $pb) ? -1 : 1;
});


/* ===================== RENDER ===================== */

ob_start();
?>

<!-- Hero Section -->
<section id="hero" class="hero section">
  <div class="container">
    <div class="row justify-content-center text-center">
      <div class="col-lg-10">
        <div class="hero-content" data-aos="fade-up">
          <h1 style="color:#fff;">
          <?= safe(isset($lab['nama']) ? $lab['nama'] : 'Nama Lab Belum Diisi'); ?>
          </h1>

          <p style="color:#fff;">
          <?= safe(isset($lab['motto']) ? $lab['motto'] : 'Motto Lab Belum Diisi'); ?>
          </p>

          <div class="hero-actions" data-aos="fade-up" data-aos-delay="150">
            <a href="#form-pendaftaran" class="btn-get-started">
              Bergabung <i class="bi bi-arrow-right"></i>
            </a>
          </div>
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
          <p><?= !empty($lab['deskripsi']) ? safe($lab['deskripsi']) : 'Deskripsi lab belum diisi.'; ?></p>
          <div class="text-center text-lg-start">
            <a href="../public/about.php" class="btn-read-more d-inline-flex align-items-center justify-content-center align-self-center">
              <span>Baca Selengkapnya</span>
              <i class="bi bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
      <div class="col-lg-6 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="200">
        <div class="about-img">
          <img src="../../../public/assets/img/dokum10.jpg" class="img-fluid" alt="">
        </div>  
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
    <div class="row gy-4 justify-content-center">

      <!-- VISI -->
      <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
        <div class="values-card values-vision">
          <h3>Visi</h3>
          <p class="values-text">
            <?= !empty($lab['visi']) ? nl2br(safe($lab['visi'])) : 'Visi lab belum diisi.'; ?>
          </p>
        </div>
      </div>

      <!-- MISI -->
      <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
        <div class="values-card values-mission">
          <h3>Misi</h3>

          <?php if (!empty($lab['misi'])): ?>
            <?php
              // Pecah misi jadi item list:
              // - jika misi dipisah newline, bakal jadi list
              $lines = preg_split("/\r\n|\n|\r/", trim($lab['misi']));
              $items = [];
              foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;
                // buang prefix angka "1. " atau "1) "
                $line = preg_replace('/^\d+\s*[\.\)]\s*/', '', $line);
                $items[] = $line;
              }
            ?>
            <ol class="values-list">
              <?php foreach ($items as $it): ?>
                <li><?= safe($it) ?></li>
              <?php endforeach; ?>
            </ol>
          <?php else: ?>
            <p class="values-text">Misi lab belum diisi.</p>
          <?php endif; ?>

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

<!-- TEAM SECTION -->
<section id="team" class="team section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Team</h2>
    <p>Anggota Lab</p>
  </div>

  <div class="container">
    <div class="swiper init-swiper team-swiper">
      <!-- SWIPER CONFIG (FINAL: NO "auto") -->
      <script type="application/json" class="swiper-config">
      {
        "loop": true,
        "speed": 600,
        "autoplay": { "delay": 4000 },
        "slidesPerView": 1,
        "spaceBetween": 16,
        "centeredSlides": false,
        "pagination": {
          "el": ".swiper-pagination",
          "clickable": true,
          "dynamicBullets": true,
          "dynamicMainBullets": 5
        },
        "navigation": {
          "nextEl": ".swiper-button-next",
          "prevEl": ".swiper-button-prev"
        },
        "breakpoints": {
          "480":  { "slidesPerView": 1, "spaceBetween": 16 },
          "768":  { "slidesPerView": 2, "spaceBetween": 18 },
          "1200": { "slidesPerView": 3, "spaceBetween": 20 }
        }
      }
      </script>

      <div class="swiper-wrapper">
        <?php if (!empty($anggotaLab)): ?>
          <?php foreach ($anggotaLab as $a): ?>
            <?php
              $foto = !empty($a['foto_resolved'])
                ? $a['foto_resolved']
                : "../../../public/assets/img/default-user.png";

              $displayRole = ucfirst(
                !empty($a['role_name'])
                  ? $a['role_name']
                  : (!empty($a['tipe']) ? $a['tipe'] : 'anggota')
              );
            ?>
            <div class="swiper-slide">
              <div class="team-member">
                <div class="member-img">
                  <img
                  src="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>"
                  alt="<?= htmlspecialchars(isset($a['nama_normal']) ? $a['nama_normal'] : 'Anggota', ENT_QUOTES, 'UTF-8') ?>"
                  >
                </div>

                <div class="member-info">
                  <h4><?= htmlspecialchars(isset($a['nama_normal']) ? $a['nama_normal'] : 'Tidak diketahui', ENT_QUOTES, 'UTF-8') ?></h4>

                  <span><?= htmlspecialchars($displayRole, ENT_QUOTES, 'UTF-8') ?></span>

                  <?php if (!empty($a['nidn'])): ?>
                    <p><?= htmlspecialchars($a['nidn'], ENT_QUOTES, 'UTF-8') ?></p>
                  <?php endif; ?>

                  <p><?= htmlspecialchars(isset($a['email']) ? $a['email'] : '-', ENT_QUOTES, 'UTF-8') ?></p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="swiper-slide">
            <p class="text-center">Belum ada anggota.</p>
          </div>
        <?php endif; ?>
      </div>

      <div class="swiper-pagination"></div>
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>

    <div class="text-center team-btn">
      <a href="anggota.php" class="btn btn-outline-primary btn-sm">
        Lihat Semua Anggota
      </a>
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
                    data-description="<?= safe($f['deskripsi']); ?>"
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
<section id="courses" class="courses section">
<div class="container section-title" data-aos="fade-up"> 
<h2>Perkuliahan</h2> 
<p>Mata Kuliah terkait</p> 
</div>

  <?php
  // STATIC DATA (tidak pakai database, tapi tetap jalan)
  $courses = [
    [
      "title" => "Kecerdasan Artifisial (AI)",
      "desc"  => "Teknologi yang fokus pada pengembangan sistem atau mesin yang dapat melakukan tugas-tugas yang biasanya memerlukan kecerdasan manusia, seperti pengenalan pola, pembelajaran, pemecahan masalah, dan pengambilan keputusan.",
      "icon"  => "bi-cpu-fill"
    ],
    [
      "title" => "Machine Learning",
      "desc"  => "Cabang dari kecerdasan artifisial yang fokus pada pengembangan algoritma yang memungkinkan mesin belajar dari data untuk membuat prediksi atau keputusan tanpa diprogram secara eksplisit.",
      "icon"  => "bi-journal-bookmark-fill"
    ],
    [
      "title" => "Pengolahan Citra & Visi Komputer",
      "desc"  => "Teknik untuk mengolah dan menganalisis gambar atau video menggunakan komputer, termasuk deteksi objek, segmentasi, pengenalan pola, dan interpretasi citra untuk aplikasi seperti pengenalan wajah dan kendaraan otomatis.",
      "icon"  => "bi-box-fill"
    ],
    [
      "title" => "Sistem Cerdas (Intelligent System)",
      "desc"  => "Pengembangan sistem yang dapat meniru atau melampaui kemampuan kognitif manusia, seperti pengambilan keputusan otomatis, perencanaan, dan pemrosesan informasi dalam konteks aplikasi nyata, seperti robotika dan sistem pakar.",
      "icon"  => "bi-diagram-3-fill"
    ],
  ];
  ?>

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="swiper init-swiper courses-swiper">

      <script type="application/json" class="swiper-config">
        {
          "loop": true,
          "speed": 600,
          "autoplay": { "delay": 4500 },
          "slidesPerView": 3,
          "spaceBetween": 24,
          "pagination": {
            "el": ".swiper-pagination",
            "clickable": true
          },
          "breakpoints": {
            "0":   { "slidesPerView": 1 },
            "768": { "slidesPerView": 2 },
            "1200":{ "slidesPerView": 3 }
          }
        }
      </script>

      <div class="swiper-wrapper">
        <?php foreach ($courses as $c): ?>
          <div class="swiper-slide">
            <div class="course-card">
              <div class="course-icon">
                <i class="bi <?= safe($c['icon']); ?>"></i>
              </div>
              <h3><?= safe($c['title']); ?></h3>
              <p><?= safe($c['desc']); ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="swiper-pagination"></div>
    </div>
  </div>
</section>
<!-- /Perkuliahan -->


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

<!-- SEMUA HTML KONTEN HOME DI SINI (hero, about, team, dst) -->
<?php
$content = ob_get_clean();
require __DIR__ . "/_layout.php";
exit;
?>
