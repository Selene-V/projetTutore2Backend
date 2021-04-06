<?php

namespace App\Controller\Users;

use Symfony\Component\Routing\Annotation\Route;
use PDO;
use Symfony\Component\HttpFoundation\Response;

class Disconnection
{

    /**
     * @Route("/disconnection", name="disconnection", methods={"POST"})
     * @return Response
     */
    public function disconnection(): Response
    {
        session_destroy();
        return new Response('You have been disconnected !');
    }
}
