<?php
$title  = "Berita | IVSS";
$active = "berita";

require_once __DIR__ . '/../../core/database.php';
$db = Database::connect();

/* ==== Helper ==== */
function tgl_id($dateStr) {
    if (!$dateStr) return '-';
    return date('d M Y', strtotime($dateStr)); // 09 Dec 2025
}

function limit_words(string $text, int $limit = 30): string {
    $words = preg_split('/\s+/', trim($text));
    if (!$words) return $text;
    if (count($words) <= $limit) return $text;
    return implode(' ', array_slice($words, 0, $limit)) . '...';
}

/* ==== Ambil filter dari GET ==== */
$q        = isset($_GET['q']) ? trim($_GET['q']) : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';

/* ==== Query list berita (kolom kiri) ==== */
$sql = "SELECT 
            b.berita_id,
            b.judul,
            b.deskripsi,
            b.foto,
            b.tgl_post,
            b.kategori
        FROM berita AS b";

$conditions = [];
$params = [];

if ($q !== '') {
    $conditions[] = "(b.judul ILIKE :q OR b.deskripsi ILIKE :q)";
    $params[':q'] = '%' . $q . '%';
}

if ($kategori !== '') {
    $conditions[] = "b.kategori = :kat";
    $params[':kat'] = $kategori;
}

if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY b.tgl_post DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$berita = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
?>

<style>
/* ====== BERITA – LIST & SIDEBAR ====== */

/* Card tiap berita */
.blog .post-item {
  background: #ffffff;
  border-radius: 14px;
  padding: 20px;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
  transition: 0.2s ease;
}

.blog .post-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 26px rgba(0, 0, 0, 0.10);
}

/* Baris atas: thumbnail kiri + judul kanan */
.blog .post-head {
  display: flex;
  gap: 18px;
  align-items: flex-start;
}

/* Thumbnail */
.blog .post-img {
  flex: 0 0 220px;
  max-width: 220px;
  border-radius: 10px;
  overflow: hidden;
}

.blog .post-img img {
  width: 100%;
  height: 120px;
  display: block;
  object-fit: cover;  /* biar rapi tanpa blur aneh */
}

/* Judul */
.blog .post-title {
  font-size: 22px;
  font-weight: 700;
  margin: 0;
  max-width: 520px;  /* lebar judul dibatasi */
}

.blog .post-title a {
  color: #0F2F8A; /* biru IVSS */
  text-decoration: none;
}

.blog .post-title a:hover {
  text-decoration: underline;
}

/* Meta kecil di bawah judul (tanggal + kategori sejajar) */
.blog .post-meta-mini {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-top: 6px;
  font-size: 0.82rem;
  color: #9a9a9a;
}

.blog .post-meta-mini i {
  color: #fbbf24 !important;
  font-size: 0.9rem;
}

.blog .post-meta-mini span {
  color: #9a9a9a;
  font-weight: 400;
}

/* BAGIAN BAWAH: deskripsi */
.blog .post-body {
  margin-top: 18px;
}

.blog .post-desc {
  color: #444;
  margin-bottom: 14px;
}

/* Read More – bar biru full width di bawah card */
.blog .post-item .readmore-box {
  background: #0F2F8A;                 /* biru tua IVSS */
  padding: 14px 20px;
  margin: 0 -20px -16px -20px;         /* samakan dengan padding card */
  border-radius: 0 0 12px 12px;
  transition: background 0.3s ease;
}

.blog .post-item:hover .readmore-box {
  background: #0C256E;                 /* sedikit lebih gelap saat hover */
}

.blog .post-item .readmore-box .readmore {
  display: flex;
  align-items: center;
  font-weight: 600;
  color: #ffffff !important;
  letter-spacing: 0.05em;
  font-size: 0.95rem;
  text-decoration: none;
}

.blog .post-item .readmore-box .readmore i {
  margin-left: 6px;
  font-size: 1rem;
  transition: transform 0.2s ease;
  color: #ffffff !important;
}

.blog .post-item .readmore-box .readmore:hover i {
  transform: translateX(4px);
}

/* ===== Sidebar umum ===== */
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

/* Responsif */
@media (max-width: 768px) {
  .blog .post-head {
    flex-direction: column;
  }
  
  .blog .post-img {
    max-width: 100%;
  }
}
</style>

<?php
ob_start();
?>

<!-- ====== Berita List Section ====== -->
<section id="blog" class="blog section">

  <!-- Tanpa breadcrumb, langsung judul -->
  <div class="container section-title" data-aos="fade-up">
    <h2>Berita</h2>
    <p>PENGUMUMAN DAN AKTIVITAS</p>
  </div>

  <div class="container">
    <div class="row gy-4">

      <!-- ===== Kolom kiri: list berita ===== -->
      <div class="col-lg-8">

        <?php if (empty($berita)): ?>
          <p>Tidak ada berita yang ditemukan.</p>
        <?php else: ?>

          <?php foreach ($berita as $b): 
            $thumb = $b['foto']
              ? "../../../public/uploads/berita/" . $b['foto']
              : "assets/img/blog/blog-1.jpg";
          ?>

            <article class="post-item mb-4" data-aos="fade-up">

              <!-- BARIS ATAS: thumbnail kiri + judul + meta di kanan -->
              <div class="post-head">
                <!-- Thumbnail -->
                <div class="post-img">
                  <img src="<?= htmlspecialchars($thumb) ?>" alt="Thumbnail berita">
                </div>

                <!-- Judul + tanggal + kategori -->
                <div class="post-main">
                  <h3 class="post-title">
                    <a href="detail_berita.php?id=<?= (int)$b['berita_id'] ?>">
                      <?= htmlspecialchars($b['judul']) ?>
                    </a>
                  </h3>

                  <div class="post-meta-mini">
                    <div class="d-flex align-items-center gap-1">
                      <i class="bi bi-clock"></i>
                      <span><?= htmlspecialchars(tgl_id($b['tgl_post'])) ?></span>
                    </div>

                    <div class="d-flex align-items-center gap-1">
                      <i class="bi bi-folder"></i>
                      <span><?= htmlspecialchars(ucfirst($b['kategori'])) ?></span>
                    </div>
                  </div>
                </div><!-- end .post-main -->
              </div><!-- end .post-head -->

              <!-- BAGIAN BAWAH: deskripsi -->
              <div class="post-body">
                <p class="post-desc">
                  <?= htmlspecialchars(limit_words($b['deskripsi'] ?? '', 35)) ?>
                </p>
              </div>

              <!-- Bar biru Read More -->
              <div class="readmore-box">
                <a href="detail_berita.php?id=<?= (int)$b['berita_id'] ?>" class="readmore">
                  <span>Read More</span>
                  <i class="bi bi-arrow-right"></i>
                </a>
              </div>

            </article>

          <?php endforeach; ?>

        <?php endif; ?>

      </div><!-- End col-lg-8 -->

      <!-- ===== Kolom kanan: sidebar ===== -->
      <div class="col-lg-4">

        <!-- Search -->
        <div class="sidebar-item mb-4">
          <h3 class="sidebar-title">Search</h3>
          <form action="berita.php" method="get" class="mt-3 d-flex">
            <input type="text"
                   class="form-control"
                   name="q"
                   placeholder="Cari berita..."
                   value="<?= htmlspecialchars($q) ?>">
            <?php if ($kategori): ?>
              <input type="hidden" name="kategori" value="<?= htmlspecialchars($kategori) ?>">
            <?php endif; ?>
            <button class="btn btn-warning ms-2" type="submit">
              <i class="bi bi-search"></i>
            </button>
          </form>
        </div>

        <!-- Categories -->
        <div class="sidebar-item mb-4">
          <h3 class="sidebar-title">Categories</h3>
          <ul class="mt-3">
            <?php foreach ($kategoriList as $k): 
              $namaKat = $k['kategori'] ?: 'lainnya';
              $link = "berita.php?kategori=" . urlencode($namaKat);
              if ($q) {
                $link .= "&q=" . urlencode($q);
              }
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
          <h3 class="sidebar-title">Recent Posts</h3>

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
?>