<?php

namespace App\Controller\Users;

use Symfony\Component\Routing\Annotation\Route;
use PDO;
use Symfony\Component\HttpFoundation\Response;

class Register
{
    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register()
    {
        $bdd = new PDO('mysql:host=127.0.0.1;dbname=projettutore2', 'root', '');

        $email = htmlspecialchars($_POST['email']);
        $password = sha1($_POST['password']);
        $confPass = sha1($_POST['confPass']);
        if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['confPass'])) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $reqemail = $bdd->prepare("SELECT * FROM user WHERE email = ?");
                $reqemail->execute(array($email));
                $emailexist = $reqemail->rowCount();
                if ($emailexist == 0) {
                    if ($password == $confPass) {
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
            return new Response($error);
        } else {
            return new Response(true);
        }
    }
}
