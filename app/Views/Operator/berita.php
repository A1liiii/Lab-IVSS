<div class="admin-content">

  <h2 class="mb-4">Manajemen Berita</h2>

  <a href="/lab-ivss/index.php?page=operator-berita-add" class="btn btn-primary mb-3">
    <i class="bi bi-plus-circle"></i> Tambah Berita
  </a>

  <div class="admin-card">
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Judul</th>
          <th>Kategori</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!empty($berita)) : ?>
        <?php foreach ($berita as $b): ?>
        <tr>
          <td><?= $b['berita_id'] ?></td>
          <td><?= $b['judul'] ?></td>
          <td><?= $b['kategori'] ?></td>
          <td><?= $b['tgl_post'] ?></td>
          <td>
            <a href="/lab-ivss/index.php?page=operator-berita-edit&id=<?= $b['berita_id'] ?>" class="btn btn-warning btn-sm">
              Edit
            </a>

            <a href="/lab-ivss/index.php?page=operator-berita-delete&id=<?= $b['berita_id'] ?>" 
              onclick="return confirm('Yakin ingin menghapus berita ini?')" class="btn btn-danger btn-sm">
              Hapus
            </a>
            
          </td>
        </tr>
        <?php endforeach; ?>
        <?php else : ?>
        <tr>
        <td colspan="6" class="text-center">Belum ada berita</td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>
