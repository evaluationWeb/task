<?php
    //importer les scripts
    include "env.php";
    include "utils/bdd.php";
    include "model/categoryModel.php";
    include "utils/utilitaire.php";
    
    $message = createCategory();
    //Logique pour ajouter une catégorie
    function createCategory() {
        //tester si le formulaire est soumis
        if(isset($_POST["submit"])) {
            //tester si les champs ne sont pas vides
            if(empty($_POST["nameCategory"])) {
                return "Le champ non de catégorie est vide";
            }
            //nettoyer l'entrée
            $nameCategory = sanitize($_POST["nameCategory"]);
            //tester si elle existe
            if(categoryExists($nameCategory)) {
                return "La categorie : " . $nameCategory . " existe déja";
            }
            //Ajouter la catégorie
            return addCategory($nameCategory);
        }

        return "";
    }
    //Test si le bouton delete est soumis
    if(isset($_POST["delete"])) {
        deleteCategory($_POST["id"]);
    }
    //récupérer la liste des catégories
    $categories = getAllCategory();

?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
</head>
<body>
    <h2>Ajouter une categorie</h2>
    <form action="" method="post">
        <input type="text" name="nameCategory" placeholder="Saisir le nom de la categorie">
        <input type="submit" value="Ajouter" name="submit">
    </form>
    <p><?=$message?></p>
    <h2>Liste des catégories</h2>
    <!-- boucle pour afficher toutes les catégories -->
    <?php foreach($categories as $category):?>
        <div><?= $category["id_category"] . ' ' . $category["name"] ?>
            <form action="" method="post">
                <input type="hidden" name="id" value="<?= $category["id_category"]?>">
                <input type="submit" value="supprimer" name="delete">
            </form>
        </div>
    <?php endforeach?>
</body>
</html>
