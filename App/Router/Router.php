<?php

namespace App\Router;

use App\Router\Route;
use App\Router\RouterException;

class Router
{
    private ?string $path;
    private ?string $bearer;
    private array $routes = [];

    public function __construct(string $path, ?string $bearer)
    {
        $this->path = $path;
        $this->bearer = $bearer;
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

    //Méthode pour lancer le router
    public function run(): void
    {
        foreach ($this->routes as $route) {
            //Test si l' url et la méthode HTTP de la requête existent
            if ($route->getUrl() === $this->path and $route->getRequestMethod() === $_SERVER['REQUEST_METHOD']) {

                $controller = "App\\Controller\\" . $route->getController() . "Controller";
                $controller = new $controller();
                $method = $route->getMethod();
                $params = $route->getParams();
                //Test si la route ne posséde pas des paramétres
                if (empty($params)) {
                    $controller->$method();
                    return;
                }
                //sinon on lance la méthode avec les paramétres
                call_user_func_array([$controller, $method], $params);
                return;
            }
        }
        //On lance exception si la page n'est pas trouvée
        throw new RouterException('Page not found', 404);
    }
}
