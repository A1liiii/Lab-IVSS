<div class="admin-content">
  <h2 class="mb-4">Log Activity</h2>

  <div class="admin-card">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>User</th>
          <th>Aksi</th>
          <th>Deskripsi</th>
          <th>Waktu</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($logs)): ?>
          <?php foreach ($logs as $row): ?>
            <tr>
              <td><?= isset($row['username']) ? $row['username'] : 'Unknown' ?></td>
              <td><?= $row['aksi'] ?></td>
              <td><?= $row['deskripsi'] ?></td>
              <td><?= $row['waktu'] ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" class="text-center">Tidak ada aktivitas.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
