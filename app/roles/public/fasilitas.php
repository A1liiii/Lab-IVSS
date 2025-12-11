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

<!-- Portfolio Section -->
<section id="portfolio" class="portfolio section" style="padding-top: 100px;">

  <div class="container section-title" data-aos="fade-up">
    <h2>Fasilitas & Peralatan</h2>
    <p>Daftar fasilitas dan peralatan laboratorium IVSS</p>
  </div>

  <div class="container">

    <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

      <!-- FILTER BUTTONS -->
      <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
        <li data-filter="*" class="filter-active">Semua</li>
        <li data-filter=".filter-fasilitas">Fasilitas</li>
        <li data-filter=".filter-peralatan">Peralatan</li>
      </ul>

      <!-- ITEM LIST -->
      <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">

        <?php if (!empty($fasilitas)): ?>
          <?php foreach ($fasilitas as $f): 
              $kategoriSlug = strtolower(str_replace(' ', '-', $f['kategori']));
          ?>

          <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-<?= $kategoriSlug ?>">
            <div class="portfolio-content h-100">

              <!-- BADGE KATEGORI (KOTAK BIRU DI GAMBAR) -->
              <span class="category-badge-overlay">
                <?= htmlspecialchars($f['kategori']) ?>
              </span>

              <!-- IMAGE -->
              <img src="/Lab-IVSS/public/uploads/fasilitas/<?= htmlspecialchars($f['foto']) ?>" 
                   class="img-fluid w-100" 
                   alt="<?= htmlspecialchars($f['nama']) ?>"
                   style="height: 300px; object-fit: cover;">

              <!-- OVERLAY INFO -->
              <div class="portfolio-info">
                <?php if (!empty($f['deskripsi'])): ?>
                  <p><?= htmlspecialchars(mb_substr($f['deskripsi'], 0, 100)) ?><?= mb_strlen($f['deskripsi']) > 100 ? '...' : '' ?></p>
                <?php endif; ?>

                <!-- ICON ZOOM -->
                <a href="/Lab-IVSS/public/uploads/fasilitas/<?= htmlspecialchars($f['foto']) ?>" 
                   class="glightbox preview-link" 
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
              Belum ada data fasilitas tersedia
            </div>
          </div>
        <?php endif; ?>

      </div>

    </div>

  </div>

</section>

<!-- Custom CSS -->
<style>
/* ==================== PORTFOLIO CONTENT ==================== */
.portfolio-content {
  position: relative;
  overflow: hidden;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  background: #fff;
}

.portfolio-content:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}

.portfolio-content img {
  transition: transform 0.5s ease;
  display: block;
}

.portfolio-content:hover img {
  transform: scale(1.08);
}

/* ==================== BADGE KATEGORI (KOTAK BIRU DI GAMBAR) ==================== */
.category-badge-overlay {
  position: absolute;
  top: 15px;
  left: 15px;
  z-index: 10;
  background: #0d6efd !important;
  color: white !important;
  font-size: 12px;
  font-weight: 700;
  padding: 8px 18px;
  border-radius: 25px;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
  transition: transform 0.3s ease;
  border: none !important;
  outline: none !important;
}

.category-badge-overlay::before,
.category-badge-overlay::after {
  display: none !important;
}

.portfolio-content:hover .category-badge-overlay {
  transform: scale(1.05);
}

/* ==================== OVERLAY INFO ==================== */
.portfolio-info {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.75) 60%, transparent 100%);
  padding: 30px 20px 20px;
  transform: translateY(calc(100% - 80px));
  transition: transform 0.3s ease;
  color: white;
  z-index: 5;
}

.portfolio-content:hover .portfolio-info {
  transform: translateY(0);
}

.portfolio-info h4 {
  color: white;
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 10px;
  line-height: 1.4;
}

.portfolio-info p {
  color: rgba(255,255,255,0.9);
  font-size: 14px;
  margin-bottom: 15px;
  line-height: 1.6;
  display: none;
}

.portfolio-content:hover .portfolio-info p {
  display: block;
}

/* ==================== ZOOM BUTTON ==================== */
.preview-link {
  background: white !important;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: inline-flex !important;
  align-items: center;
  justify-content: center;
  color: #0d6efd !important;
  transition: all 0.3s ease;
  text-decoration: none;
  position: relative;
  z-index: 10;
  opacity: 1 !important;
  visibility: visible !important;
}

.preview-link:hover {
  background: #0d6efd !important;
  color: white !important;
  transform: scale(1.15) rotate(10deg);
  box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
}

.preview-link i {
  font-size: 22px;
  line-height: 1;
}

/* ==================== FILTER BUTTONS (DI ATAS) ==================== */
.portfolio-filters {
  list-style: none;
  padding: 0;
  display: flex;
  justify-content: center;
  gap: 15px;
  flex-wrap: wrap;
  margin-bottom: 50px;
}

.portfolio-filters li {
  cursor: pointer;
  padding: 12px 28px;
  border-radius: 30px;
  background: #f8f9fa;
  transition: all 0.3s ease;
  font-size: 14px;
  font-weight: 600;
  color: #495057;
  border: 2px solid #e9ecef;
}

.portfolio-filters li:hover {
  background: #ffe69c;
  color: #212529;
  border-color: #ffc107;
  transform: translateY(-2px);
}

.portfolio-filters li.filter-active {
  background: #ffc107;
  color: #212529;
  border-color: #ffc107;
  box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
}

/* ==================== SECTION TITLE ==================== */
.section-title {
  margin-top: 80px;
  padding-top: 40px;
}

.section-title h2 {
  font-size: 32px;
  font-weight: 700;
  color: #212529;
  margin-bottom: 10px;
}

.section-title p {
  color: #6c757d;
  font-size: 16px;
}

/* ==================== STYLE UNTUK TITLE GAMBAR LIGHTBOX ==================== */
.glightbox-container .gslide-title,
.glightbox-container .gslide-desc {
  background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
  color: white !important;
  padding: 15px 25px !important;
  font-size: 18px !important;
  font-weight: 600 !important;
  text-align: left !important;
  border-radius: 0 !important;
  letter-spacing: 0.3px;
  text-shadow: 0 2px 8px rgba(0,0,0,0.3);
  position: absolute !important;
  bottom: 0 !important;
  left: 0 !important;
  right: 0 !important;
  width: 100% !important;
  z-index: 999 !important;
  overflow: hidden;
  margin: 0 !important;
}

.glightbox-container .gslide-title::before {
  content: "";
  margin-right: 0;
  font-size: 0;
  animation: none;
}

.glightbox-container .gslide-title::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: linear-gradient(90deg, #ffc107, #ffeb3b, #ffc107);
  animation: slideGradient 3s ease infinite;
}

@keyframes bounceIcon {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-5px); }
}

@keyframes slideGradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

/* ==================== ALTERNATIF: STYLE MODERN MINIMALIS ==================== */
.glightbox-container .gslide-title.modern {
  background: rgba(255, 255, 255, 0.95) !important;
  backdrop-filter: blur(10px);
  color: #212529 !important;
  padding: 18px 25px !important;
  font-size: 18px !important;
  font-weight: 600 !important;
  border-top: 3px solid #0d6efd;
  box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
}

.glightbox-container .gslide-title.modern::before {
  content: "●";
  color: #0d6efd;
  margin-right: 10px;
  font-size: 14px;
  animation: pulse 2s infinite;
}

/* ==================== ALTERNATIF: STYLE GRADIENT RAINBOW ==================== */
.glightbox-container .gslide-title.gradient {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%) !important;
  color: white !important;
  padding: 22px 30px !important;
  font-size: 22px !important;
  font-weight: 800 !important;
  text-transform: uppercase;
  letter-spacing: 2px;
  position: relative;
}

.glightbox-container .gslide-title.gradient::before {
  content: "✨";
  margin-right: 15px;
  font-size: 28px;
  filter: drop-shadow(0 0 10px rgba(255,255,255,0.8));
}

/* ==================== STYLE NAMA FASILITAS DI CARD ==================== */
.portfolio-info h4 {
  color: white;
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 10px;
  line-height: 1.4;
  padding: 12px 15px;
  background: linear-gradient(135deg, rgba(13, 110, 253, 0.9), rgba(10, 88, 202, 0.9));
  border-radius: 8px;
  border-left: 4px solid #ffc107;
  box-shadow: 0 4px 15px rgba(0,0,0,0.3);
  display: flex;
  align-items: center;
  gap: 10px;
}

.portfolio-info h4::before {
  content: "🔧";
  font-size: 20px;
  animation: rotate 3s linear infinite;
}

@keyframes rotate {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.portfolio-content:hover .portfolio-info h4 {
  background: linear-gradient(135deg, #ffc107, #ffb800);
  color: #212529;
  transform: translateX(5px);
  transition: all 0.3s ease;
}

/* ==================== RESPONSIVE ==================== */
@media (max-width: 768px) {
  .portfolio-content img {
    height: 250px !important;
  }
  
  .portfolio-info {
    transform: translateY(calc(100% - 70px));
  }
  
  .portfolio-info h4 {
    font-size: 16px;
    padding: 10px 12px;
  }
  
  .portfolio-info h4::before {
    font-size: 18px;
  }
  
  .portfolio-info p {
    font-size: 13px;
  }
  
  .category-badge-overlay {
    font-size: 10px;
    padding: 6px 14px;
    top: 10px;
    left: 10px;
  }
  
  .portfolio-filters li {
    padding: 10px 20px;
    font-size: 13px;
  }
  
  .preview-link {
    width: 42px;
    height: 42px;
  }
  
  .preview-link i {
    font-size: 20px;
  }
  
  .glightbox-container .gslide-title {
    font-size: 16px !important;
    padding: 15px 20px !important;
  }
}

@media (max-width: 576px) {
  .section-title h2 {
    font-size: 26px;
  }
  
  .section-title p {
    font-size: 14px;
  }
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>