<div class="admin-content">

  <h2 class="mb-4">Dashboard</h2>

  <div class="row g-4">
    <div class="col-md-3">
      <div class="admin-card">
        <h5>Total Mahasiswa</h5>
        <h2><?= $totalMahasiswa ?></h2>
      </div>
    </div>

    <div class="col-md-3">
      <div class="admin-card">
        <h5>Total User</h5>
        <h2><?= $totalUser ?></h2>
      </div>
    </div>

    <div class="col-md-3">
      <div class="admin-card">
        <h5>Pending Registrations</h5>
        <h2><?= $pendingReg ?></h2>
      </div>
    </div>

    <div class="col-md-3">
      <div class="admin-card">
        <h5>Total Dokumentasi</h5>
        <h2><?= $totalDokumentasi ?></h2>
      </div>
    </div>
  </div>

  <br>

  <div class="admin-card">
    <h4>Recent Activity</h4>
    <ul>
      <?php foreach ($recentActivity as $row): ?>
      <li><?= $row['activity'] ?> - <small><?= $row['created_at'] ?></small></li>
      <?php endforeach; ?>
    </ul>
  </div>

</div>
