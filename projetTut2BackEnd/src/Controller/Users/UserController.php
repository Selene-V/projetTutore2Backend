<?php

namespace App\Controller\Users;

use App\Config\Config;
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
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $dbname = Config::config('pdo_dbname');
        $host = Config::config('pdo_host');
        $user = Config::config('pdo_user');
        $password = Config::config('pdo_password');
        $dsn = sprintf(
            'mysql:dbname=%s;host=%s',
            $dbname,
            $host
        );
        $this->bdd = new PDO($dsn, $user, $password);
    }
    /**
     * @Route("/addGameToLibrary", name="addGameToLibrary", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function addGameToLibrary(Request $request): Response
    {
        $requestContent = $request->getContent();
        $searchParams = $this->parseRequestContent($requestContent);
        $data = $this->getToken($request);
        if ($this->checkToken($data)) {
            $select = $this->bdd->prepare("SELECT * FROM users_games WHERE user = :user AND game = :game");
            $select->bindParam(':user', $data['id'], PDO::PARAM_INT);
            $select->bindParam(':game', $searchParams['game'], PDO::PARAM_INT);
            $select->execute();
            if ($select->rowCount() === 0) { //On vérifie si la relation n'existe pas déjà
                $req = $this->bdd->prepare("INSERT INTO users_games VALUES (:user, :game)");
                $req->bindParam(':user', $data['id'], PDO::PARAM_INT);
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
     * @param Request $request
     * @return Response
     */
    public function removeGameFromLibrary(Request $request): Response
    {
        $requestContent = $request->getContent();
        $searchParams = $this->parseRequestContent($requestContent);
        $data = $this->getToken($request);
        if ($this->checkToken($data)) {
            $req = $this->bdd->prepare("DELETE FROM users_games WHERE user = :user AND game = :game");
            $req->bindParam(':user', $data['id'], PDO::PARAM_INT);
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
     * @param Request $request
     * @return Response
     */
    public function displayLibrary(Request $request): Response
    {
        $requestContent = $request->getContent();
        $searchParams = $this->parseRequestContent($requestContent);
        $data = $this->getToken($request);
        if ($this->checkToken($data)) {
            $req = $this->bdd->prepare('SELECT game FROM users_games WHERE user = :user');
            $req->execute(array(
                'user' => $data['id']
            ));
            $resultSQL = $req->fetchAll();

            if (empty($resultSQL)) {
                return new JsonResponse(false);
            }

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
                $description = $this->createDescription($idgame);
                $game = new Game();
                $game->hydrate($gameInfos['_source']['data']);
                $game->setImage($image);
                $game->setDescription($description);
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

    /**
     * @Route("/libraryContains", name="library_contains", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function libraryContains(Request $request): Response
    {

        $requestContent = $request->getContent();

        $searchParams = $this->parseRequestContent($requestContent);

        $data = $this->getToken($request);

        if ($this->checkToken($data)) {
            $req = $this->bdd->prepare('SELECT game FROM users_games WHERE user = :user AND game = :game');

            $req->execute(array(
                'user' => $data['id'],
                'game' => $searchParams['appid']
            ));

            $resultSQL = $req->fetch();
        }
        if (!$resultSQL) {
            return new Response($resultSQL);
        } else {
            return new Response(true);
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getToken(Request $request): array
    {
        $authorizationHeader = $request->headers->get('Authorization');
        $authorizationHeaderArray = explode(' ', $authorizationHeader);
        $token = $authorizationHeaderArray[0] ?? null;
        $data = (new TokenManager())->decode($token);
        return $data;
    }
    /**
     * @param $data
     * @return bool
     */
    public function checkToken($data): bool
    {
        if ($data['exp'] > time()) {
            return true;
        }
        return false;
    }
}
