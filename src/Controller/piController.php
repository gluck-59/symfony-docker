<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class piController
{

    #[Route('/pi')]
    public function index(): Response
    {
        ob_start();
        phpinfo();
        $info = ob_get_clean();

        return new Response($info);
    }


    #[Route('/pi/jopa')]
    public function jopa(): Response
    {
        return new Response(
            '<h1>jopa</h1'
        );
    }
}