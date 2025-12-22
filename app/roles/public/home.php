<?php
/* =====================================================
   HOME PAGE – MERGED FINAL STABLE VERSION
   ===================================================== */

$title  = "Beranda | IVSS";
$active = "home";

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

// DEBUG (matikan kalau sudah stabil)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ===================== HELPERS ===================== */
function safe($v){
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function excerpt_home($text, $limit = 60) {
    $text = trim(strip_tags((string)$text));
    if (mb_strlen($text) <= $limit) return $text;
    return mb_substr($text, 0, $limit) . '...';
}

function pick_best_role_from_agg(?string $rolesAgg, string $default): string {
    if ($rolesAgg !== null && trim($rolesAgg) !== '') {
        $priority = [
            'ketua lab' => 1,
            'admin'     => 2,
            'operator'  => 3,
            'dosen'     => 4,
            'mahasiswa' => 5
        ];

        $roles = array_map('trim', explode(',', strtolower($rolesAgg)));
        foreach ($priority as $role => $rank) {
            if (in_array($role, $roles)) return $role;
        }
        return $roles[0] ?? $default;
    }
    return strtolower($default);
}

function role_priority_for_sort(string $role): int {
    return match (strtolower($role)) {
        'ketua lab' => 1,
        'admin'     => 2,
        'operator'  => 3,
        'dosen'     => 4,
        'mahasiswa' => 5,
        default     => 99
    };
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

/* DOSEN */
$dosen = $conn->query("
    SELECT d.nip AS id_anggota, d.nama, d.jabatan, d.nidn,
           u.user_id, d.foto AS foto_field,
           {$rolesSubquery}, 'dosen' AS tipe
    FROM dosen d
    LEFT JOIN users u ON u.nip = d.nip
")->fetchAll(PDO::FETCH_ASSOC);

/* MAHASISWA */
$mahasiswa = $conn->query("
    SELECT 
        m.nim AS id_anggota,
        m.nama,
        'Mahasiswa' AS jabatan,
        NULL AS nidn,
        u.user_id,
        m.foto AS foto_field,
        {$rolesSubquery},
        'mahasiswa' AS tipe
    FROM mahasiswa m
    LEFT JOIN users u ON u.nim = m.nim
    WHERE LOWER(m.kategori) = 'riset'
")->fetchAll(PDO::FETCH_ASSOC);

/* STAFF */
$staff = $conn->query("
    SELECT NULL::varchar AS id_anggota, u.username AS nama,
           'Staff' AS jabatan, NULL AS nidn,
           u.user_id, NULL AS foto_field,
           STRING_AGG(r.role_name, ',') AS roles_agg,
           'staff' AS tipe
    FROM users u
    JOIN user_roles ur ON ur.user_id = u.user_id
    JOIN roles r ON r.role_id = ur.role_id
    WHERE LOWER(r.role_name) IN ('admin','operator')
      AND u.nip IS NULL
      AND u.nim IS NULL
    GROUP BY u.user_id, u.username
")->fetchAll(PDO::FETCH_ASSOC);

$anggotaLab = array_merge($staff, $dosen, $mahasiswa);

/* ===================== NORMALISASI ===================== */
foreach ($anggotaLab as $i => $a) {

    $tipe      = $a['tipe'] ?? 'mahasiswa';
    $idAnggota = $a['id_anggota'] ?? null;
    $userId    = $a['user_id'] ?? null;
    $fotoField = $a['foto_field'] ?? null;

    $anggotaLab[$i]['role_name'] = pick_best_role_from_agg(
        $a['roles_agg'] ?? null,
        $tipe
    );

    // EMAIL
    $email = '-';
    if ($tipe === 'dosen' && $idAnggota) {
        $q = $conn->prepare("SELECT email FROM dosen WHERE nip = :id LIMIT 1");
        $q->execute(['id' => $idAnggota]);
        $email = $q->fetchColumn() ?: '-';
    } elseif ($tipe === 'mahasiswa' && $idAnggota) {
        $q = $conn->prepare("SELECT email FROM mahasiswa WHERE nim = :id LIMIT 1");
        $q->execute(['id' => $idAnggota]);
        $email = $q->fetchColumn() ?: '-';
    }

    // FOTO (PRIORITAS: user_id → nim → foto_field → default)
$foto = "../../../public/assets/img/default-user.png";

// 1️⃣ user_id.jpg
if (!empty($userId) && file_exists(__DIR__."/../../../public/uploads/profiles/$userId.jpg")) {
    $foto = "../../../public/uploads/profiles/$userId.jpg";
}

// 2️⃣ nim.jpg (khusus mahasiswa)
elseif (
    ($tipe === 'mahasiswa') &&
    !empty($idAnggota) &&
    file_exists(__DIR__."/../../../public/uploads/profiles/$idAnggota.jpg")
) {
    $foto = "../../../public/uploads/profiles/$idAnggota.jpg";
}

// 3️⃣ foto_field lama (uploads/anggota)
elseif (!empty($fotoField) && file_exists(__DIR__."/../../../public/uploads/anggota/$fotoField")) {
    $foto = "../../../public/uploads/anggota/$fotoField";
}

    $anggotaLab[$i]['nama_normal']   = $a['nama'] ?? 'Tidak diketahui';
    $anggotaLab[$i]['email']         = $email;
    $anggotaLab[$i]['foto_resolved'] = $foto;
}

if (!function_exists('limit_words')) {
    function limit_words(string $text, int $limit = 10): string {
        $words = preg_split('/\s+/', trim(strip_tags($text)));
        if (!$words) return '';
        if (count($words) <= $limit) return implode(' ', $words);
        return implode(' ', array_slice($words, 0, $limit)) . '...';
    }
}

/* ===================== SORT ===================== */
usort($anggotaLab, function ($a, $b) {
    $pa = role_priority_for_sort($a['role_name'] ?? '');
    $pb = role_priority_for_sort($b['role_name'] ?? '');

    return $pa === $pb
        ? strcasecmp($a['nama_normal'], $b['nama_normal'])
        : ($pa <=> $pb);
});

/* ===================== RISET (HOME) ===================== */

// PROYEK (limit 4)
$stmt = $conn->prepare("
    SELECT 
        p.proyek_id,
        p.judul,
        p.status,
        p.tanggal_mulai,
        COALESCE(d.nama, 'Tidak diketahui') AS dosen_pj
    FROM proyek p
    LEFT JOIN anggota_proyek ap 
        ON ap.proyek_id = p.proyek_id AND ap.role = 'ketua'
    LEFT JOIN users u ON u.user_id = ap.user_id
    LEFT JOIN dosen d ON d.user_id = u.user_id
    ORDER BY p.tanggal_mulai DESC
    LIMIT 4
");
$stmt->execute();
$proyekHome = $stmt->fetchAll(PDO::FETCH_ASSOC);

// PUBLIKASI (limit 4)
$stmt = $conn->prepare("
    SELECT 
        p.publikasi_id,
        p.judul,
        p.link,
        p.tahun,
        COALESCE(d.nama, 'Tidak diketahui') AS penulis
    FROM publikasi p
    LEFT JOIN dosen d ON d.user_id = p.user_id
    ORDER BY p.tahun DESC
    LIMIT 4
");
$stmt->execute();
$publikasiHome = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===================== RENDER ===================== */
ob_start();
?>

<style>

/* TEAM GRID CARD */
.team-card {
  background: #fff;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 6px 18px rgba(0,0,0,.08);
  transition: transform .25s ease, box-shadow .25s ease;
}

.team-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 26px rgba(0,0,0,.12);
}

/* IMAGE FIX */
.team-img {
  width: 100%;
  height: 260px;
  overflow: hidden;
  background: #f4f6f9;
}

.team-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* INFO FIX */
.team-info {
  padding: 18px 16px 22px;
}

/* NAMA TIDAK MERUBAH CARD */
.team-name {
  font-size: 1.05rem;
  font-weight: 600;
  line-height: 1.3;
  margin-bottom: 6px;

  display: -webkit-box;
  -webkit-line-clamp: 2;      /* maksimal 2 baris */
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 2.6em;
}

.team-name a {
  color: #222;
  text-decoration: none;
}

.team-role {
  display: block;
  font-size: .9rem;
  font-weight: 500;
  color: #0d6efd;
  margin-bottom: 6px;
}

.team-meta {
  font-size: .85rem;
  color: #6c757d;
  line-height: 1.4;
}
/* ===================== RISET CARD FIX ===================== */
#riset-home .portfolio-content {
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 6px 18px rgba(0,0,0,.08);
  transition: transform .25s ease, box-shadow .25s ease;
  display: flex;
  flex-direction: column;
  height: 100%;
}

#riset-home .portfolio-content:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 26px rgba(0,0,0,.12);
}

/* ICON */
#riset-home .portfolio-content .icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  background: rgba(13,110,253,.1);
  display: flex;
  align-items: center;
  justify-content: center;
}

/* JUDUL FIX (2 BARIS) */
#riset-home .portfolio-content h4 {
  font-size: 1.05rem;
  font-weight: 600;
  line-height: 1.35;
  margin-bottom: 6px;

  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 2.8em;
}

/* META */
#riset-home .portfolio-content p {
  font-size: .9rem;
  line-height: 1.4;
  margin-bottom: auto; /* PUSH BADGE KE BAWAH */
}

/* BADGE FIX */
#riset-home .portfolio-content .badge {
  align-self: flex-start;
  font-weight: 500;
  margin-top: 12px;
}
/* ===================== VISI & MISI CARD FIX ===================== */

.vm-card {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 6px 18px rgba(0,0,0,.08);
  height: 100%;
  display: flex;
  flex-direction: column;
}

/* Bagian atas (icon) */
.vm-top {
  padding: 24px 24px 0;
}

.vm-icon {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: rgba(13,110,253,.1);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  color: #0d6efd;
}

/* Body disamakan */
.vm-body {
  padding: 20px 24px 28px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

/* Judul */
.vm-title {
  font-weight: 700;
  letter-spacing: 1px;
  margin-bottom: 12px;
}

/* TEXT VISI */
.vm-text {
  font-size: .95rem;
  line-height: 1.6;
  color: #555;
  margin-bottom: auto; /* ⬅️ PENTING: push konten agar tinggi sejajar */
}

/* LIST MISI */
.vm-misi-list {
  padding-left: 18px;
  margin: 0;
  font-size: .95rem;
  line-height: 1.6;
  color: #555;
}

.vm-misi-list li {
  margin-bottom: 8px;
}


</style>

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
          <img src="../../../public/assets/img/about.jpg" class="img-fluid" alt="">
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

<!-- Values Section -->
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
          <p class="vm-misi-list">
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

<!-- ===================== FOKUS RISET ===================== -->
<section id="riset-home" class="portfolio section portfolio-home">

  <div class="container section-title" data-aos="fade-up">
    <h2>Fokus Riset</h2>
    <p>Proyek &amp; Publikasi</p>
  </div>

  <div class="container">

    <!-- ISOTOPE LAYOUT (SAMA PERSIS DENGAN FASILITAS) -->
    <div class="isotope-layout"
        data-default-filter=".filter-proyek"
        data-layout="fitRows"
        data-sort="original-order">

      <!-- FILTER (SAMA PERSIS POLANYA) -->
      <ul class="portfolio-filters isotope-filters"
          data-aos="fade-up"
          data-aos-delay="100">
        <li data-filter=".filter-proyek" class="filter-active">Proyek</li>
        <li data-filter=".filter-publikasi">Publikasi</li>
      </ul>

      <!-- ITEMS -->
      <div class="row gy-4 isotope-container"
           data-aos="fade-up"
           data-aos-delay="200">

        <!-- ================= PROYEK ================= -->
        <?php if (!empty($proyekHome)): ?>
          <?php foreach ($proyekHome as $p): ?>
            <div class="col-xl-6 col-lg-6 col-md-6 portfolio-item isotope-item filter-proyek">
            <div class="portfolio-content p-4">

              <div class="icon mb-3">
                <i class="bi bi-diagram-3 fs-4 text-primary"></i>
              </div>

              <h4>
                <?= htmlspecialchars(limit_words($p['judul'], 10)) ?>
              </h4>

              <p class="text-muted mb-0">
                <i class="bi bi-person"></i>
                <?= htmlspecialchars($p['dosen_pj']) ?>
              </p>

              <span class="badge bg-light text-dark">
                <?= htmlspecialchars($p['status'] ?: 'Tidak diketahui') ?>
              </span>

            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <!-- ================= PUBLIKASI ================= -->
        <?php if (!empty($publikasiHome)): ?>
          <?php foreach ($publikasiHome as $p): ?>
            <div class="col-xl-6 col-lg-6 col-md-6 portfolio-item isotope-item filter-publikasi">
              <div class="portfolio-content p-4">

                <div class="icon mb-3">
                  <i class="bi bi-journal-text fs-4 text-primary"></i>
                </div>

                <h4>
                  <?= htmlspecialchars(limit_words($p['judul'], 10)) ?>
                </h4>

                <p class="text-muted mb-0">
                  <i class="bi bi-person"></i>
                  <?= htmlspecialchars($p['penulis']) ?>
                </p>

                <span class="badge bg-light text-dark">
                  <?= htmlspecialchars($p['tahun']) ?>
                </span>

              </div>
            </div>

          <?php endforeach; ?>
        <?php endif; ?>

        <?php if (empty($proyekHome) && empty($publikasiHome)): ?>
          <div class="col-12">
            <p class="text-center">Belum ada data riset.</p>
          </div>
        <?php endif; ?>

      </div>
    </div>

    <!-- BUTTON (POSISI SAMA SEPERTI FASILITAS) -->
    <div class="text-center mt-3">
      <a href="riset.php" class="btn btn-outline-primary btn-sm">
        Lihat Semua Riset
      </a>
    </div>

  </div>
</section>
<!-- ===================== /FOKUS RISET ===================== -->



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
                <a href="detail_berita.php?id=<?= (int)$b['berita_id']; ?>" class="readmore stretched-link">
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
    <p>Anggota laboratorium</p>
  </div>

  <div class="container">

    <div class="row g-4">

      <?php if (!empty($anggotaLab)): ?>
        <?php foreach ($anggotaLab as $a): ?>

          <?php
            /* =========================
               PROFIL LINK RESOLUTION
               ========================= */
            $profilId = null;
            if (!empty($a['nip'])) {
                $profilId = $a['nip'];
            } elseif (!empty($a['id_anggota'])) {
                $profilId = $a['id_anggota'];
            } elseif (!empty($a['user_id'])) {
                $profilId = $a['user_id'];
            }

            $profilTipe = $a['tipe'] ?? 'anggota';

            $profilUrl = $profilId
              ? "anggota_detail.php?tipe=" . urlencode($profilTipe) . "&id=" . urlencode($profilId)
              : "#";

            /* =========================
               DISPLAY DATA
               ========================= */
            $foto = !empty($a['foto_resolved'])
              ? $a['foto_resolved']
              : "../../../public/assets/img/default-user.png";

            $nama = $a['nama_normal'] ?? 'Tidak diketahui';

            $displayRole = ucfirst(
              $a['role_name']
              ?? ($a['tipe'] ?? 'anggota')
            );
          ?>

          <!-- CARD -->
          <div class="col-sm-6 col-lg-4">
            <div class="team-card h-100">

              <div class="ratio ratio-1x1 rounded-top overflow-hidden bg-light">
              <img
                src="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>"
                class="img-fluid w-100 h-100 team-img"
              >
              </div>

              <div class="team-info text-center">
                <h4 class="team-name">
                  <a href="<?= htmlspecialchars($profilUrl, ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>
                  </a>
                </h4>

                <span class="team-role">
                  <?= htmlspecialchars($displayRole, ENT_QUOTES, 'UTF-8') ?>
                </span>

                <?php if (!empty($a['nidn'])): ?>
                  <div class="team-meta"><?= htmlspecialchars($a['nidn'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <div class="team-meta">
                  <?= htmlspecialchars($a['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                </div>
              </div>

            </div>
          </div>

        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <p class="text-center">Belum ada anggota.</p>
        </div>
      <?php endif; ?>

    </div>

    <div class="text-center team-btn mt-4">
      <a href="anggota.php" class="btn btn-outline-primary btn-sm">
        Lihat Semua Anggota
      </a>
    </div>

  </div>
</section>
<!-- /TEAM SECTION -->


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




<!-- Contact / Pendaftaran -->
<section id="contact" class="contact section">
  <div class="container section-title" data-aos="fade-up">
    <p>Hubungi Kami</p>
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
              <h3>Alamat</h3>
              <p><?= !empty($lab['alamat']) ? safe($lab['alamat']) : 'Belum ada alamat'; ?></p>
            </div>
          </div>

          <div class="col-md-6">
            <div class="info-item" data-aos="fade" data-aos-delay="300">
              <i class="bi bi-telephone"></i>
              <h3>Telepon Kami</h3>
              <p><?= !empty($lab['no_telp']) ? safe($lab['no_telp']) : 'Belum ada nomor telepon'; ?></p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="info-item" data-aos="fade" data-aos-delay="400">
              <i class="bi bi-envelope"></i>
              <h3>Email Kami</h3>
              <p><?= !empty($lab['email']) ? safe($lab['email']) : 'Belum ada email'; ?></p>
            </div>
          </div>

          <div class="col-md-6">
            <div class="info-item" data-aos="fade" data-aos-delay="500">
              <i class="bi bi-clock"></i>
              <h3>Jam Layanan</h3>
              <p>Senin - Jum'at</p>
              <p>8:00 - 16:00 WIB</p>
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