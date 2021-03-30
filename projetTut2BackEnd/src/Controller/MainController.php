<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Image;
use App\Entity\Description;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Elasticsearch\ClientBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    private array $encoders;
    private array $normalizers;
    private Serializer $serializer;
    private array $keywordArray;
    private $client;

    /**
     * MainController constructor.
     */
    public function __construct()
    {
        $this->encoders = [new XmlEncoder(), new JsonEncoder()];
        $this->normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($this->normalizers, $this->encoders);

        $this->client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();

        $this->keywordArray = ["name", "categories", "developper", "genres", "owners", "platforms", "publisher", "steamspy_tags"];
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

        $result = $this->client->get($params);

        $idgame = $result['_source']['data']['appid'];

        $image = new Image();
        $imageData = json_decode($this->imagesByGame($idgame)->getContent(), true);
        $imageData['hits']['hits'][0]['_source']['data']['screenshots'] = json_decode(str_replace("'", "\"", $imageData['hits']['hits'][0]['_source']['data']['screenshots']), true);
        $imageData['hits']['hits'][0]['_source']['data']['movies'] = str_replace("'", "\"", $imageData['hits']['hits'][0]['_source']['data']['movies']);
        $imageData['hits']['hits'][0]['_source']['data']['movies'] = str_replace("True", "true", $imageData['hits']['hits'][0]['_source']['data']['movies']);
        $imageData['hits']['hits'][0]['_source']['data']['movies'] = json_decode(str_replace("False", "false", $imageData['hits']['hits'][0]['_source']['data']['movies']), true);

        $image->hydrate($imageData['hits']['hits'][0]['_source']['data']);
        $image->setId($imageData['hits']['hits'][0]['_id']);

        $description = new Description();
        $descriptionData = json_decode($this->descriptionsByGame($idgame)->getContent(), true);
        $description->hydrate($descriptionData['hits']['hits'][0]['_source']['data']);
        $description->setId($descriptionData['hits']['hits'][0]['_id']);

        $game = new Game();
        $game->hydrate($result['_source']['data']);
        $game->setImage($image);
        $game->setDescription($description);
        $game->setId($result['_id']);

        return new JsonResponse(json_decode($this->serializer->serialize($game, 'json')));
    }

    /**
     * @Route("/games/{page}/{sorting}", name="games", requirements={"page" = "\d+"}, methods={"GET"})
     * @param int $page
     * @param string|null $sorting
     * @return JsonResponse
     */
    public function games(int $page, string $sorting = null): JsonResponse
    {
        $gamesByPage = 8;

        if ($page < 1) {
            $page = 1;
        }
        $params = [
            'index' => 'steam',
            'size' => $gamesByPage,
            'from' => ($page-1)*$gamesByPage

        ];

        //sorting to be defined this way in the URL : /games/{page}/criteria-order (for example : name-desc)
        if($sorting !== null){

            $temp = explode('-',$sorting);
            $criteria = $temp[0];
            $order = $temp[1];

            if(in_array($criteria, $this->keywordArray)){
                $params['sort'] = array('data.' . $criteria . '.keyword:' . $order);
            }
            else{
                $params['sort'] = array('data.' . $criteria . ':' . $order);
            }
        }

        $result = $this->client->search($params);

        $games = ['games' => []];
        foreach ($result['hits']['hits'] as $gameInfos){
            $idgame = $gameInfos['_source']['data']['appid'];

            $image = new Image();
            $imageData = json_decode($this->imagesByGame($idgame)->getContent(), true);
            $imageData['hits']['hits'][0]['_source']['data']['screenshots'] = json_decode(str_replace("'", "\"", $imageData['hits']['hits'][0]['_source']['data']['screenshots']), true);
            $imageData['hits']['hits'][0]['_source']['data']['movies'] = str_replace("'", "\"", $imageData['hits']['hits'][0]['_source']['data']['movies']);
            $imageData['hits']['hits'][0]['_source']['data']['movies'] = str_replace("True", "true", $imageData['hits']['hits'][0]['_source']['data']['movies']);
            $imageData['hits']['hits'][0]['_source']['data']['movies'] = json_decode(str_replace("False", "false", $imageData['hits']['hits'][0]['_source']['data']['movies']), true);
            $image->hydrate($imageData['hits']['hits'][0]['_source']['data']);
            $image->setId($imageData['hits']['hits'][0]['_id']);

            $description = new Description();
            $descriptionData = json_decode($this->descriptionsByGame($idgame)->getContent(), true);
            $description->hydrate($descriptionData['hits']['hits'][0]['_source']['data']);
            $description->setId($descriptionData['hits']['hits'][0]['_id']);

            $game = new Game();
            $game->hydrate($gameInfos['_source']['data']);
            $game->setImage($image);
            $game->setDescription($description);
            $game->setId($gameInfos['_id']);
            array_push($games['games'], json_decode($this->serializer->serialize($game, 'json')));
        }

        $params2 = [
            'index' => 'steam',
        ];

        $totalGames = $this->client->count($params2);

        $games['nbPages'] = ceil($totalGames['count']/$gamesByPage);
        $games['gamesTotal'] = $totalGames['count'];
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

        $result = $this->client->search($params);

        $games = [];
        foreach ($result['hits']['hits'] as $gameInfos){
            $idgame = $gameInfos['_source']['data']['appid'];

            $image = new Image();
            $imageData = json_decode($this->imagesByGame($idgame)->getContent(), true);
            $imageData['hits']['hits'][0]['_source']['data']['screenshots'] = json_decode(str_replace("'", "\"", $imageData['hits']['hits'][0]['_source']['data']['screenshots']), true);
            $imageData['hits']['hits'][0]['_source']['data']['movies'] = str_replace("'", "\"", $imageData['hits']['hits'][0]['_source']['data']['movies']);
            $imageData['hits']['hits'][0]['_source']['data']['movies'] = str_replace("True", "true", $imageData['hits']['hits'][0]['_source']['data']['movies']);
            $imageData['hits']['hits'][0]['_source']['data']['movies'] = json_decode(str_replace("False", "false", $imageData['hits']['hits'][0]['_source']['data']['movies']), true);
            $image->hydrate($imageData['hits']['hits'][0]['_source']['data']);
            $image->setId($imageData['hits']['hits'][0]['_id']);

            $description = new Description();
            $descriptionData = json_decode($this->descriptionsByGame($idgame)->getContent(), true);
            $description->hydrate($descriptionData['hits']['hits'][0]['_source']['data']);
            $description->setId($descriptionData['hits']['hits'][0]['_id']);

            $game = new Game();
            $game->hydrate($gameInfos['_source']['data']);
            $game->setImage($image);
            $game->setDescription($description);
            $game->setId($gameInfos['_id']);
            array_push($games, json_decode($this->serializer->serialize($game, 'json')));
        }
        return new JsonResponse($games);
    }

    /**
     * @Route("/game/images/{appid}", name="images_by_game", methods={"GET"})
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

        $result = $this->client->search($params);

        return new JsonResponse($result);
    }

    /**
     * @Route("/game/descriptions/{appid}", name="descriptions_by_game", methods={"GET"})
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

        $results = $this->client->search($params);

        return new JsonResponse($results);
    }

    /**
     * @Route("/advancedSearch", name="advancedSearch", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function advancedSearch(Request $request): JsonResponse
    {

        $requestContent = $request->getContent();

        $searchParams = [];

        foreach (explode('&', $requestContent) as $chunk) {
            $param = explode("=", $chunk);

            $searchParams[$param[0]] = $param[1] ;
        }

        $searchPhrase = "";

        $params = [
            "index" => "steam",
            "body" => [
                "query" => [
                    "bool" => [
                            "must" => [
                            ],
                        ],
                    ],
                ],
            ];

        $iterator = 0;

        $queryParams = [];

        foreach ($searchParams as $criteria => $value) {
            if(in_array($criteria, $this->keywordArray)){
                $temp =  array("terms" => array('data.'.$criteria.'.keyword' => (array)$value));
            }
            else{
                $temp =  array("terms" => array('data.'.$criteria => (array)$value));
            }

            array_push($queryParams, ...[$temp]);

            $iterator++;

        }
        $params['body']['query']['bool']['must'] = $queryParams;

        $results = $this->client->search($params);

        return new JsonResponse($results);
    }

    
}
