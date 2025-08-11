# task
Ce site est un projet d'exercice de cours d'une TODO List en PHP POO.
Le repository contient les corrections de celui-ci.

**Prérequis** :
- PHP 8.2 +,
- Apache,
- BDD Mysql ou MariaDB,
- composer 2.7 +,
- Framework CSS pico css (*falcultatif*),

Lien de téléchargement de Pico CSS :
[pico css](https://picocss.com/)

- 1 Cloner le repository.
- 2 Créer à la racine un fichier **env.php**
avec une struture similaire :
```php
<?php
    const BDD_LOGIN = "mon login de BDD";
    const BDD_PASSWORD = "mon password de BDD";
    const BDD_SERVER = "mon url serveur de BDD";
    const BDD_NAME = "mon nom de BDD";
    const BASE_URL = "/dossier_du_projet"
```
Remplacer par vos propres valeurs (BDD et racine du projet)

- 3 Editer le fichier **.htaccess** et remplacer le chemin par votre dossier racine
dans **RewriteBase /dossier**
C'est le dossier racine de votre projet dans **htdocs** ou **www**
- 4 Saisir la commande suivante dans un terminal :
```sh
composer install
```