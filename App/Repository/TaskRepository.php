<?php

namespace App\Repository;

use App\Utils\Bdd;
use App\Model\Task;

class TaskRepository 
{
    private readonly \PDO $connection;

    public function __construct()
    {
        $this->connection = (new Bdd())->connectBDD();
    }
    
}