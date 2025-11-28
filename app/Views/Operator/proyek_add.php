<div class="admin-content">

    <h2 class="mb-4">Tambah Proyek</h2>

    <div class="admin-card p-4 shadow-sm" style="border-radius: 10px;">

        <form action="" method="POST">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Judul Proyek</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="on going">On Going</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="4"></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control">
                </div>
            </div>

            <hr class="my-4">

            <h5 class="mb-3">Anggota Proyek</h5>

            <div id="anggota-wrapper">

                <div class="row anggota-item mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Pilih User</label>
                        <select name="user_id[]" class="form-control">
                            <option value="">-- Pilih User --</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['user_id'] ?>">
                                <?= htmlspecialchars($u['identitas'] . " - " . $u['username']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Peran</label>
                        <select name="role[]" class="form-control">
                            <option value="ketua">Ketua</option>
                            <option value="anggota">Anggota</option>
                        </select>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-remove" onclick="removeAnggota(this)">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>

            </div>

            <button type="button" class="btn btn-info mb-3" onclick="addAnggota()">
                <i class="bi bi-plus-circle"></i> Tambah Anggota
            </button>

            <div class="d-flex justify-content-end mt-4">
                <a href="index.php?page=operator-proyek" class="btn btn-secondary me-2">
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
// Tambah field anggota
function addAnggota() {
    let html = `
    <div class="row anggota-item mb-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Pilih User</label>
            <select name="user_id[]" class="form-control">
                <option value="">-- Pilih User --</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['user_id'] ?>">
                        <?= htmlspecialchars($u['identitas'] . " - " . $u['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-5">
            <label class="form-label fw-semibold">Peran</label>
            <select name="role[]" class="form-control">
                <option value="ketua">Ketua</option>
                <option value="anggota">Anggota</option>
            </select>
        </div>

        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-remove" onclick="removeAnggota(this)">
                <i class="bi bi-x"></i>
            </button>
        </div>
    </div>`;

    document.getElementById("anggota-wrapper").insertAdjacentHTML('beforeend', html);
}

// Hapus field anggota
function removeAnggota(btn) {
    btn.closest('.anggota-item').remove();
}
</script>
