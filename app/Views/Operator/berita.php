<div class="admin-content">
    <h2 class="mb-4">Kelola Berita</h2>

    <a href="/lab-ivss/index.php?page=operator-berita-add" class="btn btn-primary mb-3">
        + Tambah Berita
    </a>

    <div class="admin-card" style="overflow-x:auto;"> <!-- Tambahkan scroll horizontal -->
        <table class="table table-bordered table-striped table-hover" style="min-width:900px;">
            <thead>
                <tr>
                    <th style="width:120px;">Thumbnail</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Kategori</th>
                    <th>Penulis</th>
                    <th>Tanggal</th>
                    <th style="min-width:120px; text-align:center;">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($berita as $b): ?>
                <tr>
                    <td>
                        <img src="/lab-ivss/public/uploads/berita/<?= $b['foto'] ?: 'no-image.png' ?>" 
                             style="width:100px; height:70px; object-fit:cover; border-radius:5px;">
                    </td>
                    <td><strong><?= htmlspecialchars($b['judul']) ?></strong></td>
                    <td>
                        <div class="deskripsi-preview">
                            <?= htmlspecialchars($b['deskripsi']) ?>
                        </div>
                        <?php if(strlen(strip_tags($b['deskripsi'])) > 80): ?>
                            <a href="#" class="toggle-view">View more</a>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($b['kategori']) ?></td>
                    <td><?= htmlspecialchars($b['penulis']) ?></td>
                    <td><?= htmlspecialchars($b['tgl_post']) ?></td>
                    <td>
                        <a href="/lab-ivss/index.php?page=operator-berita-edit&id=<?= $b['berita_id'] ?>" 
                           class="btn btn-warning btn-sm mb-1">Edit</a>
                        <a href="/lab-ivss/index.php?page=operator-berita-delete&id=<?= $b['berita_id'] ?>" 
                           onclick="return confirm('Hapus berita ini?')" 
                           class="btn btn-danger btn-sm mb-1">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.deskripsi-preview {
    max-height: 50px; /* tinggi preview sebelum klik view more */
    overflow: hidden;
    word-break: break-word;
    white-space: normal;
}
.toggle-view {
    font-size: 12px;
    color: blue;
    cursor: pointer;
    display: inline-block;
    margin-top: 2px;
}
</style>

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
