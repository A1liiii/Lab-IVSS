<div class="admin-content">

    <h2 class="mb-4">Data Publikasi</h2>

    <a href="/lab-ivss/index.php?page=operator-publikasi-add" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Tambah Publikasi
    </a>

    <div class="admin-card">

        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Status</th>
                    <th>Link</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $d): ?>

                        <tr>

                            <td><?= htmlspecialchars($d['judul']) ?></td>

                            <td><?= $d['tanggal_mulai'] ?: '-' ?></td>

                            <td><?= $d['tanggal_selesai'] ?: '-' ?></td>

                            <td>
                                <?php 
                                    $badge = "bg-secondary";
                                    if ($d['status'] == 'proses') $badge = "bg-warning";
                                    if ($d['status'] == 'publikasi') $badge = "bg-success";
                                ?>
                                <span class="badge <?= $badge ?>">
                                    <?= ucfirst($d['status']) ?>
                                </span>
                            </td>

                            <td>
                                <?php if (!empty($d['link'])): ?>
                                    <a href="<?= htmlspecialchars($d['link']) ?>" target="_blank">
                                        Buka
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="/lab-ivss/index.php?page=operator-publikasi-edit&id=<?= $d['publikasi_id'] ?>"
                                   class="btn btn-sm btn-warning">
                                   <i class="bi bi-pencil"></i> Edit
                                </a>

                                <a onclick="return confirm('Hapus publikasi ini?')"
                                   href="/lab-ivss/index.php?page=operator-publikasi-delete&id=<?= $d['publikasi_id'] ?>"
                                   class="btn btn-sm btn-danger">
                                   <i class="bi bi-trash"></i> Hapus
                                </a>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Belum ada data publikasi.
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>

    </div>

</div>
