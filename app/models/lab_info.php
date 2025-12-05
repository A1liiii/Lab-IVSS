<?php

class LabInfoModel {

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // =========================================
    // AMBIL DATA
    // =========================================
    public function get()
    {
        $stmt = $this->db->prepare("SELECT * FROM lab_info WHERE lab_id = 1 LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null; // kembalikan null kalau tidak ada
    }
    // =========================================
    // UPDATE DATA
    // =========================================
   public function update($data)
{
    $sql = "UPDATE lab_info SET
        nama       = :nama,
        deskripsi  = :deskripsi,
        visi       = :visi,
        misi       = :misi,
        motto      = :motto,
        alamat     = :alamat,
        email      = :email,
        no_telp    = :no_telp,
        youtube    = :youtube,
        instagram  = :instagram,
        tiktok     = :tiktok
    WHERE lab_id = 1";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        'nama'       => $data['nama'] ?? null,
        'deskripsi'  => $data['deskripsi'] ?? null,
        'visi'       => $data['visi'] ?? null,
        'misi'       => $data['misi'] ?? null,
        'motto'      => $data['motto'] ?? null,
        'alamat'     => $data['alamat'] ?? null,
        'email'      => $data['email'] ?? null,
        'no_telp'    => !empty($data['no_telp']) ? $data['no_telp'] : null, // ❌ string kosong jadi null
        'youtube'    => $data['youtube'] ?? null,
        'instagram'  => $data['instagram'] ?? null,
        'tiktok'     => $data['tiktok'] ?? null
    ]);
}

}
