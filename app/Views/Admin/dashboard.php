<div class="admin-content">

  <h2 class="mb-4">Dashboard</h2>

  <div class="row g-4">
    <div class="col-md-3">
      <div class="admin-card">
        <h5>Total Mahasiswa</h5>
        <h2><?php echo isset($totalMahasiswa) ? $totalMahasiswa : 0; ?></h2>
      </div>
    </div>

    <div class="col-md-3">
      <div class="admin-card">
        <h5>Total User</h5>
        <h2><?php echo isset($totalUser) ? $totalUser : 0; ?></h2>
      </div>
    </div>

    <div class="col-md-3">
      <div class="admin-card">
        <h5>Pending Registrations</h5>
        <h2><?php echo isset($pendingReg) ? $pendingReg : 0; ?></h2>
      </div>
    </div>

    <div class="col-md-3">
      <div class="admin-card">
        <h5>Total Semua Modul</h5>
        <h2><?php echo isset($totalSemuaModul) ? $totalSemuaModul : 0; ?></h2>
      </div>
    </div>
  </div>
  </ul>
</div>
</div>
