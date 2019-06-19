<?php

namespace App\Controller;

use App\Entity\Product;
use App\Helper\ProductFactory;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController{
    
    /**
     * @var EntityManagerInterface
     */
    private $em;    
    /**
     * @var ProductFactory
     */
    private $productFactory;    
    /**
     * @var ProductRepository
     */
    private $repository;
    
    public function __construct(EntityManagerInterface $em, ProductFactory $productFactory, ProductRepository $repository){
        $this->em = $em;
        $this->productFactory = $productFactory;
        $this->repository = $repository;
    }
    
    /**
    * @Route("/products", methods={"POST"})
    */
    public function newProduct(Request $request): Response{
      
        $requestBody = $request->getContent();
        
        $product = $this
            ->productFactory
            ->createProduct($requestBody);
        $this->em->persist($product);
        $this->em->flush();
        
        return new JsonResponse($product);
    }
    /**
    * @Route("/products", methods={"GET"})
    */
    public function productsList(): Response{
        
        $list = $this->repository->findAll();
        
        return new JsonResponse($list);
    }
    /**
    * @Route("/products/{id}", methods={"GET"})
    */
    public function getProduct(int $id): Response{
        
        $product = $this->repository->find($id);
        
        $returnCode = is_null($product) ? Response::HTTP_NO_CONTENT : 200;
        
        return new JsonResponse($product, $returnCode);
    }
    
    /**
    * @Route("/products/{id}", methods={"PUT"})
    */   
    public function updateProduct(int $id, Request $request): Response{
        
        $requestBody = $request->getContent();

        $sentProduct = $this
            ->productFactory
            ->createProduct($requestBody);

        $existingProduct = $this
            ->repository
            ->find($id);
        
        if(is_null($existingProduct)){
            return new Response("", Response::HTTP_NOT_FOUND);
        }
        
        $existingProduct
            ->setName($sentProduct->getName())
            ->setPrice($sentProduct->getPrice())
            ->setDescription($sentProduct->getDescription())
            ->setCategory($sentProduct->getCategory());
        
        $this->em->flush();
        return new JsonResponse($existingProduct);
    }
    
    /**
    * @Route("/products/{id}", methods={"DELETE"})
    */   
    public function remove(int $id): Response{
        
        $product = $this->repository->find($id);
        
        $this->em->remove($product);
        $this->em->flush();
        
        return new Response('', Response::HTTP_NO_CONTENT);
    }
    
}