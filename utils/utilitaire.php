<?php

//Supprimer les balises + les caractères spéciaux, suppression des espaces
function sanitize(string $value) {
    
    return htmlspecialchars(strip_tags(trim($value)), ENT_NOQUOTES);
}
