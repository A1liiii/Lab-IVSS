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
    die("<div class='alert alert-danger'>Data mahasiswa tidak ditemukan di tabel mahasiswa.</div>");
}

// Format tanggal
function formatDate($ts){
    if(!$ts) return "-";
    return date("d M Y, H:i", strtotime($ts));
}

// ========================= HANDLE UPDATE (ADMIN) =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_mahasiswa_admin'])) {

    $allowedStatus = ['aktif','cuti','lulus'];
    $status = in_array($_POST['status'], $allowedStatus) ? $_POST['status'] : 'aktif';

    $stmt = $conn->prepare("
        UPDATE mahasiswa SET
            nama      = ?,
            email     = ?,
            prodi     = ?,
            angkatan  = ?,
            status    = ?
        WHERE nim = ?
    ");

    $stmt->execute([
        $_POST['nama'] ?? '',
        $_POST['email'] ?? '',
        $_POST['prodi'] ?? '',
        $_POST['angkatan'] ?? '',
        $status,
        $nim
    ]);
    // === LOG UPDATE PROFIL MAHASISWA ===
    try {
        $log = $conn->prepare("
            INSERT INTO log_activity (user_id, aksi, deskripsi, waktu)
            VALUES (?, ?, ?, NOW())
        ");
        $log->execute([
            $_SESSION['user']['user_id'], // admin
            'update',
            'Admin Memperbarui profil mahasiswa ' . $mhs['nama'] . ' (' . $nim . ')'
        ]);
    } catch (PDOException $e) {}
    
    $_SESSION['flash_success'] = "Profil mahasiswa berhasil diperbarui.";
    header("Location: detail_mahasiswa.php?user_id=".$user_id);
    exit;
}
// ========================= UPDATE FOTO (ADMIN) =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_foto_admin'])) {

    if (!empty($_FILES['foto']['name'])) {

        $foto = $_FILES['foto'];
        $ext  = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg','jpeg','png'])) {

            $dir = __DIR__ . "/../../../public/uploads/profiles/";
            if(!is_dir($dir)) mkdir($dir, 0777, true);

            $filename = $nim . "." . $ext;
            move_uploaded_file($foto['tmp_name'], $dir . $filename);

            $stmt = $conn->prepare("UPDATE mahasiswa SET foto=? WHERE nim=?");
            $stmt->execute([$filename, $nim]);

            $_SESSION['flash_success'] = "Foto mahasiswa berhasil diperbarui.";
        } else {
            $_SESSION['flash_error'] = "Format foto harus JPG atau PNG.";
        }
    }

    header("Location: detail_mahasiswa.php?user_id=".$user_id);
    exit;
}
ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-mortarboard-fill"></i> Detail Mahasiswa
</h2>

<div class="card shadow-sm border-0 p-4">

    <!-- FOTO PROFIL -->
    <div class="text-center mb-4">
    <?php
    $fotoMhs = $mhs['foto'] 
        ?: (file_exists(__DIR__."/../../../public/uploads/profiles/".$nim.".jpg") ? $nim.".jpg" : null);

    $fotoMhs = $fotoMhs 
        ?: (file_exists(__DIR__."/../../../public/uploads/profiles/".$nim.".png") ? $nim.".png" : null);
    ?>

        <img src="../../../public/uploads/profiles/<?= safe($fotoMhs) ?>"
            onerror="this.src='../../../public/assets/img/default-user.png';"
            class="rounded-circle border"
            style="width:120px;height:120px;object-fit:cover;">
        
        <h5 class="fw-bold mt-3"><?= safe($mhs['nama']) ?></h5>
        <span class="badge bg-info text-dark px-3 py-2">Mahasiswa</span>
    <form method="POST" enctype="multipart/form-data" class="mt-2">
        <input type="hidden" name="update_foto_admin" value="1">

        <label class="btn btn-sm btn-outline-primary">
            Ganti Foto
            <input type="file" name="foto" accept=".jpg,.jpeg,.png" hidden>
        </label>

        <button class="btn btn-sm btn-primary">
            <i class="bi bi-upload"></i> Upload
        </button>
    </form>
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

<h6 class="fw-bold text-primary mb-3">
    <i class="bi bi-pencil-square"></i> Edit Profil Mahasiswa
</h6>

<?php if(!empty($_SESSION['flash_success'])): ?>
<div class="alert alert-success">
    <?= safe($_SESSION['flash_success']) ?>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<form method="POST">
<input type="hidden" name="update_mahasiswa_admin" value="1">

<div class="row g-3">

    <div class="col-md-6">
        <label class="fw-semibold">Nama</label>
        <input type="text" name="nama" class="form-control"
               value="<?= safe($mhs['nama']) ?>" required>
    </div>

    <div class="col-md-6">
        <label class="fw-semibold">Email</label>
        <input type="email" name="email" class="form-control"
               value="<?= safe($mhs['email']) ?>">
    </div>

    <div class="col-md-6">
        <label class="fw-semibold">Program Studi</label>
        <input type="text" name="prodi" class="form-control"
               value="<?= safe($mhs['prodi']) ?>">
    </div>

    <div class="col-md-6">
        <label class="fw-semibold">Angkatan</label>
        <input type="number" name="angkatan" class="form-control"
               value="<?= safe($mhs['angkatan']) ?>">
    </div>

    <div class="col-md-6">
        <label class="fw-semibold">Status Mahasiswa</label>
        <select name="status" class="form-select">
            <option value="aktif" <?= $mhs['status']=='aktif'?'selected':'' ?>>Aktif</option>
            <option value="cuti"  <?= $mhs['status']=='cuti'?'selected':'' ?>>Cuti</option>
            <option value="lulus" <?= $mhs['status']=='lulus'?'selected':'' ?>>Lulus</option>
        </select>
    </div>

</div>

<div class="mt-4">
    <button class="btn btn-primary">
        <i class="bi bi-save"></i> Simpan Perubahan
    </button>
</div>

</form>


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
