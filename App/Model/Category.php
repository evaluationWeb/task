<?php

namespace App\Model;

use App\Utils\Bdd;
use App\Model\CategoryException;

class Category
{
    //Attributs
    private int $idCategory;
    private string $name;

    //Bdd (récupérer la connexion)
    private \PDO $connexion;

    public function __construct()
    {
        //Injection de dépendance
        $this->connexion = (new Bdd())->connectBDD();
    }

    //Getters et Setters
    public function getIdCategory(): int
    {
        return $this->idCategory;
    }

    public function setIdCategory(int $id): self
    {
        $this->idCategory = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
