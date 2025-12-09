<?php
session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("admin");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

// Resolve ID user aktif
$user_id = $_SESSION['user']['user_id'] ?? null;

if(!$user_id){
    die("<div class='alert alert-danger'>Session invalid — user tidak ditemukan.</div>");
}

// ================== FETCH DATA USER ==================
$stmt = $conn->prepare("
    SELECT 
        u.user_id,
        u.username,
        u.nip,
        d.nidn,
        d.nama AS nama_dosen,
        d.email AS email_dosen,
        d.jabatan AS jabatan_dosen
    FROM users u
    LEFT JOIN dosen d ON d.nip = u.nip
    WHERE u.user_id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user){
    die("<div class='alert alert-danger'>User tidak ditemukan di database.</div>");
}

// ================== FETCH ROLE ==================
$stmtRole = $conn->prepare("
    SELECT r.role_name
    FROM user_roles ur
    JOIN roles r ON r.role_id = ur.role_id
    WHERE ur.user_id = ?
");
$stmtRole->execute([$user_id]);
$roles = $stmtRole->fetchAll(PDO::FETCH_COLUMN);

// helper function
function safe($x){ return htmlspecialchars($x ?? "-", ENT_QUOTES, 'UTF-8'); }

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-person-circle"></i> Profil Anda
</h2>

<div class="card shadow-sm border-0 p-4">

    <!-- FOTO PROFIL -->
    <div class="text-center mb-4">
        <img src="/public/uploads/profiles/<?= $user['user_id'] ?>.jpg"
             onerror="this.src='/public/assets/img/default-user.png';"
             class="rounded-circle border"
             style="width:120px;height:120px;object-fit:cover;">

        <h5 class="fw-bold mt-3"><?= safe($user['nama_dosen'] ?: $user['username']) ?></h5>

        <div class="mt-2">
            <?php foreach($roles as $r): ?>
                <span class="badge bg-secondary mx-1"><?= safe($r) ?></span>
            <?php endforeach; ?>
        </div>
    </div>


    <hr>

    <!-- DATA DIRI ADMIN -->
    <h6 class="fw-semibold text-primary mb-3">
        <i class="bi bi-card-list"></i> Data
    </h6>

    <table class="table table-borderless">
        <tr>
            <th width="30%">NIP</th>
            <td><?= safe($user['nip']) ?></td>
        </tr>
        <tr>
            <th>Nama</th>
            <td><?= safe($user['nama_dosen']) ?></td>
        </tr>
        <tr>
            <th>NIDN</th>
            <td><?= safe($user['nidn']) ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= safe($user['email_dosen']) ?></td>
        </tr>
        <tr>
            <th>Jabatan</th>
            <td><?= safe($user['jabatan_dosen']) ?></td>
        </tr>
    </table>


    <hr>

    <!-- UPDATE AKUN LOGIN -->
    <h6 class="fw-semibold text-primary mb-3">
        <i class="bi bi-pencil-square"></i> Pengaturan Login
    </h6>

    <form action="update_account.php" method="POST">
        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

        <div class="mb-3">
            <label class="fw-semibold">Username</label>
            <input type="text" name="username" class="form-control"
                   value="<?= safe($user['username']) ?>">
        </div>

        <div class="mb-3">
            <label class="fw-semibold">Password Baru (opsional)</label>
            <input type="password" name="password" class="form-control"
                   placeholder="Kosongkan jika tidak mengganti">
        </div>

        <button type="submit" name="update" class="btn btn-primary">
            <i class="bi bi-save"></i> Simpan Perubahan
        </button>
    </form>

</div>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
