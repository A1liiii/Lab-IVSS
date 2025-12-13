<?php 
$title = "Fasilitas | IVSS";
$active = "fasilitas";

require_once __DIR__ . "/../../core/database.php";
$db = Database::connect();

function e($v){
  return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

// Ambil semua fasilitas
$qFasilitas = $db->query("SELECT * FROM fasilitas ORDER BY fasilitas_id DESC");
$fasilitas = $qFasilitas->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<section id="portfolio" class="portfolio section portfolio-page" style="padding-top: 100px;">

  <div class="container section-title" data-aos="fade-up">
    <h2>Fasilitas &amp; Peralatan</h2>
    <p>Daftar fasilitas dan peralatan laboratorium IVSS</p>
  </div>

  <div class="container">

    <div class="isotope-layout" data-default-filter="*" data-layout="fitRows" data-sort="original-order">

      <!-- FILTER BUTTONS -->
      <ul class="portfolio-filters isotope-filters portfolio-tabs" data-aos="fade-up" data-aos-delay="100">
        <li data-filter="*" class="filter-active">Semua</li>
        <li data-filter=".filter-fasilitas">Fasilitas</li>
        <li data-filter=".filter-peralatan">Peralatan</li>
      </ul>

      <!-- ITEM LIST -->
      <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">

        <?php if (!empty($fasilitas)): ?>
          <?php foreach ($fasilitas as $f): 
            $kategori = strtolower(trim(isset($f['kategori']) ? $f['kategori'] : ''));
            $kategoriSlug = ($kategori === 'peralatan') ? 'peralatan' : 'fasilitas';

            $foto = !empty($f['foto'])
              ? "/Lab-IVSS/public/uploads/fasilitas/" . e($f['foto'])
              : "/Lab-IVSS/public/assets/img/facility-placeholder.jpg";

            $nama     = e(isset($f['nama']) ? $f['nama'] : 'Tanpa Nama');
            $descRaw  = strip_tags((string)(isset($f['deskripsi']) ? $f['deskripsi'] : ''));
            $desc     = e($descRaw);

            // ringkas buat card (bukan buat lightbox)
            $descCardRaw = mb_substr($descRaw, 0, 110);
            $descCard = e($descCardRaw) . (mb_strlen($descRaw) > 110 ? '...' : '');
          ?>
            <!-- CARD LEBAR: col-xl-4 (lebih enak daripada xl-3) -->
            <div class="col-xl-4 col-lg-4 col-md-6 portfolio-item isotope-item filter-<?= $kategoriSlug ?>">
              <div class="portfolio-content h-100">

                <div class="thumb-wrap">
                  <img src="<?= $foto ?>" class="img-fluid thumb-img" alt="<?= $nama ?>">
                </div>

                <div class="portfolio-info">
                  <h4><?= $nama ?></h4>

                  <?php if ($descRaw !== ''): ?>
                    <p><?= $descCard ?></p>
                  <?php else: ?>
                    <p class="muted">Deskripsi belum tersedia.</p>
                  <?php endif; ?>

                  <!-- tombol + simetris -->
                  <a href="<?= $foto ?>"
                     class="glightbox preview-link"
                     data-gallery="fasilitas-gallery"
                     data-title="<?= $nama ?>"
                     data-description="<?= $desc ?>">
                    <i class="bi bi-plus-lg"></i>
                  </a>
                </div>

              </div>
            </div>
          <?php endforeach; ?>

        <?php else: ?>
          <div class="col-12 text-center py-5">
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              Belum ada data fasilitas tersedia
            </div>
          </div>
        <?php endif; ?>

      </div>

    </div>

  </div>
</section>

<style>
/* =========================================
   FASILITAS PUBLIC – FINAL FIX (CLEAN)
   ========================================= */

/* SECTION spacing */
.portfolio-page{
  padding-bottom: 40px;
}

/* FILTER TABS (lebih modern & ga “formal”) */
.portfolio-page .portfolio-tabs{
  display:flex;
  justify-content:center;
  gap:10px;
  flex-wrap:wrap;
  margin:18px 0 34px;
  padding:0;
  list-style:none;
}

.portfolio-page .portfolio-tabs li{
  cursor:pointer;
  padding:9px 16px;
  border-radius:999px;
  font-size:14px;
  font-weight:700;
  color:#495057;
  background:#f1f3f5;
  border:1px solid #e9ecef;
  transition:all .18s ease;
  user-select:none;
}

.portfolio-page .portfolio-tabs li:hover{
  background:#e9ecef;
  transform:translateY(-1px);
}

.portfolio-page .portfolio-tabs li.filter-active{
  background:#0d6efd;
  border-color:#0d6efd;
  color:#fff;
  box-shadow:0 10px 20px rgba(13,110,253,.22);
}

/* CARD */
.portfolio-page .portfolio-content{
  background:#fff;
  border-radius:8px; /* jangan terlalu rounded */
  overflow:hidden;
  box-shadow:0 10px 26px rgba(0,0,0,.08);
  transition:transform .22s ease, box-shadow .22s ease;
}

.portfolio-page .portfolio-content:hover{
  transform:translateY(-6px);
  box-shadow:0 18px 40px rgba(0,0,0,.12);
}

/* FOTO konsisten */
.portfolio-page .thumb-wrap{
  width:100%;
  height:260px;            /* bikin lebih “panjang/lega” */
  overflow:hidden;
  background:#f2f2f2;
}

@media (max-width: 768px){
  .portfolio-page .thumb-wrap{ height:240px; }
}

.portfolio-page .thumb-img{
  width:100%;
  height:100%;
  object-fit:cover;        /* ini yang bikin konsisten */
  display:block;
  transition:transform .35s ease;
}

.portfolio-page .portfolio-content:hover .thumb-img{
  transform:scale(1.04);   /* kecil aja biar ga norak */
}

/* INFO di bawah foto (center) */
.portfolio-page .portfolio-info{
  position:relative;
  padding:14px 16px 18px;
  text-align:center;
}

.portfolio-page .portfolio-info h4{
  font-size:16px;
  font-weight:800;
  margin:0 0 6px;
  color:#212529;
}

/* DESKRIPSI: center & max 2 baris */
.portfolio-page .portfolio-info p{
  font-size:13px;
  color:#6c757d;
  line-height:1.45;
  margin:0 auto;
  max-width: 92%;
  display:-webkit-box;
  -webkit-line-clamp:2;
  -webkit-box-orient:vertical;
  overflow:hidden;
}

.portfolio-page .portfolio-info p.muted{
  opacity:.7;
}

/* tombol +: rapi & simetris di tengah bawah foto/info */
.portfolio-page .preview-link{
  position:absolute;
  left:50%;
  top:-20px;               /* nempel di batas foto & info */
  transform:translateX(-50%);
  width:44px;
  height:44px;
  border-radius:50%;
  background:#0d6efd;
  color:#fff;
  display:flex;
  align-items:center;
  justify-content:center;
  text-decoration:none;
  box-shadow:0 12px 24px rgba(13,110,253,.25);
  transition:transform .18s ease, background .18s ease;
}

.portfolio-page .preview-link i{
  font-size:18px;
  line-height:1;
}

.portfolio-page .preview-link:hover{
  background:#084298;
  transform:translateX(-50%) scale(1.06);
}

/* Biar klik area isotope rapi */
.portfolio-page .portfolio-item{
  padding-left: 10px;
  padding-right: 10px;
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>
