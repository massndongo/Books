<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    
    /**
     * @Route("/api/books", name="book", methods={"GET"})
     */
    public function getAllBooks(BookRepository $bookRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "getAllBooks-" . $page . "-" . $limit;
        
        $jsonBookList = $cache->get($idCache, function (ItemInterface $item) use ($bookRepository, $page, $limit, $serializer) {
            $item->tag("booksCache");
            $bookList = $bookRepository->findAllWithPagination($page, $limit);
            return $serializer->serialize($bookList, 'json', ['groups' => 'getBooks']);
        });
      
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
    * @IsGranted("ROLE_ADMIN", message="Vous n\'avez pas les droits suffisants pour supprimer un livre")
    */
    public function deleteBook(Book $book, EntityManagerInterface $em, TagAwareCacheInterface $cachePool): JsonResponse 
    {
        $cachePool->invalidateTags(["booksCache"]);
        $em->remove($book);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

   /**
    * @Route("/api/books", name="createBook", methods={"POST"})
    * @IsGranted("ROLE_ADMIN", message="Vous n\'avez pas les droits suffisants pour créer un livre")
    */
   public function createBook(Request $request, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, SerializerInterface $serializer, AuthorRepository $authorRepository, ValidatorInterface $validator): JsonResponse
   {
        $book = $serializer -> deserialize($request->getContent(), Book::class, 'json');

        $error = $validator -> validate($book);
        if ($error -> count() > 0) {
            return new JsonResponse($serializer -> serialize($error, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
            // return throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, "La requête est invalide");
        }
        // Récupération de l'ensemble des données envoyées sous forme de tableau
        $content = $request->toArray();

        // Récupération de l'idAuthor. S'il n'est pas défini, alors on met -1 par défaut.
        $idAuthor = $content['idAuthor'] ?? -1;

        // On cherche l'auteur qui correspond et on l'assigne au livre.
        // Si "find" ne trouve pas l'auteur, alors null sera retourné.
        $book->setAuthor($authorRepository->find($idAuthor));

        $em -> persist($book);
        $em -> flush();

        $jsonBook = $serializer -> serialize($book, 'json', ['groups' => 'getBooks']);
        $location = $urlGenerator -> generate('detailBook', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        
        return new JsonResponse($jsonBook, Response::HTTP_CREATED, ["location" => $location], true);
   }

   
   /**
    * @Route("/api/books/{id}", name="updateBook", methods={"PUT"})
    */
   public function updateBook(Request $request, SerializerInterface $serializer, Book $currentBook, EntityManagerInterface $em, AuthorRepository $authorRepository): JsonResponse 
   {
       $updatedBook = $serializer->deserialize($request->getContent(), 
               Book::class, 
               'json', 
               [AbstractNormalizer::OBJECT_TO_POPULATE => $currentBook]);
       $content = $request->toArray();
       $idAuthor = $content['idAuthor'] ?? -1;
       $updatedBook->setAuthor($authorRepository->find($idAuthor));
       
       $em->persist($updatedBook);
       $em->flush();
       return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
  }
}
