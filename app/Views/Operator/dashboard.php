<div class="admin-content">

  <h2 class="mb-4">Dashboard Operator</h2>

  <!-- CARD STATISTIK -->
  <div class="row g-4 mb-4">
    <div class="col-md-4 col-lg-3">
      <div class="admin-card">
        <h5>Total Berita</h5>
        <h2><?= isset($stats['berita']) ? $stats['berita'] : 0 ?></h2>
      </div>
    </div>

    <div class="col-md-4 col-lg-3">
      <div class="admin-card">
        <h5>Total Dokumentasi</h5>
        <h2><?= isset($stats['dokumentasi']) ? $stats['dokumentasi'] : 0 ?></h2>
      </div>
    </div>

    <div class="col-md-4 col-lg-3">
      <div class="admin-card">
        <h5>Total Publikasi</h5>
        <h2><?= isset($stats['publikasi']) ? $stats['publikasi'] : 0 ?></h2>
      </div>
    </div>

    <div class="col-md-4 col-lg-3">
      <div class="admin-card">
        <h5>Total Proyek</h5>
        <h2><?= isset($stats['proyek']) ? $stats['proyek'] : 0 ?></h2>
      </div>
    </div>

    <div class="col-md-4 col-lg-3">
      <div class="admin-card">
        <h5>Total Fasilitas</h5>
        <h2><?= isset($stats['fasilitas']) ? $stats['fasilitas'] : 0 ?></h2>
      </div>
    </div>
  </div>

  <!-- SHORTCUT -->
  <h4 class="mb-3">Shortcut Aksi Cepat</h4>
  <div class="row g-4 mb-4">
    <div class="col-md-4 col-lg-2">
      <a href="/lab-ivss/index.php?page=operator-berita-add" class="shortcut-box">
        <i class="bi bi-newspaper" style="font-size:40px;"></i>
        <p class="mt-2">Tambah Berita</p>
      </a>
    </div>

    <div class="col-md-4 col-lg-2">
      <a href="/lab-ivss/index.php?page=operator-dokumentasi-add" class="shortcut-box">
        <i class="bi bi-images" style="font-size:40px;"></i>
        <p class="mt-2">Tambah Dokumentasi</p>
      </a>
    </div>

    <div class="col-md-4 col-lg-2">
      <a href="/lab-ivss/index.php?page=operator-publikasi-add" class="shortcut-box">
        <i class="bi bi-journal-text" style="font-size:40px;"></i>
        <p class="mt-2">Tambah Publikasi</p>
      </a>
    </div>

    <div class="col-md-4 col-lg-2">
      <a href="/lab-ivss/index.php?page=operator-proyek-add" class="shortcut-box">
        <i class="bi bi-folder2-open" style="font-size:40px;"></i>
        <p class="mt-2">Tambah Proyek</p>
      </a>
    </div>

    <div class="col-md-4 col-lg-2">
      <a href="/lab-ivss/index.php?page=operator-fasilitas-add" class="shortcut-box">
        <i class="bi bi-building" style="font-size:40px;"></i>
        <p class="mt-2">Tambah Fasilitas</p>
      </a>
    </div>
  </div>

</div>
