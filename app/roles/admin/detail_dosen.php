<?php
session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("admin");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$user_id = $_GET['user_id'] ?? null;
if (!$user_id) die("User ID tidak valid.");

function safe($x){ return htmlspecialchars($x ?? "-", ENT_QUOTES,'UTF-8'); }

// =====================================================
// HANDLE FOTO UPLOAD
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_foto'])) {

    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['flash_error'] = "Gagal upload foto.";
        header("Location: detail_dosen.php?user_id=".$_POST['user_id_redirect']);
        exit;
    }

    $file = $_FILES['foto'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, ['jpg','jpeg'])) {
        $_SESSION['flash_error'] = "Foto harus format JPG.";
        header("Location: detail_dosen.php?user_id=".$_POST['user_id_redirect']);
        exit;
    }

    $target = __DIR__ . "/../../../public/uploads/profiles/" . $_POST['foto_user_id'] . ".jpg";
    move_uploaded_file($file['tmp_name'], $target);

    $_SESSION['flash_success'] = "Foto berhasil diperbarui.";
    header("Location: detail_dosen.php?user_id=".$_POST['user_id_redirect']);
    exit;
}

// =====================================================
// HANDLE UPDATE FORM NORMAL
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // UPDATE PROFIL DOSEN
    if (isset($_POST['update_dosen'])) {
        $stmt = $conn->prepare("
            UPDATE dosen SET nama=?, nidn=?, email=?, jabatan=? WHERE nip=?
        ");
        $stmt->execute([
            $_POST['nama'], $_POST['nidn'], $_POST['email'], $_POST['jabatan'], $_POST['nip']
        ]);
    }

    // UPDATE ACCOUNT LOGIN
    if (isset($_POST['update_account'])) {
        $q = ["username=?"];
        $p = [$_POST['username']];

        if (!empty($_POST['password'])) {
            $q[] = "password=?";
            $p[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        $p[] = $_POST['user_id'];

        $stmt = $conn->prepare("UPDATE users SET ".implode(",", $q)." WHERE user_id=?");
        $stmt->execute($p);
    }

    // Pendidikan CRUD
    if (isset($_POST['add_edu'])) {
        $stmt = $conn->prepare("
            INSERT INTO pendidikan (nip_dosen, pendidikan_tinggi, universitas, tahun_awal, tahun_akhir)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_POST['nip_dosen'], $_POST['pendidikan_tinggi'], $_POST['universitas'],
            $_POST['tahun_awal'], $_POST['tahun_akhir']
        ]);
    }

    if (isset($_POST['update_edu'])) {
        $stmt = $conn->prepare("
            UPDATE pendidikan SET pendidikan_tinggi=?, universitas=?, tahun_awal=?, tahun_akhir=? WHERE id=?
        ");
        $stmt->execute([
            $_POST['pendidikan_tinggi'], $_POST['universitas'],
            $_POST['tahun_awal'], $_POST['tahun_akhir'], $_POST['id']
        ]);
    }

    // Mata Kuliah CRUD
    if (isset($_POST['add_mk'])) {

        function kodeMK($nama,$prodi,$tahun){
            $a=""; foreach(explode(" ",$nama) as $w){ $a .= strtoupper($w[0]); }
            $b=""; foreach(explode(" ",$prodi) as $w){ $b .= strtoupper($w[0]); }
            $tahun = str_replace(" ","",$tahun);
            if(str_contains($tahun,"/")){
                [$x,$y] = explode("/",$tahun);
                return "$a-$b-" . substr($x,-2) . substr($y,-2);
            }
            return "$a-$b-" . substr($tahun,-2);
        }

        $kode = kodeMK($_POST['nama_matkul'], $_POST['prodi'], $_POST['tahun_ajar']);

        $stmt = $conn->prepare("
            INSERT INTO mata_kuliah (kode_matkul, nip, nama_matkul, semester, prodi, sks, tahun_ajar)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $kode, $_POST['nip'], $_POST['nama_matkul'], $_POST['semester'],
            $_POST['prodi'], $_POST['sks'], $_POST['tahun_ajar']
        ]);
    }

    if (isset($_POST['update_mk'])) {
        $stmt = $conn->prepare("
            UPDATE mata_kuliah SET nama_matkul=?, semester=?, prodi=?, sks=?, tahun_ajar=?
            WHERE kode_matkul=?
        ");
        $stmt->execute([
            $_POST['nama_matkul'], $_POST['semester'], $_POST['prodi'],
            $_POST['sks'], $_POST['tahun_ajar'], $_POST['kode_matkul']
        ]);
    }

    header("Location: detail_dosen.php?user_id=".$_POST['user_id_redirect']);
    exit;
}

// =====================================================
// FETCH USER + ROLE
// =====================================================
$stmt = $conn->prepare("
    SELECT u.username, u.nip
    FROM users u
    WHERE u.user_id = ?
");
$stmt->execute([$user_id]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$u) die("User tidak ditemukan.");

$nip = $u['nip'];

// Fetch role prioritas
$stmt = $conn->prepare("
    SELECT r.role_name
    FROM user_roles ur
    JOIN roles r ON r.role_id = ur.role_id
    WHERE ur.user_id = ?
");
$stmt->execute([$user_id]);
$roles = $stmt->fetchAll(PDO::FETCH_COLUMN);

$prioritas = ["ketua lab","admin","operator","dosen"];
$userRole = "Tidak Ada";

foreach ($prioritas as $p) {
    foreach ($roles as $r) {
        if (strtolower($p) === strtolower($r)) {
            $userRole = ucfirst($r);
            break 2;
        }
    }
}

// Fetch dosen
$stmt = $conn->prepare("
    SELECT * FROM dosen WHERE nip=? LIMIT 1
");
$stmt->execute([$nip]);
$dosen = $stmt->fetch(PDO::FETCH_ASSOC);

// Pendidikan
$stmt = $conn->prepare("SELECT * FROM pendidikan WHERE nip_dosen=? ORDER BY tahun_akhir DESC");
$stmt->execute([$nip]);
$pendidikan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// MK
$stmt = $conn->prepare("SELECT * FROM mata_kuliah WHERE nip=? ORDER BY tahun_ajar DESC");
$stmt->execute([$nip]);
$mk = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<style>
.edit-input { display:none; }
.row-editing .text-value { display:none; }
.row-editing .edit-input { display:block; }

.profile-img {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border-radius: 100%;
    border: 4px solid #eee;
    margin:auto;
}
</style>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-person-badge-fill"></i> Detail Dosen
</h2>

<div class="card shadow-sm border-0 p-4">

    <!-- FOTO + ROLE -->
    <div class="text-center mb-4">
        <img src="../../../public/uploads/profiles/<?= $user_id ?>.jpg"
             onerror="this.src='../../../public/assets/img/default-user.png';"
             class="profile-img">

        <form method="POST" enctype="multipart/form-data" class="mt-3">
            <input type="hidden" name="upload_foto" value="1">
            <input type="hidden" name="foto_user_id" value="<?= $user_id ?>">
            <input type="hidden" name="user_id_redirect" value="<?= $user_id ?>">

            <input type="file" name="foto" accept=".jpg,.jpeg" class="form-control mb-2 w-10" required>
            <button class="btn btn-primary btn-sm ">Upload Foto</button>
        </form>

        <span class="badge bg-info px-3 py-2 mt-2"><?= safe($userRole) ?></span>
    </div>

    <hr>

    <!-- PROFIL DOSEN -->
    <form method="POST">
        <input type="hidden" name="user_id_redirect" value="<?= $user_id ?>">
        <input type="hidden" name="nip" value="<?= $nip ?>">

        <table class="table table-borderless">
            <tr>
                <th width="25%">NIP</th>
                <td><?= safe($nip) ?></td>
            </tr>

            <?php
            $fields = [
                "Nama" => "nama",
                "NIDN" => "nidn",
                "Email" => "email",
                "Jabatan" => "jabatan"
            ];
            foreach ($fields as $label => $field): ?>
                <tr class="editable-row">
                    <th><?= $label ?></th>
                    <td>
                        <span class="text-value"><?= safe($dosen[$field]) ?></span>
                        <input type="text" name="<?= $field ?>" class="form-control edit-input" value="<?= safe($dosen[$field]) ?>">
                        <i class="bi bi-pencil-square text-primary action-btn ms-2 edit-toggle"></i>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <button class="btn btn-primary mt-3" name="update_dosen">
            <i class="bi bi-save"></i> Simpan Profil
        </button>
    </form>

    <hr>

    <!-- PENDIDIKAN -->
    <h5 class="fw-bold mb-2"><i class="bi bi-mortarboard"></i> Riwayat Pendidikan</h5>

    <form method="POST" class="mb-3" id="addEduForm" style="display:none;">
        <input type="hidden" name="add_edu" value="1">
        <input type="hidden" name="nip_dosen" value="<?= $nip ?>">
        <input type="hidden" name="user_id_redirect" value="<?= $user_id ?>">

        <div class="row g-2">
            <div class="col"><input class="form-control" name="pendidikan_tinggi" placeholder="Jenjang"></div>
            <div class="col"><input class="form-control" name="universitas" placeholder="Universitas"></div>
            <div class="col"><input class="form-control" name="tahun_awal" placeholder="Mulai"></div>
            <div class="col"><input class="form-control" name="tahun_akhir" placeholder="Selesai"></div>
            <div class="col-auto"><button class="btn btn-success btn-sm">Tambah</button></div>
        </div>
    </form>

    <button class="btn btn-sm btn-success mb-2" onclick="document.getElementById('addEduForm').style.display='block'">+ Tambah Pendidikan</button>

    <?php if (empty($pendidikan)): ?>
        <p class="text-muted small">Belum ada data pendidikan.</p>
    <?php else: ?>
        <?php foreach ($pendidikan as $edu): ?>
            <form method="POST" class="row edu-row align-items-center my-2">
                <input type="hidden" name="update_edu" value="1">
                <input type="hidden" name="id" value="<?= $edu['id'] ?>">
                <input type="hidden" name="user_id_redirect" value="<?= $user_id ?>">

                <div class="col text-display"><?= safe($edu['pendidikan_tinggi']) ?></div>
                <div class="col text-display"><?= safe($edu['universitas']) ?></div>
                <div class="col text-display"><?= safe($edu['tahun_awal']) ?></div>
                <div class="col text-display"><?= safe($edu['tahun_akhir']) ?></div>

                <input class="col form-control text-edit d-none" name="pendidikan_tinggi" value="<?= safe($edu['pendidikan_tinggi']) ?>">
                <input class="col form-control text-edit d-none" name="universitas" value="<?= safe($edu['universitas']) ?>">
                <input class="col form-control text-edit d-none" name="tahun_awal" value="<?= safe($edu['tahun_awal']) ?>">
                <input class="col form-control text-edit d-none" name="tahun_akhir" value="<?= safe($edu['tahun_akhir']) ?>">

                <div class="col-auto d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm btn-edit-toggle"><i class="bi bi-pencil"></i></button>
                    <button type="submit" class="btn btn-success btn-sm d-none btn-save-inline"><i class="bi bi-check-lg"></i></button>
                </div>
            </form>
        <?php endforeach; ?>
    <?php endif; ?>

    <hr>

    <!-- MATA KULIAH -->
    <h5 class="fw-bold mb-2"><i class="bi bi-journal-bookmark"></i> Mata Kuliah</h5>

    <form method="POST" class="mb-3" id="addMkForm" style="display:none;">
        <input type="hidden" name="add_mk" value="1">
        <input type="hidden" name="nip" value="<?= $nip ?>">
        <input type="hidden" name="user_id_redirect" value="<?= $user_id ?>">

        <div class="row g-2">
            <div class="col"><input class="form-control" name="nama_matkul" placeholder="Nama MK"></div>
            <div class="col"><input class="form-control" name="semester" placeholder="Sem"></div>
            <div class="col"><input class="form-control" name="sks" placeholder="SKS"></div>
            <div class="col"><input class="form-control" name="prodi" placeholder="Prodi"></div>
            <div class="col"><input class="form-control" name="tahun_ajar" placeholder="Tahun"></div>
            <div class="col-auto"><button class="btn btn-success btn-sm">Tambah</button></div>
        </div>
    </form>

    <button class="btn btn-sm btn-success mb-2" onclick="document.getElementById('addMkForm').style.display='block'">+ Tambah Mata Kuliah</button>

    <?php if (empty($mk)): ?>
        <p class="text-muted small">Belum ada data MK.</p>
    <?php else: ?>
        <?php foreach ($mk as $m): ?>
            <form method="POST" class="row mk-row align-items-center my-2">
                <input type="hidden" name="update_mk" value="1">
                <input type="hidden" name="kode_matkul" value="<?= $m['kode_matkul'] ?>">
                <input type="hidden" name="user_id_redirect" value="<?= $user_id ?>">

                <div class="col text-display"><?= safe($m['nama_matkul']) ?></div>
                <div class="col text-display"><?= safe($m['semester']) ?></div>
                <div class="col text-display"><?= safe($m['sks']) ?></div>
                <div class="col text-display"><?= safe($m['prodi']) ?></div>
                <div class="col text-display"><?= safe($m['tahun_ajar']) ?></div>

                <input class="col form-control text-edit d-none" name="nama_matkul" value="<?= safe($m['nama_matkul']) ?>">
                <input class="col form-control text-edit d-none" name="semester" value="<?= safe($m['semester']) ?>">
                <input class="col form-control text-edit d-none" name="sks" value="<?= safe($m['sks']) ?>">
                <input class="col form-control text-edit d-none" name="prodi" value="<?= safe($m['prodi']) ?>">
                <input class="col form-control text-edit d-none" name="tahun_ajar" value="<?= safe($m['tahun_ajar']) ?>">

                <div class="col-auto d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm btn-edit-toggle"><i class="bi bi-pencil"></i></button>
                    <button type="submit" class="btn btn-success btn-sm d-none btn-save-inline"><i class="bi bi-check-lg"></i></button>
                </div>
            </form>
        <?php endforeach; ?>
    <?php endif; ?>

    <hr>

    <!-- PENGATURAN AKUN -->
    <h5 class="fw-bold mb-2"><i class="bi bi-lock-fill"></i> Pengaturan Akun</h5>

    <form method="POST">
        <input type="hidden" name="update_account" value="1">
        <input type="hidden" name="user_id" value="<?= $user_id ?>">
        <input type="hidden" name="user_id_redirect" value="<?= $user_id ?>">

        <div class="mb-2">
            <label>Username</label>
            <input class="form-control" name="username" value="<?= safe($u['username']) ?>">
        </div>

        <div class="mb-2">
            <label>Password Baru (opsional)</label>
            <input class="form-control" name="password" type="password">
        </div>

        <button class="btn btn-primary">Simpan Akun</button>
    </form>

</div>

<script>
// toggle inline edit
document.querySelectorAll(".edit-toggle").forEach(btn => {
    btn.onclick = () => {
        let row = btn.closest("tr");
        row.classList.toggle("row-editing");
    };
});

// education/mk inline edit handler
document.querySelectorAll(".btn-edit-toggle").forEach(btn => {
    btn.onclick = function() {
        let row = this.closest("form");
        let editing = row.classList.contains("editing");

        if (!editing) {
            row.classList.add("editing");
            row.querySelectorAll(".text-display").forEach(e => e.classList.add("d-none"));
            row.querySelectorAll(".text-edit").forEach(e => e.classList.remove("d-none"));
            row.querySelector(".btn-save-inline").classList.remove("d-none");
            this.innerHTML = `<i class="bi bi-x-lg"></i>`;
            this.classList.replace("btn-outline-primary","btn-outline-danger");
        } else {
            row.classList.remove("editing");
            row.querySelectorAll(".text-display").forEach(e => e.classList.remove("d-none"));
            row.querySelectorAll(".text-edit").forEach(e => e.classList.add("d-none"));
            row.querySelector(".btn-save-inline").classList.add("d-none");
            this.innerHTML = `<i class="bi bi-pencil"></i>`;
            this.classList.replace("btn-outline-danger","btn-outline-primary");
        }
    };
});
</script>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
