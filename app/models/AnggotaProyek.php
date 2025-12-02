<?php

class AnggotaProyek
{
    private $db;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
    }

    public function insertBatch($proyek_id, $anggota)
    {
        $sql = "INSERT INTO anggota_proyek (user_id, proyek_id, role)
                VALUES (?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        foreach ($anggota as $a) {
            $stmt->execute([
                $a['user_id'] ?: null,  // support user_id NULL
                $proyek_id,
                $a['role']
            ]);
        }
    }

    public function deleteByProyek($proyek_id)
    {
        $sql = "DELETE FROM anggota_proyek WHERE proyek_id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$proyek_id]);
    }
}
