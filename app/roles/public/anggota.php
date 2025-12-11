<?php
// =========================
// SETUP & AMBIL DATA ANGGOTA
// =========================
$title  = "Anggota | IVSS";
$active = "team";

ob_start();

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

/*
   Ambil semua anggota dari:
   - dosen
   - mahasiswa

   Gabung ke user_roles + roles untuk urutan:
   role_name:
     'ketua lab'  -> urutan 1
     'dosen'      -> urutan 2
     'mahasiswa'  -> urutan 3

   Sekaligus ambil:
   - kategori   -> dari mahasiswa.kategori (riset / alumni), NULL untuk dosen
   - status_mhs -> dari mahasiswa.status (aktif / lulus / dll), NULL untuk dosen
*/

$sql = "
    SELECT 
        a.user_id,
        a.id_anggota,
        a.nama,
        a.foto,
        a.jabatan,
        a.kategori,
        a.status_mhs,
        a.tipe,
        r.role_name
    FROM (
        -- Dosen
        SELECT 
            d.user_id,
            d.nip AS id_anggota,
            d.nama,
            d.foto,
            d.jabatan,
            NULL::varchar AS kategori,
            NULL::varchar AS status_mhs,
            'dosen'::varchar AS tipe
        FROM dosen d

        UNION ALL

        -- Mahasiswa
        SELECT 
            m.user_id,
            m.nim AS id_anggota,
            m.nama,
            m.foto,
            'Mahasiswa'::varchar AS jabatan,
            m.kategori::varchar AS kategori,
            m.status::varchar   AS status_mhs,
            'mahasiswa'::varchar AS tipe
        FROM mahasiswa m
    ) AS a
    JOIN user_roles ur ON ur.user_id = a.user_id
    JOIN roles r       ON r.role_id = ur.role_id
    WHERE LOWER(r.role_name) IN ('ketua lab','dosen','mahasiswa')
    ORDER BY 
        CASE 
            WHEN LOWER(r.role_name) = 'ketua lab' THEN 1   -- ketua lab dulu
            WHEN LOWER(r.role_name) = 'dosen'     THEN 2   -- lalu dosen
            WHEN LOWER(r.role_name) = 'mahasiswa' THEN 3   -- terakhir mahasiswa
            ELSE 4
        END,
        a.nama ASC
";

$stmt    = $conn->query($sql);
$anggota = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- CSS KHUSUS HALAMAN ANGGOTA -->
<style>
/* Filter tabs */
.anggota-filter-group {
    text-align: center;
    margin-bottom: 1.5rem;
}

.anggota-filter-btn {
    background: none;
    border: none;
    padding: 6px 14px;
    margin: 0 4px;
    font-weight: 600;
    font-size: 1rem;
    color: #64748b;
    cursor: pointer;
    position: relative;
}

.anggota-filter-btn::after {
    content: "";
    position: absolute;
    left: 50%;
    bottom: -4px;
    width: 0;
    height: 2px;
    background: #facc15;
    border-radius: 999px;
    transform: translateX(-50%);
    transition: width 0.2s ease;
}

.anggota-filter-btn.active {
    color: #facc15;
}

.anggota-filter-btn.active::after {
    width: 60%;
}

/* Card Base */
.team-member {
    background: #ffffff;
    border-radius: 18px;
    padding: 20px 16px 26px;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
    transition: all 0.25s ease;
    text-align: center;
}

/* Hover Card */
.team-member:hover {
    transform: translateY(-8px);
    box-shadow: 0 16px 35px rgba(15, 23, 42, 0.15);
}

/* Foto */
.team-member .member-img {
    width: 100%;
    height: 240px;
    overflow: hidden;
    border-radius: 14px;
    background: #f1f5f9;
}

.team-member .member-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

/* Separator antara foto & teks */
.team-member::before {
    content: "";
    width: 70%;
    height: 1.5px;
    background: #e2e8f0;
    margin: 12px auto 18px;
    display: block;
    border-radius: 2px;
}

/* Nama */
.team-member .member-info h4 {
    font-size: 1.15rem;
    font-weight: 700;
    letter-spacing: 0.3px;
    color: #0f172a;
    margin-bottom: 6px;
    transition: all 0.25s ease;
}

/* Jabatan */
.team-member .member-info span {
    font-size: 0.85rem;
    font-weight: 500;
    color: #64748b;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

/* Hover Text */
.team-member:hover .member-info h4 {
    color: #1e40af;
    transform: translateY(-2px);
}

.team-member:hover .member-info span {
    color: #475569;
}
</style>

<!-- Team Section -->
<section id="team" class="team section">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2>Team</h2>
    <p>ANGGOTA TIM</p>
  </div>
  <!-- End Section Title -->

  <div class="container">

    <!-- FILTER TABS -->
    <div class="anggota-filter-group" data-aos="fade-up" data-aos-delay="50">
      <button class="anggota-filter-btn active" data-filter="all">All</button>
      <button class="anggota-filter-btn" data-filter="dosen">Dosen</button>
      <button class="anggota-filter-btn" data-filter="mahasiswa">Mahasiswa</button>
      <button class="anggota-filter-btn" data-filter="alumni">Alumni</button>
    </div>

    <div class="row gy-4">

      <?php if (empty($anggota)) : ?>

        <div class="col-12">
          <p class="text-center">Belum ada data anggota.</p>
        </div>

      <?php else : ?>

        <?php foreach ($anggota as $row) : ?>

          <?php
          // lowercase helper
          $roleNameLower = strtolower($row['role_name'] ?? '');
          $kategoriLower = strtolower($row['kategori'] ?? '');
          $statusLower   = strtolower($row['status_mhs'] ?? '');

          // =========================
          // TENTUKAN TYPE UNTUK FILTER
          // =========================
          if ($roleNameLower === 'dosen' || $roleNameLower === 'ketua lab') {
              $filterType = 'dosen';
          } elseif ($roleNameLower === 'mahasiswa' &&
                   ($kategoriLower === 'alumni' || $statusLower === 'lulus')) {
              // mahasiswa kategori alumni ATAU status lulus => alumni
              $filterType = 'alumni';
          } elseif ($roleNameLower === 'mahasiswa') {
              $filterType = 'mahasiswa';
          } else {
              $filterType = 'other';
          }

          // =========================
          // TENTUKAN JABATAN TAMPILAN
          // =========================
          $displayJabatan = $row['jabatan']; // default dari query

          if ($roleNameLower === 'dosen') {
              $displayJabatan = 'Dosen Peneliti';
          } elseif ($roleNameLower === 'mahasiswa') {
              if ($kategoriLower === 'alumni' || $statusLower === 'lulus') {
                  $displayJabatan = 'Alumni';
              } else {
                  $displayJabatan = 'Mahasiswa Riset';
              }
          }
          // Ketua lab: tetap pakai jabatan asli dari tabel dosen

          // Link ke detail
          $link = "anggota_detail.php?tipe=" . urlencode($row['tipe']) .
                  "&id=" . urlencode($row['id_anggota']);
          ?>

          <div class="col-lg-3 col-md-6 d-flex align-items-stretch mb-4 anggota-item"
               data-type="<?= htmlspecialchars($filterType) ?>"
               data-aos="fade-up">

            <a href="<?= $link ?>" class="w-100 text-decoration-none text-dark">

              <div class="team-member h-100">

                <!-- FOTO -->
                <div class="member-img">
                  <img
                    src="/lab-IVSS/public/uploads/anggota/<?= htmlspecialchars($row['foto'] ?: 'default.jpg') ?>"
                    alt="<?= htmlspecialchars($row['nama']) ?>">
                </div>

                <!-- NAMA & JABATAN -->
                <div class="member-info">
                  <h4><?= htmlspecialchars($row['nama']) ?></h4>
                  <span><?= htmlspecialchars($displayJabatan) ?></span>
                </div>

              </div>

            </a>

          </div>

        <?php endforeach; ?>

      <?php endif; ?>

    </div>
  </div>
</section>
<!-- End Team Section -->

<script>
document.addEventListener('DOMContentLoaded', function () {
  const buttons = document.querySelectorAll('.anggota-filter-btn');
  const items   = document.querySelectorAll('.anggota-item');

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      const filter = btn.getAttribute('data-filter');

      // aktifkan tombol
      buttons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      // show/hide card sesuai filter
      items.forEach(card => {
        const type = card.getAttribute('data-type'); // dosen / mahasiswa / alumni / other

        if (filter === 'all') {
          // ALL: hanya dosen + mahasiswa aktif
          if (type === 'alumni') {
            card.classList.add('d-none');   // alumni (termasuk yg lulus) disembunyikan
          } else {
            card.classList.remove('d-none');
          }
        } else if (filter === type) {
          card.classList.remove('d-none');
        } else {
          card.classList.add('d-none');
        }
      });
    });
  });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>
