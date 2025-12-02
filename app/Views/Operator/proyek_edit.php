<div class="admin-content">

    <h2 class="mb-4">Edit Proyek</h2>

    <div class="admin-card p-4">

        <form action="" method="POST">

            <input type="hidden" name="proyek_id" value="<?= $data['proyek_id'] ?>">

            <div class="mb-3">
                <label class="form-label">Judul Proyek</label>
                <input type="text" name="judul" value="<?= htmlspecialchars($data['judul']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="<?= $data['tanggal_mulai'] ?>" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="<?= $data['tanggal_selesai'] ?>" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="perencanaan" <?= $data['status']=='perencanaan'?'selected':'' ?>>Perencanaan</option>
                    <option value="berjalan" <?= $data['status']=='berjalan'?'selected':'' ?>>Berjalan</option>
                    <option value="selesai" <?= $data['status']=='selesai'?'selected':'' ?>>Selesai</option>
                </select>
            </div>

            <hr>

            <h5>Anggota Proyek</h5>

            <table class="table table-bordered" id="anggotaTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Peran</th>
                        <th width="50">#</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($anggota as $a): ?>
                        <tr>
                            <td>
                                <select name="user_id[]" class="form-control">
                                    <option value="">-- Pilih User --</option>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?= $u['user_id'] ?>"
                                            <?= $u['user_id']==$a['user_id']?'selected':'' ?>>
                                            <?= htmlspecialchars($u['nama']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td>
                                <select name="role[]" class="form-control">
                                    <option value="ketua" <?= $a['role']=='ketua'?'selected':'' ?>>Ketua</option>
                                    <option value="anggota" <?= $a['role']=='anggota'?'selected':'' ?>>Anggota</option>
                                </select>
                            </td>

                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="this.parentNode.parentNode.remove();">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>

            <button type="button" class="btn btn-sm btn-secondary mb-3" onclick="addRow()">
                <i class="bi bi-plus-circle"></i> Tambah Anggota
            </button>

            <script>
                function addRow() {
                    let row = `
                        <tr>
                            <td>
                                <select name="user_id[]" class="form-control">
                                    <option value="">-- Pilih User --</option>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?= $u['user_id'] ?>">
                                            <?= htmlspecialchars($u['nama']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td>
                                <select name="role[]" class="form-control">
                                    <option value="ketua">Ketua</option>
                                    <option value="anggota">Anggota</option>
                                </select>
                            </td>

                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="this.parentNode.parentNode.remove();">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    document.querySelector("#anggotaTable tbody").insertAdjacentHTML("beforeend", row);
                }
            </script>

            <button class="btn btn-primary mt-3">Update</button>

        </form>

    </div>

</div>
