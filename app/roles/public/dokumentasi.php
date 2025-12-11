<?php
$title = "Dokumentasi | IVSS";
$active = "dokumentasi";

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

// Ambil semua dokumentasi
$stmt = $conn->prepare("
    SELECT documentation_id, type_file, caption, tanggal_kegiatan, jenis_kegiatan, uploaded_by, uploaded_at
    FROM act_documentation
    ORDER BY tanggal_kegiatan DESC, documentation_id DESC
");
$stmt->execute();
$dokumentasi = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<!-- ====================== CSS FIX ====================== -->
<style>
/* Bikin tinggi card seragam */
.portfolio-item .portfolio-content {
    height: 300px;                /* Atur tinggi ideal */
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    background: #f0f0f0;         /* fallback */
}

/* Foto memenuhi ruang card */
.portfolio-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;            /* Crop rapi */
    object-position: center;
    display: block;
}

/* Overlay info */
.portfolio-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 15px;
    background: linear-gradient(180deg, transparent, rgba(0,0,0,0.7));
    color: #fff;
}

.portfolio-info h4 {
    margin: 0 0 5px 0;
    font-size: 18px;
    font-weight: 600;
}

.portfolio-info p {
    margin: 0 0 10px 0;
    font-size: 14px;
}

.portfolio-info a i {
    font-size: 28px;
    color: #fff;
}
</style>
<!-- =================== END CSS FIX ===================== -->

<section id="portfolio" class="portfolio section">

  <div class="container section-title" data-aos="fade-up">
    <h2>Dokumentasi</h2>
  </div>

  <div class="container">

    <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

      <!-- FILTER -->
      <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
        <li data-filter="*" class="filter-active">All</li>
        <li data-filter=".filter-workshop">Workshop</li>
        <li data-filter=".filter-riset">Riset</li>
        <li data-filter=".filter-seminar">Seminar</li>
        <li data-filter=".filter-kunjungan">Kunjungan</li>
        <li data-filter=".filter-lomba">Lomba</li>
        <li data-filter=".filter-pengabdian">Pengabdian</li>
      </ul>

      <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">

        <?php 
        function mapFilter($jenis) {
            return "filter-" . strtolower($jenis);
        }

        foreach ($dokumentasi as $d): 
            $img = "../../../public/uploads/dokumentasi/" . $d['type_file'];
            $filterClass = mapFilter($d['jenis_kegiatan']);
        ?>

        <div class="col-lg-4 col-md-6 portfolio-item isotope-item <?= $filterClass ?>">
          <div class="portfolio-content h-100">

            <!-- FOTO -->
            <img src="<?= $img ?>" class="img-fluid" alt="<?= htmlspecialchars($d['jenis_kegiatan']) ?>">

            <div class="portfolio-info">

              <h4><?= htmlspecialchars($d['jenis_kegiatan']) ?></h4>
              <p><?= nl2br(htmlspecialchars($d['caption'])) ?></p>

              <a href="<?= $img ?>" 
                 title="<?= htmlspecialchars($d['jenis_kegiatan']) ?>" 
                 data-gallery="portfolio-gallery-<?= strtolower($d['jenis_kegiatan']) ?>" 
                 class="glightbox d-flex align-items-center justify-content-center">
                <i class="bi bi-zoom-in"></i>
              </a>

            </div>
          </div>
        </div>

        <?php endforeach; ?>

      </div>

    </div>

  </div>

</section>

<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>
