<?php

namespace App\Controller\Users;

use Symfony\Component\Routing\Annotation\Route;
use PDO;
use Symfony\Component\HttpFoundation\Response;
use App\Manager\TokenManager;

class Connection
{
    /**
     * @Route("/connection", name="connection", methods={"POST"})
     */
    public function connection()
    {
        $tokenManager = new TokenManager();
        $bdd = new PDO('mysql:host=127.0.0.1;dbname=projettutore2', 'root', '');

        $email = $_POST['email'];

        $req = $bdd->prepare("SELECT id, password FROM user WHERE email = :email");
        $req->execute(array(
            'email' => $email
        ));
        $result = $req->fetch();

        if (!$result) {
            return new Response('Wrong login or password !');
        } else {
            $isPasswordCorrect = password_verify($_POST['password'], $result['password']);
            if ($isPasswordCorrect) {
                $token = $tokenManager->encode([
                    "iss" => "http://projettutbackend2",
                    "aud" => "http://projettutbackend2",
                    "iat" => time(),
                    "exp" => time() + 86400,
                    'email' => $email
                ]);

                return new Response($token);
            } else {
                return new Response(false);
            }
        }
    }
}
