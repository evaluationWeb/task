<?php

namespace App\Controller;

class HomeController
{

    public function home()
    {
        $name = $_GET["name"] ?? "";
        include "App/View/viewHome.php";
    }

    public function error404()
    {
        http_response_code(404);
        include "App/View/viewError404.php";
    }
}
