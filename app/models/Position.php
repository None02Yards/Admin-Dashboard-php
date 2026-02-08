<?php
class Position
{
    protected $db;
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all()
    {
        $stmt = $this->db->query("SELECT * FROM positions ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM positions WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create($name)
    {
        $stmt = $this->db->prepare("INSERT INTO positions (name) VALUES (?)");
        $stmt->execute([$name]);
        return (int)$this->db->lastInsertId();
    }

    public function update($id, $name)
    {
        $stmt = $this->db->prepare("UPDATE positions SET name = ? WHERE id = ?");
        return $stmt->execute([$name, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM positions WHERE id = ?");
        return $stmt->execute([$id]);
    }
}