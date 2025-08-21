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
- 5 Mise en place de l'envoi d'email

Saisir la commande suivante pour télécharger la librairie (si elle n'est pas déja installée)
```sh
composer require phpmailer/phpmailer
```

- 6 Editer la configuration du projet (env.php) comme ci-dessous :
```php
    const SMTP_SERVER = "url du serveur smtp";
    const SMTP_PORT = "numéro de port du serveur smtp";
    const SMTP_SECURITY = "Sécurité SMTP par ex :  tls ou ssl";
    const SMTP_LOGIN = "login du compte email";
    const SMTP_PASSWORD = "password du compte email";
```
- 7 si votre configuration est correcte vous pouvez tester l'envoi d'un email avec : 

La route suivante : 

http://localhost/dossier_de_votre_projet/test/email

```txt
Elle va envoyer un email à votre boite (config SMTP)
Vous pourez voir les logs de l'envoi du mail 
de la configuration de :

$this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
Dans la méthode config du service

Si vous voulez désactiver les logs passer la valeur à :
SMTP::DEBUG_OFF
```
