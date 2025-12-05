<div class="admin-content">

    <h2 class="mb-4">Edit Dokumentasi</h2>

    <div class="admin-card">

        <form method="POST" enctype="multipart/form-data">

            <input type="hidden" name="dokumentasi_id" value="<?= $data['id']; ?>">
            <input type="hidden" name="file_lama" value="<?= $data['file_path']; ?>">

            <div class="mb-3">
                <label>Judul Kegiatan</label>
                <input type="text" name="judul_kegiatan" class="form-control"
                       value="<?= $data['judul_kegiatan']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Tanggal Kegiatan</label>
                <input type="date" name="tanggal_kegiatan" class="form-control"
                       value="<?= $data['tanggal_kegiatan']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Jenis Kegiatan</label>
                <select name="jenis_kegiatan" class="form-control" required>
                    <option value="">-- Pilih Jenis --</option>
                    <?php
                        $jenis = ["workshop", "riset", "seminar", "kunjungan", "lomba", "pengabdian", "aktivitas_lain"];
                        foreach ($jenis as $j) :
                    ?>
                        <option value="<?= $j; ?>" <?= ($data['jenis_kegiatan'] == $j ? "selected" : ""); ?>>
                            <?= ucfirst($j); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Deskripsi Kegiatan</label>
                <textarea name="deskripsi_kegiatan" class="form-control" rows="4"
                ><?= $data['deskripsi_kegiatan']; ?></textarea>
            </div>

            <div class="mb-3">
    <label class="form-label">Dokumentasi (Foto)</label><br>

    <!-- Preview foto lama -->
    <?php if (!empty($data['file_path'])): ?>
        <img id="preview-old" 
             src="/lab-ivss/public/uploads/dokumentasi/<?= htmlspecialchars($data['file_path']) ?>" 
             style="width:250px;margin-bottom:10px;border-radius:6px;">
    <?php else: ?>
        <p class="text-muted">Tidak ada foto</p>
    <?php endif; ?>

    <!-- Input file baru -->
    <input type="file" name="file_path" class="form-control" accept="image/*" onchange="previewFoto(event)">
</div>

            
            <div class="d-flex justify-content-end mt-4">
                <a href="index.php?page=operator-dokumentasi" class="btn btn-secondary me-2">
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
    let preview = document.getElementById('preview-new');

    // jika preview baru belum ada, buat img element
    if(!preview) {
        preview = document.createElement('img');
        preview.id = 'preview-new';
        preview.style.width = '250px';
        preview.style.marginTop = '10px';
        preview.style.borderRadius = '6px';
        input.parentNode.insertBefore(preview, input.nextSibling);
    }

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>