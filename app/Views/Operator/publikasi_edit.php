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
                    <label class="form-label fw-semibold">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" 
                           value="<?php echo isset($data['tanggal_mulai']) ? $data['tanggal_mulai'] : ''; ?>" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" 
                           value="<?php echo isset($data['tanggal_selesai']) ? $data['tanggal_selesai'] : ''; ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Status Publikasi</label>
                <select name="status" class="form-select" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="draft" <?php echo ($data['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                    <option value="publik" <?php echo ($data['status'] == 'publik') ? 'selected' : ''; ?>>Publik</option>
                    <option value="arsip" <?php echo ($data['status'] == 'arsip') ? 'selected' : ''; ?>>Arsip</option>
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
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>

        </form>

    </div>

</div>
