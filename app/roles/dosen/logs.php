<?php
// app/roles/dosen/logs.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("dosen");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$active = "logs";
$title = "Aktivitas Saya";

$user_id = $_SESSION['user']['user_id'] ?? null;
if (!$user_id) die("Invalid session");

// Safety helper
function safe($x){ return htmlspecialchars($x ?? "-", ENT_QUOTES, 'UTF-8'); }


// ========================= HANDLE ACTIONS =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ADD LOG
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        if ($deskripsi === '') {
            $_SESSION['flash_error'] = "Deskripsi tidak boleh kosong.";
            header("Location: logs.php");
            exit;
        }

        // ambil id terbaru lalu +1
        $nextId = $conn->query("SELECT COALESCE(MAX(log_id), 0) + 1 FROM log_activity")->fetchColumn();

        $stmt = $conn->prepare("
            INSERT INTO log_activity(log_id, user_id, deskripsi, aksi, waktu)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$nextId, $user_id, $deskripsi, 'update']);

        $_SESSION['flash_success'] = "Log berhasil ditambahkan.";
        header("Location: logs.php");
        exit;
    }

    // UPDATE LOG
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $log_id = $_POST['log_id'];
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        $stmt = $conn->prepare("UPDATE log_activity SET deskripsi=? WHERE log_id=? AND user_id=?");
        $stmt->execute([$deskripsi, $log_id, $user_id]);

        $_SESSION['flash_success'] = "Log berhasil diperbarui.";
        header("Location: logs.php");
        exit;
    }

    // DELETE LOG
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $log_id = $_POST['log_id'];

        $stmt = $conn->prepare("DELETE FROM log_activity WHERE log_id=? AND user_id=?");
        $stmt->execute([$log_id, $user_id]);

        $_SESSION['flash_success'] = "Log dihapus.";
        header("Location: logs.php");
        exit;
    }
}


// ========================= PAGINATION =========================
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 6;
$offset = ($page - 1) * $perPage;

// count
$stmt = $conn->prepare("SELECT COUNT(*) FROM log_activity WHERE user_id=?");
$stmt->execute([$user_id]);
$total = (int)$stmt->fetchColumn();
$totalPages = ceil($total / $perPage);

// fetch logs
$stmt = $conn->prepare("
    SELECT log_id, deskripsi, aksi, waktu
    FROM log_activity
    WHERE user_id=?
    ORDER BY waktu DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $user_id);
$stmt->bindValue(2, $perPage, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);


// flash messages
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary"><i class="bi bi-clock-history"></i> Aktivitas Saya</h2>

<?php if($flash_success): ?>
<div class="alert alert-success"><?= safe($flash_success) ?></div>
<?php endif; ?>

<?php if($flash_error): ?>
<div class="alert alert-danger"><?= safe($flash_error) ?></div>
<?php endif; ?>


<!-- ADD LOG -->
<div class="card p-3 shadow-sm mb-4 border-0">
    <form method="POST" class="d-flex gap-2">
        <input type="hidden" name="action" value="add">
        <input class="form-control" name="deskripsi" placeholder="Tambahkan aktivitas baru..." required>
        <button class="btn btn-primary"><i class="bi bi-plus-lg"></i></button>
    </form>
</div>


<!-- LOG LIST -->
<div class="row g-3">
<?php if(empty($logs)): ?>
    <div class="col-12 text-center text-muted">Belum ada aktivitas.</div>
<?php else: ?>
    <?php foreach($logs as $log): ?>
        <div class="col-12">
            <div class="card shadow-sm p-3 position-relative">

                <!-- DISPLAY MODE -->
                <div class="log-display">
                    <div class="fw-semibold"><?= safe($log['deskripsi']) ?></div>
                    <div class="small text-muted"><?= date("d M Y • H:i", strtotime($log['waktu'])) ?></div>

                    <div class="position-absolute top-0 end-0 p-2 d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="openEdit(<?= $log['log_id'] ?>)">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <form method="POST" onsubmit="return confirm('Hapus log ini?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="log_id" value="<?= $log['log_id'] ?>">
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>

                <!-- EDIT MODE -->
                <form method="POST" class="log-edit d-none">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="log_id" value="<?= $log['log_id'] ?>">

                    <textarea name="deskripsi" class="form-control mb-2" rows="2"><?= safe($log['deskripsi']) ?></textarea>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="closeEdit(this)">Batal</button>
                        <button class="btn btn-primary btn-sm">Simpan</button>
                    </div>
                </form>

            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>


<!-- PAGINATION -->
<?php if($totalPages > 1): ?>
<nav class="mt-4 d-flex justify-content-center">
    <ul class="pagination pagination-sm">

        <li class="page-item <?= $page <= 1 ? 'disabled':'' ?>">
            <a class="page-link" href="?page=<?= $page-1 ?>">Prev</a>
        </li>

        <?php for($p=max(1,$page-2); $p<=min($totalPages,$page+2); $p++): ?>
        <li class="page-item <?= $page == $p ? 'active':'' ?>">
            <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
        </li>
        <?php endfor; ?>

        <li class="page-item <?= $page >= $totalPages ? 'disabled':'' ?>">
            <a class="page-link" href="?page=<?= $page+1 ?>">Next</a>
        </li>

    </ul>
</nav>
<?php endif; ?>


<script>
function openEdit(id){
    const card = event.target.closest(".card");
    card.querySelector(".log-display").classList.add("d-none");
    card.querySelector(".log-edit").classList.remove("d-none");
}

function closeEdit(btn){
    const card = btn.closest(".card");
    card.querySelector(".log-display").classList.remove("d-none");
    card.querySelector(".log-edit").classList.add("d-none");
}
</script>

<?php
$content = ob_get_clean();
include "_layout.php";
