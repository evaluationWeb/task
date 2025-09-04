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
        <?php

        use App\Utils\Tools;

        include "App/View/components/navbar.php";
        ?>
    </header>
    <main class="container-fluid">
        <h2>Liste des categories</h2>
        <table class="striped">
            <thead data-theme="dark">
                <th>ID</th>
                <th>NAME</th>
                <?php if (Tools::checkGrants("ROLE_ADMIN")) : ?>
                    <th>Supprimer</th>
                    <th>Editer</th>
                <?php endif ?>
            </thead>
            <!-- Boucler sur le tableau de Category -->
            <?php foreach ($categories as $category): ?>
                <!-- afficher le contenu de l'attribut name (Category) -->
                <tr>
                    <td><?= $category->getIdCategory() ?> </td>
                    <td><?= $category->getName() ?> </td>
                    <!-- version avec id en post avec un bouton -->
                    <?php if (Tools::checkGrants("ROLE_ADMIN")) : ?>
                    <td>
                        <button id="<?= $category->getIdCategory() ?>" data-target="modal-delete" data-tooltip="Supprimer la catégorie" onclick="toggleModal(event, this)">
                            Supprimer
                        </button>
                    </td>
                    <td>
                        <form action="/task/category/update" method="post">
                            <input type="hidden" name="id" value="<?= $category->getIdCategory() ?>">
                            <button type="submit" name="update" data-tooltip="Editer la catégorie">
                                Editer
                            </button>
                        </form>
                    </td>
                    <?php endif ?>
                </tr>
            <?php endforeach ?>
        </table>
        <p><?= $message ?></p>
    </main>
    <dialog id="modal-delete">
        <article>
            <header>
                <button
                    aria-label="Close"
                    rel="prev"
                    data-target="modal-delete"
                    onclick="toggleModal(event)"></button>
                <h3>Confirmer la suppression</h3>
            </header>
            <p>
                Attention La suppression de la catégorie est définitive.
            </p>
            <footer>
                <button
                    role="button"
                    class="secondary"
                    data-target="modal-delete"
                    onclick="toggleModal(event)">
                    Cancel
                </button>

                <button id="delete-category">
                    Confirm
                </button>
            </footer>
        </article>
    </dialog>
</body>

</html>