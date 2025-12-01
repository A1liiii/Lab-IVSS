<div class="admin-content">

    <h2 class="mb-4">Data Proyek</h2>

    <a href="/lab-ivss/index.php?page=operator-proyek-add" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Tambah Proyek
    </a>

    <div class="admin-card">

        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Jumlah Anggota</th>
                    <th>Daftar Anggota</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['judul']) ?></td>

                            <td>
                                <?= $p['tanggal_mulai'] ?> s/d <br>
                                <?= $p['tanggal_selesai'] ?>
                            </td>

                            <td>
                                <?php 
                                    $badge = "bg-secondary";
                                    if ($p['status'] == 'perencanaan') $badge = "bg-info";
                                    if ($p['status'] == 'berjalan') $badge = "bg-warning";
                                    if ($p['status'] == 'selesai') $badge = "bg-success";
                                ?>
                                <span class="badge <?= $badge ?>">
                                    <?= ucfirst($p['status']) ?>
                                </span>
                            </td>

                            <td><b><?= $p['jumlah_anggota'] ?></b> anggota</td>

                            <td>
                                <?php if (!empty($p['anggota'])): ?>
                                    <ul class="mb-0">
                                        <?php foreach ($p['anggota'] as $a): ?>
                                            <li><?= htmlspecialchars($a['nama']) ?> (<?= $a['role'] ?>)</li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <span class="text-muted">Tidak ada anggota</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="/lab-ivss/index.php?page=operator-proyek-edit&id=<?= $p['proyek_id'] ?>"
                                   class="btn btn-sm btn-warning">
                                   <i class="bi bi-pencil"></i> Edit
                                </a>

                                <a onclick="return confirm('Hapus proyek ini?')"
                                   href="/lab-ivss/index.php?page=operator-proyek-delete&id=<?= $p['proyek_id'] ?>"
                                   class="btn btn-sm btn-danger">
                                   <i class="bi bi-trash"></i> Hapus
                                </a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            Belum ada data proyek.
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>

    </div>

</div>
