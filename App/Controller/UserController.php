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

    public function addUser() {
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
                    //Set et hash du mot de passe
                    $this->user->setFirstname($firstname);
                    $this->user->setLastname($lastname);
                    $this->user->setPassword($password);
                    $this->user->hashPassword();
                    //ajoute le compte en BDD
                    $this->user->saveUser();
                    $message = "Le compte : " . $this->user->getEmail() . " a été ajouté en BDD";
                } else {
                
                    $message = "Le compte existe déja";
                }
                
            } else {
                $message ="Veuillez remplir tous les champs";
            }
        }
        
        include "App/View/viewRegisterUser.php";
    }
}
