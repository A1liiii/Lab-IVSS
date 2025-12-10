<?php
// app/roles/dosen/publikasi.php

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("dosen");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$active = "publikasi";
$title  = "Publikasi Saya";

function safe($v){ return htmlspecialchars($v ?? "-", ENT_QUOTES,'UTF-8'); }

// Ambil user_id dosen
$user_id = $_SESSION['user']['user_id'] ?? null;

// =====================================================
// ================= HANDLE FORM ACTIONS ===============
// =====================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    // ADD
    if ($_POST['action'] === "add") {
        $judul = trim($_POST['judul']);
        $tahun = trim($_POST['tahun']);
        $link  = trim($_POST['link']);

        if ($judul === "" || $tahun === "") {
            $_SESSION['flash_error'] = "Judul dan Tahun wajib diisi.";
            header("Location: publikasi.php");
            exit;
        }

        $stmt = $conn->prepare("
            INSERT INTO publikasi (user_id, judul, tahun, link)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $judul, $tahun, $link]);

        $_SESSION['flash_success'] = "Publikasi berhasil ditambahkan.";
        header("Location: publikasi.php");
        exit;
    }

    // UPDATE
    if ($_POST['action'] === "update") {
        $id    = $_POST['publikasi_id'];
        $judul = trim($_POST['judul']);
        $tahun = trim($_POST['tahun']);
        $link  = trim($_POST['link']);

        $stmt = $conn->prepare("
            UPDATE publikasi 
            SET judul=?, tahun=?, link=?
            WHERE publikasi_id=? AND user_id=?
        ");
        $stmt->execute([$judul, $tahun, $link, $id, $user_id]);

        $_SESSION['flash_success'] = "Publikasi berhasil diperbarui.";
        header("Location: publikasi.php");
        exit;
    }

    // DELETE
    if ($_POST['action'] === "delete") {
        $id = $_POST['publikasi_id'];
        $stmt = $conn->prepare("DELETE FROM publikasi WHERE publikasi_id=? AND user_id=?");
        $stmt->execute([$id, $user_id]);

        $_SESSION['flash_success'] = "Publikasi berhasil dihapus.";
        header("Location: publikasi.php");
        exit;
    }
}

// =====================================================
// ===================== LISTING ========================
// =====================================================

// Search
$search = strtolower(trim($_GET['q'] ?? ""));

$where = "WHERE p.user_id = ?";
$params = [$user_id];

if ($search !== "") {
    $where .= " AND (LOWER(p.judul) LIKE ? OR LOWER(p.tahun) LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Pagination
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 8;
$offset   = ($page - 1) * $perPage;

// Count
$stmt = $conn->prepare("SELECT COUNT(*) FROM publikasi p $where");
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

// Fetch data
$sql = "
    SELECT 
        p.publikasi_id, p.judul, p.tahun, p.link,
        d.nama AS penulis
    FROM publikasi p
    LEFT JOIN users u ON u.user_id = p.user_id
    LEFT JOIN dosen d ON d.nip = u.nip
    $where
    ORDER BY p.tahun DESC, p.publikasi_id DESC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);

$bind = 1;
foreach ($params as $p) {
    $stmt->bindValue($bind++, $p);
}
$stmt->bindValue($bind++, $perPage, PDO::PARAM_INT);
$stmt->bindValue($bind++, $offset, PDO::PARAM_INT);

$stmt->execute();
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Flash
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-journal-text"></i> Publikasi Saya
</h2>

<?php if($flash_success): ?>
<div class="alert alert-success"><?= safe($flash_success) ?></div>
<?php endif; ?>

<?php if($flash_error): ?>
<div class="alert alert-danger"><?= safe($flash_error) ?></div>
<?php endif; ?>

<!-- Search + Add -->
<div class="d-flex align-items-center gap-3 mb-3">
    <form method="GET" class="d-flex gap-2 w-100" style="max-width:700px;">
        <input type="text" class="form-control" placeholder="Cari judul atau tahun..." name="q" value="<?= safe($search) ?>">
        <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
    </form>

    <button class="btn btn-primary ms-auto" onclick="toggleAdd()">+ Tambah</button>
</div>

<!-- ADD FORM BOX -->
<div id="addBox" class="card shadow-sm border-0 p-3 mb-4" style="display:none;">
    <form method="POST" class="row g-3">
        <input type="hidden" name="action" value="add">

        <div class="col-md-6">
            <label class="form-label small">Judul</label>
            <input class="form-control" name="judul" required>
        </div>

        <div class="col-md-2">
            <label class="form-label small">Tahun</label>
            <input class="form-control" name="tahun" required>
        </div>

        <div class="col-md-4">
            <label class="form-label small">Link Publikasi</label>
            <input class="form-control" name="link">
        </div>

        <div class="col-12 text-end">
            <button type="button" class="btn btn-secondary" onclick="toggleAdd()">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>

<!-- LIST (GRID CARD) -->
<div class="row g-4">
<?php if(empty($list)): ?>
    <div class="col-12">
        <div class="card p-4 text-center text-muted">Belum ada publikasi.</div>
    </div>
<?php else: ?>
    <?php foreach($list as $p): ?>
        <div class="col-12 col-md-6 col-lg-3">
            <a href="<?= safe($p['link']) ?>" target="_blank" style="text-decoration:none;color:inherit;">
                <div class="card h-100 shadow-sm p-3">
                    <h6 class="fw-bold mb-1"><?= safe($p['judul']) ?></h6>
                    <div class="small text-muted mb-2">
                        Oleh: <?= safe($p['penulis']) ?><br>
                        Tahun: <?= safe($p['tahun']) ?>
                    </div>

                    <div class="d-flex gap-2 mt-auto">
                        <button type="button"
                                class="btn btn-outline-primary btn-sm w-100"
                                onclick="openEdit(<?= $p['publikasi_id'] ?>, '<?= safe($p['judul']) ?>', '<?= safe($p['tahun']) ?>', '<?= safe($p['link']) ?>'); return false;">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <form method="POST" onsubmit="return confirm('Hapus publikasi ini?')" style="width:60px;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="publikasi_id" value="<?= $p['publikasi_id'] ?>">
                            <button class="btn btn-outline-danger btn-sm w-100"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

<!-- PAGINATION -->
<?php if($totalPages > 1): ?>
<nav class="mt-4 d-flex justify-content-center">
    <ul class="pagination pagination-sm">
        <li class="page-item <?= $page<=1 ? 'disabled':'' ?>">
            <a class="page-link" href="?<?= http_build_query(['page'=>$page-1,'q'=>$search]) ?>">Prev</a>
        </li>
        <?php for($i=1;$i<=$totalPages;$i++): ?>
            <li class="page-item <?= $page==$i?'active':'' ?>">
                <a class="page-link" href="?<?= http_build_query(['page'=>$i,'q'=>$search]) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= $page>=$totalPages ? 'disabled':'' ?>">
            <a class="page-link" href="?<?= http_build_query(['page'=>$page+1,'q'=>$search]) ?>">Next</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
    <form method="POST">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="publikasi_id" id="edit_id">

        <div class="modal-header">
            <h5 class="modal-title">Edit Publikasi</h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <label class="form-label small">Judul</label>
            <input class="form-control mb-2" name="judul" id="edit_judul" required>

            <label class="form-label small">Tahun</label>
            <input class="form-control mb-2" name="tahun" id="edit_tahun" required>

            <label class="form-label small">Link</label>
            <input class="form-control" name="link" id="edit_link">
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>
</div>
</div>

<style>
/* --- CARD ULTRA HD --- */
.card-ultra {
    border: none !important;
    border-radius: 18px;
    padding: 20px;
    background: #ffffff;
    transition: all .25s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(0,0,0,0.06);
}

/* Glow hover */
.card-ultra:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 26px rgba(0,0,0,0.12);
}

/* Judul */
.card-ultra h6 {
    font-weight: 700;
    font-size: 15px;
    color: #1a1a1a;
    line-height: 1.35;
}

/* Tahun + penulis */
.card-meta {
    font-size: 13px;
    color: #6c757d;
}

/* separator refined */
.card-sep {
    height: 1px;
    background: linear-gradient(to right, #e0e0e0, transparent);
    margin: 10px 0 12px;
}

/* Button group */
.card-btn-group button {
    border-radius: 10px !important;
}

/* Link mask (agar 1 card bisa jadi link tapi BTN tetap bisa diklik) */
.card-link-mask {
    position: absolute;
    inset: 0;
    z-index: 1;
}

/* BTN di atas mask */
.card-btn-group {
    position: relative;
    z-index: 5;
}

/* Smooth hover effect for title glow */
.card-ultra:hover h6 {
    color: #0d6efd;
}

/* Grid spacing refine */
.publikasi-grid .col-12, 
.publikasi-grid .col-md-6,
.publikasi-grid .col-lg-3 {
    display: flex;
}
</style>


<script>
function toggleAdd(){
    const box = document.getElementById("addBox");
    box.style.display = box.style.display === "none" ? "block" : "none";
}

// Open edit modal
function openEdit(id, judul, tahun, link){
    document.getElementById("edit_id").value    = id;
    document.getElementById("edit_judul").value = judul;
    document.getElementById("edit_tahun").value = tahun;
    document.getElementById("edit_link").value  = link;

    const modal = new bootstrap.Modal(document.getElementById("editModal"));
    modal.show();
}
</script>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
