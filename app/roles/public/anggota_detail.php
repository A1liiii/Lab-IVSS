<?php
$title  = "Detail Anggota | IVSS";
$active = "team";

ob_start();

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

// =========================
// 1. Ambil parameter
// =========================
$tipe = $_GET['tipe'] ?? null;   // 'dosen' / 'mahasiswa'
$id   = $_GET['id']   ?? null;   // nip / nim

$profil      = null;
$mataKuliah  = [];
$proyek      = [];
$pendidikan  = [];
$error       = null;

if (!$tipe || !$id) {
    $error = "Parameter detail tidak lengkap.";
} else {
    $tipe = strtolower($tipe);

    if ($tipe === 'dosen') {
        // =========================
        // 2A. Ambil data DOSEN
        // =========================
        $stmt = $conn->prepare("
            SELECT *
            FROM dosen
            WHERE nip = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $profil = $stmt->fetch(PDO::FETCH_ASSOC);
        $userId = $profil['user_id'] ?? null;

        $fotoProfil = "../../../public/assets/img/default-user.png";

        if ($userId) {
            $candidate = "../../../public/uploads/profiles/{$userId}.jpg";
            if (file_exists($candidate)) {
                $fotoProfil = $candidate;
            }
        }

        if ($profil) {
            $userId = $profil['user_id'] ?? null;

            // 2B. Ambil MATA KULIAH
            $stmt = $conn->prepare("
                SELECT *
                FROM mata_kuliah
                WHERE nip = :nip
                ORDER BY tahun_ajar DESC, semester ASC, nama_matkul ASC
            ");
            $stmt->execute(['nip' => $profil['nip']]);
            $mataKuliah = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 2C. Ambil PROYEK (via user_id)
            if ($userId) {
                $stmt = $conn->prepare("
                    SELECT p.*, ap.role
                    FROM anggota_proyek ap
                    JOIN proyek p ON p.proyek_id = ap.proyek_id
                    WHERE ap.user_id = :user_id
                    ORDER BY p.tanggal_mulai DESC, p.proyek_id DESC
                ");
                $stmt->execute(['user_id' => $userId]);
                $proyek = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            // 2D. Ambil PENDIDIKAN (urut S1-S2-S3)
            $stmt = $conn->prepare("
                SELECT *,
                  CASE
                    WHEN pendidikan_tinggi ILIKE '%s1%'      THEN 1
                    WHEN pendidikan_tinggi ILIKE '%sarjana%' THEN 1

                    WHEN pendidikan_tinggi ILIKE '%s2%'      THEN 2
                    WHEN pendidikan_tinggi ILIKE '%magister%' THEN 2

                    WHEN pendidikan_tinggi ILIKE '%s3%'      THEN 3
                    WHEN pendidikan_tinggi ILIKE '%doktor%'  THEN 3

                    WHEN pendidikan_tinggi ILIKE '%prof%'    THEN 4
                    ELSE 99
                  END AS level_pendidikan
                FROM pendidikan
                WHERE nip_dosen = :nip
                ORDER BY level_pendidikan ASC
            ");
            $stmt->execute(['nip' => $profil['nip']]);
            $pendidikan = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } else {
            $error = "Data dosen tidak ditemukan.";
        }

    } elseif ($tipe === 'mahasiswa') {
        // =========================
        // 2A. Ambil data MAHASISWA
        // =========================
        $stmt = $conn->prepare("
            SELECT *
            FROM mahasiswa
            WHERE nim = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $profil = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($profil) {
            $userId = $profil['user_id'] ?? null;

            // Mahasiswa: tidak ada matkul & pendidikan di sini
            $mataKuliah = [];
            $pendidikan = [];

            // 2B. Ambil PROYEK (via user_id)
            if ($userId) {
                $stmt = $conn->prepare("
                    SELECT p.*, ap.role
                    FROM anggota_proyek ap
                    JOIN proyek p ON p.proyek_id = ap.proyek_id
                    WHERE ap.user_id = :user_id
                    ORDER BY p.tanggal_mulai DESC, p.proyek_id DESC
                ");
                $stmt->execute(['user_id' => $userId]);
                $proyek = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            $error = "Data mahasiswa tidak ditemukan.";
        }

    } else {
        $error = "Tipe anggota tidak dikenali.";
    }
}
?>

<style>
/* =========================================
   STYLE CARD DETAIL
   ========================================= */

/* Card header profil */
.profile-header-card {
    border-radius: 20px;
    padding: 22px 24px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.10);
    background: #ffffff;
}

/* FOTO header (kotak, foto tidak kepotong) */
.profile-photo-wrapper {
    width: 180px;
    height: 180px;
    border-radius: 18px;
    overflow: hidden;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-photo-wrapper img {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    object-position: center;
}

/* Badge tipe anggota */
.badge-anggota {
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    border-radius: 999px;
    padding: 4px 10px;
}

/* Card umum */
.detail-card {
    border-radius: 18px;
    border: none;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
    background-color: #ffffff;
}

/* Judul card */
.detail-card .card-title {
    font-size: 1.2rem;
    font-weight: 700;
    letter-spacing: 0.3px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.9rem;
    color: #10348f;
}

.detail-card .card-title i {
    font-size: 1.4rem;
}

/* Baris info profil (label : value) */
.info-row + .info-row {
    border-top: 1px dashed #e2e8f0;
    margin-top: 0.35rem;
    padding-top: 0.35rem;
}

.info-label {
    font-size: 0.9rem;
    color: #64748b;
}

.info-value {
    font-size: 0.95rem;
    color: #0f172a;
}

/* List item untuk matkul / proyek / pendidikan */
.item-list + .item-list {
    border-top: 1px solid #e2e8f0;
    margin-top: 0.75rem;
    padding-top: 0.75rem;
}

.item-title {
    font-weight: 600;
    margin-bottom: 0.15rem;
}

.item-meta {
    font-size: 0.86rem;
    color: #64748b;
}

/* Badge role proyek */
.badge-role-ketua {
    background-color: #facc15;
    color: #000;
}

.badge-role-anggota {
    background-color: #e2e8f0;
    color: #0f172a;
}

/* Badge status proyek */
.badge-status-selesai {
    background-color: #22c55e;
    color: #ffffff;
}
.badge-status-ongoing,
.badge-status-aktif {
    background-color: #0ea5e9;
    color: #ffffff;
}

/* Spasi antar row/card */
.section-gap {
    margin-top: 1.5rem;
}
</style>

<section class="section">
  <div class="container">

    <div class="mb-4">
      <a href="anggota.php" class="btn btn-sm btn-outline-secondary">
        &larr; Kembali ke Anggota
      </a>
    </div>

    <?php if (!empty($error)): ?>

      <div class="alert alert-danger">
        <?= htmlspecialchars($error) ?>
      </div>

    <?php elseif (!empty($profil)): ?>

      <!-- =========================
           CARD 1: HEADER PROFIL
           ========================= -->
      <div class="profile-header-card mb-4">
        <div class="row g-4 align-items-center">
          <!-- Foto kiri -->
          <div class="col-md-3 text-center">
            <div class="profile-photo-wrapper mx-md-0 mx-auto">
            <img
              src="<?= htmlspecialchars($fotoProfil) ?>?v=<?= time() ?>"
              alt="<?= htmlspecialchars($profil['nama'] ?? '') ?>">
            </div>
          </div>

          <!-- Info kanan -->
          <div class="col-md-9">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
              <h3 class="mb-0">
                <?= htmlspecialchars($profil['nama']) ?>
              </h3>

              <?php if ($tipe === 'dosen'): ?>
                <span class="badge bg-primary-subtle text-primary badge-anggota">
                  Dosen
                </span>
              <?php else: ?>
                <span class="badge bg-success-subtle text-success badge-anggota">
                  Mahasiswa Riset
                </span>
              <?php endif; ?>
            </div>

            <p class="mb-1 text-muted">
              <?php if ($tipe === 'dosen'): ?>
                <?= htmlspecialchars($profil['jabatan'] ?? '-') ?>
              <?php else: ?>
                <?= htmlspecialchars($profil['prodi'] ?? '-') ?>
                <?php if (!empty($profil['angkatan'])): ?>
                  · Angkatan <?= htmlspecialchars($profil['angkatan']) ?>
                <?php endif; ?>
              <?php endif; ?>
            </p>

            <?php if (!empty($profil['email'])): ?>
              <p class="mb-0">
                <i class="bi bi-envelope me-1"></i>
                <?= htmlspecialchars($profil['email']) ?>
              </p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <?php if ($tipe === 'dosen'): ?>

        <!-- =========================
             DOSEN
             ROW 1: PROFIL (6) & PENDIDIKAN (6)
             ========================= -->
        <div class="row section-gap">
          <!-- PROFIL LENGKAP -->
          <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="card detail-card h-100">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="bi bi-person-badge"></i>
                  <span>Profil Lengkap</span>
                </h5>

                <div class="info-row d-flex justify-content-between">
                  <div class="info-label">NIP</div>
                  <div class="info-value"><?= htmlspecialchars($profil['nip']) ?></div>
                </div>

                <div class="info-row d-flex justify-content-between">
                  <div class="info-label">NIDN</div>
                  <div class="info-value"><?= htmlspecialchars($profil['nidn'] ?? '-') ?></div>
                </div>

                <div class="info-row d-flex justify-content-between">
                  <div class="info-label">Jabatan</div>
                  <div class="info-value"><?= htmlspecialchars($profil['jabatan'] ?? '-') ?></div>
                </div>

                <div class="info-row d-flex justify-content-between">
                  <div class="info-label">Email</div>
                  <div class="info-value"><?= htmlspecialchars($profil['email'] ?? '-') ?></div>
                </div>
              </div>
            </div>
          </div>

          <!-- PENDIDIKAN -->
          <div class="col-lg-6">
            <div class="card detail-card h-100">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="bi bi-mortarboard"></i>
                  <span>Pendidikan</span>
                </h5>

                <?php if (empty($pendidikan)): ?>
                  <p class="mb-0 text-muted">Belum ada data pendidikan.</p>
                <?php else: ?>
                  <?php foreach ($pendidikan as $pd): ?>
                    <div class="item-list">
                      <div class="item-title">
                        <?= htmlspecialchars($pd['pendidikan_tinggi']) ?>
                      </div>
                      <div class="item-meta">
                        <?= htmlspecialchars($pd['universitas']) ?>
                        <?php if (!empty($pd['tahun_akhir'])): ?>
                          (<?= htmlspecialchars($pd['tahun_akhir'] ?? '') ?>)
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- =========================
             DOSEN
             ROW 2: MATA KULIAH (6) & PROYEK (6)
             ========================= -->
        <div class="row section-gap mb-4">
          <!-- MATA KULIAH -->
          <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="card detail-card h-100">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="bi bi-journal-bookmark"></i>
                  <span>Mata Kuliah</span>
                </h5>

                <?php if (empty($mataKuliah)): ?>
                  <p class="mb-0 text-muted">Belum ada data mata kuliah.</p>
                <?php else: ?>
                  <?php foreach ($mataKuliah as $mk): ?>
                    <div class="item-list">
                      <div class="item-title">
                        <?= htmlspecialchars($mk['nama_matkul']) ?>
                      </div>
                      <div class="item-meta">
                        · Semester: <?= htmlspecialchars($mk['semester']) ?>
                        · SKS: <?= htmlspecialchars($mk['sks']) ?>
                      </div>
                      <div class="item-meta">
                        Prodi: <?= htmlspecialchars($mk['prodi']) ?>
                        <?php if (!empty($mk['tahun_ajar'])): ?>
                          · Tahun Ajar: <?= htmlspecialchars($mk['tahun_ajar']) ?>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- PROYEK -->
          <div class="col-lg-6">
            <div class="card detail-card h-100">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="bi bi-kanban"></i>
                  <span>Proyek</span>
                </h5>

                <?php if (empty($proyek)): ?>
                  <p class="mb-0 text-muted">Belum ada proyek yang tercatat.</p>
                <?php else: ?>
                  <?php foreach ($proyek as $p): ?>
                    <?php
                      $roleLower   = strtolower($p['role'] ?? '');
                      $statusLower = strtolower($p['status'] ?? '');
                    ?>
                    <div class="item-list">
                      <div class="d-flex justify-content-between align-items-start">
                        <div class="item-title">
                          <?= htmlspecialchars($p['judul']) ?>
                        </div>
                        <div class="d-flex gap-1 flex-wrap">
                          <?php if ($roleLower === 'ketua'): ?>
                            <span class="badge badge-role-ketua">Ketua Proyek</span>
                          <?php else: ?>
                            <span class="badge badge-role-anggota">Anggota</span>
                          <?php endif; ?>

                          <?php if ($statusLower === 'selesai'): ?>
                            <span class="badge badge-status-selesai">
                              <?= htmlspecialchars($p['status']) ?>
                            </span>
                          <?php elseif (in_array($statusLower, ['ongoing','aktif'])): ?>
                            <span class="badge badge-status-ongoing">
                              <?= htmlspecialchars($p['status']) ?>
                            </span>
                          <?php elseif (!empty($p['status'])): ?>
                            <span class="badge bg-secondary text-white">
                              <?= htmlspecialchars($p['status']) ?>
                            </span>
                          <?php endif; ?>
                        </div>
                      </div>

                      <div class="item-meta mt-1">
                        <?php if (!empty($p['tanggal_mulai'])): ?>
                          Periode:
                          <?= htmlspecialchars($p['tanggal_mulai']) ?>
                          <?php if (!empty($p['tanggal_selesai'])): ?>
                            &ndash; <?= htmlspecialchars($p['tanggal_selesai']) ?>
                          <?php else: ?>
                            &ndash; sekarang
                          <?php endif; ?>
                        <?php endif; ?>
                      </div>

                      <?php if (!empty($p['deskripsi'])): ?>
                        <div class="item-meta mt-1">
                          <?= htmlspecialchars(mb_strimwidth($p['deskripsi'], 0, 130, '...')) ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

      <?php else: ?>

        <!-- =========================
             MAHASISWA
             ROW: PROFIL (5) & PROYEK (7)
             ========================= -->
        <div class="row section-gap mb-4">
          <!-- PROFIL MAHASISWA -->
          <div class="col-lg-5 mb-4 mb-lg-0">
            <div class="card detail-card h-100">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="bi bi-person-badge"></i>
                  <span>Profil Lengkap</span>
                </h5>

                <div class="info-row d-flex justify-content-between">
                  <div class="info-label">NIM</div>
                  <div class="info-value"><?= htmlspecialchars($profil['nim']) ?></div>
                </div>

                <div class="info-row d-flex justify-content-between">
                  <div class="info-label">Program Studi</div>
                  <div class="info-value"><?= htmlspecialchars($profil['prodi'] ?? '-') ?></div>
                </div>

                <div class="info-row d-flex justify-content-between">
                  <div class="info-label">Angkatan</div>
                  <div class="info-value"><?= htmlspecialchars($profil['angkatan'] ?? '-') ?></div>
                </div>

                <div class="info-row d-flex justify-content-between">
                  <div class="info-label">Kategori</div>
                  <div class="info-value"><?= htmlspecialchars($profil['kategori'] ?? '-') ?></div>
                </div>

                <div class="info-row d-flex justify-content-between">
                  <div class="info-label">Status</div>
                  <div class="info-value"><?= htmlspecialchars($profil['status'] ?? '-') ?></div>
                </div>

                <div class="info-row d-flex justify-content-between">
                  <div class="info-label">Email</div>
                  <div class="info-value"><?= htmlspecialchars($profil['email'] ?? '-') ?></div>
                </div>

                <div class="info-row d-flex justify-content-between">
                  <div class="info-label">Tanggal Bergabung</div>
                  <div class="info-value">
                    <?= htmlspecialchars($profil['tanggal_join'] ?? '-') ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- PROYEK MAHASISWA (lebih lebar) -->
          <div class="col-lg-7">
            <div class="card detail-card h-100">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="bi bi-kanban"></i>
                  <span>Proyek</span>
                </h5>

                <?php if (empty($proyek)): ?>
                  <p class="mb-0 text-muted">Belum ada proyek yang tercatat.</p>
                <?php else: ?>
                  <?php foreach ($proyek as $p): ?>
                    <?php
                      $roleLower   = strtolower($p['role'] ?? '');
                      $statusLower = strtolower($p['status'] ?? '');
                    ?>
                    <div class="item-list">
                      <div class="d-flex justify-content-between align-items-start">
                        <div class="item-title">
                          <?= htmlspecialchars($p['judul']) ?>
                        </div>
                        <div class="d-flex gap-1 flex-wrap">
                          <?php if ($roleLower === 'ketua'): ?>
                            <span class="badge badge-role-ketua">Ketua Proyek</span>
                          <?php else: ?>
                            <span class="badge badge-role-anggota">Anggota</span>
                          <?php endif; ?>

                          <?php if ($statusLower === 'selesai'): ?>
                            <span class="badge badge-status-selesai">
                              <?= htmlspecialchars($p['status']) ?>
                            </span>
                          <?php elseif (in_array($statusLower, ['ongoing','aktif'])): ?>
                            <span class="badge badge-status-ongoing">
                              <?= htmlspecialchars($p['status']) ?>
                            </span>
                          <?php elseif (!empty($p['status'])): ?>
                            <span class="badge bg-secondary text-white">
                              <?= htmlspecialchars($p['status']) ?>
                            </span>
                          <?php endif; ?>
                        </div>
                      </div>

                      <div class="item-meta mt-1">
                        <?php if (!empty($p['tanggal_mulai'])): ?>
                          Periode:
                          <?= htmlspecialchars($p['tanggal_mulai']) ?>
                          <?php if (!empty($p['tanggal_selesai'])): ?>
                            &ndash; <?= htmlspecialchars($p['tanggal_selesai']) ?>
                          <?php else: ?>
                            &ndash; sekarang
                          <?php endif; ?>
                        <?php endif; ?>
                      </div>

                      <?php if (!empty($p['deskripsi'])): ?>
                        <div class="item-meta mt-1">
                          <?= htmlspecialchars(mb_strimwidth($p['deskripsi'], 0, 130, '...')) ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>

              </div>
            </div>
          </div>
        </div>

      <?php endif; ?>

    <?php endif; ?>

  </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>