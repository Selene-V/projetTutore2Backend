<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Elasticsearch\ClientBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{
    /**
     * MainController constructor.
     */
    public function __construct()
    {
    }

    /**
     * @Route("/game/{id}", name="game")
     * @param $id
     */
    public function game($id)
    {
        $params = [
            'index' => 'steam',
            'id' => $id
        ];

        $client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();

        $result = $client->get($params);
        dd($result);
    }

    /**
     * @Route("/games/{page}", name="games")
     * @param int $page
     * @return JsonResponse
     */
    public function games(int $page)
    {
        if ($page < 1) {
            $page = 1;
        }
        $params = [
            'index' => 'steam',
            'size' => 8,
            'from' => $page

        ];

        $client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();

        $result = $client->search($params);

        return new JsonResponse($result);

        //dd($result);
    }
}
