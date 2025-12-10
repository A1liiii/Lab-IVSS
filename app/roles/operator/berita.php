<?php
// app/roles/operator/berita.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../../core/auth.php";
requireRole("operator");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$active = "berita";
$title  = "Manajemen Berita";

function safe($v){
    return htmlspecialchars((string)(isset($v) ? $v : ""), ENT_QUOTES, 'UTF-8');
}
function excerpt($text, $len = 120){
    $text = trim(strip_tags(isset($text) ? $text : ""));
    if (mb_strlen($text) <= $len) return $text;
    return mb_substr($text, 0, $len) . "...";
}
function text_length($s){
    $s = isset($s) ? (string)$s : '';
    if (function_exists('mb_strlen')) {
        return mb_strlen($s, 'UTF-8');
    }
    return strlen($s);
}


$currentUserId = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : null;

// ====================== HANDLE POSTS (ADD / UPDATE / DELETE) ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action    = isset($_POST['action']) ? $_POST['action'] : '';

    // ---------- TAMBAH BERITA ----------
    if ($action === 'add') {
        $judul     = isset($_POST['judul']) ? trim($_POST['judul']) : '';
        $kategori  = isset($_POST['kategori']) ? trim($_POST['kategori']) : '';
        $tgl_post  = isset($_POST['tgl_post']) ? trim($_POST['tgl_post']) : '';
        $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';

        if ($judul === '' || $kategori === '') {
            $_SESSION['flash_error'] = "Judul dan kategori wajib diisi.";
            header("Location: berita.php");
            exit;
        }

        if ($tgl_post === '') {
            $tgl_post = date("Y-m-d");
        }

        // Upload foto (opsional)
        $fotoName = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $foto = $_FILES['foto'];
            $allowed = ['image/jpeg','image/jpg','image/png','image/webp'];
            if (in_array($foto['type'], $allowed)) {
                $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                    $newName = 'berita_' . time() . '_' . rand(100,999) . '.' . $ext;
                    $dest = __DIR__ . "/../../../public/uploads/berita/" . $newName;
                    if (move_uploaded_file($foto['tmp_name'], $dest)) {
                        $fotoName = $newName;
                    }
                }
            }
        }

        // file_url (lampiran) sementara dikosongkan
        $fileUrl = null;

        $stmt = $conn->prepare("
            INSERT INTO berita (user_id, judul, deskripsi, foto, file_url, tgl_post, kategori)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $currentUserId,
            $judul,
            $deskripsi,
            $fotoName,
            $fileUrl,
            $tgl_post,
            $kategori
        ]);

        $_SESSION['flash_success'] = "Berita berhasil ditambahkan.";
        header("Location: berita.php");
        exit;
    }

    // ---------- UPDATE BERITA ----------
    if ($action === 'update') {

        $id        = isset($_POST['berita_id']) ? $_POST['berita_id'] : null;
        $judul     = isset($_POST['judul']) ? trim($_POST['judul']) : '';
        $kategori  = isset($_POST['kategori']) ? trim($_POST['kategori']) : '';
        $tgl_post  = isset($_POST['tgl_post']) ? trim($_POST['tgl_post']) : '';
        $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';

        if (!$id) {
            $_SESSION['flash_error'] = "ID berita tidak ditemukan.";
            header("Location: berita.php");
            exit;
        }

        if ($judul === '' || $kategori === '') {
            $_SESSION['flash_error'] = "Judul dan kategori wajib diisi.";
            header("Location: berita.php");
            exit;
        }

        if ($tgl_post === '') {
            $tgl_post = date("Y-m-d");
        }

        // ===================== CEK FOTO BARU ======================
        $fotoName = null;

        if (!empty($_FILES['foto_baru']) && $_FILES['foto_baru']['error'] === UPLOAD_ERR_OK) {

            $foto = $_FILES['foto_baru'];
            $allowed = ['image/jpeg','image/png','image/webp'];

            if (in_array($foto['type'], $allowed)) {
                $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));

                if (in_array($ext, ['jpg','jpeg','png','webp'])) {

                    $newName = 'berita_' . time() . '_' . rand(100,999) . '.' . $ext;
                    $uploadPath = __DIR__ . "/../../../public/uploads/berita/" . $newName;

                    move_uploaded_file($foto['tmp_name'], $uploadPath);

                    // ambil foto lama
                    $stmtFoto = $conn->prepare("SELECT foto FROM berita WHERE berita_id = ?");
                    $stmtFoto->execute([$id]);
                    $old = $stmtFoto->fetchColumn();

                    if ($old && is_file(__DIR__ . "/../../../public/uploads/berita/" . $old)) {
                        unlink(__DIR__ . "/../../../public/uploads/berita/" . $old);
                    }

                    $fotoName = $newName;
                }
            }
        }

        // ===================== UPDATE BERITA ======================
        if ($fotoName) {
            $stmt = $conn->prepare("
                UPDATE berita
                SET judul = ?, kategori = ?, tgl_post = ?, deskripsi = ?, foto = ?
                WHERE berita_id = ?
            ");
            $stmt->execute([$judul, $kategori, $tgl_post, $deskripsi, $fotoName, $id]);
        } else {
            $stmt = $conn->prepare("
                UPDATE berita
                SET judul = ?, kategori = ?, tgl_post = ?, deskripsi = ?
                WHERE berita_id = ?
            ");
            $stmt->execute([$judul, $kategori, $tgl_post, $deskripsi, $id]);
        }

        $_SESSION['flash_success'] = "Berita berhasil diperbarui.";
        header("Location: berita.php");
        exit;
    }


    // ---------- HAPUS BERITA ----------
    if ($action === 'delete') {
        $id = isset($_POST['berita_id']) ? $_POST['berita_id'] : null;
        if ($id) {
            // ambil nama file dulu agar bisa dihapus
            $stmt = $conn->prepare("SELECT foto FROM berita WHERE berita_id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmtDel = $conn->prepare("DELETE FROM berita WHERE berita_id = ?");
            $stmtDel->execute([$id]);

            // hapus file fisik (kalau ada)
            if (!empty($row['foto'])) {
                $path = __DIR__ . "/../../../public/uploads/berita/" . $row['foto'];
                if (is_file($path)) @unlink($path);
            }

            $_SESSION['flash_success'] = "Berita telah dihapus.";
        } else {
            $_SESSION['flash_error'] = "ID berita tidak ditemukan.";
        }

        header("Location: berita.php");
        exit;
    }
}

// ====================== LIST + SEARCH + PAGINATION ======================
$page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
$perPage = 6; // 3 kolom x 2 baris
$offset = ($page - 1) * $perPage;

$search = isset($_GET['q']) ? trim($_GET['q']) : "";
$where  = "WHERE 1=1";
$params = [];

if ($search !== '') {
    $where .= " AND (LOWER(judul) LIKE ? OR LOWER(kategori) LIKE ?)";
    $params[] = '%' . strtolower($search) . '%';
    $params[] = '%' . strtolower($search) . '%';
}

// total count
$stmtCount = $conn->prepare("SELECT COUNT(*) FROM berita $where");
$stmtCount->execute($params);
$total = (int)$stmtCount->fetchColumn();
$totalPages = (int)ceil($total / $perPage);

// fetch page
$sql = "SELECT berita_id, judul, deskripsi, foto, file_url, tgl_post, kategori
        FROM berita
        $where
        ORDER BY tgl_post DESC, berita_id DESC
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

// flash messages
$flash_success = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : null;
$flash_error   = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-newspaper"></i> Manajemen Berita
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
        <input type="text" name="q" class="form-control" placeholder="Cari judul atau kategori..."
               value="<?= safe($search) ?>">
        <button class="btn btn-outline-primary" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </form>

    <button class="btn btn-primary ms-auto" type="button" onclick="toggleAddBerita()">
        <i class="bi bi-plus-lg"></i> Tambah Berita
    </button>
</div>

<!-- ADD FORM (TOGGLE) -->
<div id="addBeritaBox" class="card shadow-sm border-0 p-3 mb-4" style="display:none;">
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="action" value="add">

        <div class="col-md-6">
            <label class="form-label small">Judul Berita</label>
            <input type="text" name="judul" class="form-control" required>
        </div>

        <div class="col-md-3">
            <label class="form-label small">Kategori</label>
            <select name="kategori" class="form-select" required>
                <option value="">- Pilih -</option>
                <option value="berita">Berita</option>
                <option value="pengumuman">Pengumuman</option>
                <option value="aktivitas">Aktivitas</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label small">Tanggal Posting</label>
            <input type="date" name="tgl_post" class="form-control"
                   value="<?= date('Y-m-d') ?>">
        </div>

        <div class="col-md-6">
            <label class="form-label small">Foto (opsional)</label>
            <input type="file" name="foto" class="form-control"
                   accept=".jpg,.jpeg,.png,.webp">
            <div class="form-text small">Disimpan di: <code>/public/uploads/berita/</code></div>
        </div>

        <div class="col-12">
            <label class="form-label small">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="form-control" placeholder="Isi berita..."></textarea>
        </div>

        <div class="col-12 text-end">
            <button type="button" class="btn btn-secondary" onclick="toggleAddBerita()">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>

<!-- LIST BERITA (GRID 3 x 2) -->
<div class="row g-4">
    <?php if(empty($list)): ?>
        <div class="col-12">
            <div class="card p-4 text-center text-muted">
                Belum ada berita.
            </div>
        </div>
    <?php else: ?>
        <?php foreach($list as $b): 
            $imgSrc = "../../../public/assets/img/blog/blog-1.jpg";
            if (!empty($b['foto'])) {
                $imgSrc = "../../../public/uploads/berita/" . safe($b['foto']);
            }
            $tgl = $b['tgl_post'] ? date("d M Y", strtotime($b['tgl_post'])) : "-";
        ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-sm h-100 berita-card"
                     data-id="<?= (int)$b['berita_id'] ?>"
                     data-judul="<?= safe($b['judul']) ?>"
                     data-kategori="<?= safe($b['kategori']) ?>"
                     data-tgl="<?= safe($b['tgl_post']) ?>"
                     data-deskripsi="<?= safe($b['deskripsi']) ?>"
                     data-foto="<?= safe($b['foto']) ?>"
                     data-fileurl="<?= safe($b['file_url']) ?>">
                    
                    <img src="<?= $imgSrc ?>" class="card-img-top"
                        style="height:160px;object-fit:cover;"
                        onerror="this.onerror=null;this.src='../../../public/assets/img/blog/blog-1.jpg';">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary text-uppercase small">
                                <?= safe($b['kategori'] ?: '-') ?>
                            </span>
                            <span class="text-muted small">
                                <i class="bi bi-calendar"></i> <?= safe($tgl) ?>
                            </span>
                        </div>

                        <h6 class="fw-bold mb-2"><?= safe($b['judul']) ?></h6>
                        <?php
                            $fullTextRaw  = isset($b['deskripsi']) ? $b['deskripsi'] : "";
                            $fullText     = trim(strip_tags($fullTextRaw));
                            $shortText    = excerpt($fullText, 150);
                            $hasMore      = (text_length($fullText) > text_length($shortText));
                        ?>
                        <p class="small text-muted mb-3 flex-grow-1">
                            <span class="berita-short"><?= safe($shortText) ?></span>
                            <?php if ($hasMore): ?>
                                <span class="berita-full d-none"><?= safe($fullText) ?></span>
                                <span class="toggle-view text-primary" style="cursor:pointer;">View more</span>
                            <?php endif; ?>
                        </p>
                        <div class="d-flex gap-2">
                            <button type="button"
                                    class="btn btn-outline-primary btn-sm w-100"
                                    onclick="openBeritaDetail(this)">
                                <i class="bi bi-pencil-square"></i> Detail / Edit
                            </button>

                            <form method="POST"
                                  onsubmit="return confirm('Hapus berita ini?');"
                                  style="width:80px;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="berita_id" value="<?= (int)$b['berita_id'] ?>">
                                <button type="submit"
                                        class="btn btn-outline-danger btn-sm w-100">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
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
                   href="?<?= http_build_query(array_merge($_GET, ['page' => max(1,$page-1)])) ?>">
                    Prev
                </a>
            </li>

            <?php
            $start = max(1, $page - 3);
            $end   = min($totalPages, $page + 3);
            for ($p = $start; $p <= $end; $p++): ?>
                <li class="page-item <?= $p==$page ? 'active' : '' ?>">
                    <a class="page-link"
                       href="?<?= http_build_query(array_merge($_GET, ['page'=>$p])) ?>">
                        <?= $p ?>
                    </a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= $page>=$totalPages ? 'disabled' : '' ?>">
                <a class="page-link"
                   href="?<?= http_build_query(array_merge($_GET, ['page' => min($totalPages,$page+1)])) ?>">
                    Next
                </a>
            </li>
        </ul>
    </nav>
<?php endif; ?>

<!-- DETAIL / EDIT MODAL -->
<div class="modal fade" id="beritaDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
    <form method="POST" enctype="multipart/form-data" id="beritaDetailForm">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="berita_id" id="berita_id_field">

        <div class="modal-header">
          <h5 class="modal-title">
              <i class="bi bi-newspaper"></i> Detail Berita
          </h5>
          <button type="button" class="btn-close"
                  data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
            <div class="row g-3">
                <div class="col-md-4 text-center">
                <img id="beritaPhoto"
                    src="../../../public/assets/img/blog/blog-1.jpg"
                    style="width:100%;max-width:220px;height:150px;object-fit:cover;border-radius:8px;">

                    <label class="mt-3 w-100 btn btn-outline-primary btn-sm">
                        <input type="file"
                            id="foto_input"
                            name="foto_baru"
                            accept=".jpg,.jpeg,.png,.webp"
                            class="d-none"
                            onchange="previewFotoBerita(event)">
                        <i class="bi bi-image"></i> Pilih Foto Baru
                    </label>

                    <div class="small text-muted">
                        Format: JPG/PNG/WebP — maks 2MB
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mb-2">
                        <label class="form-label small">Judul</label>
                        <input type="text" class="form-control"
                               id="judul_field" name="judul" required>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-md-6">
                            <label class="form-label small">Kategori</label>
                            <select class="form-select" id="kategori_field" name="kategori" required>
                                <option value="berita">Berita</option>
                                <option value="pengumuman">Pengumuman</option>
                                <option value="aktivitas">Aktivitas</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Tanggal Posting</label>
                            <input type="date" class="form-control"
                                   id="tgl_field" name="tgl_post">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small">Deskripsi</label>
                        <textarea class="form-control"
                                  id="deskripsi_field"
                                  name="deskripsi" rows="5"></textarea>
                    </div>

                    <div class="small text-muted" id="fileInfoBox" style="display:none;">
                        Lampiran: <a href="#" id="fileUrlLink" target="_blank">Lihat file</a>
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
function toggleAddBerita(){
    const box = document.getElementById('addBeritaBox');
    box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
}

function openBeritaDetail(btn){
    const card = btn.closest('.berita-card');
    if (!card) return;

    const id        = card.dataset.id;
    const judul     = card.dataset.judul || '';
    const kategori  = card.dataset.kategori || 'berita';
    const tgl       = card.dataset.tgl || '';
    const deskripsi = card.dataset.deskripsi || '';
    const foto      = card.dataset.foto || '';
    const fileUrl   = card.dataset.fileurl || '';

    document.getElementById('berita_id_field').value = id;
    document.getElementById('judul_field').value = judul;
    document.getElementById('kategori_field').value = kategori;
    document.getElementById('tgl_field').value = tgl;
    document.getElementById('deskripsi_field').value = deskripsi;

    const img = document.getElementById('beritaPhoto');
    if (foto) {
        img.src = "../../../public/uploads/berita/" + foto;
    } else {
        img.src = "../../../public/assets/img/blog/blog-1.jpg";
    }

    const fileBox = document.getElementById('fileInfoBox');
    const link    = document.getElementById('fileUrlLink');
    if (fileUrl) {
        fileBox.style.display = 'block';
        link.href = fileUrl;
    } else {
        fileBox.style.display = 'none';
    }

    const modal = new bootstrap.Modal(document.getElementById('beritaDetailModal'));
    modal.show();
}

function previewFotoBerita(e) {
    const file = e.target.files[0];
    if (!file) return;

    const allowed = ['image/jpeg','image/png','image/webp'];
    if (!allowed.includes(file.type)) {
        alert("Format file harus JPG/PNG/WebP");
        e.target.value = "";
        return;
    }

    if (file.size > 2 * 1024 * 1024) { // 2MB
        alert("Ukuran foto maksimal 2 MB");
        e.target.value = "";
        return;
    }

    const reader = new FileReader();
    reader.onload = function(ev) {
        document.getElementById('beritaPhoto').src = ev.target.result;
    };
    reader.readAsDataURL(file);
}

document.addEventListener('DOMContentLoaded', function () {
    var toggles = document.querySelectorAll('.toggle-view');
    for (var i = 0; i < toggles.length; i++) {
        toggles[i].addEventListener('click', function () {
            var btn = this;
            var p   = btn.parentNode;
            var shortEl = p.querySelector('.berita-short');
            var fullEl  = p.querySelector('.berita-full');

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

.berita-card {
    border-radius: 14px;
    overflow: hidden;
    transition: .2s ease;
}
.berita-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 24px rgba(0,0,0,0.08);
}
</style>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
