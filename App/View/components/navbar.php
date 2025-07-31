<nav class="navbar">
    <ul>
        <li><a href="<?= BASE_URL ?>/">Accueil</a></li>
        <li><a href="<?= BASE_URL ?>/category/all">Liste catégories</a></li>


        <?php if (isset($_SESSION["connected"])) :?>

        <li><a href="<?= BASE_URL ?>/category/add">Ajouter catégorie</a></li>
        <li><a href="<?= BASE_URL ?>/user/deconnexion">Deconnexion</a></li>
        <?php else : ?>

        <li><a href="<?= BASE_URL ?>/user/register">Inscription</a></li>
        <li><a href="<?= BASE_URL ?>/user/connexion">Connexion</a></li>
        <?php endif ?>
    </ul>
</nav>
