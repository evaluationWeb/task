<?php

namespace App\Controller;

use App\Model\User;
use App\Repository\UserRepository;
use App\Service\EmailService;
use App\Utils\Tools;

class UserController
{
    private readonly UserRepository $userRepository;

    private readonly EmailService $emailService;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->emailService = new EmailService();
    }

    /**
     * Méthode qui gére l'ajout d'un nouvel utilisateur en BDD
     * @return void affiche et gére la page d'inscription
     */
    public function addUser()
    {
        $base = (BASE_URL === "/") ? "" : BASE_URL;
        $message = "";
        //Test si le formulaire est submit
        if (isset($_POST["submit"])) {
            if (!empty($_POST["firstname"]) && !empty($_POST["lastname"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {
                $user = new User();
                $email = Tools::sanitize($_POST["email"]);
                $user->setEmail($email);

                if (!$this->userRepository->isUserByEmailExist($user)) {
                    //Sanitize des autres valeur
                    $firstname = Tools::sanitize($_POST["firstname"]);
                    $lastname = Tools::sanitize($_POST["lastname"]);
                    $password = Tools::sanitize($_POST["password"]);
                    //Test si l'utilisateur à ajouter une image
                    if (!empty($_FILES["img"]["tmp_name"])) {

                        //récupération du chemin temporaire
                        $tmp = $_FILES["img"]["tmp_name"];
                        //récupération de nom par défault
                        $defaultName = $_FILES["img"]["name"];
                        //récupération du format de l'image
                        $format = Tools::getFileExtension($defaultName);
                        //nouveau nom 
                        $newImgName = uniqid("user") . $firstname . $lastname . "." . $format;
                        //enregistrement de l'image
                        move_uploaded_file($tmp, ".." . $base . "/public/image/" . $newImgName);
                        //set name de l'image
                        $user->setImg($newImgName);
                    } else {
                        //Set default image
                        $user->setImg("profil.png");
                    }
                    //Set et hash du mot de passe
                    $user->setFirstname($firstname);
                    $user->setLastname($lastname);
                    $user->setPassword($password);
                    $user->hashPassword();
                    $user->addGrant("ROLE_USER");
                    //ajoute le compte en BDD
                    $this->userRepository->saveUser($user);
                    //Message et redirection
                    $message = "Le compte : " . $user->getEmail() . " a été ajouté en BDD";
                    header("Refresh:2; url=" . $base ."/user/register");
                } else {

                    $message = "Le compte existe déja";
                    header("Refresh:2; url=" . $base . "/user/register");
                }
            } else {
                $message = "Veuillez remplir tous les champs";
                header("Refresh:2; url=" . $base . "/user/register");
            }
        }

        include_once "App/View/viewRegisterUser.php";
    }

    /**
     * Méthode qui gére la connexion d'un utilisateur
     * @return void affiche et gére la page de connexion
     */
    public function connexion()
    {
        $base = (BASE_URL === "/") ? "" : BASE_URL;
        $message = "";
        if (isset($_POST["submit"])) {
            if (!empty($_POST["email"]) && !empty($_POST["password"])) {
                $email = Tools::sanitize($_POST["email"]);
                $password = Tools::sanitize($_POST["password"]);
                $user = new User();
                $user->setEmail($email);
                $user->setPassword($password);
                //Test si le compte existe
                if ($this->userRepository->isUserByEmailExist($user)) {
                    //récupération du compte en BDD
                    $userConnected = $this->userRepository->findUserByEmail($user);
                    //test si le password est identique
                    if ($user->passwordVerify($userConnected->getPassword())) {

                        //initialiser les super gobale de la SESSION
                        $_SESSION["connected"] = true;
                        $_SESSION["email"] = $email;
                        $_SESSION["id"] = $userConnected->getIdUser();
                        $_SESSION["img"] = $userConnected->getImg();
                        //récupération de la liste de droits
                        $_SESSION["grant"] = $userConnected->getGrant();
                        //redirection vers accueil connecté
                        header('Location: ' . BASE_URL . '');
                    } else {
                        $message = "Les informations de connexion ne sont pas correctes";
                        header("Refresh:2; url=" . $base . "/user/connexion");
                    }
                } else {
                    $message = "Les informations de connexion ne sont pas correctes";
                    header("Refresh:2; url=" . $base . "/user/connexion");
                }
            } else {
                $message = "Veuillez remplir les champs";
               header("Refresh:2; url=" . $base . "/user/connexion");
            }
        }
        include_once "App/View/viewConnexion.php";
    }

    /**
     * Méthode qui déconnecte un utilisateur (coupe la session)
     * @return void déconnecte et retourne à la page d'accueil
     */
    public function deconnexion()
    {
        session_destroy();
        header('Location: /task/');
    }

    /**
     * Méthode qui affiche les informations de profil de l'utilisateur connecté
     * @return void affiche et gére la page de profil de l'utilisateur connecté
     */
    public function showUserProfile()
    {
        //Récupération et nettoyage de la super globale session
        $email = Tools::sanitize($_SESSION["email"]);

        //setter l'email à l'objet User
        $user = new User();
        $user->setEmail($email);

        //Récupération du compte 
        $userConnected = $this->userRepository->findUserByEmail($user);

        //Retourne la vue HTML
        include_once "App/View/viewUserProfil.php";
    }

    /**
     * Méthode qui permet le changement du mot de passe de l'utilisateur connecté
     * @return void affiche et gére la page de changement du mot de passe 
     * de l'utilisateur connecté
     */
    public function modifyPassword()
    {   
        $base = (BASE_URL === "/") ? "" : BASE_URL;
        //Test si le formulaire est soumis
        if (isset($_POST["submit"])) {
            //Test si tous les champs sont remplis
            if (!empty($_POST["oldPassword"]) && !empty($_POST["newPassword"]) && !empty($_POST["confirmPassword"])) {
                //récupération et nettoyage des informations
                $oldPassword = Tools::sanitize($_POST["oldPassword"]);
                $newPassword = Tools::sanitize($_POST["newPassword"]);
                $confirmPassword = Tools::sanitize($_POST["confirmPassword"]);
                $email = Tools::sanitize($_SESSION["email"]);
                //Test si les 2 nouveaux mots de passe sont identiques
                if ($newPassword === $confirmPassword) {
                    //set de l'email
                    $user = new User();
                    $user->setEmail($email);
                    //Test si le compte existe
                    if ($this->userRepository->isUserByEmailExist($user)) {
                        //récupération du compte depuis son email
                        $oldUser = $this->userRepository->findUserByEmail($user);
                        //récupération de l'ancien hash
                        $oldHash = $oldUser->getPassword();
                        //test si l'ancien mot de passe est valide
                        if (password_verify($oldPassword, $oldHash)) {
                            //set du nouveau mot de passe
                            $user->setPassword($newPassword);
                            //Hash du nouveau mot de passe
                            $user->hashPassword();
                            //mise à jour du mot de passe
                            $this->userRepository->updatePassword($user);
                            $message = "Le mot de passe à été mis à jour";
                            header("Refresh:2; url=" .  $base ."/user/deconnexion");
                        } else {
                            $message = "L'ancien mot de passe est incorrect";
                            header("Refresh:2; url=" . $base . "/user/update/password");
                        }
                    } else {
                        $message = "Le compte n'existe pas";
                        header("Refresh:2; url=" .  $base ."/user/deconnexion");
                    }
                } else {
                    $message = "Les 2 nouveaux mots de passe ne correspondent pas";
                    header("Refresh:2; url=" . $base . "/user/update/password");
                }
            } else {
                $message = "Veuillez remplir tous les champs du formulaire";
                 header("Refresh:2; url=" . $base . "/user/update/password");
            }
        }
        include_once "App/View/viewModifyPassword.php";
    }

    /**
     * Méthode qui permet le remplacement de l'image de profil de l'utilisateur connecté
     * @return void affiche et gére la page de remplacement de l'image de profil 
     * de l'utilisateur connecté
     */
    public function modifyImage()
    {
        $base = (BASE_URL === "/") ? "" : BASE_URL;
        //test si le formulaire est soumis
        if (isset($_POST["submit"])) {
            //test si l'image existe
            if (!empty($_FILES["img"]["tmp_name"])) {
                //récupération du chemin temporaire
                $tmp = $_FILES["img"]["tmp_name"];
                //récupération de nom par défault
                $defaultName = $_FILES["img"]["name"];
                //récupération du format de l'image
                $format = Tools::getFileExtension($defaultName);
                //set de l'email
                $user = new User();
                $user->setEmail(Tools::sanitize($_SESSION["email"]));
                //récupération des informations de l'utilisateur
                $userConnected = $this->userRepository->findUserByEmail($user);
                $newImgName = uniqid("user") . $userConnected->getFirstname() . $userConnected->getLastname() . "." . $format;
                //enregistrement de l'image
                move_uploaded_file($tmp, ".." . BASE_URL . "/public/image/" . $newImgName);
                //set de l'image
                $user->setImg($newImgName);
                //update du compte en BDD
                $this->userRepository->updateImage($user);
                //mise à jour de la session
                $_SESSION["img"] = $newImgName;
                //message de confirmation et redirection
                $message = "Image mise à jour";
                header("Refresh:1; url=" . $base . "/user/profil");
            } else {
                $message = "Veuillez sélectionner une image";
                header("Refresh:2; url=" . $base . "/user/update/img");
            }
        }

        include_once "App/View/viewModifyImage.php";
    }

    /**
     * Méthode qui permet la modification des informations du profil utilisateur connecté
     * @return void affiche et gére la page de modification des informations du profil 
     * de l'utilisateur connecté
     */
    public function modifyInfo()
    {
        $base = (BASE_URL === "/") ? "" : BASE_URL;
        //Test si le formulaire est submit
        if (isset($_POST["submit"])) {
            $user = new User();
            $user->setEmail(Tools::sanitize($_SESSION["email"]));
            $oldUserInfo = $this->userRepository->findUserByEmail($user);
            //Test si les champs sont remplis
            if (!empty($_POST["firstname"]) && !empty($_POST["firstname"]) && !empty($_POST["lastname"])) {
                //récupération et nettoyage des informations
                $firstname = Tools::sanitize($_POST["firstname"]);
                $lastname = Tools::sanitize($_POST["lastname"]);
                $email = Tools::sanitize($_POST["email"]);
                $oldEmail = Tools::sanitize($_SESSION["email"]);
                //set de l'email
                $user->setEmail($email);
                //test si l'email n'existe pas déja
                if ($email != $oldEmail && $this->userRepository->isUserByEmailExist($user)) {

                    $message = "Attention l'email existe déja en BDD";
                    header("Refresh:1; url=" . $base . "/user/profil");
                } else {
                    //set du prénon et du nom
                    $user->setFirstname($firstname);
                    $user->setLastname($lastname);
                    //Mise à jour du compte
                    $this->userRepository->updateInformation($user, $oldEmail);
                    //Mise à jour de la session
                    $_SESSION["email"] = $user->getEmail();
                    $oldUserInfo = $this->userRepository->findUserByEmail($user);
                    //Message de confirmation et redirection
                    $message = "Le compte a été mis à jour";
                    header("Refresh:1; url=" . $base . "/user/profil");
                }
            } else {
                $message = "Veuillez renseigner tous les champs";
                header("Refresh:1; url=" . $base . "/user/update/info");
            }
        } else {
            $user = new User();
            //Récupération des anciennes valeurs
            $user->setEmail(Tools::sanitize($_SESSION["email"]));
            $oldUserInfo = $this->userRepository->findUserByEmail($user);
        }

        include_once "App/View/viewModifyUserProfil.php";
    }

    /**
     * Méthode qui permet la demande de régénération du mot de passe oublié
     * @return void affiche et gére la page de demande de régénération du mot de passe oublié
     * Envoi un email pour récupérer le mot de passe 
     */
    public function recoverPassword()
    {
        $base = (BASE_URL === "/") ? "" : BASE_URL;
        //Test si le fomulaire est soumis
        if (isset($_POST["submit"])) {
            //Test si l'email est renseigné
            if (!empty($_POST["email"])) {
                //Nettoyage du champ
                $email = Tools::sanitize($_POST["email"]);
                //Timestamp pour la durée de vie du lien
                $date = new \DateTimeImmutable();
                $dateValidity = $date->getTimestamp();
                $hashEmail = md5($email);
                $link = "http://localhost" . $base . "/user/password/generate?email=$hashEmail&validity=$dateValidity";
                //composants de l'email à envoyer
                $receiver = $email;
                $subject = "Recuperation du password";
                $body = <<<HTML
                    <p>Cliquer sur le lien pour re creer votre mot de passe</p>
                    <a href='$link'>
                        Re creer votre mot de passe
                    </a>
                    <p>Attention le lien est valide pendant 2H00 !</p>
                    HTML;
                //envoi de l'email
                $this->emailService->sendMail($receiver, $subject, $body);
                $message = "envoi d'un email pour re créer votre mot de passe";
                header("Refresh:2; url=" . BASE_URL . "");
            }
        }
        include_once "App/View/viewRecoverPassword.php";
    }

    /**
     * Méthode qui va régénérer votre mot de passe oublié
     * @return void affiche et gére la page qui permet de re créer un mot de passe oublié
     */
    public function regeneratePassword()
    {
         $base = (BASE_URL === "/") ? "" : BASE_URL;
        //test si les paramètres GET existent
        if (isset($_GET["email"]) && isset($_GET["validity"])) {
            //Nettoyage du hash de l'email (GET)
            $hashEmail = Tools::sanitize($_GET["email"]);
            //Nettoyage de la date de validitée (GET)
            $dateValidity = (int) Tools::sanitize($_GET["validity"]);
            //test si le formulaire est submit
            if (isset($_POST["submit"])) {
                //test si les champs sont tous renseignés
                if (!empty($_POST["newPassword"]) && !empty($_POST["confirmPassword"])) {
                    //Créer un objet DateTimeImmutable depuis le timestamp $dateValidity
                    $dateValidity = (new \DateTimeImmutable())->setTimestamp($dateValidity);
                    //Ajouter 24h
                    $dateValidityPlus2Hours = $dateValidity->modify('+2 hours');
                    //test si la date est toujours valide (- de 2h00)
                    if (new \DateTimeImmutable() < $dateValidityPlus2Hours) {
                        //set de l'email en MD5 au User
                        $user = new User();
                        $user->setEmail($hashEmail);
                        //test si l'email est valide
                        if ($this->userRepository->isUserByHashEmailExist($user)) {
                            //récupération et nettoyage du password
                            $newPassword = Tools::sanitize($_POST["newPassword"]);
                            //set du password au User
                            $user->setPassword($newPassword);
                            //hash du password
                            $user->hashPassword();
                            //Mise à jour du mot de passe
                            $this->userRepository->updateForgotPassword($user);
                            //Message de confirmation et redirection
                            $message = "Le mot de passe à été modifié";
                            header("Refresh:2; url=" . $base . "/user/connexion");
                        }
                        //Sinon on arrête
                        else {
                            $message = "L'email n'est pas valide";
                            header("Refresh:2; url=" . BASE_URL . "");
                        }
                    }
                    //Sinon la date est dépassée
                    else {
                        $message = "Le temps est dépassé refaire la demande";
                        header("Refresh:2; url=" . BASE_URL . "");
                    }
                }
            }
        }
        //Sinon redirige vers l'accueil
        else {
            header("Location:" . BASE_URL . "");
        }
        include_once "App/View/viewRegeneratePassword.php";
    }
}
