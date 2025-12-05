<style>
    /* WRAPPER UTAMA FULL LEBAR */
    .lab-wrapper {
        width: 100%;
        max-width: 100%;
        background: #ffffff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-top: 20px;
    }

    .lab-title {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 25px;
        text-align: center;
    }

    /* LABEL */
    .lab-label {
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
    }

    /* TEXTAREA BESAR (VISI, MISI, DESKRIPSI, ALAMAT) */
    .lab-big-box {
        width: 100%;
        min-height: 150px;
        resize: vertical;
        padding: 12px;
        font-size: 15px;
        border-radius: 10px;
        border: 1px solid #ccc;
        margin-bottom: 20px;
    }

    /* GRID 2 KOLOM */
    .lab-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    /* INPUT NORMAL */
    .lab-input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
    }

    /* RESPONSIVE UNTUK HP */
    @media (max-width: 768px) {
        .lab-grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>



<div class="lab-wrapper">

    <h3 class="lab-title">Edit Lab Info</h3>

    <form action="index.php?page=admin-labinfo-update" method="POST">

        <!-- NAMA LAB -->
        <div>
            <label class="lab-label">Nama Lab</label>
            <input type="text" name="nama" class="lab-input"
                   value="<?= htmlspecialchars($labinfo['nama'] ?? '') ?>">
        </div>

        <!-- VISI -->
        <div>
            <label class="lab-label">Visi</label>
            <textarea name="visi" class="lab-big-box"><?= htmlspecialchars($labinfo['visi'] ?? '') ?></textarea>
        </div>

        <!-- MISI -->
        <div>
            <label class="lab-label">Misi</label>
            <textarea name="misi" class="lab-big-box"><?= htmlspecialchars($labinfo['misi'] ?? '') ?></textarea>
        </div>

        <!-- DESKRIPSI -->
        <div>
            <label class="lab-label">Deskripsi</label>
            <textarea name="deskripsi" class="lab-big-box"><?= htmlspecialchars($labinfo['deskripsi'] ?? '') ?></textarea>
        </div>

        <!-- ALAMAT + EMAIL -->
        <div class="lab-grid-2">
            <div>
                <label class="lab-label">Alamat</label>
                <textarea name="alamat" class="lab-big-box" style="min-height:120px;"><?= htmlspecialchars($labinfo['alamat'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="lab-label">Email</label>
                <input type="email" name="email" class="lab-input"
                       value="<?= htmlspecialchars($labinfo['email'] ?? '') ?>">
            </div>
        </div>

        <!-- TELEPON + YOUTUBE -->
        <div class="lab-grid-2">
            <div>
                <label class="lab-label">No. Telepon</label>
                <input type="text" name="no_telp" class="lab-input"
                       value="<?= htmlspecialchars($labinfo['no_telp'] ?? '') ?>">
            </div>

            <div>
                <label class="lab-label">YouTube</label>
                <input type="text" name="youtube" class="lab-input"
                       value="<?= htmlspecialchars($labinfo['youtube'] ?? '') ?>">
            </div>
        </div>

        <!-- IG + TIKTOK -->
        <div class="lab-grid-2">
            <div>
                <label class="lab-label">Instagram</label>
                <input type="text" name="instagram" class="lab-input"
                       value="<?= htmlspecialchars($labinfo['instagram'] ?? '') ?>">
            </div>

            <div>
                <label class="lab-label">TikTok</label>
                <input type="text" name="tiktok" class="lab-input"
                       value="<?= htmlspecialchars($labinfo['tiktok'] ?? '') ?>">
            </div>
        </div>

        <!-- BUTTON -->
        <div style="display: flex; gap: 15px; margin-top: 25px;">

            <a href="index.php?page=admin-dashboard"
                style="
                    flex:1;
                    padding: 12px;
                    font-size: 17px;
                    border-radius: 10px;
                    background: #6c757d;
                    color: white;
                    text-align:center;
                    text-decoration:none;
                    font-weight: 500;">
                ← Kembali
            </a>

            <button type="submit"
                style="
                    flex:1;
                    padding: 12px;
                    font-size: 17px;
                    border: none;
                    border-radius: 10px;
                    background: #0d6efd;
                    color: white;
                    cursor: pointer;
                    font-weight: 500;
                ">
                Simpan
            </button>
        </div>

    </form>
</div>
