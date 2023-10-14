<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    
    /**
     * @Route("/blog", name="blog_list")
     */
    public function getBookList(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'welcome to your new controller!',
            'path' => 'src/Controller/BookController.php',
        ]);
    }

    #[Route('/test', name: 'book', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'test la route!',
            'path' => 'src/Controller/BookController.php',
        ]);
    }
}
