<?php

namespace App\Repository;

use App\Utils\Bdd;
use App\Model\Category;
use App\Model\CategoryException;

class CategoryRepository
{
    private readonly \PDO $connection;

    public function __construct()
    {
        $this->connection = (new Bdd())->connectBDD();
    }

    //Méthodes
    /**
     * Méthode qui ajoute un enregistrement en BDD
     * requête de MAJ insert
     * @param Category $category en entrée (avec le name) 
     * @return Category retourne la category avec son ID
     */
    public function saveCategory(Category $category): Category
    {
        try {
            //Récupération de la valeur de name (category)
            $name = $category->getName();
            //Ecrire la requête SQL
            $request = "INSERT INTO category(name) VALUES (?)";
            //1 préparer la requête
            $req = $this->connection->prepare($request);
            //2 Bind les paramètres
            $req->bindParam(1, $name, \PDO::PARAM_STR);
            //3 executer la requête
            $req->execute();
            //4 récupération de l'ID
            $id = $this->connection->lastInsertId('category');
            $category->setIdCategory($id);
            //Retour de la category
            return $category;
            //Capture des erreurs 
        } catch (\Exception $e) {
            throw new CategoryException($e->getMessage());
        }
    }
    /**
     * Méthode qui retourne toutes les categories de la BDD
     * @return array[Category] retourne un tableau de Category
     */
    public function findAllCategory(): array
    {
        try {
            //Ecrire la requête SQL
            $request = "SELECT c.id_category AS idCategory , c.name FROM category AS c";
            $req = $this->connection->prepare($request);
            $req->execute();
            return $req->fetchAll(\PDO::FETCH_CLASS, Category::class);
        } catch (\Exception $e) {
            throw new CategoryException($e->getMessage());
        }
    }

    /**
     * Méthode qui retourne true si la category existe en BDD
     * @param Category $category 
     * @return bool true si existe / false si n'existe pas
     */
    public function isCategoryByNameExist(Category $category): bool
    {
        try {
            //Récupération de la valeur de name (category)
            $name = $category->getName();
            //Ecrire la requête SQL
            $request = "SELECT c.id_category FROM category AS c WHERE c.name = ?";
            //préparer la requête
            $req = $this->connection->prepare($request);
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
            //Suppression de la category dans la table d'association task_category
            $requestAsso = "DELETE FROM task_category WHERE id_category = ?";
            $req = $this->connection->prepare($requestAsso);
            $req->bindParam(1, $id, \PDO::PARAM_INT);
            $req->execute();
            //Suppression de la category par son id_category
            $request = "DELETE FROM category WHERE id_category = ?";
            $req2 = $this->connection->prepare($request);
            $req2->bindParam(1, $id, \PDO::PARAM_INT);
            $req2->execute();
        } catch (\Exception $e) {
            throw new CategoryException($e->getMessage());
        }
    }

    /**
     * Méthode qui retourne une Category depuis son ID
     * @param int $id ID de la category en BDD
     * @return Category|null retourne une Category si elle existe
     */
    public function findCategory(int $id): null | Category
    {
        try {
            //Ecrire la requête SQL
            $request = "SELECT c.id_category AS idCategory, c.name FROM category AS c WHERE c.id_category = ?";
            //préparer la requête
            $req = $this->connection->prepare($request);
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
     * Méthode qui retourne une Category depuis son name
     * @param Category Objet Category
     * @return Category | stdClass | null retourne une Category si elle existe
     */
    public function findCategoryByName(Category $category): null | Category
    {
        try {
            //Récupération du name
            $name = $category->getName();
            //Ecrire la requête SQL
            $request = "SELECT c.id_category AS idCategory, c.name FROM category AS c WHERE c.name = ?";
            //préparer la requête
            $req = $this->connection->prepare($request);
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
     * Méthode qui met à jour un enregistrement Category en BDD
     * requête de MAJ insert
     * @var Category $category  
     * @return void
     */
    public function updateCategory(Category $category): void {
        try {
            //Récupération de la valeur de name (category)
            $name = $category->getName();
            //Récupération de la valeur de l'id (category)
            $id = $category->getIdCategory();
            //Stocker la requête dans une variable
            $request = "UPDATE category set name = ? WHERE id_category = ?";
            //1 préparer la requête
            $req = $this->connection->prepare($request);
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
