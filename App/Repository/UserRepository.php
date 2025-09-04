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
            $grants = $user->getGrant();
            $grants = implode(",",$grants);
            
            //Requête SQL
            $request = "INSERT INTO users(firstname, lastname, email, password, img, grants) VALUE (?,?,?,?,?,?)";

            //prépararation de la requête
            $req = $this->connection->prepare($request);

            //bind param
            $req->bindParam(1, $firstname, \PDO::PARAM_STR);
            $req->bindParam(2, $lastname, \PDO::PARAM_STR);
            $req->bindParam(3, $email, \PDO::PARAM_STR);
            $req->bindParam(4, $password, \PDO::PARAM_STR);
            $req->bindParam(5, $img, \PDO::PARAM_STR);
            $req->bindParam(6, $grants, \PDO::PARAM_STR);
            
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
     * @param User Objet User
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

    /**
     * Méthode qui retourne un objet User ou null
     * @param User Objet User
     * @return User retourne on objet User depuis l'email assigné à l'objet
     */
    public function findUserByEmail(User $user): User
    {
        try {
            //Récupération de la valeur de name (category)
            $email = $user->getEmail();
            //Ecrire la requête SQL
            $request = "SELECT u.id_users AS idUser, u.firstname, u.lastname, u.password , u.img, u.email, u.grants FROM users AS u WHERE u.email = ?";
            //préparer la requête
            $req = $this->connection->prepare($request);
            //assigner le paramètre
            $req->bindParam(1, $email, \PDO::PARAM_STR);
            //exécuter la requête
            $req->execute();

            $req->setFetchMode(\PDO::FETCH_CLASS, User::class);
            //récupérer le resultat
            $user = $req->fetch();

            //hydratation des droits grants
            return $this->hydrateGrantsUser($user);
            //return $user;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

        /**
     * Méthode qui met à jour le mot de passe du compte en BDD
     * @param User Objet User
     * @return void
     */
    public function updatePassword(User $user)
    {
        try {
            $email = $user->getEmail();
            $password = $user->getPassword();
            $request = "UPDATE users SET password = ? WHERE email = ?";
            $req = $this->connection->prepare($request);
            $req->bindParam(1, $password, \PDO::PARAM_STR);
            $req->bindParam(2, $email, \PDO::PARAM_STR);
            $req->execute();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Méthode qui met à jour l'image de profil
     * @param User Objet User
     * @return void
     */
    public function updateImage(User $user)
    {
        try {
            $email = $user->getEmail();
            $img = $user->getImg();
            $request = "UPDATE users SET img = ? WHERE email = ?";
            $req = $this->connection->prepare($request);
            $req->bindParam(1, $img, \PDO::PARAM_STR);
            $req->bindParam(2, $email, \PDO::PARAM_STR);
            $req->execute();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Méthode qui met à jour les informations du profil
     * @param User $user Objet User avec les paramètres à mettre à jour
     * @param string $oldEmail ancien email pour trouver le compte à update
     * @return void
     */
    public function updateInformation(User $user, string $oldEmail)
    {
        try {
            $email = $user->getEmail();
            $firstname = $user->getFirstname();
            $lastname = $user->getLastname();
            $request = "UPDATE users SET firstname = ?, lastname = ?, email = ? WHERE email = ?";
            $req = $this->connection->prepare($request);
            $req->bindParam(1, $firstname, \PDO::PARAM_STR);
            $req->bindParam(2, $lastname, \PDO::PARAM_STR);
            $req->bindParam(3, $email, \PDO::PARAM_STR);
            $req->bindParam(4, $oldEmail, \PDO::PARAM_STR);
            $req->execute();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Méthode qui vérifie si un compte existe en BDD 
     * par un email hash en MD5
     * @param User $user Objet User
     * @return bool true si existe / false si n'existe pas
     */
    public function isUserByHashEmailExist(User $user) : bool {
        try {
            //Récupération de la valeur de name (category)
            $email = $user->getEmail();
            //Ecrire la requête SQL
            $request = "SELECT u.id_users FROM users AS u WHERE md5(u.email) = ?";
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

    /**
     * Méthode qui remplace le mot de passe du compte dont le hash de l'email en MD5 est correct
     * @param User $user Objet User
     * @return void
     */
    public function updateForgotPassword(User $user)
    {
        try {
            $email = $user->getEmail();
            $password = $user->getPassword();
            $request = "UPDATE users SET password = ? WHERE md5(email) = ?";
            $req = $this->connection->prepare($request);
            $req->bindParam(1, $password, \PDO::PARAM_STR);
            $req->bindParam(2, $email, \PDO::PARAM_STR);
            $req->execute();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Méthode qui hydrate les droits d'un User
     * @param objet User $user
     * @return User 
     */
    private function hydrateGrantsUser(object $user) : User {
        //test si les droits ne sont pas vides
        if (!empty($user->grants)) {
            //hydratation des droits grants
            $grants = $user->grants;
            $grants = explode(",",$grants);
            foreach($grants as $grant) {
                $user->addGrant($grant);
            } 
        }
        return $user;
    }
}
