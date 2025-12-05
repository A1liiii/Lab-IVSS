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
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Foto Baru (opsional)</label>
                <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewFoto(event)">

                <!-- Preview foto lama -->
                <?php if (!empty($data['foto'])): ?>
                    <img id="preview-old" 
                        src="/lab-ivss/public/uploads/fasilitas/<?= htmlspecialchars($data['foto']) ?>" 
                        style="width:120px;height:120px;object-fit:cover;margin-top:10px;border-radius:6px;">
                <?php else: ?>
                    <p class="text-muted" id="no-photo">Tidak ada foto</p>
                <?php endif; ?>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="index.php?page=operator-fasilitas" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>

                <button class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>

        </form>
    </div>
</div>
<script>
function previewFoto(event) {
    const input = event.target;
    let previewNew = document.getElementById('preview-new');

    // Jika preview baru belum ada, buat elemen img
    if(!previewNew) {
        previewNew = document.createElement('img');
        previewNew.id = 'preview-new';
        previewNew.style.width = '120px';
        previewNew.style.height = '120px';
        previewNew.style.objectFit = 'cover';
        previewNew.style.borderRadius = '6px';
        previewNew.style.marginTop = '10px';
        input.parentNode.insertBefore(previewNew, input.nextSibling);
    }

    if(input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewNew.src = e.target.result;
            previewNew.style.display = 'block';

            // Sembunyikan preview lama / teks "Tidak ada foto"
            const old = document.getElementById('preview-old');
            if(old) old.style.display = 'none';
            const noPhoto = document.getElementById('no-photo');
            if(noPhoto) noPhoto.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>