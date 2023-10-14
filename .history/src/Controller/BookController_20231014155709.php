<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    
    /**
     * @Route("/api/books", name="book", methods={"GET"})
     */
    public function getBookList(BookRepository $bookRepository, Ser $serializer): JsonResponse
    {
        $bookList = $bookRepository -> findAll();
        return new JsonResponse([
            'books' => $bookList 
        ]);
    }
}
