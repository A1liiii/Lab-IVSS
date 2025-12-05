<div class="container mt-4">
    <h2>Tambah User Untuk Dosen</h2>

    <form method="POST" action="index.php?page=admin-user-store">

        <!-- Hidden field untuk identitas dosen -->
        <input type="hidden" name="identitas" value="<?= $d['nip'] ?>">

        <div class="mb-3">
            <label>Username (NIDN)</label>
            <input type="text" name="username" class="form-control" value="<?= $d['nidn'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Password (NIP)</label>
            <input type="text" name="password" class="form-control" value="<?= $d['nip'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Pilih Role</label>
            <select name="role_id" class="form-select" required>
                <option value="1">Admin</option>
                <option value="2">Operator</option>
                <option value="3" selected>Dosen</option>
            </select>
        </div>

        <div class="d-flex align-items-center gap-2 mt-4">
            <a href="index.php?page=admin-user" class="btn btn-secondary px-4">
                ← Kembali
            </a>

            <button type="submit" class="btn btn-primary px-4">
                Simpan User
            </button>
        </div>
    </form>
</div>
