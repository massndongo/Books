<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AuthorController extends AbstractController
{
    
    /**
     * @Route("/api/authors", name="author", methods={"GET"})
     */
    public function getBookList(AuthorRepository $authorRepository, SerializerInterface $serializer): JsonResponse
    {
        $authorList = $authorRepository -> findAll();
        $jsonAuthorList = $serializer -> serialize($authorList, 'json', ['groups' => 'getBooks']);
        return new JsonResponse($jsonAuthorList, Response::HTTP_OK, [], true);
    }


    /**
     * @Route("/api/authors/{id}", name="detailAuthor", methods={"GET"})
     */
    public function getDetailBook(Author $book, SerializerInterface $serializer): JsonResponse {

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
