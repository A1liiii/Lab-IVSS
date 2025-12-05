<style>
/* ===== ALERT (TOAST) ===== */
.alert {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    min-width: 250px;
    max-width: 350px;
    padding: 12px 20px;
    color: #000;
    font-weight: bold;
    text-align: center;
    border-radius: 10px;
    background: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 9999;
    animation: fadeIn 0.4s ease-out, fadeOut 0.4s ease-in 3s forwards;
}

/* Teks warna hijau untuk sukses, merah untuk error */
.alert-success { color: #28a745; }
.alert-error { color: #dc3545; }

@keyframes fadeIn {
    from { opacity: 0; transform: translate(-50%, -20px); }
    to { opacity: 1; transform: translate(-50%, 0); }
}

@keyframes fadeOut {
    to { opacity: 0; transform: translate(-50%, -20px); }
}

/* ===== LAYOUT ===== */
.container-user {
    display: grid;
    grid-template-columns: 1fr 330px; /* kiri besar, kanan edit */
    gap: 25px;
    margin: 20px;
    align-items: start;
}

/* ===== BOX ===== */
.box {
    background: white;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* ===== Title ===== */
.title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 15px;
}

/* ===== BUTTONS ===== */
.btn-add {
    padding: 8px 14px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    display: inline-block;
    margin-bottom: 15px;
    cursor: pointer;
}

.btn-edit {
    padding: 6px 10px;
    background: #007bff;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    margin-right: 4px;
}

.btn-delete {
    padding: 6px 10px;
    background: #dc3545;
    color: white;
    border-radius: 6px;
    text-decoration: none;
}

/* ===== FORM ===== */
input, select {
    width: 100%;
    padding: 7px;
    margin-bottom: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.btn-save {
    width: 100%;
    padding: 8px;
    background: #0d6efd;
    border: none;
    color: white;
    border-radius: 6px;
    cursor: pointer;
}

/* ===== TABLE ===== */
table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #343a40;
    color: white;
    padding: 10px;
}

td {
    padding: 10px;
    border-bottom: 1px solid #eee;
}
</style>

<!-- ===== Alerts ===== -->
<?php if(isset($_SESSION['success'])): ?>
<div class="alert alert-success">
    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
<div class="alert alert-error">
    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
</div>
<?php endif; ?>

<script>
// Hapus alert setelah animasi selesai
document.querySelectorAll('.alert').forEach(alert => {
    alert.addEventListener('animationend', e => {
        if (e.animationName === 'fadeOut') alert.remove();
    });
});
</script>

<!-- ===== LAYOUT ===== -->
<div class="container-user">

    <!-- ===== LEFT: TABEL USERS ===== -->
    <div class="box">
        <div class="title">User Management</div>

        <!-- Tombol Tambah Dosen -->
        <a class="btn-add" href="index.php?page=admin-dosen-add">Tambah Dosen</a>
        <table>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>NIP/NIM</th>
                <th>Aksi</th>
            </tr>

            <?php foreach($users as $u): ?>
            <tr>
                <td><?= $u['username'] ?></td>
                <td><?= $u['role_name'] ?></td>
                <td><?= $u['identitas'] ?? '-' ?></td>

               <td style="display:flex; gap:10px;">
                <a class="btn-edit"
                href="index.php?page=admin-user&edit=<?= $u['user_id'] ?>">
                Edit
                </a>

                <a class="btn-delete"
                onclick="return confirm('Hapus user ini?')"
                href="index.php?page=admin-user-delete&id=<?= $u['user_id'] ?>">
                Hapus
                </a>
            </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- ===== RIGHT: FORM EDIT ===== -->
    <?php if(isset($userEdit) && $userEdit): ?>
    <div class="box">
        <div class="title">Edit User</div>

        <form method="POST" action="index.php?page=admin-user-update">
            <input type="hidden" name="user_id" value="<?= $userEdit['user_id'] ?>">

            <label>Username</label>
            <input type="text" name="username" value="<?= $userEdit['username'] ?>" required>

            <label>Role</label>
            <select name="role_id" required>
                <option value="1" <?= ($userEdit['role_id']==1?'selected':'') ?>>Admin</option>
                <option value="2" <?= ($userEdit['role_id']==2?'selected':'') ?>>Operator</option>
                <option value="3" <?= ($userEdit['role_id']==3?'selected':'') ?>>Dosen</option>
                <option value="4" <?= ($userEdit['role_id']==4?'selected':'') ?>>Mahasiswa</option>
            </select>

            <!-- Tombol Simpan dan Cancel Sebelahan -->
            <div style="display:flex; gap:10px; margin-top:10px;">
                <button class="btn-save" style="flex:1;">Update</button>

                <a href="index.php?page=admin-user"
                style="flex:1; background:#6c757d; padding:8px; 
                        color:white; text-align:center; border-radius:6px; 
                        text-decoration:none;">
                Cancel
                </a>
            </div>
        </form>

    </div>
    <?php endif; ?>

</div>
