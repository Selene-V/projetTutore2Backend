<?php

namespace App\Controller\Users;

use App\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use PDO;
use App\Manager\TokenManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Config\Config;

class Connection extends AbstractController
{
    /**
     * @Route("/connection", name="connection", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function connection(Request $request): JsonResponse
    {
        $dbname = Config::config('pdo_dbname');
        $host = Config::config('pdo_host');
        $user = Config::config('pdo_user');
        $password = Config::config('pdo_password');

        $dsn = sprintf(
            'mysql:dbname=%s;host=%s',
            $dbname,
            $host
        );

        $tokenManager = new TokenManager();
        $bdd = new PDO($dsn, $user, $password);

        $requestContent = $request->getContent();

        $searchParams = $this->parseRequestContent($requestContent);

        $email = urldecode($searchParams['email']);
        $password = $searchParams['password'];

        $req = $bdd->prepare("SELECT id, password FROM user WHERE email = :email");
        $req->execute(array(
            'email' => $email
        ));
        $result = $req->fetch();

        if (!$result) {
            return new JsonResponse('Wrong login or password !');
        } else {
            $isPasswordCorrect = password_verify($password, $result['password']);
            if ($isPasswordCorrect) {
                $token = $tokenManager->encode([
                    "iss" => "http://projettutbackend2",
                    "aud" => "http://projettutbackend2",
                    "iat" => time(),
                    "exp" => time() + 86400,
                    "id" => $result['id']
                ]);

                return new JsonResponse($token);
            } else {
                return new JsonResponse('Wrong login or password !');
            }
        }
    }
}
