<?php
require_once __DIR__ . '/../Config/database.php';


class Dosen {

    public function getAvailableDosen() {
        $db = Database::Connect();
        return $db->query("SELECT * FROM dosen WHERE user_id IS NULL")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setUserId($nip, $user_id) {
        $db = Database::Connect();
        $stmt = $db->prepare("UPDATE dosen SET user_id=? WHERE nip=?");
        return $stmt->execute([$user_id, $nip]);
    }
}
