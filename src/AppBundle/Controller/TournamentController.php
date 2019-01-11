<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Tournament;

class TournamentController extends FOSRestController
{
    /**
     * @Rest\Get("/api/tournament/get")
     * @throws \Exception
     * @return JsonResponse
     */
    public function getAll()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Tournament');
        $tournamentsCollection = $repository->findAll();
        $tournamentsArray = [];

        foreach ($tournamentsCollection as $tournament) {
            $tournamentsArray[] = array(
                'id' => $tournament->getId(),
                'name' => $tournament->getName(),
                'game_id' => $tournament->getGameId(),
                'teams_number' => $tournament->getTeamsNumber(),
                'is_completed' => $tournament->getIsCompleted(),
                'description' => $tournament->getDescription(),
            );
        }
        return new JsonResponse(["tournaments"=>$tournamentsArray],200);
    }

    /**
     * @Rest\Get("/api/tournament/getOpenWithGame")
     * @throws \Exception
     * @return JsonResponse
     */
    public function getAllOpenWithGame()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Tournament');
        $gameRepository = $this->getDoctrine()->getRepository('AppBundle:Game');

        $tournamentsCollection = $repository->findBy([
            'isCompleted' => false,
        ]);

        $tournamentsArray = [];

        foreach ($tournamentsCollection as $tournament) {
            $game = $gameRepository->find($tournament->getGameId());

            $tournamentsArray[] = array(
                'id' => $tournament->getId(),
                'name' => $tournament->getName(),
                'game_id' => $tournament->getGameId(),
                'teams_number' => $tournament->getTeamsNumber(),
                'is_completed' => $tournament->getIsCompleted(),
                'description' => $tournament->getDescription(),
                'game' => [
                    'id' => $game->getId(),
                    'name' => $game->getName(),
                    'shortName' => $game->getShortName(),
                    'imageUrl' => $game->getImageUrl(),
                    'logoUrl' => $game->getLogoUrl(),
                ]
            );
        }
        return new JsonResponse(["tournaments"=>$tournamentsArray],200);
    }
}