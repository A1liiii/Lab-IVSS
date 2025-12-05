<div class="admin-content">
    <h2 class="mb-4">Manajemen Dokumentasi</h2>

    <a href="/lab-ivss/index.php?page=operator-dokumentasi-add" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Tambah Dokumentasi
    </a>

    <div class="admin-card">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th style="width:35%;">Judul & Deskripsi</th>
                    <th style="width:12%;">Jenis</th>
                    <th style="width:12%;">Tanggal</th>
                    <th style="width:15%;">Dokumentasi</th>
                    <th style="width:15%; text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)) : ?>
                    <?php foreach ($data as $d) : ?>
                        <tr>
                            <!-- Judul & Deskripsi -->
                            <td>
                                <strong><?= htmlspecialchars($d['judul_kegiatan']); ?></strong><br>
                                <div class="deskripsi-preview" style="
                                    max-height:50px;
                                    overflow:hidden;
                                    word-break:break-word;
                                    white-space: normal;">
                                    <?= htmlspecialchars($d['deskripsi_kegiatan']); ?>
                                </div>
                                <?php if(strlen(strip_tags($d['deskripsi_kegiatan'])) > 80): ?>
                                    <a href="#" class="toggle-view" style="font-size:12px; color:blue; display:inline-block; margin-top:2px;">View more</a>
                                <?php endif; ?>
                            </td>

                            <!-- Jenis Kegiatan -->
                            <td>
                                <span class="badge bg-primary"><?= ucfirst($d['jenis_kegiatan']); ?></span>
                            </td>

                            <!-- Tanggal -->
                            <td><?= date('d M Y', strtotime($d['tanggal_kegiatan'])); ?></td>

                            <!-- Dokumentasi (Thumbnail / Icon) -->
                            <td>
                                <?php if (!empty($d['file_path'])): ?>
                                    <?php
                                        $file = htmlspecialchars($d['file_path']);
                                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                    ?>
                                    <?php if (in_array($ext, ['jpg','jpeg','png','gif'])): ?>
                                        <img src="/lab-ivss/public/uploads/dokumentasi/<?= $file ?>" 
                                            style="width:120px; height:auto; object-fit:cover; border-radius:6px;">
                                    <?php else: ?>
                                        <a href="/lab-ivss/public/uploads/dokumentasi/<?= $file ?>" target="_blank">
                                            <i class="bi bi-file-earmark-text" style="font-size:40px;"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Aksi -->
                            <td style="text-align:center;">
                                <a href="/lab-ivss/index.php?page=operator-dokumentasi-edit&documentation_id=<?= $d['id'] ?>" 
                                   class="btn btn-warning btn-sm mb-1">Edit</a>
                                <a href="/lab-ivss/index.php?page=operator-dokumentasi-delete&documentation_id=<?= $d['id'] ?>" 
                                   onclick="return confirm('Yakin ingin menghapus dokumentasi ini?')" 
                                   class="btn btn-danger btn-sm mb-1">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada dokumentasi</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- View More / View Less Script -->
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
