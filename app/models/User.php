<?php 

require_once __DIR__ . '/../Config/database.php';

class User {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    // =======================
    // COUNTING
    // =======================
    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM users";
        return $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function countPending() {
        $sql = "SELECT COUNT(*) AS total FROM registrations WHERE status = 'pending'";
        return $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // =======================
    // CREATE USER
    // =======================
    public function create($data) {

    $sql = "INSERT INTO users (nip, nim, username, password)
            VALUES (:nip, :nim, :username, :password)
            RETURNING user_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
        ':nip'      => $data['nip'] ?? null,
        ':nim'      => $data['nim'] ?? null,
        ':username' => $data['username'],
        ':password' => $data['password']
    ]);

    $user_id = $stmt->fetchColumn();

    // ------ UPDATE mahasiswa.user_id jika nim cocok ------
    if (!empty($data['nim'])) {
        $stmt2 = $this->conn->prepare("
            UPDATE mahasiswa 
            SET user_id = :uid 
            WHERE nim = :nim
        ");
        $stmt2->execute([
            ':uid' => $user_id,
            ':nim' => $data['nim']
        ]);
    }

    // ------ UPDATE dosen.user_id jika nip cocok ------
    if (!empty($data['nip'])) {
        $stmt3 = $this->conn->prepare("
            UPDATE dosen 
            SET user_id = :uid 
            WHERE nip = :nip
        ");
        $stmt3->execute([
            ':uid' => $user_id,
            ':nip' => $data['nip']
        ]);
    }

    return $user_id;
}

    // =======================
    // UPDATE USER
    // =======================
    public function updateUser($data) {
    // 1. Update username di tabel users
    $sql = "UPDATE users SET username = :username WHERE user_id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
        ':username' => $data['username'],
        ':id'       => $data['user_id']
    ]);

    // 2. Ambil identitas user (nim / nip)
    $stmtUser = $this->conn->prepare("SELECT nim, nip FROM users WHERE user_id = :id");
    $stmtUser->execute([':id' => $data['user_id']]);
    $u = $stmtUser->fetch(PDO::FETCH_ASSOC);

    // 3. Jika mahasiswa → update nama di tabel mahasiswa
    if (!empty($u['nim'])) {
        $stmt2 = $this->conn->prepare("
            UPDATE mahasiswa 
            SET nama = :username 
            WHERE nim = :nim
        ");
        $stmt2->execute([
            ':username' => $data['username'],
            ':nim'      => $u['nim']
        ]);
    }

    // 4. Jika dosen → update nama di tabel dosen
    if (!empty($u['nip'])) {
        $stmt3 = $this->conn->prepare("
            UPDATE dosen 
            SET nama = :username 
            WHERE nip = :nip
        ");
        $stmt3->execute([
            ':username' => $data['username'],
            ':nip'      => $u['nip']
        ]);
    }

    // 5. Update role seperti biasa
    $stmtCheck = $this->conn->prepare("SELECT 1 FROM user_roles WHERE user_id = :id");
    $stmtCheck->execute([':id' => $data['user_id']]);

    if ($stmtCheck->fetchColumn()) {
        $stmt2 = $this->conn->prepare("UPDATE user_roles SET role_id = :r WHERE user_id = :id");
        $stmt2->execute([':r'=>$data['role_id'], ':id'=>$data['user_id']]);
    } else {
        $stmt2 = $this->conn->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (:id, :r)");
        $stmt2->execute([':id'=>$data['user_id'], ':r'=>$data['role_id']]);
    }

    return true;
}
    // =======================
    // DELETE USER
    // =======================
    public function delete($id) {

    // 1. Hapus relasi role
    $delRole = $this->conn->prepare("DELETE FROM user_roles WHERE user_id = :id");
    $delRole->execute([':id' => $id]);

    // 2. Lepas user dari mahasiswa
    $stmt1 = $this->conn->prepare("UPDATE mahasiswa SET user_id = NULL WHERE user_id = :id");
    $stmt1->execute([':id' => $id]);

    // 3. Lepas user dari dosen
    $stmt2 = $this->conn->prepare("UPDATE dosen SET user_id = NULL WHERE user_id = :id");
    $stmt2->execute([':id' => $id]);

    // 4. Hapus user
    $delUser = $this->conn->prepare("DELETE FROM users WHERE user_id = :id");
    $delUser->execute([':id' => $id]);

    return true;
}


    // =======================
    // VALIDASI NIM/NIP
    // =======================
    public function nimExists($nim) {
        // cek mahasiswa
        $stmt = $this->conn->prepare("SELECT 1 FROM mahasiswa WHERE nim = :nim LIMIT 1");
        $stmt->execute([':nim' => $nim]);
        if ($stmt->fetchColumn()) return true;

        // cek dosen
        $stmt2 = $this->conn->prepare("SELECT 1 FROM dosen WHERE nip = :nim LIMIT 1");
        $stmt2->execute([':nim' => $nim]);
        if ($stmt2->fetchColumn()) return true;

        return false;
    }

           public function getAll() {
    $sql = "SELECT 
                u.user_id,
                u.username,
                u.nim,
                u.nip,
                ro.role_name,
                COALESCE(u.nip, u.nim) AS identitas
            FROM users u
            LEFT JOIN user_roles r ON u.user_id = r.user_id
            LEFT JOIN roles ro ON ro.role_id = r.role_id
            ORDER BY u.user_id DESC";

    return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}
        public function find($id) {
            $sql = "SELECT u.*, r.role_id
                    FROM users u
                    LEFT JOIN user_roles r ON u.user_id = r.user_id
                    WHERE u.user_id = :id
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function assignRole($user_id, $role_id) {
    $sql = "INSERT INTO user_roles (user_id, role_id)
            VALUES (:user_id, :role_id)";
    
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([
        ':user_id' => $user_id,
        ':role_id' => $role_id
    ]);
}



}
