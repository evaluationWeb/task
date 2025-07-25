<?php
include "../env.php";
include "../utils/bdd.php";
//ajouter une category (le contenu de la catégorie son nom)
function addCategory(string $name)
{

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
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function getCategoryById(int $id)
{
    try {
        $request = "SELECT c.id_category, c.name FROM category AS c WHERE c.id_category = ?";
        //1 preparer la requête
        $req = connectBDD()->prepare($request);
        //2 assigner le paramètre
        $req->bindParam(1, $id, PDO::PARAM_INT);
        //3 executer la requête
        $req->execute();
        //4 récupérer le resultat
        $category =  $req->fetch(PDO::FETCH_ASSOC);
        return $category;
    } catch (Exception $e) {
       return [$e->getMessage()];
    }
}

function getAllCategory() {
    try {
        $request = "SELECT c.id_category, c.name FROM category AS c";
        $req = connectBDD()->prepare($request);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [$e->getMessage()];
    }
}

function updateCategory(int $id, string $name) {
    try {
        $request = "UPDATE category SET name = ? WHERE id_category = ?";
        $req = connectBDD()->prepare($request);
        $req->bindParam(1, $name, PDO::PARAM_STR);
        $req->bindParam(2, $id, PDO::PARAM_INT);
        $req->execute();
    } catch (Exception $e) {
        return $e->getMessage();//throw $th;
    }
}

function deleteCategory(int $id) {
    try {
        $request = "DELETE FROM category WHERE id_category = ?";
        $req = connectBDD()->prepare($request);
        $req->bindParam(1, $id, PDO::PARAM_INT);
        $req->execute();
    } catch (Exception $e) {
        return $e->getMessage();//throw $th;
    }
}

function addCategoryV2(string $name)
{

    try {
        //Stocker la requête dans une variable
        $request = "INSERT INTO category(name) VALUES (:label)";
        //1 préparer la requête
        $req = connectBDD()->prepare($request);
        //2 Bind les paramètres
        //$req->bindParam(':label', $name, PDO::PARAM_STR);
        //3 executer la requête
        $req->execute(['label'=>$name]);

        //Capture des erreurs 
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
echo "<pre>";
print_r(getAllCategory());
echo "</pre>";
deleteCategory(1);
echo "<pre>";
print_r(getAllCategory());
echo "</pre>";