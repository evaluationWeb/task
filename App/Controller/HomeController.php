<?php

namespace App\Controller;

use App\Service\EmailService;

class HomeController
{
    private readonly EmailService $emailService;

    public function __construct()
    {
        $this->emailService = new EmailService();
    }

    public function home()
    {
        $name = $_GET["name"] ?? "";
        include "App/View/viewHome.php";
    }

    public function error404()
    {
        http_response_code(404);
        include "App/View/viewError404.php";
    }

    public function unauthorized()
    {
        http_response_code(401);
        include "App/View/viewUnauthorized.php";
    }
    /**
     * Méthode de test pour envoyer un email
     */
    public function testEmail()
    {
        /*----------------------------------
        --------Composants de l'email-------
        ----------------------------------*/

        // 1 Objet de l'email
        $subject = "test de mail";

        // 2 Contenu de l'email au format HTML en heredoc
        $body = <<<HTML
        <h1>Test de mail</h1>
        <a href="https://www.google.com">google</a>
        HTML;

        //3 Destinataire de l'email (même que le compte configuré)
        $receiver = SMTP_LOGIN;

        try {

            //Envoi de l'email
            $this->emailService->sendMail($receiver, $subject, $body);
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }
}
