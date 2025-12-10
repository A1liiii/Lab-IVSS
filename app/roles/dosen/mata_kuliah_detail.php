<?php
// app/roles/dosen/mata_kuliah_detail.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("dosen");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$kode = $_GET['kode'] ?? null;
if (!$kode) {
    header("Location: mata_kuliah.php");
    exit;
}

function safe($v){ return htmlspecialchars($v ?? "-", ENT_QUOTES, 'UTF-8'); }

// ambil nip dari users.session
$user_id = $_SESSION['user']['user_id'] ?? null;
$stmt = $conn->prepare("SELECT nip FROM users WHERE user_id = ? LIMIT 1");
$stmt->execute([$user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$nip = $row['nip'] ?? null;
if (!$nip) {
    die("<div class='alert alert-warning'>NIP dosen tidak ditemukan. Pastikan akun terhubung dengan data dosen.</div>");
}

// HANDLE POST (update / delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $nama = trim($_POST['nama_matkul'] ?? '');
        $semester = trim($_POST['semester'] ?? null);
        $sks = trim($_POST['sks'] ?? null);
        $prodi = trim($_POST['prodi'] ?? null);
        $tahun_ajar = trim($_POST['tahun_ajar'] ?? null);
        $kode_post = $_POST['kode_matkul'] ?? null;

        if (!$kode_post) {
            $_SESSION['flash_error'] = "Kode mata kuliah tidak ditemukan.";
            header("Location: mata_kuliah_detail.php?kode=" . rawurlencode($kode));
            exit;
        }

        $stmtUp = $conn->prepare("UPDATE mata_kuliah SET nama_matkul=?, semester=?, prodi=?, sks=?, tahun_ajar=? WHERE kode_matkul=? AND nip=?");
        $stmtUp->execute([$nama, $semester ?: null, $prodi ?: null, $sks ?: null, $tahun_ajar ?: null, $kode_post, $nip]);

        $_SESSION['flash_success'] = "Perubahan disimpan.";
        header("Location: mata_kuliah_detail.php?kode=" . rawurlencode($kode_post));
        exit;
    }

    // Delete
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $kode_post = $_POST['kode_matkul'] ?? null;
        if ($kode_post) {
            $stmtDel = $conn->prepare("DELETE FROM mata_kuliah WHERE kode_matkul = ? AND nip = ?");
            $stmtDel->execute([$kode_post, $nip]);
            $_SESSION['flash_success'] = "Mata kuliah dihapus.";
            header("Location: mata_kuliah.php");
            exit;
        }
    }
}

// ambil data mata kuliah
$stmt = $conn->prepare("SELECT kode_matkul, nip, nama_matkul, semester, prodi, sks, tahun_ajar FROM mata_kuliah WHERE kode_matkul = ? AND nip = ? LIMIT 1");
$stmt->execute([$kode, $nip]);
$mk = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$mk) {
    die("<div class='alert alert-danger'>Mata kuliah tidak ditemukan atau bukan milik Anda.</div>");
}

// flash
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary"><i class="bi bi-journal-bookmark"></i> Detail Mata Kuliah</h2>

<?php if($flash_success): ?>
  <div class="alert alert-success"><?= safe($flash_success) ?></div>
<?php endif; ?>
<?php if($flash_error): ?>
  <div class="alert alert-danger"><?= safe($flash_error) ?></div>
<?php endif; ?>

<div class="card shadow-sm p-4 mb-4">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h4 class="fw-bold mb-1"><?= safe($mk['nama_matkul']) ?></h4>
            <div class="text-muted small">
                Kode: <strong><?= safe($mk['kode_matkul']) ?></strong>
                <span class="mx-2">•</span>
                Prodi: <strong><?= safe($mk['prodi']) ?></strong>
                <span class="mx-2">•</span>
                Tahun: <strong><?= safe($mk['tahun_ajar']) ?></strong>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-outline-warning" id="btnEdit"><i class="bi bi-pencil"></i> Edit</button>
            <form method="POST" onsubmit="return confirm('Hapus mata kuliah ini?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="kode_matkul" value="<?= safe($mk['kode_matkul']) ?>">
                <button class="btn btn-outline-danger"><i class="bi bi-trash"></i> Hapus</button>
            </form>
        </div>
    </div>

    <hr>

    <!-- READ MODE -->
    <div id="viewMode">
        <table class="table table-borderless">
            <tr><th width="30%">Nama</th><td><?= safe($mk['nama_matkul']) ?></td></tr>
            <tr><th>Semester</th><td><?= safe($mk['semester']) ?></td></tr>
            <tr><th>SKS</th><td><?= safe($mk['sks']) ?></td></tr>
            <tr><th>Prodi</th><td><?= safe($mk['prodi']) ?></td></tr>
            <tr><th>Tahun Ajar</th><td><?= safe($mk['tahun_ajar']) ?></td></tr>
        </table>
    </div>

    <!-- EDIT MODE (hidden by default) -->
    <div id="editMode" style="display:none;">
        <form method="POST" id="formEdit">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="kode_matkul" value="<?= safe($mk['kode_matkul']) ?>">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label small">Nama Mata Kuliah</label>
                    <input name="nama_matkul" class="form-control" value="<?= safe($mk['nama_matkul']) ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Semester</label>
                    <input name="semester" class="form-control" value="<?= safe($mk['semester']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">SKS</label>
                    <input name="sks" class="form-control" value="<?= safe($mk['sks']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Prodi</label>
                    <input name="prodi" class="form-control" value="<?= safe($mk['prodi']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Tahun Ajar</label>
                    <input name="tahun_ajar" class="form-control" value="<?= safe($mk['tahun_ajar']) ?>">
                </div>
            </div>

            <div class="mt-3 text-end">
                <button type="button" class="btn btn-secondary" id="btnCancelEdit">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>

    <div class="mt-3">
        <a href="mata_kuliah.php" class="btn btn-light"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</div>

<script>
document.getElementById('btnEdit').addEventListener('click', function(){
    document.getElementById('viewMode').style.display = 'none';
    document.getElementById('editMode').style.display = 'block';
    this.disabled = true;
});
document.getElementById('btnCancelEdit').addEventListener('click', function(){
    document.getElementById('viewMode').style.display = 'block';
    document.getElementById('editMode').style.display = 'none';
    document.getElementById('btnEdit').disabled = false;
});
</script>

<?php
$content = ob_get_clean();
include "_layout.php";
