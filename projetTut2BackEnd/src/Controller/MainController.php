<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{

    private array $keywordArray;
    private array $mustArray;
    private array $specialHandleArray;

    /**
     * MainController constructor.
     */
    public function __construct()
    {
        parent::__construct();

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

        $idGame = $result['_source']['data']['appid'];

        $image = $this->createImage($idGame);

        $description = $this->createDescription($idGame);

        $requirement = $this->createRequirement($idGame);

        $tagCloud = json_decode($this->tagCloud($idGame)->getContent(), true);

        $game = new Game();
        $game->hydrate($result['_source']['data']);
        $game->setImage($image);
        $game->setDescription($description);
        $game->setRequirement($requirement);
        $game->setId($result['_id']);
        $game->setTagCloud($tagCloud);

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
            'from' => ($page - 1) * $gamesByPage

        ];

        //sorting to be defined this way in the URL : /games/{page}/criteria-order (for example : name-desc)
        if($sorting !== null){
            $params['sort'] = $this->setSorting($sorting, $this->keywordArray);
        }

        $result = $this->client->search($params);

        $games = ['games' => []];

        foreach ($result['hits']['hits'] as $gameInfos){
            $idGame = $gameInfos['_source']['data']['appid'];

            $image = $this->createImage($idGame);

            $description = $this->createDescription($idGame);

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

        $games['nbPages'] = ceil($totalGames['count'] / $gamesByPage);
        return new JsonResponse($games);
    }

    /**
     * @Route("/gameByName/{name}/{page}", name="game_by_name", methods={"GET"})
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
            'from' => ($page - 1) * $gamesByPage,
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
            $idGame = $gameInfos['_source']['data']['appid'];

            $image = $this->createImage($idGame);

            $description = $this->createDescription($idGame);

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

        $games['nbPages'] = ceil($totalGames['count'] / $gamesByPage);

        return new JsonResponse($games);
    }

    /**
     * @Route("/advancedSearch", name="advanced_search", methods={"POST"})
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
            'from' => ($page - 1) * $gamesByPage,
            "body" => [
                "query" => [
                    "bool" => [
                        "should" => [],
                    ],
                ],
            ]
        ];

        $shouldQueryParams = [];
        $mustQueryParams = [];

        if (isset($searchParams['sorting'])) {
            $params['sort'] = $this->setSorting($searchParams['sorting'], $this->keywordArray);

            unset($searchParams['sorting']);
        }

        foreach ($searchParams as $criteria => $value) {
            if (in_array($criteria, $this->mustArray)) { //bloc critères ET logique, must dans la requête

                $specialParams = explode("+", $value);

                foreach ($specialParams as $specialParam) {

                    $handledParam = $this->handleSpecialParams($specialParam);

                    array_push($mustQueryParams, array("terms" => array('data.' . $criteria . '.keyword' => (array)$handledParam)));
                }
            } else { //block critères OU logique, should dans la requête
                if ($criteria === "release_date" && strlen($value) === 4) {
                    $range = array("data.release_date" => array("gte" => $value . "||/y", "lte" => $value . "||/y"));
                } else if ($criteria === "name") {
                    array_push($shouldQueryParams, array("match" => array('data.' . $criteria => $value)));
                } else if ($criteria === "release_date_begin" || $criteria === "release_date_end") {
                    switch ($criteria) {
                        case 'release_date_begin':
                            $releaseDateBegin = $value;
                            break;
                        case 'release_date_end':
                            if (!isset($range)) {
                                $range = array("data.release_date" => array("gte" => $releaseDateBegin, "lte" => $value));
                            } else {
                                $range['data.release_date'] = array("gte" => $releaseDateBegin, "lte" => $value);
                            }
                            break;
                    }
                } else if ($criteria === "review_rate_low" || $criteria === "review_rate_high") {
                    switch ($criteria) {
                        case 'review_rate_low':
                            $reviewRateLow = $value;
                            break;
                        case 'review_rate_high':
                            if (!isset($range)) {
                                $range = array("data.positive_review_percentage" => array("gte" => $reviewRateLow, "lte" => $value));
                            } else {
                                $range['data.positive_review_percentage'] = array("gte" => $reviewRateLow, "lte" => $value);
                            }
                            break;
                    }
                } else if (in_array($criteria, $this->specialHandleArray)) {

                    $specialParams = explode("+", $value);

                    foreach ($specialParams as $specialParam) {

                        $handledParam = $this->handleSpecialParams($specialParam);

                        if (in_array($criteria, $this->keywordArray)) {
                            array_push($shouldQueryParams, array("terms" => array('data.' . $criteria . '.keyword' =>  (array)$handledParam)));
                        } else {
                            array_push($shouldQueryParams, array("terms" => array('data.' . $criteria =>  (array)$handledParam)));
                        }
                    }
                } else {
                    if (in_array($criteria, $this->keywordArray)) {
                        array_push($shouldQueryParams, array("terms" => array('data.' . $criteria . '.keyword' =>  (array)$value)));
                    } else {
                        array_push($shouldQueryParams, array("terms" => array('data.' . $criteria =>  (array)$value)));
                    }
                }
            }
        }

        $params['body']['query']['bool']['should'] = $shouldQueryParams;
        $params['body']['query']['bool']['must'] = $mustQueryParams;

        if (isset($range)) {
            array_push($params['body']['query']['bool']['should'], array("range" => $range));
        }

        $results = $this->client->search($params);

        $games = ['games' => []];

        foreach ($results['hits']['hits'] as $gameInfos){
            $idGame = $gameInfos['_source']['data']['appid'];

            $image = $this->createImage($idGame);

            $description = $this->createDescription($idGame);

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
        unset($params2['sort']);

        $totalGames = $this->client->count($params2);

        $games['nbPages'] = ceil($totalGames['count'] / $gamesByPage);

        return new JsonResponse($games);
    }

    /**
     * @Route("/fuzzySearch", name="fuzzy_search", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function fuzzySearch(Request $request): JsonResponse
    {
        $requestContent = $request->getContent();

        $searchParams = $this->parseRequestContent($requestContent);

        $params = [
            'index' => 'steam',
            'size' => 100,
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => []
                    ]
                ]
            ]
        ];

        $fuzzyQueryParams = [];
        $wildcardQueryParams = [];
        $savedCriteria = null;

        foreach ($searchParams as $criteria => $value) {
            $savedCriteria = $criteria;
            $fuzzyQueryParams['data.' . $criteria . '.keyword'] = array("value" => $value, "fuzziness" => "2", "boost" => 0.1);
            $wildcardQueryParams['data.' . $criteria . '.keyword'] = array("value" => $value . "*");
        }

        $params['body']['query']['bool']['should'][]['fuzzy'] = $fuzzyQueryParams;
        $params['body']['query']['bool']['should'][]['wildcard'] = $wildcardQueryParams;

        $results = $this->client->search($params);

        $trimmedResult = [];

        foreach ($results["hits"]["hits"] as $key => $value) {
            foreach ($value["_source"] as $key2 => $value2) {
                $game = new Game();
                $game->hydrate($value2);
            }

            $savedCriteriaTab = explode('_', '' . $savedCriteria);
            $savedCriteriaOk = '';
            foreach ($savedCriteriaTab as $key3 => $value3) {
                $savedCriteriaTab[$key3] = ucfirst($value3);
                $savedCriteriaOk = $savedCriteriaOk . $savedCriteriaTab[$key3];
            }

            array_push($trimmedResult, call_user_func(array($game, "get" . $savedCriteriaOk)));
        }

        $mergedResults = [];

        foreach ($trimmedResult as $key => $value) {
            $mergedResults = array_merge((array)$mergedResults, (array)$value);
        }

        $mergedResults = array_unique($mergedResults);
        $mergedResults = array_slice($mergedResults, 0, 10);

        return new JsonResponse($mergedResults);
    }

    /**
     * @Route("/relatedGames", name="related_games", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function relatedGames(Request $request): JsonResponse
    {
        $requestContent = $request->getContent();

        $searchParams = $this->parseRequestContent($requestContent);

        $params = [
            "index" => "steam",
            "body" => [
                "query" => [
                    "bool" => [
                        "should" => [],
                    ],
                ],
            ]
        ];

        $shouldQueryParams = [];

        $tags = json_decode($this->tagWeightByGame($searchParams['appid'])->getContent(), true);

        foreach ($tags as $key => $weight) {
            $key = str_replace("_", " ", $key);

            array_push($shouldQueryParams, array("match" => array('data.steamspy_tags' => array("query" => $key, "boost" => $weight))));
        }

        $params['body']['query']['bool']['should'] = $shouldQueryParams;

        $results = $this->client->search($params);

        $games = ['games' => []];

        foreach ($results['hits']['hits'] as $gameInfos){
            $idGame = $gameInfos['_source']['data']['appid'];

            $image = $this->createImage($idGame);

            $description = $this->createDescription($idGame);

            $game = new Game();
            $game->hydrate($gameInfos['_source']['data']);
            $game->setImage($image);
            $game->setDescription($description);
            $game->setId($gameInfos['_id']);
            array_push($games['games'], json_decode($this->serializer->serialize($game, 'json')));
        }

        return new JsonResponse($games);
    }
}
