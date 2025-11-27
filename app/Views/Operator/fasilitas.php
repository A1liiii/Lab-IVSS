<div class="admin-content">

    <h2 class="mb-4">Data Fasilitas</h2>

    <a href="/lab-ivss/index.php?page=operator-fasilitas-add" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Tambah Fasilitas
    </a>

    <div class="admin-card">

        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th width="60">Foto</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>

                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $d): ?>
                        <tr>
                            <td>
                                <?php if (!empty($d['foto'])): ?>
                                    <img src="public/uploads/fasilitas/<?= $d['foto'] ?>"
                                         style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <td><?= $d['nama'] ?></td>
                            <td><?= ucfirst($d['kategori']) ?></td>
                            <td>
                                <span class="badge 
                                    <?= $d['status'] == 'tersedia' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ucfirst($d['status']) ?>
                                </span>
                            </td>

                            <td>
                                <a href="/lab-ivss/index.php?page=operator-fasilitas-edit&fasilitas_id=<?= $d['id'] ?>"
                                   class="btn btn-sm btn-warning">
                                   <i class="bi bi-pencil"></i> Edit
                                </a>

                                <a onclick="return confirm('Hapus fasilitas ini?')"
                                   href="/lab-ivss/index.php?page=operator-fasilitas-delete&id=<?= $d['id'] ?>"
                                   class="btn btn-sm btn-danger">
                                   <i class="bi bi-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>

                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Belum ada data fasilitas.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>
        </table>

    </div>

</div>

