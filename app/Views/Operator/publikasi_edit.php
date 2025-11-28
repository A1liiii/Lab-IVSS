<div class="admin-content">

    <h2 class="mb-4">Edit Proyek</h2>

    <div class="admin-card p-4 shadow-sm" style="border-radius: 10px;">

        <!-- ======================= -->
        <!-- FORM EDIT PROYEK        -->
        <!-- ======================= -->
        <form action="" method="POST">
            <input type="hidden" name="proyek_id" value="<?= $proyek['id'] ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold">Judul Proyek</label>
                <input type="text" name="judul" class="form-control"
                       value="<?= htmlspecialchars($proyek['judul']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="4"><?= 
                    htmlspecialchars($proyek['deskripsi']) ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control"
                           value="<?= $proyek['tanggal_mulai'] ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control"
                           value="<?= $proyek['tanggal_selesai'] ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-control" required>
                    <option value="draft"   <?= $proyek['status']=='draft'?'selected':'' ?>>Draft</option>
                    <option value="proses"  <?= $proyek['status']=='proses'?'selected':'' ?>>Proses</option>
                    <option value="selesai" <?= $proyek['status']=='selesai'?'selected':'' ?>>Selesai</option>
                </select>
            </div>

            <div class="d-flex justify-content-end">
                <a href="index.php?page=operator-proyek" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>

                <button class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>

        <hr class="my-4">

        <!-- ======================= -->
        <!-- LIST ANGGOTA PROYEK     -->
        <!-- ======================= -->
        <h4 class="mb-3">Anggota Proyek</h4>

        <?php if (!empty($anggota)): ?>
            <ul class="list-group mb-3">
                <?php foreach ($anggota as $a): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($a['nama']) ?></strong><br>
                            <small class="text-muted"><?= ucfirst($a['role']) ?></small>
                        </div>

                        <a onclick="return confirm('Hapus anggota ini?')"
                           href="/lab-ivss/index.php?page=operator-proyek-anggota-delete
                                &id=<?= $a['id'] ?>&proyek_id=<?= $proyek['id'] ?>"
                           class="btn btn-sm btn-danger">
                            <i class="bi bi-trash"></i>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">Belum ada anggota.</p>
        <?php endif; ?>

        <hr class="my-4">

        <!-- ======================= -->
        <!-- FORM TAMBAH ANGGOTA     -->
        <!-- ======================= -->
        <h4 class="mb-3">Tambah Anggota</h4>

        <form action="" method="POST">
            <input type="hidden" name="add_anggota" value="1">
            <input type="hidden" name="proyek_id" value="<?= $proyek['id'] ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Pilih User</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">-- Pilih User --</option>

                        <?php foreach ($listUser as $u): ?>
                            <option value="<?= $u['user_id'] ?>">
                                <?= htmlspecialchars($u['nama']) ?> (<?= $u['email'] ?>)
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Peran</label>
                    <select name="role" class="form-control" required>
                        <option value="ketua">Ketua</option>
                        <option value="anggota">Anggota</option>
                        <option value="developer">Developer</option>
                        <option value="designer">Designer</option>
                    </select>
                </div>
            </div>

            <button class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Tambahkan Anggota
            </button>
        </form>

    </div>

</div>
