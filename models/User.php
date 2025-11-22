<?php
class User {
    private $db;
    public function __construct($db){
        // $db is Database instance
        $this->db = $db;
    }

    // Find admin user by username
    public function findAdminByUsername($username){
        return $this->db->queryOne("SELECT * FROM admin_users WHERE username = ?", [$username]);
    }

    // Static convenience wrapper
    public static function findAdmin($username){
        require_once __DIR__ . '/../core/database_new.php';
        $db = new Database();
        $u = new self($db);
        return $u->findAdminByUsername($username);
    }
}
?>
