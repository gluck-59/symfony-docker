<?php
// src/Controller/SiteController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SiteController extends AbstractController
{
    #[Route('/site/main', name: 'main')]
    public function main(): Response
    {
//        $this->addFlash('success', 'Test');
        return $this->render('site/main.html.twig', [
        'title' => 'Главная'
        ]);
    }

    #[Route('/site/pi', name: 'phpinfo')]
    public function pi(): Response
    {
        ob_start();
        phpinfo();
        $info = ob_get_clean();

        return new Response($info);
    }
}
