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
}
