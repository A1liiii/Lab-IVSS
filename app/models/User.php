<?php 

require_once __DIR__ . '/../Config/database.php';

class User {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM users";
        $stmt = $this->conn->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function countPending() {
        $sql = "SELECT COUNT(*) AS total FROM registrations WHERE status = 'pending'";
        $stmt = $this->conn->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
    public function create($data) {
    // Cek nim
    $stmtCheck = $this->conn->prepare("SELECT user_id FROM users WHERE nim = :nim");
    $stmtCheck->execute([':nim' => $data['nim']]);
    $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Nim sudah ada, kembalikan user_id yang ada
        return $existing['user_id'];
    }

    // Insert baru
    $sql = "INSERT INTO users (nim, username, password) 
            VALUES (:nim, :username, :password)
            RETURNING user_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
        ':nim'      => $data['nim'],
        ':username' => $data['username'],
        ':password' => $data['password']
    ]);
    return $stmt->fetchColumn(); // kembalikan user_id
}

// Tambahkan method assignRole
public function assignRole($user_id, $role_id) {
    // cek dulu apakah sudah ada
    $stmtCheck = $this->conn->prepare("SELECT 1 FROM user_roles WHERE user_id = :user_id AND role_id = :role_id");
    $stmtCheck->execute([
        ':user_id' => $user_id,
        ':role_id' => $role_id
    ]);
    
    if ($stmtCheck->fetch()) {
        // sudah ada, tidak perlu insert
        return false;
    }

    // insert baru
    $sql = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([
        ':user_id' => $user_id,
        ':role_id' => $role_id
    ]);
}


}
