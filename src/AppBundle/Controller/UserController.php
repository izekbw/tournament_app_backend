<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;
use Ramsey\Uuid\Uuid;

class UserController extends FOSRestController
{

    /**
     * @Rest\Post("/api/register")
     * @param Request $request
     * @throws \Exception
     * @return JsonResponse
     */
    public function register(Request $request)
    {
        $user = new User();
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');

        $username = $request->request->get('username');
        $password = $request->request->get('password');

        if (!$username) {
            return new JsonResponse(["message"=>"Missing username"],400);
        } else {
            $checkUsername = $repository->findBy(['username' => $username]);
            if ($checkUsername) {
                return new JsonResponse(["message"=>"Username already exists"],400);
            }
            $user->setUsername($request->request->get('username'));
        }

        if (!$password) {
            return new JsonResponse(["message"=>"Missing password"],400);
        } else {
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        }
        $uuid = Uuid::uuid4();

        $user->setUuid($uuid);

        $em->persist($user);
        $em->flush();

        return new JsonResponse(["message"=>"Well done! You can now log in, " .$user->getUsername()],200);
    }

    /**
     * @Rest\Post("/api/login")
     * @param Request $request
     * @throws \Exception
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');

        $username = $request->request->get('username');
        $password = $request->request->get('password');

        if (!$username) {
            return new JsonResponse(["message"=>"Type your username"],400);
        } else {
            /** @var User $user */
            $user = $repository->findOneBy(['username' => $username]);
            if (!$user) {
                return new JsonResponse(["message"=>"User does not exist"],400);
            }
        }

        if (!$password) {
            return new JsonResponse(["message"=>"Type your password"],400);
        }
        else {
            $hash = $user->getPassword();
            $passwordCheck = password_verify($password, $hash);
            if (!$passwordCheck) {
                return new JsonResponse(["message"=>"Wrong password"],400);
            }
        }

        return new JsonResponse([
            "message" => "Successful login, welcome " .$user->getUsername(),
            "username" => $user->getUsername(),
            "uuid" => $user->getUuid(),
        ],200);
    }
}