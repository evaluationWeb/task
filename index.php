<?php

//importer les ressources
include "./env.php";

include "./vendor/autoload.php";

//Analyse de l'URL avec parse_url() et retourne ses composants
$url = parse_url($_SERVER['REQUEST_URI']);
//test si l'url posséde une route sinon on renvoi à la racine
$path = $url['path'] ??  '/';

//Test des routes
switch ($path) {
    case "/task/":
        echo "Bienvenue";
        break;
    case "/task/connexion" :
        echo "Connexion";
        break;
    case "/task/task/add":
        echo "La tache a été ajouté";
        break;
    default:
        echo "404";
        break;
}
