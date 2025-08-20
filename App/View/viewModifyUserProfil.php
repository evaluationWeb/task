<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/style/main.css">
    <link rel="stylesheet" href="../../public/style/pico.min.css">
    <title>Editer les informations du compte</title>
</head>

<body>
    <header class="container-fluid">
        <?php include "App/View/components/navbar.php"; ?>
    </header>
    <main class="container-fluid">
        <form action="" method="post" enctype="multipart/form-data">
            <h2>Mettre à jour l'image de profil :</h2>
            <input type="file" name="img">
            <input type="submit" value="Modifier" name="submit">
            <!-- Affichage des erreurs ou résultat -->
            <p class="error"><?= $message ?? "" ?></p>
        </form>
    </main>
</body>

</html>