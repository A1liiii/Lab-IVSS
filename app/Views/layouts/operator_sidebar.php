<div class="admin-sidebar" style="
  width:260px;
  background: var(--surface-color);
  border-right: 1px solid color-mix(in srgb, var(--default-color), transparent 85%);
  padding: 20px;">

  <h3>IVSS Operator</h3>

  <a href="/lab-ivss/index.php?page=operator-dashboard"
     class="<?= (isset($active) && $active === 'dashboard') ? 'active' : '' ?>">
    <i class="bi bi-speedometer2"></i> Dashboard
  </a>

  <a href="/lab-ivss/index.php?page=operator-berita"
     class="<?= (isset($active) && $active === 'berita') ? 'active' : '' ?>">
    <i class="bi bi-newspaper"></i> Berita
  </a>

  <a href="/lab-ivss/index.php?page=operator-dokumentasi"
     class="<?= (isset($active) && $active === 'dokumentasi') ? 'active' : '' ?>">
    <i class="bi bi-images"></i> Dokumentasi
  </a>

  <a href="/lab-ivss/index.php?page=operator-publikasi"
     class="<?= (isset($active) && $active === 'publikasi') ? 'active' : '' ?>">
    <i class="bi bi-journal-text"></i> Publikasi
  </a>

  <a href="/lab-ivss/index.php?page=operator-proyek"
     class="<?= (isset($active) && $active === 'proyek') ? 'active' : '' ?>">
    <i class="bi bi-folder2-open"></i> Proyek
  </a>

  <a href="/lab-ivss/index.php?page=operator-fasilitas"
     class="<?= (isset($active) && $active === 'fasilitas') ? 'active' : '' ?>">
    <i class="bi bi-building"></i> Fasilitas
  </a>

  <a href="/lab-ivss/public/logout.php" style="margin-top:20px;">
    <i class="bi bi-box-arrow-right"></i> Logout
  </a>

</div>
