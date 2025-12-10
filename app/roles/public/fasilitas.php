<?php
<<<<<<< HEAD
$title = "Tentang Kami | IVSS";
$active = "about";
=======
$title = "Fasilitas | IVSS";
$active = "fasilitas";

require_once __DIR__ . "/../../core/database.php";
$db = Database::connect();

// Ambil semua fasilitas
$qFasilitas = $db->query("SELECT * FROM fasilitas ORDER BY fasilitas_id DESC");
$fasilitas = $qFasilitas->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>
>>>>>>> bb5a2ce (doneFasillitas)

ob_start(); ?>
<!-- Portfolio Section -->
<section id="portfolio" class="portfolio section">

<!-- Section Title -->
<div class="container section-title" data-aos="fade-up">
  <h2>Dokumentasi</h2>
  <p>Explore detailed documentation of our latest activities, research progress, and collaborative projects.</p>
</div><!-- End Section Title -->

<div class="container">

  <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

<<<<<<< HEAD
    <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
      <li data-filter="*" class="filter-active">All</li>
      <li data-filter=".filter-workshop">Workshop</li>
      <li data-filter=".filter-riset">Riset</li>
      <li data-filter=".filter-seminar">Seminar</li>
      <li data-filter=".filter-kunjungan">Kunjungan</li>
      <li data-filter=".filter-lomba">Lomba</li>
      <li data-filter=".filter-pengabdian">Pengabdian</li>
    </ul><!-- End Portfolio Filters -->

    <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">
=======
      <!-- FILTER -->
      <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
        <li data-filter="*" class="filter-active">All</li>
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

              
              <!-- BADGE CATEGORY -->
              
            <span class="category-badge badge bg-primary position-absolute top-0 start-0 m-3" style="z-index: 10;">
            <?= htmlspecialchars($f['kategori']) ?>
            </span>

              

              <!-- IMAGE -->
              <img src="/Lab-IVSS/public/uploads/fasilitas/<?= htmlspecialchars($f['foto']) ?>" 
              class="img-fluid w-100" 
              alt="<?= htmlspecialchars($f['nama']) ?>"
              style="height: 300px; object-fit: cover;">

              <!-- OVERLAY -->
              <div class="portfolio-info">
                <h4><?= htmlspecialchars($f['nama']) ?></h4>
                
                <?php if (!empty($f['deskripsi'])): ?>
                  <p><?= htmlspecialchars(mb_substr($f['deskripsi'], 0, 100)) ?><?= mb_strlen($f['deskripsi']) > 100 ? '...' : '' ?></p>
                <?php endif; ?>

                <!-- ICON ZOOM -->
                <a href="public/uploads/fasilitas/<?= htmlspecialchars($f['foto']) ?>" 
                   class="glightbox preview-link" 
                   data-gallery="fasilitas-gallery"
                   title="<?= htmlspecialchars($f['nama']) ?>">
                  <i class="bi bi-zoom-in"></i>
                </a>
              </div>

            </div>
>>>>>>> bb5a2ce (doneFasillitas)

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-workshop">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/workshop-1.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Workshop 1</h4>
            <p>Workshop Pengolahan Citra Dasar yang diikuti oleh mahasiswa dan dosen. 
              Pada kegiatan ini peserta belajar teknik preprocessing, filtering, 
              dan segmentasi gambar menggunakan Python.</p>
            <a href="../../../public/assets/img/portfolio/workshop-1.jpg" title="Workshop 1" data-gallery="portfolio-gallery-workshop" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
<<<<<<< HEAD
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-riset">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/riset-1.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Riset 1</h4>
            <p>Kegiatan riset sistem tracking objek 
              berbasis kamera. Pada sesi ini tim melakukan 
              pengujian akurasi dan kalibrasi alat.</p>
            <a href="../../../public/assets/img/portfolio/riset-1.jpg" title="Riset 1" data-gallery="portfolio-gallery-riset" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->
=======

          <?php endforeach; ?>
        
        <?php else: ?>
          <div class="col-12 text-center py-5">
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              Belum ada data fasilitas tersedia
            </div>
          </div>
        <?php endif; ?>
>>>>>>> bb5a2ce (doneFasillitas)

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-seminar">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/seminar-1.jpeg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Seminar 1</h4>
            <p>Seminar ‘AI for Social Good’ yang 
              membahas pemanfaatan kecerdasan buatan untuk 
              bidang pendidikan dan kesehatan. Acara ini menghadirkan 
              pembicara dari kampus dan industri.</p>
            <a href="../../../public/assets/img/portfolio/seminar-1.jpeg" title="Seminar 1" data-gallery="portfolio-gallery-seminar" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-kunjungan">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/kunjungan-1.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Kunjungan 1</h4>
            <p>Kunjungan mahasiswa dari SMK Informatika 
              untuk mengenal alat dan riset yang 
              ada di Laboratorium Vision System</p>
            <a href="../../../public/assets/img/portfolio/kunjungan-1.jpg" title="Kunjungan 1" data-gallery="portfolio-gallery-kunjungan" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-lomba">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/lomba-1.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Lomba 1</h4>
            <p>Tim Vision Tech mengikuti Lomba Robotika Nasional 
              dan berhasil masuk 10 besar 
              kategori robot transportasi.</p>
            <a href="../../../public/assets/img/portfolio/lomba-1.jpg" title="Lomba 1" data-gallery="portfolio-gallery-lomba" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-pengabdian">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/pengabdian-1.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Pengabdian 1</h4>
            <p>Kegiatan pengabdian berupa pelatihan editing video dasar untuk 
              guru SMA Muhammadiyah.</p>
            <a href="../../../public/assets/img/portfolio/pengabdian-1.jpg" title="Pengabdian 1" data-gallery="portfolio-gallery-pengabdian" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-workshop">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/workshop-2.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Workshop 2</h4>
            <p>Pelatihan Dasar Machine Learning untuk Mahasiswa Baru</p>
            <a href="../../../public/assets/img/portfolio/workshop-2.jpg" title="Workshop 2" data-gallery="portfolio-gallery-workshop" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-riset">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/riset-2.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Riset 2</h4>
            <p>Proses kalibrasi menggunakan pola checkerboard untuk mendapatkan nilai intrinsic matrix.</p>
            <a href="../../../public/assets/img/portfolio/riset-2.jpg" title="Riset 2" data-gallery="portfolio-gallery-riset" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-seminar">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/seminar-2.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Seminar 2</h4>
            <p>Seminar Tren Keamanan Data dan AI di Tahun 2025</p>
            <a href="../../../public/assets/img/portfolio/seminar-2.jpg" title="Seminar 2" data-gallery="portfolio-gallery-seminar" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-seminar">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/seminar-3.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Seminar 3</h4>
            <p>Seminar Dasar Sistem Embedded untuk Proyek IoT</p>
            <a href="../../../public/assets/img/portfolio/seminar-3.jpg" title="Seminar 3" data-gallery="portfolio-gallery-seminar" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-kunjungan">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/kunjungan-2.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Kunjungan 2</h4>
            <p>Kunjungan Industri ke PT XYZ Robotics</p>
            <a href="../../../public/assets/img/portfolio/kunjungan-2.jpg" title="Kunjungan 2" data-gallery="portfolio-gallery-kunjungan" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-lomba">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/lomba-2.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Lomba 2</h4>
            <p>Tim Lab IVSS Juara 2 Hackathon Kampus</p>
            <a href="../../../public/assets/img/portfolio/lomba-2.jpg" title="Lomba 3" data-gallery="portfolio-gallery-lomba" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

    </div><!-- End Portfolio Container -->

  </div>

</div>

</section>
<<<<<<< HEAD
<!-- /Portfolio Section -->
=======

<!-- Custom CSS -->
<style>
.portfolio-content {
  position: relative;
  overflow: hidden;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  background: #fff;
}

.portfolio-content:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}

.portfolio-content img {
  transition: transform 0.5s ease;
  display: block;
}

.portfolio-content:hover img {
  transform: scale(1.1);
}

.portfolio-info {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.7) 70%, transparent 100%);
  padding: 30px 20px 20px;
  transform: translateY(calc(100% - 60px));
  transition: transform 0.3s ease;
  color: white;
}

.portfolio-content:hover .portfolio-info {
  transform: translateY(0);
}

.portfolio-info h4 {
  color: white;
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 8px;
  line-height: 1.4;
}

.portfolio-info p {
  color: rgba(255,255,255,0.9);
  font-size: 14px;
  margin-bottom: 15px;
  line-height: 1.6;
}

.preview-link {
  background: white;
  width: 45px;
  height: 45px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: #333;
  transition: all 0.3s ease;
  text-decoration: none;
}

.preview-link:hover {
  background: #ffc107;
  color: white;
  transform: scale(1.15) rotate(5deg);
}

.preview-link i {
  font-size: 20px;
}

.category-badge {
  font-size: 11px;
  font-weight: 600;
  padding: 6px 16px;
  border-radius: 20px;
  text-transform: capitalize;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.portfolio-filters {
  list-style: none;
  padding: 0;
  display: flex;
  justify-content: center;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 50px;
}

.portfolio-filters li {
  cursor: pointer;
  padding: 10px 24px;
  border-radius: 25px;
  background: #f8f9fa;
  transition: all 0.3s ease;
  font-size: 14px;
  font-weight: 600;
  color: #666;
  border: 2px solid transparent;
}

.portfolio-filters li:hover {
  background: #e9ecef;
  color: #333;
  border-color: #dee2e6;
}

.portfolio-filters li.filter-active {
  background: #ffc107;
  color: white;
  border-color: #ffc107;
  box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
}

/* Responsive */
@media (max-width: 768px) {
  .portfolio-content img {
    height: 250px !important;
  }
  
  .portfolio-info {
    transform: translateY(calc(100% - 50px));
  }
  
  .portfolio-info h4 {
    font-size: 16px;
  }
  
  .portfolio-info p {
    font-size: 13px;
  }
}
</style>

>>>>>>> bb5a2ce (doneFasillitas)
<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>
