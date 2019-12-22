<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use App\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PostController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("/posts", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        //$data = json_decode($request->getContent(), true);

        //$post = new Post($data['title'], $data['description']);

        $post = $this->serializer->deserialize($request->getContent(), Post::class, 'json');

        $erros = $this->validator->validate($post);

        if (count($erros))
        {   
            throw new ValidationException($erros);
        }

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return new Response('ok', Response::HTTP_CREATED);
    }


    /**
     * @Route("/posts/{id}", methods={"GET"})
     */
     public function details(int $id): Response
     {
         $post = $this->entityManager->getRepository(Post::class)->find($id);

         if (null === $post)
         {
             throw new NoFoundHttpException('Post não encontrado');
         }

         return JsonResponse::fromJsonString($this->serialize->serialize($post, 'json'));

        /**return JsonResponse::create([
             'id' => $post->getId(),
             'title' => $post->title,
             'description' => $post->description,
             'createdAt' => $post->getCreatedAt()->format('Y-m-d'),
         ]); */ 

     }

          /**
      * @Route("/posts", methods={"GET"})
      */
      public function index(): Response
      {
          /** @var Post $post */
          $posts = $this->entityManager->getRepository(Post::class)->findAll();

          $daa = [];

          foreach ($posts as $post)
          {
              $data[] = [
                'id' => $post->getId(),
                'title' => $post->title,
                'description' => $post->description,
                'createdAt' => $post->getCreatedAt()->format('Y-m-d'),
              ];
          }
          
          return JsonResponse::create($data);
      }

      /**
       * @Route("/posts/{id}", methods={"PUT"})
       */
      public function update(Request $request, int $id):Response
      {
          $post = $this->entityManager->getRepository(Post::class)->find($id);

          $data = json_decode($request->getContent(), true);

          $post->title = $data['title'];
          $post->description = $data['description'];

          $this->entityManager->persist($post);
          $this->entityManager->flush();

          return new Response('OK');
      }

      /**
     * @Route("/posts/{id}", methods={"DELETE"})
     */
    public function delete(Request $request, int $id) : Response
    {
        $post = $this->entityManager->getRepository(Post::class)->find($id);

        $this->entityManager->remove($post);
        $this->entityManager->flush();
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}