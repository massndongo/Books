<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthorController extends AbstractController
{
    
    /**
     * @Route("/api/authors", name="author", methods={"GET"})
     */
    public function getAuthorList(AuthorRepository $authorRepository, SerializerInterface $serializer): JsonResponse
    {
        $authorList = $authorRepository -> findAll();
        $jsonAuthorList = $serializer -> serialize($authorList, 'json', ['groups' => 'getAuthors']);
        return new JsonResponse($jsonAuthorList, Response::HTTP_OK, [], true);
    }


    /**
     * @Route("/api/authors/{id}", name="detailAuthor", methods={"GET"})
     */
    public function getDetailAuthor(Author $author, SerializerInterface $serializer): JsonResponse {

        $jsonAuthor = $serializer -> serialize($author, 'json', ['groups' => 'getAuthors']);
        return new JsonResponse($jsonAuthor, Response::HTTP_OK, ['accept' => 'json'], true);
   }

   /**
    * @Route("/api/authors/{id}", name="deleteAuthor", methods={"DELETE"})
    */
   public function deleteAuthor(Author $author, EntityManagerInterface $em) : JsonResponse {
    
        $em -> remove($author);
        $em -> flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
   }   
   
   /**
   * @Route("/api/authors", name="createAuthor", methods={"POST"})
   */
  public function createAuthor(Request $request, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, SerializerInterface $serializer, BookRepository $bookRepository): JsonResponse
  {
       $author = $serializer -> deserialize($request->getContent(), Author::class, 'json');

       // Récupération de l'ensemble des données envoyées sous forme de tableau 
       $content = $request->toArray();
       // Récupération de l'idBook. S'il n'est pas défini, alors on met -1 par défaut.
       $books = $content['books'];
       foreach ($books as $key => $value) {
       // On cherche le livre qui correspond et on l'assigne à l'auteur.
       // Si "find" ne trouve pas le livre, alors null sera retourné.
        $idBook = $value['id'] ?? -1;
        $author -> addBook($bookRepository ->find($idBook));
        $em -> persist($author);
       }
       dd($author);

 
       $em -> flush();

       $jsonAuthor = $serializer -> serialize($author, 'json', ['groups' => 'getAuthors']);
       $location = $urlGenerator -> generate('detailAuthor', ['id' => $author->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
       
       return new JsonResponse($jsonAuthor, Response::HTTP_CREATED, ["location" => $location], true);
  }
   
}
