<?php
// app/roles/mahasiswa/profile.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("mahasiswa");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

function safe($x){ return htmlspecialchars($x ?? "-", ENT_QUOTES, 'UTF-8'); }

// Ambil identitas dari session
$user_id = $_SESSION['user']['user_id'] ?? null;
$nim     = $_SESSION['user']['nim'] ?? null;

if(!$nim){
    die("<div class='alert alert-danger'>Session error: NIM tidak ditemukan.</div>");
}

// =======================================================
// ================ HANDLE UPDATE REQUESTS ================
// =======================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---------- UPDATE FOTO ----------
    if (isset($_POST['update_foto'])) {

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

                $_SESSION['flash_success'] = "Foto berhasil diperbarui.";
            } else {
                $_SESSION['flash_error'] = "Format foto harus JPG atau PNG.";
            }
        }

        header("Location: profile.php");
        exit;
    }

    // ---------- UPDATE DATA MAHASISWA ----------
    if (isset($_POST['update_profile'])) {

        $stmt = $conn->prepare("
            UPDATE mahasiswa 
            SET nama=?, email=?, prodi=?, angkatan=?, status=?
            WHERE nim=?
        ");

        $stmt->execute([
            $_POST['nama'] ?? '',
            $_POST['email'] ?? '',
            $_POST['prodi'] ?? '',
            $_POST['angkatan'] ?? '',
            $_POST['status'] ?? 'aktif',
            $nim
        ]);

        $_SESSION['flash_success'] = "Profil berhasil diperbarui.";
        header("Location: profile.php");
        exit;
    }
}

// =======================================================
// ===================== FETCH DATA =======================
// =======================================================

$stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE nim=? LIMIT 1");
$stmt->execute([$nim]);
$m = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$m){
    die("<div class='alert alert-danger'>Data mahasiswa tidak ditemukan.</div>");
}

// Pembimbing jika ada
$pembimbing = null;
if (!empty($m['pembimbing_user_id'])) {

    $stmtP = $conn->prepare("
        SELECT d.nama 
        FROM users u
        LEFT JOIN dosen d ON d.nip = u.nip
        WHERE u.user_id=?
    ");
    $stmtP->execute([$m['pembimbing_user_id']]);
    $pembimbing = $stmtP->fetch(PDO::FETCH_ASSOC);
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
    <i class="bi bi-person-circle"></i> Profil Mahasiswa
</h2>

<?php if($flash_success): ?>
<div class="alert alert-success"><?= safe($flash_success) ?></div>
<?php endif; ?>

<?php if($flash_error): ?>
<div class="alert alert-danger"><?= safe($flash_error) ?></div>
<?php endif; ?>

<div class="card shadow-sm border-0 p-4">

    <!-- FOTO PROFIL -->
    <div class="text-center mb-4">
        <img id="previewFoto"
             src="/lab-ivss/public/uploads/profiles/<?= safe($m['foto'] ?: $nim.'.jpg') ?>"
             onerror="this.src='/lab-ivss/public/assets/img/default-user.png';"
             class="rounded-circle border shadow-sm"
             style="width:140px;height:140px;object-fit:cover;">

        <form method="POST" enctype="multipart/form-data" class="mt-3">
            <input type="hidden" name="update_foto" value="1">

            <label class="btn btn-sm btn-outline-primary">
                Ganti Foto
                <input type="file" name="foto" accept=".jpg,.jpeg,.png" hidden onchange="previewImg(this)">
            </label>

            <button class="btn btn-sm btn-primary">
                <i class="bi bi-upload"></i> Upload
            </button>
        </form>

        <h5 class="fw-bold mt-3"><?= safe($m['nama']) ?></h5>

        <span class="badge bg-primary px-3 py-2">
            Mahasiswa <?= safe(ucfirst($m['kategori'])) ?>
        </span>

        <?php if($pembimbing): ?>
            <div class="small text-muted mt-1">
                <i class="bi bi-person-check"></i> Pembimbing: <?= safe($pembimbing['nama']) ?>
            </div>
        <?php endif; ?>
    </div>

    <hr>

    <!-- DATA PROFIL -->
    <h6 class="fw-semibold text-primary mb-3">
        <i class="bi bi-card-list"></i> Data Mahasiswa
    </h6>

    <form method="POST">
    <input type="hidden" name="update_profile" value="1">

    <table class="table table-borderless">

        <tr>
            <th width="30%">NIM</th>
            <td><strong><?= safe($m['nim']) ?></strong></td>
        </tr>

        <?php
        $fields = [
            "Nama"     => ["nama",     $m["nama"]],
            "Email"    => ["email",    $m["email"]],
            "Prodi"    => ["prodi",    $m["prodi"]],
            "Angkatan" => ["angkatan", $m["angkatan"]],
        ];

        foreach ($fields as $label => $data):
            [$field, $value] = $data;
        ?>
        <tr class="edit-row">
            <th><?= $label ?></th>
            <td>
                <span class="text-value"><?= safe($value) ?></span>

                <!-- editable -->
                <input class="form-control edit-input" 
                       style="display:none"
                       name="<?= $field ?>" 
                       value="<?= safe($value) ?>">

                <i class="bi bi-pencil-square edit-icon"></i>

                <button type="button" class="btn btn-sm btn-danger cancel-btn mt-2">
                    <i class="bi bi-x-lg"></i>
                </button>
            </td>
        </tr>
        <?php endforeach; ?>

        <tr>
            <th>Tanggal Join</th>
            <td><?= safe($m['tanggal_join']) ?></td>
        </tr>

    </table>

    <button class="btn btn-primary mt-3">
        <i class="bi bi-save"></i> Simpan Perubahan
    </button>

    </form>

</div>

<script>
// PREVIEW FOTO
function previewImg(input){
    if(input.files && input.files[0]){
        const reader = new FileReader();
        reader.onload = e => document.getElementById('previewFoto').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}

// TOGGLE EDIT
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
