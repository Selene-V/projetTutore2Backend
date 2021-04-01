<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Component\Routing\Annotation\Route;
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
    private array $mustArray;
    private array $specialHandleArray;

    /**
     * MainController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->encoders = [new XmlEncoder(), new JsonEncoder()];
        $this->normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($this->normalizers, $this->encoders);

        $this->keywordArray = ["name", "categories", "developer", "genres", "owners", "platforms", "publisher", "steamspy_tags"];

        $this->mustArray = ["categories", "genres"];

        $this->specialHandleArray = ["developer", "publisher", "categories", "genres", "steamspy_tags"];
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

        $image = $this->createImage($idgame);

        $description = $this->createDescription($idgame);

        $requirement = $this->createRequirement($idgame);

        $game = new Game();
        $game->hydrate($result['_source']['data']);
        $game->setImage($image);
        $game->setDescription($description);
        $game->setRequirement($requirement);
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

            $image = $this->createImage($idgame);

            $description = $this->createDescription($idgame);

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
        return new JsonResponse($games);
    }

    /**
     * @Route("/gameByName/{name}/{page}", name="gameByName", methods={"GET"})
     * @param string $name
     * @param int $page
     * @return JsonResponse
     */
    public function gameByName(string $name, int $page): JsonResponse
    {
        $gamesByPage = 8;

        if ($page < 1) {
            $page = 1;
        }
        $params = [
            'index' => 'steam',
            'size' => $gamesByPage,
            'from' => ($page-1)*$gamesByPage,
            'body' => [
                'query' => [
                    'match' => [
                        'data.name' => $name
                    ]
                ],
            ],
        ];

        $result = $this->client->search($params);

        $games = ['games' => []];
        foreach ($result['hits']['hits'] as $gameInfos){
            $idgame = $gameInfos['_source']['data']['appid'];

            $image = $this->createImage($idgame);

            $description = $this->createDescription($idgame);

            $game = new Game();
            $game->hydrate($gameInfos['_source']['data']);
            $game->setImage($image);
            $game->setDescription($description);
            $game->setId($gameInfos['_id']);
            array_push($games['games'], json_decode($this->serializer->serialize($game, 'json')));
        }
        $params2 = [
            'index' => 'steam',
            'body' => [
                'query' => [
                    'match' => [
                        'data.name' => $name
                    ]
                ],
            ],
        ];

        $totalGames = $this->client->count($params2);
        
        $games['nbPages'] = ceil($totalGames['count']/$gamesByPage);

        return new JsonResponse($games);
    }

    /**
     * @Route("/advancedSearch", name="advancedSearch", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function advancedSearch(Request $request): JsonResponse
    {

        $requestContent = $request->getContent();

        $searchParams = $this->parseRequestContent($requestContent);

        $gamesByPage = 8;
        $page = $searchParams['page'];
        unset($searchParams['page']);
        if ($page < 1) {
            $page = 1;
        }

        $params = [
            "index" => "steam",
            'size' => $gamesByPage,
            'from' => ($page-1)*$gamesByPage,
            "body" => [
                "query" => [
                    "bool" => [
                        "should" => [
                            ],
                        ],
                     ],
                ]
            ];

        $shouldQueryParams = [];
        $mustQueryParams = [];

        if(isset($searchParams['sorting'])){
            $params['sort'] = $this->setSorting($searchParams['sorting'], $this->keywordArray);

            unset($searchParams['sorting']);
        }

        foreach ($searchParams as $criteria => $value) {
            if(in_array($criteria ,$this->mustArray)){ //bloc critères ET logique, must dans la requête

                $specialParams = explode("+", $value);

                foreach ($specialParams as $specialParam) {

                    $handledParam = $this->handleSpecialParams($specialParam);
    
                    array_push($mustQueryParams, array("terms" => array('data.'.$criteria.'.keyword' => (array)$handledParam)));
                }
            }
            else{ //block critères OU logique, should dans la requête
                if($criteria === "release_date" && strlen($value) === 4){
                    $range = array("data.release_date" => array("gte" => $value."||/y", "lte" => $value."||/y" ));
                }
                else if ($criteria === "name") {
                    array_push($shouldQueryParams, array("match" => array('data.'.$criteria => $value)));
                }
                else if($criteria === "release_date_begin" || $criteria ==="release_date_end"){
                    switch ($criteria) {
                        case 'release_date_begin':
                            $releaseDateBegin = $value;
                            break;
                        case 'release_date_end':
                            if(!isset($range)){
                                $range = array("data.release_date" => array("gte" => $releaseDateBegin, "lte" => $value));
                            }
                            else{
                                $range['data.release_date'] = array("gte" => $releaseDateBegin, "lte" => $value);
                            }
                            break;
                    }
                }
                else if($criteria === "review_rate_low" || $criteria === "review_rate_high"){
                    switch ($criteria) {
                        case 'review_rate_low':
                            $reviewRateLow = $value;
                            break;
                        case 'review_rate_high':
                            if(!isset($range)){
                                $range = array("data.positive_review_percentage" => array("gte" => $reviewRateLow, "lte" => $value));
                            }
                            else{
                                $range['data.positive_review_percentage'] = array("gte" => $reviewRateLow, "lte" => $value);
                            }
                            break;
                        }
                }
                else if (in_array($criteria,$this->specialHandleArray)) {

                    $specialParams = explode("+", $value);

                    foreach ($specialParams as $specialParam) {

                        $handledParam = $this->handleSpecialParams($specialParam);
        
                        if(in_array($criteria ,$this->keywordArray)){
                            array_push($shouldQueryParams, array("terms" => array('data.'.$criteria.'.keyword' =>  (array)$handledParam)));
                        }
                        else{
                            array_push($shouldQueryParams, array("terms" => array('data.'.$criteria =>  (array)$handledParam)));
                        }
                    }
                }
                else{
                    if(in_array($criteria ,$this->keywordArray)){
                        array_push($shouldQueryParams, array("terms" => array('data.'.$criteria.'.keyword' =>  (array)$value)));
                    }
                    else{
                        array_push($shouldQueryParams, array("terms" => array('data.'.$criteria =>  (array)$value)));
                    }
                }
            }
        }

        $params['body']['query']['bool']['should'] = $shouldQueryParams;
        $params['body']['query']['bool']['must'] = $mustQueryParams;

        if(isset($range)){
            array_push($params['body']['query']['bool']['should'], array("range" => $range));
        }

        $results = $this->client->search($params);

        $games = ['games' => []];
        foreach ($results['hits']['hits'] as $gameInfos){
            $idgame = $gameInfos['_source']['data']['appid'];

            $image = $this->createImage($idgame);

            $description = $this->createDescription($idgame);

            $game = new Game();
            $game->hydrate($gameInfos['_source']['data']);
            $game->setImage($image);
            $game->setDescription($description);
            $game->setId($gameInfos['_id']);
            array_push($games['games'], json_decode($this->serializer->serialize($game, 'json')));
        }
        $params2 = $params;
        unset($params2['from']);
        unset($params2['size']);

        $totalGames = $this->client->count($params2);

        $games['nbPages'] = ceil($totalGames['count']/$gamesByPage);

        return new JsonResponse($games);
    }

    /**
     * @Route("/fuzzySearch", name="fuzzySearch", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function fuzzySearch(Request $request): JsonResponse
    {

        $requestContent = $request->getContent();

        $searchParams = $this->parseRequestContent($requestContent);

        $params = [
            'index' => 'steam',
            'body' => [
                'query' => [
                    'fuzzy' => [
                    ]
                ]
            ]
        ];

        $queryParams = [];

        foreach ($searchParams as $criteria => $value) {
            $queryParams['data.'.$criteria.'.keyword'] = array("value" => $value, "fuzziness" => "2",);
        }

        $params['body']['query']['fuzzy'] = $queryParams;

        $results = $this->client->search($params);

        return new JsonResponse($results);
    }

    
}
