<?php

namespace App\Controller;

use App\Model\Category;
use App\Repository\CategoryRepository;
use App\Utils\Tools;

class CategoryController
{
    //Attribut Model Category
    private CategoryRepository $categoryRepository;

    public function __construct()
    {
        //Injection de dépendance
        $this->categoryRepository = new CategoryRepository();
    }

    public function showAllCategory()
    {
        $message = "";
        //Test si le message existe
        if (isset($_GET["message"])) {
            //Récupération et sanitize du message
            $message = Tools::sanitize($_GET["message"]);
            //refresh de la page au bout de 1 seconde et demie
            $base = (BASE_URL === "/") ? "" : BASE_URL;
            header("Refresh:4; url=" . $base . "/category/all");
        }
        //tableau des catégories
        $categories = $this->categoryRepository->findAllCategory();
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
                $name = Tools::sanitize($_POST["name"]);
                //Créer un Objet Category
                $category = new Category();
                //Setter le nom
                $category->setName($name);
                //tester si la category n'existe pas
                if (!$this->categoryRepository->isCategoryByNameExist($category)) {
                    //ajouter la category en BDD
                    $this->categoryRepository->saveCategory($category);
                    $base = (BASE_URL === "/") ? "" : BASE_URL;
                    //redirection vers la liste des categories avec un paramètre GET
                    header("Location: " . $base . "/category/all?message=La category " . $name . " a été ajouté en BDD");
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
        $base = (BASE_URL === "/") ? "" : BASE_URL;
        if (isset($_GET["id"])) {
            $id = Tools::sanitize($_GET["id"]);
            $this->categoryRepository->deleteCategory($id);
            
            header('Location: ' . $base . '/category/all?message=La catégorie a été supprimé');
        }
        header('Location: ' . $base . '/category/all');
    }
    
    public function modifyCategory()
    {
        $base = (BASE_URL === "/") ? "" : BASE_URL;
        //test si le formulaire est submit
        if (isset($_POST["submit"])) {
            //Test si le champ est vide
            if (empty($_POST["name"])) {
                //redirection à la liste des catégorie avec un message
                header('Location: ' . $base . '/category/all?message=Veuillez remplir tous les champs');
            }
            //nettoyage des informations
            $name = Tools::sanitize($_POST["name"]);
            $id = Tools::sanitize($_POST["id"]);
            $category = new Category();
            //set du name et ID
            $category->setName($name);
            $category->setIdCategory($id);
            //Test si la catégorie existe déja (éviter les doublons)
            if ($this->categoryRepository->isCategoryByNameExist($category)) {
                //redirection à la liste des catégorie avec un message
                header('Location: ' . $base . '/category/all?message=Aucune mise à jour');
            }
            //Mise à jour de la catégorie
            $this->categoryRepository->updateCategory($category);
            //redirection à la liste des catégorie avec un message
            header('Location: ' . $base . '/category/all?message=Aucune mise à jour');
        } else {
            //sanitize de l'id 
            $id = Tools::sanitize($_POST["id"]);
            //récupération de la précédente valeur de la catégorie
            $cat = $this->categoryRepository->findCategory($id);
        }
        include "App/View/viewModifyCategory.php";
    }
}
