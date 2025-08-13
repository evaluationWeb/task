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

    //Méthodes
    /**
     * Méthode qui ajoute un enregistrement en BDD
     * requête de MAJ insert
     * @var $name sera récupéré par l'objet 
     * @return void
     */
    public function saveCategory(): Category
    {
        try {
            //Récupération de la valeur de name (category)
            $name = $this->name;
            //Stocker la requête dans une variable
            $request = "INSERT INTO category(name) VALUES (?)";
            //1 préparer la requête
            $req = $this->connexion->prepare($request);
            //2 Bind les paramètres
            $req->bindParam(1, $name, \PDO::PARAM_STR);
            //3 executer la requête
            $req->execute();
            return $this;
            //Capture des erreurs 
        } catch (\Exception $e) {
            throw new CategoryException($e->getMessage());
        }
    }

    /**
     * Méthode qui retourne toutes les categories de la BDD
     * @return array Category tableau d'objet Category
     */
    public function findAllCategory(): array
    {
        try {
            $request = "SELECT c.id_category AS idCategory , c.name FROM category AS c";
            $req = $this->connexion->prepare($request);
            $req->execute();
            return $req->fetchAll(\PDO::FETCH_CLASS, Category::class);
        } catch (\Exception $e) {
            throw new CategoryException($e->getMessage());
        }
    }

    /**
     * Méthode qui retourne true si la category existe en BDD
     * @return bool true si existe / false si n'existe pas
     */
    public function isCategoryByNameExist(): bool
    {
        try {
            //Récupération de la valeur de name (category)
            $name = $this->name;
            //Ecrire la requête SQL
            $request = "SELECT c.id_category FROM category AS c WHERE c.name = ?";
            //préparer la requête
            $req = $this->connexion->prepare($request);
            //assigner le paramètre
            $req->bindParam(1, $name, \PDO::PARAM_STR);
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
     * Méthodes qui supprime une category en BDD
     * @param int $id id de la catgeory à supprimer
     */
    public function deleteCategory(int $id): void
    {
        try {
            $requestAsso = "DELETE FROM task_category WHERE id_category = ?";
            $req = $this->connexion->prepare($requestAsso);
            $req->bindParam(1, $id, \PDO::PARAM_INT);
            $req->execute();
            $request = "DELETE FROM category WHERE id_category = ?";
            $req2 = $this->connexion->prepare($request);
            $req2->bindParam(1, $id, \PDO::PARAM_INT);
            $req2->execute();
        } catch (\Exception $e) {
            throw new CategoryException($e->getMessage());
        }
    }
    /**
     * Méthode qui retourne une Category depuis son ID
     * @param int $id ID de la category en BDD
     * @return Category | stdClass | null retourne une Category si elle existe
     */
    public function findCategory(int $id): null | Category
    {
        try {
            $request = "SELECT c.id_category AS idCategory, c.name FROM category AS c WHERE c.id_category = ?";
            //préparer la requête
            $req = $this->connexion->prepare($request);
            //assigner le paramètre
            $req->bindParam(1, $id, \PDO::PARAM_INT);
            //exécuter la requête
            $req->execute();
            $req->setFetchMode(\PDO::FETCH_CLASS, Category::class);
            //récupérer le resultat
            return $req->fetch();
        } catch (\Exception $e) {
            throw new CategoryException($e->getMessage());
        }
    }

    /**
     * Méthode qui retourne une Category depuis son ID
     * @param int $id ID de la category en BDD
     * @return Category | stdClass | null retourne une Category si elle existe
     */
    public function findCategoryByName(string $name): null | Category
    {
        try {
            $request = "SELECT c.id_category AS idCategory, c.name FROM category AS c WHERE c.name = ?";
            //préparer la requête
            $req = $this->connexion->prepare($request);
            //assigner le paramètre
            $req->bindParam(1, $name, \PDO::PARAM_STR);
            //exécuter la requête
            $req->execute();
            $req->setFetchMode(\PDO::FETCH_CLASS, Category::class);
            //récupérer le resultat
            return $req->fetch();
        } catch (\Exception $e) {
            throw new CategoryException($e->getMessage());
        }
    }
    /**
     * Méthode qui ajoute un enregistrement en BDD
     * requête de MAJ insert
     * @var $name sera récupéré par l'objet 
     * @return void
     */
    public function updateCategory(int $id): void {
        try {
            //Récupération de la valeur de name (category)
            $name = $this->name;
            //Stocker la requête dans une variable
            $request = "UPDATE category set name = ? WHERE id_category = ?";
            //1 préparer la requête
            $req = $this->connexion->prepare($request);
            //2 Bind les paramètres
            $req->bindParam(1, $name, \PDO::PARAM_STR);
            $req->bindParam(2, $id, \PDO::PARAM_INT);
            //3 executer la requête
            $req->execute();
         
            //Capture des erreurs 
        } catch (\Exception $e) {
            throw new CategoryException($e->getMessage());
        }
    }
}
