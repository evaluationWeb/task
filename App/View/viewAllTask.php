<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/style/main.css">
    <title>Category</title>
</head>

<body>
    <?php include "App/View/components/navbar.php"; ?>
    <h1>Liste des taches</h1>
    <table>
        <thead>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Date de fin</th>
            <th>Auteur</th>
        </thead>
        <!-- Boucler sur le tableau de Category -->
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= $task->getIdTask() ?> </td>
                <td><?= $task->getTitle() ?> </td>
                <!-- version avec id en post avec un bouton -->
                <td>
                    <?= $task->getDescription() ?>
                </td>
                <td>
                    <?= $task->getEndDate()->format('d/m/Y H:i:s') ?>
                </td>
                <td>
                    <?= $task->getUser()->getFirstname() . " " . $task->getUser()->getLastname() ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
</body>
</html>
