<?php
require_once '../../config/database.php';
require_once '../../models/MahasiswaModel.php';

$model = new MahasiswaModel($conn);

/*
========================================
✅ SIMULASI DOSEN LOGIN (SEMENTARA)
GANTI nanti jadi:
session_start();
$dosen_id = $_SESSION['user_id'];
========================================
*/
$dosen_id = 1;

$result = $model->getByDosen($dosen_id);
?>

<h3>Mahasiswa Bimbingan</h3>

<a href="tambah-mahasiswa.php">+ Tambah Mahasiswa</a>

<br><br>

<table border="1" cellpadding="8">
  <tr>
    <th>NIM</th>
    <th>Nama</th>
    <th>Email</th>
    <th>Prodi</th>
    <th>Angkatan</th>
    <th>Status</th>
    <th>Kategori</th>
  </tr>

  <?php while ($row = pg_fetch_assoc($result)) : ?>
  <tr>
    <td><?= $row['nim'] ?></td>
    <td><?= $row['nama'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['prodi'] ?></td>
    <td><?= $row['angkatan'] ?></td>
    <td><?= $row['status'] ?></td>
    <td><?= $row['kategori'] ?></td>
  </tr>
  <?php endwhile; ?>
</table>
