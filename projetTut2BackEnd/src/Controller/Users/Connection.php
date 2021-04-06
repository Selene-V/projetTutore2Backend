<?php

namespace App\Controller\Users;

use Symfony\Component\Routing\Annotation\Route;
use PDO;
use Symfony\Component\HttpFoundation\Response;

class Connection
{
    /**
     * @Route("/connection", name="connection", methods={"POST"})
     */
    public function connection()
    {
        $bdd = new PDO('mysql:host=127.0.0.1;dbname=projettutore2', 'root', '');

        $email = $_POST['email'];

        $req = $bdd->prepare("SELECT id, password FROM user WHERE email = ':email'");
        $req->execute(array(
            'email' => $email
        ));
        $result = $req->fetch();

        if (!$result) {
            return new Response('Wrong login or password !');
        } else {
            $isPasswordCorrect = password_verify($_POST['password'], $result['password']);
            if ($isPasswordCorrect) {
                session_start();
                $_SESSION['id'] = $result['id'];
                $_SESSION['email'] = $email;
                return new Response('You are connected !');
            } else {
                return new Response('Wrong login or password !');
            }
        }
    }
}
