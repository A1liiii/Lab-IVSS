<?php include __DIR__ . '/../Layouts/mahasiswa_header.php'; ?>
<?php include __DIR__ . '/../Layouts/mahasiswa_sidebar.php'; ?>

<div class="mhs-content">

    <!-- PROFIL MAHASISWA -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Profil Mahasiswa
        </div>
        <div class="card-body d-flex">

            <!-- FOTO PROFIL -->
            <div style="width:120px; height:120px; margin-right:20px;">
                <?php if (!empty($mahasiswa['foto'])) { ?>
                    <img src="<?php echo BASE_URL . '/lab-ivss/public/uploads/mahasiswa/' . $mahasiswa['foto']; ?>" 
                         alt="Foto Mahasiswa" 
                         style="width:100%; height:100%; object-fit:cover; border-radius:10px;">
                <?php } else { ?>
                    <img src="<?php echo BASE_URL . '../../../public/assets/img/default-user.png'; ?>" 
                         alt="Default Foto" 
                         style="width:100%; height:100%; object-fit:cover; border-radius:10px;">
                <?php } ?>
            </div>

            <!-- DATA PROFIL -->
            <div>
                <h5><?php echo htmlspecialchars($mahasiswa['nama']); ?></h5>
                <p class="mb-1"><strong>NIM:</strong> <?php echo $mahasiswa['nim']; ?></p>
                <p class="mb-1"><strong>Program Studi:</strong> <?php echo $mahasiswa['prodi']; ?></p>
                <p class="mb-1"><strong>Angkatan:</strong> <?php echo $mahasiswa['angkatan']; ?></p>

                <p class="mb-1">
                <strong>Status:</strong> 
                <span class="badge 
                    <?php 
                        echo ($mahasiswa['status'] == 'aktif') ? 'bg-success' : 
                            (($mahasiswa['status'] == 'cuti') ? 'bg-warning' : 'bg-secondary'); 
                    ?>
                ">
        <?php echo htmlspecialchars($mahasiswa['status']); ?>
    </span>
</p>

            </div>

        </div>
    </div>


    <!-- INFORMASI RISET -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            Riset / Proyek yang Diikuti
        </div>

        <div class="card-body">
            <?php 
            if (!empty($proyek)) { 
            ?>
                <h5><?php echo htmlspecialchars($proyek['judul']); ?></h5>
                <p><strong>Dosen Pembimbing:</strong> <?php echo htmlspecialchars($proyek['nama_dosen']); ?></p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-info"><?php echo $proyek['status']; ?></span>
                </p>
            <?php 
            } else { 
            ?>
                <p class="text-muted fst-italic">
                    Belum mengikuti riset atau belum dipilihkan pembimbing.
                </p>
            <?php 
            } 
            ?>
        </div>
    </div>


    <!-- TOMBOL ABSEN -->
    <div class="mb-4">
        <a href="/absen" class="btn btn-primary btn-lg">Absen Hari Ini</a>
    </div>


    <!-- LOG AKTIVITAS -->
    <div class="card">
        <div class="card-header bg-dark text-white">Log Aktivitas</div>
        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
            
            <?php 
            if (!empty($log)) { 
            ?>
                <ul>
                    <?php 
                    foreach ($log as $item) { 
                    ?>
                        <li>
                            <strong><?php echo $item['waktu']; ?></strong> — 
                            <?php echo htmlspecialchars($item['aktivitas']); ?>
                        </li>
                    <?php } ?>
                </ul>
            <?php 
            } else { 
            ?>
                <p class="text-muted">Belum ada aktivitas.</p>
            <?php 
            } 
            ?>

        </div>
    </div>

</div>
