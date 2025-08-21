<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/style/main.css">
    <link rel="stylesheet" href="../../public/style/pico.min.css">
    <title>Recover Password</title>
</head>

<body>
    <header class="container-fluid">
        <?php include "App/View/components/navbar.php"; ?>
    </header>
    <main class="container-fluid">

        <form action="" method="post">
            <h2>Mot de passe oublié :</h2>
            <input type="text" name="email" placeholder="Saisir l'email du compte">
            <input type="submit" value="Récupérer" name="submit">
            <!-- Affichage des erreurs ou résultat -->
            <p class="error"><?= $message ?? "" ?></p>
        </form>
    </main>
</body>

</html>