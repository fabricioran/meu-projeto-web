<?php

namespace Core;

use Config\Database;

abstract class Model
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }
}