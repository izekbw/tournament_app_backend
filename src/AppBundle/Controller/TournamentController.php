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
use AppBundle\Entity\User;
use AppBundle\Entity\tournament_bracket_team;
use AppBundle\Entity\tournament_teams;

class TournamentController extends FOSRestController
{
    /**
     * @Rest\Get("/api/tournament/getBracket")
     * @throws \Exception
     * @return JsonResponse
     */
    public function getBracket(Request $request)
    {
        $tournamentBracketTeamRepository = $this->getDoctrine()->getRepository('AppBundle:tournament_bracket_team');
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        $teamRepository = $this->getDoctrine()->getRepository('AppBundle:Team');
        $tournamentId = $request->query->get('tournamentId');

        $bracketItems = $tournamentBracketTeamRepository->findBy([
            'tournamentId' => $tournamentId,
        ]);
        $bracketArray = [];

        foreach ($bracketItems as $bracketItem) {
            /** @var tournament_bracket_team $bracketItem */
            if ($bracketItem->getTeamId()) {
                $teamId = $bracketItem->getTeamId();
                $team = $teamRepository->find($teamId);
                $teamName = $team->getName();
                $leader = $userRepository->findOneBy([
                    'uuid' => $team->getLeaderUuid(),
                ]);
                $leaderName = $leader->getUsername();
            } else {
                $teamName = null;
                $leaderName = null;
            }

            $bracketArray[$bracketItem->getRound()][] = [
                'teamName' => $teamName,
                'leaderName' => $leaderName,
            ];
        }
        $array = [];

        foreach ($bracketArray as $item) {
            $array[] = $item;
        }
        return new JsonResponse(["bracket"=>$array],200);
    }

    /**
     * @Rest\Post("/api/tournament/register")
     * @throws \Exception
     * @return JsonResponse
     */
    public function registerTeamForTournament(Request $request)
    {
        $tournamentRepository = $this->getDoctrine()->getRepository('AppBundle:Tournament');
        $tournamentTeamsRepository = $this->getDoctrine()->getRepository('AppBundle:tournament_teams');
        $tournamentBracketTeamRepository = $this->getDoctrine()->getRepository('AppBundle:tournament_bracket_team');
        $gameRepository = $this->getDoctrine()->getRepository('AppBundle:Game');
        $em = $this->getDoctrine()->getManager();

        $teamId = $request->request->get('teamId');
        $tournamentId = $request->request->get('tournamentId');
        $gameId = $request->request->get('gameId');

        $teamCheck = $tournamentTeamsRepository->find($teamId);

        if ($teamCheck) {
            return new JsonResponse(["message"=>"Team is already registered to this tournament."],400);
        }
        $tournamentTeam = new tournament_teams();
        $tournamentTeam->setTeamId($teamId);
        $tournamentTeam->setTournamentId($tournamentId);

        $em->persist($tournamentTeam);
        $em->flush();

        $tournament = $tournamentRepository->find($tournamentId);
        $maxTeams = $tournament->getTeamsNumber();
        $currentTeams = count($tournamentTeamsRepository->findBy([
            'tournamentId' => $tournamentId,
        ]));
        $roundCounterHelper = $maxTeams;

        if ($currentTeams == $maxTeams) {
            $tournament->setIsStarted(true);

            $bracketTeams = $tournamentTeamsRepository->findBy([
                'tournamentId' => $tournamentId,
            ]);
            shuffle($bracketTeams);
            $bracketPlaces = 2 * count($bracketTeams) - 1;
            $round = 1;
            for ($place = 1; $place <= $bracketPlaces; $place++) {
                $bracketItem = new tournament_bracket_team();
                $bracketItem->setTournamentId($tournamentId);
                $bracketItem->setBracketPlace($place);
                $bracketItem->setRound($round);
                if (array_key_exists($place - 1, $bracketTeams)) {
                    $bracketItem->setTeamId($bracketTeams[$place - 1]->getTeamId());
                }
                $em->persist($bracketItem);
                $em->flush();

                if ($place == $roundCounterHelper) {$roundCounterHelper = $roundCounterHelper + $maxTeams / 2;
                    $maxTeams = $maxTeams / 2;
                    $round++;
                }
            }
        }
            $tournamentsCollection = $tournamentRepository->findBy([
                'gameId' => $gameId,
                'isCompleted' => false,
            ]);

            $openTournaments = [];
            $startedTournaments = [];

            foreach ($tournamentsCollection as $tournament) {
                $game = $gameRepository->find($tournament->getGameId());
                $registeredTeams = $tournamentTeamsRepository->findBy([
                    'tournamentId' => $tournament->getId(),
                ]);

                if ($tournament->getIsStarted()) {
                    $startedTournaments[] = array(
                        'id' => $tournament->getId(),
                        'name' => $tournament->getName(),
                        'game_id' => $tournament->getGameId(),
                        'teams_number' => $tournament->getTeamsNumber(),
                        'is_completed' => $tournament->getIsCompleted(),
                        'is_started' => $tournament->getIsStarted(),
                        'description' => $tournament->getDescription(),
                        'registeredTeams' => count($registeredTeams),
                        'game' => [
                            'id' => $game->getId(),
                            'name' => $game->getName(),
                            'shortName' => $game->getShortName(),
                            'imageUrl' => $game->getImageUrl(),
                            'logoUrl' => $game->getLogoUrl(),
                        ]
                    );
                } else {
                    $openTournaments[] = array(
                        'id' => $tournament->getId(),
                        'name' => $tournament->getName(),
                        'game_id' => $tournament->getGameId(),
                        'teams_number' => $tournament->getTeamsNumber(),
                        'is_completed' => $tournament->getIsCompleted(),
                        'is_started' => $tournament->getIsStarted(),
                        'description' => $tournament->getDescription(),
                        'registeredTeams' => count($registeredTeams),
                        'game' => [
                            'id' => $game->getId(),
                            'name' => $game->getName(),
                            'shortName' => $game->getShortName(),
                            'imageUrl' => $game->getImageUrl(),
                            'logoUrl' => $game->getLogoUrl(),
                        ]
                    );
                }
            }
        return new JsonResponse(["message"=>'Successfully registered to a tournament', "startedTournaments"=>$startedTournaments, "openTournaments"=>$openTournaments],200);
    }

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
                'is_started' => $tournament->getIsStarted(),
                'description' => $tournament->getDescription(),
            );
        }
        return new JsonResponse(["tournaments"=>$tournamentsArray],200);
    }

    /**
     * @Rest\Get("/api/tournament/getActiveForGame")
     * @param $request
     * @throws \Exception
     * @return JsonResponse
     */
    public function getActiveForGame(Request $request)
    {
        $gameId = $request->query->get('gameId');
        $repository = $this->getDoctrine()->getRepository('AppBundle:Tournament');
        $gameRepository = $this->getDoctrine()->getRepository('AppBundle:Game');
        $tournamentTeamsRepository = $this->getDoctrine()->getRepository('AppBundle:tournament_teams');

        $tournamentsCollection = $repository->findBy([
            'gameId' => $gameId,
                   'isCompleted' => false,
        ]);

        $openTournaments = [];
        $startedTournaments = [];

        foreach ($tournamentsCollection as $tournament) {
            $game = $gameRepository->find($tournament->getGameId());
            $registeredTeams = $tournamentTeamsRepository->findBy([
                'tournamentId' => $tournament->getId(),
            ]);

            if ($tournament->getIsStarted()) {
                $startedTournaments[] = array(
                    'id' => $tournament->getId(),
                    'name' => $tournament->getName(),
                    'game_id' => $tournament->getGameId(),
                    'teams_number' => $tournament->getTeamsNumber(),
                    'is_completed' => $tournament->getIsCompleted(),
                    'is_started' => $tournament->getIsStarted(),
                    'description' => $tournament->getDescription(),
                    'registeredTeams' => count($registeredTeams),
                    'game' => [
                        'id' => $game->getId(),
                        'name' => $game->getName(),
                        'shortName' => $game->getShortName(),
                        'imageUrl' => $game->getImageUrl(),
                        'logoUrl' => $game->getLogoUrl(),
                    ]
                );
            } else {
                $openTournaments[] = array(
                    'id' => $tournament->getId(),
                    'name' => $tournament->getName(),
                    'game_id' => $tournament->getGameId(),
                    'teams_number' => $tournament->getTeamsNumber(),
                    'is_completed' => $tournament->getIsCompleted(),
                    'is_started' => $tournament->getIsStarted(),
                    'description' => $tournament->getDescription(),
                    'registeredTeams' => count($registeredTeams),
                    'game' => [
                        'id' => $game->getId(),
                        'name' => $game->getName(),
                        'shortName' => $game->getShortName(),
                        'imageUrl' => $game->getImageUrl(),
                        'logoUrl' => $game->getLogoUrl(),
                    ]
                );
            }
        }
        return new JsonResponse(["startedTournaments"=>$startedTournaments, "openTournaments"=>$openTournaments],200);
    }
}