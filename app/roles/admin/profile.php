<?php
session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("admin");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$user_id = $_SESSION['user']['user_id'] ?? null;
if(!$user_id) die("<div class='alert alert-danger'>Session invalid.</div>");

function safe($x){ return htmlspecialchars($x ?? "-", ENT_QUOTES, 'UTF-8'); }

// ========================================================
// =============== HANDLE UPDATE REQUESTS =================
// ========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---------- UPDATE FOTO ----------
    if (isset($_POST['update_foto'])) {

        if (!empty($_FILES['foto']['name'])) {
            $foto = $_FILES['foto'];

            $allowed = ['image/jpeg','image/jpg'];
            $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));

            if (in_array($foto['type'], $allowed) && ($ext === "jpg" || $ext === "jpeg")) {

                $dest = __DIR__ . "/../../../public/uploads/profiles/" . $user_id . ".jpg";
                move_uploaded_file($foto['tmp_name'], $dest);

                $_SESSION['flash_success'] = "Foto profil berhasil diperbarui.";
            } else {
                $_SESSION['flash_error'] = "Format foto harus JPG.";
            }
        }

        header("Location: profile.php");
        exit;
    }

    // ---------- UPDATE PROFIL ----------
    if (isset($_POST['update_profile'])) {

        $stmt = $conn->prepare("
            UPDATE dosen SET nama=?, nidn=?, email=?, jabatan=?
            WHERE nip=?
        ");
        $stmt->execute([
            $_POST['nama'],
            $_POST['nidn'],
            $_POST['email'],
            $_POST['jabatan'],
            $_POST['nip']
        ]);

        $_SESSION['flash_success'] = "Profil berhasil diperbarui.";
        header("Location: profile.php");
        exit;
    }

    // ---------- UPDATE LOGIN ----------
    if (isset($_POST['update_account'])) {

        $sql = "UPDATE users SET username=?";
        $params = [ $_POST['username'] ];

        if (!empty($_POST['password'])) {
            $sql .= ", password=?";
            $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE user_id=?";
        $params[] = $user_id;

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        $_SESSION['flash_success'] = "Akun berhasil diperbarui.";
        header("Location: profile.php");
        exit;
    }
}

// ========================================================
// ====================== FETCH DATA =======================
// ========================================================
$stmt = $conn->prepare("
    SELECT u.user_id, u.username, u.nip,
           d.nama AS nama_dosen, d.nidn, d.email AS email_dosen, d.jabatan
    FROM users u
    LEFT JOIN dosen d ON d.nip = u.nip
    WHERE u.user_id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// --- Roles ---
$stmtRole = $conn->prepare("
    SELECT r.role_name 
    FROM user_roles ur 
    JOIN roles r ON r.role_id = ur.role_id
    WHERE ur.user_id=?
");
$stmtRole->execute([$user_id]);
$roles = $stmtRole->fetchAll(PDO::FETCH_COLUMN);

// --- Role Priority ---
$priority = ["ketua lab","admin","operator","dosen"];
$currentRole = "Tidak Ada";

foreach ($priority as $p) {
    foreach ($roles as $r) {
        if (strtolower($p) === strtolower($r)) {
            $currentRole = ucfirst($r);
            break 2;
        }
    }
}

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<style>
.edit-icon {
    cursor: pointer;
    margin-left: 8px;
    color: #0d6efd;
}
.save-btn, .cancel-btn {
    display: none;
    margin-left: 6px;
    cursor: pointer;
}
.edit-row.editing .text-value { display:none; }
.edit-row.editing .edit-input { display:block !important; }
.edit-row.editing .save-btn,
.edit-row.editing .cancel-btn { display:inline-block !important; }
</style>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-person-circle"></i> Profil Anda
</h2>

<?php if($flash_success): ?>
<div class="alert alert-success"><?= safe($flash_success) ?></div>
<?php endif; ?>

<?php if($flash_error): ?>
<div class="alert alert-danger"><?= safe($flash_error) ?></div>
<?php endif; ?>

<div class="card shadow-sm border-0 p-4">

    <!-- =================== FOTO PROFIL =================== -->
    <div class="text-center mb-4">
        <img id="previewFoto"
             src="../../../public/uploads/profiles/<?= $user['user_id'] ?>.jpg"
             onerror="this.src='../../../public/assets/img/default-user.png';"
             class="rounded-circle border"
             style="width:140px;height:140px;object-fit:cover;">

        <form method="POST" enctype="multipart/form-data" class="mt-3">
            <input type="hidden" name="update_foto" value="1">

            <label class="btn btn-sm btn-outline-primary">
                Ganti Foto
                <input type="file" name="foto" accept=".jpg,.jpeg" hidden onchange="previewImg(this)">
            </label>

            <button class="btn btn-sm btn-primary">
                <i class="bi bi-upload"></i> Upload
            </button>
        </form>

        <h5 class="fw-bold mt-3"><?= safe($user['nama_dosen'] ?: $user['username']) ?></h5>
        <span class="badge bg-primary px-3 py-2"><?= safe($currentRole) ?></span>
    </div>

    <hr>

    <!-- =================== DATA DIRI (EDIT PER KOLOM) =================== -->
    <h6 class="fw-semibold text-primary mb-3">
        <i class="bi bi-card-list"></i> Data Diri
    </h6>

    <form method="POST">
        <input type="hidden" name="update_profile" value="1">
        <input type="hidden" name="nip" value="<?= safe($user['nip']) ?>">

        <table class="table table-borderless">

            <tr>
                <th width="30%">NIP</th>
                <td><strong><?= safe($user['nip']) ?></strong></td>
            </tr>

            <?php
            $fields = [
                "Nama" => ["nama", $user["nama_dosen"]],
                "NIDN" => ["nidn", $user["nidn"]],
                "Email" => ["email", $user["email_dosen"]],
                "Jabatan" => ["jabatan", $user["jabatan"]],
            ];

            foreach ($fields as $label => $data):
                [$field, $value] = $data;
            ?>
            <tr class="edit-row">
                <th><?= $label ?></th>
                <td>
                    <span class="text-value"><?= safe($value) ?></span>

                    <input class="form-control edit-input" 
                           name="<?= $field ?>" 
                           style="display:none"
                           value="<?= safe($value) ?>">

                    <i class="bi bi-pencil-square edit-icon"></i>


                    <button type="button" class="btn btn-sm btn-danger cancel-btn mt-2">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <button class="btn btn-primary mt-2">
            <i class="bi bi-save"></i> Simpan Semua Perubahan
        </button>
    </form>

    <hr>

    <!-- =================== LOGIN SETTING =================== -->
    <h6 class="fw-semibold text-primary mb-3">
        <i class="bi bi-lock-fill"></i> Pengaturan Login
    </h6>

    <form method="POST">
        <input type="hidden" name="update_account" value="1">

        <div class="mb-3">
            <label class="fw-semibold">Username</label>
            <input class="form-control" name="username" value="<?= safe($user['username']) ?>">
        </div>

        <div class="mb-3">
            <label class="fw-semibold">Password Baru (opsional)</label>
            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak mengganti">
        </div>

        <button class="btn btn-primary">
            <i class="bi bi-save"></i> Update Login
        </button>
    </form>

</div>

<script>
// FOTO PREVIEW
function previewImg(input){
    if(input.files && input.files[0]){
        const reader = new FileReader();
        reader.onload = e => document.getElementById("previewFoto").src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}

// EDIT PER KOLOM
document.querySelectorAll(".edit-icon").forEach(icon => {
    icon.addEventListener("click", () => {
        const row = icon.closest(".edit-row");
        row.classList.add("editing");
    });
});

document.querySelectorAll(".cancel-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        const row = btn.closest(".edit-row");
        row.classList.remove("editing");
    });
});
</script>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
