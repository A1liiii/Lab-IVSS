<?php
// app/roles/dosen/mata_kuliah.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("dosen");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$active = "mata_kuliah";
$title = "Mata Kuliah";

// helper
function safe($v){ return htmlspecialchars($v ?? "-", ENT_QUOTES, 'UTF-8'); }

// ambil nip dosen via user_id session
$user_id = $_SESSION['user']['user_id'] ?? null;
$stmt = $conn->prepare("SELECT nip FROM users WHERE user_id = ? LIMIT 1");
$stmt->execute([$user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$nip = $row['nip'] ?? null;
if (!$nip) {
    die("<div class='alert alert-warning'>NIP dosen tidak ditemukan. Pastikan akun terhubung dengan data dosen.</div>");
}

// ===================== helper generate kode mata kuliah =====================
function generateKodeMK($nama, $prodi, $tahun, $conn) {
    // inisial MK dari tiap kata di nama mata kuliah
    $inisialMK = "";
    foreach (preg_split('/\s+/', trim($nama)) as $w) {
        $inisialMK .= strtoupper(substr($w, 0, 1));
    }
    // inisial prodi
    $inisialProdi = "";
    foreach (preg_split('/\s+/', trim($prodi)) as $w) {
        $inisialProdi .= strtoupper(substr($w, 0, 1));
    }

    // tahun format "2025/2026" atau "2025": ambil dua digit akhir dari masing2 (fallback)
    $tahun = str_replace(" ", "", $tahun);
    if (str_contains($tahun, "/")) {
        [$s,$e] = explode("/", $tahun);
        $s2 = substr($s, -2);
        $e2 = substr($e, -2);
    } else {
        $s2 = substr($tahun, -2);
        $e2 = $s2;
    }

    $base = "{$inisialMK}-{$inisialProdi}-{$s2}{$e2}";

    // pastikan unik, kalau ada collision tambahkan -01, -02 ...
    $candidate = $base;
    $i = 1;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM mata_kuliah WHERE kode_matkul = ?");
    while (true) {
        $stmt->execute([$candidate]);
        $c = (int)$stmt->fetchColumn();
        if ($c === 0) break;
        $candidate = $base . "-" . str_pad($i, 2, "0", STR_PAD_LEFT);
        $i++;
        if ($i > 999) break;
    }
    return $candidate;
}

// ===================== HANDLE ACTIONS (POST) =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ADD MK
    if (isset($_POST['add_mk'])) {
        $nama = trim($_POST['nama_matkul'] ?? '');
        $semester = trim($_POST['semester'] ?? '');
        $sks = trim($_POST['sks'] ?? '');
        $prodi = trim($_POST['prodi'] ?? '');
        $tahun_ajar = trim($_POST['tahun_ajar'] ?? '');

        if ($nama === '') {
            $_SESSION['flash_error'] = "Nama mata kuliah wajib diisi.";
            header("Location: mata_kuliah.php");
            exit;
        }

        $kode = generateKodeMK($nama, $prodi ?: "XX", $tahun_ajar ?: date("Y"), $conn);

        $stmt = $conn->prepare("INSERT INTO mata_kuliah (kode_matkul, nip, nama_matkul, semester, prodi, sks, tahun_ajar)
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$kode, $nip, $nama, $semester ?: null, $prodi ?: null, $sks ?: null, $tahun_ajar ?: null]);

        $_SESSION['flash_success'] = "Mata kuliah ditambahkan.";
        header("Location: mata_kuliah.php");
        exit;
    }

    // UPDATE MK
    if (isset($_POST['update_mk'])) {
        $kode = $_POST['kode_matkul'] ?? null;
        $nama = trim($_POST['nama_matkul'] ?? '');
        $semester = trim($_POST['semester'] ?? null);
        $sks = trim($_POST['sks'] ?? null);
        $prodi = trim($_POST['prodi'] ?? null);
        $tahun_ajar = trim($_POST['tahun_ajar'] ?? null);

        if ($kode) {
            $stmt = $conn->prepare("UPDATE mata_kuliah SET nama_matkul=?, semester=?, prodi=?, sks=?, tahun_ajar=? WHERE kode_matkul=? AND nip=?");
            $stmt->execute([$nama, $semester ?: null, $prodi ?: null, $sks ?: null, $tahun_ajar ?: null, $kode, $nip]);
            $_SESSION['flash_success'] = "Perubahan disimpan.";
        } else {
            $_SESSION['flash_error'] = "Kode mata kuliah tidak ditemukan.";
        }
        header("Location: mata_kuliah.php");
        exit;
    }

    // DELETE MK
    if (isset($_POST['delete_mk'])) {
        $kode = $_POST['kode_matkul'] ?? null;
        if ($kode) {
            $stmt = $conn->prepare("DELETE FROM mata_kuliah WHERE kode_matkul = ? AND nip = ?");
            $stmt->execute([$kode, $nip]);
            $_SESSION['flash_success'] = "Mata kuliah dihapus.";
        }
        header("Location: mata_kuliah.php");
        exit;
    }
}

// ===================== LIST + SEARCH + FILTER =====================
$q = trim($_GET['q'] ?? '');
$filterYear = trim($_GET['tahun'] ?? '');

$where = "WHERE nip = ?";
$params = [$nip];

if ($q !== '') {
    $where .= " AND (LOWER(nama_matkul) LIKE ? OR LOWER(prodi) LIKE ?)";
    $params[] = '%' . mb_strtolower($q, 'UTF-8') . '%';
    $params[] = '%' . mb_strtolower($q, 'UTF-8') . '%';
}
if ($filterYear !== '') {
    $where .= " AND tahun_ajar = ?";
    $params[] = $filterYear;
}

// ambil daftar tahun_ajar yang ada untuk filter dropdown
$stmtYears = $conn->prepare("SELECT DISTINCT tahun_ajar FROM mata_kuliah WHERE nip = ? ORDER BY tahun_ajar DESC");
$stmtYears->execute([$nip]);
$years = $stmtYears->fetchAll(PDO::FETCH_COLUMN);

// fetch data
$sql = "SELECT kode_matkul, nama_matkul, semester, prodi, sks, tahun_ajar
        FROM mata_kuliah
        $where
        ORDER BY tahun_ajar DESC, nama_matkul ASC
        LIMIT 200";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$mkList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// flash
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary"><i class="bi bi-book-half"></i> Mata Kuliah Diampu</h2>

<?php if($flash_success): ?>
    <div class="alert alert-success"><?= safe($flash_success) ?></div>
<?php endif; ?>
<?php if($flash_error): ?>
    <div class="alert alert-danger"><?= safe($flash_error) ?></div>
<?php endif; ?>

<div class="d-flex gap-3 mb-3 align-items-center">
    <form class="d-flex gap-2 align-items-center" method="GET" style="flex:1; max-width:900px;">
        <input type="text" name="q" class="form-control" placeholder="Cari nama mata kuliah atau prodi..." value="<?= safe($q) ?>">
        <select name="tahun" class="form-select" style="width:160px;">
            <option value="">Semua Tahun</option>
            <?php foreach($years as $y): ?>
                <option value="<?= safe($y) ?>" <?= $filterYear===$y ? 'selected' : '' ?>><?= safe($y) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
    </form>

    <div class="ms-auto">
        <button class="btn btn-success" onclick="toggleAdd()">+ Tambah Mata Kuliah</button>
    </div>
</div>

<!-- ADD FORM -->
<div id="addBox" class="card shadow-sm p-3 mb-4" style="display:none;">
    <form method="POST" class="row g-2 align-items-end">
        <input type="hidden" name="add_mk" value="1">
        <div class="col-md-5">
            <label class="form-label small">Nama Mata Kuliah</label>
            <input name="nama_matkul" class="form-control" required>
        </div>
        <div class="col-md-1">
            <label class="form-label small">Semester</label>
            <input name="semester" class="form-control">
        </div>
        <div class="col-md-1">
            <label class="form-label small">SKS</label>
            <input name="sks" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label small">Prodi</label>
            <input name="prodi" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label small">Tahun Ajar</label>
            <input name="tahun_ajar" class="form-control" placeholder="2025/2026">
        </div>

        <div class="col-12 text-end mt-2">
            <button type="button" class="btn btn-secondary" onclick="toggleAdd()">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>

<!-- LIST CARDS (wide card grid) -->
<div class="row g-3">
    <?php if(empty($mkList)): ?>
        <div class="col-12">
            <div class="card p-4 text-center text-muted">Belum ada mata kuliah.</div>
        </div>
    <?php else: ?>
        <?php foreach($mkList as $m): ?>
            <div class="col-12">
                <div class="card shadow-sm p-3">
                    <div class="d-flex align-items-center">
                        <div style="flex:1;">
                            <a href="mata_kuliah_detail.php?kode=<?= rawurlencode($m['kode_matkul']) ?>" class="stretched-link text-decoration-none">
                                <h5 class="mb-1 fw-bold"><?= safe($m['nama_matkul']) ?></h5>
                            </a>
                            <div class="small text-muted">
                                <?= safe($m['prodi']) ?> • Semester <?= safe($m['semester']) ?> • <?= safe($m['sks']) ?> SKS
                                <span class="ms-3">Tahun: <?= safe($m['tahun_ajar']) ?></span>
                            </div>
                        </div>

                        <div class="ms-3 d-flex gap-2 align-items-center">
                            <button class="btn btn-outline-primary btn-sm" onclick="openDetail('<?= rawurlencode($m['kode_matkul']) ?>')">
                                <i class="bi bi-eye"></i>
                            </button>

                            <!-- inline edit form (tampilkan modal edit ketika diperlukan) -->
                            <button class="btn btn-outline-warning btn-sm" onclick="openEditModal('<?= rawurlencode($m['kode_matkul']) ?>')">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <form method="POST" onsubmit="return confirm('Hapus mata kuliah ini?');" style="display:inline;">
                                <input type="hidden" name="delete_mk" value="1">
                                <input type="hidden" name="kode_matkul" value="<?= safe($m['kode_matkul']) ?>">
                                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- DETAIL MODAL (dapat dari API) -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Mata Kuliah</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="detailBody">
        <div class="mb-2"><strong>Nama:</strong> <span id="d_nama"></span></div>
        <div class="mb-2"><strong>Semester:</strong> <span id="d_semester"></span></div>
        <div class="mb-2"><strong>SKS:</strong> <span id="d_sks"></span></div>
        <div class="mb-2"><strong>Prodi:</strong> <span id="d_prodi"></span></div>
        <div class="mb-2"><strong>Tahun Ajar:</strong> <span id="d_tahun"></span></div>
        <div class="mt-3 small text-muted" id="d_kode"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="editForm">
        <input type="hidden" name="update_mk" value="1">
        <input type="hidden" name="kode_matkul" id="edit_kode">
        <div class="modal-header">
          <h5 class="modal-title">Edit Mata Kuliah</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row g-2">
                <div class="col-md-8">
                    <label class="form-label small">Nama Mata Kuliah</label>
                    <input name="nama_matkul" id="edit_nama" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Semester</label>
                    <input name="semester" id="edit_semester" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">SKS</label>
                    <input name="sks" id="edit_sks" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Prodi</label>
                    <input name="prodi" id="edit_prodi" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label small">Tahun Ajar</label>
                    <input name="tahun_ajar" id="edit_tahun" class="form-control">
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Toggle add box
function toggleAdd(){
    const b = document.getElementById('addBox');
    b.style.display = b.style.display === 'none' ? 'block' : 'none';
}

// open detail via API
function openDetail(kode){
    fetch('mata_kuliah_detail_api.php?kode=' + encodeURIComponent(kode))
        .then(r=>r.json())
        .then(j=>{
            if (j.error) {
                alert(j.error);
                return;
            }
            const d = j.data;
            document.getElementById('d_nama').textContent = d.nama_matkul || '-';
            document.getElementById('d_semester').textContent = d.semester || '-';
            document.getElementById('d_sks').textContent = d.sks || '-';
            document.getElementById('d_prodi').textContent = d.prodi || '-';
            document.getElementById('d_tahun').textContent = d.tahun_ajar || '-';
            document.getElementById('d_kode').textContent = 'Kode: ' + (d.kode_matkul || '-');
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        }).catch(e=>{
            console.error(e);
            alert('Gagal ambil detail.');
        });
}

// open edit modal and fill
function openEditModal(kode){
    fetch('mata_kuliah_detail_api.php?kode=' + encodeURIComponent(kode))
        .then(r=>r.json())
        .then(j=>{
            if (j.error) { alert(j.error); return; }
            const d = j.data;
            document.getElementById('edit_kode').value = d.kode_matkul;
            document.getElementById('edit_nama').value = d.nama_matkul;
            document.getElementById('edit_semester').value = d.semester || '';
            document.getElementById('edit_sks').value = d.sks || '';
            document.getElementById('edit_prodi').value = d.prodi || '';
            document.getElementById('edit_tahun').value = d.tahun_ajar || '';
            const modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        }).catch(e=>{
            console.error(e);
            alert('Gagal ambil data edit.');
        });
}
</script>

<style>
/* sedikit styling supaya card wide rapih */
.card .stretched-link { color: inherit; }
.card { border-radius: 12px; }
</style>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
