<?php
// app/roles/operator/dokumentasi.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../../core/auth.php";
requireRole("operator");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$active = "dokumentasi";
$title  = "Manajemen Dokumentasi";

function safe($v){
    return htmlspecialchars((string)(isset($v) ? $v : ""), ENT_QUOTES, 'UTF-8');
}
function excerpt_text($text, $len){
    $text = trim(strip_tags(isset($text) ? $text : ""));
    if (function_exists('mb_strlen')) {
        if (mb_strlen($text, 'UTF-8') <= $len) return $text;
        return mb_substr($text, 0, $len, 'UTF-8') . "...";
    } else {
        if (strlen($text) <= $len) return $text;
        return substr($text, 0, $len) . "...";
    }
}

$currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// ====================== HANDLE POSTS (ADD / UPDATE / DELETE) ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // ---------- TAMBAH DOKUMENTASI ----------
    if ($action === 'add') {
        $caption          = isset($_POST['caption']) ? trim($_POST['caption']) : '';
        $jenis_kegiatan   = isset($_POST['jenis_kegiatan']) ? trim($_POST['jenis_kegiatan']) : '';
        $tanggal_kegiatan = isset($_POST['tanggal_kegiatan']) ? trim($_POST['tanggal_kegiatan']) : '';

        if ($caption === '' || $jenis_kegiatan === '') {
            $_SESSION['flash_error'] = "Caption dan jenis kegiatan wajib diisi.";
            header("Location: dokumentasi.php");
            exit;
        }

        if ($tanggal_kegiatan === '') {
            $tanggal_kegiatan = date("Y-m-d");
        }

        $allowedJenis = array('workshop','riset','seminar','kunjungan','lomba','pengabdian','aktivitas_lain');
        if (!in_array($jenis_kegiatan, $allowedJenis)) {
            $_SESSION['flash_error'] = "Jenis kegiatan tidak valid.";
            header("Location: dokumentasi.php");
            exit;
        }

        // Upload file (foto dokumentasi)
        $fileName = null;
        if (isset($_FILES['file_dok']) && $_FILES['file_dok']['error'] === UPLOAD_ERR_OK) {
            $f = $_FILES['file_dok'];
            $allowedMime = array('image/jpeg','image/jpg','image/png','image/webp');
            if (in_array($f['type'], $allowedMime)) {
                $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
                if (in_array($ext, array('jpg','jpeg','png','webp'))) {
                    $newName = 'dok_' . time() . '_' . rand(100,999) . '.' . $ext;
                    $destDir = __DIR__ . "/../../../public/uploads/dokumentasi/";
                    if (!is_dir($destDir)) {
                        @mkdir($destDir, 0777, true);
                    }
                    $dest = $destDir . $newName;
                    if (move_uploaded_file($f['tmp_name'], $dest)) {
                        $fileName = $newName;
                    }
                }
            }
        }

        $stmt = $conn->prepare("
            INSERT INTO act_documentation (type_file, caption, tanggal_kegiatan, jenis_kegiatan, uploaded_by)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute(array(
            $fileName,
            $caption,
            $tanggal_kegiatan,
            $jenis_kegiatan,
            $currentUserId
        ));

        $_SESSION['flash_success'] = "Dokumentasi berhasil ditambahkan.";
        header("Location: dokumentasi.php");
        exit;
    }

    // ---------- UPDATE DOKUMENTASI ----------
    if ($action === 'update') {
        $id              = isset($_POST['documentation_id']) ? (int)$_POST['documentation_id'] : 0;
        $caption         = isset($_POST['caption']) ? trim($_POST['caption']) : '';
        $jenis_kegiatan  = isset($_POST['jenis_kegiatan']) ? trim($_POST['jenis_kegiatan']) : '';
        $tanggal_kegiatan = isset($_POST['tanggal_kegiatan']) ? trim($_POST['tanggal_kegiatan']) : '';

        if (!$id) {
            $_SESSION['flash_error'] = "ID dokumentasi tidak ditemukan.";
            header("Location: dokumentasi.php");
            exit;
        }

        if ($caption === '' || $jenis_kegiatan === '') {
            $_SESSION['flash_error'] = "Caption dan jenis kegiatan wajib diisi.";
            header("Location: dokumentasi.php");
            exit;
        }

        if ($tanggal_kegiatan === '') {
            $tanggal_kegiatan = date("Y-m-d");
        }

        $allowedJenis = array('workshop','riset','seminar','kunjungan','lomba','pengabdian','aktivitas_lain');
        if (!in_array($jenis_kegiatan, $allowedJenis)) {
            $_SESSION['flash_error'] = "Jenis kegiatan tidak valid.";
            header("Location: dokumentasi.php");
            exit;
        }

        $fileName = null;

        // cek upload file baru
        if (isset($_FILES['file_baru']) && $_FILES['file_baru']['error'] === UPLOAD_ERR_OK) {
            $f = $_FILES['file_baru'];
            $allowedMime = array('image/jpeg','image/jpg','image/png','image/webp');
            if (in_array($f['type'], $allowedMime)) {
                $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
                if (in_array($ext, array('jpg','jpeg','png','webp'))) {

                    $newName = 'dok_' . time() . '_' . rand(100,999) . '.' . $ext;
                    $destDir = __DIR__ . "/../../../public/uploads/dokumentasi/";
                    if (!is_dir($destDir)) {
                        @mkdir($destDir, 0777, true);
                    }
                    $uploadPath = $destDir . $newName;

                    move_uploaded_file($f['tmp_name'], $uploadPath);

                    // hapus file lama
                    $stmtFile = $conn->prepare("SELECT type_file FROM act_documentation WHERE documentation_id = ?");
                    $stmtFile->execute(array($id));
                    $old = $stmtFile->fetchColumn();

                    if ($old && is_file($destDir . $old)) {
                        @unlink($destDir . $old);
                    }

                    $fileName = $newName;
                }
            }
        }

        if ($fileName) {
            $stmt = $conn->prepare("
                UPDATE act_documentation
                SET caption = ?, tanggal_kegiatan = ?, jenis_kegiatan = ?, type_file = ?
                WHERE documentation_id = ?
            ");
            $stmt->execute(array($caption, $tanggal_kegiatan, $jenis_kegiatan, $fileName, $id));
        } else {
            $stmt = $conn->prepare("
                UPDATE act_documentation
                SET caption = ?, tanggal_kegiatan = ?, jenis_kegiatan = ?
                WHERE documentation_id = ?
            ");
            $stmt->execute(array($caption, $tanggal_kegiatan, $jenis_kegiatan, $id));
        }

        $_SESSION['flash_success'] = "Dokumentasi berhasil diperbarui.";
        header("Location: dokumentasi.php");
        exit;
    }

    // ---------- HAPUS DOKUMENTASI ----------
    if ($action === 'delete') {
        $id = isset($_POST['documentation_id']) ? (int)$_POST['documentation_id'] : 0;

        if ($id) {
            $stmt = $conn->prepare("SELECT type_file FROM act_documentation WHERE documentation_id = ?");
            $stmt->execute(array($id));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmtDel = $conn->prepare("DELETE FROM act_documentation WHERE documentation_id = ?");
            $stmtDel->execute(array($id));

            if ($row && !empty($row['type_file'])) {
                $destDir = __DIR__ . "/../../../public/uploads/dokumentasi/";
                $path = $destDir . $row['type_file'];
                if (is_file($path)) {
                    @unlink($path);
                }
            }

            $_SESSION['flash_success'] = "Dokumentasi telah dihapus.";
        } else {
            $_SESSION['flash_error'] = "ID dokumentasi tidak ditemukan.";
        }

        header("Location: dokumentasi.php");
        exit;
    }
}

// ====================== LIST + SEARCH + PAGINATION ======================
$page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$perPage = 8; // 4 kolom x 2 baris
$offset  = ($page - 1) * $perPage;

$search = isset($_GET['q']) ? trim($_GET['q']) : "";
$where  = "WHERE 1=1";
$params = array();

if ($search !== '') {
    $where .= " AND (LOWER(caption) LIKE ? OR LOWER(jenis_kegiatan) LIKE ?)";
    $params[] = '%' . strtolower($search) . '%';
    $params[] = '%' . strtolower($search) . '%';
}

// total
$stmtCount = $conn->prepare("SELECT COUNT(*) FROM act_documentation $where");
$stmtCount->execute($params);
$total = (int)$stmtCount->fetchColumn();
$totalPages = (int)ceil($total / $perPage);

// data
$sql = "SELECT documentation_id, type_file, caption, tanggal_kegiatan, jenis_kegiatan
        FROM act_documentation
        $where
        ORDER BY tanggal_kegiatan DESC, documentation_id DESC
        LIMIT ? OFFSET ?";

$paramsPage = $params;
$paramsPage[] = $perPage;
$paramsPage[] = $offset;

$stmt = $conn->prepare($sql);
$bindIndex = 1;
foreach ($params as $p) {
    $stmt->bindValue($bindIndex++, $p);
}
$stmt->bindValue($bindIndex++, $perPage, PDO::PARAM_INT);
$stmt->bindValue($bindIndex++, $offset, PDO::PARAM_INT);
$stmt->execute();
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// flash
$flash_success = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : null;
$flash_error   = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-camera-reels"></i> Manajemen Dokumentasi
</h2>

<?php if ($flash_success): ?>
    <div class="alert alert-success"><?= safe($flash_success) ?></div>
<?php endif; ?>
<?php if ($flash_error): ?>
    <div class="alert alert-danger"><?= safe($flash_error) ?></div>
<?php endif; ?>

<!-- SEARCH + ADD -->
<div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
    <form class="d-flex gap-2" method="GET" style="max-width:480px; flex:1;">
        <input type="text" name="q" class="form-control" placeholder="Cari caption atau jenis kegiatan..."
               value="<?= safe($search) ?>">
        <button class="btn btn-outline-primary" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </form>

    <button class="btn btn-primary ms-auto" type="button" onclick="toggleAddDok()">
        <i class="bi bi-plus-lg"></i> Tambah Dokumentasi
    </button>
</div>

<!-- ADD FORM (TOGGLE) -->
<div id="addDokBox" class="card shadow-sm border-0 p-3 mb-4" style="display:none;">
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="action" value="add">

        <div class="col-md-3">
            <label class="form-label small">Tanggal Kegiatan</label>
            <input type="date" name="tanggal_kegiatan" class="form-control"
                   value="<?= date('Y-m-d') ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label small">Jenis Kegiatan</label>
            <select name="jenis_kegiatan" class="form-select" required>
                <option value="">- Pilih -</option>
                <option value="workshop">Workshop</option>
                <option value="riset">Riset</option>
                <option value="seminar">Seminar</option>
                <option value="kunjungan">Kunjungan</option>
                <option value="lomba">Lomba</option>
                <option value="pengabdian">Pengabdian</option>
                <option value="aktivitas_lain">Aktivitas Lain</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label small">File Dokumentasi (foto)</label>
            <input type="file" name="file_dok" class="form-control"
                   accept=".jpg,.jpeg,.png,.webp">
            <div class="form-text small">Disimpan di: <code>/public/uploads/dokumentasi/</code></div>
        </div>

        <div class="col-12">
            <label class="form-label small">Caption</label>
            <textarea name="caption" rows="3" class="form-control" placeholder="Keterangan singkat dokumentasi..." required></textarea>
        </div>

        <div class="col-12 text-end">
            <button type="button" class="btn btn-secondary" onclick="toggleAddDok()">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>

<!-- LIST DOKUMENTASI -->
<div class="row g-4">
    <?php if (empty($list)): ?>
        <div class="col-12">
            <div class="card p-4 text-center text-muted">
                Belum ada dokumentasi.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($list as $d):
            $imgSrc = "../../../public/assets/img/facility-placeholder.jpg";
            if (!empty($d['type_file'])) {
                $imgSrc = "../../../public/uploads/dokumentasi/" . safe($d['type_file']);
            }
            $tglLabel = '-';
            if (!empty($d['tanggal_kegiatan'])) {
                $tglLabel = date("d M Y", strtotime($d['tanggal_kegiatan']));
            }
        ?>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="dok-item position-relative h-100"
                 style="cursor:pointer;"
                 onclick="openDokPreview(this)"
                 data-id="<?= (int)$d['documentation_id'] ?>"
                 data-caption="<?= safe($d['caption']) ?>"
                 data-jenis="<?= safe($d['jenis_kegiatan']) ?>"
                 data-tanggal="<?= safe($d['tanggal_kegiatan']) ?>"
                 data-file="<?= safe($d['type_file']) ?>">

                <img src="<?= $imgSrc ?>"
                     class="dok-img"
                     onerror="this.onerror=null;this.src='../../../public/assets/img/blog/blog-1.jpg';">

                <!-- badge tanggal (kiri atas) -->
                <span class="dok-date-badge">
                    <?= safe($tglLabel) ?>
                </span>

                <!-- badge jenis (kanan atas) -->
                <span class="dok-type-badge badge bg-warning text-dark">
                    <?= strtoupper(safe($d['jenis_kegiatan'])) ?>
                </span>

                <!-- overlay dengan caption -->
                <div class="dok-overlay">
                    <div class="overlay-inner text-center text-white px-3">
                        <p class="small mb-3">
                            <?= safe(excerpt_text($d['caption'], 80)) ?>
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <!-- Hapus langsung -->
                            <form method="POST"
                                  onclick="event.stopPropagation();"
                                  onsubmit="event.stopPropagation(); return confirm('Hapus dokumentasi ini?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="documentation_id" value="<?= (int)$d['documentation_id'] ?>">
                                <button type="submit" class="btn btn-outline-light btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- PAGINATION -->
<?php if ($totalPages > 1): ?>
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
            for ($p = $start; $p <= $end; $p++): ?>
                <li class="page-item <?= $p==$page ? 'active' : '' ?>">
                    <a class="page-link"
                       href="?<?= http_build_query(array_merge($_GET, array('page'=>$p))) ?>">
                       <?= $p ?>
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

<!-- =============== MODAL PREVIEW DOKUMENTASI =============== -->
<div class="modal fade" id="dokPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
            <i class="bi bi-image"></i> Preview Dokumentasi
        </h5>
        <button type="button" class="btn-close"
                data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
            <div class="col-lg-7 text-center">
                <img id="dokPreviewPhoto"
                     src="../../../public/assets/img/blog/blog-1.jpg"
                     class="preview-dok-img">
            </div>
            <div class="col-lg-5">
                <div class="mb-1 small text-muted" id="dokPreviewIdText"></div>
                <span class="badge bg-primary mb-2" id="dokPreviewTanggalBadge">-</span>
                <span class="badge bg-warning text-dark mb-2" id="dokPreviewJenisBadge">-</span>

                <h6 class="fw-semibold mt-3">Caption</h6>
                <p id="dokPreviewCaption" class="small" style="white-space:pre-wrap;"></p>
            </div>
        </div>
      </div>

      <div class="modal-footer justify-content-between">
        <form method="POST" id="dokPreviewDeleteForm"
              onsubmit="return confirm('Hapus dokumentasi ini?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="documentation_id" id="dokPreviewDeleteId">
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </form>

        <div>
            <button type="button" class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">Tutup</button>
            <button type="button" class="btn btn-primary"
                    id="btnDokPreviewEdit"
                    onclick="openDokEditFromPreview()">
                <i class="bi bi-pencil-square"></i> Edit
            </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- =============== MODAL EDIT DOKUMENTASI =============== -->
<div class="modal fade" id="dokEditModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dok">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data" id="dokEditForm">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="documentation_id" id="dokEditId">

        <div class="modal-header">
          <h5 class="modal-title">
              <i class="bi bi-camera-reels"></i> Edit Dokumentasi
          </h5>
          <button type="button" class="btn-close"
                  data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
            <div class="row g-3">
                <div class="col-md-4 text-center">
                    <img id="dokEditPhoto"
                         src="../../../public/assets/img/blog/blog-1.jpg"
                         style="width:100%;max-width:260px;max-height:220px;object-fit:cover;border-radius:12px;background:#000000;">

                    <label class="mt-3 w-100 btn btn-outline-primary btn-sm">
                        <input type="file"
                               id="dokFileInput"
                               name="file_baru"
                               accept=".jpg,.jpeg,.png,.webp"
                               class="d-none"
                               onchange="previewFotoDok(event)">
                        <i class="bi bi-image"></i> Pilih File Baru
                    </label>

                    <div class="small text-muted">
                        Format: JPG/PNG/WebP — maks 2MB
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="mb-2">
                        <label class="form-label small">Tanggal Kegiatan</label>
                        <input type="date" class="form-control"
                               id="dokEditTanggal" name="tanggal_kegiatan">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small">Jenis Kegiatan</label>
                        <select class="form-select" id="dokEditJenis" name="jenis_kegiatan" required>
                            <option value="workshop">Workshop</option>
                            <option value="riset">Riset</option>
                            <option value="seminar">Seminar</option>
                            <option value="kunjungan">Kunjungan</option>
                            <option value="lomba">Lomba</option>
                            <option value="pengabdian">Pengabdian</option>
                            <option value="aktivitas_lain">Aktivitas Lain</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small">Caption</label>
                        <textarea class="form-control"
                                  id="dokEditCaption"
                                  name="caption" rows="5"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">
                Simpan Perubahan
            </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function toggleAddDok(){
    var box = document.getElementById('addDokBox');
    if (!box) return;
    if (box.style.display === 'none' || box.style.display === '') {
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}

// ===== PREVIEW =====
function openDokPreview(card){
    if (!card) return;

    var id       = card.getAttribute('data-id');
    var caption  = card.getAttribute('data-caption') || '';
    var jenis    = card.getAttribute('data-jenis') || '';
    var tanggal  = card.getAttribute('data-tanggal') || '';
    var fileName = card.getAttribute('data-file') || '';

    var imgPrev = document.getElementById('dokPreviewPhoto');
    if (fileName) {
        imgPrev.src = "../../../public/uploads/dokumentasi/" + fileName;
    } else {
        imgPrev.src = "../../../public/assets/img/blog/blog-1.jpg";
    }

    document.getElementById('dokPreviewCaption').innerHTML = caption !== '' ? caption : '-';
    document.getElementById('dokPreviewJenisBadge').innerHTML = jenis ? jenis.toUpperCase() : '-';
    document.getElementById('dokPreviewTanggalBadge').innerHTML = tanggal || '-';
    document.getElementById('dokPreviewIdText').innerHTML = "ID: " + id;

    var btnEdit = document.getElementById('btnDokPreviewEdit');
    btnEdit.setAttribute('data-id', id);
    btnEdit.setAttribute('data-caption', caption);
    btnEdit.setAttribute('data-jenis', jenis);
    btnEdit.setAttribute('data-tanggal', tanggal);
    btnEdit.setAttribute('data-file', fileName);

    document.getElementById('dokPreviewDeleteId').value = id;

    var modalEl = document.getElementById('dokPreviewModal');
    var modal   = new bootstrap.Modal(modalEl);
    modal.show();
}

// ===== BUKA MODAL EDIT DARI PREVIEW =====
function openDokEditFromPreview(){
    var btn = document.getElementById('btnDokPreviewEdit');
    var id       = btn.getAttribute('data-id');
    var caption  = btn.getAttribute('data-caption') || '';
    var jenis    = btn.getAttribute('data-jenis') || 'workshop';
    var tanggal  = btn.getAttribute('data-tanggal') || '';
    var fileName = btn.getAttribute('data-file') || '';

    document.getElementById('dokEditId').value = id;
    document.getElementById('dokEditCaption').value = caption;
    document.getElementById('dokEditJenis').value = jenis;
    document.getElementById('dokEditTanggal').value = tanggal;

    var img = document.getElementById('dokEditPhoto');
    if (fileName) {
        img.src = "../../../public/uploads/dokumentasi/" + fileName;
    } else {
        img.src = "../../../public/assets/img/blog/blog-1.jpg";
    }

    var prevEl = document.getElementById('dokPreviewModal');
    var prevModal = bootstrap.Modal.getInstance(prevEl);
    if (prevModal) prevModal.hide();

    var editEl = document.getElementById('dokEditModal');
    var editModal = new bootstrap.Modal(editEl);
    editModal.show();
}

function previewFotoDok(e){
    var file = e.target.files[0];
    if (!file) return;

    var allowed = ['image/jpeg','image/png','image/webp','image/jpg'];
    if (allowed.indexOf(file.type) === -1) {
        alert("Format file harus JPG/PNG/WebP");
        e.target.value = "";
        return;
    }

    if (file.size > 2 * 1024 * 1024) { // 2MB
        alert("Ukuran file maksimal 2 MB");
        e.target.value = "";
        return;
    }

    var reader = new FileReader();
    reader.onload = function(ev){
        var img = document.getElementById('dokEditPhoto');
        img.src = ev.target.result;
    };
    reader.readAsDataURL(file);
}
</script>

<style>
.card .btn { border-radius: 8px; }

/* GRID DOKUMENTASI */
.dok-item {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    background: #000000;
    transition: transform .2s ease, box-shadow .2s ease;
}
.dok-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0,0,0,0.12);
}

.dok-img {
    display: block;
    width: 100%;
    height: 180px;
    object-fit: cover;
}

/* badge tanggal kiri atas */
.dok-date-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background: rgba(255,255,255,0.95);
    color: #333;
    padding: 3px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
}

/* badge jenis kanan atas */
.dok-type-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 11px;
    text-transform: uppercase;
}

/* overlay caption */
.dok-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.85), rgba(0,0,0,0.10));
    opacity: 0;
    display: flex;
    align-items: flex-end;
    transition: opacity .2s ease;
}
.dok-item:hover .dok-overlay {
    opacity: 1;
}

.dok-overlay .overlay-inner {
    width: 100%;
    padding-bottom: 14px;
}

.dok-overlay p {
    max-height: 3.6em;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0.75rem;
    word-break: break-word;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}

/* preview modal image */
.preview-dok-img {
    width: 100%;
    max-width: 320px;
    max-height: 40vh;
    object-fit: contain;
    border-radius: 12px;
    background: #000;
    display: block;
    margin: 0 auto;
}

/* ukuran modal edit custom */
.modal-dok {
    max-width: 800px;
}
@media (max-width: 768px) {
    .modal-dok {
        max-width: 95%;
    }
}
</style>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
