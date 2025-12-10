<?php
// app/roles/mahasiswa/profile.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("mahasiswa");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

function safe($x){ return htmlspecialchars($x ?? "-", ENT_QUOTES, 'UTF-8'); }

// ===========================
//  AMBIL IDENTITAS LOGIN
// ===========================
$user_id = $_SESSION['user']['user_id'] ?? null;
$nim     = $_SESSION['user']['nim'] ?? null;  // mahasiswa login selalu punya NIM

if(!$nim){
    die("<div class='alert alert-danger'>NIM tidak ditemukan dalam session.</div>");
}

// ===========================
//  HANDLE POST UPDATE
// ===========================
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    // update nama & email
    if(isset($_POST['update_profile'])){
        $stmt = $conn->prepare("
            UPDATE mahasiswa SET nama=?, email=? WHERE nim=?
        ");
        $stmt->execute([$_POST['nama'], $_POST['email'], $nim]);
    }

    // upload foto
    if(isset($_POST['upload_foto'])){
        if(isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK){

            $f = $_FILES['foto'];
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));

            if(!in_array($ext, ['jpg','jpeg','png'])){
                $_SESSION['flash_error'] = "Foto harus JPG atau PNG.";
            } else {
                $dir = __DIR__ . "/../../../public/uploads/profiles/";
                if(!is_dir($dir)) mkdir($dir, 0777, true);

                $filename = $nim.".".$ext;
                move_uploaded_file($f['tmp_name'], $dir . $filename);

                $stmt = $conn->prepare("UPDATE mahasiswa SET foto=? WHERE nim=?");
                $stmt->execute([$filename, $nim]);

                $_SESSION['flash_success'] = "Foto berhasil diperbarui.";
            }
        }
        header("Location: profile.php");
        exit;
    }

    header("Location: profile.php");
    exit;
}

// ===========================
//  FETCH DATA MAHASISWA
// ===========================
$stmt = $conn->prepare("
    SELECT *
    FROM mahasiswa
    WHERE nim = ?
    LIMIT 1
");
$stmt->execute([$nim]);
$m = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$m){
    die("<div class='alert alert-danger'>Data mahasiswa tidak ditemukan.</div>");
}

// ===========================
//  FETCH PEMBIMBING (optional)
// ===========================
$pembimbing = null;
if(!empty($m['pembimbing_user_id'])){
    $stmt = $conn->prepare("
        SELECT d.nama
        FROM users u
        LEFT JOIN dosen d ON d.nip = u.nip
        WHERE u.user_id = ?
    ");
    $stmt->execute([$m['pembimbing_user_id']]);
    $pembimbing = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ===========================
//  RENDER PAGE
// ===========================
ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-person-circle"></i> Profil Saya
</h2>

<!-- FLASH MESSAGES -->
<?php if(isset($_SESSION['flash_success'])): ?>
<div class="alert alert-success"><?= safe($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
<?php endif; ?>

<?php if(isset($_SESSION['flash_error'])): ?>
<div class="alert alert-danger"><?= safe($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
<?php endif; ?>

<div class="card p-4 shadow-sm">

    <!-- FOTO PROFIL + UPLOAD INLINE -->
    <div class="text-center mb-4">
        <img src="../../../public/uploads/profiles/<?= safe($m['foto'] ?: $user_id.'.jpg') ?>"
             onerror="this.src='../../../public/assets/img/default-user.png';"
             class="rounded-circle border shadow-sm"
             style="width:130px;height:130px;object-fit:cover;display:block;margin:auto;">

        <!-- Upload foto langsung di bawah gambar -->
        <form method="POST" enctype="multipart/form-data" class="mt-3">
            <input type="hidden" name="upload_foto">
            <input type="file" name="foto" accept=".jpg,.jpeg,.png" class="form-control mb-2" required>
            <button class="btn btn-sm btn-primary">
                <i class="bi bi-upload"></i> Ganti Foto
            </button>
        </form>

        <!-- Nama -->
        <h5 class="fw-bold mt-3"><?= safe($m['nama']) ?></h5>

        <!-- Pembimbing jika ada -->
        <?php if($pembimbing): ?>
            <div class="small text-muted">
                <i class="bi bi-person-check"></i> Pembimbing: <?= safe($pembimbing['nama']) ?>
            </div>
        <?php endif; ?>
    </div>

    <hr>

    <!-- DATA MAHASISWA FULL -->
    <h6 class="fw-semibold text-primary mb-3">
        <i class="bi bi-card-list"></i> Data Mahasiswa
    </h6>

    <table class="table table-borderless">
        <tr><th width="30%">NIM</th><td><?= safe($m['nim']) ?></td></tr>
        <tr><th>Nama</th><td><?= safe($m['nama']) ?></td></tr>
        <tr><th>Email</th><td><?= safe($m['email']) ?></td></tr>
        <tr><th>Prodi</th><td><?= safe($m['prodi']) ?></td></tr>
        <tr><th>Angkatan</th><td><?= safe($m['angkatan']) ?></td></tr>
        <tr><th>Status</th><td><?= safe($m['status']) ?></td></tr>
        <tr><th>Kategori</th><td><?= safe($m['kategori']) ?></td></tr>
        <tr><th>Tanggal Join</th><td><?= safe($m['tanggal_join']) ?></td></tr>
    </table>

    <hr>

    <!-- EDIT NAMA + EMAIL -->
    <h6 class="fw-semibold text-primary mb-3">
        <i class="bi bi-pencil-square"></i> Edit Data Diri
    </h6>

    <form method="POST" class="row g-3">
        <div class="col-md-6">
            <label class="form-label small">Nama</label>
            <input name="nama" class="form-control" value="<?= safe($m['nama']) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label small">Email</label>
            <input name="email" class="form-control" value="<?= safe($m['email']) ?>">
        </div>

        <div class="col-12">
            <button class="btn btn-primary" name="update_profile">
                <i class="bi bi-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>

</div>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
