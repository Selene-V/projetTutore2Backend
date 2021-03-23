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
     * @return JsonResponse
     */
    public function game(string $id): JsonResponse
    {
        $params = [
            'index' => 'steam',
            'id' => $id
        ];

        $client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();

        $result = $client->get($params);

        return new JsonResponse($result);
    }

    /**
     * @Route("/games/{page}", name="games", requirements={"page" = "\d+"}, methods={"GET"})
     * @param int $page
     * @return JsonResponse
     */
    public function games(int $page): JsonResponse
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

        array_push($result, ['images' => json_decode($this->images())]);
        return new JsonResponse($result);
    }

    /**
     * @Route("/gameByName/{name}", name="gameByName", methods={"GET"})
     * @param string $name
     * @return JsonResponse
     */
    public function gameByName(string $name): JsonResponse
    {
        $params = [
            'index' => 'steam',
            'body' => [
                'query' => [
                    'match' => [
                        'data.name' => $name
                    ]
                ],
            ],
        ];

        $client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();

        $result = $client->search($params);

        return new JsonResponse($result);
    }

    /**
     * @Route("/games/images", name="images_game", methods={"GET"})
     * @return JsonResponse
     */
    public function images(): JsonResponse
    {
        $params = [
            'index' => 'steam_media_data',
        ];

        $client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();

        $result = $client->search($params);

        return new JsonResponse($result);
    }
}
