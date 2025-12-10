<?php
// app/roles/dosen/bimbingan.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("dosen");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

// ====================== BASE DATA ======================
$active = "bimbingan";
$title = "Daftar Bimbingan";

function safe($v){ return htmlspecialchars($v ?? "-", ENT_QUOTES, 'UTF-8'); }

$dosen_user_id = $_SESSION['user']['user_id'] ?? null;
if (!$dosen_user_id) die("User invalid.");


// ====================== HANDLE POSTS ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---------------- ADD BIMBINGAN ----------------
    if ($_POST['action'] === 'add') {

        $nim      = trim($_POST['nim'] ?? '');
        $nama     = trim($_POST['nama'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $prodi    = trim($_POST['prodi'] ?? '');
        $angkatan = trim($_POST['angkatan'] ?? '');

        if ($nim === '' || $nama === '') {
            $_SESSION['flash_error'] = "NIM dan Nama wajib diisi.";
            header("Location: bimbingan.php");
            exit;
        }

        // FOTO VALIDATION
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = "Foto wajib diupload (format JPG).";
            header("Location: bimbingan.php");
            exit;
        }

        $foto = $_FILES['foto'];
        $allowed = ['image/jpeg','image/jpg'];

        if (!in_array($foto['type'], $allowed)) {
            $_SESSION['flash_error'] = "Foto harus berupa file JPG.";
            header("Location: bimbingan.php");
            exit;
        }

        $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg'])) {
            $_SESSION['flash_error'] = "Foto harus berekstensi .jpg";
            header("Location: bimbingan.php");
            exit;
        }

        // SAVE PHOTO
        $dest = __DIR__ . "/../../../public/uploads/profiles/" . $nim . ".jpg";
        move_uploaded_file($foto['tmp_name'], $dest);

        // INSERT DATABASE (NOW USING user_id DOSEN)
        $stmt = $conn->prepare("
            INSERT INTO mahasiswa (nim, user_id, nama, email, prodi, angkatan, status, foto, kategori, tanggal_join)
            VALUES (?, ?, ?, ?, ?, ?, 'aktif', ?, 'bimbingan', ?)
        ");
        $stmt->execute([
            $nim,
            $dosen_user_id,
            $nama,
            $email,
            $prodi,
            $angkatan,
            $nim . ".jpg",
            date("Y-m-d")
        ]);

        $_SESSION['flash_success'] = "Mahasiswa bimbingan berhasil ditambahkan.";
        header("Location: bimbingan.php");
        exit;
    }

    // ---------------- UPDATE BIMBINGAN ----------------
    if ($_POST['action'] === 'update') {

        $nim = $_POST['nim_old'] ?? null;

        if (!$nim) {
            $_SESSION['flash_error'] = "Identifier tidak ditemukan.";
            header("Location: bimbingan.php");
            exit;
        }

        $nama     = trim($_POST['nama']);
        $email    = trim($_POST['email']);
        $prodi    = trim($_POST['prodi']);
        $angkatan = trim($_POST['angkatan']);
        $status   = trim($_POST['status']);

        $stmt = $conn->prepare("
            UPDATE mahasiswa 
            SET nama=?, email=?, prodi=?, angkatan=?, status=?
            WHERE nim=? AND user_id=?
        ");
        $stmt->execute([$nama,$email,$prodi,$angkatan,$status,$nim,$dosen_user_id]);

        $_SESSION['flash_success'] = "Data mahasiswa berhasil diperbarui.";
        header("Location: bimbingan.php");
        exit;
    }

    // ---------------- DELETE BIMBINGAN ----------------
    if ($_POST['action'] === 'delete') {
        $nim = $_POST['nim'];

        $stmt = $conn->prepare("
            DELETE FROM mahasiswa 
            WHERE nim=? AND kategori='bimbingan' AND user_id=?
        ");
        $stmt->execute([$nim, $dosen_user_id]);

        $_SESSION['flash_success'] = "Mahasiswa bimbingan dihapus.";
        header("Location: bimbingan.php");
        exit;
    }
}


// ====================== LIST + SEARCH + PAGINATION ======================
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 8;
$offset = ($page - 1) * $perPage;

$search = trim($_GET['q'] ?? '');

// BASE FILTER: only show mahasiswa bimbingan milik dosen
$where = "WHERE kategori = 'bimbingan' AND user_id = ?";
$params = [$dosen_user_id];

if ($search !== '') {
    $where .= " AND (LOWER(nama) LIKE ? OR LOWER(nim) LIKE ?)";
    $params[] = '%'.strtolower($search).'%';
    $params[] = '%'.strtolower($search).'%';
}

// COUNT
$stmtCount = $conn->prepare("SELECT COUNT(*) FROM mahasiswa $where");
$stmtCount->execute($params);
$total = (int)$stmtCount->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

// DATA FETCH
$sql = "SELECT nim, nama, email, prodi, angkatan, status, foto, tanggal_join
        FROM mahasiswa
        $where
        ORDER BY tanggal_join DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

$bindIndex = 1;
foreach ($params as $p) {
    $stmt->bindValue($bindIndex++, $p);
}
$stmt->bindValue($bindIndex++, $perPage, PDO::PARAM_INT);
$stmt->bindValue($bindIndex++, $offset, PDO::PARAM_INT);

$stmt->execute();
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ====================== FLASH MSGS ======================
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-people"></i> Daftar Bimbingan
</h2>

<?php if($flash_success): ?>
<div class="alert alert-success"><?= safe($flash_success) ?></div>
<?php endif; ?>

<?php if($flash_error): ?>
<div class="alert alert-danger"><?= safe($flash_error) ?></div>
<?php endif; ?>


<!-- SEARCH + ADD -->
<div class="d-flex align-items-center gap-3 mb-3">
    <form class="d-flex gap-2 w-100" method="GET" style="max-width:800px;">
        <input type="text" name="q" class="form-control" placeholder="Cari nama atau NIM..." value="<?= safe($search) ?>">
        <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
    </form>

    <button class="btn btn-primary ms-auto" onclick="toggleAdd()">+ Tambah Bimbingan</button>
</div>


<!-- ADD FORM -->
<div id="addBox" class="card shadow-sm p-3 mb-4" style="display:none;">
    <form method="POST" enctype="multipart/form-data" class="row g-2">
        <input type="hidden" name="action" value="add">

        <div class="col-md-2">
            <label class="form-label small">NIM</label>
            <input name="nim" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label small">Nama</label>
            <input name="nama" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label small">Email</label>
            <input name="email" class="form-control" type="email">
        </div>

        <div class="col-md-2">
            <label class="form-label small">Prodi</label>
            <input name="prodi" class="form-control">
        </div>

        <div class="col-md-1">
            <label class="form-label small">Angkatan</label>
            <input name="angkatan" class="form-control">
        </div>

        <div class="col-md-4 mt-2">
            <label class="form-label small">Foto (JPG)</label>
            <input name="foto" type="file" class="form-control" accept=".jpg,.jpeg" required>
        </div>

        <div class="col-12 text-end mt-3">
            <button type="button" class="btn btn-secondary" onclick="toggleAdd()">Batal</button>
            <button class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>


<!-- LIST -->
<div class="row g-4">
<?php if(empty($list)): ?>
    <div class="col-12">
        <div class="card p-4 text-center text-muted">Belum ada mahasiswa bimbingan.</div>
    </div>

<?php else: foreach($list as $m): ?>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card shadow-sm p-3 text-center h-100">
                    <div class="mb-2">
                        <img src="../../../public/uploads/profiles/<?= safe($m['nim']) ?>.jpg"
                             onerror="this.src='../../../public/assets/img/default-user.png';"
                             class="rounded-circle" style="width:80px;height:80px;object-fit:cover;">
                    </div>
            <h6 class="fw-bold"><?= safe($m['nama']) ?></h6>
            <div class="text-muted small"><?= safe($m['nim']) ?></div>

            <span class="badge <?= $m['status']=='aktif'?'bg-success':'bg-secondary' ?> mt-2">
                <?= safe($m['status']) ?>
            </span>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm w-100"
                        onclick="openDetail('<?= rawurlencode($m['nim']) ?>')">
                    <i class="bi bi-eye"></i> Detail
                </button>

                <form method="POST" onsubmit="return confirm('Hapus data?')" style="width:100px;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="nim" value="<?= safe($m['nim']) ?>">
                    <button class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>

        </div>
    </div>
<?php endforeach; endif; ?>
</div>


<!-- PAGINATION -->
<?php if($totalPages > 1): ?>
<nav class="mt-4 d-flex justify-content-center">
    <ul class="pagination pagination-sm">
        <li class="page-item <?= $page<=1?'disabled':'' ?>">
            <a class="page-link"
               href="?<?= http_build_query(array_merge($_GET,['page'=>$page-1])) ?>">
               Prev
            </a>
        </li>

        <?php 
        $start = max(1, $page-3);
        $end   = min($totalPages, $page+3);

        for($p=$start;$p<=$end;$p++): ?>
            <li class="page-item <?= $page==$p?'active':'' ?>">
                <a class="page-link"
                   href="?<?= http_build_query(array_merge($_GET,['page'=>$p])) ?>">
                    <?= $p ?>
                </a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>">
            <a class="page-link"
               href="?<?= http_build_query(array_merge($_GET,['page'=>$page+1])) ?>">
               Next
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>



<!-- ============ DETAIL MODAL ============ -->
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <form method="POST">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="nim_old" id="nim_old">

        <div class="modal-header">
          <h5 class="modal-title">Detail Mahasiswa</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="row g-3">
            <div class="col-md-4 text-center">
              <img id="detailPhoto"
                   src="../../../public/assets/img/default-user.png"
                   style="width:150px;height:150px;object-fit:cover;border-radius:8px;">
            </div>

            <div class="col-md-8">

                <label class="small">NIM</label>
                <input class="form-control mb-2" id="nim_field" disabled>

                <label class="small">Nama</label>
                <input class="form-control mb-2" name="nama" id="nama_field">

                <label class="small">Email</label>
                <input class="form-control mb-2" name="email" id="email_field">

                <div class="row">
                    <div class="col">
                        <label class="small">Prodi</label>
                        <input class="form-control mb-2" name="prodi" id="prodi_field">
                    </div>
                    <div class="col">
                        <label class="small">Angkatan</label>
                        <input class="form-control mb-2" name="angkatan" id="angkatan_field">
                    </div>
                    <div class="col">
                        <label class="small">Status</label>
                        <select class="form-control mb-2" name="status" id="status_field">
                            <option value="aktif">aktif</option>
                            <option value="nonaktif">nonaktif</option>
                        </select>
                    </div>
                </div>

            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button class="btn btn-primary">Simpan Perubahan</button>
        </div>

      </form>

    </div>
  </div>
</div>


<!-- SCRIPT -->
<script>
function toggleAdd(){
    const box = document.getElementById("addBox");
    box.style.display = box.style.display === "none" ? "block" : "none";
}

function openDetail(nim){
    fetch("bimbingan_detail_api.php?nim=" + nim)
        .then(r => r.json())
        .then(res => {
            if(res.error){ alert(res.error); return; }

            let d = res.data;

            document.getElementById("nim_old").value = d.nim;
            document.getElementById("nim_field").value = d.nim;

            document.getElementById("nama_field").value = d.nama;
            document.getElementById("email_field").value = d.email;
            document.getElementById("prodi_field").value = d.prodi;
            document.getElementById("angkatan_field").value = d.angkatan;
            document.getElementById("status_field").value = d.status;

            document.getElementById("detailPhoto").src =
                "../../../public/uploads/profiles/" + d.nim + ".jpg";

            new bootstrap.Modal(document.getElementById("detailModal")).show();
        })
        .catch(err => console.log(err));
}
</script>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
