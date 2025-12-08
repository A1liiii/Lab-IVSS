<?php
$title  = "Publikasi | IVSS";
$active = "publikasi";

require_once __DIR__ . '/../../core/database.php';

function limit_words(string $text, int $limit = 10): string {
    $words = preg_split('/\s+/', trim($text));
    if (!$words) return $text;
    if (count($words) <= $limit) {
        return $text;
    }
    $short = implode(' ', array_slice($words, 0, $limit));
    return $short . '...';
}

$db = Database::connect();

// query publikasi + penulis (dosen), tapi jangan hilangin publikasinya
$sql = "SELECT 
            p.publikasi_id,
            p.judul,
            p.link,
            p.tahun,
            COALESCE(d.nama, 'Tidak diketahui') AS penulis
        FROM publikasi AS p
        LEFT JOIN dosen AS d ON d.user_id = p.user_id
        ORDER BY p.tahun DESC";

$stmt = $db->prepare($sql);
$stmt->execute();
$publikasi = $stmt->fetchAll(PDO::FETCH_ASSOC);


// DEBUG ringan: kalau mau lihat berapa data yang ketarik
// echo '<pre>'; var_dump($publikasi); echo '</pre>'; exit;

ob_start();
?>


<!-- Publikasi Section -->
    <section id="recent-posts" class="recent-posts section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Recent Posts</h2>
        <p>Publikasi   </p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-5">

          <?php foreach ($publikasi as $p): ?>

<div class="col-xl-4 col-md-6">
  <div class="post-item position-relative h-100" data-aos="fade-up">

    <div class="post-img position-relative overflow-hidden">
      <img src="assets/img/blog/blog-1.jpg" class="img-fluid" alt="">
      <span class="post-date"><?= $p['tahun'] ?></span>
    </div>

    <div class="post-content d-flex flex-column">

      <h3 class="post-title" title="<?= htmlspecialchars($p['judul']) ?>">
        <?= htmlspecialchars(limit_words($p['judul'], 10)) ?>
      </h3>


      <div class="meta d-flex align-items-center">
        <div class="d-flex align-items-center">
          <i class="bi bi-person"></i>
          <span class="ps-2"><?= htmlspecialchars($p['penulis']) ?></span>
        </div>
      </div>

      <hr>

      <div class="readmore-box">
        <a href="<?= $p['link'] ?>" class="readmore stretched-link" target="_blank">
          <span>Read More</span>
          <i class="bi bi-arrow-right"></i>
        </a>
      </div>


    </div>

  </div>
</div>

<?php endforeach ?>


        </div>

      </div>

    </section><!-- /Recent Posts Section -->

<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>
