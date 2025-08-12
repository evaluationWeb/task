<nav>
    <ul>
        <!-- Menu commun -->
        <li><strong><a href="<?= BASE_URL ?>/">Accueil</a></strong></li>    
    </ul>
        <!-- Menu connecté -->
        <?php if (isset($_SESSION["connected"])) :?>
    <ul>
        <li><a href="<?= BASE_URL ?>/category/all">Liste catégories</a></li>
        <li><a href="<?= BASE_URL ?>/category/add">Ajouter catégorie</a></li>
        <li><a href="<?= BASE_URL ?>/task/all">Liste taches</a></li>
        <li><a href="<?= BASE_URL ?>/task/add">Ajouter une tache</a></li>
        <li><a href="<?= BASE_URL ?>/user/deconnexion"><img src="<?= BASE_URL ?>/public/image/<?= $_SESSION["img"] ?>" alt="deconnexion"></a></li>
        <?php else : ?>
        <!-- Menu déconnecté -->
        <li><a href="<?= BASE_URL ?>/user/register">Inscription</a></li>
        <li><a href="<?= BASE_URL ?>/user/connexion">Connexion</a></li>
        <?php endif ?>
    </ul>
</nav>
