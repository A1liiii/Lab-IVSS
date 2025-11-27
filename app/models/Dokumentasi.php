<?php 
require_once __DIR__ . '/../Config/database.php';

class Dokumentasi {

    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // Ambil semua data dokumentasi
    public function all() {
        $sql = "SELECT documentation_id, file_path, type_file, caption, judul_kegiatan, deskripsi_kegiatan, tanggal_kegiatan, jenis_kegiatan
                FROM act_documentation
                ORDER BY documentation_id DESC";
        $stmt = $this->db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Alias untuk view: tombol edit/hapus pakai $d['id']
        foreach ($data as &$d) {
            $d['id'] = $d['documentation_id'];
        }

        return $data;
    }

    // Ambil 1 data berdasarkan ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM act_documentation WHERE documentation_id = :id");
        $stmt->execute([':id' => $id]);
        $d = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($d) $d['id'] = $d['documentation_id'];
        return $d;
    }

    // Tambah dokumentasi baru
    public function insert($file_path, $type_file, $caption, $judul, $deskripsi, $tgl, $jenis, $user_id) {
        $sql = "INSERT INTO act_documentation 
        (file_path, type_file, caption, judul_kegiatan, deskripsi_kegiatan, tanggal_kegiatan, jenis_kegiatan, uploaded_by)
        VALUES
        (:file_path, :type_file, :caption, :judul, :deskripsi, :tgl, :jenis, :user_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':file_path' => $file_path,
            ':type_file' => $type_file,
            ':caption'   => $caption,
            ':judul'     => $judul,
            ':deskripsi' => $deskripsi,
            ':tgl'       => $tgl,
            ':jenis'     => $jenis,
            ':user_id'   => $user_id
        ]);
    }

    // Update dokumentasi
    public function update($id, $file_path, $type_file, $caption, $judul, $deskripsi, $tgl, $jenis) {
        $sql = "UPDATE act_documentation SET
                file_path = :file_path,
                type_file = :type_file,
                caption = :caption,
                judul_kegiatan = :judul,
                deskripsi_kegiatan = :deskripsi,
                tanggal_kegiatan = :tgl,
                jenis_kegiatan = :jenis
                WHERE documentation_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id'        => $id,
            ':file_path' => $file_path,
            ':type_file' => $type_file,
            ':caption'   => $caption,
            ':judul'     => $judul,
            ':deskripsi' => $deskripsi,
            ':tgl'       => $tgl,
            ':jenis'     => $jenis
        ]);
    }

    // Hapus dokumentasi
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM act_documentation WHERE documentation_id = :id");
        $stmt->execute([':id' => $id]);
    }

    // Hitung total dokumentasi
    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM act_documentation";
        $res = $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);
        return (int)$res['total'];
    }
}
