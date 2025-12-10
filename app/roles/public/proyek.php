<?php
$title  = "Proyek | IVSS";
$active = "proyek";

require_once __DIR__ . '/../../core/database.php';
$db = Database::connect();

/**
 * Ambil semua proyek + dosen penanggung jawab (ketua).
 */
$sql = "SELECT 
            p.proyek_id,
            p.judul,
            p.deskripsi,
            p.tanggal_mulai,
            p.tanggal_selesai,
            p.status,
            COALESCE(d.nama, 'Tidak diketahui') AS dosen_pj
        FROM proyek AS p
        LEFT JOIN anggota_proyek AS ap
            ON ap.proyek_id = p.proyek_id
           AND ap.role = 'ketua'
        LEFT JOIN users AS u
            ON u.user_id = ap.user_id
        LEFT JOIN dosen AS d
            ON d.user_id = u.user_id
        ORDER BY p.tanggal_mulai DESC";

$stmt = $db->prepare($sql);
$stmt->execute();
$proyek = $stmt->fetchAll(PDO::FETCH_ASSOC);

/** batasi judul di card jadi 10 kata */
function limit_words(string $text, int $limit = 10): string {
    $words = preg_split('/\s+/', trim($text));
    if (!$words) return $text;
    if (count($words) <= $limit) return $text;
    return implode(' ', array_slice($words, 0, $limit)) . '...';
}

/** format tanggal simple */
function tgl_id($dateStr) {
    if (!$dateStr) return '-';
    return date('d M Y', strtotime($dateStr)); // contoh: 05 Mar 2025
}

ob_start();
?>

<style>
/* ==================== PROYEK – CARD LIST ==================== */

/* Card dasar di section proyek saja */
#projects .post-item {
  background: #ffffff;
  border-radius: 14px;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
  transition: 0.2s ease;
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 260px;           /* tinggi minimum sama untuk semua card */
  overflow: hidden;
}

/* Efek hover card */
#projects .post-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 26px rgba(0, 0, 0, 0.10);
}

/* Isi card (atas) */
#projects .post-item .post-content {
  padding: 24px 24px 18px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

/* Judul proyek: tetap area tinggi 3 baris */
#projects .post-item .post-title {
  display: -webkit-box;
  -webkit-line-clamp: 3;        /* maksimal 3 baris */
  -webkit-box-orient: vertical;
  overflow: hidden;
  font-size: 22px;
  font-weight: 700;
  margin-bottom: 12px;
  min-height: 72px;             /* kira² tinggi 3 baris */
}

/* Meta (dosen dan tanggal) */
#projects .post-item .meta i {
  color: #fbbf24;               /* ikon kuning */
}

#projects .post-item .meta span {
  font-size: 0.95rem;
  color: #4b5563;
}

/* Badge status di dalam flow (bukan pojok) */
#projects .post-item .status-badge {
  position: static;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 999px;
  text-transform: uppercase;
}

/* Warna status */
.status-running {
  background: #e0f2fe;
  color: #0369a1;
}

.status-done {
  background: #dcfce7;
  color: #15803d;
}

.status-other {
  background: #e5e7eb;
  color: #374151;
}

/* Wrapper status jaraknya kecil */
#projects .post-item .mt-1.mb-2 {
  margin-top: 4px !important;
  margin-bottom: 6px !important;
}

/* Bar biru Detail Proyek di bawah card */
#projects .post-item .readmore-box {
  background: #0F2F8A;
  padding: 14px 20px;
  margin: 0 -24px -18px -24px;      /* full width, nempel ke kiri-kanan & bawah */
  border-radius: 0 0 12px 12px;
  transition: background 0.3s ease;
  margin-top: auto;                  /* dorong bar biru ke paling bawah card */
}

#projects .post-item:hover .readmore-box {
  background: #0C256E;
}

#projects .post-item .readmore-box .readmore {
  display: flex;
  align-items: center;
  font-weight: 600;
  color: #ffffff !important;
  text-decoration: none;
}

#projects .post-item .readmore-box .readmore i {
  margin-left: 6px;
  font-size: 1rem;
}

/* ==================== MODAL DETAIL PROYEK ==================== */

.proyek-modal-header {
  background: #0F2F8A;
  color: #ffffff;
  border-bottom: none;
}

.proyek-modal-header .modal-title {
  color: #ffffff;
  font-weight: 600;
}

.proyek-modal-header .btn-close {
  filter: brightness(0) invert(1);
  opacity: 0.95;
}

/* Status badge di modal */
.modal-status-badge {
  padding: 4px 12px;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 999px;
  text-transform: uppercase;
  display: inline-block;
}

.modal-status-running {
  background: #e0f2fe;
  color: #0369a1;
}

.modal-status-done {
  background: #dcfce7;
  color: #15803d;
}

.modal-status-other {
  background: #e5e7eb;
  color: #374151;
}


</style>

<!-- Proyek Section -->
<section id="projects" class="recent-posts section">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2>Riset</h2>
    <p>Proyek</p>
  </div>

  <div class="container">
    <div class="row gy-5 align-items-stretch">

      <?php foreach ($proyek as $p): 
        $status = strtolower(trim($p['status'] ?? ''));
        $statusClass = match ($status) {
          'on going'  => 'status-running',
          'selesai'   => 'status-done',
          default     => 'status-other',
        };
      ?>

        <div class="col-xl-4 col-md-6 d-flex">
          <div class="post-item position-relative h-100 w-100" data-aos="fade-up">
            <div class="post-content d-flex flex-column">

              <!-- Judul proyek -->
              <h3 class="post-title" title="<?= htmlspecialchars($p['judul']) ?>">
                <?= htmlspecialchars(limit_words($p['judul'], 10)) ?>
              </h3>

              <!-- Dosen penanggung jawab -->
              <div class="meta d-flex align-items-center mb-2">
                <div class="d-flex align-items-center">
                  <i class="bi bi-person"></i>
                  <span class="ps-2"><?= htmlspecialchars($p['dosen_pj']) ?></span>
                </div>
              </div>

              <!-- Tanggal mulai -->
              <div class="meta d-flex align-items-center mb-2">
                <div class="d-flex align-items-center">
                  <i class="bi bi-clock"></i>
                  <span class="ps-2">
                    <?= htmlspecialchars(tgl_id($p['tanggal_mulai'])) ?>
                  </span>
                </div>
              </div>

              <!-- STATUS di bawah tanggal -->
              <div class="mt-1 mb-2">
                <span class="status-badge <?= $statusClass ?>">
                  <?= htmlspecialchars($p['status'] ?: 'Tidak diketahui') ?>
                </span>
              </div>

              <!-- Tombol Detail Proyek (modal) -->
              <div class="readmore-box">
                <a href="#"
                   class="readmore stretched-link"
                   data-bs-toggle="modal"
                   data-bs-target="#proyekModal<?= (int)$p['proyek_id'] ?>">
                  <span>Detail Proyek</span>
                  <i class="bi bi-arrow-right"></i>
                </a>
              </div>

            </div>
          </div>
        </div>

        <!-- MODAL DETAIL PROYEK -->
        <div class="modal fade" id="proyekModal<?= (int)$p['proyek_id'] ?>" tabindex="-1"
             aria-labelledby="proyekModalLabel<?= (int)$p['proyek_id'] ?>" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

              <div class="modal-header proyek-modal-header">
                <h5 class="modal-title" id="proyekModalLabel<?= (int)$p['proyek_id'] ?>">
                  <?= htmlspecialchars($p['judul']) ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <div class="modal-body">

                <!-- Baris: Deskripsi (kiri) + Status (kanan) -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <p class="mb-0"><strong>Deskripsi Proyek</strong></p>

                  <?php
                    $status = strtolower(trim($p['status'] ?? ''));
                    $statusClassModal = match ($status) {
                      'berjalan', 'on going'  => 'modal-status-running',
                      'selesai'               => 'modal-status-done',
                      'perencanaan', 'planning' => 'modal-status-planning',
                      default                 => 'modal-status-other',
                    };
                  ?>
                  <span class="modal-status-badge <?= $statusClassModal ?>">
                    <?= htmlspecialchars($p['status'] ?: '-') ?>
                  </span>
                </div>

                <!-- Isi deskripsi -->
                <p class="mb-3">
                  <?= nl2br(htmlspecialchars($p['deskripsi'] ?? '-')) ?>
                </p>

                <!-- Tanggal mulai -->
                <p class="d-flex align-items-center mb-1">
                  <i class="bi bi-calendar-event me-2 text-primary"></i>
                  <strong class="me-1">Tanggal Mulai:</strong>
                  <span><?= htmlspecialchars(tgl_id($p['tanggal_mulai'])) ?></span>
                </p>

                <!-- Tanggal selesai -->
                <p class="d-flex align-items-center mb-3">
                  <i class="bi bi-calendar-check me-2 text-success"></i>
                  <strong class="me-1">Tanggal Selesai:</strong>
                  <span><?= htmlspecialchars(tgl_id($p['tanggal_selesai'])) ?></span>
                </p>

                <hr>

                <p><strong>Anggota Proyek</strong></p>

                <?php
                // ambil anggota proyek (dosen & mahasiswa)
                $stmtAng = $db->prepare("
                  SELECT 
                    ap.role,
                    COALESCE(d.nama, m.nama, u.username) AS nama
                  FROM anggota_proyek AS ap
                  JOIN users AS u ON u.user_id = ap.user_id
                  LEFT JOIN dosen AS d ON d.user_id = u.user_id
                  LEFT JOIN mahasiswa AS m ON m.user_id = u.user_id
                  WHERE ap.proyek_id = :pid
                ");
                $stmtAng->execute([':pid' => $p['proyek_id']]);
                $anggota = $stmtAng->fetchAll(PDO::FETCH_ASSOC);

                $ketua = null;
                $anggotaLain = [];

                foreach ($anggota as $row) {
                    $roleLower = strtolower(trim($row['role'] ?? ''));
                    if ($roleLower === 'ketua' && $ketua === null) {
                        $ketua = $row['nama'];
                    } else {
                        $anggotaLain[] = $row['nama'];
                    }
                }
                ?>

                <?php if (!$ketua && empty($anggotaLain)): ?>
                  <p class="text-muted mb-0">Belum ada data anggota.</p>
                <?php else: ?>

                  <?php if ($ketua): ?>
                    <p class="d-flex align-items-center mb-1">
                      <i class="bi bi-person-circle me-2"></i>
                      <span><strong>Ketua:</strong> <?= htmlspecialchars($ketua) ?></span>
                    </p>
                  <?php endif; ?>

                  <?php if (!empty($anggotaLain)): ?>
                    <?php foreach ($anggotaLain as $index => $namaAnggota): ?>
                      <p class="d-flex align-items-center mb-1">
                        <i class="bi bi-person-circle me-2"></i>
                        <span>
                          <strong>Anggota <?= $index + 1 ?>:</strong>
                          <?= htmlspecialchars($namaAnggota) ?>
                        </span>
                      </p>
                    <?php endforeach; ?>
                  <?php endif; ?>

                <?php endif; ?>

              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                  Tutup
                </button>
              </div>

            </div>
          </div>
        </div>
        <!-- END MODAL -->

      <?php endforeach; ?>

    </div>
  </div>

</section><!-- /Projects Section -->

<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>
