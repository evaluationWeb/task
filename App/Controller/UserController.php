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
                    if( !empty($_FILES["img"]["tmp_name"])) {
                        
                        //récupération du chemin temporaire
                        $tmp = $_FILES["img"]["tmp_name"];
                        //récupération de nom par défault
                        $defaultName = $_FILES["img"]["name"];
                        //récupération du format de l'image
                        $format = Utilitaire::getFileExtension($defaultName);
                        //nouveau nom 
                        $newImgName = $firstname . $lastname . "." . $format;
                        //enregistrement de l'image
                        move_uploaded_file($tmp,".." . BASE_URL . "/public/image/" . $newImgName);
                        //set name de l'image
                        $this->user->setImg($newImgName);
                    }
                    else {
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

        include "App/View/viewRegisterUser.php";
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
        include "App/View/viewConnexion.php";
    }

    public function deconnexion()
    {
        session_destroy();
        header('Location: /task/');
    }
}
