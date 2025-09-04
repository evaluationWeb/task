<?php

namespace App\Model;



class Category
{
    //Attributs
    private int $idCategory;
    private string $name;

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
