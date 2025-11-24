<!DOCTYPE html>
<html>
<head>
</head>
<body>
  <!-- header -->
<?php include _DIR_ . '/../layouts/public_header.php'; ?>

<!-- Portfolio Section -->
<section id="portfolio" class="portfolio section">

<!-- Section Title -->
<div class="container section-title" data-aos="fade-up">
  <h2>Dokumentasi</h2>
  <p>Explore detailed documentation of our latest activities, research progress, and collaborative projects.</p>
</div><!-- End Section Title -->

<div class="container">

  <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

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

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/app-1.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Workshop 1</h4>
            <p>Workshop Pengolahan Citra Dasar yang diikuti oleh mahasiswa dan dosen. 
              Pada kegiatan ini peserta belajar teknik preprocessing, filtering, 
              dan segmentasi gambar menggunakan Python.</p>
            <a href="../../../public/assets/img/portfolio/app-1.jpg" title="Workshop 1" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/product-1.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Riset 1</h4>
            <p>Kegiatan riset sistem tracking objek 
              berbasis kamera. Pada sesi ini tim melakukan 
              pengujian akurasi dan kalibrasi alat.</p>
            <a href="../../../public/assets/img/portfolio/product-1.jpg" title="Riset 1" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/branding-1.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Seminar 1</h4>
            <p>Seminar ‘AI for Social Good’ yang 
              membahas pemanfaatan kecerdasan buatan untuk 
              bidang pendidikan dan kesehatan. Acara ini menghadirkan 
              pembicara dari kampus dan industri.</p>
            <a href="../../../public/assets/img/portfolio/branding-1.jpg" title="Seminar 1" data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-books">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/books-1.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Kunjungan 1</h4>
            <p>Kunjungan mahasiswa dari SMK Informatika 
              untuk mengenal alat dan riset yang 
              ada di Laboratorium Vision System</p>
            <a href="../../../public/assets/img/portfolio/books-1.jpg" title="Kunjungan 1" data-gallery="portfolio-gallery-book" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/app-2.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Lomba 1</h4>
            <p>Tim Vision Tech mengikuti Lomba Robotika Nasional 
              dan berhasil masuk 10 besar 
              kategori robot transportasi.</p>
            <a href="../../../public/assets/img/portfolio/app-2.jpg" title="Lomba 1" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/product-2.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Pengabdian 1</h4>
            <p>Kegiatan pengabdian berupa pelatihan editing video dasar untuk 
              guru SMA Muhammadiyah.</p>
            <a href="../../../public/assets/img/portfolio/product-2.jpg" title="Pengabdian 1" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/branding-2.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Workshop 2</h4>
            <p>Pelatihan Dasar Machine Learning untuk Mahasiswa Baru</p>
            <a href="../../../public/assets/img/portfolio/branding-2.jpg" title="Workshop 2" data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-books">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/books-2.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Riset 2</h4>
            <p>Proses kalibrasi menggunakan pola checkerboard untuk mendapatkan nilai intrinsic matrix.</p>
            <a href="../../../public/assets/img/portfolio/books-2.jpg" title="Riset 2" data-gallery="portfolio-gallery-book" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/app-3.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Seminar 2</h4>
            <p>Seminar Tren Keamanan Data dan AI di Tahun 2025</p>
            <a href="../../../public/assets/img/portfolio/app-3.jpg" title="Seminar 2" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/product-3.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Seminar 3</h4>
            <p>Seminar Dasar Sistem Embedded untuk Proyek IoT</p>
            <a href="../../../public/assets/img/portfolio/product-3.jpg" title="Seminar 3" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/branding-3.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Kunjungan 2</h4>
            <p>Kunjungan Industri ke PT XYZ Robotics</p>
            <a href="../../../public/assets/img/portfolio/branding-3.jpg" title="Kunjungan 2" data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

      <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-books">
        <div class="portfolio-content h-100">
          <img src="../../../public/assets/img/portfolio/books-3.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Lomba 2</h4>
            <p>Tim Lab IVSS Juara 2 Hackathon Kampus</p>
            <a href="../../../public/assets/img/portfolio/books-3.jpg" title="Lomba 3" data-gallery="portfolio-gallery-book" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.html" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </div><!-- End Portfolio Item -->

    </div><!-- End Portfolio Container -->

  </div>

</div>

</section>
<!-- /Portfolio Section -->

<?php include _DIR_ . '/../layouts/public_footer.php'; ?>
<!-- footer -->
</body>
</html>