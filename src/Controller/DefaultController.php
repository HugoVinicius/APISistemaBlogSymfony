<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Mailer\SimpleMailer;

class DefaultController extends AbstractController
{
    private SimpleMailer $mail;

    public function __construct(SimpleMailer $mailer){
        $this->mail = $mailer;
    }

    public function index(string $name) : Response {
        return $this->render('index.html.twig', [
            'name' => $name,
        ]);
    }

    public function mail() : Response {

        $this->mail->send('hugovinicius94@yahoo.com.br', 'msg a ser enviada');
                
        return new Response('ok');
    }
}