<?php
class User
{
    protected $db;
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return [
            'id' => $row['id'],
            'username' => $row['username'],
            'password_hash' => $row['password_hash'],
            'role' => $row['role']
        ];
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT id, username, role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function all()
    {
        $stmt = $this->db->query("SELECT id, username, role, created_at FROM users ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function createUser($username, $password, $role = 'voter')
    {
        // basic uniqueness check
        $exists = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $exists->execute([$username]);
        if ($exists->fetchColumn() > 0) {
            throw new Exception('Username already exists.');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hash, $role]);
        return (int)$this->db->lastInsertId();
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}