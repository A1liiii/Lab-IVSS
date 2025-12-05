<div class="admin-content">

    <h2 class="mb-4">Tambah Berita Baru</h2>

    <form method="POST" enctype="multipart/form-data">

        <!-- JUDUL -->
        <label class="form-label">Judul Berita</label>
        <input type="text" name="judul" class="form-control mb-3" required>

        <!-- DESKRIPSI -->
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control mb-3" rows="6" required></textarea>

        <!-- KATEGORI -->
        <label class="form-label">Kategori</label>
        <select name="kategori" class="form-control mb-3" required>
            <option value="">-- Pilih Kategori --</option>
            <option value="berita">Berita</option>
            <option value="pengumuman">Pengumuman</option>
            <option value="aktivitas">Aktivitas</option>
            <option value="lainnya">Lainnya</option>
        </select>

        <!-- PENULIS -->
        <label class="form-label">Penulis</label>
        <input type="text" class="form-control mb-3" 
        value="<?= $_SESSION['username'] ?>" disabled>

        <!-- TANGGAL POST -->
        <label class="form-label">Tanggal Posting</label>
        <input type="date" name="tgl_post" class="form-control mb-3"
        value="<?= isset($berita['tgl_post']) && !empty($berita['tgl_post']) ? $berita['tgl_post'] : date('Y-m-d') ?>" required>


        <!-- FOTO -->
        <label class="form-label">Thumbnail / Foto Berita</label>
        <input type="file" name="foto" class="form-control" 
               accept="image/*" onchange="previewFoto(event)" required>

        <img id="preview" src="#" 
             style="display:none;width:200px;margin-top:10px;border-radius:6px;">

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

<script>
function previewFoto(event) {
    const img = document.getElementById('preview');
    img.src = URL.createObjectURL(event.target.files[0]);
    img.style.display = "block";
}
</script>
