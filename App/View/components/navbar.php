<?php use App\Utils\Tools; ?>
<nav>
    <ul>
        <!-- Menu commun -->
        <li><strong><a href="<?= (BASE_URL === "/") ? "" : BASE_URL ?>/" data-tooltip="Page Accueil">Accueil</a></strong></li>
    </ul>
    <!-- Menu connecté -->
    <?php if (isset($_SESSION["connected"])) : ?>
    <ul>
        <li><a href="<?= (BASE_URL === "/") ? "" : BASE_URL ?>/category/all" data-tooltip="Liste des categories" class="secondary">Liste catégories</a></li>
        <?php if (Tools::checkGrants("ROLE_ADMIN")) : ?>
            <li><a href="<?= (BASE_URL === "/") ? "" : BASE_URL ?>/category/add" data-tooltip="Ajouter une catégorie" class="secondary">Ajouter catégorie</a></li>
        <?php endif ?>
        <li><a href="<?= (BASE_URL === "/") ? "" : BASE_URL ?>/task/all" data-tooltip="Liste des taches" class="secondary">Ma Liste de taches</a></li>
        <li><a href="<?= (BASE_URL === "/") ? "" : BASE_URL ?>/task/add" data-tooltip="Ajouter une tache" class="secondary">Ajouter une tache</a></li>
        <li><a href="<?= (BASE_URL === "/") ? "" : BASE_URL ?>/user/profil" data-tooltip="Profil" class="secondary"><img src="<?= (BASE_URL === "/") ? "" : BASE_URL ?>/public/image/<?= $_SESSION["img"] ?>" alt="image de profil"></a></li>
        <li><a href="<?= (BASE_URL === "/") ? "" : BASE_URL ?>/logout" data-tooltip="Déconnexion" class="secondary"><img src="<?= (BASE_URL === "/") ? "" : BASE_URL ?>/public/image/logout.png" alt="deconnexion"></a></li>
    <?php else : ?>
        <!-- Menu déconnecté -->
        <li><a href="<?= (BASE_URL === "/") ? "" : BASE_URL ?>/register" data-tooltip="Créer un compte" class="secondary">Inscription</a></li>
        <li><a href="<?= (BASE_URL === "/") ? "" : BASE_URL ?>/login" data-tooltip="Se connecter" class="secondary">Connexion</a></li>
    <?php endif ?>
    </ul>
</nav>