<?php
//importer les ressources
include "./env.php";

include "./vendor/autoload.php";

//importer les classes du router
use App\Router\Router;
use App\Router\Route;

//Démarrage de la session
session_start();

//Assignation du role ROLE_PUBLIC
Router::assignDefaultGrant();


//inport et instance de HomeController
use App\Controller\HomeController;

$homeController = new HomeController();

//Instance du Router
$router = new Router();

/*--------------------------
-----Ajout des routes-------
--------------------------*/

/* Bloc routes communes */
$router->addRoute(new Route('/', 'GET', 'Home', 'home', ["ROLE_PUBLIC", "ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/test/email', 'GET', 'Home', 'testEmail', ["ROLE_PUBLIC", "ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/test/email', 'POST', 'Home', 'testEmail', ["ROLE_PUBLIC", "ROLE_USER", "ROLE_ADMIN"]));

/* Bloc routes déconnectées */

$router->addRoute(new Route('/user/connexion', 'GET', 'User', 'connexion', ["ROLE_PUBLIC"]));
$router->addRoute(new Route('/user/connexion', 'POST', 'User', 'connexion', ["ROLE_PUBLIC"]));
$router->addRoute(new Route('/user/register', 'GET', 'User', 'addUser', ["ROLE_PUBLIC"]));
$router->addRoute(new Route('/user/register', 'POST', 'User', 'addUser', ["ROLE_PUBLIC"]));
$router->addRoute(new Route('/user/password/recover', 'GET', 'User', 'recoverPassword', ["ROLE_PUBLIC"]));
$router->addRoute(new Route('/user/password/recover', 'POST', 'User', 'recoverPassword', ["ROLE_PUBLIC"]));
$router->addRoute(new Route('/user/password/generate', 'GET', 'User', 'regeneratePassword', ["ROLE_PUBLIC"]));
$router->addRoute(new Route('/user/password/generate', 'POST', 'User', 'regeneratePassword', ["ROLE_PUBLIC"]));


/* Bloc routes connectées */

$router->addRoute(new Route('/user/deconnexion', 'GET', 'User', 'deconnexion', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/category/all', 'GET', 'Category', 'showAllCategory', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/category/delete', 'GET', 'Category', 'removeCategory', ["ROLE_ADMIN"]));
$router->addRoute(new Route('/category/update', 'GET', 'Category', 'modifyCategory', ["ROLE_ADMIN"]));
$router->addRoute(new Route('/category/update', 'POST', 'Category', 'modifyCategory', ["ROLE_ADMIN"]));
$router->addRoute(new Route('/category/add', 'GET', 'Category', 'addCategory', ["ROLE_ADMIN"]));
$router->addRoute(new Route('/category/add', 'POST', 'Category', 'addCategory', ["ROLE_ADMIN"]));
$router->addRoute(new Route('/task/add', 'GET', 'Task', 'addTask', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/task/add', 'POST', 'Task', 'addTask', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/task/all', 'GET', 'Task', 'showAllTask', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/task/update', 'GET', 'Task', 'modifyTask', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/task/update', 'POST', 'Task', 'modifyTask', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/task/validate', 'GET', 'Task', 'terminateTask', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/task/validate', 'POST', 'Task', 'terminateTask', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/user/profil', 'GET', 'User', 'showUserProfile', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/user/update/password', 'GET', 'User', 'modifyPassword', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/user/update/password', 'POST', 'User', 'modifyPassword', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/user/update/img', 'GET', 'User', 'modifyImage', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/user/update/img', 'POST', 'User', 'modifyImage', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/user/update/info', 'GET', 'User', 'modifyInfo', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/user/update/info', 'POST', 'User', 'modifyInfo', ["ROLE_USER", "ROLE_ADMIN"]));
$router->addRoute(new Route('/tasks', 'GET', 'Task', 'showAllTaskHydrate', ["ROLE_USER", "ROLE_ADMIN"]));


//Démarrage du Router
try {
    $router->run();
} catch (\App\Router\RouterException $e) {
    if ($e->getCode() == 404) {
        //affiche la page 404
        $homeController->error404();
    }
    if ($e->getCode() == 401) {
        //affiche la page 401
        $homeController->unauthorized();
    }
}
