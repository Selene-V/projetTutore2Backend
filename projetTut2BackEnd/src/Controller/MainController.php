<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Elasticsearch\ClientBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

class MainController extends AbstractController
{
    /**
     * MainController constructor.
     */
    public function __construct()
    {
    }

    /**
     * @Route("/game/{id}", name="game", methods={"GET"})
     * @param string $id
     */
    public function game(string $id)
    {
        $params = [
            'index' => 'steam',
            'id' => $id
        ];

        $client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();

        $result = $client->get($params);
        //dd($result);
    }

    /**
     * @Route("/games/{page}", name="games", requirements={"page" = "\d+"}, methods={"GET"})
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

    /**
     * @Route("/game/{appid}/images", name="images_game", methods={"GET"})
     * @param string $appid
     */
    public function images(string $appid)
    {
        $params = [
            'index' => 'steam_media_data',
            'body' => [
                'query' => [
                    'match' => [
                        'data.steam_appid' => $appid
                    ]
                ]
            ]
        ];

        $client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();
        $results = $client->search($params);
        return new JsonResponse($results);
    }
}
