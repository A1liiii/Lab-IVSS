<?php
// role = admin/operator/mahasiswa/dosen/ketua_lab
include __DIR__ . "/{$role}_header.php";
include __DIR__ . "/{$role}_sidebar.php";
?>

<div class="admin-content">
    <?php include $viewPath; ?>
</div>

<?php include __DIR__ . "/{$role}_footer.php"; ?>
