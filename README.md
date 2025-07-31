# task

créer à la racine un fichier env.php
avec une struture similaire :
```php
<?php
    const BDD_LOGIN = "mon login de BDD";
    const BDD_PASSWORD = "mon password de BDD";
    const BDD_SERVER = "mon url serveur de BDD";
    const BDD_NAME = "mon nom de BDD";
    const BASE_URL = "/dossier_du_projet"
```
saisir la commande suivante dans un terminal :
```sh
composer install
```
éditer le fichier **.htaccess** et remplacer le chemin de vôtre dossier racine
dans rewrite base /dossier
