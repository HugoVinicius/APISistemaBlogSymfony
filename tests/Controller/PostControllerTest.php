<?php

namespace App\Tests\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PostControllerTest extends WebTestCase
{
    private EntityManagerInterface $em;
    private KernelBrowser $client;

    public function SetUp(): void
    {
        $this->client = self::createClient();
        
        // Cria a ferramenta para manipulação do banco de dados
        $this->em = self::$kernel->getContainer()->get('doctrine')->getManager();
        $tool = new SchemaTool($this->em);

        // Recupera mea informação da entidade Post
        $metadata = $this->em->getClassMetadata(Post::class);

        // apaga a abela associada â entidade Post
        $tool->dropSchema([$metadata]);

        try{
            //cria a tabela associda à entidade Post
            $tool->createSchema([$metadata]);
        } catch(ToolsException $e) {
            $this->fail("Impossível cria tabela Post: " .$e->getMessae());
        }
    }

    public function test_create_post(): void
    {
        $this->client->request('POST', '/posts', [], [], [], json_encode([
            'title' => 'primeiro Teste Funcional',
            'description' => 'Alguma descrição'
        ]));

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());        
    }

    public function test_delete_post(): void
    {
        $postNovo = new Post('Titulo do Posto', 'Descrição do Post');
        $this->em->persist($postNovo);
        $this->em->flush();

        $this->client->request('DELETE', '/posts/1', [], [], [], '');

        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());        
   }

   public function test_create_post_with_invalid_title(): Void
   {
        $this->client->request('POST', '/posts', [], [], [], json_encode([
            //'title' =>  '1234', 
            'description' => 'teste campo descrição',          
        ]));
        $this->assertequals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
   }
}
