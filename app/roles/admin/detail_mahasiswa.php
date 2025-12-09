<?php
session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("admin");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$user_id = $_GET['user_id'] ?? null;
if(!$user_id){
    die("User ID tidak valid.");
}

// helper tampilan aman
function safe($x){ return htmlspecialchars($x ?? "-", ENT_QUOTES, "UTF-8"); }

// ========================= FETCH USER =========================
$stmtUser = $conn->prepare("SELECT username, nim FROM users WHERE user_id = ?");
$stmtUser->execute([$user_id]);
$u = $stmtUser->fetch(PDO::FETCH_ASSOC);

if(!$u || !$u['nim']){
    die("<div class='alert alert-warning'>⚠️ Data mahasiswa tidak ditemukan (NIM kosong / tidak terkait user).</div>");
}

$nim = $u['nim'];

// ========================= FETCH DATA MAHASISWA =========================
$stmtMhs = $conn->prepare("
    SELECT m.nim, m.nama, m.email, m.prodi, m.angkatan, m.status, 
           m.tanggal_join, m.kategori, m.foto
    FROM mahasiswa m
    WHERE m.nim = ?
    LIMIT 1
");

$stmtMhs->execute([$nim]);
$mhs = $stmtMhs->fetch(PDO::FETCH_ASSOC);

if(!$mhs){
    die("<div class='alert alert-danger'>❌ Data mahasiswa tidak ditemukan di tabel mahasiswa.</div>");
}

// Format tanggal
function formatDate($ts){
    if(!$ts) return "-";
    return date("d M Y, H:i", strtotime($ts));
}

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-mortarboard-fill"></i> Detail Mahasiswa
</h2>

<div class="card shadow-sm border-0 p-4">

    <!-- FOTO PROFIL -->
    <div class="text-center mb-4">
        <img src="/public/uploads/profiles/<?= $user_id ?>.jpg"
             onerror="this.src='/public/assets/img/default-user.png';"
             class="rounded-circle border"
             style="width:120px;height:120px;object-fit:cover;">
        
        <h5 class="fw-bold mt-3"><?= safe($mhs['nama']) ?></h5>
        <span class="badge bg-info text-dark px-3 py-2">Mahasiswa</span>
    </div>

    <hr>

    <!-- DATA MAHASISWA -->
    <h6 class="text-primary fw-semibold mb-3">
        <i class="bi bi-card-list"></i> Informasi Mahasiswa
    </h6>

    <table class="table table-borderless">
        <tr>
            <th width="30%">NIM</th>
            <td><?= safe($mhs['nim']) ?></td>
        </tr>
        <tr>
            <th>Nama</th>
            <td><?= safe($mhs['nama']) ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= safe($mhs['email']) ?></td>
        </tr>
        <tr>
            <th>Program Studi</th>
            <td><?= safe($mhs['prodi']) ?></td>
        </tr>
        <tr>
            <th>Angkatan</th>
            <td><?= safe($mhs['angkatan']) ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                <?php if($mhs['status']=="aktif"): ?>
                    <span class="badge bg-success">Aktif</span>
                <?php else: ?>
                    <span class="badge bg-danger"><?= safe($mhs['status']) ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Kategori</th>
            <td><?= safe($mhs['kategori']) ?></td>
        </tr>
        <tr>
            <th>Tanggal Join</th>
            <td><?= formatDate($mhs['tanggal_join']) ?></td>
        </tr>
    </table>

    <hr>

    <!-- PENGATURAN AKUN -->
    <h6 class="fw-bold text-primary mb-3">
        <i class="bi bi-lock-fill"></i> Pengaturan Akun Login
    </h6>

    <form action="update_account.php" method="POST">
        <input type="hidden" name="user_id" value="<?= $user_id ?>">

        <div class="mb-3">
            <label class="fw-semibold">Username</label>
            <input type="text" name="username" class="form-control" value="<?= safe($u['username']) ?>">
        </div>

        <div class="mb-3">
            <label class="fw-semibold">Password Baru (opsional)</label>
            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
        </div>

        <button class="btn btn-primary">
            <i class="bi bi-save"></i> Simpan Perubahan
        </button>
    </form>

    <div class="mt-4">
        <a href="user.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

</div>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
