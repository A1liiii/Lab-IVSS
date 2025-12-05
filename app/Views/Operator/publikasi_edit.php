<div class="admin-content">

    <h2 class="mb-4">Edit Publikasi</h2>

    <div class="admin-card p-4 shadow-sm" style="border-radius: 10px;">

        <form action="" method="POST">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Judul Publikasi</label>
                    <input type="text" name="judul" class="form-control" 
                           value="<?php echo htmlspecialchars($data['judul']); ?>" required>
                </div>

                <div class="col-md-3 mb-3">
                <label class="form-label">Tahun</label>
                <input type="number" name="tahun" class="form-control mb-3"
                    min="2000" max="2099" value="<?= date('Y') ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Status Publikasi</label>
                <select name="status" class="form-select" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="proses" <?php echo ($data['status'] == 'proses') ? 'selected' : ''; ?>>Proses</option>
                    <option value="publikasi" <?php echo ($data['status'] == 'publikasi') ? 'selected' : ''; ?>>publikasi</option>
                    <option value="draft" <?php echo ($data['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="4" 
                          placeholder="Isi deskripsi publikasi..."><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Link</label>
                <input type="text" name="link" class="form-control" 
                       value="<?php echo isset($data['link']) ? htmlspecialchars($data['link']) : ''; ?>" 
                       placeholder="Contoh: https://contoh.com">
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="index.php?page=operator-publikasi" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>

                <button class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

</div>
