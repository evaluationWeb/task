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
            <h2>Modifier la tache</h2>
            <input type="text" name="title" placeholder="Sasir le titre de la tache" value="<?= $task->getTitle() ?>">
            <textarea name="description" rows="4" cols="50" placeholder="Saisir la description de la tache"><?= $task->getDescription() ?></textarea>
            <label for="endDate">Saisir la date de fin de la tache</label>
            <input type="datetime-local" name="endDate" value="<?= $task->getEndDate()->format('Y-m-d') . 'T' . $task->getEndDate()->format('H:i') ?>">

            <label for="categories">Choisir les categories :</label>
            <select multiple="true" name="categories[]">
                <optgroup label="Category">
                    <?php foreach ($categories as $category) : ?>
                        <?php if (associateCategory($task->getCategories(), $category->getIdCategory())) : ?>
                            <option value="<?= $category->getIdCategory() ?>" selected><?= $category->getName() ?></option>
                        <?php else : ?>
                            <option value="<?= $category->getIdCategory() ?>"><?= $category->getName() ?></option>
                        <?php endif ?>
                    <?php endforeach ?>
                </optgroup>
            </select>
            <input type="hidden" name="id" value="<?= $task->getIdTask() ?>">
            <input type="submit" value="Modifier" name="submit">
        </form>
        <p><?= $message ?? "" ?></p>
    </main>
</body>

</html>
<?php

//test si la catégorie est associée à la tache.
function associateCategory(array $array, int $id)
{

    foreach ($array as $value) {
        if ($value->getIdCategory() == $id) {
            return true;
        }
    }
}
