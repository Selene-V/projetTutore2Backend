<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Elasticsearch\ClientBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MainController extends AbstractController
{
    private $encoders;
    private $normalizers;
    private $serializer;

    /**
     * MainController constructor.
     */
    public function __construct()
    {
        $this->encoders = [new XmlEncoder(), new JsonEncoder()];
        $this->normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($this->normalizers, $this->encoders);
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

        $images = $this->imagesByGame($result['_source']['data']['appid']);

        $descriptions = $this->descriptionsByGame($result['_source']['data']['appid']);

        array_push($result, ['images' => json_decode($images->getContent())]);
        array_push($result, ['descriptions' => json_decode($descriptions->getContent())]);

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

        $games = [];
        foreach ($result['hits']['hits'] as $gameInfos){
            $idgame = $gameInfos['_source']['data']['appid'];
            $image = new Image();
            $imageData = json_decode($this->imagesByGame($idgame)->getContent(), true);
            $image->hydrate($imageData['hits']['hits'][0]['_source']['data']);

            $game = new Game();
            $game->hydrate($gameInfos['_source']['data']);
            $game->setImage($image);
            array_push($games, $this->serializer->serialize($game, 'json'));
        }
        return new JsonResponse($games);
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
     * @Route("/games/images", name="images_games", methods={"GET"})
     * @return JsonResponse
     */
    public function images(): JsonResponse
    {
        $params = [
            'index' => 'steam_media_data',
            'size' => 8,
        ];

        $client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();

        $result = $client->search($params);

        return new JsonResponse($result);
    }

    /**
     * @Route("/game/{appid}/images", name="images_by_game", methods={"GET"})
     * @param string $appid
     * @return JsonResponse
     */
    public function imagesByGame(string $appid): JsonResponse
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

    /**
     * @Route("/game/{appid}/descriptions", name="images_by_game", methods={"GET"})
     * @param string $appid
     * @return JsonResponse
     */
    public function descriptionsByGame(string $appid): JsonResponse
    {
        $params = [
            'index' => 'steam_description_data',
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

    /**
     * @Route("/advancedSearch/{publisher}/{producer}", name="advancedSearch", methods={"GET"})
     * @param string $publisher
     * @param string $producer
     * @return JsonResponse
     */
    public function advancedSearch(string $publisher = null,  string $producer): JsonResponse
    {

        dd($publisher);

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
