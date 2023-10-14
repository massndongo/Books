<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    
        $em -> remove($book);
        
   }
   
}
