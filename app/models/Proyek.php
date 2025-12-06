<?php
require_once __DIR__ . '/../Config/database.php';

class Proyek
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM proyek";
        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getAll()
    {
        $sql = "SELECT p.*,
                COUNT(a.id) AS jumlah_anggota
                FROM proyek p
                LEFT JOIN anggota_proyek a ON a.proyek_id = p.proyek_id
                GROUP BY p.proyek_id
                ORDER BY p.proyek_id DESC";

        $stmt = $this->db->query($sql);

        // JANGAN langsung return
        $proyek = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tambahkan data anggota
        foreach ($proyek as &$p) {
            $p['anggota'] = $this->getAnggota($p['proyek_id']);
        }

        return $proyek;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM proyek WHERE proyek_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** INSERT DENGAN RETURNING — FIX UTAMA */
    public function insert($data)
    {
        $sql = "INSERT INTO proyek (judul, deskripsi, tanggal_mulai, tanggal_selesai, status)
                VALUES (:judul, :deskripsi, :mulai, :selesai, :status)
                RETURNING proyek_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':judul'   => $data['judul'],
            ':deskripsi' => $data['deskripsi'],
            ':mulai'   => $data['tanggal_mulai'],
            ':selesai' => $data['tanggal_selesai'],
            ':status'  => $data['status']
        ]);

        return $this->db->lastInsertId(); // <-- ID terbaru
    }

    public function update($id, $data)
    {
        $sql = "UPDATE proyek SET 
                judul=:judul,
                deskripsi=:deskripsi,
                tanggal_mulai=:mulai,
                tanggal_selesai=:selesai,
                status=:status
                WHERE proyek_id=:id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':judul'   => $data['judul'],
            ':deskripsi' => $data['deskripsi'],
            ':mulai'   => $data['tanggal_mulai'],
            ':selesai' => $data['tanggal_selesai'],
            ':status'  => $data['status'],
            ':id'      => $id
        ]);
    }

    public function delete($id)
    {
        // hapus anggota dulu
        $sql1 = "DELETE FROM anggota_proyek WHERE proyek_id = ?";
        $stmt1 = $this->db->prepare($sql1);
        $stmt1->execute([$id]);

        // lalu hapus proyek
        $sql2 = "DELETE FROM proyek WHERE proyek_id = ?";
        $stmt2 = $this->db->prepare($sql2);
        return $stmt2->execute([$id]);
    }


    public function getAnggota($proyek_id)
    {
        $sql = "SELECT a.*, 
                u.username AS nama,
                u.nip,
                u.nim
            FROM anggota_proyek a
            LEFT JOIN users u ON a.user_id = u.user_id
            WHERE a.proyek_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$proyek_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteAnggota($proyek_id)
    {
        $sql = "DELETE FROM anggota_proyek WHERE proyek_id=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$proyek_id]);
    }

    public function insertAnggota($data)
    {
        $sql = "INSERT INTO anggota_proyek (proyek_id, user_id, role)
                VALUES (:pid, :uid, :role)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':pid' => $data['proyek_id'],
            ':uid' => $data['user_id'],
            ':role'=> $data['role']
        ]);
    }

    // Ambil proyek/riset berdasarkan id mahasiswa
    public function getByMahasiswa($mhs_id) {
        if (!$mhs_id) return [];

        $sql = "SELECT * FROM proyek WHERE mahasiswa_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$mhs_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
