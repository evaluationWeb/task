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
        <p class="error"><?= $message ?? "" ?></p>
        <form action="" method="post" enctype="multipart/form-data">
            <h2>Editer les informations du profil :</h2>
            <fieldset>
                <label>
                Prénom :
                <input
                    type="text"
                    name="firstname"
                    value="<?= $oldUserInfo->getFirstname() ?? "" ?>"
                />
                </label>
                <label>
                Nom :
                <input
                    type="text"
                    name="lastname"
                    value="<?= $oldUserInfo->getLastname() ?? "" ?>"
                />
                </label>
                
                <label>
                Email :
                <input
                    type="email"
                    name="email"
                    value="<?= $oldUserInfo->getEmail() ?? ""?>"
                />
                </label>
            </fieldset>
            <input type="submit" value="Modifier" name="submit">
        </form>
    </main>
</body>

</html>