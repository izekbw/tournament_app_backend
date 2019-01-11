<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Game;

class GameController extends FOSRestController
{
    /**
     * @Rest\Get("/api/game/get")
     * @throws \Exception
     * @return JsonResponse
     */
    public function getAll()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Game');
        $gamesCollection = $repository->findAll();
        $gamesArray = [];

        foreach ($gamesCollection as $game) {
            $gamesArray[] = array(
                'id' => $game->getId(),
                'name' => $game->getName(),
                'shortName' => $game->getShortName(),
                'imageUrl' => $game->getImageUrl(),
                'logoUrl' => $game->getLogoUrl(),
                'website' => $game->getWebsite(),
                'description' => $game->getDescription()
            );
        }
        return new JsonResponse(["games"=>$gamesArray],200);
    }

    /**
     * @param string $id
     * @return object|void
     */
    public function get($id)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Game');
        $game = $repository->find($id);
    }
}