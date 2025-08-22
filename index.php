<?php
//importer les ressources
include "./env.php";

include "./vendor/autoload.php";

//Analyse de l'URL avec parse_url() et retourne ses composants
$url = parse_url($_SERVER['REQUEST_URI']);
//test si l'url posséde une route sinon on renvoi à la racine
$path = $url['path'] ??  '/';

session_start();

/*--------------------------
---------Token JWT----------
--------------------------*/
$bearer = isset($_SERVER['HTTP_AUTHORIZATION']) ? preg_replace(
    '/Bearer\s+/',
    '',
    $_SERVER['HTTP_AUTHORIZATION']
) : null;

/*--------------------------
--------Bloc Router---------
--------------------------*/

//importer les classes du router
use App\Router\Router;
use App\Router\Route;


//inport et instance de HomeController
use App\Controller\HomeController;
$homeController = new HomeController();

//Instance du Router
$router = new Router(substr($path, strlen(BASE_URL)), $bearer);

/*--------------------------
-----Ajout des routes-------
--------------------------*/

/* Bloc routes communes */
$router->addRoute(new Route('/', 'GET', 'Home', 'home'));
$router->addRoute(new Route('/test/email', 'GET', 'Home', 'testEmail'));
$router->addRoute(new Route('/test/email', 'POST', 'Home', 'testEmail'));

/* Bloc routes déconnectées */
if (!isset($_SESSION["connected"])) {
    $router->addRoute(new Route('/user/connexion', 'GET', 'User', 'connexion'));
    $router->addRoute(new Route('/user/connexion', 'POST', 'User', 'connexion'));
    $router->addRoute(new Route('/user/register', 'GET', 'User', 'addUser'));
    $router->addRoute(new Route('/user/register', 'POST', 'User', 'addUser'));
    $router->addRoute(new Route('/user/password/recover', 'GET', 'User', 'recoverPassword'));
    $router->addRoute(new Route('/user/password/recover', 'POST', 'User', 'recoverPassword'));
    $router->addRoute(new Route('/user/password/generate', 'GET', 'User', 'regeneratePassword'));
    $router->addRoute(new Route('/user/password/generate', 'POST', 'User', 'regeneratePassword'));
}

/* Bloc routes connectées */
if (isset($_SESSION["connected"])) {
    $router->addRoute(new Route('/user/deconnexion', 'GET', 'User', 'deconnexion'));
    $router->addRoute(new Route('/category/all', 'GET', 'Category', 'showAllCategory'));
    $router->addRoute(new Route('/category/delete', 'GET', 'Category', 'removeCategory'));
    $router->addRoute(new Route('/category/update', 'GET', 'Category', 'modifyCategory'));
    $router->addRoute(new Route('/category/update', 'POST', 'Category', 'modifyCategory'));
    $router->addRoute(new Route('/category/add', 'GET', 'Category', 'addCategory'));
    $router->addRoute(new Route('/category/add', 'POST', 'Category', 'addCategory'));
    $router->addRoute(new Route('/task/add', 'GET', 'Task', 'addTask'));
    $router->addRoute(new Route('/task/add', 'POST', 'Task', 'addTask'));
    $router->addRoute(new Route('/task/all', 'GET', 'Task', 'showAllTask'));
    $router->addRoute(new Route('/task/update', 'GET', 'Task', 'modifyTask'));
    $router->addRoute(new Route('/task/update', 'POST', 'Task', 'modifyTask'));
    $router->addRoute(new Route('/task/validate', 'GET', 'Task', 'terminateTask'));
    $router->addRoute(new Route('/task/validate', 'POST', 'Task', 'terminateTask'));
    $router->addRoute(new Route('/user/profil', 'GET', 'User', 'showUserProfile'));
    $router->addRoute(new Route('/user/update/password', 'GET', 'User', 'modifyPassword'));
    $router->addRoute(new Route('/user/update/password', 'POST', 'User', 'modifyPassword'));
    $router->addRoute(new Route('/user/update/img', 'GET', 'User', 'modifyImage'));
    $router->addRoute(new Route('/user/update/img', 'POST', 'User', 'modifyImage'));
    $router->addRoute(new Route('/user/update/info', 'GET', 'User', 'modifyInfo'));
    $router->addRoute(new Route('/user/update/info', 'POST', 'User', 'modifyInfo'));
}

//Démarrage du Router
try {
    $router->run();
} catch (\App\Router\RouterException $e) {
    //affiche la page 404
    $homeController->error404();
}
