<?php

namespace App\Router;

use App\Router\Route;
use App\Router\RouterException;

class Router
{
    private ?string $path;
    private ?string $bearer;
    private array $routes = [];

    public function __construct()
    {
        $this->setPath();
        $this->setBearer();
    }

    //Getters
    public function getPath(): string|null
    {
        return $this->path;
    }

    public function getBearer(): string|null
    {
        return $this->bearer;
    }

    //Méthode pour ajouter une route
    public function addRoute(Route $routes): void
    {
        $this->routes[] = $routes;
    }

    //Méthode pour supprimer une route
    public function removeRoute(Route $routes): void
    {
        $key = array_search($routes, $this->routes);
        unset($this->routes[$key]);
    }

    /**
     * Méthode pour lancer le router
     * @return void|RouterException appele la méthode du controller
     * @throws RouterException dans le cas ou elle n'existe pas
     * ou si l'utilisateur ne possède pas les droits
     */
    public function run(): void
    {
        foreach ($this->routes as $route) {
            //Test si l' url et la méthode HTTP de la requête existent
            if ($this->isValidRoute($route)) {

                if (!$this->isGranted($route->getGrants())) {
                    //On lance une exception si l'utilisateur n'a pas l'authorization'
                    throw new RouterException('Page Unauthorized', 401);
                }
                //instance du controller lié à la route
                $controller = "App\\Controller\\" . $route->getController() . "Controller";
                $controller = new $controller();
                $method = $route->getMethod();
                $params = $route->getParams();
                //Test si la route ne posséde pas des paramétres
                if (empty($params)) {
                    //Appel de la méthode sans paramètres
                    $controller->$method();
                    return;
                }
                //Appel de la méthode avec des paramétres
                call_user_func_array([$controller, $method], $params);
                return;
            }
        }
        //On lance une exception si la page n'est pas trouvée
        throw new RouterException('Page not found', 404);
    }

    /**
     * Méthode qui vérifie si l'utilisateur à le droit d'accéder à la ressource
     * @param array $granted droits autorises de la route
     * @return bool true si autorisé false si pas autorisé
     */
    private function isGranted(array $granted): bool
    {
        //boucle parcours des droits de l'utilisateur
        foreach ($_SESSION["grant"] as $grant) {
            //boucle parcours des droits associés de la route
            foreach ($granted as $value) {
                //test si un droit de l'utilisateur est contenu dans la route
                if ($grant == $value) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Méthode qui vérifie si la route et la méthode HTTP existe
     * @param Route $route à vérifier
     * @return bool true si existe false si elle n'existe pas
     */
    private function isValidRoute(Route $route): bool
    {
        if (
            $route->getUrl() === $this->path and
            $route->getRequestMethod() === $_SERVER['REQUEST_METHOD']
        ) {
            return true;
        }
        return false;
    }

    /**
     * Méthode qui récupére le path de l'url
     * @return void assigne le path de l'url
     */
    public function setPath(): void
    {
        //Analyse de l'URL avec parse_url() et retourne ses composants
        $url = parse_url($_SERVER['REQUEST_URI']);
        //test si l'url posséde une route sinon on renvoi à la racine
        $path = $url['path'] ??  '/';
        //Test si la BASE_URL est /
        if (BASE_URL != "/") {
            $path = substr($path, strlen(BASE_URL));
        }
        $this->path = $path;
    }

    /**
     * Méthode qui récupére Bearer Token
     * @return void assigne le Bearer Token
     */
    public function setBearer(): void
    {
        $bearer = isset($_SERVER['HTTP_AUTHORIZATION']) ? preg_replace(
            '/Bearer\s+/',
            '',
            $_SERVER['HTTP_AUTHORIZATION']
        ) : null;
        $this->bearer = $bearer;
    }

    public static function assignDefaultGrant() {
        //Test si pas connecté donner le role public
        if (!isset($_SESSION["connected"])) {
            $_SESSION["grant"] = ["ROLE_PUBLIC"];
        }
    }
}
