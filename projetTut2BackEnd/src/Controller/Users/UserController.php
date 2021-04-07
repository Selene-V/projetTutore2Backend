<?php

namespace App\Controller\Users;

use App\Controller\AbstractController;
use App\Entity\Game;
use App\Manager\TokenManager;
use PDO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private PDO $bdd;

    public function __construct()
    {
        parent::__construct();
        $this->bdd = new PDO('mysql:host=127.0.0.1;dbname=projettutore2', 'root', '');
    }

    /**
     * @Route("/addGameToLibrary", name="addGameToLibrary", methods={"POST"})
     */
    public function addGameToLibrary(Request $request)
    {
        $requestContent = $request->getContent();

        $searchParams = $this->parseRequestContent($requestContent);

        $data = $this->getToken($request);

        if ($this->checkToken($data, $searchParams['user'])) {
            $select = $this->bdd->prepare("SELECT * FROM users_games WHERE user = :user AND game = :game");

            $select->bindParam(':user', $searchParams['user'], PDO::PARAM_INT);
            $select->bindParam(':game', $searchParams['game'], PDO::PARAM_INT);

            $select->execute();

            if ($select->rowCount() === 0) { //On vérifie si la relation n'existe pas déjà
                $req = $this->bdd->prepare("INSERT INTO users_games VALUES (:user, :game)");

                $req->bindParam(':user', $searchParams['user'], PDO::PARAM_INT);
                $req->bindParam(':game', $searchParams['game'], PDO::PARAM_INT);

                if ($req->execute()) {
                    return new Response(true);
                }
            }
            return new Response(false);
        }
        return new Response(false);
    }

    /**
     * @Route("/removeGameFromLibrary", name="removeGameFromLibrary", methods={"POST"})
     */
    public function removeGameFromLibrary(Request $request)
    {
        $requestContent = $request->getContent();

        $searchParams = $this->parseRequestContent($requestContent);

        $data = $this->getToken($request);

        if ($this->checkToken($data, $searchParams['user'])) {
            $req = $this->bdd->prepare("DELETE FROM users_games WHERE user = :user AND game = :game");

            $req->bindParam(':user', $searchParams['user'], PDO::PARAM_INT);
            $req->bindParam(':game', $searchParams['game'], PDO::PARAM_INT);

            if ($req->execute()) {
                return new Response(true);
            }
            return new Response(false);
        }
        return new Response(false);
    }

    /**
     * @Route("/displayLibrary", name="display_library", requirements={"page" = "\d+"}, methods={"POST"})
     * @return JsonResponse
     */
    public function displayLibrary(Request $request)
    {

        $requestContent = $request->getContent();

        $searchParams = $this->parseRequestContent($requestContent);

        $data = $this->getToken($request);

        if ($this->checkToken($data, $searchParams['user'])) {
            $req = $this->bdd->prepare('SELECT game FROM users_games WHERE user = :user');

            $req->execute(array(
                'user' => $searchParams['user']
            ));

            $resultSQL = $req->fetchAll();

            $gamesByPage = 8;
            $page = $searchParams['page'];
            if ($page < 1) {
                $page = 1;
            }
            $params = [
                'index' => 'steam',
                'size' => $gamesByPage,
                'from' => ($page - 1) * $gamesByPage,
                'body' => [
                    'query' => [
                        'bool' => [
                            'should' => [],
                        ],
                    ],
                ],
            ];

            $queryParams = [];

            foreach ($resultSQL as $game) {
                array_push($queryParams, array("terms" => array('data.appid' =>  (array)$game[0])));
            }

            $params['body']['query']['bool']['should'] = $queryParams;

            $result = $this->client->search($params);

            $games = ['games' => []];
            foreach ($result['hits']['hits'] as $gameInfos) {
                $idgame = $gameInfos['_source']['data']['appid'];

                $image = $this->createImage($idgame);

                $game = new Game();
                $game->hydrate($gameInfos['_source']['data']);
                $game->setImage($image);
                $game->setId($gameInfos['_id']);
                array_push($games['games'], json_decode($this->serializer->serialize($game, 'json')));
            }

            unset($params['size']);
            unset($params['page']);
            unset($params['from']);

            $totalGames = $this->client->count($params);

            $games['nbPages'] = ceil($totalGames['count'] / $gamesByPage);
            return new JsonResponse($games);
        }
        return new Response(false);
    }

    public function getToken(Request $request)
    {
        $authorizationHeader = $request->headers->get('Authorization');
        $authorizationHeaderArray = explode(' ', $authorizationHeader);
        $token = $authorizationHeaderArray[0] ?? null;
        $data = (new TokenManager())->decode($token);

        return $data;
    }

    public function checkToken($data, $id)
    {
        if ($data['exp'] > time() && $data['id'] === $id) {
            return true;
        }
        return false;
    }
}
