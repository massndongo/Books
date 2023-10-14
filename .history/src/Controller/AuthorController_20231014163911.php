<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthorController extends AbstractController
{
    
    /**
     * @Route("/api/authors", name="author", methods={"GET"})
     */
    public function getBookList(AuthorRepository $authorRepository, SerializerInterface $serializer): JsonResponse
    {
        $authorList = $authorRepository -> findAll();
        $jsonBookList = $serializer -> serialize($bookList, 'json', ['groups' => 'getBooks']);
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }


    /**
     * @Route("/api/books/{id}", name="detailBook", methods={"GET"})
     */
    public function getDetailBook(int $id, Book $book, SerializerInterface $serializer, BookRepository $bookRepository): JsonResponse {

        $jsonBook = $serializer -> serialize($book, 'json', ['groups' => 'getBooks']);
        return new JsonResponse($jsonBook, Response::HTTP_OK, ['accept' => 'json'], true);
        // $book = $bookRepository->find($id);
        // if ($book) {
        //     $jsonBook = $serializer->serialize($book, 'json');
        //     return new JsonResponse($jsonBook, Response::HTTP_OK, [], true);
        // }
        // return new JsonResponse(null, Response::HTTP_NOT_FOUND);
   }
}
