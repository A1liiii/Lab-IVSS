<?php
class Auth {

    // urutan role dari paling tinggi sampai paling rendah
    private static $roleHierarchy = [
        'ketua_lab' => 4,
        'admin'     => 3,
        'operator'  => 2,
        'mahasiswa' => 1
    ];

    public static function check() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "login");
            exit;
        }
    }

    // cek role minimum
    public static function authorize($allowedRoles = []) {

        self::check();  

        $currentRole = $_SESSION['role'];

        // kalau user punya role tertinggi → boleh akses semua yang di bawahnya
        $currentPower = self::$roleHierarchy[$currentRole];
        
        foreach ($allowedRoles as $role) {
            if ($currentPower >= self::$roleHierarchy[$role]) {
                return true; 
            }
        }

        // jika akses ditolak
        die("Akses ditolak.");
    }
}
