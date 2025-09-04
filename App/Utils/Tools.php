<?php

namespace App\Utils;

class Tools
{

    /**
     * Méthode pour Supprimer les balises + les caractères spéciaux, suppression des espaces
     * @param string $value la chaine à nettoyer
     * @return string chaine néttoyer 
     */
    public static function sanitize(string $value): string
    {

        return htmlspecialchars(strip_tags(trim($value)), ENT_NOQUOTES);
    }

    /**
     * Méthode qui retourne l'extension d'un fichier
     * @param string $file nom du fichier
     * @return string extension du fichier
     */
    public static function getFileExtension($file)
    {
        return substr(strrchr($file, '.'), 1);
    }

    //Méthode qui convertie une chaine de caractéres en UTF-8
    public static function utf8Encode(string $str): string
    {

        return mb_convert_encoding(
            $str,
            "UTF-8",
            mb_detect_encoding($str)
        );
    }

    /**
     * Méthode qui test si l'utilisateur à le droit d'utiliser
     * une fonctionnalité
     * @param string $grant droit à vérifier
     * @return bool true si le droit est vérifié sinon false
     */
    public static function checkGrants (string $grant) : bool {
        $users = $_SESSION["grant"];
        //boucle parcours des droits de l'utilisateur
        foreach ($users as $userGrant) {    
            if ( $userGrant == $grant) {
                return true;
            }    
        }
        return false;
    }
}
