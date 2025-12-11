<?php
$title = "Fasilitas | IVSS";
$active = "fasilitas";

require_once __DIR__ . "/../../core/database.php";
$db = Database::connect();

// Ambil semua fasilitas
$qFasilitas = $db->query("SELECT * FROM fasilitas ORDER BY fasilitas_id DESC");
$fasilitas = $qFasilitas->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>
<section id="portfolio" class="portfolio section">

  <div class="container section-title" data-aos="fade-up">
    <p>Fasilitas & Peralatan</p>
  </div>

  <div class="container">
    <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

      <!-- FILTER -->
      <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
        <li data-filter="*" class="filter-active">All</li>
        <li data-filter=".filter-fasilitas">Fasilitas</li>
        <li data-filter=".filter-peralatan">Peralatan</li>
      </ul>

      <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">

        <?php if (!empty($fasilitas)): ?>
          <?php foreach ($fasilitas as $f): 
            $kategoriSlug = strtolower(str_replace(' ', '-', $f['kategori']));
          ?>

          <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-<?= $kategoriSlug ?>">
            <div class="portfolio-content h-100">

              <!-- Badge kategori -->
              <span class="category-badge badge bg-primary position-absolute top-0 start-0 m-3">
                <?= htmlspecialchars($f['kategori']) ?>
              </span>

              <!-- Gambar -->
              <img src="/Lab-IVSS/public/uploads/fasilitas/<?= htmlspecialchars($f['foto']) ?>" 
                   class="img-fluid w-100"
                   style="height:300px; object-fit:cover;"
                   alt="<?= htmlspecialchars($f['nama']) ?>">

              <!-- Overlay -->
              <div class="portfolio-info">
                <h4><?= htmlspecialchars($f['nama']) ?></h4>

                <?php if (!empty($f['deskripsi'])): ?>
                  <p><?= htmlspecialchars(mb_substr($f['deskripsi'], 0, 100)) ?><?= mb_strlen($f['deskripsi']) > 100 ? '...' : '' ?></p>
                <?php endif; ?>

                <a href="/Lab-IVSS/public/uploads/fasilitas/<?= htmlspecialchars($f['foto']) ?>" 
                  class="glightbox d-flex align-items-center justify-content-center"
                  data-gallery="fasilitas-gallery"
                  title="<?= htmlspecialchars($f['nama']) ?>">
                  <i class="bi bi-zoom-in"></i>
                </a>
              </div>

            </div>
          </div>

          <?php endforeach; ?>

        <?php else: ?>
          <div class="col-12 text-center py-5">
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              Belum ada data fasilitas tersedia.
            </div>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </div>

</section>

<!-- CUSTOM CSS -->
<style>
/* Styling card */
.portfolio-content {
  position: relative;
  overflow: hidden;
  border-radius: 12px;
  background: #fff;
  transition: 0.3s;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.portfolio-content:hover {
  transform: translateY(-8px);
  box-shadow: 0 10px 24px rgba(0,0,0,0.18);
}
.portfolio-content img {
  transition: 0.5s;
}
.portfolio-content:hover img {
  transform: scale(1.1);
}

/* Overlay */
.portfolio-info {
  position: absolute;
  bottom: 0;
  width: 100%;
  padding: 25px 18px;
  background: linear-gradient(to top, rgba(0,0,0,0.9), rgba(0,0,0,0.3), transparent);
  transform: translateY(calc(100% - 55px));
  transition: 0.3s;
  color: white;
}
.portfolio-content:hover .portfolio-info {
  transform: translateY(0);
}

/* Badge */
.category-badge {
  font-size: 11px;
  padding: 6px 14px;
  border-radius: 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

/* Filter */
.portfolio-filters li {
  cursor: pointer;
  padding: 10px 24px;
  border-radius: 25px;
  font-weight: 600;
  background: #f8f9fa;
  transition: 0.3s;
}
.portfolio-filters li.filter-active {
  background: #ffc107;
  color: #fff;
  box-shadow: 0 4px 14px rgba(255,193,7,0.4);
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>
