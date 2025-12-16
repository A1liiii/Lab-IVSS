<?php
$title  = "Detail Berita | IVSS";
$active = "berita"; // biar menu 'Berita' tetap aktif

require_once __DIR__ . '/../../core/database.php';
$db = Database::connect();

/* ==== Helper ==== */
function tgl_id($dateStr) {
    if (!$dateStr) return '-';
    return date('d M Y', strtotime($dateStr)); // 09 Dec 2025
}

function limit_words(string $text, int $limit = 10): string {
    $words = preg_split('/\s+/', trim($text));
    if (!$words) return $text;
    if (count($words) <= $limit) return $text;
    return implode(' ', array_slice($words, 0, $limit)) . '...';
}

/* ==== Ambil ID dari query string ==== */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* ==== Ambil 1 berita ==== */
$detail = null;
if ($id > 0) {
    $stmt = $db->prepare("
        SELECT 
            b.berita_id,
            b.judul,
            b.deskripsi,
            b.foto,
            b.file_url,
            b.tgl_post,
            b.kategori
        FROM berita AS b
        WHERE b.berita_id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $id]);
    $detail = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* ==== Sidebar: kategori (dengan jumlah) ==== */
$sqlKat = "SELECT kategori, COUNT(*) AS jumlah
           FROM berita
           GROUP BY kategori
           ORDER BY kategori";
$katStmt = $db->query($sqlKat);
$kategoriList = $katStmt->fetchAll(PDO::FETCH_ASSOC);

/* ==== Sidebar: recent posts ==== */
$sqlRecent = "SELECT 
                berita_id,
                judul,
                foto,
                tgl_post
              FROM berita
              ORDER BY tgl_post DESC
              LIMIT 5";
$recentStmt = $db->query($sqlRecent);
$recentPosts = $recentStmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<style>
/* ====== DETAIL BERITA (kiri) ====== */

.blog .post-detail {
  background: #ffffff;
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
}

/* Thumbnail besar di atas */
.blog .post-detail .post-img {
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 20px;
}

.blog .post-detail .post-img img {
  width: 100%;
  height: auto;
  display: block;
}

/* Judul besar */
.blog .detail-title {
  font-size: 26px;
  font-weight: 700;
  color: #0F2F8A;
  margin-bottom: 10px;
}

/* Meta kecil (tanggal + kategori) */
.blog .post-meta-mini {
  display: flex;
  align-items: center;
  gap: 18px;
  margin-bottom: 18px;
  font-size: 0.9rem;
  color: #9a9a9a;
}

.blog .post-meta-mini i {
  color: #fbbf24 !important;
  font-size: 1rem;
}

.blog .post-meta-mini span {
  color: #6b7280;
}

/* Isi deskripsi */
.blog .detail-content {
  font-size: 0.98rem;
  line-height: 1.7;
  color: #374151;
}

/* Link file (jika ada) */
.blog .detail-file {
  margin-top: 18px;
}

/* ====== Sidebar (kanan) – sama dengan berita.php ====== */

.blog .sidebar-title {
  font-size: 1.05rem;
  font-weight: 600;
  margin-bottom: 10px;
}

/* Categories: tanpa bullet, abu-abu */
.blog .sidebar-item ul {
  list-style: none;
  padding-left: 0;
  margin: 0;
}

.blog .sidebar-item ul li + li {
  margin-top: 6px;
}

.blog .sidebar-item ul li a {
  display: flex;
  justify-content: space-between;
  align-items: center;
  text-decoration: none;
  font-size: 0.95rem;
  color: #6b7280;             /* abu-abu tua */
}

.blog .sidebar-item ul li a span {
  color: #9ca3af;             /* abu-abu muda utk angka */
  font-size: 0.85rem;
}

/* Search button di sidebar biar rapi */
.blog .sidebar-item form button {
  border-radius: 6px;
}

/* Recent posts: judul hitam */
.blog .recent-title a {
  color: #111827;
  text-decoration: none;
}

.blog .recent-title a:hover {
  color: #0F2F8A;
}

/* Recent posts: tanggal kecil + icon */
.blog .recent-date {
  font-size: 0.8rem;
  color: #9ca3af;
}

.blog .recent-date i {
  font-size: 0.85rem;
  vertical-align: -1px;
}
</style>

<section id="blog" class="blog section">

  <div class="container section-title" data-aos="fade-up">
    <h2>Berita</h2>
    <p>Berita Lengkap</p>
  </div>

  <div class="container">
    <div class="row gy-4">

      <!-- ===== KIRI: DETAIL BERITA ===== -->
      <div class="col-lg-8">

        <?php if (!$detail): ?>
          <p>Berita tidak ditemukan.</p>
        <?php else: ?>
          <?php
            $thumb = $detail['foto']
              ? "../../../public/uploads/berita/" . $detail['foto']
              : "assets/img/blog/blog-1.jpg";
          ?>

          <article class="post-detail" data-aos="fade-up">

            <!-- Thumbnail -->
            <div class="post-img">
              <img src="<?= htmlspecialchars($thumb) ?>" alt="Thumbnail berita">
            </div>

            <!-- Judul -->
            <h1 class="detail-title">
              <?= htmlspecialchars($detail['judul']) ?>
            </h1>

            <!-- Meta: tanggal + kategori -->
            <div class="post-meta-mini">
              <div class="d-flex align-items-center gap-1">
                <i class="bi bi-clock"></i>
                <span><?= htmlspecialchars(tgl_id($detail['tgl_post'])) ?></span>
              </div>
              <div class="d-flex align-items-center gap-1">
                <i class="bi bi-folder"></i>
                <span><?= htmlspecialchars(ucfirst($detail['kategori'] ?: 'Lainnya')) ?></span>
              </div>
            </div>

            <!-- Isi berita (deskripsi penuh) -->
            <div class="detail-content">
              <?= nl2br(htmlspecialchars($detail['deskripsi'] ?? '')) ?>
            </div>

            <!-- Link file eksternal (opsional) -->
            <?php if (!empty($detail['file_url'])): ?>
              <div class="detail-file">
                <a href="../../../public/uploads/berita/files/<?= htmlspecialchars($detail['file_url']) ?>"
                  target="_blank"
                  rel="noopener"
                  class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-file-earmark-pdf me-1"></i> Lihat Dokumen
                </a>
              </div>
            <?php endif; ?>

          </article>
        <?php endif; ?>

      </div>

      <!-- ===== KANAN: SIDEBAR ===== -->
      <div class="col-lg-4">

        <!-- Search -->
        <div class="sidebar-item mb-4">
          <h3 class="sidebar-title">Cari</h3>
          <!-- search diarahkan ke list berita -->
          <form action="berita.php" method="get" class="mt-3 d-flex">
            <input type="text"
                   class="form-control"
                   name="q"
                   placeholder="Cari berita...">
            <button class="btn btn-warning ms-2" type="submit">
              <i class="bi bi-search"></i>
            </button>
          </form>
        </div>

        <!-- Categories -->
        <div class="sidebar-item mb-4">
          <h3 class="sidebar-title">kategori</h3>
          <ul class="mt-3">
            <?php foreach ($kategoriList as $k): 
              $namaKat = $k['kategori'] ?: 'lainnya';
              $link = "berita.php?kategori=" . urlencode($namaKat);
            ?>
              <li>
                <a href="<?= $link ?>">
                  <?= htmlspecialchars(ucfirst($namaKat)) ?>
                  <span>(<?= (int)$k['jumlah'] ?>)</span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <!-- Recent Posts -->
        <div class="sidebar-item">
          <h3 class="sidebar-title">Berita terbaru</h3>

          <div class="mt-3">
            <?php foreach ($recentPosts as $r): 
              $thumbSmall = $r['foto']
              
                ? "../../../public/uploads/berita/" . $r['foto']
                : "assets/img/blog/blog-1.jpg";
            ?>
              <div class="d-flex mb-3">
                <div class="flex-shrink-0 me-3">
                  <img src="<?= htmlspecialchars($thumbSmall) ?>"
                       alt="recent"
                       style="width: 80px; height: 60px; object-fit: cover; border-radius: 6px;">
                </div>
                <div class="flex-grow-1">
                  <h4 class="mb-1 recent-title" style="font-size: 0.95rem;">
                    <a href="detail_berita.php?id=<?= (int)$r['berita_id'] ?>">
                      <?= htmlspecialchars(limit_words($r['judul'], 8)) ?>
                    </a>
                  </h4>
                  <span class="recent-date">
                    <i class="bi bi-calendar-event me-1"></i>
                    <?= htmlspecialchars(tgl_id($r['tgl_post'])) ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div><!-- End sidebar -->

    </div>
  </div>

</section>

<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
