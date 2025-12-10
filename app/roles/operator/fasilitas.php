<?php
// app/roles/operator/fasilitas.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../../core/auth.php";
requireRole("operator");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$active = "fasilitas";
$title  = "Manajemen Fasilitas";

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

    // ---------- TAMBAH FASILITAS ----------
    if ($action === 'add') {
        $nama      = isset($_POST['nama']) ? trim($_POST['nama']) : '';
        $kategori  = isset($_POST['kategori']) ? trim($_POST['kategori']) : '';
        $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';

        if ($nama === '' || $kategori === '') {
            $_SESSION['flash_error'] = "Nama fasilitas dan kategori wajib diisi.";
            header("Location: fasilitas.php");
            exit;
        }

        $fotoName = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $foto = $_FILES['foto'];
            $allowed = array('image/jpeg','image/jpg','image/png','image/webp');

            if (in_array($foto['type'], $allowed)) {
                $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
                if (in_array($ext, array('jpg','jpeg','png','webp'))) {
                    $newName = 'fasilitas_' . time() . '_' . rand(100,999) . '.' . $ext;
                    $destDir = __DIR__ . "/../../../public/uploads/fasilitas/";
                    if (!is_dir($destDir)) {
                        @mkdir($destDir, 0777, true);
                    }
                    $dest = $destDir . $newName;
                    if (move_uploaded_file($foto['tmp_name'], $dest)) {
                        $fotoName = $newName;
                    }
                }
            }
        }

        $stmt = $conn->prepare("
            INSERT INTO fasilitas (user_id, nama, deskripsi, foto, kategori)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute(array(
            $currentUserId,
            $nama,
            $deskripsi,
            $fotoName,
            $kategori
        ));

        $_SESSION['flash_success'] = "Fasilitas berhasil ditambahkan.";
        header("Location: fasilitas.php");
        exit;
    }

    // ---------- UPDATE FASILITAS ----------
    if ($action === 'update') {
        $id        = isset($_POST['fasilitas_id']) ? $_POST['fasilitas_id'] : null;
        $nama      = isset($_POST['nama']) ? trim($_POST['nama']) : '';
        $kategori  = isset($_POST['kategori']) ? trim($_POST['kategori']) : '';
        $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';

        if (!$id) {
            $_SESSION['flash_error'] = "ID fasilitas tidak ditemukan.";
            header("Location: fasilitas.php");
            exit;
        }

        if ($nama === '' || $kategori === '') {
            $_SESSION['flash_error'] = "Nama fasilitas dan kategori wajib diisi.";
            header("Location: fasilitas.php");
            exit;
        }

        $fotoName = null;

        if (isset($_FILES['foto_baru']) && $_FILES['foto_baru']['error'] === UPLOAD_ERR_OK) {
            $foto = $_FILES['foto_baru'];
            $allowed = array('image/jpeg','image/jpg','image/png','image/webp');

            if (in_array($foto['type'], $allowed)) {
                $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
                if (in_array($ext, array('jpg','jpeg','png','webp'))) {

                    $newName = 'fasilitas_' . time() . '_' . rand(100,999) . '.' . $ext;
                    $destDir = __DIR__ . "/../../../public/uploads/fasilitas/";
                    if (!is_dir($destDir)) {
                        @mkdir($destDir, 0777, true);
                    }
                    $uploadPath = $destDir . $newName;

                    move_uploaded_file($foto['tmp_name'], $uploadPath);

                    // hapus foto lama
                    $stmtFoto = $conn->prepare("SELECT foto FROM fasilitas WHERE fasilitas_id = ?");
                    $stmtFoto->execute(array($id));
                    $old = $stmtFoto->fetchColumn();

                    if ($old && is_file($destDir . $old)) {
                        @unlink($destDir . $old);
                    }

                    $fotoName = $newName;
                }
            }
        }

        if ($fotoName) {
            $stmt = $conn->prepare("
                UPDATE fasilitas
                SET nama = ?, kategori = ?, deskripsi = ?, foto = ?
                WHERE fasilitas_id = ?
            ");
            $stmt->execute(array($nama, $kategori, $deskripsi, $fotoName, $id));
        } else {
            $stmt = $conn->prepare("
                UPDATE fasilitas
                SET nama = ?, kategori = ?, deskripsi = ?
                WHERE fasilitas_id = ?
            ");
            $stmt->execute(array($nama, $kategori, $deskripsi, $id));
        }

        $_SESSION['flash_success'] = "Fasilitas berhasil diperbarui.";
        header("Location: fasilitas.php");
        exit;
    }

    // ---------- HAPUS FASILITAS ----------
    if ($action === 'delete') {
        $id = isset($_POST['fasilitas_id']) ? $_POST['fasilitas_id'] : null;

        if ($id) {
            $stmt = $conn->prepare("SELECT foto FROM fasilitas WHERE fasilitas_id = ?");
            $stmt->execute(array($id));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmtDel = $conn->prepare("DELETE FROM fasilitas WHERE fasilitas_id = ?");
            $stmtDel->execute(array($id));

            if ($row && !empty($row['foto'])) {
                $destDir = __DIR__ . "/../../../public/uploads/fasilitas/";
                $path = $destDir . $row['foto'];
                if (is_file($path)) {
                    @unlink($path);
                }
            }

            $_SESSION['flash_success'] = "Fasilitas telah dihapus.";
        } else {
            $_SESSION['flash_error'] = "ID fasilitas tidak ditemukan.";
        }

        header("Location: fasilitas.php");
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
    $where .= " AND (LOWER(nama) LIKE ? OR LOWER(kategori) LIKE ?)";
    $params[] = '%' . strtolower($search) . '%';
    $params[] = '%' . strtolower($search) . '%';
}

$stmtCount = $conn->prepare("SELECT COUNT(*) FROM fasilitas $where");
$stmtCount->execute($params);
$total = (int)$stmtCount->fetchColumn();
$totalPages = (int)ceil($total / $perPage);

$sql = "SELECT fasilitas_id, nama, deskripsi, foto, kategori
        FROM fasilitas
        $where
        ORDER BY fasilitas_id DESC
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

$flash_success = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : null;
$flash_error   = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-building-gear"></i> Manajemen Fasilitas
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
        <input type="text" name="q" class="form-control" placeholder="Cari nama atau kategori fasilitas..."
               value="<?= safe($search) ?>">
        <button class="btn btn-outline-primary" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </form>

    <button class="btn btn-primary ms-auto" type="button" onclick="toggleAddFasilitas()">
        <i class="bi bi-plus-lg"></i> Tambah Fasilitas
    </button>
</div>

<!-- ADD FORM (TOGGLE) -->
<div id="addFasilitasBox" class="card shadow-sm border-0 p-3 mb-4" style="display:none;">
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="action" value="add">

        <div class="col-md-6">
            <label class="form-label small">Nama Fasilitas</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label small">Kategori</label>
            <select name="kategori" class="form-select" required>
                <option value="">- Pilih -</option>
                <option value="fasilitas">Fasilitas</option>
                <option value="peralatan">Peralatan</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label small">Foto (opsional)</label>
            <input type="file" name="foto" class="form-control"
                   accept=".jpg,.jpeg,.png,.webp">
            <div class="form-text small">Disimpan di: <code>/public/uploads/fasilitas/</code></div>
        </div>

        <div class="col-12">
            <label class="form-label small">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="form-control" placeholder="Deskripsi singkat fasilitas..."></textarea>
        </div>

        <div class="col-12 text-end">
            <button type="button" class="btn btn-secondary" onclick="toggleAddFasilitas()">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>

<!-- LIST FASILITAS (8 item / page, 4 kolom x 2 baris) -->
<div class="row g-4">
    <?php if (empty($list)): ?>
        <div class="col-12">
            <div class="card p-4 text-center text-muted">
                Belum ada fasilitas.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($list as $f): 
            $imgSrc = "../../../public/assets/img/facility-placeholder.jpg";
            if (!empty($f['foto'])) {
                $imgSrc = "../../../public/uploads/fasilitas/" . safe($f['foto']);
            }
        ?>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="fasilitas-item position-relative h-100"
                 style="cursor:pointer;"
                 onclick="openFasilitasPreview(this)"
                 data-id="<?= (int)$f['fasilitas_id'] ?>"
                 data-nama="<?= safe($f['nama']) ?>"
                 data-kategori="<?= safe($f['kategori']) ?>"
                 data-deskripsi="<?= safe($f['deskripsi']) ?>"
                 data-foto="<?= safe($f['foto']) ?>">

                <img src="<?= $imgSrc ?>"
                     class="fasilitas-img"
                     onerror="this.onerror=null;this.src='../../../public/assets/img/blog/blog-1.jpg';">

                <span class="fasilitas-badge badge bg-warning text-dark">
                    <?= strtoupper(safe($f['kategori'])) ?>
                </span>

                <div class="fasilitas-overlay">
                    <div class="overlay-inner text-center text-white px-3">
                        <h6 class="fw-bold mb-1"><?= safe($f['nama']) ?></h6>
                        <p class="small mb-3">
                            <?= safe(excerpt_text($f['deskripsi'], 70)) ?>
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <!-- HAPUS dari kartu (tidak buka modal) -->
                            <form method="POST"
                                  onclick="event.stopPropagation();"
                                  onsubmit="event.stopPropagation(); return confirm('Hapus fasilitas ini?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="fasilitas_id" value="<?= (int)$f['fasilitas_id'] ?>">
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

<!-- ====================== MODAL PREVIEW ====================== -->
<div class="modal fade" id="fasilitasPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
            <i class="bi bi-image"></i> Preview Fasilitas
        </h5>
        <button type="button" class="btn-close"
                data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
            <div class="col-lg-7 text-center">
                <img id="previewPhoto"
                     src="../../../public/assets/img/blog/blog-1.jpg"
                     class="preview-fasilitas-img">
            </div>
            <div class="col-lg-5">
                <h5 id="previewNama" class="fw-bold mb-1">-</h5>
                <span class="badge bg-warning text-dark mb-2" id="previewKategori">-</span>
                <div class="small text-muted mb-3" id="previewIdText"></div>

                <h6 class="fw-semibold">Deskripsi</h6>
                <p id="previewDeskripsi" class="small" style="white-space:pre-wrap;"></p>
            </div>
        </div>
      </div>

      <div class="modal-footer justify-content-between">
        <form method="POST" id="previewDeleteForm"
              onsubmit="return confirm('Hapus fasilitas ini?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="fasilitas_id" id="preview_delete_id">
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </form>

        <div>
            <button type="button" class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">Tutup</button>
            <button type="button" class="btn btn-primary"
                    id="btnPreviewEdit"
                    onclick="openEditFromPreview()">
                <i class="bi bi-pencil-square"></i> Edit
            </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ====================== MODAL EDIT ====================== -->
<div class="modal fade" id="fasilitasEditModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-fasilitas">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data" id="fasilitasEditForm">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="fasilitas_id" id="edit_fasilitas_id">

        <div class="modal-header">
          <h5 class="modal-title">
              <i class="bi bi-building-gear"></i> Edit Fasilitas
          </h5>
          <button type="button" class="btn-close"
                  data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
            <div class="row g-3">
                <div class="col-md-4 text-center">
                    <img id="editPhoto"
                         src="../../../public/assets/img/blog/blog-1.jpg"
                         style="width:100%;max-width:260px;max-height:220px;object-fit:cover;border-radius:12px;background:#000000;">

                    <label class="mt-3 w-100 btn btn-outline-primary btn-sm">
                        <input type="file"
                               id="foto_input"
                               name="foto_baru"
                               accept=".jpg,.jpeg,.png,.webp"
                               class="d-none"
                               onchange="previewFotoFasilitas(event)">
                        <i class="bi bi-image"></i> Pilih Foto Baru
                    </label>

                    <div class="small text-muted">
                        Format: JPG/PNG/WebP — maks 2MB
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="mb-2">
                        <label class="form-label small">Nama Fasilitas</label>
                        <input type="text" class="form-control"
                               id="edit_nama" name="nama" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small">Kategori</label>
                        <select class="form-select" id="edit_kategori" name="kategori" required>
                            <option value="fasilitas">Fasilitas</option>
                            <option value="peralatan">Peralatan</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small">Deskripsi</label>
                        <textarea class="form-control"
                                  id="edit_deskripsi"
                                  name="deskripsi" rows="5"></textarea>
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
function toggleAddFasilitas(){
    var box = document.getElementById('addFasilitasBox');
    if (!box) return;
    if (box.style.display === 'none' || box.style.display === '') {
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}

// ========== PREVIEW MODAL ==========
function openFasilitasPreview(card){
    if (!card) return;

    var id        = card.getAttribute('data-id');
    var nama      = card.getAttribute('data-nama') || '';
    var kategori  = card.getAttribute('data-kategori') || '';
    var deskripsi = card.getAttribute('data-deskripsi') || '';
    var foto      = card.getAttribute('data-foto') || '';

    var imgPrev = document.getElementById('previewPhoto');
    if (foto) {
        imgPrev.src = "../../../public/uploads/fasilitas/" + foto;
    } else {
        imgPrev.src = "../../../public/assets/img/blog/blog-1.jpg";
    }

    document.getElementById('previewNama').innerHTML = nama;
    document.getElementById('previewKategori').innerHTML = kategori ? kategori.toUpperCase() : '-';
    document.getElementById('previewDeskripsi').innerHTML = deskripsi !== '' ? deskripsi : '-';
    document.getElementById('previewIdText').innerHTML = "ID: " + id;

    // set data untuk tombol edit
    var btnEdit = document.getElementById('btnPreviewEdit');
    btnEdit.setAttribute('data-id', id);
    btnEdit.setAttribute('data-nama', nama);
    btnEdit.setAttribute('data-kategori', kategori);
    btnEdit.setAttribute('data-deskripsi', deskripsi);
    btnEdit.setAttribute('data-foto', foto);

    // set id untuk delete
    document.getElementById('preview_delete_id').value = id;

    var modalEl = document.getElementById('fasilitasPreviewModal');
    var modal   = new bootstrap.Modal(modalEl);
    modal.show();
}

// ========== BUKA MODAL EDIT DARI PREVIEW ==========
function openEditFromPreview(){
    var btnEdit = document.getElementById('btnPreviewEdit');
    var id        = btnEdit.getAttribute('data-id');
    var nama      = btnEdit.getAttribute('data-nama') || '';
    var kategori  = btnEdit.getAttribute('data-kategori') || 'fasilitas';
    var deskripsi = btnEdit.getAttribute('data-deskripsi') || '';
    var foto      = btnEdit.getAttribute('data-foto') || '';

    document.getElementById('edit_fasilitas_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_kategori').value = kategori;
    document.getElementById('edit_deskripsi').value = deskripsi;

    var imgEdit = document.getElementById('editPhoto');
    if (foto) {
        imgEdit.src = "../../../public/uploads/fasilitas/" + foto;
    } else {
        imgEdit.src = "../../../public/assets/img/blog/blog-1.jpg";
    }

    // tutup preview, buka edit
    var prevModalEl = document.getElementById('fasilitasPreviewModal');
    var prevModal   = bootstrap.Modal.getInstance(prevModalEl);
    if (prevModal) prevModal.hide();

    var editModalEl = document.getElementById('fasilitasEditModal');
    var editModal   = new bootstrap.Modal(editModalEl);
    editModal.show();
}

function previewFotoFasilitas(e) {
    var file = e.target.files[0];
    if (!file) return;

    var allowed = ['image/jpeg','image/png','image/webp','image/jpg'];
    if (allowed.indexOf(file.type) === -1) {
        alert("Format file harus JPG/PNG/WebP");
        e.target.value = "";
        return;
    }

    if (file.size > 2 * 1024 * 1024) { // 2MB
        alert("Ukuran foto maksimal 2 MB");
        e.target.value = "";
        return;
    }

    var reader = new FileReader();
    reader.onload = function(ev) {
        var img = document.getElementById('editPhoto');
        img.src = ev.target.result;
    };
    reader.readAsDataURL(file);
}
</script>

<style>
.card .btn { border-radius: 8px; }

/* FASILITAS GRID */
.fasilitas-item {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    background: #000000;
    transition: transform .2s ease, box-shadow .2s ease;
}
.fasilitas-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0,0,0,0.12);
}

.fasilitas-img {
    display: block;
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.fasilitas-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 11px;
    text-transform: uppercase;
}

.fasilitas-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.85), rgba(0,0,0,0.15));
    opacity: 0;
    display: flex;
    align-items: flex-end;
    transition: opacity .2s ease;
}
.fasilitas-item:hover .fasilitas-overlay {
    opacity: 1;
}

.fasilitas-overlay .overlay-inner {
    width: 100%;
    padding-bottom: 14px;
}

.fasilitas-overlay p {
    max-height: 3.6em;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0.75rem;
    word-break: break-word;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}

.preview-fasilitas-img {
    width: 100%;
    max-width: 295px;        /* lebih kecil dari 520px */
    max-height: 37vh;        /* lebih kecil dari 60vh */
    object-fit: contain;
    border-radius: 12px;
    background: #000;
    display: block;
    margin: 0 auto;          /* biar posisinya tetap center */
}

.modal-fasilitas {
    max-width: 850px;       /* kecilkan card putih */
}

@media (max-width: 768px) {
    .modal-fasilitas {
        max-width: 95%;     /* responsif di HP */
    }
}
</style>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
