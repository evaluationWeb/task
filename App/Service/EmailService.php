<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService
{

    //Attribut
    private readonly PHPMailer $mail;

    //Constructeur
    public function __construct()
    {
        $this->mail = new PHPMailer(true);
    }

    //Méthodes
    
    /**
     * Méthode pour envoyer un email
     * @param string $receiver email du destinataire
     * @param string $subject objet de l'email
     * @param string $body corp de l'email en HTML (string)
     * @return string retourne que le mail à été envoyé avec succés
     * @throws \Exception retourne une exception avec l'erreur  
     */
    public function sendMail(string $receiver, string $subject, string $body): string
    {
        try {
            //Application de la configuration (serveur SMTP)
            $this->config();
            //Email de l'expéditeur (même que le compte SMTP)
            $this->mail->setFrom(SMTP_LOGIN, 'Mailer');
            //Email du Destinataire
            $this->mail->addAddress($receiver);

            //Contenu de l'email
            $this->mail->isHTML(true);
            //Objet de l'email
            $this->mail->Subject = $subject;
            //Contenu de l'email
            $this->mail->Body    = $body;
            //envoi de l'email
            $this->mail->send();
            return 'Message has been sent';
        } catch (Exception $e) {
            throw new \Exception($this->mail->ErrorInfo);
        }
    }
    /**
     * Méthode pour configurer le serveur SMTP (envoi de mail)
     * @var string utilise les constantes dans le fichier env.php
     */
    private function config()
    {
        //Server settings
        $this->mail->SMTPDebug = SMTP::DEBUG_OFF; //SMTP::DEBUG_SERVER pour activer le debug SMTP::DEBUG_OFF pour désactiver les logs
        $this->mail->isSMTP();
        $this->mail->Host       = SMTP_SERVER;
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = SMTP_LOGIN;
        $this->mail->Password   = SMTP_PASSWORD;
        $this->mail->SMTPSecure = SMTP_SECURITY;
        $this->mail->Port       = SMTP_PORT;
    }
}
