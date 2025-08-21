<?php

namespace App\Utils;

use App\Model\Category;

class Utilitaire {

    /**
     * Méthode pour Supprimer les balises + les caractères spéciaux, suppression des espaces
     * @param string $value la chaine à nettoyer
     * @return string chaine néttoyer 
    */
    public static function sanitize(string $value) : string {
    
        return htmlspecialchars(strip_tags(trim($value)), ENT_NOQUOTES);
    }

    /**
     * Méthode qui retourne l'extension d'un fichier
     * @param string $file nom du fichier
     * @return string extension du fichier
     */
    public static function getFileExtension($file){
        return substr(strrchr($file,'.'),1);
    }

    //Méthode qui deshydrate l'objet User en tableau
    public static function toArray(Category $category) :array {
        $data = [];
        foreach ($category as $key => $value) {
            $method = 'get' . ucfirst($key);
            if (method_exists($category, $method)) {
                $user[$key] = $category->$method();
            }
        }
        return $data;         
    }
}
