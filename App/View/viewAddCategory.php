<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Categorie</title>
</head>
<body>
    <h1>Ajouter une catégorie</h1>
    <form action="" method="post">
        <input type="text" name="name" placeholder="Saisir le nom de la categorie">
        <input type="submit" value="Enregistrer" name="submit">
    </form>
    <!-- Affichage des erreurs ou résultat -->
    <p><?= $message ?></p>
</body>
</html>
