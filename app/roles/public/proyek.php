<?php
$title = "Publikasi | IVSS";
$active = "publikasi";
?>

<style>
/* ====== Publikasi Card – Theme IVSS (Biru + Kuning) ====== */

.post-item {
  border-radius: 16px;
  background: #0b1120; /* biru tua */
  padding: 18px 20px;
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}

.post-item:hover {
  transform: translateY(-6px);
  box-shadow: 0 14px 50px rgba(0, 0, 0, 0.6);
  border-color: #fbbf24; /* kuning IVSS */
}

/* Tahun badge (pojok kanan atas) */
.post-date {
  position: absolute;
  top: 14px;
  right: 14px;
  background: #fbbf24;
  color: #1e1e1e;
  padding: 5px 12px;
  border-radius: 50px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.08em;
}

/* Judul */
.post-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #e5e7eb;
  transition: color 0.2s ease;
  margin-bottom: 10px;
}

.post-title:hover {
  color: #fbbf24;
}

/* Meta (penulis) */
.meta {
  color: #9ca3af !important;
  font-size: 0.9rem;
  margin-bottom: 8px;
}

.meta i {
  color: #fbbf24;
}

/* Read more */
.readmore {
  display: inline-flex;
  align-items: center;
  font-weight: 600;
  color: #fbbf24;
  letter-spacing: 0.08em;
  font-size: 0.85rem;
  transition: all 0.2s ease;
}

.readmore:hover {
  color: #fcd34d;
}

.readmore i {
  transition: transform 0.2s ease;
}

.post-item:hover .readmore i {
  transform: translateX(4px);
}
</style>

<?php
ob_start(); 
?>


<!-- Recent Posts Section -->
    <section id="recent-posts" class="recent-posts section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Recent Posts</h2>
        <p>Publikasi   </p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-5">

          <div class="col-xl-4 col-md-6">
            <div class="post-item position-relative h-100" data-aos="fade-up" data-aos-delay="100">

              <div class="post-img position-relative overflow-hidden">
                <img src="assets/img/blog/blog-1.jpg" class="img-fluid" alt="">
                <span class="post-date">December 12</span>
              </div>

              <div class="post-content d-flex flex-column">

                <h3 class="post-title">Eum ad dolor et. Autem aut fugiat debitis</h3>

                <div class="meta d-flex align-items-center">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-person"></i> <span class="ps-2">Julia Parker</span>
                  </div>
                  <span class="px-3 text-black-50">/</span>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-folder2"></i> <span class="ps-2">Politics</span>
                  </div>
                </div>

                <hr>

                <a href="blog-details.html" class="readmore stretched-link"><span>Read More</span><i class="bi bi-arrow-right"></i></a>

              </div>

            </div>
          </div><!-- End post item -->

          <div class="col-xl-4 col-md-6">
            <div class="post-item position-relative h-100" data-aos="fade-up" data-aos-delay="200">

              <div class="post-img position-relative overflow-hidden">
                <img src="assets/img/blog/blog-2.jpg" class="img-fluid" alt="">
                <span class="post-date">July 17</span>
              </div>

              <div class="post-content d-flex flex-column">

                <h3 class="post-title">Et repellendus molestiae qui est sed omnis</h3>

                <div class="meta d-flex align-items-center">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-person"></i> <span class="ps-2">Mario Douglas</span>
                  </div>
                  <span class="px-3 text-black-50">/</span>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-folder2"></i> <span class="ps-2">Sports</span>
                  </div>
                </div>

                <hr>

                <a href="blog-details.html" class="readmore stretched-link"><span>Read More</span><i class="bi bi-arrow-right"></i></a>

              </div>

            </div>
          </div><!-- End post item -->

          <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="post-item position-relative h-100">

              <div class="post-img position-relative overflow-hidden">
                <img src="assets/img/blog/blog-3.jpg" class="img-fluid" alt="">
                <span class="post-date">September 05</span>
              </div>

              <div class="post-content d-flex flex-column">

                <h3 class="post-title">Quia assumenda est et veritati tirana ploder</h3>

                <div class="meta d-flex align-items-center">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-person"></i> <span class="ps-2">Lisa Hunter</span>
                  </div>
                  <span class="px-3 text-black-50">/</span>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-folder2"></i> <span class="ps-2">Economics</span>
                  </div>
                </div>

                <hr>

                <a href="blog-details.html" class="readmore stretched-link"><span>Read More</span><i class="bi bi-arrow-right"></i></a>

              </div>

            </div>
          </div><!-- End post item -->

        </div>

      </div>

    </section><!-- /Recent Posts Section -->

<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>
