<?php
// src/Controller/SiteController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SiteController extends AbstractController
{
    #[Route('site/main', name: 'main')]
    public function main(): Response
    {
        return $this->render('site/main.html.twig', [
        'message' => 'Добро пожаловать на главную страницу!',
            'title' => 'Главная'
        ]);
    }
}
