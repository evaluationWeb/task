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
            //Stocker la requête dans une variable
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
}
