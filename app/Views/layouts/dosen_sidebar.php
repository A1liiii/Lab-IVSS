<div class="admin-sidebar" style="
  width:260px;
  background: var(--surface-color);
  border-right: 1px solid color-mix(in srgb, var(--default-color), transparent 85%);
  padding: 20px;
">

  <h3>IVSS Dosen</h3>

  <!-- ===== Profile ===== -->
  <a href="/lab-ivss/index.php?page=dosen-profile" 
     class="<?= (isset($active) && $active === 'profile') ? 'active' : '' ?>">
    <i class="bi bi-person-circle"></i> Profile
  </a>

  <!-- ===== Dashboard ===== -->
  <a href="/lab-ivss/index.php?page=dosen-dashboard" 
     class="<?= (isset($active) && $active === 'dashboard') ? 'active' : '' ?>">
    <i class="bi bi-speedometer2"></i> Dashboard
  </a>

  <!-- ===== Mahasiswa ===== -->
  <a href="/lab-ivss/index.php?page=dosen-mahasiswa" 
     class="<?= (isset($active) && $active === 'mahasiswa') ? 'active' : '' ?>">
    <i class="bi bi-mortarboard"></i> Mahasiswa
  </a>

  <!-- ===== Publikasi ===== -->
  <a href="/lab-ivss/index.php?page=dosen-publikasi" 
     class="<?= (isset($active) && $active === 'publikasi') ? 'active' : '' ?>">
    <i class="bi bi-journal-text"></i> Publikasi
  </a>

  <!-- ===== Log Aktivitas ===== -->
  <a href="/lab-ivss/index.php?page=dosen-logs" 
     class="<?= (isset($active) && $active === 'logs') ? 'active' : '' ?>">
    <i class="bi bi-clipboard-data"></i> Log Aktivitas
  </a>

  <!-- ===== Logout ===== -->
  <a href="/lab-ivss/public/logout.php" style="margin-top:20px;">
    <i class="bi bi-box-arrow-right"></i> Logout
  </a>

</div>
