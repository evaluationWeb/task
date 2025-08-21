<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/style/main.css">
    <link rel="stylesheet" href="../public/style/pico.min.css">
    <title>Connexion</title>
</head>

<body>
    <header class="container-fluid">
        <?php include "App/View/components/navbar.php"; ?>
    </header>
    <main class="container-fluid">

        <form action="" method="post">
            <h2>Se connecter</h2>
            <input type="email" name="email" placeholder="saisir le mail">
            <input type="password" name="password" placeholder="saisir le mot de passe">
            <input type="submit" value="connexion" name="submit">
            <p class="error"><?= $message ?></p>
            <a href="<?= BASE_URL ?>/user/password/recover" data-tooltip="Récupération du mot de passe">
                <input type="button" value="Récupération du mot de passe">
            </a>
        </form>
    </main>
</body>

</html>