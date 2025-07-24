<?php
//importer le fichier bdd.php
include "../utils/bdd.php";

//ajouter une category (le contenu de la catégorie son nom)
function addCategory(string $name) {

    try {
        //Stocker la requête dans une variable
        $request = "INSERT INTO category(name) VALUES (?)";
        //1 préparer la requête
        $req = connectBDD()->prepare($request);
        //2 Bind les paramètres
        $req->bindParam(1, $name, PDO::PARAM_STR);
        //3 executer la requête
        $req->execute();

    //Capture des erreurs 
    } catch(Exception $e) {
        echo $e->getMessage();
    }
}

function getCategoryById(int $id) {
    try {
        $request = "SELECT c.id_category, c.name FROM category AS c WHERE c.id_category = ?";
        //1 preparer la requête
        $req = connectBDD()->prepare($request);
        //2 assigner le paramètre
        $req->bindParam(1,$id,PDO::PARAM_INT);
        //3 executer la requête
        $req->execute();
        //4 récupérer le resultat
        $category =  $req->fetch(PDO::FETCH_ASSOC);
        return $category;
    }
    catch(Exception $e) {
        $e->getMessage();
    }
}

function getCategoryByNameNosecure(string $name) {
    try {
        $request = "SELECT c.id_category, c.name FROM category AS c where c.name =  '$name'";
        $req = connectBDD()->query($request);
        $req->setFetchMode(PDO::FETCH_ASSOC);
        return $req->fetch();
    }
    catch(Exception $e) {
        echo $e->getMessage();
    }
}

?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test injection SQL</title>
</head>
<body>
    <form action="" method="post">
        <input type="text" name="name" placeholder="Saisir le nom de la catégorie à trouver">
        <input type="submit" value="envoyer" name="submit">
    </form>
</body>
</html>

<?php

if (isset($_POST["submit"])) {
    print_r(getCategoryByNameNosecure($_POST["name"]));
}
