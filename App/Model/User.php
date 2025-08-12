<?php

namespace App\Model;

use App\Utils\Bdd;


class User 
{
    //Attributs
    private int $idUser;
    private string $firstname;
    private string $lastname;
    private string $email;
    private string $password;
    private ?string $img;

    //Bdd
    private \PDO $connexion;

    //Constructeur
    public function __construct()
    {
        $this->connexion = (new Bdd())->connectBDD();
        $this->img = "profil.png";
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

    public function getImg() : ?string {
        return $this->img;
    }

    public function setImg(?string $img) :void {
        $this->img = $img;
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

            //prépararation de la requête
            $req = $this->connexion->prepare($request);
            //bind param
            $req->bindParam(1, $firstname, \PDO::PARAM_STR);
            $req->bindParam(2, $lastname, \PDO::PARAM_STR);
            $req->bindParam(3, $email, \PDO::PARAM_STR);
            $req->bindParam(4, $password, \PDO::PARAM_STR);
            //éxécution de la requête
            $req->execute();
            //récupération de l'id
            $id = $this->connexion->lastInsertId('users');
            //set id et retourner l'utilisateur
            $this->idUser = $id;
            return $this;
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    public function isUserByEmailExist(): bool
    {
        try {
            //Récupération de la valeur de name (category)
            $email = $this->email;
            //Ecrire la requête SQL
            $request = "SELECT u.id_users FROM users AS u WHERE u.email = ?";
            //préparer la requête
            $req = $this->connexion->prepare($request);
            //assigner le paramètre
            $req->bindParam(1, $email, \PDO::PARAM_STR);
            //exécuter la requête
            $req->execute();
            //récupérer le resultat
            $data = $req->fetch(\PDO::FETCH_ASSOC);
            //Test si l'enrgistrement est vide
            if (empty($data)) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    public function findUserByEmail(): User
    {
        try {
            //Récupération de la valeur de name (category)
            $email = $this->email;
            //Ecrire la requête SQL
            $request = "SELECT u.id_users AS idUser, u.firstname, u.lastname, u.password FROM users AS u WHERE u.email = ?";
            //préparer la requête
            $req = $this->connexion->prepare($request);
            //assigner le paramètre
            $req->bindParam(1, $email, \PDO::PARAM_STR);
            //exécuter la requête
            $req->execute();
            
            $req->setFetchMode(\PDO::FETCH_CLASS, User::class);
            //récupérer le resultat
            return $req->fetch();
            
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
