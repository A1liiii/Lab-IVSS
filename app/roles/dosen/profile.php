<?php
// app/roles/dosen/profile.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("dosen");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

function safe($v){ return htmlspecialchars($v ?? "-", ENT_QUOTES,'UTF-8'); }

$user_id = $_SESSION['user']['user_id'] ?? null;
if (!$user_id) die("User not found.");

// =====================================================
// HANDLE POST
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Upload foto
    if ($_POST['action'] === 'upload_photo') {
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = "Foto gagal diupload.";
        } else {
            $file = $_FILES['photo'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, ['jpg','jpeg'])) {
                $_SESSION['flash_error'] = "Foto harus JPG.";
            } else {
                $dest = __DIR__ . "/../../../public/uploads/profiles/" . $user_id . ".jpg";
                move_uploaded_file($file['tmp_name'], $dest);
                $_SESSION['flash_success'] = "Foto berhasil diperbarui.";
            }
        }
        header("Location: profile.php"); exit;
    }

    // Update data dosen
    if ($_POST['action'] === 'update_dosen') {
        $stmt = $conn->prepare("
            UPDATE dosen SET nama=?, nidn=?, email=?, jabatan=?, pendidikan=? 
            WHERE nip = ?
        ");
        $stmt->execute([
            $_POST['nama'], $_POST['nidn'], $_POST['email'],
            $_POST['jabatan'], $_POST['pendidikan'], $_POST['nip']
        ]);
        $_SESSION['flash_success'] = "Data dosen diperbarui.";
        header("Location: profile.php"); exit;
    }

    // Update user account
    if ($_POST['action'] === 'update_user') {
        $username = trim($_POST['username']);
        $pwd = trim($_POST['password']);

        if ($pwd === "") {
            $stmt = $conn->prepare("UPDATE users SET username=? WHERE user_id=?");
            $stmt->execute([$username, $user_id]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, password=? WHERE user_id=?");
            $stmt->execute([$username, password_hash($pwd, PASSWORD_DEFAULT), $user_id]);
        }
        $_SESSION['flash_success'] = "Akun diperbarui.";
        header("Location: profile.php"); exit;
    }
}

    // =====================================================
    // FETCH USER + DOSEN DATA
    // =====================================================
    $stmt = $conn->prepare("
    SELECT 
        u.username,
        u.nip,
        r.role_name AS role
    FROM users u
    LEFT JOIN user_roles ur ON ur.user_id = u.user_id
    LEFT JOIN roles r ON r.role_id = ur.role_id
    WHERE u.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) die("User not found.");
$dosen = null;
if ($user['nip']) {
    $stmt = $conn->prepare("SELECT * FROM dosen WHERE nip=? LIMIT 1");
    $stmt->execute([$user['nip']]);
    $dosen = $stmt->fetch(PDO::FETCH_ASSOC);
}

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-person-circle"></i> Profil Saya
</h2>

<?php if($flash_success): ?>
<div class="alert alert-success"><?= safe($flash_success) ?></div>
<?php endif; ?>

<?php if($flash_error): ?>
<div class="alert alert-danger"><?= safe($flash_error) ?></div>
<?php endif; ?>

<!-- ==================== PROFILE HEADER CARD ==================== -->
<div class="card shadow-sm p-4 text-center mb-4">
    <img src="../../../public/uploads/profiles/<?= $user_id ?>.jpg"
         onerror="this.src='../../../public/assets/img/default-user.png';"
         class="rounded-circle mx-auto d-block"
         style="width:130px;height:130px;object-fit:cover;">

    <h4 class="fw-bold mt-3"><?= safe($dosen['nama'] ?? 'Nama Tidak Ada') ?></h4>
    <div class="text-muted small text-uppercase"><?= safe($user['role']) ?></div>

    <form method="POST" enctype="multipart/form-data" class="mt-3" style="max-width:300px;margin:auto;">
        <input type="hidden" name="action" value="upload_photo">
        <input type="file" name="photo" class="form-control mb-2" accept=".jpg,.jpeg" required>
        <button class="btn btn-primary w-100">Upload Foto</button>
    </form>
</div>

<!-- ==================== DATA DOSEN ==================== -->
<div class="card shadow-sm p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="fw-semibold mb-0">Data Dosen</h5>
        <button class="btn btn-light" onclick="toggleDosenEdit()">
            <span class="iconify" data-icon="mdi:pencil"></span>
        </button>
    </div>

    <div id="dosenView" class="mt-3">
        <p><strong>NIP:</strong> <?= safe($dosen['nip']) ?></p>
        <p><strong>Nama:</strong> <?= safe($dosen['nama']) ?></p>
        <p><strong>NIDN:</strong> <?= safe($dosen['nidn']) ?></p>
        <p><strong>Email:</strong> <?= safe($dosen['email']) ?></p>
        <p><strong>Jabatan:</strong> <?= safe($dosen['jabatan']) ?></p>
    </div>

    <form method="POST" id="dosenEdit" style="display:none;" class="mt-3">
        <input type="hidden" name="action" value="update_dosen">
        <input type="hidden" name="nip" value="<?= safe($dosen['nip']) ?>">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small">Nama</label>
                <input name="nama" class="form-control" value="<?= safe($dosen['nama']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label small">NIDN</label>
                <input name="nidn" class="form-control" value="<?= safe($dosen['nidn']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label small">Email</label>
                <input name="email" type="email" class="form-control" value="<?= safe($dosen['email']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label small">Jabatan</label>
                <input name="jabatan" class="form-control" value="<?= safe($dosen['jabatan']) ?>">
            </div>
        </div>

        <button class="btn btn-primary mt-3">Simpan Perubahan</button>
    </form>
</div>

<!-- ==================== DATA USER ==================== -->
<div class="card shadow-sm p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="fw-semibold mb-0">Data User</h5>
        <button class="btn btn-light" onclick="toggleUserEdit()">
            <span class="iconify" data-icon="mdi:pencil"></span>
        </button>
    </div>

    <div id="userView" class="mt-3">
        <p><strong>Username:</strong> <?= safe($user['username']) ?></p>
        <p><strong>Role:</strong> <?= safe($user['role']) ?></p>
    </div>

    <form method="POST" id="userEdit" style="display:none;" class="mt-3">
        <input type="hidden" name="action" value="update_user">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small">Username</label>
                <input name="username" class="form-control" value="<?= safe($user['username']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label small">Silahkan request kepada admin untuk password baru</label>
            </div>
        </div>

        <button class="btn btn-primary mt-3">Simpan Perubahan</button>
    </form>
</div>

<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

<script>
function toggleDosenEdit(){
    document.getElementById("dosenView").style.display = "none";
    document.getElementById("dosenEdit").style.display = "block";
}
function toggleUserEdit(){
    document.getElementById("userView").style.display = "none";
    document.getElementById("userEdit").style.display = "block";
}
</script>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
