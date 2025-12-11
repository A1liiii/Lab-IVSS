<?php
$title = "Riset | IVSS";
$active = "riset";

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

/* ===========================================================
   FETCH PUBLIKASI
=========================================================== */
$stmtPub = $conn->prepare("
    SELECT 
        p.judul,
        p.link,
        p.tahun,
        d.nama
    FROM publikasi p
    LEFT JOIN users u ON u.user_id = p.user_id
    LEFT JOIN dosen d ON d.nip = u.nip
    ORDER BY p.tahun DESC
");
$stmtPub->execute();
$publikasi = $stmtPub->fetchAll(PDO::FETCH_ASSOC);

/* ===========================================================
   FETCH PROYEK + ANGGOTA
=========================================================== */
function getAnggotaProyek($conn, $proyek_id) {
    $stmt = $conn->prepare("
        SELECT a.role, u.username, d.nama
        FROM anggota_proyek a
        LEFT JOIN users u ON u.user_id = a.user_id
        LEFT JOIN dosen d ON d.nip = u.nip
        WHERE a.proyek_id = ?
    ");
    $stmt->execute([$proyek_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$stmtProyek = $conn->prepare("
    SELECT proyek_id, judul, deskripsi, tanggal_mulai, tanggal_selesai, status
    FROM proyek
    ORDER BY tanggal_mulai DESC
");
$stmtProyek->execute();
$proyekList = $stmtProyek->fetchAll(PDO::FETCH_ASSOC);

/* ===========================================================
   URL FIXER
=========================================================== */
function fixUrl($url) {
    $url = trim($url);

    if ($url === '') return '#';

    if (preg_match('/^https?:\/\//i', $url)) return $url;

    if (preg_match('/^www\./i', $url)) return "https://" . $url;

    return "../../../public/uploads/publikasi/" . $url;
}

ob_start();
?>

<style>
/* ============= FIX CARD & TITLE OVERFLOW ============= */
.post-item {
    padding: 25px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0px 0px 25px rgba(0,0,0,0.08);
    min-height: 240px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.post-title {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    line-height: 1.4;
    max-width: 100%;
    white-space: normal;
    overflow-wrap: break-word;

    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.meta i { color: #c9a800; }

.post-date {
    background: #ffde59;
    color: #000;
    padding: 5px 12px;
    font-size: 12px;
    border-radius: 5px;
    font-weight: 600;
}
</style>

<!-- ===========================================================
     PUBLIKASI
=========================================================== -->
<section id="recent-posts" class="recent-posts section">

    <div class="container section-title" data-aos="fade-up">
        <p>Daftar publikasi hasil riset dan penelitian</p>
    </div>

    <div class="container">
        <div class="row gy-5">

            <?php foreach ($publikasi as $i => $p): ?>
            <div class="col-xl-4 col-md-6">
                <div class="post-item h-100" data-aos="fade-up" data-aos-delay="<?= 100 + $i * 100 ?>">

                    <div class="post-content d-flex flex-column">

                        <h3 class="post-title"><?= htmlspecialchars($p['judul']) ?></h3>

                        <div class="meta d-flex align-items-center mt-2">
                            <i class="bi bi-person"></i>
                            <span class="ps-2"><?= htmlspecialchars($p['nama'] ?? "Dosen") ?></span>
                        </div>

                        <div class="d-flex align-items-center mt-2">
                            <i class="bi bi-calendar-event"></i>
                            <span class="ps-2"><?= htmlspecialchars($p['tahun']) ?></span>
                        </div>

                        <hr>

                        <!-- ========== BUTTON LINK ONLY (FIX) ========== -->
                        <a href="<?= fixUrl($p['link']) ?>"
                           target="_blank"
                           class="btn btn-primary mt-auto w-100 d-flex justify-content-between align-items-center">
                            <span>Buka Publikasi</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>

                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
    </div>

</section>

<!-- ===========================================================
     PROYEK RISET
=========================================================== -->
<section id="projects" class="recent-posts section">

    <div class="container section-title" data-aos="fade-up">
        <p>Daftar Proyek Riset</p>
    </div>

    <div class="container">
        <div class="row gy-5">

            <?php foreach ($proyekList as $i => $p): 
                $anggota = getAnggotaProyek($conn, $p['proyek_id']);
            ?>
            <div class="col-xl-4 col-md-6">
                <div class="post-item h-100" data-aos="fade-up" data-aos-delay="<?= 150 + $i * 100 ?>">

                    <div class="post-content d-flex flex-column">

                        <h3 class="post-title"><?= htmlspecialchars($p['judul']) ?></h3>

                        <div class="meta d-flex align-items-center mt-2">
                            <i class="bi bi-flag"></i>
                            <span class="ps-2 text-capitalize"><?= htmlspecialchars($p['status']) ?></span>
                        </div>

                        <div class="mt-2 text-muted small">
                            <i class="bi bi-calendar-range"></i>

                            <?php if ($p['tanggal_selesai']): ?>
                                <span class="ps-2">
                                    <?= htmlspecialchars($p['tanggal_mulai']) ?> →
                                    <?= htmlspecialchars($p['tanggal_selesai']) ?>
                                </span>
                            <?php else: ?>
                                <span class="ps-2"><?= htmlspecialchars($p['tanggal_mulai']) ?> (ongoing)</span>
                            <?php endif; ?>
                        </div>

                        <p class="mt-3" style="text-align: justify;">
                            <?= nl2br(htmlspecialchars(substr($p['deskripsi'], 0, 150))) ?>...
                        </p>

                        <div class="mt-2">
                            <strong>Anggota Proyek:</strong>
                            <ul class="mt-2">
                                <?php foreach ($anggota as $a): ?>
                                <li>
                                    <?= htmlspecialchars($a['nama'] ?? $a['username']) ?>
                                    <span class="text-muted">(<?= htmlspecialchars($a['role']) ?>)</span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

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
