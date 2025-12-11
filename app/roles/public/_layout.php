<?php 
require_once __DIR__ . "/../../core/database.php";

$conn = Database::connect(); // ini WAJIB

// Query 1 data lab
$sql = "SELECT * FROM lab_info LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute();

$lab = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lab) {
    $lab = []; // biar aman kalo kosong
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?= isset($title) ? $title : 'IVSS' ?></title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <link href="../../../public/assets/img/logo-fix-putih.jpg" rel="icon">

  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
      <a href="../public/home.php" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="../../../public/assets/img/logo_ivss2.png" alt="">
        <h1 class="sitename">IVSS</h1>
      </a>
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="../public/home.php" class="active">Beranda<br></a></li>
            <li class="dropdown"><a href="../public/about.php"><span>Tentang Kami</span></a>
          </li>
          <li class="dropdown"><a href="../public/riset.php"><span>Riset</span></a>
          </li>
          <li class="dropdown"><a href="../public/fasilitas.php"><span>Fasilitas</span></a>
          </li>
          <li class="dropdown"><a href="../public/berita.php"><span>Berita</span></a>
          </li>
          <li class="dropdown"><a href="../public/dokumentasi.php"><span>Galeri</span></a></li>
          <li><a href="../public/home.php#contact">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <!-- <a class="btn-getstarted flex-md-shrink-0" href="index.html#about">Get Started</a> -->

    </div>
  </header>

<main class="main">

    <!-- INI BAGIAN KONTEN DINAMIS -->
    <?= isset($content) ? $content : '' ?>

</main>



<footer id="footer" class="footer">

  

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
        <a href="public/home.php" class="d-flex align-items-center">
        <span class="sitename">IVSS</span>
        </a>
          <div class="footer-contact pt-3">
          <p>
            <?php 
            if (!empty($lab['alamat'])) {
                echo htmlspecialchars($lab['alamat']);
            } else {
                echo 'Belum ada alamat';
            }
            ?>
          </p>
          <p class="mt-3">
            <strong>Phone:</strong>
            <span>
                <?php 
                if (!empty($lab['no_telp'])) {
                    echo htmlspecialchars($lab['no_telp']);
                } else {
                    echo 'Belum ada nomor telepon';
                }
                ?>
            </span>
          </p>
          <p>
            <strong>Email:</strong>
            <span>
                <?php 
                if (!empty($lab['email'])) {
                    echo htmlspecialchars($lab['email']);
                } else {
                    echo 'Belum ada email';
                }
                ?>
            </span>
          </p>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><i class="bi bi-chevron-right"></i> <a href="public/home.php">Beranda</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="public/about.php">Tentang Kami</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="public/riset.php">Riset</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="public/fasilitas.php">Fasilitas</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><i class="bi bi-chevron-right"></i> <a href="public/berita.php">Berita</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="public/dokumentasi.php">Galeri</a></li>
            <li><i class="bi bi-chevron-right"></i> <a href="#">Kontak</a></li>
          </ul>
        </div>

        <div class="col-lg-4 col-md-12">
          <h4>Follow Us</h4>
          <p>Dapatkan pembaruan tentang penelitian, publikasi, dan program kolaborasi melalui kanal resmi kami.</p>
          <!-- <div class="social-links d-flex">
              <?php if (!empty($lab['instagram'])): ?>
                  <a href="<?= htmlspecialchars($lab['instagram']) ?>" target="_blank">
                      <i class="bi bi-instagram"></i>
                  </a>
              <?php endif; ?>
              <?php if (!empty($lab['youtube'])): ?>
                  <a href="<?= htmlspecialchars($lab['youtube']) ?>" target="_blank">
                      <i class="bi bi-youtube"></i>
                  </a>
              <?php endif; ?>
              <?php if (!empty($lab['tiktok'])): ?>
                  <a href="<?= htmlspecialchars($lab['tiktok']) ?>" target="_blank">
                      <i class="bi bi-tiktok"></i>
                  </a>
              <?php endif; ?>
          </div> -->
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Intelligence Vision and Smart System</strong> <span>All Rights Reserved</span></p>
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

  <!-- js scroolss navbar -->
  <script>
  window.addEventListener('scroll', function() {
    if (window.scrollY > 10) {
      document.body.classList.add('scrolled');
    } else {
      document.body.classList.remove('scrolled');
    }
  });
</script>
</body>
</html>
