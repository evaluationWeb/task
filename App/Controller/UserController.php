<?php

namespace App\Controller;

use App\Model\User;
use App\Utils\Utilitaire;

class UserController
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function addUser()
    {
        $message = "";
        //Test si le formulaire est submit
        if (isset($_POST["submit"])) {
            if (!empty($_POST["firstname"]) && !empty($_POST["lastname"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {

                $email = Utilitaire::sanitize($_POST["email"]);
                $this->user->setEmail($email);

                if (!$this->user->isUserByEmailExist()) {
                    //Sanitize des autres valeur
                    $firstname = Utilitaire::sanitize($_POST["firstname"]);
                    $lastname = Utilitaire::sanitize($_POST["lastname"]);
                    $password = Utilitaire::sanitize($_POST["password"]);
                    //Test si l'utilisateur à ajouter une image
                    if (!empty($_FILES["img"]["tmp_name"])) {

                        //récupération du chemin temporaire
                        $tmp = $_FILES["img"]["tmp_name"];
                        //récupération de nom par défault
                        $defaultName = $_FILES["img"]["name"];
                        //récupération du format de l'image
                        $format = Utilitaire::getFileExtension($defaultName);
                        //nouveau nom 
                        $newImgName = uniqid("user") . $firstname . $lastname . "." . $format;
                        //enregistrement de l'image
                        move_uploaded_file($tmp, ".." . BASE_URL . "/public/image/" . $newImgName);
                        //set name de l'image
                        $this->user->setImg($newImgName);
                    } else {
                        //Set default image
                        $this->user->setImg("profil.png");
                    }
                    //Set et hash du mot de passe
                    $this->user->setFirstname($firstname);
                    $this->user->setLastname($lastname);
                    $this->user->setPassword($password);
                    $this->user->hashPassword();
                    //ajoute le compte en BDD
                    $this->user->saveUser();
                    $message = "Le compte : " . $this->user->getEmail() . " a été ajouté en BDD";
                    header("Refresh:2; url=/task/user/register");
                } else {

                    $message = "Le compte existe déja";
                    header("Refresh:2; url=/task/user/register");
                }
            } else {
                $message = "Veuillez remplir tous les champs";
                header("Refresh:2; url=/task/user/register");
            }
        }

        include_once "App/View/viewRegisterUser.php";
    }

    public function connexion()
    {
        $message = "";
        if (isset($_POST["submit"])) {
            if (!empty($_POST["email"]) && !empty($_POST["password"])) {
                $email = Utilitaire::sanitize($_POST["email"]);
                $password = Utilitaire::sanitize($_POST["password"]);
                $this->user->setEmail($email);
                $this->user->setPassword($password);
                //Test si le compte existe
                if ($this->user->isUserByEmailExist()) {
                    //récupération du compte en BDD
                    $userConnected = $this->user->findUserByEmail();

                    //test si le password est identique
                    if ($this->user->passwordVerify($userConnected->getPassword())) {

                        //initialiser les super gobale de la SESSION
                        $_SESSION["connected"] = true;
                        $_SESSION["email"] = $email;
                        $_SESSION["id"] = $userConnected->getIdUser();
                        $_SESSION["img"] = $userConnected->getImg();
                        header('Location: /task');
                    } else {
                        $message = "Les informations de connexion ne sont pas correctes";
                        header("Refresh:2; url=/task/user/connexion");
                    }
                } else {
                    $message = "Les informations de connexion ne sont pas correctes";
                    header("Refresh:2; url=/task/user/connexion");
                }
            } else {
                $message = "Veuillez remplir les champs";
                header("Refresh:2; url=/task/user/connexion");
            }
        }
        include_once "App/View/viewConnexion.php";
    }

    public function deconnexion()
    {
        session_destroy();
        header('Location: /task/');
    }

    public function showUserProfile()
    {
        //Récupération et nettoyage de la super globale session
        $email = Utilitaire::sanitize($_SESSION["email"]);

        //setter l'email à l'objet User
        $this->user->setEmail($email);

        //Récupération du compte 
        $userConnected = $this->user->findUserByEmail();

        //Retourne la vue HTML
        include_once "App/View/viewUserProfil.php";
    }

    public function modifyPassword()
    {
        //Test si le formulaire est soumis
        if (isset($_POST["submit"])) {
            //Test si tous les champs sont remplis
            if (!empty($_POST["oldPassword"]) && !empty($_POST["newPassword"]) && !empty($_POST["confirmPassword"])) {
                //récupération et nettoyage des informations
                $oldPassword = Utilitaire::sanitize($_POST["oldPassword"]);
                $newPassword = Utilitaire::sanitize($_POST["newPassword"]);
                $confirmPassword = Utilitaire::sanitize($_POST["confirmPassword"]);
                $email = Utilitaire::sanitize($_SESSION["email"]);
                //Test si les 2 nouveaux mots de passe sont identiques
                if ($newPassword === $confirmPassword) {
                    //set de l'email
                    $this->user->setEmail($email);
                    //Test si le compte existe
                    if ($this->user->isUserByEmailExist()) {
                        //récupération du compte depuis son email
                        $oldUser = $this->user->findUserByEmail();
                        //récupération de l'ancien hash
                        $oldHash = $oldUser->getPassword();
                        //test si l'ancien mot de passe est valide
                        if (password_verify($oldPassword, $oldHash)) {
                            //set du nouveau mot de passe
                            $this->user->setPassword($newPassword);
                            //Hash du nouveau mot de passe
                            $this->user->hashPassword();
                            //mise à jour du mot de passe
                            $this->user->updatePassword();
                            $message = "Le mot de passe à été mis à jour";
                            header("Refresh:2; url=/task/user/deconnexion");
                        } else {
                            $message = "L'ancien mot de passe est incorrect";
                            header("Refresh:2; url=/task/user/update/password");
                        }
                    } else {
                        $message = "Le compte n'existe pas";
                        header("Refresh:2; url=/task/user/deconnexion");
                    }
                } else {
                    $message = "Les 2 nouveaux mots de passe ne correspondent pas";
                    header("Refresh:2; url=/task/user/update/password");
                }
            } else {
                $message = "Veuillez remplir tous les champs du formulaire";
                header("Refresh:2; url=/task/user/update/password");
            }
        }
        include_once "App/View/viewModifyPassword.php";
    }

    public function modifyImage()
    {
        //test si le formulaire est soumis
        if (isset($_POST["submit"])) {
            //test si l'image existe
            if (!empty($_FILES["img"]["tmp_name"])) {
                //récupération du chemin temporaire
                $tmp = $_FILES["img"]["tmp_name"];
                //récupération de nom par défault
                $defaultName = $_FILES["img"]["name"];
                //récupération du format de l'image
                $format = Utilitaire::getFileExtension($defaultName);
                //set de l'email
                $this->user->setEmail(Utilitaire::sanitize($_SESSION["email"]));
                //récupération des informations de l'utilisateur
                $userConnected = $this->user->findUserByEmail();
                $newImgName = uniqid("user") . $userConnected->getFirstname() . $userConnected->getLastname() . "." . $format;
                //enregistrement de l'image
                move_uploaded_file($tmp, ".." . BASE_URL . "/public/image/" . $newImgName);
                //set de l'image
                $this->user->setImg($newImgName);
                //update du compte en BDD
                $this->user->updateImage();
                //mise à jour de la session
                $_SESSION["img"] = $newImgName;
                //message de confirmation et redirection
                $message = "Image mise à jour";
                header("Refresh:1; url=/task/user/profil"); 
            } else {
                $message = "Veuillez sélectionner une image";
                header("Refresh:2; url=/task/user/update/img"); 
            }
        }

        include_once "App/View/viewModifyImage.php";
    }

    public function modifyInfo() {
        //Test si le formulaire est submit
        if (isset($_POST["submit"])) {
            $this->user->setEmail(Utilitaire::sanitize($_SESSION["email"]));
            $oldUserInfo = $this->user->findUserByEmail();
            //Test si les champs sont remplis
            if (!empty($_POST["firstname"]) && !empty($_POST["firstname"]) && !empty($_POST["lastname"])) {
                //récupération et nettoyage des informations
                $firstname = Utilitaire::sanitize($_POST["firstname"]);
                $lastname = Utilitaire::sanitize($_POST["lastname"]);
                $email = Utilitaire::sanitize($_POST["email"]);
                $oldEmail = Utilitaire::sanitize($_SESSION["email"]);
                //set de l'email
                $this->user->setEmail($email);
                //test si l'email n'existe pas déja
                if ($email != $oldEmail && $this->user->isUserByEmailExist()) {

                    $message = "Attention l'email existe déja en BDD";
                    header("Refresh:1; url=/task/user/profil");
                } else {
                    //set du prénon et du nom
                    $this->user->setFirstname($firstname);
                    $this->user->setLastname($lastname);
                    //Mise à jour du compte
                    $this->user->updateInformation($oldEmail);
                    //Mise à jour de la session
                    $_SESSION["email"] = $this->user->getEmail();
                    $oldUserInfo = $this->user->findUserByEmail();
                    //Message de confirmation et redirection
                    $message = "Le compte a été mis à jour";
                    header("Refresh:1; url=/task/user/profil"); 
                }
            } else {
                $message = "Veuillez renseigner tous les champs";
                header("Refresh:1; url=/task/user/update/info"); 
            }
        } else {
            //Récupération des anciennes valeurs
            $this->user->setEmail(Utilitaire::sanitize($_SESSION["email"]));
            $oldUserInfo = $this->user->findUserByEmail();
        }

        include_once "App/View/viewModifyUserProfil.php";
    }
}
