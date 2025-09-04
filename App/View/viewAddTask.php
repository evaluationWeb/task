<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/style/main.css">
    <link rel="stylesheet" href="../public/style/pico.min.css">
    <title>Ajouter une tache</title>
</head>

<body>
    <header class="container-fluid">
        <?php include "App/View/components/navbar.php"; ?>
    </header>
    <main class="container-fluid">
        <form action="" method="post">
            <h2>Ajouter une tache</h2>
            <input type="text" name="title" placeholder="Saisir le titre de la tache">
            <textarea name="description" rows="4" cols="50" placeholder="Saisir la description de la tache"></textarea>
            <label for="endDate">Saisir la date de fin de la tache</label>
            <input type="datetime-local" name="endDate">

            <label for="categories">Choisir les categories :</label>
            <select multiple="true" name="categories[]">
                <optgroup label="Category">
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= $category->getIdCategory() ?>"><?= $category->getName() ?></option>
                    <?php endforeach ?>
                </optgroup>
            </select>
            <input type="submit" value="Ajouter" name="submit">
        </form>
        <p class="error"><?= $message ?? "" ?></p>
    </main>
</body>

</html>