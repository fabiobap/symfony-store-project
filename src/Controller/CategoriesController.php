<?php

namespace App\Controller;

use App\Entity\Category;
use App\Helper\CategoryFactory;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController{
    
    /**
     * @var EntityManagerInterface
     */
    private $em;    
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;    
    /**
     * @var CategoryRepository
     */
    private $repository;
    
    public function __construct(EntityManagerInterface $em, CategoryFactory $categoryFactory, CategoryRepository $repository){
        $this->em = $em;
        $this->categoryFactory = $categoryFactory;
        $this->repository = $repository;
    }
    
    /**
    * @Route("/categories", methods={"POST"})
    */
    public function newCategory(Request $request): Response{
      
        $requestBody = $request->getContent();
        
        $category = $this
            ->categoryFactory
            ->createCategory($requestBody);
        
        $this->em->persist($category);
        $this->em->flush();
        
        return new JsonResponse($category);
    }
    /**
    * @Route("/categories", methods={"GET"})
    */
    public function categoriesList(): Response{
        
        $list = $this->repository->findAll();
        
        return new JsonResponse($list);
    }
    /**
    * @Route("/categories/{id}", methods={"GET"})
    */
    public function getCategory(int $id): Response{
        
        $category = $this->repository->find($id);
        
        $returnCode = is_null($category) ? Response::HTTP_NO_CONTENT : 200;
        
        return new JsonResponse($category, $returnCode);
    }
    
    /**
    * @Route("/categories/{id}", methods={"PUT"})
    */   
    public function updateCategory(int $id, Request $request): Response{
        
        $requestBody = $request->getContent();

        $sentCategory = $this
            ->categoryFactory
            ->createCategory($requestBody);
        
        $existingCategory = $this
            ->repository
            ->find($id);
        
        if(is_null($existingCategory)){
            return new Response("", Response::HTTP_NOT_FOUND);
        }
        
        $existingCategory
            ->setName($sentCategory->getName());
        
        $this->em->flush();
        return new JsonResponse($existingCategory);
    }
    
    /**
    * @Route("/categories/{id}", methods={"DELETE"})
    */   
    public function remove(int $id): Response{
        
        $category = $this->repository->find($id);
        
        $this->em->remove($category);
        $this->em->flush();
        
        return new Response('', Response::HTTP_NO_CONTENT);
    }
    
}