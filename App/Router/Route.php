<?php

namespace App\Router;

class Route
{
    //Attributs
    private ?string $url;
    private ?string $httpMethod;
    private ?string $controller;
    private ?string $method;
    private array $param = [];

    //Méthodes
    //Constructeur
    public function __construct(string $url, string $httpMethod, string $controller, string $method, ?array $param = [])
    {
        $this->url = $url;
        $this->httpMethod = $httpMethod;
        $this->controller = $controller;
        $this->method = $method;
        if($param){
            $this->addParams($param);
        } 
    }

    //Getters
    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getController(): ?string
    {
        return $this->controller;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function getParams(): array
    {
        return $this->param;
    }
    
    public function getRequestMethod(): string
    {
        return $this->httpMethod;
    }

    //Setters
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }
    
    public function addParams(array $params): void
    {
        foreach ($params as $param) {
            $this->param[] = $param;
        }
    }

    public function setRequestMethod(string $httpMethod): void
    {
        $this->httpMethod = $httpMethod;
    }
}
