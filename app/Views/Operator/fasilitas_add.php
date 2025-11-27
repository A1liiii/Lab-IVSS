<div class="admin-content">

    <h2 class="mb-4">Tambah Fasilitas</h2>

    <div class="admin-card p-4 shadow-sm" style="border-radius: 10px;">
        <form action="" method="POST" enctype="multipart/form-data">

            <div class="row">
                <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Nama Fasilitas</label>
                <input type="text" name="nama" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="4"></textarea>
            </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-control" required>
                    <option value="tersedia">Tersedia</option>
                    <option value="dipinjam">Sedang Digunakan</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Kategori Fasilitas</label>
                <select name="kategori" class="form-control" required>
                    <option value="fasilitas">Fasilitas</option>
                    <option value="peralatan">Peralatan</option>
                    <option value="ruangan">Ruangan</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Foto (opsional)</label>
                <input type="file" name="foto" class="form-control">
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
