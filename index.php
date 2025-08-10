<?php

//importer les ressources
include "./env.php";

include "./vendor/autoload.php";

//Analyse de l'URL avec parse_url() et retourne ses composants
$url = parse_url($_SERVER['REQUEST_URI']);
//test si l'url posséde une route sinon on renvoi à la racine
$path = $url['path'] ??  '/';

session_start();

//import des classes controller
use App\Controller\HomeController;
use App\Controller\CategoryController;
use App\Controller\UserController;
use App\Controller\TaskController;

//Instance des controller
$homeController = new HomeController();
$categoryController = new CategoryController();
$userController = new UserController();
$taskController = new TaskController();

if ( !isset($_SESSION["connected"])) {
    //Test des routes version deconnecté
    switch (substr($path, strlen(BASE_URL))) {
        case "/":
            $homeController->home();
            break;
        case "/category/all":
            $categoryController->showAllCategory();
            break;
        case "/user/connexion":
            $userController->connexion();
            break;
        case "/user/register":
            $userController->addUser();
            break;
        default:
            $homeController->error404();
            break;
    }
} else {
//Test des routes version connecté
    switch (substr($path, strlen(BASE_URL))) {
        case "/":
            $homeController->home();
            break;
        case "/category/all":
            $categoryController->showAllCategory();
            break;
        case "/user/deconnexion":
            $userController->deconnexion();
            break;
        case "/category/delete" :
            $categoryController->removeCategory();
            break;
        case "/category/update" :
            $categoryController->modifyCategory();
            break;
        case "/category/add" :
            $categoryController->addCategory();
            break;
        case "/task/add" :
            $taskController->addTask();
            break;
        case "/task/all" :
            $taskController->showAllTask();
            break;
        case "/task/update" :
            $taskController->modifyTask();
            break;
        default:
            $homeController->error404();
            break;
    }
}




