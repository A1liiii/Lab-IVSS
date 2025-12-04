<div class="admin-content">

    <h2 class="mb-4">Edit Berita</h2>

    <div class="admin-card">

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label>Judul</label>
                <input type="text" name="judul" value="<?= $data['judul'] ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="5" class="form-control" required><?= $data['deskripsi'] ?></textarea>
            </div>

            <div class="mb-3">
                <label>Kategori</label>
                <select name="kategori" class="form-control" required>
                    <option value="berita"     <?= $data['kategori']=='berita' ? 'selected':'' ?>>Berita</option>
                    <option value="pengumuman" <?= $data['kategori']=='pengumuman' ? 'selected':'' ?>>Pengumuman</option>
                    <option value="aktivitas"  <?= $data['kategori']=='aktivitas' ? 'selected':'' ?>>Aktivitas</option>
                    <option value="lainnya"     <?= $data['kategori']=='lainnya' ? 'selected':'' ?>>Lainnya</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Foto Saat Ini</label><br>
                <?php if ($data['foto']): ?>
                    <img src="public/uploads/berita/<?= $data['foto'] ?>" width="120" class="mb-2">
                <?php else: ?>
                    <p>Tidak ada foto</p>
                <?php endif; ?>
                <input type="file" name="foto" class="form-control mt-2">
            </div>

            <div class="mb-3">
                <label>File Tambahan</label>
                <?php if ($data['file_url']): ?>
                    <p>File: <?= $data['file_url'] ?></p>
                <?php else: ?>
                    <p>Tidak ada file PDF</p>
                <?php endif; ?>
                <input type="file" name="file_url" class="form-control mt-2">
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="index.php?page=operator-berita" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>

                <button class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </form>

    </div>

</div>
