<?php

namespace App\Model;

use App\Utils\Bdd;
use stdClass;

class User 
{
    //Attributs
    private int $idUser;
    private string $firstname;
    private string $lastname;
    private string $email;
    private string $password;

    //Bdd
    private \PDO $connexion;

    //Constructeur
    public function __construct()
    {
        $this->connexion = (new Bdd())->connectBDD();
    }
    //Getters et Setters
    public function getIdUser() : int {
        return $this->idUser;
    }

    public function setIdUser(int $id) : void {
        $this->idUser = $id;
    }

    public function getFirstname() : string {
        return $this->firstname;
    }

    public function setFirstname(string $firstname) : void {
        $this->firstname = $firstname;
    }

    public function getLastname() : string {
        return $this->lastname;
    }

    public function setLastname(string $lastname) : void {
        $this->lastname = $lastname;
    }

    public function getEmail() : string {
        return $this->email;
    }

    public function setEmail(string $email) : void {
        $this->email = $email;
    }

    public function getPassword() : string {
        return $this->password;
    }

    public function setPassword(string $password) : void {
        $this->password = $password;
    }
    //méthode pour hash et vérifier le password
    public function hashPassword() : void 
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
    }

    public function passwordVerify(string $hash) : bool 
    {

        return password_verify($this->password, $hash);
    }
    //Méthodes (Requête SQL)

    public function saveUser() : User {
        try {
            //Récupération des données de l'utilisateur
            $firstname = $this->firstname;
            $lastname = $this->lastname;
            $email = $this->email;
            $password = $this->password;
            $request = "INSERT INTO users(firstname, lastname, email, password) VALUE (?,?,?,?)";
            //récupération de la connexion
            $req = $this->connexion;
            //prépararation de la requête
            $sqlRequest = $req->prepare($request);
            //bind paral
            $sqlRequest->bindParam(1, $firstname, \PDO::PARAM_STR);
            $sqlRequest->bindParam(2, $lastname, \PDO::PARAM_STR);
            $sqlRequest->bindParam(3, $email, \PDO::PARAM_STR);
            $sqlRequest->bindParam(4, $password, \PDO::PARAM_STR);
            //éxécution de la requête
            $sqlRequest->execute();
            //récupération de l'id
            $id = $req->lastInsertId('users');
            //set id et retourner l'utilisateur
            $this->idUser = $id;
            return $this;
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
