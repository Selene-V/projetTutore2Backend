<?php

namespace App\Controller\Users;

use App\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use PDO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Register extends AbstractController
{
    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request)
    {
        $bdd = new PDO('mysql:host=127.0.0.1;dbname=projettutore2', 'root', '');

        $requestContent = $request->getContent();

        $searchParams = $this->parseRequestContent($requestContent);

        $email = urldecode($searchParams['email']);
        $password = $searchParams['password'];
        $confPass = $searchParams['confPass'];

        if (!empty($email) && !empty($password) && !empty($confPass)) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $reqemail = $bdd->prepare("SELECT * FROM user WHERE email = ?");
                $reqemail->execute(array($email));
                $emailexist = $reqemail->rowCount();
                if ($emailexist == 0) {
                    if ($password == $confPass) {
                        $password = password_hash($password, PASSWORD_DEFAULT);
                        $insertmbr = $bdd->prepare("INSERT INTO user(email, password) VALUES(?, ?)");
                        $insertmbr->execute(array($email, $password));
                    } else {
                        $error = "Your passwords don't match !";
                    }
                } else {
                    $error = "Email address already used !";
                }
            } else {
                $error = "Your email address is not valid !";
            }
        } else {
            $error = "All fields must be completed !";
        }

        if (isset($error)) {
            return new JsonResponse($error);
        } else {
            return new JsonResponse(true);
        }
    }
}
