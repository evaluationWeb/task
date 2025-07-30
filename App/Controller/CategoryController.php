<?php

namespace App\Controller;

use App\Model\Category;

class CategoryController {

    //Attribut Model Category
    private Category $category;

    public function __construct()
    {   
        //Injection de dépendance
        $this->category = new Category();
    }

    public function showAllCategory() {

        $categories = $this->category->findAllCategory();
        include "App/View/viewAllCategory.php";
    }
}