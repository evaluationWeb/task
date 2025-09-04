<?php

namespace App\Model;

class User
{
    //Attributs
    private int $idUser;
    private string $firstname;
    private string $lastname;
    private string $email;
    private string $password;
    private ?string $img;
    private ?array $grant;

    //constructeur
    public function __construct()
    {
        $this->grant = [];
    }

    //Getters et Setters
    public function getIdUser(): int
    {
        return $this->idUser;
    }

    public function setIdUser(int $id): void
    {
        $this->idUser = $id;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): void
    {
        $this->img = $img;
    }

    public function getGrant() : ?array {
        return $this->grant;
    } 
    public function addGrant(string $grant): self
    {
        $this->grant[] = $grant;
        return $this;
    }

    public function removeCategory(string $grant): self
    {
        unset($this->grant[array_search($grant, $this->grant)]);
        sort($this->grant);
        return $this;
    }

    //méthode pour hash et vérifier le password
    public function hashPassword(): void
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
    }

    public function passwordVerify(string $hash): bool
    {
        return password_verify($this->password, $hash);
    }
}
