<?php
// app/roles/mahasiswa/logs.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("mahasiswa");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$user_id = $_SESSION['user']['user_id'] ?? null;
if(!$user_id) die("User tidak ditemukan.");

// helper
function safe($s){ return htmlspecialchars($s ?? "-", ENT_QUOTES, 'UTF-8'); }

// handle actions (add / update / delete) - inline posts to same file
if($_SERVER['REQUEST_METHOD'] === 'POST'){
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
    if(isset($_POST['action']) && $_POST['action'] === 'update'){
        $log_id = $_POST['log_id'] ?? null;
        $des = trim($_POST['deskripsi'] ?? '');
        if($log_id && $des !== ''){
            $stmt = $conn->prepare("UPDATE log_activity SET deskripsi = ? WHERE log_id = ? AND user_id = ?");
            $stmt->execute([$des, $log_id, $user_id]);
        }
    }
    if(isset($_POST['action']) && $_POST['action'] === 'delete'){
        $log_id = $_POST['log_id'] ?? null;
        if($log_id){
            $stmt = $conn->prepare("DELETE FROM log_activity WHERE log_id = ? AND user_id = ?");
            $stmt->execute([$log_id, $user_id]);
        }
    }
    header("Location: logs.php");
    exit;
}

// pagination & search
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 4;
$offset = ($page-1)*$perPage;

$q = trim($_GET['q'] ?? '');

// count
if($q !== ''){
    $stmt = $conn->prepare("SELECT COUNT(*) FROM log_activity WHERE user_id = ? AND deskripsi ILIKE ?");
    $stmt->execute([$user_id, "%$q%"]);
} else {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM log_activity WHERE user_id = ?");
    $stmt->execute([$user_id]);
}
$total = (int)$stmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));

// fetch
if($q !== ''){
    $stmt = $conn->prepare("SELECT log_id, deskripsi, waktu FROM log_activity WHERE user_id = ? AND deskripsi ILIKE ? ORDER BY waktu DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $user_id);
    $stmt->bindValue(2, "%$q%");
    $stmt->bindValue(3, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(4, $offset, PDO::PARAM_INT);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("SELECT log_id, deskripsi, waktu FROM log_activity WHERE user_id = ? ORDER BY waktu DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $user_id);
    $stmt->bindValue(2, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
}
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>
<h2 class="fw-bold mb-4 text-primary"><i class="bi bi-clock-history"></i> Aktivitas Saya</h2>

<div class="card p-3 mb-3">
  <form class="d-flex gap-2" method="GET">
    <input name="q" class="form-control form-control-sm" placeholder="Cari aktivitas..." value="<?= safe($q) ?>">
    <button class="btn btn-outline-primary btn-sm">Cari</button>
  </form>
</div>

<!-- add inline -->
<div class="card p-3 mb-3">
  <form method="POST" class="d-flex gap-2">
    <input type="hidden" name="action" value="add">
    <input name="deskripsi" class="form-control" placeholder="Tambah aktivitas singkat...">
    <button class="btn btn-primary">Tambah</button>
  </form>
</div>

<div class="list-group mb-3">
  <?php if(empty($rows)): ?>
    <div class="card p-3 text-center text-muted">Belum ada aktivitas.</div>
  <?php else: foreach($rows as $r): ?>
    <div class="list-group-item d-flex align-items-start justify-content-between">
      <div class="flex-grow-1 me-3">
        <div class="log-desc" data-id="<?= $r['log_id'] ?>"><?= safe($r['deskripsi']) ?></div>
        <div class="small text-muted"><?= date("d M Y • H:i", strtotime($r['waktu'])) ?></div>
      </div>

      <div class="d-flex gap-2 align-items-start">
        <!-- edit button toggles inline form -->
        <button class="btn btn-outline-primary btn-sm btn-edit" data-id="<?= $r['log_id'] ?>">
          <i class="bi bi-pencil"></i>
        </button>

        <form method="POST" onsubmit="return confirm('Hapus aktivitas?');">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="log_id" value="<?= $r['log_id'] ?>">
          <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
        </form>
      </div>

      <!-- hidden edit form -->
      <div class="edit-row mt-2 w-100 d-none" id="edit-<?= $r['log_id'] ?>">
        <form method="POST" class="d-flex gap-2">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="log_id" value="<?= $r['log_id'] ?>">
          <input name="deskripsi" class="form-control form-control-sm" value="<?= safe($r['deskripsi']) ?>">
          <button class="btn btn-success btn-sm">Simpan</button>
        </form>
      </div>
    </div>
  <?php endforeach; endif; ?>
</div>

<!-- pagination compact -->
<?php if($totalPages > 1): ?>
<nav class="d-flex justify-content-center">
  <ul class="pagination pagination-sm">
    <li class="page-item <?= $page<=1 ? 'disabled':'' ?>">
      <a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>max(1,$page-1)])) ?>">Prev</a>
    </li>
    <?php for($p=1;$p<=$totalPages;$p++): ?>
      <li class="page-item <?= $p==$page ? 'active':'' ?>"><a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>$p])) ?>"><?= $p ?></a></li>
    <?php endfor; ?>
    <li class="page-item <?= $page>=$totalPages ? 'disabled':'' ?>">
      <a class="page-link" href="?<?= http_build_query(array_merge($_GET,['page'=>min($totalPages,$page+1)])) ?>">Next</a>
    </li>
  </ul>
</nav>
<?php endif; ?>

<script>
// inline edit toggles
document.querySelectorAll('.btn-edit').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    const id = btn.dataset.id;
    const el = document.getElementById('edit-' + id);
    el.classList.toggle('d-none');
  });
});
</script>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
