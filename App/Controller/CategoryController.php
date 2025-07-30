<?php

namespace App\Controller;

use App\Model\Category;
use App\Utils\Utilitaire;

class CategoryController
{

    //Attribut Model Category
    private Category $category;

    public function __construct()
    {
        //Injection de dépendance
        $this->category = new Category();
    }

    public function showAllCategory()
    {
        //Récupération du message de confirmation
        $message = $_GET["message"] ?? "";
        $categories = $this->category->findAllCategory();
        include "App/View/viewAllCategory.php";
    }

    public function addCategory()
    {
        //Message erreur ou confirmation
        $message = "";
        //tester si le formulaire est soumis
        if (isset($_POST["submit"])) {
            //tester si le champs est non vide
            if (!empty($_POST["name"])) {
                //nettoyer les informations
                $name = Utilitaire::sanitize($_POST["name"]);
                //Créer un Objet Category
                $category = new Category();
                //Setter le nom
                $category->setName($name);
                //tester si la category n'existe pas
                if (!$category->isCategoryByNameExist()) {
                    //ajouter la category en BDD
                    $category->saveCategory();
                    //redirection vers la liste des categories avec un paramètre GET
                    header("Location: /task/category/all?message=La category " . $name . " a été ajouté en BDD");
                } else {
                    $message = "La categorie existe déja";
                }
            } else {
                $message = "Veuillez remplir les champs obligatoire";
            }
        }

        include "App/View/viewAddCategory.php";
    }

    public function removeCategory()
    {
        if (isset($_POST["delete"])) {
            $id = Utilitaire::sanitize($_POST["id"]);
            $this->category->deleteCategory($id);
            header('Location: /task/category/all?message=La catégorie a été supprimé');
        }
    }
}
