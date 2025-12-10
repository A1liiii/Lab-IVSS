<?php
// app/roles/operator/proyek.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../../core/auth.php";
requireRole("operator");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$active = "proyek";
$title  = "Manajemen Proyek";

function safe($v){
    return htmlspecialchars((string)(isset($v) ? $v : ""), ENT_QUOTES, 'UTF-8');
}

function excerpt($text, $len = 150){
    $text = trim(strip_tags(isset($text) ? $text : ""));
    if (function_exists('mb_strlen')) {
        if (mb_strlen($text, 'UTF-8') <= $len) return $text;
        return mb_substr($text, 0, $len, 'UTF-8') . "...";
    } else {
        if (strlen($text) <= $len) return $text;
        return substr($text, 0, $len) . "...";
    }
}

function text_length_custom($s){
    $s = isset($s) ? (string)$s : '';
    if (function_exists('mb_strlen')) return mb_strlen($s, 'UTF-8');
    return strlen($s);
}

$currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// ====================== HANDLE POSTS (ADD / UPDATE / DELETE) ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // ambil data anggota dari form (builder)
    $anggotaUsers = (isset($_POST['anggota_user']) && is_array($_POST['anggota_user']))
        ? $_POST['anggota_user'] : array();
    $anggotaRoles = (isset($_POST['anggota_role']) && is_array($_POST['anggota_role']))
        ? $_POST['anggota_role'] : array();
    $allowedRoles = array('ketua','anggota');

    // ---------- TAMBAH PROYEK ----------
    if ($action === 'add') {
        $judul           = isset($_POST['judul']) ? trim($_POST['judul']) : '';
        $deskripsi       = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';
        $tanggal_mulai   = isset($_POST['tanggal_mulai']) ? trim($_POST['tanggal_mulai']) : '';
        $tanggal_selesai = isset($_POST['tanggal_selesai']) ? trim($_POST['tanggal_selesai']) : '';
        $status          = isset($_POST['status']) ? trim($_POST['status']) : 'on going';

        if ($judul === '') {
            $_SESSION['flash_error'] = "Judul proyek wajib diisi.";
            header("Location: proyek.php");
            exit;
        }

        if ($tanggal_mulai === '') {
            $tanggal_mulai = date("Y-m-d");
        }

        if ($status !== 'on going' && $status !== 'selesai') {
            $status = 'on going';
        }

        $stmt = $conn->prepare("
            INSERT INTO proyek (judul, deskripsi, tanggal_mulai, tanggal_selesai, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute(array(
            $judul,
            $deskripsi,
            $tanggal_mulai !== '' ? $tanggal_mulai : null,
            $tanggal_selesai !== '' ? $tanggal_selesai : null,
            $status
        ));

        // ambil ID proyek
        $proyekId = $conn->lastInsertId("proyek_proyek_id_seq");

        // ======= SIMPAN ANGGOTA PROYEK (dari builder) =======
        if (!empty($anggotaUsers)) {
            $stmtAng = $conn->prepare("
                INSERT INTO anggota_proyek (user_id, proyek_id, role)
                VALUES (?, ?, ?)
            ");

            $count = count($anggotaUsers);
            for ($i = 0; $i < $count; $i++) {
                $uid  = (int)$anggotaUsers[$i];
                $role = isset($anggotaRoles[$i]) ? trim($anggotaRoles[$i]) : 'anggota';
                if ($uid <= 0) continue;
                if (!in_array($role, $allowedRoles, true)) $role = 'anggota';
                $stmtAng->execute(array($uid, (int)$proyekId, $role));
            }
        }

        $_SESSION['flash_success'] = "Proyek berhasil ditambahkan.";
        header("Location: proyek.php");
        exit;
    }

    // ---------- UPDATE PROYEK ----------
    if ($action === 'update') {
        $id              = isset($_POST['proyek_id']) ? (int)$_POST['proyek_id'] : 0;
        $judul           = isset($_POST['judul']) ? trim($_POST['judul']) : '';
        $deskripsi       = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';
        $tanggal_mulai   = isset($_POST['tanggal_mulai']) ? trim($_POST['tanggal_mulai']) : '';
        $tanggal_selesai = isset($_POST['tanggal_selesai']) ? trim($_POST['tanggal_selesai']) : '';
        $status          = isset($_POST['status']) ? trim($_POST['status']) : 'on going';

        if (!$id) {
            $_SESSION['flash_error'] = "ID proyek tidak ditemukan.";
            header("Location: proyek.php");
            exit;
        }
        if ($judul === '') {
            $_SESSION['flash_error'] = "Judul proyek wajib diisi.";
            header("Location: proyek.php");
            exit;
        }
        if ($tanggal_mulai === '') {
            $tanggal_mulai = date("Y-m-d");
        }
        if ($status !== 'on going' && $status !== 'selesai') {
            $status = 'on going';
        }

        $stmt = $conn->prepare("
            UPDATE proyek
            SET judul = ?, deskripsi = ?, tanggal_mulai = ?, tanggal_selesai = ?, status = ?
            WHERE proyek_id = ?
        ");
        $stmt->execute(array(
            $judul,
            $deskripsi,
            $tanggal_mulai !== '' ? $tanggal_mulai : null,
            $tanggal_selesai !== '' ? $tanggal_selesai : null,
            $status,
            $id
        ));

        // ======= UPDATE ANGGOTA PROYEK (hapus lama, insert dari builder) =======
        $stmtDelAng = $conn->prepare("DELETE FROM anggota_proyek WHERE proyek_id = ?");
        $stmtDelAng->execute(array($id));

        if (!empty($anggotaUsers)) {
            $stmtAng = $conn->prepare("
                INSERT INTO anggota_proyek (user_id, proyek_id, role)
                VALUES (?, ?, ?)
            ");

            $count = count($anggotaUsers);
            for ($i = 0; $i < $count; $i++) {
                $uid  = (int)$anggotaUsers[$i];
                $role = isset($anggotaRoles[$i]) ? trim($anggotaRoles[$i]) : 'anggota';
                if ($uid <= 0) continue;
                if (!in_array($role, $allowedRoles, true)) $role = 'anggota';
                $stmtAng->execute(array($uid, $id, $role));
            }
        }

        $_SESSION['flash_success'] = "Proyek berhasil diperbarui.";
        header("Location: proyek.php");
        exit;
    }

    // ---------- HAPUS PROYEK ----------
    if ($action === 'delete') {
        $id = isset($_POST['proyek_id']) ? (int)$_POST['proyek_id'] : 0;

        if ($id) {
            $stmtDelAnggota = $conn->prepare("DELETE FROM anggota_proyek WHERE proyek_id = ?");
            $stmtDelAnggota->execute(array($id));

            $stmtDel = $conn->prepare("DELETE FROM proyek WHERE proyek_id = ?");
            $stmtDel->execute(array($id));

            $_SESSION['flash_success'] = "Proyek telah dihapus.";
        } else {
            $_SESSION['flash_error'] = "ID proyek tidak ditemukan.";
        }

        header("Location: proyek.php");
        exit;
    }
}

// ====================== LIST + SEARCH + PAGINATION ======================
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$perPage = 8; // 4 kolom x 2 baris
$offset = ($page - 1) * $perPage;

$search = isset($_GET['q']) ? trim($_GET['q']) : "";
$where  = "WHERE 1=1";
$params = array();

if ($search !== '') {
    $where .= " AND (LOWER(judul) LIKE ? OR LOWER(deskripsi) LIKE ?)";
    $params[] = '%' . strtolower($search) . '%';
    $params[] = '%' . strtolower($search) . '%';
}

// total count proyek
$stmtCount = $conn->prepare("SELECT COUNT(*) FROM proyek $where");
$stmtCount->execute($params);
$total = (int)$stmtCount->fetchColumn();
$totalPages = (int)ceil($total / $perPage);

// LIST PROYEK + RINGKASAN & DETAIL ANGGOTA (untuk builder edit)
$sql = "
    SELECT
        p.proyek_id,
        p.judul,
        p.deskripsi,
        p.tanggal_mulai,
        p.tanggal_selesai,
        p.status,
        COALESCE(
            (
                SELECT string_agg(
                    u.username || ' (' || ap.role || ')',
                    ', ' ORDER BY ap.role, u.username
                )
                FROM anggota_proyek ap
                JOIN users u ON u.user_id = ap.user_id
                WHERE ap.proyek_id = p.proyek_id
            ),
            ''
        ) AS anggota_list,
        COALESCE(
            (
                SELECT string_agg(
                    ap.user_id::text || ':' || u.username || ':' || ap.role,
                    '|' ORDER BY ap.role, u.username
                )
                FROM anggota_proyek ap
                JOIN users u ON u.user_id = ap.user_id
                WHERE ap.proyek_id = p.proyek_id
            ),
            ''
        ) AS anggota_detail
    FROM proyek p
    $where
    ORDER BY p.tanggal_mulai DESC NULLS LAST, p.proyek_id DESC
    LIMIT ? OFFSET ?
";

$paramsPage = $params;
$paramsPage[] = $perPage;
$paramsPage[] = $offset;

$stmt = $conn->prepare($sql);
$bindIndex = 1;
foreach ($params as $p) {
    $stmt->bindValue($bindIndex++, $p, PDO::PARAM_STR);
}
$stmt->bindValue($bindIndex++, $perPage, PDO::PARAM_INT);
$stmt->bindValue($bindIndex++, $offset, PDO::PARAM_INT);
$stmt->execute();
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// LIST USER UNTUK PILIH ANGGOTA
$stmtUsers = $conn->query("
    SELECT user_id, username
    FROM users
    ORDER BY username ASC
");
$userRows = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// flash messages
$flash_success = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : null;
$flash_error   = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-kanban"></i> Manajemen Proyek
</h2>

<?php if($flash_success): ?>
    <div class="alert alert-success"><?= safe($flash_success) ?></div>
<?php endif; ?>
<?php if($flash_error): ?>
    <div class="alert alert-danger"><?= safe($flash_error) ?></div>
<?php endif; ?>

<!-- SEARCH + ADD -->
<div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
    <form class="d-flex gap-2" method="GET" style="max-width:480px; flex:1;">
        <input type="text" name="q" class="form-control"
               placeholder="Cari judul atau deskripsi proyek..."
               value="<?= safe($search) ?>">
        <button class="btn btn-outline-primary" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </form>

    <button class="btn btn-primary ms-auto" type="button" onclick="toggleAddProyek()">
        <i class="bi bi-plus-lg"></i> Tambah Proyek
    </button>
</div>

<!-- ADD FORM (TOGGLE) -->
<div id="addProyekBox" class="card shadow-sm border-0 p-3 mb-4" style="display:none;">
    <form method="POST" class="row g-3">
        <input type="hidden" name="action" value="add">

        <div class="col-md-6">
            <label class="form-label small">Judul Proyek</label>
            <input type="text" name="judul" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label small">Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" class="form-control"
                   value="<?= date('Y-m-d') ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label small">Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" class="form-control">
        </div>

        <div class="col-md-3">
            <label class="form-label small">Status</label>
            <select name="status" class="form-select">
                <option value="on going">On Going</option>
                <option value="selesai">Selesai</option>
            </select>
        </div>

        <!-- BUILDER ANGGOTA (TAMBAH) -->
        <div class="col-md-4">
            <label class="form-label small">Nama Anggota</label>
            <select id="anggota_user_add" class="form-select form-select-sm">
                <option value="">- Pilih User -</option>
                <?php foreach($userRows as $u): ?>
                    <option value="<?= (int)$u['user_id'] ?>"><?= safe($u['username']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label small">Peran</label>
            <select id="anggota_role_add" class="form-select form-select-sm">
                <option value="ketua">Ketua</option>
                <option value="anggota" selected>Anggota</option>
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-outline-primary btn-sm w-100"
                    onclick="tambahAnggota('add')">
                + Tambah Anggota
            </button>
        </div>

        <div class="col-12">
            <table class="table table-sm table-bordered align-middle mb-1">
                <thead class="table-light">
                    <tr>
                        <th style="width:55%;">Nama</th>
                        <th style="width:25%;">Peran</th>
                        <th style="width:20%;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody id="anggota_table_body_add">
                    <!-- baris anggota akan ditambah via JS -->
                </tbody>
            </table>
            <div class="form-text small">
                Tambah anggota satu per satu, bisa lebih dari satu ketua/anggota sesuai kebutuhan.
            </div>
        </div>

        <div class="col-12">
            <label class="form-label small">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="form-control"
                      placeholder="Deskripsi singkat proyek..."></textarea>
        </div>

        <div class="col-12 text-end">
            <button type="button" class="btn btn-secondary" onclick="toggleAddProyek()">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>

<!-- LIST PROYEK -->
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 proyek-grid">
    <?php if(empty($list)): ?>
        <div class="col">
            <div class="card p-4 text-center text-muted">
                Belum ada proyek.
            </div>
        </div>
    <?php else: ?>
        <?php foreach($list as $p): 
            $tglMulai   = !empty($p['tanggal_mulai']) ? date("d M Y", strtotime($p['tanggal_mulai'])) : "-";
            $tglSelesai = !empty($p['tanggal_selesai']) ? date("d M Y", strtotime($p['tanggal_selesai'])) : "-";
            $statusBadge = "bg-secondary";
            if ($p['status'] === 'on going') $statusBadge = "bg-info";
            if ($p['status'] === 'selesai')  $statusBadge = "bg-success";

            $fullDescRaw = isset($p['deskripsi']) ? $p['deskripsi'] : "";
            $fullDesc    = trim(strip_tags($fullDescRaw));
            $shortDesc   = excerpt($fullDesc, 180);
            $hasMoreDesc = (text_length_custom($fullDesc) > text_length_custom($shortDesc));

            $anggotaList   = isset($p['anggota_list']) ? $p['anggota_list'] : "";
            $anggotaDetail = isset($p['anggota_detail']) ? $p['anggota_detail'] : "";
        ?>
            <div class="col d-flex">
                <div class="card shadow-sm proyek-card p-3 w-100"
                     data-id="<?= (int)$p['proyek_id'] ?>"
                     data-judul="<?= safe($p['judul']) ?>"
                     data-deskripsi="<?= safe($fullDescRaw) ?>"
                     data-tglmulai="<?= safe($p['tanggal_mulai']) ?>"
                     data-tglselesai="<?= safe($p['tanggal_selesai']) ?>"
                     data-status="<?= safe($p['status']) ?>"
                     data-anggota="<?= safe($anggotaList) ?>"
                     data-anggota-detail="<?= safe($anggotaDetail) ?>">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge <?= $statusBadge ?> text-uppercase small">
                            <?= safe($p['status']) ?>
                        </span>
                        <span class="text-muted small ms-2">
                            <i class="bi bi-calendar-event"></i>
                            <?= safe($tglMulai) ?> &ndash; <?= safe($tglSelesai) ?>
                        </span>
                    </div>

                    <h6 class="fw-bold mb-2"><?= safe($p['judul']) ?></h6>

                    <div class="small text-muted mb-2 desc-wrapper">
                        <span class="proyek-short"><?= safe($shortDesc) ?></span>
                        <?php if ($hasMoreDesc): ?>
                            <span class="proyek-full d-none"><?= safe($fullDesc) ?></span>
                            <span class="toggle-proyek text-primary" style="cursor:pointer;">View more</span>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3 small">
                        <strong>Anggota:</strong><br>
                        <?php if ($anggotaList !== ''): ?>
                            <span><?= safe($anggotaList) ?></span>
                        <?php else: ?>
                            <span class="text-muted">Belum ada anggota terdaftar.</span>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2 mt-auto">
                        <button type="button"
                                class="btn btn-outline-primary btn-sm w-100"
                                onclick="openProyekDetail(this)">
                            <i class="bi bi-pencil-square"></i> Detail / Edit
                        </button>

                        <form method="POST"
                              onsubmit="return confirm('Hapus proyek ini beserta anggota yang terkait?');"
                              style="width:80px;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="proyek_id" value="<?= (int)$p['proyek_id'] ?>">
                            <button type="submit"
                                    class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-trash"></i>
                            </button>
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
                <a class="page-link"
                   href="?<?= http_build_query(array_merge($_GET, array('page' => max(1,$page-1)))) ?>">
                    Prev
                </a>
            </li>

            <?php
            $start = max(1, $page - 3);
            $end   = min($totalPages, $page + 3);
            for ($pageno = $start; $pageno <= $end; $pageno++): ?>
                <li class="page-item <?= $pageno==$page ? 'active' : '' ?>">
                    <a class="page-link"
                       href="?<?= http_build_query(array_merge($_GET, array('page'=>$pageno))) ?>">
                        <?= $pageno ?>
                    </a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= $page>=$totalPages ? 'disabled' : '' ?>">
                <a class="page-link"
                   href="?<?= http_build_query(array_merge($_GET, array('page' => min($totalPages,$page+1)))) ?>">
                    Next
                </a>
            </li>
        </ul>
    </nav>
<?php endif; ?>

<!-- DETAIL / EDIT MODAL -->
<div class="modal fade" id="proyekDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="proyekDetailForm">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="proyek_id" id="proyek_id_field">

        <div class="modal-header">
          <h5 class="modal-title">
              <i class="bi bi-kanban"></i> Detail Proyek
          </h5>
          <button type="button" class="btn-close"
                  data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="mb-2">
                        <label class="form-label small">Judul Proyek</label>
                        <input type="text" class="form-control"
                               id="judul_field" name="judul" required>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-md-4">
                            <label class="form-label small">Tanggal Mulai</label>
                            <input type="date" class="form-control"
                                   id="tglmulai_field" name="tanggal_mulai">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Tanggal Selesai</label>
                            <input type="date" class="form-control"
                                   id="tglselesai_field" name="tanggal_selesai">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Status</label>
                            <select class="form-select" id="status_field" name="status">
                                <option value="on going">On Going</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small">Deskripsi</label>
                        <textarea class="form-control"
                                  id="deskripsi_field"
                                  name="deskripsi" rows="5"></textarea>
                    </div>

                    <hr class="my-3"/>

                    <!-- BUILDER ANGGOTA (EDIT) -->
                    <div class="mb-2 small">
                        <strong>Anggota Proyek:</strong>

                        <div class="row mt-2">
                            <div class="col-md-5 mb-2">
                                <label class="form-label small">Nama Anggota</label>
                                <select id="anggota_user_edit" class="form-select form-select-sm">
                                    <option value="">- Pilih User -</option>
                                    <?php foreach($userRows as $u): ?>
                                        <option value="<?= (int)$u['user_id'] ?>"><?= safe($u['username']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label small">Peran</label>
                                <select id="anggota_role_edit" class="form-select form-select-sm">
                                    <option value="ketua">Ketua</option>
                                    <option value="anggota" selected>Anggota</option>
                                </select>
                            </div>
                            <div class="col-auto d-flex align-items-end mb-2">
                                <button type="button" class="btn btn-outline-primary btn-sm px-2 py-1"
                                        onclick="tambahAnggota('edit')"> + Tambah Anggota
                                </button>
                            </div>
                        </div>

                        <table class="table table-sm table-bordered align-middle mb-1">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:55%;">Nama</th>
                                    <th style="width:25%;">Peran</th>
                                    <th style="width:20%;" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="anggota_table_body_edit">
                                <!-- baris anggota akan diisi via JS -->
                            </tbody>
                        </table>

                        <div class="form-text">
                            Tambah / hapus anggota langsung di sini, lalu klik <em>Simpan Perubahan</em>.
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">
                Simpan Perubahan
            </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function toggleAddProyek(){
    var box = document.getElementById('addProyekBox');
    if (box.style.display === 'none' || box.style.display === '') {
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}

function tambahAnggota(mode){
    var prefix = (mode === 'edit') ? 'edit' : 'add';
    var selectUser = document.getElementById('anggota_user_' + prefix);
    var selectRole = document.getElementById('anggota_role_' + prefix);
    var tbody      = document.getElementById('anggota_table_body_' + prefix);

    if (!selectUser || !selectRole || !tbody) return;

    var uid = selectUser.value;
    if (!uid) {
        alert("Pilih nama anggota terlebih dahulu.");
        return;
    }

    var uname    = selectUser.options[selectUser.selectedIndex].text;
    var role     = selectRole.value;
    var roleText = selectRole.options[selectRole.selectedIndex].text;

    // Cegah duplikat (user + role sama persis)
    var rows = tbody.getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {
        if (rows[i].getAttribute('data-user') === uid &&
            rows[i].getAttribute('data-role') === role) {
            alert("Anggota dengan peran tersebut sudah ada.");
            return;
        }
    }

    var tr = document.createElement('tr');
    tr.setAttribute('data-user', uid);
    tr.setAttribute('data-role', role);
    tr.innerHTML =
        "<td>"+ uname +
        "<input type='hidden' name='anggota_user[]' value='"+ uid +"'></td>" +
        "<td>"+ roleText +
        "<input type='hidden' name='anggota_role[]' value='"+ role +"'></td>" +
        "<td class='text-end'>" +
            "<button type='button' class='btn btn-sm btn-outline-danger' "+
            "onclick='hapusAnggotaRow(this)'>" +
            "<i class='bi bi-x'></i></button>" +
        "</td>";

    tbody.appendChild(tr);
}

function hapusAnggotaRow(btn){
    var tr = btn.closest('tr');
    if (tr) tr.parentNode.removeChild(tr);
}

function openProyekDetail(btn){
    // cari dulu card inner (yang punya data-*), kalau tidak ada baru cari .proyek-card lama
    var card = btn.closest('.proyek-card-inner') || btn.closest('.proyek-card');
    if (!card) return;

    var id          = card.getAttribute('data-id');
    var judul       = card.getAttribute('data-judul') || '';
    var deskripsi   = card.getAttribute('data-deskripsi') || '';
    var tglMulai    = card.getAttribute('data-tglmulai') || '';
    var tglSelesai  = card.getAttribute('data-tglselesai') || '';
    var status      = card.getAttribute('data-status') || 'on going';
    var anggotaText = card.getAttribute('data-anggota') || '';
    var anggotaDet  = card.getAttribute('data-anggota-detail') || '';

    document.getElementById('proyek_id_field').value = id;
    document.getElementById('judul_field').value = judul;
    document.getElementById('deskripsi_field').value = deskripsi;
    document.getElementById('tglmulai_field').value = tglMulai;
    document.getElementById('tglselesai_field').value = tglSelesai;
    document.getElementById('status_field').value = status;

    // isi tabel anggota di modal edit
    var tbody = document.getElementById('anggota_table_body_edit');
    if (tbody) {
        tbody.innerHTML = "";
        if (anggotaDet !== '') {
            // format: userId:username:role|userId:username:role|...
            var rows = anggotaDet.split('|');
            for (var i = 0; i < rows.length; i++) {
                var parts = rows[i].split(':');
                if (parts.length < 3) continue;
                var uid  = parts[0];
                var uname= parts[1];
                var role = parts[2];
                var roleText = (role === 'ketua') ? 'Ketua' : 'Anggota';

                var tr = document.createElement('tr');
                tr.setAttribute('data-user', uid);
                tr.setAttribute('data-role', role);
                tr.innerHTML =
                    "<td>"+ uname +
                    "<input type='hidden' name='anggota_user[]' value='"+ uid +"'></td>" +
                    "<td>"+ roleText +
                    "<input type='hidden' name='anggota_role[]' value='"+ role +"'></td>" +
                    "<td class='text-end'>" +
                        "<button type='button' class='btn btn-sm btn-outline-danger' "+
                        "onclick='hapusAnggotaRow(this)'>" +
                        "<i class='bi bi-x'></i></button>" +
                    "</td>";
                tbody.appendChild(tr);
            }
        }
    }

    var modalEl = document.getElementById('proyekDetailModal');
    var modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// VIEW MORE / VIEW LESS DESKRIPSI PROYEK
document.addEventListener('DOMContentLoaded', function () {
    var toggles = document.querySelectorAll('.toggle-proyek');
    for (var i = 0; i < toggles.length; i++) {
        toggles[i].addEventListener('click', function () {
            var btn = this;
            var p   = btn.parentNode;
            var shortEl = p.querySelector('.proyek-short');
            var fullEl  = p.querySelector('.proyek-full');

            if (!shortEl || !fullEl) return;

            if (fullEl.classList.contains('d-none')) {
                fullEl.classList.remove('d-none');
                shortEl.classList.add('d-none');
                btn.innerHTML = 'View less';
            } else {
                fullEl.classList.add('d-none');
                shortEl.classList.remove('d-none');
                btn.innerHTML = 'View more';
            }
        });
    }
});
</script>

<style>
.card .btn { border-radius: 8px; }

/* grid tetap rapi */
.proyek-grid {
    margin-top: 0.5rem;
}

/* card: punya tinggi minimal, tapi boleh memanjang kalau isi banyak */
.proyek-card {
    border-radius: 14px;
    transition: .2s ease;
    display: flex;
    flex-direction: column;
    min-height: 320px;     /* baseline tinggi kartu */
    padding-top: 16px;
    padding-bottom: 16px;
}

/* area deskripsi yang bisa mengembang */
.proyek-card .desc-wrapper {
    flex-grow: 1;
    /* HAPUS overflow:hidden di sini */
}

/* text view-more tetap inline */
.proyek-card .proyek-short,
.proyek-card .proyek-full {
    display: inline;
}

.proyek-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0,0,0,0.08);
}
</style>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
