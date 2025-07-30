<?php

//importer les ressources
include "./env.php";

include "./vendor/autoload.php";

//Analyse de l'URL avec parse_url() et retourne ses composants
$url = parse_url($_SERVER['REQUEST_URI']);
//test si l'url posséde une route sinon on renvoi à la racine
$path = $url['path'] ??  '/';

//import des classes controller
use App\Controller\HomeController;
use App\Controller\CategoryController;

//Instance des controller
$homeController = new HomeController();
$categoryController = new CategoryController();

//Test des routes
switch ($path) {
    case "/task/":
        $homeController->home();
        break;
    case "/task/category/all" :
        $categoryController->showAllCategory();
        break;
    case "/task/task/add":
        echo "La tache a été ajouté";
        break;
    default:
        echo "404";
        break;
}
