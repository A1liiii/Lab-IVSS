<div class="admin-content">

    <h2 class="mb-4">Tambah Dokumentasi</h2>

    <div class="admin-card p-4 shadow-sm" style="border-radius: 10px;">

        <form action="" method="POST" enctype="multipart/form-data">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Judul Kegiatan</label>
                    <input type="text" name="judul_kegiatan" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Tanggal Kegiatan</label>
                    <input type="date" name="tanggal_kegiatan" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Jenis Kegiatan</label>
                <select name="jenis_kegiatan" class="form-select" required>
                    <option value="">-- Pilih Jenis Kegiatan --</option>
                    <option value="workshop">Workshop</option>
                    <option value="riset">Riset</option>
                    <option value="seminar">Seminar</option>
                    <option value="kunjungan">Kunjungan</option>
                    <option value="lomba">Lomba</option>
                    <option value="pengabdian">Pengabdian</option>
                    <option value="aktivitas_lain">Aktivitas Lain</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi Kegiatan</label>
                <textarea name="deskripsi_kegiatan" class="form-control" rows="4" placeholder="Tulis deskripsi kegiatan di sini..."></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Caption (opsional)</label>
                <input type="text" name="caption" class="form-control" placeholder="Contoh: Suasana kegiatan penyuluhan">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Upload Foto / File</label>
                <input type="file" name="file_path" class="form-control" required>
                <small class="text-muted">Format diperbolehkan: JPG, PNG, PDF, DOC</small>
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
