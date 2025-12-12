<?php
$title = "Beranda | IVSS";
$active = "home";

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

/* =========================
   1. Ambil 1 data lab
   ========================= */
$sql = "SELECT * FROM lab_info LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$lab = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

/* =========================
   2. Ambil semua fasilitas
   ========================= */
$sql = "SELECT * FROM fasilitas ORDER BY fasilitas_id DESC LIMIT 9";
$stmt = $conn->prepare($sql);
$stmt->execute();
$fasilitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   3. Ambil semua mata kuliah
   ========================= */
$sql = "SELECT * FROM mata_kuliah ORDER BY semester ASC LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->execute();
$matkul = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   4. Ambil dokumentasi
   ========================= */
$sql = "SELECT * FROM act_documentation 
        WHERE type_file ILIKE '%.jpg' 
           OR type_file ILIKE '%.jpeg'
           OR type_file ILIKE '%.png'
           OR type_file ILIKE '%.webp'
        ORDER BY documentation_id DESC LIMIT 8";
$stmt = $conn->prepare($sql);
$stmt->execute();
$dokumentasi = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   5. Ambil 3 berita terbaru
   ========================= */
$sql = "SELECT * FROM berita ORDER BY tgl_post DESC LIMIT 3";
$stmt = $conn->prepare($sql);
$stmt->execute();
$berita = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===================================================
   6. FETCH ANGGOTA LAB (Dosen + Mahasiswa + Staff)
   - role information fetched from user_roles when user exists
   - do not assume users row exists for every dosen/mahasiswa
   =================================================== */

/* ---- helper subquery templates ----
   We'll use a correlated subquery to aggregate roles for a given user_id.
   If user_id is NULL (no linked user account), roles_agg will be NULL.
*/
$rolesSubquery = "(
    SELECT STRING_AGG(r.role_name, ',') 
    FROM user_roles ur
    JOIN roles r ON r.role_id = ur.role_id
    WHERE ur.user_id = u.user_id
) AS roles_agg";

/* ---- DOSEN ---- */
$sqlDosen = "
    SELECT 
        d.nip AS id_anggota,
        d.nama,
        d.jabatan,
        d.nidn,
        u.user_id,
        d.foto AS foto_field,
        {$rolesSubquery},
        'dosen' AS tipe
    FROM dosen d
    LEFT JOIN users u ON u.nip = d.nip
    -- roles aggregated via subquery above
";
$dosen = $conn->query($sqlDosen)->fetchAll(PDO::FETCH_ASSOC);

/* ---- MAHASISWA ---- */
$sqlMhs = "
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
";
$mahasiswa = $conn->query($sqlMhs)->fetchAll(PDO::FETCH_ASSOC);

/* ---- STAFF (users who have ketua lab/admin/operator role) ----
   We explicitly fetch users that have one of these roles.
   We aggregate roles here too (could be multiple), using STRING_AGG.
*/
$sqlStaff = "
    SELECT
        NULL::varchar AS id_anggota,
        u.username AS nama,
        'Staff' AS jabatan,
        NULL AS nidn,
        u.user_id,
        NULL AS foto_field,
        STRING_AGG(r.role_name, ',') AS roles_agg,
        'staff' AS tipe
    FROM users u
    JOIN user_roles ur ON ur.user_id = u.user_id
    JOIN roles r ON r.role_id = ur.role_id
    WHERE LOWER(r.role_name) IN ('admin','operator') 
      AND u.nip IS NULL   -- jangan ambil dosen
      AND u.nim IS NULL   -- jangan ambil mahasiswa
    GROUP BY u.user_id, u.username
";
$staff = $conn->query($sqlStaff)->fetchAll(PDO::FETCH_ASSOC);

/* ---- Merge arrays ---- */
$anggotaLab = array_merge($staff, $dosen, $mahasiswa);

/* ===================================================
   7. NORMALIZE ROLE & EMAIL & FOTO SAFE ACCESS
   - build 'role_name' (single best role) from roles_agg if present
   - if roles_agg is null use sensible default ("dosen"/"mahasiswa"/"staff")
   - email fetched from dosen/mahasiswa table when tipe available
   - foto priority:
       1) ../../../public/uploads/profiles/{user_id}.jpg  (if user_id present)
       2) ../../../public/uploads/anggota/{foto_field}    (if foto_field present)
       3) ../../../public/assets/img/default-user.png
   =================================================== */

function pick_best_role_from_agg(?string $rolesAgg, string $default) : string {
    // $rolesAgg like "admin,operator" or null
    if (!is_null($rolesAgg) && trim($rolesAgg) !== '') {
        // split and choose highest priority according to required order
        $parts = array_map('trim', explode(',', $rolesAgg));
        $partsLower = array_map('strtolower', $parts);

        // priority map
        $priority = [
            'ketua lab' => 1,
            'admin'     => 2,
            'operator'  => 3,
            'dosen'     => 4,
            'mahasiswa' => 5
        ];

        $best = null;
        $bestScore = 999;
        foreach ($partsLower as $p) {
            if (isset($priority[$p]) && $priority[$p] < $bestScore) {
                $best = $p;
                $bestScore = $priority[$p];
            }
        }
        if ($best !== null) {
            return $best;
        }
        // fallback to first part if unknown
        return strtolower($partsLower[0] ?? $default);
    }

    return strtolower($default);
}

foreach ($anggotaLab as $idx => $row) {
    // ensure indexes exist
    $tipe = $row['tipe'] ?? 'mahasiswa';
    $rolesAgg = $row['roles_agg'] ?? $row['roles_agg'] ?? null; // staff uses roles_agg; dosen/mahasiswa have roles_agg too
    $userId = $row['user_id'] ?? null;
    $fotoField = $row['foto_field'] ?? null;
    $idAnggota = $row['id_anggota'] ?? null;

    // role_name (single)
    $defaultRole = $tipe === 'dosen' ? 'dosen' : ($tipe === 'mahasiswa' ? 'mahasiswa' : 'staff');
    $roleName = pick_best_role_from_agg($rolesAgg, $defaultRole);

    // email
    $email = '-';
    if ($tipe === 'dosen' && !empty($idAnggota)) {
        $q = $conn->prepare("SELECT email FROM dosen WHERE nip = :nip LIMIT 1");
        $q->execute(['nip' => $idAnggota]);
        $email = $q->fetchColumn() ?: '-';
    } elseif ($tipe === 'mahasiswa' && !empty($idAnggota)) {
        $q = $conn->prepare("SELECT email FROM mahasiswa WHERE nim = :nim LIMIT 1");
        $q->execute(['nim' => $idAnggota]);
        $email = $q->fetchColumn() ?: '-';
    } else {
        // staff or unknown — no email in users table per DB structure
        $email = '-';
    }

    // foto resolution
    $defaultFoto = "../../../public/assets/img/default-user.png";
    $foto = $defaultFoto;

    // 1) profile by user_id
    if (!empty($userId)) {
        $p = __DIR__ . "/../../../public/uploads/profiles/" . $userId . ".jpg";
        $webPath = "../../../public/uploads/profiles/" . $userId . ".jpg";
        if (file_exists($p)) {
            $foto = $webPath;
        } elseif (!empty($fotoField)) {
            // fallback to foto field (relative to uploads/anggota)
            $p2 = __DIR__ . "/../../../public/uploads/anggota/" . $fotoField;
            $webPath2 = "../../../public/uploads/anggota/" . $fotoField;
            if (file_exists($p2)) {
                $foto = $webPath2;
            }
        }
    } else {
        // no user_id — try foto_field (dosen/mahasiswa may have foto field)
        if (!empty($fotoField)) {
            $p2 = __DIR__ . "/../../../public/uploads/anggota/" . $fotoField;
            $webPath2 = "../../../public/uploads/anggota/" . $fotoField;
            if (file_exists($p2)) {
                $foto = $webPath2;
            }
        }
    }

    // normalize name
    $nama = $row['nama'] ?? ($row['username'] ?? 'Tidak diketahui');

    // nidn if exists
    $nidn = $row['nidn'] ?? null;

    // write back normalized fields
    $anggotaLab[$idx]['role_name'] = $roleName;
    $anggotaLab[$idx]['email'] = $email;
    $anggotaLab[$idx]['foto_resolved'] = $foto;
    $anggotaLab[$idx]['nama_normal'] = $nama;
    $anggotaLab[$idx]['nidn'] = $nidn;
}

/* ===================================================
   8. SORT ANGGOTA BERDASARKAN PRIORITAS ROLE, lalu name
   Order: ketua lab (1) -> admin (2) -> operator (3) -> dosen (4) -> mahasiswa (5)
   =================================================== */
function role_priority_for_sort(string $role): int {
    $r = strtolower($role);
    return match($r) {
        'ketua lab' => 1,
        'admin'     => 2,
        'operator'  => 3,
        'dosen'     => 4,
        'mahasiswa' => 5,
        default     => 99,
    };
}

usort($anggotaLab, function($a, $b) {
    $pa = role_priority_for_sort($a['role_name'] ?? '');
    $pb = role_priority_for_sort($b['role_name'] ?? '');
    if ($pa === $pb) {
        return strcasecmp($a['nama_normal'] ?? '', $b['nama_normal'] ?? '');
    }
    return $pa <=> $pb;
});

/* ===================================================
   9. Render view (rest of page unchanged except using normalized fields)
   =================================================== */

ob_start();
?>

    <!-- Hero Section -->
    <section id="hero" class="hero section">
      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
            <h1 data-aos="fade-up" style="color:#FFFFFF;">
              <?= htmlspecialchars($lab['nama'] ?? 'Nama Lab Belum Diisi'); ?>
            </h1>
            <p data-aos="fade-up" data-aos-delay="100" style="color:#FFFFFF;">
              <?= htmlspecialchars($lab['motto'] ?? $lab['deskripsi'] ?? 'Deskripsi belum diisi.'); ?>
            </p>
            <div class="d-flex flex-column flex-md-row" data-aos="fade-up" data-aos-delay="200">
              <a href="#form-pendaftaran" class="btn-get-started">Bergabung <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </div>
    </section><!-- /Hero Section -->

    <!-- About, Clients, Values, Services, Recent Posts sections unchanged... -->
    <!-- (I'll keep them identical to your original file) -->

    <!-- Clients / Dokumentasi -->
    <section id="clients" class="clients section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Aktivitas Kami</h2>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": { "delay": 5000 },
              "slidesPerView": "auto",
              "pagination": { "el": ".swiper-pagination", "type": "bullets", "clickable": true },
              "breakpoints": {
                "320": { "slidesPerView": 2, "spaceBetween": 40 },
                "480": { "slidesPerView": 3, "spaceBetween": 60 },
                "640": { "slidesPerView": 4, "spaceBetween": 80 },
                "992": { "slidesPerView": 6, "spaceBetween": 120 }
              }
            }
          </script>

          <div class="swiper-wrapper align-items-center">
            <?php if (!empty($dokumentasi)): ?>
              <?php foreach ($dokumentasi as $dok): ?>
                <div class="swiper-slide">
                  <img src="../../../public/uploads/dokumentasi/<?= htmlspecialchars($dok['type_file']) ?>" class="img-fluid" alt="">
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="swiper-slide text-center">Belum ada dokumentasi.</div>
            <?php endif; ?>
          </div>

          <div class="swiper-pagination"></div>
        </div>
      </div>
    </section>

    <!-- VALUES SECTION -->
    <section id="values" class="values section">
      <div class="container section-title" data-aos="fade-up">
        <p>Visi & Misi</p>
      </div>
      <div class="container">
        <div class="row gy-4 justify-content-center">
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card">
              <h3>VISI</h3>
              <p style="text-align: justify;"><?= nl2br(htmlspecialchars($lab['visi'] ?? '')) ?></p>
            </div>
          </div>
          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card">
              <h3>MISI</h3>
              <div style="text-align: justify;">
                <?php
                  $misiText = $lab['misi'] ?? '';
                  $misiList = $misiText === '' ? [] : preg_split('/\r\n|\r|\n/', $misiText);
                  echo "<ol>";
                  foreach ($misiList as $m) {
                      if (trim($m) !== "") echo "<li>" . htmlspecialchars($m) . "</li>";
                  }
                  echo "</ol>";
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Services -->
    <section id="services" class="services section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Riset</h2>
        <p>Fokus riset</p>
      </div>
      <div class="container">
        <div class="row gy-4 justify-content-center">
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="service-item item-cyan position-relative">
              <div class="icon"><i class="bi bi-eye"></i></div>
              <h3>Intelligence Vision</h3>
              <p>Teknologi yang memungkinkan mesin “melihat”.</p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item item-orange position-relative">
              <div class="icon"><i class="bi bi-cpu"></i></div>
              <h3>Smart System</h3>
              <p>Sistem adaptif dan cerdas untuk otomasi.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Recent Posts -->
    <section id="recent-posts" class="recent-posts section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Recent Posts</h2>
        <p>Berita</p>
      </div>
      <div class="container">
        <div class="row gy-5">
          <?php if (!empty($berita)): ?>
            <?php foreach ($berita as $b): ?>
              <?php
                $fotoBerita = !empty($b['foto']) ? $b['foto'] : "default.jpg";
                $judul = htmlspecialchars($b['judul'] ?? '');
                $kategori = htmlspecialchars($b['kategori'] ?? '-');
                $tanggal = !empty($b['tgl_post']) ? date('F d', strtotime($b['tgl_post'])) : "Unknown";
              ?>
              <div class="col-xl-4 col-md-6">
                <div class="post-item position-relative h-100">
                  <div class="post-img position-relative overflow-hidden">
                    <img src="../../../public/uploads/berita/<?= $fotoBerita ?>" class="img-fluid" alt="<?= $judul ?>">
                    <span class="post-date"><?= $tanggal ?></span>
                  </div>
                  <div class="post-content d-flex flex-column">
                    <h3 class="post-title"><?= $judul ?></h3>
                    <div class="meta d-flex align-items-center">
                      <div class="d-flex align-items-center">
                        <i class="bi bi-folder2"></i>
                        <span class="ps-2"><?= $kategori ?></span>
                      </div>
                    </div>
                    <hr>
                    <a href="detail_berita.php?id=<?= $b['berita_id'] ?>" class="readmore stretched-link">
                      <span>Read More</span><i class="bi bi-arrow-right"></i>
                    </a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-center">Belum ada berita.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- TEAM SECTION (SCROLLABLE & FIXED) -->
    <section id="team" class="team section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Team</h2>
        <p>Anggota Lab</p>
      </div>

      <div class="container">
        <div class="swiper init-swiper">
          <!-- SWIPER CONFIG -->
          <script type="application/json" class="swiper-config">
          {
            "loop": true,
            "speed": 600,
            "autoplay": { "delay": 4000 },
            "slidesPerView": "auto",
            "centeredSlides": false,
            "spaceBetween": 30,
            "pagination": {
              "el": ".swiper-pagination",
              "type": "bullets",
              "clickable": true
            },
            "navigation": {
              "nextEl": ".swiper-button-next",
              "prevEl": ".swiper-button-prev"
            },
            "breakpoints": {
              "320": { "slidesPerView": 1, "spaceBetween": 10 },
              "480": { "slidesPerView": 1.3, "spaceBetween": 20 },
              "768": { "slidesPerView": 2, "spaceBetween": 25 },
              "1200": { "slidesPerView": 3, "spaceBetween": 30 }
            }
          }
          </script>

          <div class="swiper-wrapper">
            <?php if (!empty($anggotaLab)): ?>
              <?php foreach ($anggotaLab as $a): ?>
                <?php
                  $foto = $a['foto_resolved'] ?? "../../../public/assets/img/default-user.png";
                  $displayRole = ucfirst($a['role_name'] ?? ($a['tipe'] ?? 'anggota'));
                ?>
                <div class="swiper-slide">
                  <div class="team-member text-center">
                    <div class="member-img mb-3">
                      <img src="<?= htmlspecialchars($foto) ?>" class="img-fluid" style="border-radius:10px;" alt="<?= htmlspecialchars($a['nama_normal'] ?? 'Anggota') ?>">
                    </div>
                    <div class="member-info">
                      <h4><?= htmlspecialchars($a['nama_normal'] ?? 'Tidak diketahui') ?></h4>
                      <span><?= htmlspecialchars($displayRole) ?></span>
                      <?php if (!empty($a['nidn'])): ?>
                        <p><?= htmlspecialchars($a['nidn']) ?></p>
                      <?php endif; ?>
                      <p><?= htmlspecialchars($a['email'] ?? '-') ?></p>
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

        <div class="text-center mt-3">
          <a href="anggota.php" class="btn btn-primary">Lihat Semua Anggota</a>
        </div>
      </div>
    </section>

     <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Informasi</h2>
        <p>Kontak Kami & Form Pendaftaran</p>
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
              <h3>Alamat Lab</h3>
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
              <h3>Kontak Kami</h3>
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
              <h3>Email Kami</h3>
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
              <h3>Jam Layanan</h3>
              <p>Senin - Jum'at</p>
              <p>08:00 - 16:00 WIB</p>
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
