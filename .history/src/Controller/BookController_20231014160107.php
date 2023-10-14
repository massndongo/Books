<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    
    /**
     * @Route("/api/books", name="book", methods={"GET"})
     */
    public function getBookList(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $bookList = $bookRepository -> findAll();
        $jsonBookList = $serializer -> ser
        return new JsonResponse([
            'books' => $bookList 
        ]);
    }
}
