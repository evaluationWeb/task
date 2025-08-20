<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/style/main.css">
    <link rel="stylesheet" href="../public/style/pico.min.css">
    <title>Tasks</title>
</head>

<body>
    <header class="container-fluid">
        <?php include "App/View/components/navbar.php"; ?>
    </header>
    <main class="container-fluid">
        <h2>Liste des taches</h2>
        <table class="striped">
            <thead data-theme="dark">
                <th>Title</th>
                <th>Date de fin</th>
                <th>Auteur</th>
                <th>Categories</th>
                <th>Editer</th>
                <th>Valider</th>
            </thead>
            <!-- Boucler sur le tableau de Category -->
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= $task->getTitle() ?> </td>
                    <td>
                        <?= $task->getEndDate()->format('d/m/Y') ?>
                    </td>
                    <td>
                        <?= $task->getUser()->getFirstname() . " " . $task->getUser()->getLastname() ?>
                    </td>
                    <td>
                        <?php foreach ($task->getCategories() as $category) : ?>
                            <?= $category->getName() . " " ?>
                        <?php endforeach ?>
                    </td>
                    <td>
                        <form action="/task/task/update" method="post">
                            <input type="hidden" name="id" value="<?= $task->getIdTask() ?>">
                            <input type="submit" name="update" value="Editer">
                        </form>
                    </td>
                    <td>
                        <form action="/task/task/validate" method="post">
                            <input type="hidden" name="id" value="<?= $task->getIdTask() ?>">
                            <input type="submit" name="valider" value="Valider">
                        </form>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
    </main>
</body>

</html>