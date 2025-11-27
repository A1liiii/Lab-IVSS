<div class="admin-content">

    <h2 class="mb-4">Edit Fasilitas</h2>

    <div class="admin-card">
        <form action="" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="foto_lama" value="<?= $data['foto'] ?>">

            <div class="mb-3">
                <label class="form-label">Nama Fasilitas</label>
                <input type="text" name="nama" class="form-control"
                       value="<?= $data['nama'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="4"><?= $data['deskripsi'] ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="tersedia"        <?= $data['status']=='tersedia'?'selected':'' ?>>Tersedia</option>
                    <option value="tidak_tersedia"  <?= $data['status']=='tidak_tersedia'?'selected':'' ?>>Tidak Tersedia</option>
                    <option value="perbaikan"       <?= $data['status']=='perbaikan'?'selected':'' ?>>Perbaikan</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-control" required>
                    <option value="fasilitas" <?= $data['kategori']=='fasilitas'?'selected':'' ?>>Fasilitas</option>
                    <option value="peralatan" <?= $data['kategori']=='peralatan'?'selected':'' ?>>Peralatan</option>
                    <option value="ruangan"   <?= $data['kategori']=='ruangan'?'selected':'' ?>>Ruangan</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Foto Baru (opsional)</label>
                <input type="file" name="foto" class="form-control">

                <?php if (!empty($data['foto'])): ?>
                    <img src="public/uploads/fasilitas/<?= $data['foto'] ?>"
                         style="width:90px;height:90px;object-fit:cover;margin-top:10px;border-radius:4px;">
                <?php endif; ?>
            </div>

            <button class="btn btn-primary" type="submit">
                <i class="bi bi-save"></i> Simpan Perubahan
            </button>

            <a href="index.php?page=operator-fasilitas" class="btn btn-secondary">
                Kembali
            </a>

        </form>
    </div>
</div>
