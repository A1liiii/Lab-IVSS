<div class="container mt-4">
    <h2>Tambah Dosen</h2>

    <form action="index.php?page=admin-dosen-store" method="POST">

        <div class="mb-3">
            <label>NIP</label>
            <input type="text" name="nip" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>NIDN</label>
            <input type="text" name="nidn" class="form-control">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>

        <div class="mb-3">
            <label>Jabatan</label>
            <input type="text" name="jabatan" class="form-control">
        </div>

        <div class="mb-3">
            <label>Pendidikan</label>
            <input type="text" name="pendidikan" class="form-control">
        </div>

        <div class="d-flex align-items-center gap-2 mt-4">
    <a href="index.php?page=admin-user" class="btn btn-secondary px-4">
        ← Kembali
    </a>

    <button type="submit" class="btn btn-primary px-4">
        Simpan
    </button>
</div>
    </form>
</div>
