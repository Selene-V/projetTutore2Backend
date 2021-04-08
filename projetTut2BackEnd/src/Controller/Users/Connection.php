<?php

namespace App\Controller\Users;

use App\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use PDO;
use Symfony\Component\HttpFoundation\Response;
use App\Manager\TokenManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Connection extends AbstractController
{
    /**
     * @Route("/connection", name="connection", methods={"POST"})
     */
    public function connection(Request $request)
    {
        $tokenManager = new TokenManager();
        $bdd = new PDO('mysql:host=127.0.0.1;dbname=projettutore2', 'root', '');

        $requestContent = $request->getContent();

        $searchParams = $this->parseRequestContent($requestContent);

        $email = $searchParams['email'];
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
