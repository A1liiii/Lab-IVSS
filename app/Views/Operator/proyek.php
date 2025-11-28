<div class="admin-content">

    <h2 class="mb-4">Daftar Proyek</h2>

    <a href="/lab-ivss/index.php?page=operator-proyek-add" 
       class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Tambah Proyek
    </a>

    <?php if (!empty($data)): ?>
        <?php foreach ($data as $p): ?>

        <div class="admin-card p-4 mb-4 shadow-sm" style="border-radius: 10px;">

            <!-- INFO PROYEK -->
            <div class="d-flex justify-content-between">
                <h4><?= htmlspecialchars($p['judul']) ?></h4>
                <span class="badge 
                    <?= $p['status']=='selesai' ? 'bg-success' : 
                       ($p['status']=='proses'?'bg-warning':'bg-secondary') ?>">
                    <?= ucfirst($p['status']) ?>
                </span>
            </div>

            <p class="text-muted small">
                <?= $p['tanggal_mulai'] ?: '-' ?> — <?= $p['tanggal_selesai'] ?: '-' ?>
            </p>

            <p><?= nl2br(htmlspecialchars($p['deskripsi'])) ?></p>

            <hr>

            <!-- LIST ANGGOTA -->
            <h5 class="mb-3">Anggota Proyek</h5>

            <?php if (!empty($p['anggota'])): ?>
                <ul class="list-group mb-3">
                    <?php foreach ($p['anggota'] as $a): ?>
                        <li class="list-group-item">
                            <?= htmlspecialchars($a['username']) ?> 
                            (<?= htmlspecialchars($a['identitas']) ?>)
                        <br>
                        <small class="text-muted">Peran: <?= ucfirst($a['role']) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">Tidak ada anggota.</p>
            <?php endif; ?>

            <div class="d-flex justify-content-end mt-3">
                <a href="/lab-ivss/index.php?page=operator-proyek-edit&proyek_id=<?= $p['id'] ?>"
                   class="btn btn-warning me-2">
                    <i class="bi bi-pencil"></i> Edit
                </a>

                <a onclick="return confirm('Hapus proyek ini?')"
                   href="/lab-ivss/index.php?page=operator-proyek-delete&proyek_id=<?= $p['id'] ?>"
                   class="btn btn-danger">
                    <i class="bi bi-trash"></i> Hapus
                </a>
            </div>

        </div>

        <?php endforeach; ?>

    <?php else: ?>
        <p class="text-muted text-center">Belum ada proyek.</p>
    <?php endif; ?>

</div>
