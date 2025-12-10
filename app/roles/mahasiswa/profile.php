<?php
// app/roles/mahasiswa/profile.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("mahasiswa");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

function safe($v) { return htmlspecialchars($v ?? "-", ENT_QUOTES, 'UTF-8'); }

$user_id = $_SESSION['user']['user_id'] ?? null;
if (!$user_id) die("User not found.");

// =====================================================
// HANDLE POST
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Upload foto
    if (isset($_POST['action']) && $_POST['action'] === 'upload_photo') {
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = "Foto gagal diupload.";
        } else {
            $file = $_FILES['photo'];
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, ['jpg','jpeg'])) {
                $_SESSION['flash_error'] = "Foto harus JPG.";
            } else {
                $destDir = __DIR__ . "/../../../public/uploads/profiles/";
                if (!is_dir($destDir)) {
                    @mkdir($destDir, 0777, true);
                }
                $dest = $destDir . $user_id . ".jpg";
                move_uploaded_file($file['tmp_name'], $dest);
                $_SESSION['flash_success'] = "Foto berhasil diperbarui.";
            }
        }
        header("Location: profile.php"); exit;
    }

    // Update data mahasiswa
    if (isset($_POST['action']) && $_POST['action'] === 'update_mahasiswa') {
        // sesuaikan field dengan tabel mahasiswa-mu
        $stmt = $conn->prepare("
            UPDATE mahasiswa 
               SET nim = ?, nama = ?, email = ?, prodi = ?, angkatan = ?
             WHERE user_id = ?
        ");
        $stmt->execute([
            $_POST['nim'] ?? null,
            $_POST['nama'] ?? null,
            $_POST['email'] ?? null,
            $_POST['prodi'] ?? null,
            $_POST['angkatan'] ?? null,
            $user_id
        ]);

        $_SESSION['flash_success'] = "Data mahasiswa diperbarui.";
        header("Location: profile.php"); exit;
    }

    // Update user account
    if (isset($_POST['action']) && $_POST['action'] === 'update_user') {
        $username = trim($_POST['username'] ?? '');
        $pwd      = trim($_POST['password'] ?? '');

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
// FETCH USER + MAHASISWA DATA
// =====================================================
$stmt = $conn->prepare("
    SELECT 
        u.username,
        r.role_name AS role
    FROM users u
    LEFT JOIN user_roles ur ON ur.user_id = u.user_id
    LEFT JOIN roles r       ON r.role_id = ur.role_id
    WHERE u.user_id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) die("User not found.");

$stmt = $conn->prepare("
    SELECT nim, nama, email, prodi, angkatan 
    FROM mahasiswa 
    WHERE user_id = ?
    LIMIT 1
");
$stmt->execute([$user_id]);
$mhs = $stmt->fetch(PDO::FETCH_ASSOC);

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-person-circle"></i> Profil Saya
</h2>

<?php if ($flash_success): ?>
  <div class="alert alert-success"><?= safe($flash_success) ?></div>
<?php endif; ?>

<?php if ($flash_error): ?>
  <div class="alert alert-danger"><?= safe($flash_error) ?></div>
<?php endif; ?>

<!-- ==================== PROFILE HEADER CARD ==================== -->
<div class="card shadow-sm p-4 text-center mb-4">
  <img src="../../../public/uploads/profiles/<?= $user_id ?>.jpg"
       onerror="this.src='../../../public/assets/img/default-user.png';"
       class="rounded-circle mx-auto d-block"
       style="width:130px;height:130px;object-fit:cover;">

  <h4 class="fw-bold mt-3"><?= safe($mhs['nama'] ?? 'Nama Tidak Ada') ?></h4>
  <div class="text-muted small text-uppercase"><?= safe($user['role']) ?></div>

  <form method="POST" enctype="multipart/form-data" class="mt-3" style="max-width:300px;margin:auto;">
    <input type="hidden" name="action" value="upload_photo">
    <input type="file" name="photo" class="form-control mb-2" accept=".jpg,.jpeg" required>
    <button class="btn btn-primary w-100">Upload Foto</button>
  </form>
</div>

<!-- ==================== DATA MAHASISWA ==================== -->
<div class="card shadow-sm p-4 mb-4">
  <div class="d-flex justify-content-between align-items-center">
   <h5 class="fw-semibold mb-0 text-primary">
    <i class="bi bi-mortarboard-fill me-2"></i> Data Mahasiswa
    </h5>
    <button class="btn btn-light" onclick="toggleMhsEdit()">
      <span class="iconify" data-icon="mdi:pencil"></span>
    </button>
  </div>

  <!-- tampilan view -->
  <div id="mhsView" class="mt-3">
    <p><strong>NIM:</strong> <?= safe($mhs['nim'] ?? null) ?></p>
    <p><strong>Nama:</strong> <?= safe($mhs['nama'] ?? null) ?></p>
    <p><strong>Email:</strong> <?= safe($mhs['email'] ?? null) ?></p>
    <p><strong>Program Studi:</strong> <?= safe($mhs['prodi'] ?? null) ?></p>
    <p><strong>Angkatan:</strong> <?= safe($mhs['angkatan'] ?? null) ?></p>
  </div>

  <!-- tampilan edit -->
  <form method="POST" id="mhsEdit" style="display:none;" class="mt-3">
    <input type="hidden" name="action" value="update_mahasiswa">

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label small">NIM</label>
        <input name="nim" class="form-control" value="<?= safe($mhs['nim'] ?? null) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label small">Nama</label>
        <input name="nama" class="form-control" value="<?= safe($mhs['nama'] ?? null) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label small">Email</label>
        <input name="email" type="email" class="form-control" value="<?= safe($mhs['email'] ?? null) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label small">Program Studi</label>
        <input name="prodi" class="form-control" value="<?= safe($mhs['prodi'] ?? null) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label small">Angkatan</label>
        <input name="angkatan" class="form-control" value="<?= safe($mhs['angkatan'] ?? null) ?>">
      </div>
    </div>

    <button class="btn btn-primary mt-3">Simpan Perubahan</button>
    <button type="button" class="btn btn-secondary mt-3 ms-2" onclick="cancelMhsEdit()">Batal</button>
  </form>
</div>

<!-- ==================== DATA USER ==================== -->
<div class="card shadow-sm p-4 mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <h5 class="fw-semibold mb-0 text-primary">
    <i class="bi bi-person-badge-fill me-2"></i> Data User
    </h5>
    <button class="btn btn-light" onclick="toggleUserEdit()">
      <span class="iconify" data-icon="mdi:pencil"></span>
    </button>
  </div>

  <!-- view -->
  <div id="userView" class="mt-3">
    <p><strong>Username:</strong> <?= safe($user['username']) ?></p>
    <p><strong>Role:</strong> <?= safe($user['role']) ?></p>
  </div>

  <!-- edit -->
  <form method="POST" id="userEdit" style="display:none;" class="mt-3">
    <input type="hidden" name="action" value="update_user">

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label small">Username</label>
        <input name="username" class="form-control" value="<?= safe($user['username']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label small">Password Baru (opsional)</label>
        <input name="password" type="password" class="form-control" placeholder="Kosongkan jika tidak diganti">
      </div>
    </div>

    <button class="btn btn-primary mt-3">Simpan Perubahan</button>
    <button type="button" class="btn btn-secondary mt-3 ms-2" onclick="cancelUserEdit()">Batal</button>
  </form>
</div>

<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
<script>
function toggleMhsEdit() {
  document.getElementById('mhsView').style.display = 'none';
  document.getElementById('mhsEdit').style.display = 'block';
}
function toggleUserEdit() {
  document.getElementById('userView').style.display = 'none';
  document.getElementById('userEdit').style.display = 'block';
}
function toggleMhsEdit() {
    document.getElementById('mhsView').style.display = 'none';
    document.getElementById('mhsEdit').style.display = 'block';
}

function cancelMhsEdit() {
    document.getElementById('mhsEdit').style.display = 'none';
    document.getElementById('mhsView').style.display = 'block';
}

function toggleUserEdit() {
    document.getElementById('userView').style.display = 'none';
    document.getElementById('userEdit').style.display = 'block';
}

function cancelUserEdit() {
    document.getElementById('userEdit').style.display = 'none';
    document.getElementById('userView').style.display = 'block';
}
</script>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
