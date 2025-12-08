<?php
$title = "Fasilitas | IVSS";
$active = "fasilitas";

require_once __DIR__ . "/../../core/database.php";
$db = Database::connect();

// Ambil kategori unik dari database
$qKategori = $db->query("SELECT DISTINCT kategori FROM fasilitas ORDER BY kategori ASC");
$kategoriList = $qKategori->fetchAll(PDO::FETCH_ASSOC);

// Ambil semua fasilitas
$qFasilitas = $db->query("SELECT * FROM fasilitas ORDER BY fasilitas_id DESC");
$fasilitas = $qFasilitas->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<!-- Portfolio Section -->
<section id="portfolio" class="portfolio section">

  <div class="container section-title" data-aos="fade-up">
    <h2>Fasilitas & Peralatan</h2>
    <p>Daftar fasilitas dan peralatan laboratorium IVSS</p>
  </div>

  <div class="container">

    <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

      <!-- FILTER -->
      <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
        <li data-filter="*" class="filter-active">All</li>
        <li data-filter=".filter-fasilitas">Fasilitas</li>
        <li data-filter=".filter-peralatan">Peralatan</li>

        <?php foreach ($kategoriList as $k): 
            if (!empty($k['kategori'])):
                $slug = strtolower(str_replace(' ', '-', $k['kategori']));
        ?>
          <li data-filter=".filter-<?= $slug ?>"><?= htmlspecialchars($k['kategori']) ?></li>
        <?php endif; endforeach; ?>
      </ul>

      <!-- ITEM LIST -->
      <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">

        <?php foreach ($fasilitas as $f): 
            $kategoriSlug = strtolower(str_replace(' ', '-', $f['kategori']));
        ?>

        <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-<?= $kategoriSlug ?>">

          <div class="portfolio-content">

            <!-- BADGE CATEGORY -->
            <span class="category-badge"><?= htmlspecialchars($f['kategori']) ?></span>

            <!-- IMAGE -->
            <img src="public/uploads/fasilitas/<?= htmlspecialchars($f['foto']) ?>" 
                 class="img-fluid" alt="">

            <!-- OVERLAY -->
            <div class="portfolio-info">
              <h4><?= htmlspecialchars($f['nama']) ?></h4>
              <p><?= htmlspecialchars($f['nama']) ?></p>

              <!-- ICON ZOOM (NO PENITI) -->
              <a href="public/uploads/fasilitas/<?= htmlspecialchars($f['foto']) ?>" 
                 class="glightbox preview-link" 
                 title="<?= htmlspecialchars($f['nama']) ?>">
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