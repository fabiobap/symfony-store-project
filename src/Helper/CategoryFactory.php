<?php

namespace App\Helper;

use App\Entity\Category;

class CategoryFactory{
    
    public function createCategory(string $json): Category{
        
        $jsonData = json_decode($json);
        $category = new Category();
        $category
            ->setName($jsonData->name);
        
        return $category;
    }
}