<?php
class Candidate
{
    protected $db;
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function allByPosition($position_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM candidates WHERE position_id = ? ORDER BY id ASC");
        $stmt->execute([$position_id]);
        return $stmt->fetchAll();
    }

    public function all()
    {
        $stmt = $this->db->query("SELECT * FROM candidates ORDER BY position_id, id");
        return $stmt->fetchAll();
    }

    public function allWithPosition()
    {
        $stmt = $this->db->query("SELECT c.*, p.name as position_name FROM candidates c JOIN positions p ON c.position_id = p.id ORDER BY p.id, c.id");
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM candidates WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create($position_id, $name)
    {
        $stmt = $this->db->prepare("INSERT INTO candidates (position_id, name) VALUES (?, ?)");
        $stmt->execute([$position_id, $name]);
        return (int)$this->db->lastInsertId();
    }

    public function update($id, $position_id, $name)
    {
        $stmt = $this->db->prepare("UPDATE candidates SET position_id = ?, name = ? WHERE id = ?");
        return $stmt->execute([$position_id, $name, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM candidates WHERE id = ?");
        return $stmt->execute([$id]);
    }
}