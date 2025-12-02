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
                <label>Caption (opsional)</label>
                <input type="text" name="caption" class="form-control" value="<?= $data['caption']; ?>">
            </div>

            <div class="mb-3">
                <label>File Saat Ini</label><br>

                <?php if (preg_match('/\.(jpg|jpeg|png)$/i', $data['file_path'])) : ?>
                    <img src="public/uploads/dokumentasi/<?= $data['file_path']; ?>" 
                         width="120" class="mb-2" style="border-radius: 6px; object-fit:cover;">
                <?php else : ?>
                    <p>File: <?= $data['file_path']; ?></p>
                <?php endif; ?>

                <input type="file" name="file_path" class="form-control mt-2">
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
