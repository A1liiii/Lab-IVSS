<div class="admin-content">

    <h2 class="mb-4">Data Fasilitas</h2>

    <a href="/lab-ivss/index.php?page=operator-fasilitas-add" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Tambah Fasilitas
    </a>

    <div class="admin-card">

        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th style="width:15%;">Gambar Fasilitas</th>
                    <th style="width:32%;">Nama & Deskripsi</th>
                    <th style="width:5%;">Kategori</th>
                    <th style="width:10%; text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $d): ?>
                        <tr>
                            <!-- Foto -->
                            <td>
                                <?php if (!empty($d['foto'])): ?>
                                    <img src="public/uploads/fasilitas/<?= htmlspecialchars($d['foto']) ?>" 
                                         style="width:150px;height:auto;object-fit:cover;border-radius:6px;">
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Nama & Deskripsi -->
                            <td>
                                <strong><?= htmlspecialchars($d['nama']) ?></strong><br>
                                <div class="deskripsi-preview" style="
                                    max-height:50px;
                                    overflow:hidden;
                                    word-break:break-word;
                                    white-space: normal;">
                                    <?= htmlspecialchars($d['deskripsi']) ?>
                                </div>
                                <?php if(strlen(strip_tags($d['deskripsi'])) > 80): ?>
                                    <a href="#" class="toggle-view" style="font-size:12px; color:blue; display:inline-block; margin-top:2px;">View more</a>
                                <?php endif; ?>
                            </td>

                            <!-- Kategori -->
                            <td><?= !empty($d['kategori']) ? ucfirst($d['kategori']) : '-' ?></td>

                            <!-- Aksi -->
                            <td style="text-align:center;">
                                <a href="/lab-ivss/index.php?page=operator-fasilitas-edit&fasilitas_id=<?= $d['id'] ?>" 
                                   class="btn btn-sm btn-warning mb-1">
                                   <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="/lab-ivss/index.php?page=operator-fasilitas-delete&id=<?= $d['id'] ?>" 
                                   onclick="return confirm('Hapus fasilitas ini?')" 
                                   class="btn btn-sm btn-danger mb-1">
                                   <i class="bi bi-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Belum ada data fasilitas.
                        </td>
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
