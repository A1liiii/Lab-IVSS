<?php
$title  = "Publikasi | IVSS";
$active = "publikasi";

require_once __DIR__ . '/../../core/database.php';

function limit_words(string $text, int $limit = 10): string {
    $words = preg_split('/\s+/', trim($text));
    if (!$words) return $text;
    if (count($words) <= $limit) {
        return $text;
    }
    $short = implode(' ', array_slice($words, 0, $limit));
    return $short . '...';
}

$db = Database::connect();

// query publikasi + penulis (dosen), tapi jangan hilangin publikasinya
$sql = "SELECT 
            p.publikasi_id,
            p.judul,
            p.link,
            p.tahun,
            COALESCE(d.nama, 'Tidak diketahui') AS penulis
        FROM publikasi AS p
        LEFT JOIN dosen AS d ON d.user_id = p.user_id
        ORDER BY p.tahun DESC";

$stmt = $db->prepare($sql);
$stmt->execute();
$publikasi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
/*--------------------------------------------------------------
# Publikasi Section
--------------------------------------------------------------*/
/*--------------------------------------------------------------
# Publikasi Section
--------------------------------------------------------------*/

/* Card publikasi rapi + readmore nempel ke bawah */
.recent-posts .post-item {
  background-color: var(--surface-color);
  box-shadow: 0px 2px 20px rgba(0, 0, 0, 0.1);
  border-radius: 12px;
  overflow: hidden;          /* hilangkan sisa putih di bawah */
  display: flex;
  flex-direction: column;
}

/* Garis pemisah sedikit rapat */
.recent-posts .post-item hr {
  color: color-mix(in srgb, var(--default-color), transparent 80%);
  margin: 10px 0 6px;
}

/* Konten card */
.recent-posts .post-item .post-content {
  padding: 26px 30px 0;      /* atas agak turun, bawah nol (biru tutup) */
  display: flex;
  flex-direction: column;
}

/* Judul publikasi: bisa 1–3 baris */
.recent-posts .post-item .post-title {
  display: -webkit-box;
  -webkit-line-clamp: 3; 
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 72px; /* 3 baris @ 24px, sesuaikan dengan font size */
}

/* Read More bar biru nempel ke bawah card */
.recent-posts .post-item .readmore-box {
  background: #0F2F8A;
  border-radius: 0 0 12px 12px;
  padding: 14px 20px;
  margin: 8px -30px 0 -30px;  /* kiri-kanan full, atas sedikit jarak, BAWAH 0  */
}

/* Text di dalam biru → putih */
.recent-posts .post-item .readmore-box .readmore {
  color: #ffffff !important;
  display: flex;
  align-items: center;
  font-weight: 600;
}

/* Arrow hover */
.recent-posts .post-item .readmore:hover i {
  transform: translateX(6px);
  transition: transform 0.2s ease;
}
/* Biar semua card punya tinggi konten yang sama */
.recent-posts .post-item .post-content {
  padding: 26px 30px 0;
  display: flex;
  flex-direction: column;
  min-height: 180px; /* --- tambahkan ini --- */
}

</style>

<?php
ob_start();
?>


<!-- Publikasi Section -->
    <section id="recent-posts" class="recent-posts section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Riset</h2>
        <p>Publikasi   </p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-5">

          <?php foreach ($publikasi as $p): ?>

  <div class="col-xl-4 col-md-6">
    <div class="post-item position-relative h-100" data-aos="fade-up">

      <div class="post-content d-flex flex-column">

        <!-- JUDUL -->
        <h3 class="post-title" title="<?= htmlspecialchars($p['judul']) ?>">
          <?= htmlspecialchars(limit_words($p['judul'], 10)) ?>
        </h3>

        <!-- NAMA DOSEN -->
        <div class="meta d-flex align-items-center mb-1">
          <div class="d-flex align-items-center">
            <i class="bi bi-person"></i>
            <span class="ps-2"><?= htmlspecialchars($p['penulis']) ?></span>
          </div>
        </div>

        <!-- TAHUN (ikon jam) -->
        <div class="meta d-flex align-items-center mb-2">
          <div class="d-flex align-items-center">
            <i class="bi bi-clock"></i>
            <span class="ps-2"><?= htmlspecialchars($p['tahun']) ?></span>
          </div>
        </div>

        <hr>

        <!-- READ MORE -->
        <div class="readmore-box">
          <a href="<?= htmlspecialchars($p['link']) ?>" class="readmore stretched-link" target="_blank">
            <span>Read More</span>
            <i class="bi bi-arrow-right"></i>
          </a>
        </div>

      </div>

    </div>
  </div>

<?php endforeach ?>

        </div>

      </div>

    </section><!-- /Recent Posts Section -->

<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>
