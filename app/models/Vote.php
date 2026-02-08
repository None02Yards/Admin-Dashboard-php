<?php
class Vote
{
    protected $db;
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}