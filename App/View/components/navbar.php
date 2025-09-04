<?php use App\Utils\Tools; ?>
<nav>
    <ul>
        <!-- Menu commun -->
        <li><strong><a href="<?= BASE_URL ?>/" data-tooltip="Page Accueil">Accueil</a></strong></li>
    </ul>
    <!-- Menu connecté -->
    <?php if (isset($_SESSION["connected"])) : ?>
    <ul>
        <li><a href="<?= BASE_URL ?>/category/all" data-tooltip="Liste des categories">Liste catégories</a></li>
        <?php if (Tools::checkGrants("ROLE_ADMIN")) : ?>
            <li><a href="<?= BASE_URL ?>/category/add" data-tooltip="Ajouter une catégorie">Ajouter catégorie</a></li>
        <?php endif ?>
        <li><a href="<?= BASE_URL ?>/task/all" data-tooltip="Liste des taches">Ma Liste de taches</a></li>
        <li><a href="<?= BASE_URL ?>/task/add" data-tooltip="Ajouter une tache">Ajouter une tache</a></li>
        <li><a href="<?= BASE_URL ?>/user/profil" data-tooltip="Profil"><img src="<?= BASE_URL ?>/public/image/<?= $_SESSION["img"] ?>" alt="image de profil"></a></li>
        <li><a href="<?= BASE_URL ?>/user/deconnexion" data-tooltip="Déconnexion"><img src="<?= BASE_URL ?>/public/image/logout.png" alt="deconnexion"></a></li>
    <?php else : ?>
        <!-- Menu déconnecté -->
        <li><a href="<?= BASE_URL ?>/user/register" data-tooltip="Créer un compte">Inscription</a></li>
        <li><a href="<?= BASE_URL ?>/user/connexion" data-tooltip="Se connecter">Connexion</a></li>
    <?php endif ?>
    </ul>
</nav>