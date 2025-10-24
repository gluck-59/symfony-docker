<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class piController
{

    #[Route('/pi')]
    public function index(): Response
    {
        return new Response(
            phpinfo()
//        var_dump(123)
        );
    }


    #[Route('/pi/jopa')]
    public function jopa(): Response
    {
        return new Response(
            '<h1>jopa</h1'
        );
    }
}