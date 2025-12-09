<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= $title ?? 'IVSS' ?></title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <link href="../../../public/assets/img/logo-fix-putih.jpg" rel="icon">

  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@300;400;500;600;700&family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">

  <link href="../../../public/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../../public/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../../../public/assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="../../../public/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="../../../public/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <link href="../../../public/assets/css/main.css" rel="stylesheet">
</head>

<body class="index-page">

<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">
      <a href="../Public/home.php" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="../../../public/assets/img/logo-fix.png" alt="">
        <h1 class="sitename">IVSS</h1>
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="/lab-ivss/index.php?page=home" class="active">Beranda<br></a></li>
            <li class="dropdown"><a href="../Public/about.php"><span>Tentang Kami</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="#">Lab</a></li>
              <li><a href="#">Visi & Misi</a></li>
              <li class="dropdown"><a href="#"><span>Anggota Lab</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                  <li><a href="#">Dosen</a></li>
                  <li><a href="#">Mahasiswa</a></li>
                  <li><a href="#">Alumni</a></li>
                </ul>
              </li>
            </ul>
          </li>
         <li class="dropdown <?= ($active === 'riset') ? 'active' : '' ?>">
            <a href="riset.php"><span>Riset</span> <i class="bi bi-chevron-down"></i></a>
            <ul>
              <li><a href="proyek.php">Proyek</a></li>
              <li><a href="publikasi.php">Publikasi</a></li>
            </ul>
          </li>
          <li class="dropdown"><a href="#"><span>Fasilitas</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="#">Ruangan</a></li>
              <li class="dropdown"><a href="#"><span>Peralatan</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                  <li><a href="#">High-Speed Camera</a></li>
                  <li><a href="#">Lighting Tools</a></li>
                  <li><a href="#">Supporting Tools</a></li>
                </ul>
              </li>
            </ul>
          </li>
          <li class="dropdown"><a href="../Public/berita.php"><span>Berita</span></i></a>
          <li class="dropdown"><a href="../Public/dokumentasi.php"><span>Galeri</span></a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <!-- <a class="btn-getstarted flex-md-shrink-0" href="index.html#about">Get Started</a> -->

    </div>
  </header>

<main class="main">

    <!-- INI BAGIAN KONTEN DINAMIS -->
    <?= $content ?? '' ?>

</main>



<footer id="footer" class="footer">

  

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.html" class="d-flex align-items-center">
            <span class="sitename">Intelligence Vision and Smart System</span>
          </a>
          <div class="footer-contact pt-3">
            <p>A108 Adam Street</p>
            <p>New York, NY 535022</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+1 5589 55488 55</span></p>
            <p><strong>Email:</strong> <span>info@example.com</span></p>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Home</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">About us</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Services</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Terms of service</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Web Design</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Web Development</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Product Management</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Marketing</a></li>
          </ul>
        </div>

        <div class="col-lg-4 col-md-12">
          <h4>Follow Us</h4>
          <p>Cras fermentum odio eu feugiat lide par naso tierra videa magna derita valies</p>
          <div class="social-links d-flex">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">FlexStart</strong> <span>All Rights Reserved</span></p>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="../../../public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../../../public/assets/vendor/php-email-form/validate.js"></script>
  <script src="../../../public/assets/vendor/aos/aos.js"></script>
  <script src="../../../public/assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="../../../public/assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="../../../public/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="../../../public/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="../../../public/assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="../../../public/assets/js/main.js"></script>
</body>
</html>
