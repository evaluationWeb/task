<?php

namespace App\Repository;

use App\Utils\Bdd;
use App\Model\User;

class UserRepository
{

    private readonly \PDO $connection;

    public function __construct()
    {
        $this->connection = (new Bdd())->connectBDD();
    }

    /**
     * Méthode pour ajouter un User en BDD
     * @param User Objet User
     * @return User retourne un Objet User qui correspond à l'enregistrement en BDD
     */
    public function saveUser(User $user): User
    {
        try {
            //Récupération des données de l'utilisateur
            $firstname = $user->getFirstname();
            $lastname = $user->getLastname();
            $email = $user->getEmail();
            $password = $user->getPassword();
            $img = $user->getImg();
            $request = "INSERT INTO users(firstname, lastname, email, password, img) VALUE (?,?,?,?,?)";

            //prépararation de la requête
            $req = $this->connection->prepare($request);
            //bind param
            $req->bindParam(1, $firstname, \PDO::PARAM_STR);
            $req->bindParam(2, $lastname, \PDO::PARAM_STR);
            $req->bindParam(3, $email, \PDO::PARAM_STR);
            $req->bindParam(4, $password, \PDO::PARAM_STR);
            $req->bindParam(5, $img, \PDO::PARAM_STR);
            //éxécution de la requête
            $req->execute();
            //récupération de l'id
            $id = $this->connection->lastInsertId('users');
            //set id et retourner l'utilisateur
            $user->setIdUser($id);
            //Retourne l'Objet User
            return $user;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Méthode qui vérifie si un compte existe en BDD
     * @return bool true si existe / false si n'existe pas
     */
    public function isUserByEmailExist(User $user): bool
    {
        try {
            //Récupération de la valeur de name (category)
            $email = $user->getEmail();
            //Ecrire la requête SQL
            $request = "SELECT u.id_users FROM users AS u WHERE u.email = ?";
            //préparer la requête
            $req = $this->connection->prepare($request);
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

    
}
