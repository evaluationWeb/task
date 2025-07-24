<?php

function connectBDD() {
    //variable 
    include "../env.php";
    return new PDO('mysql:host=' . BDD_SERVER . ';dbname=' . BDD_NAME .'',
        BDD_LOGIN,
        BDD_PASSWORD, 
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
}

connectBDD();