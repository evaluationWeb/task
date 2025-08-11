<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/style/main.css">
    <link rel="stylesheet" href="../public/style/pico.min.css">
    <title>Editer Categorie</title>
</head>

<body>
    <header class="container-fluid">
        <?php include "App/View/components/navbar.php"; ?>
    </header>
    <main class="container-fluid">

        <form action="" method="post">
            <h2>Editer la catégorie</h2>
            <input type="text" name="name" placeholder="Saisir le nom de la categorie" value="<?= $cat->GetName() ?>">
            <input type="hidden" name="id" value="<?= $cat->getIdCategory() ?>">
            <input type="submit" value="Enregistrer" name="submit">
        </form>
        <p><?= $message ?? "" ?></p>
    </main>
</body>

</html>