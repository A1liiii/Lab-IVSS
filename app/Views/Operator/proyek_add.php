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
                        <option value="on going">on going</option>
                        <option value="selesai">selesai</option>
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
                    <input type="date" name="tanggal_mulai" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control">
                </div>
            </div>
            <!-- ANGGOTA PROYEK -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Anggota Proyek</label>

                <table class="table table-bordered" id="anggotaTable">
                    <thead>
                        <tr>
                            <th width="50%">User</th>
                            <th width="40%">Role</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Row default pertama -->
                        <tr>
                            <td>
                                <select name="user_id[]" class="form-control">
                                    <option value="">-- Pilih User --</option>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?= $u['user_id'] ?>">
                                            <?= htmlspecialchars($u['nama']) ?>
                                            (<?= $u['nip'] ?: $u['nim'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select name="role[]" class="form-select" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="ketua">Ketua</option>
                                    <option value="anggota">Anggota</option>
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button" onclick="addRow()" class="btn btn-success btn-sm">+</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Template row anggota untuk JS -->
            <template id="anggotaRowTemplate">
                <tr>
                    <td>
                        <select name="user_id[]" class="form-control">
                            <option value="">-- Pilih User --</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['user_id'] ?>">
                                    <?= htmlspecialchars($u['nama']) ?>
                                    (<?= $u['nip'] ?: $u['nim'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select name="role[]" class="form-select" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="ketua">Ketua</option>
                            <option value="anggota">Anggota</option>
                        </select>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">-</button>
                    </td>
                </tr>
            </template>
            <script>
                function addRow() {
                    let table = document.querySelector("#anggotaTable tbody");
                    let template = document.querySelector("#anggotaRowTemplate");
                    let clone = template.content.cloneNode(true);
                    table.appendChild(clone);
                }

                function removeRow(btn) {
                    btn.closest('tr').remove();
                }
            </script>

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
