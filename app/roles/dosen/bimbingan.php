<?php
// app/roles/dosen/bimbingan.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("dosen");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$active = "bimbingan";
$title = "Daftar Bimbingan";

function safe($v){
    return htmlspecialchars(!empty($v) ? $v : "-", ENT_QUOTES, 'UTF-8');
}
function now_ts(){ return date("Y-m-d H:i:s"); }

// ====================== HANDLE POSTS (add / update / delete) ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Tambah bimbingan
    // ========================= ADD BIMBINGAN =========================
    if (isset($_POST['action']) && $_POST['action'] === 'add') {

        $nim      = trim(isset($_POST['nim']) ? $_POST['nim'] : '');
        $nama     = trim(isset($_POST['nama']) ? $_POST['nama'] : '');
        $email    = trim(isset($_POST['email']) ? $_POST['email'] : '');
        $prodi    = trim(isset($_POST['prodi']) ? $_POST['prodi'] : '');
        $angkatan = trim(isset($_POST['angkatan']) ? $_POST['angkatan'] : '');

        // --- VALIDASI DASAR ---
        if ($nim === '' || $nama === '') {
            $_SESSION['flash_error'] = "NIM dan Nama wajib diisi.";
            header("Location: bimbingan.php");
            exit;
        }

        // --- VALIDASI FOTO ---
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = "Foto wajib diupload (format JPG).";
            header("Location: bimbingan.php");
            exit;
        }

        $foto = $_FILES['foto'];

        // cek tipe file
        $allowed = ['image/jpeg', 'image/jpg'];
        if (!in_array($foto['type'], $allowed)) {
            $_SESSION['flash_error'] = "Foto harus berupa file JPG.";
            header("Location: bimbingan.php");
            exit;
        }

        // cek ekstensi file
        $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        if ($ext !== 'jpg' && $ext !== 'jpeg') {
            $_SESSION['flash_error'] = "Foto harus berekstensi .jpg";
            header("Location: bimbingan.php");
            exit;
        }

        // --- SIMPAN FOTO ---
        $dest = __DIR__ . "/../../../public/uploads/profiles/" . $nim . ".jpg";
        move_uploaded_file($foto['tmp_name'], $dest);

        // --- INSERT KE DATABASE ---
        $stmt = $conn->prepare("
            INSERT INTO mahasiswa (nim, user_id, nama, email, prodi, angkatan, status, foto, kategori, tanggal_join)
            VALUES (?, NULL, ?, ?, ?, ?, 'aktif', ?, 'bimbingan', ?)
        ");

        $stmt->execute([
            $nim,
            $nama,
            $email,
            $prodi,
            $angkatan,
            $nim . ".jpg",    // nama file foto
            date("Y-m-d")
        ]);

        $_SESSION['flash_success'] = "Mahasiswa bimbingan berhasil ditambahkan.";
        header("Location: bimbingan.php");
        exit;
    }

    // Update mahasiswa (detail modal)
    if (isset($_POST['action']) && $_POST['action'] === 'update') {

        $nim = isset($_POST['nim_old']) ? $_POST['nim_old'] : null; // identify by nim_old
        $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $prodi = isset($_POST['prodi']) ? trim($_POST['prodi']) : '';
        $angkatan = isset($_POST['angkatan']) ? trim($_POST['angkatan']) : '';
        $status = isset($_POST['status']) ? trim($_POST['status']) : 'aktif';

        if (!$nim) {
            $_SESSION['flash_error'] = "Identifier tidak ditemukan.";
        } else {
            $stmt = $conn->prepare("UPDATE mahasiswa SET nama=?, email=?, prodi=?, angkatan=?, status=? WHERE nim = ?");
            $stmt->execute([$nama, $email, $prodi, $angkatan, $status, $nim]);
            $_SESSION['flash_success'] = "Data mahasiswa diperbarui.";
        }
        // kembali ke page yang sama, pake GET page param kalau ada
        $redir = "bimbingan.php";
        header("Location: $redir");
        exit;
    }

    // Hapus mahasiswa bimbingan
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $nim = isset($_POST['nim']) ? $_POST['nim'] : null;
        if ($nim) {
            $stmt = $conn->prepare("DELETE FROM mahasiswa WHERE nim = ? AND kategori = 'bimbingan'");
            $stmt->execute([$nim]);
            $_SESSION['flash_success'] = "Mahasiswa bimbingan dihapus.";
        } else {
            $_SESSION['flash_error'] = "NIM tidak ditemukan.";
        }
        header("Location: bimbingan.php");
        exit;
    }
}

// ====================== LIST + SEARCH + PAGINATION ======================
$page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
$perPage = 8;
$offset = ($page - 1) * $perPage;

$search = isset($_GET['q']) ? trim($_GET['q']) : "";
$where = "WHERE kategori = 'bimbingan'";
$params = [];

if ($search !== '') {
    $where .= " AND (LOWER(nama) LIKE ? OR LOWER(nim) LIKE ?)";
    $params[] = '%'.strtolower($search).'%';
    $params[] = '%'.strtolower($search).'%';
}

// total count
$stmtCount = $conn->prepare("SELECT COUNT(*) FROM mahasiswa $where");
$stmtCount->execute($params);
$total = (int)$stmtCount->fetchColumn();
$totalPages = (int)ceil($total / $perPage);

// fetch page
$sql = "SELECT nim, nama, email, prodi, angkatan, status, foto, tanggal_join
        FROM mahasiswa
        $where
        ORDER BY tanggal_join DESC
        LIMIT ? OFFSET ?";
$paramsPage = array_merge($params, [$perPage, $offset]);
$stmt = $conn->prepare($sql);

// PDO wants integers for LIMIT/OFFSET depending on DB. Bind explicitly.
$bindIndex = 1;
foreach ($params as $p) {
    $stmt->bindValue($bindIndex++, $p);
}
$stmt->bindValue($bindIndex++, $perPage, PDO::PARAM_INT);
$stmt->bindValue($bindIndex++, $offset, PDO::PARAM_INT);
$stmt->execute();
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// flash messages
$flash_success = (isset($_SESSION['flash_success'])) ? $_SESSION['flash_success'] : null;
$flash_error   = (isset($_SESSION['flash_error'])) ? $_SESSION['flash_error'] : null;
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

<div class="d-flex align-items-center gap-3 mb-3">
    <form class="d-flex gap-2 w-100" method="GET" style="max-width:800px;">
        <input type="text" name="q" class="form-control" placeholder="Cari nama atau NIM..." value="<?= safe($search) ?>">
        <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
    </form>

    <button class="btn btn-primary ms-auto" onclick="toggleAdd()">+ Tambah Bimbingan</button>
</div>

<!-- ADD FORM (toggle) -->
<div id="addBox" class="card shadow-sm border-0 p-3 mb-4" style="display:none;">
        <form method="POST" enctype="multipart/form-data" class="row g-2 align-items-end">
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

        <div class="col-md-3 mt-2">
            <label class="form-label small">Foto (JPG)</label>
            <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg" required>
        </div>

        <div class="col-12 text-end mt-3">
            <button type="button" class="btn btn-secondary" onclick="toggleAdd()">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>

</div>

<!-- LIST -->
<div class="row g-4">
    <?php if(empty($list)): ?>
        <div class="col-12">
            <div class="card p-4 text-center text-muted">Belum ada mahasiswa bimbingan.</div>
        </div>
    <?php else: ?>
        <?php foreach($list as $m): ?>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card shadow-sm p-3 text-center h-100">
                    <div class="mb-2">
                        <img src="../../../public/uploads/profiles/<?= safe($m['nim']) ?>.jpg"
                             onerror="this.src='../../../public/assets/img/default-user.png';"
                             class="rounded-circle" style="width:80px;height:80px;object-fit:cover;">
                    </div>

                    <h6 class="fw-bold mb-1"><?= safe($m['nama']) ?></h6>
                    <div class="text-muted small mb-2"><?= safe($m['nim']) ?></div>
                    <div>
                        <span class="badge <?= $m['status']=='aktif' ? 'bg-success' : 'bg-secondary' ?>"><?= safe($m['status']) ?></span>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm w-100" onclick="openDetail('<?= rawurlencode($m['nim']) ?>')">
                            <i class="bi bi-eye"></i> Detail
                        </button>
                        <form method="POST" onsubmit="return confirm('Hapus data bimbingan?');" style="width:120px;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="nim" value="<?= safe($m['nim']) ?>">
                            <button class="btn btn-outline-danger btn-sm w-100" type="submit"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- PAGINATION -->
<?php if($totalPages > 1): ?>
    <nav class="mt-4 d-flex justify-content-center">
        <ul class="pagination pagination-sm">
            <li class="page-item <?= $page<=1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => max(1,$page-1)])) ?>">Prev</a>
            </li>

            <?php
            // show up to 7 page buttons centered around current page
            $start = max(1, $page - 3);
            $end = min($totalPages, $page + 3);
            for ($p = $start; $p <= $end; $p++): ?>
                <li class="page-item <?= $p==$page ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page'=>$p])) ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= $page>=$totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => min($totalPages,$page+1)])) ?>">Next</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>


<!-- DETAIL MODAL -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="detailForm">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="nim_old" id="nim_old">
        <div class="modal-header">
          <h5 class="modal-title">Detail Mahasiswa Bimbingan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row g-3">
                <div class="col-md-4 text-center">
                    <img id="detailPhoto" src="../../../public/assets/img/default-user.png" style="width:140px;height:140px;object-fit:cover;border-radius:8px;">
                </div>
                <div class="col-md-8">
                    <div class="mb-2">
                        <label class="form-label small">NIM</label>
                        <input class="form-control" id="nim_field" name="nim_display" disabled>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Nama</label>
                        <input class="form-control" id="nama_field" name="nama" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Email</label>
                        <input class="form-control" id="email_field" name="email" type="email">
                    </div>
                    <div class="mb-2 row">
                        <div class="col">
                            <label class="form-label small">Prodi</label>
                            <input class="form-control" id="prodi_field" name="prodi">
                        </div>
                        <div class="col">
                            <label class="form-label small">Angkatan</label>
                            <input class="form-control" id="angkatan_field" name="angkatan">
                        </div>
                        <div class="col">
                            <label class="form-label small">Status</label>
                            <select class="form-control" id="status_field" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="lulus">Lulus</option>
                                <option value="cuti">Cuti</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-2 small text-muted">
                        <strong>Catatan:</strong> Edit lalu tekan <em>Simpan Perubahan</em>.
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function toggleAdd(){
    const box = document.getElementById('addBox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
}

// open detail modal and populate fields via AJAX-ish fetch to same file using query param (we'll read from PHP array in page)
function openDetail(nim){
    // find data from rendered list (we can request via fetch to endpoint returning JSON, but to keep simple we will call server endpoint)
    fetch('bimbingan_detail_api.php?nim=' + nim)
        .then(r => r.json())
        .then(data => {
            if(data.error){
                alert(data.error);
                return;
            }
            const d = data.data;
            document.getElementById('nim_old').value = d.nim;
            document.getElementById('nim_field').value = d.nim;
            document.getElementById('nama_field').value = d.nama;
            document.getElementById('email_field').value = d.email;
            document.getElementById('prodi_field').value = d.prodi;
            document.getElementById('angkatan_field').value = d.angkatan;
            document.getElementById('status_field').value = d.status || 'aktif';
            document.getElementById('detailPhoto').src = '../../../public/uploads/profiles/' + nim + '.jpg';
            // show modal
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        })
        .catch(err => {
            console.error(err);
            alert('Gagal ambil data.');
        });
}
</script>

<style>
.card .btn { border-radius: 8px; }
</style>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
