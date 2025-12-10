<?php
$active = "user";
$title = "User Management";

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

/* ===============================
   SEARCH + PAGINATION SETTINGS
================================ */
$search = $_GET['q'] ?? "";
$perPage = 8;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

/* ===============================
   MAIN USER QUERY (FILTER + PAGINATION)
================================ */
$sql = "
    SELECT 
        u.user_id, u.username, u.nip, u.nim,
        COALESCE(d.nama, m.nama, 'User Tanpa Nama') AS nama,
        STRING_AGG(r.role_name, ',') AS roles,

        MIN(
            CASE 
                WHEN r.role_name = 'ketua lab' THEN 1
                WHEN r.role_name = 'admin' THEN 2
                WHEN r.role_name = 'operator' THEN 3
                WHEN r.role_name = 'dosen' THEN 4
                WHEN r.role_name = 'mahasiswa' THEN 5
                ELSE 99
            END
        ) AS role_sort

    FROM users u
    LEFT JOIN dosen d ON d.nip = u.nip
    LEFT JOIN mahasiswa m ON m.nim = u.nim
    LEFT JOIN user_roles ur ON ur.user_id = u.user_id
    LEFT JOIN roles r ON r.role_id = ur.role_id

    WHERE COALESCE(d.nama, m.nama, '') ILIKE :search

    GROUP BY u.user_id, d.nama, m.nama
    ORDER BY role_sort ASC, nama ASC
    LIMIT :limit OFFSET :offset
";

$stmt = $conn->prepare($sql);
$stmt->bindValue(":search", "%".$search."%", PDO::PARAM_STR);
$stmt->bindValue(":limit", $perPage, PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   GET TOTAL DATA UNTUK PAGINATION
================================ */
$countStmt = $conn->prepare("
    SELECT COUNT(*) FROM users u
    LEFT JOIN dosen d ON d.nip = u.nip
    LEFT JOIN mahasiswa m ON m.nim = u.nim
    WHERE COALESCE(d.nama, m.nama, '') ILIKE :search
");
$countStmt->bindValue(":search", "%".$search."%", PDO::PARAM_STR);
$countStmt->execute();
$totalRows = $countStmt->fetchColumn();
$totalPages = ceil($totalRows / $perPage);

/* ===============================
   ROLE PRIORITY LOGIC
================================ */
function resolveRole($rolesRaw) {
    $roles = explode(",", (string)$rolesRaw);

    $priority = [
        "ketua lab" => 1,
        "admin" => 2,
        "operator" => 3,
        "dosen" => 4,
        "mahasiswa" => 5
    ];

    $selected = "User";
    $lowest = 999;

    foreach ($roles as $r) {
        $r = trim($r);
        if (isset($priority[$r]) && $priority[$r] < $lowest) {
            $selected = ucfirst(str_replace("_", " ", $r));
            $lowest = $priority[$r];
        }
    }
    return $selected;
}

// ORDER card tampil: ketua lab → admin → operator → dosen → mahasiswa
function rolePriority($roles) {
    $roles = explode(",", (string)$roles);

    if (in_array("ketua lab", $roles)) return 1;
    if (in_array("admin", $roles)) return 2;
    if (in_array("operator", $roles)) return 3;
    if (in_array("dosen", $roles)) return 4;
    if (in_array("mahasiswa", $roles)) return 5;

    return 99;
}


ob_start();
?>


<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-people-fill"></i> User Management
</h2>

<div class="d-flex justify-content-between mb-3">
    
    <!-- SEARCH -->
    <form method="GET" class="d-flex align-items-center gap-2">
        <input type="text" 
            name="q" 
            value="<?= htmlspecialchars($search) ?>" 
            class="form-control" 
            placeholder="Cari Nama Pengguna...">
        <button class="btn btn-outline-primary">
            <i class="bi bi-search"></i>
        </button>
        <a href="user.php" class="btn btn-outline-secondary d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i>
            <span>batal</span>
        </a>
    </form>
    <!-- ADD BUTTON -->
    <a href="add_dosen_step1.php" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Tambah Dosen
    </a>
</div>

<div class="row g-4">

<?php foreach($users as $u): ?>
    <?php $roleUtama = resolveRole($u['roles']); ?>

    <div class="col-6 col-md-4 col-lg-3 mb-4">
        <div class="card shadow-sm border-0 p-3 text-center user-card">

            <!-- FOTO -->
            <div class="user-avatar-wrapper">
                <img src="../../../public/uploads/profiles/<?=$u['user_id']?>.jpg"
                     onerror="this.src='../../../public/assets/img/default-user.png';"
                     class="user-avatar">
            </div>

            <!-- NAMA -->
            <h6 class="fw-bold mt-3 mb-1">
                <?= htmlspecialchars($u['nama']) ?>
            </h6>

            <!-- ROLE -->
            <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-1 mb-2" 
                  style="border-radius: 12px;">
                <?= htmlspecialchars($roleUtama) ?>
            </span>

            <a href="detail_user.php?id=<?=$u['user_id']?>" 
               class="btn btn-outline-primary btn-sm w-100 mt-2">
                <i class="bi bi-eye"></i> Detail
            </a>

        </div>
    </div>
<?php endforeach; ?>

</div>

<!-- PAGINATION -->
<?php if ($totalPages > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">

        <!-- Prev -->
        <li class="page-item <?= ($page <= 1 ? 'disabled' : '') ?>">
            <a class="page-link" href="?page=<?= $page-1 ?>&q=<?= urlencode($search) ?>">Prev</a>
        </li>

        <!-- Number Buttons -->
        <?php for($p=1; $p <= $totalPages; $p++): ?>
            <li class="page-item <?= ($p == $page ? 'active' : '') ?>">
                <a class="page-link" href="?page=<?= $p ?>&q=<?= urlencode($search) ?>">
                    <?= $p ?>
                </a>
            </li>
        <?php endfor; ?>

        <!-- Next -->
        <li class="page-item <?= ($page >= $totalPages ? 'disabled' : '') ?>">
            <a class="page-link" href="?page=<?= $page+1 ?>&q=<?= urlencode($search) ?>">Next</a>
        </li>

    </ul>
</nav>
<?php endif; ?>
</div>


<style>
.user-card {
    border-radius: 14px;
    transition: .25s ease;
}
.user-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,.15);
}

.user-photo {
    width: 70px;
    height: 70px;
    object-fit: cover;
}

.role-badge {
    background: #004aad;
    color: white;
    padding: 4px 8px;
    border-radius: 8px;
    margin-top: 4px;
    font-size: 12px;
}
.user-card {
    border-radius: 12px;
    overflow: hidden;
    transition: .25s ease;
}

.user-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 14px rgba(0,0,0,0.08);
}

.user-avatar-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    padding-top: 15px;
}

.user-avatar {
    width: 85px;
    height: 85px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e9efff;
}

</style>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
