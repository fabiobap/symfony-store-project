<?php

namespace App\Helper;

use App\Entity\Product;
use App\Repository\CategoryRepository;

class ProductFactory{
    /**
    * @var CategoryRepository
    */
    private $categoryRepository;
    public function __construct(CategoryRepository $categoryRepository){
        
        $this->categoryRepository = $categoryRepository;
        
    }
    public function createProduct(string $json): Product{
        
        $jsonData = json_decode($json);
        
        $categoryId = $jsonData->categoryId;
        $category = $this
            ->categoryRepository
            ->find($categoryId);
        
        $product = new Product();
        $product
            ->setName($jsonData->name)
            ->setPrice($jsonData->price)
            ->setDescription($jsonData->description)
            ->setCategory($category);
        
        return $product;
    }
}