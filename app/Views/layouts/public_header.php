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
          <li><a href="../Public/home.php" class="active">Beranda<br></a></li>
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
          <li class="dropdown"><a href="#"><span>Riset</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="../Public/proyek.php">Projek</a></li>
              <li><a href="../Public/publikasi.php">Publikasi</a></li>
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
          <li class="dropdown"><a href="../Public/berita.php"><span>Berita</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="#">Pengumuman</a></li>
              <li><a href="#">Penghargaan</a></li>
            </ul>
          </li>
          <li class="dropdown"><a href="../Public/dokumentasi.php"><span>Galeri</span></a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <!-- <a class="btn-getstarted flex-md-shrink-0" href="index.html#about">Get Started</a> -->

    </div>
  </header>

<main class="main">
