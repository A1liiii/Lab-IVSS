<?php

class Approval {

    private $conn;

    public function __construct() {
         $this->conn = Database::connect();
    }

    public function getPending() {
       $stmt = $this->conn->query("
        SELECT r.*, u.username AS approved_by_username
        FROM registrations r
        LEFT JOIN users u ON r.approved_by = u.user_id
        ORDER BY r.reg_id ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->conn->prepare("SELECT * FROM registrations WHERE reg_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   public function setApproved($id, $approvedByUserId) {
    $stmt = $this->conn->prepare(
        "UPDATE registrations 
         SET status='approved', approved_by = ?, approved_at = NOW() 
         WHERE reg_id=?"
    );
    return $stmt->execute([$approvedByUserId, $id]);
}

}
