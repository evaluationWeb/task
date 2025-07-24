<?php
//importer le fichier bdd.php
include "../utils/bdd.php";

//ajouter une category (objet de connexion et le contenu de la catégorie)
function addCategory(string $name) {

    try {
        $request = "INSERT INTO category(`name`) VALUES (?)";
        //Ecrire toutes les étapes de la requêtes 
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

function addCategoryNosecure(string $name) {
    try {
        $request = "SELECT * FROM category where id_category = $name";
        $req = connectBDD()->query($request);
    }
    catch(Exception $e) {
        echo $e->getMessage();
    }
}
