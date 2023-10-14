<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;

class BookController extends AbstractController
{
    
    /**
     * @Route("/api/books", name="book", methods={"GET"})
     */
    public function getBookList(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $bookList = $bookRepository -> findAll();
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


   /**
    * @Route("/api/books/{id}", name="deleteBook", methods={"DELETE"})
    */
   public function deleteBook(Book $book, EntityManagerInterface $em): JsonResponse 
   {
       $em->remove($book);
       $em->flush();

       return new JsonResponse(null, Response::HTTP_NO_CONTENT);
   }

   /**
    * @Route("/api/books", name="createBook", methods={"POST"})
    */
   public function createBook(Request $request, EntityManagerInterface $em, UrlGenerator $urlGenerator, SerializerInterface $serializer): JsonResponse
   {
        $book = $serializer -> serialize($request->getContent(), Book::class, 'json');
        $
   }
}
