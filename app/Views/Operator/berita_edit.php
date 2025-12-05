<div class="admin-content">

    <h2 class="mb-4">Edit Berita</h2>

    <form method="POST" enctype="multipart/form-data">

        <!-- JUDUL -->
        <label class="form-label">Judul Berita</label>
        <input type="text" name="judul" class="form-control mb-3" 
               value="<?= $berita['judul'] ?>" required>

        <!-- DESKRIPSI -->
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control mb-3" rows="6" required><?= $berita['deskripsi'] ?></textarea>

        <!-- KATEGORI -->
        <label class="form-label">Kategori</label>
        <select name="kategori" class="form-control mb-3" required>
            <option value="berita"        <?= $berita['kategori']=='berita' ? 'selected':'' ?>>Berita</option>
            <option value="pengumuman"        <?= $berita['kategori']=='pengumuman' ? 'selected':'' ?>>Pengumuman</option>
            <option value="aktivitas"    <?= $berita['kategori']=='aktivitas' ? 'selected':'' ?>>Aktivitas</option>
            <option value="lainnya"    <?= $berita['kategori']=='lainnya' ? 'selected':'' ?>>Lainnya</option>
        </select>

        <!-- PENULIS -->
        <label class="form-label">Penulis</label>
        <input type="text" class="form-control mb-3"
       value="<?= $_SESSION['username'] ?>" disabled>

        <!-- TANGGAL POST -->
        <label class="form-label">Tanggal Posting</label>
        <input type="date" name="tgl_post" class="form-control mb-3"
        value="<?= isset($berita['tgl_post']) && !empty($berita['tgl_post']) ? $berita['tgl_post'] : date('Y-m-d') ?>" required>

        <!-- FOTO / THUMBNAIL -->
        <label class="form-label">Foto Berita</label><br>

        <?php if ($berita['foto']): ?>
            <img src="/lab-ivss/public/uploads/berita/<?= $berita['foto'] ?>" 
                id="preview-old"
                style="width:200px;margin-bottom:10px;border-radius:6px;">
        <?php else: ?>
            <p class="text-muted">Tidak ada foto</p>
        <?php endif; ?>

        <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewFoto(event)">

        <!-- PREVIEW NEW PHOTO -->
        <img id="preview-new" src="#" 
             style="display:none;width:200px;margin-top:10px;border-radius:6px;">

        <div class="d-flex justify-content-end mt-4">
            <a href="index.php?page=operator-berita" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>

            <button class="btn btn-primary">
                <i class="bi bi-save"></i> Update
            </button>
        </div>

    </form>

</div>

<script>
function previewFoto(event) {
    const img = document.getElementById('preview-new');
    const old = document.getElementById('preview-old');

    img.src = URL.createObjectURL(event.target.files[0]);
    img.style.display = "block";

    if (old) old.style.opacity = "0.4";
}
</script>
