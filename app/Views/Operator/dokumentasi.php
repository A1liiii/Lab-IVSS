<div class="admin-content">
    <h2 class="mb-4">Manajemen Dokumentasi</h2>

    <a href="/lab-ivss/index.php?page=operator-dokumentasi-add" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Tambah Dokumentasi
    </a>

    <div class="admin-card">    
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul & Deskripsi</th>
                    <th>Jenis</th>
                    <th>Tanggal</th>
                    <th>Preview</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)) : ?>
                    <?php $no = 1; foreach ($data as $d) : ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <strong><?= $d['judul_kegiatan']; ?></strong><br>
                                <small class="text-muted"><?= $d['deskripsi_kegiatan']; ?></small>
                            </td>
                            <td>
                                <span class="badge bg-primary"><?= ucfirst($d['jenis_kegiatan']); ?></span>
                            </td>
                            <td><?= date('d M Y', strtotime($d['tanggal_kegiatan'])); ?></td>
                            <td>
                                <?php if ($d['file_path']) : ?>
                                    <?php if (preg_match('/\.(jpg|jpeg|png)$/i', $d['file_path'])) : ?>
                                        <img src="public/uploads/dokumentasi/<?= $d['file_path']; ?>" 
                                             style="width:70px; height:70px; object-fit:cover; border-radius:6px;">
                                    <?php else : ?>
                                        <a href="public/uploads/dokumentasi/<?= $d['file_path']; ?>" target="_blank">
                                            <i class="bi bi-file-earmark-text" style="font-size:40px;"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                            <a href="/lab-ivss/index.php?page=operator-dokumentasi-edit&documentation_id=<?= $d['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="/lab-ivss/index.php?page=operator-dokumentasi-delete&documentation_id=<?= $d['id'] ?>" 
                                   onclick="return confirm('Yakin ingin menghapus dokumentasi ini?')" 
                                   class="btn btn-danger btn-sm">Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" class="text-center">Belum ada dokumentasi</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
