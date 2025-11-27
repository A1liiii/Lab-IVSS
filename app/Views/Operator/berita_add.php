<div class="admin-content">

    <h2 class="mb-4">Tambah Berita</h2>

    <div class="admin-card">
        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label>Judul Berita</label>
                <input type="text" name="judul" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="5" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label>Kategori</label>
                <select name="kategori" class="form-control" required>
                    <option value="berita">Berita</option>
                    <option value="pengumuman">Pengumuman</option>
                    <option value="aktivitas">Aktivitas</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Foto (Optional)</label>
                <input type="file" name="foto" class="form-control">
            </div>

            <div class="mb-3">
                <label>File Tambahan (PDF Optional)</label>
                <input type="file" name="file_url" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="/lab-ivss/index.php?page=operator-berita" class="btn btn-secondary">Kembali</a>

        </form>
    </div>

</div>
