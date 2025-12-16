<?php
$title = "Tentang Kami | IVSS";
$active = "about";

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

/* ============================================================
   FETCH LAB INFO (nama, deskripsi, visi, misi)
============================================================ */
$stmtLab = $conn->prepare("SELECT nama, deskripsi, visi, misi FROM lab_info LIMIT 1");
$stmtLab->execute();
$lab = $stmtLab->fetch(PDO::FETCH_ASSOC) ?: [
    'nama' => 'Laboratorium',
    'deskripsi' => '',
    'visi' => '',
    'misi' => ''
];

/* ============================================================
   FETCH DOSEN + ROLE
============================================================ */
$sqlDosen = "
    SELECT 
        u.user_id,
        u.username,
        u.nip,
        d.nama AS nama_dosen
    FROM users u
    JOIN dosen d ON d.nip = u.nip
";
$stmtDosen = $conn->prepare($sqlDosen);
$stmtDosen->execute();
$dosenList = $stmtDosen->fetchAll(PDO::FETCH_ASSOC);

/* Ambil role utama */
function getRoleUtama($conn, $user_id) {

    $stmt = $conn->prepare("
        SELECT r.role_name
        FROM user_roles ur
        JOIN roles r ON r.role_id = ur.role_id
        WHERE ur.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!$roles) return "Peneliti";

    $prioritas = ["ketua lab", "admin", "operator", "dosen"];

    foreach ($prioritas as $p) {
        foreach ($roles as $r) {
            if (strtolower($r) === $p) {
                return ucfirst($r);
            }
        }
    }

    return "Peneliti";
}

/* PRIORITAS SORTING ANGGOTA */
function getSortIndex($role) {
    $priority = [
        "ketua lab" => 1,
        "admin" => 2,
        "operator" => 3,
        "dosen" => 4,
        "peneliti" => 5
    ];

    $r = strtolower($role);

    return $priority[$r] ?? 999;
}

/* ============================================================
   FETCH MAHASISWA RISET
============================================================ */
$sqlMhs = "
    SELECT nim, nama
    FROM mahasiswa
    WHERE kategori = 'riset'
      AND status = 'aktif'
    ORDER BY nama ASC
";
$stmtMhs = $conn->prepare($sqlMhs);
$stmtMhs->execute();
$mhsList = $stmtMhs->fetchAll(PDO::FETCH_ASSOC);

/* ============================================================
   SORT DOSEN BERDASARKAN PRIORITAS ROLE
============================================================ */
foreach ($dosenList as &$d) {
    $d['main_role'] = getRoleUtama($conn, $d['user_id']);
    $d['sort_index'] = getSortIndex(strtolower($d['main_role']));
}
unset($d);

usort($dosenList, function($a, $b) {
    return $a['sort_index'] - $b['sort_index'];
});

/* ============================================================
   START OUTPUT BUFFER
============================================================ */
ob_start();
?>

<style>
/* FIX CARD TEAM SIZE */
.team-member {
    background: #ffffff;
    border-radius: 14px;
    padding: 15px;
    width: 100%;
    max-width: 260px;
    min-height: 420px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-left: auto;
    margin-right: auto;
}



/* Foto Rounded Circle */
.member-img {
    width: 160px;
    height: 160px;
    margin: 0 auto 15px auto;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #eee;
}

.member-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.member-info h4 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 4px;
    word-wrap: break-word;
    white-space: normal;
}

.member-info span {
    font-size: 14px;
    color: #666;
}
</style>

<!-- ABOUT SECTION -->
<section id="about" class="about section">
    <div class="container" data-aos="fade-up">
        <div class="row gx-0">

            <div class="col-lg-6 d-flex flex-column justify-content-center" 
                 data-aos="fade-up" data-aos-delay="200">
                <div class="content">
                    <h2><?= htmlspecialchars($lab['nama']) ?></h2>
                    
                    <p style="text-align: justify;">
                        <?= nl2br(htmlspecialchars($lab['deskripsi'])) ?>
                    </p>

                </div>
            </div>

            <div class="col-lg-6 d-flex align-items-center" 
                 data-aos="zoom-out" data-aos-delay="200">
                <img src="../../../public/assets/img/about.jpg" class="img-fluid" alt="">
            </div>

        </div>
    </div>
</section>

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


<!-- TEAM SECTION -->
<section id="team" class="team section">
    <div class="container section-title" data-aos="fade-up">
        <p>ANGGOTA TIM LABORATORIUM</p>
    </div>

    <div class="container">
        <div class="row gy-4">

            <!-- DOSEN TERURUT SESUAI PRIORITAS -->
            <?php foreach ($dosenList as $i => $d): 
                $role = $d['main_role'];
                $img = "../../../public/uploads/profiles/" . $d['user_id'] . ".jpg";
                ?>

                <div class="col-lg-3 col-md-6 d-flex align-items-stretch"
                     data-aos="fade-up"
                     data-aos-delay="<?= 100 + ($i * 80) ?>">
                     
                    <div class="team-member">
                        <div class="member-img">
                            <img src="<?= $img ?>"
                                 onerror="this.src='../../../public/assets/img/default-user.png';'"
                                 alt="">
                        </div>

                        <div class="member-info">
                            <h4><?= htmlspecialchars($d['nama_dosen']) ?></h4>
                            <span><?= htmlspecialchars($role) ?></span>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>


            <!-- MAHASISWA RISET -->
            <?php foreach ($mhsList as $j => $m): 
                $img = "../../../public/uploads/profiles/" . $m['nim'] . ".jpg";
                ?>

                <div class="col-lg-3 col-md-6 d-flex align-items-stretch"
                     data-aos="fade-up"
                     data-aos-delay="<?= 300 + ($j * 80) ?>">

                    <div class="team-member">
                        <div class="member-img">
                            <img src="<?= $img ?>"
                                 onerror="this.src='../../../public/assets/img/default-user.png';'"
                                 alt="">
                        </div>

                        <div class="member-info">
                            <h4><?= htmlspecialchars($m['nama']) ?></h4>
                            <span>Anggota | Riset</span>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>

        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>
