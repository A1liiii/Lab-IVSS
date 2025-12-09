<?php
session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("admin");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

// ======== INPUT / ROUTING ========
$user_id = $_GET['user_id'] ?? null;
if(!$user_id) die("User ID tidak valid.");

function safe($x){ return htmlspecialchars($x ?? "-", ENT_QUOTES,'UTF-8'); }


// ======================================================================
// ======================== HANDLE FORM ACTIONS =========================
// ======================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ===== UPDATE PROFIL DOSEN =====
    if (isset($_POST['update_dosen'])) {
        $stmt = $conn->prepare("
            UPDATE dosen SET nama=?, nidn=?, email=?, jabatan=? 
            WHERE nip=?
        ");
        $stmt->execute([
            $_POST['nama'], $_POST['nidn'], $_POST['email'], $_POST['jabatan'], $_POST['nip']
        ]);
    }

    // ===== UPDATE AKUN LOGIN =====
    if (isset($_POST['update_account'])) {
        $queries = ["username = ?" ];
        $params = [ $_POST['username'] ];

        if (!empty($_POST['password'])) {
            $queries[] = "password = ?";
            $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        $params[] = $_POST['user_id'];

        $sql = "UPDATE users SET ".implode(",", $queries)." WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
    }

    function generateKodeMK($nama, $prodi, $tahun){
        // inisial MK
        $inisialMK = "";
        foreach(explode(" ", trim($nama)) as $w){
            $inisialMK .= strtoupper(substr($w, 0, 1));
        }

        // inisial prodi
        $inisialProdi = "";
        foreach(explode(" ", trim($prodi)) as $w){
            $inisialProdi .= strtoupper(substr($w, 0, 1));
        }

        // Tahun ajar format xx/xxxx → ambil 2 digit + 2 digit
        $tahun = str_replace(" ", "", $tahun);
        if(str_contains($tahun, "/")){
            [$start, $end] = explode("/", $tahun);
            $startShort = substr($start, -2);
            $endShort   = substr($end, -2);
        } else {
            // fallback kalau salah format
            $startShort = substr($tahun, -2);
            $endShort   = substr($tahun, -2);
        }

        return $inisialMK . "-" . $inisialProdi . "-" . $startShort . $endShort;
    }

    // ===== TAMBAH PENDIDIKAN =====
    if(isset($_POST['add_edu'])){
        $stmt = $conn->prepare("
            INSERT INTO pendidikan (nip_dosen, pendidikan_tinggi, universitas, tahun_awal, tahun_akhir)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_POST['nip_dosen'], $_POST['pendidikan_tinggi'], $_POST['universitas'],
            $_POST['tahun_awal'], $_POST['tahun_akhir']
        ]);
    }

    // ===== UPDATE PENDIDIKAN =====
    if(isset($_POST['update_edu'])){
        $stmt = $conn->prepare("
            UPDATE pendidikan
            SET pendidikan_tinggi=?, universitas=?, tahun_awal=?, tahun_akhir=?
            WHERE id=?
        ");
        $stmt->execute([
            $_POST['pendidikan_tinggi'], $_POST['universitas'], $_POST['tahun_awal'], 
            $_POST['tahun_akhir'], $_POST['id']
        ]);
    }

    // ===== TAMBAH MK =====
    if(isset($_POST['add_mk'])){
        $kodeMK = generateKodeMK($_POST['nama_matkul'], $_POST['prodi'], $_POST['tahun_ajar']);

        $stmt = $conn->prepare("
            INSERT INTO mata_kuliah(kode_matkul, nip, nama_matkul, semester, prodi, sks, tahun_ajar)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $kodeMK,
            $_POST['nip'],
            $_POST['nama_matkul'],
            $_POST['semester'],
            $_POST['prodi'],
            $_POST['sks'],
            $_POST['tahun_ajar']
        ]);
    }

    // ===== UPDATE MK =====
    if(isset($_POST['update_mk'])){
        $stmt = $conn->prepare("
            UPDATE mata_kuliah
            SET nama_matkul=?, semester=?, prodi=?, sks=?, tahun_ajar=?
            WHERE kode_matkul=?
        ");
        $stmt->execute([
            $_POST['nama_matkul'], $_POST['semester'], $_POST['prodi'], 
            $_POST['sks'], $_POST['tahun_ajar'], $_POST['kode_matkul']
        ]);
    }

    // refresh page setelah proses
    header("Location: detail_dosen.php?user_id=".$_POST['user_id_redirect']);
    exit;
}



// ======================================================================
// ========================= FETCH DATA DOSEN ===========================
// ======================================================================

$stmtUser = $conn->prepare("SELECT username, nip FROM users WHERE user_id = ?");
$stmtUser->execute([$user_id]);
$u = $stmtUser->fetch(PDO::FETCH_ASSOC);

if(!$u || !$u['nip']){
    die("<div class='alert alert-warning'>⚠️ Dosen tidak ditemukan.</div>");
}

$nip = $u['nip'];

$stmtDosen = $conn->prepare("
    SELECT nip, user_id, nama, nidn, email, jabatan, pendidikan, foto
    FROM dosen
    WHERE nip = ?
    LIMIT 1
");
$stmtDosen->execute([$nip]);
$dosen = $stmtDosen->fetch(PDO::FETCH_ASSOC);


// pendidikan
$stmtEdu = $conn->prepare("
    SELECT id, pendidikan_tinggi, universitas, tahun_awal, tahun_akhir
    FROM pendidikan
    WHERE nip_dosen = ?
    ORDER BY tahun_akhir DESC
");
$stmtEdu->execute([$nip]);
$pendidikan = $stmtEdu->fetchAll(PDO::FETCH_ASSOC);

// mata kuliah
$stmtMK = $conn->prepare("
    SELECT kode_matkul, nama_matkul, semester, prodi, sks, tahun_ajar
    FROM mata_kuliah
    WHERE nip = ?
    ORDER BY tahun_ajar DESC
");
$stmtMK->execute([$nip]);
$mk = $stmtMK->fetchAll(PDO::FETCH_ASSOC);



ob_start();
?>

<style>
.edit-input { display:none; }
.row-editing .text-value { display:none; }
.row-editing .edit-input { display:block; }
.action-btn { cursor:pointer; }
</style>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-person-badge-fill"></i> Detail Dosen
</h2>

<div class="card shadow-sm border-0 p-4">

    <!-- PROFILE -->
    <div class="text-center mb-4">
        <img src="/public/uploads/profiles/<?= $dosen['user_id'] ?>.jpg"
             onerror="this.src='/public/assets/img/default-user.png';"
             class="rounded-circle border"
             style="width:120px;height:120px;object-fit:cover;">
        <h5 class="fw-bold mt-3"><?= safe($dosen['nama']) ?></h5>
        <span class="badge bg-warning-subtle text-dark px-3 py-2">Dosen</span>
    </div>

    <hr>

    <!-- INFO DOSEN EDITABLE -->
<!-- INFO DOSEN EDITABLE -->
<form method="POST">
    <input type="hidden" name="user_id_redirect" value="<?= $user_id ?>">
    <input type="hidden" name="nip" value="<?= $nip ?>">

    <table class="table table-borderless">

        <!-- NIP tampil & editable -->
        <tr>
            <th width="30%">NIP</th>
            <td><strong><?= safe($dosen['nip']) ?></strong></td>
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
                <th width="30%"><?= $label ?></th>
                <td>
                    <span class="text-value"><?= safe($dosen[$field]) ?></span>
                    <input type="text" name="<?= $field ?>" class="form-control edit-input"
                           value="<?= safe($dosen[$field]) ?>">
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

    <!-- RIWAYAT PENDIDIKAN (CRUD INLINE) -->
    <div class="card shadow-sm p-3 border-0 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-bold"><i class="bi bi-mortarboard"></i> Riwayat Pendidikan</h6>
            <button class="btn btn-sm btn-success" onclick="toggleAddEdu()">+ Tambah</button>
        </div>

        <form method="POST" id="addEduForm" style="display:none;">
            <input type="hidden" name="user_id_redirect" value="<?= $user_id ?>">
            <input type="hidden" name="nip_dosen" value="<?= $nip ?>">
            <input type="hidden" name="add_edu" value="1">

            <div class="row g-2 mt-2">
                <div class="col"><input class="form-control" name="pendidikan_tinggi" placeholder="Jenjang"></div>
                <div class="col"><input class="form-control" name="universitas" placeholder="Universitas"></div>
                <div class="col"><input class="form-control" name="tahun_awal" placeholder="Mulai"></div>
                <div class="col"><input class="form-control" name="tahun_akhir" placeholder="Selesai"></div>
                <div class="col-auto"><button class="btn btn-primary btn-sm">Simpan</button></div>
            </div>
        </form>

        <?php if(empty($pendidikan)): ?>
            <p class="text-muted small mt-2">Belum ada data pendidikan.</p>
        <?php else: ?>
            <?php foreach($pendidikan as $edu): ?>
            <form method="POST" class="row edu-row align-items-center my-2">

                <input type="hidden" name="user_id_redirect" value="<?= $user_id ?>">
                <input type="hidden" name="id" value="<?= $edu['id'] ?>">
                <input type="hidden" name="update_edu" value="1">

                <!-- display mode -->
                <div class="col text-display"><?= safe($edu['pendidikan_tinggi']) ?></div>
                <div class="col text-display"><?= safe($edu['universitas']) ?></div>
                <div class="col text-display"><?= safe($edu['tahun_awal']) ?></div>
                <div class="col text-display"><?= safe($edu['tahun_akhir']) ?></div>

                <!-- edit mode -->
                <input class="col form-control text-edit d-none" name="pendidikan_tinggi" value="<?= safe($edu['pendidikan_tinggi']) ?>">
                <input class="col form-control text-edit d-none" name="universitas" value="<?= safe($edu['universitas']) ?>">
                <input class="col form-control text-edit d-none" name="tahun_awal" value="<?= safe($edu['tahun_awal']) ?>">
                <input class="col form-control text-edit d-none" name="tahun_akhir" value="<?= safe($edu['tahun_akhir']) ?>">

                <div class="col-auto d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm btn-edit-toggle">
                        <i class="bi bi-pencil"></i>
                    </button>

                    <button type="submit" class="btn btn-success btn-sm d-none btn-save-inline">
                        <i class="bi bi-check-lg"></i>
                    </button>
                </div>
            </form>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <hr>

    <!-- MATA KULIAH INLINE CRUD -->
    <div class="card shadow-sm p-3 border-0 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-bold"><i class="bi bi-journal-bookmark"></i> Mata Kuliah</h6>
            <button class="btn btn-sm btn-success" onclick="toggleAddMK()">+ Tambah</button>
        </div>
        <form method="POST" id="addMkForm" style="display:none;">
            <input type="hidden" name="user_id_redirect" value="<?= $user_id ?>">
            <input type="hidden" name="nip" value="<?= $nip ?>">
            <input type="hidden" name="add_mk" value="1">

            <div class="row g-2 mt-2">
                <div class="col"><input class="form-control" name="nama_matkul" placeholder="Nama MK"></div>
                <div class="col"><input class="form-control" name="semester" placeholder="Semester"></div>
                <div class="col"><input class="form-control" name="sks" placeholder="SKS"></div>
                <div class="col"><input class="form-control" name="prodi" placeholder="Prodi"></div>
                <div class="col"><input class="form-control" name="tahun_ajar" placeholder="Tahun"></div>
                <div class="col-auto"><button class="btn btn-primary btn-sm">Simpan</button></div>
            </div>
        </form>

        <?php if(empty($mk)): ?>
            <p class="text-muted small mt-2">Belum ada data mata kuliah.</p>
        <?php else: ?>
          <?php foreach($mk as $row): ?>
            <form method="POST" class="row mk-row align-items-center my-2">

                <input type="hidden" name="user_id_redirect" value="<?= $user_id ?>">
                <input type="hidden" name="kode_matkul" value="<?= $row['kode_matkul'] ?>">
                <input type="hidden" name="update_mk" value="1">

                <!-- display mode -->
                <div class="col text-display"><?= safe($row['nama_matkul']) ?></div>
                <div class="col text-display"><?= safe($row['semester']) ?></div>
                <div class="col text-display"><?= safe($row['sks']) ?></div>
                <div class="col text-display"><?= safe($row['prodi']) ?></div>
                <div class="col text-display"><?= safe($row['tahun_ajar']) ?></div>

                <!-- edit mode -->
                <input class="col form-control text-edit d-none" name="nama_matkul" value="<?= safe($row['nama_matkul']) ?>">
                <input class="col form-control text-edit d-none" name="semester" value="<?= safe($row['semester']) ?>">
                <input class="col form-control text-edit d-none" name="sks" value="<?= safe($row['sks']) ?>">
                <input class="col form-control text-edit d-none" name="prodi" value="<?= safe($row['prodi']) ?>">
                <input class="col form-control text-edit d-none" name="tahun_ajar" value="<?= safe($row['tahun_ajar']) ?>">

                <div class="col-auto d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm btn-edit-toggle">
                        <i class="bi bi-pencil"></i>
                    </button>

                    <button type="submit" class="btn btn-success btn-sm d-none btn-save-inline">
                        <i class="bi bi-check-lg"></i>
                    </button>
                </div>

            </form>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>

    <hr>

    <!-- LOGIN -->
    <h6 class="fw-bold text-primary mb-3">
        <i class="bi bi-lock-fill"></i> Pengaturan Akun Login
    </h6>

    <form method="POST">
        <input type="hidden" name="user_id_redirect" value="<?= $user_id ?>">
        <input type="hidden" name="update_account" value="1">
        <input type="hidden" name="user_id" value="<?= $user_id ?>">

        <div class="mb-3">
            <label class="fw-semibold">Username</label>
            <input type="text" name="username" class="form-control" value="<?= safe($u['username']) ?>">
        </div>

        <div class="mb-3">
            <label class="fw-semibold">Password Baru (opsional)</label>
            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Simpan Akun
        </button>
    </form>

</div>


<script>
document.querySelectorAll(".edit-toggle").forEach(btn => {
    btn.addEventListener("click", ()=>{
        let row = btn.closest("tr") || btn.closest(".row");
        row.classList.toggle("row-editing");
    });
});

function toggleAddEdu(){
    let form = document.getElementById("addEduForm");
    form.style.display = form.style.display==="none" ? "block" : "none";
}

function toggleAddMK(){
    let form = document.getElementById("addMkForm");
    form.style.display = form.style.display==="none" ? "block" : "none";
}
// Toggle mode for education & matkul forms
document.querySelectorAll(".btn-edit-toggle").forEach(btn => {
    btn.addEventListener("click", function() {
        let row = this.closest("form");
        let isEditing = row.classList.contains("editing");

        if (!isEditing) {
            // masuk mode edit
            row.classList.add("editing");

            row.querySelectorAll(".text-display").forEach(el => el.classList.add("d-none"));
            row.querySelectorAll(".text-edit").forEach(el => el.classList.remove("d-none"));

            row.querySelector(".btn-save-inline").classList.remove("d-none");

            // ganti icon dari edit → cancel
            this.innerHTML = `<i class="bi bi-x-lg"></i>`;
            this.classList.remove("btn-outline-primary");
            this.classList.add("btn-outline-danger");

        } else {
            // batal edit → kembali view mode
            row.classList.remove("editing");

            row.querySelectorAll(".text-display").forEach(el => el.classList.remove("d-none"));
            row.querySelectorAll(".text-edit").forEach(el => el.classList.add("d-none"));

            row.querySelector(".btn-save-inline").classList.add("d-none");

            // reset icon ke pensil
            this.innerHTML = `<i class="bi bi-pencil"></i>`;
            this.classList.remove("btn-outline-danger");
            this.classList.add("btn-outline-primary");
        }
    });
});

</script>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
