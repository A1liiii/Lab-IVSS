<div class="admin-content">

    <h2 class="mb-4">Data Publikasi</h2>

    <a href="/lab-ivss/index.php?page=operator-publikasi-add" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Tambah Publikasi
    </a>

    <!-- Dropdown filter tahun -->
    <form method="GET" class="mb-3">
        <input type="hidden" name="page" value="operator-publikasi">
        <div class="row">
            <div class="col-md-3">
                <select name="tahun" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua Tahun --</option>
                    <?php foreach ($years as $y): ?>
                        <option value="<?= $y ?>" 
                            <?= (!empty($_GET['tahun']) && $_GET['tahun'] == $y) ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>

    <div class="admin-card">

        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th style="width:10%;">Judul</th>
                    <th style="width:5%;">Tahun</th>
                    <th style="width:40%;">Deskripsi</th>
                    <th style="width:7%;">Link</th>
                    <th style="width:12%; text-align:center;">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $d): 
                        $tahun = substr($d['tanggal_mulai'], 0, 4);
                    ?>
                        <tr>
                            <td style="vertical-align:middle;"><?= htmlspecialchars($d['judul']) ?></td>
                            <td style="vertical-align:middle;"><?= $tahun ?></td>
                            <td style="vertical-align:middle;">
                                <div class="deskripsi-preview" style="
                                    max-height:50px;
                                    overflow:hidden;
                                    word-break:break-word;
                                    white-space: normal;
                                ">
                                    <?= htmlspecialchars($d['deskripsi']) ?>
                                </div>
                                <?php if(strlen(strip_tags($d['deskripsi'])) > 80): ?>
                                    <a href="#" class="toggle-view" style="font-size:12px; color:blue;">View more</a>
                                <?php endif; ?>
                            </td>
                            <td style="vertical-align:middle;">
                                <?php if (!empty($d['link'])): ?>
                                    <a href="<?= htmlspecialchars($d['link']) ?>" target="_blank">Buka</a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center; vertical-align:middle;">
                                <a href="/lab-ivss/index.php?page=operator-publikasi-edit&id=<?= $d['id'] ?>"
                                   class="btn btn-sm btn-warning mb-1">
                                   <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a onclick="return confirm('Hapus publikasi ini?')"
                                   href="/lab-ivss/index.php?page=operator-publikasi-delete&id=<?= $d['id'] ?>"
                                   class="btn btn-sm btn-danger mb-1">
                                   <i class="bi bi-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada data publikasi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.toggle-view').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const deskripsi = this.previousElementSibling;
            if(deskripsi.style.maxHeight && deskripsi.style.maxHeight !== "50px") {
                deskripsi.style.maxHeight = "50px";
                this.textContent = "View more";
            } else {
                deskripsi.style.maxHeight = "none";
                this.textContent = "View less";
            }
        });
    });
});
</script>
