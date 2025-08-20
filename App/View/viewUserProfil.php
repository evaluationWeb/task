<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/style/main.css">
    <link rel="stylesheet" href="../public/style/pico.min.css">
    <script src="../public/script/main.js"></script>
    <title>Category</title>
</head>

<body>
    <header class="container-fluid">
        <?php include "App/View/components/navbar.php"; ?>
    </header>
    <main class="container-fluid">
    <article>
        <h2> Profil utilisateur</h2>
        <p>Nom : <?= $userConnected->getLastname()?></p>
        <p>Prénom : <?= $userConnected->getFirstname()?></p>
        <p>Email : <?= $userConnected->getEmail()?></p>
        <a href="<?= BASE_URL ?>/user/update/password"><button>Changer le mot de passe</button></a>
    </article>    
    
    </main>
</body>

</html>