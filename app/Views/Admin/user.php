<style>
/* ===== ALERT (TOAST) ===== */
.alert {
    position: fixed;          /* tetap di posisi atas meski scroll */
    top: 20px;                /* jarak dari atas layar */
    left: 50%;                /* tengah horizontal */
    transform: translateX(-50%); /* geser setengah lebar untuk benar-benar tengah */
    min-width: 250px;
    max-width: 350px;
    padding: 12px 20px;
    color: #000;              /* default, nanti diubah per tipe */
    font-weight: bold;
    text-align: center;
    border-radius: 10px;
    background: white;        /* background putih */
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 9999;
    animation: fadeIn 0.4s ease-out, fadeOut 0.4s ease-in 3s forwards;
}

/* Teks warna hijau untuk sukses, merah untuk error */
.alert-success { color: #28a745; }
.alert-error   { color: #dc3545; }

@keyframes fadeIn {
    from { opacity: 0; transform: translate(-50%, -20px); }
    to   { opacity: 1; transform: translate(-50%, 0); }
}

@keyframes fadeOut {
    to { opacity: 0; transform: translate(-50%, -20px); }
}

/* ===== Layout Grid ===== */
.container-user {
    display: grid;
    grid-template-columns: 330px 1fr; /* kiri kecil, kanan besar */
    gap: 25px;
    margin: 20px;
    align-items: start; /* FORM TIDAK IKUT TURUN */
}

/* ===== Box ===== */
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
    margin-bottom: 10px;
}

/* ===== Inputs ===== */
input, select {
    width: 100%;
    padding: 6px;             
    margin-bottom: 6px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

/* ===== Buttons ===== */
.btn-save {
    background: #28a745;
    color: white;
    width: 100%;
    padding: 6px 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
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

/* ===== Table ===== */
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
    alert.addEventListener('animationend', (e) => {
        if (e.animationName === 'fadeOut') {
            alert.remove();
        }
    });
});
</script>

<div class="container-user">

    <!-- LEFT COLUMN -->
    <div class="left-column">

        <!-- FORM TAMBAH -->
        <div class="box">
            <div class="title">Tambah User</div>

            <form method="POST" action="index.php?page=admin-user-store">
                <label>Role:</label>
                <select name="role_id" required>
                    <option value="">-- pilih role --</option>
                    <option value="1">Admin</option>
                    <option value="2">Operator</option>
                    <option value="3">Dosen</option>
                    <option value="4">Mahasiswa</option>
                </select>

                <label>NIM / NIP:</label>
                <input type="text" name="nim" required>

                <label>Username:</label>
                <input type="text" name="username" required>

                <label>Password:</label>
                <input type="password" name="password" required>

                <button class="btn-save">Simpan</button>
            </form>
        </div>

        <!-- FORM EDIT -->
        <?php if (!empty($userEdit)): ?>
        <div class="box">
            <div class="title">Edit User</div>

            <form method="POST" action="index.php?page=admin-user-update">
                <input type="hidden" name="user_id" value="<?= $userEdit['user_id'] ?>">

                <label>Username:</label>
                <input type="text" name="username" value="<?= $userEdit['username'] ?>" required>

                <div style="display:flex; gap:10px;">
                    <button class="btn-save" style="flex:1;">Update</button>

                    <a href="index.php?page=admin-user"
                       style="flex:1; text-align:center; background:#6c757d; color:white;
                              padding:6px 10px; border-radius:6px; text-decoration:none; font-size:13px;">
                       Cancel
                    </a>
                </div>
            </form>
        </div>
        <?php endif; ?>

    </div>

    <!-- RIGHT COLUMN (TABEL SCROLL) -->
    <div class="box table-wrapper">
        <div class="title">User Management</div>

        <table>
            <tr>
                <th>Username</th>
                <th>Role</th>
                <th>NIP/NIM</th>
                <th>Aksi</th>
            </tr>

            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['username'] ?></td>
                <td><?= $u['role_name'] ?></td>
                <td><?= $u['identitas'] ?? '-' ?></td>
                <td>
                    <a class="btn-edit" href="index.php?page=admin-user&edit=<?= $u['user_id'] ?>">Edit</a>
                    <a class="btn-delete" href="index.php?page=admin-user-delete&id=<?= $u['user_id'] ?>"
                       onclick="return confirm('Hapus user ini?')">
                       Hapus
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

</div>
