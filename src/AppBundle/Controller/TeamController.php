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
use AppBundle\Entity\Team;
use AppBundle\Entity\TeamMembers;
use Ramsey\Uuid\Uuid;

class TeamController extends FOSRestController
{

    /**
     * @Rest\Post("/api/team/create")
     * @param Request $request
     * @throws \Exception
     * @return JsonResponse
     */
        public function createTeam(Request $request)
    {
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        $teamRepository = $this->getDoctrine()->getRepository('AppBundle:Team');
        $teamMembersRepository = $this->getDoctrine()->getRepository('AppBundle:TeamMembers');
        $em = $this->getDoctrine()->getManager();

        $uuid = $request->request->get('uuid');

        if (!$uuid) {
            return new JsonResponse(["message"=>"User is not logged in"],400);
        } else {
            $user = $userRepository->findOneBy([
                'uuid' => $uuid,
            ]);

            if (!$user) {
                return new JsonResponse(["message"=>"User does not exist"],400);
            }
        }

        $name = $request->request->get('name');

        if (!$name) {
            return new JsonResponse(["message"=>"Please enter name for your team"],400);
        } else {
            $teamCheck = $teamRepository->findOneBy([
                'name' => $name,
            ]);

            if ($teamCheck) {
                return new JsonResponse(["message"=>"Team already exists"],400);
            }
        }

        $leaderUuid = $user->getUuid();

        $team = new Team();

        $team->setName($name);
        $team->setLeaderUuid($leaderUuid);

        $em->persist($team);
        $em->flush();

        $teamId = $team->getId();

        $teamMember = new TeamMembers();
        $teamMember->setUsername($user->getUsername());
        $teamMember->setIsAccepted(true);
        $teamMember->setIsDenied(false);
        $teamMember->setTeamId($teamId);
        $teamMember->setUserId($user->getId());

        $em->persist($teamMember);
        $em->flush();

        $teamsWithRoster = $this->fetchAllWithRoster($uuid);

        return new JsonResponse(["message"=>"Created team: " .$team->getName(), 'teamsWithRoster' => $teamsWithRoster],200);
    }

    /**
     * @Rest\Post("/api/team/invite/accept")
     * @param Request $request
     * @throws \Exception
     * @return JsonResponse
     */
    public function acceptInvite(Request $request)
    {
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        $teamRepository = $this->getDoctrine()->getRepository('AppBundle:Team');
        $teamMembersRepository = $this->getDoctrine()->getRepository('AppBundle:TeamMembers');
        $em = $this->getDoctrine()->getManager();

        $inviteId = $request->request->get('id');
        if (!$inviteId) {
            return new JsonResponse(["message"=>"This team invite doesn't exist"],400);
        }

        $uuid = $request->request->get('uuid');

        if (!$uuid) {
            return new JsonResponse(["message"=>"User is not logged in"],400);
        } else {
            $user = $userRepository->findOneBy([
                'uuid' => $uuid,
            ]);

            if (!$user) {
                return new JsonResponse(["message"=>"This user doesn't exist"],400);
            }
        }

        $invite = $teamMembersRepository->findOneBy([
            'id' => $inviteId,
            'userId' => $user->getId(),
        ]);

        if (!$invite) {
            return new JsonResponse(["message"=>"This team invite doesn't exist"],400);
        }


        $invite->setIsAccepted(true);

        $em->persist($invite);
        $em->flush();

        $teamsWithRoster = $this->fetchAllWithRoster($uuid);
        $invites = $this->fetchInvites($uuid);

        return new JsonResponse([
            "message" => "Team invitation accepted",
            "teamsWithRoster" => $teamsWithRoster,
            "invites" => $invites,
        ],200);
    }

    /**
     * @Rest\Post("/api/team/invite/deny")
     * @param Request $request
     * @throws \Exception
     * @return JsonResponse
     */
    public function denyInvite(Request $request)
    {
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        $teamRepository = $this->getDoctrine()->getRepository('AppBundle:Team');
        $teamMembersRepository = $this->getDoctrine()->getRepository('AppBundle:TeamMembers');
        $em = $this->getDoctrine()->getManager();

        $inviteId = $request->request->get('id');
        if (!$inviteId) {
            return new JsonResponse(["message"=>"This team invite doesn't exist"],400);
        }

        $uuid = $request->request->get('uuid');

        if (!$uuid) {
            return new JsonResponse(["message"=>"User is not logged in"],400);
        } else {
            $user = $userRepository->findOneBy([
                'uuid' => $uuid,
            ]);

            if (!$user) {
                return new JsonResponse(["message"=>"This user doesn't exist"],400);
            }
        }

        $invite = $teamMembersRepository->findOneBy([
            'id' => $inviteId,
            'userId' => $user->getId(),
        ]);

        if (!$invite) {
            return new JsonResponse(["message"=>"This team invite doesn't exist"],400);
        }


        $invite->setIsDenied(true);

        $em->persist($invite);
        $em->flush();

        $teamsWithRoster = $this->fetchAllWithRoster($uuid);
        $invites = $this->fetchInvites($uuid);

        return new JsonResponse([
            "message" => "Team invitation denied",
            "teamsWithRoster" => $teamsWithRoster,
            "invites" => $invites,
        ],200);
    }

    /**
     * @Rest\Post("/api/team/invite")
     * @param Request $request
     * @throws \Exception
     * @return JsonResponse
     */
    public function inviteMember(Request $request)
    {
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        $teamRepository = $this->getDoctrine()->getRepository('AppBundle:Team');
        $teamMembersRepository = $this->getDoctrine()->getRepository('AppBundle:TeamMembers');
        $em = $this->getDoctrine()->getManager();

        $teamId = $request->request->get('teamId');
        if (!$teamId) {
            return new JsonResponse(["message"=>"No team specified"],400);
        }

        $username = $request->request->get('username');
        if (!$username) {
            return new JsonResponse(["message"=>"Please enter username to invite"],400);
        } else {
            $invitedMember = $userRepository->findOneBy([
                'username' => $username,
            ]);

            if (!$invitedMember) {
                return new JsonResponse(["message"=>"There is no user with name: " . $username],400);
            }
        }

        $uuid = $request->request->get('uuid');
        if (!$uuid) {
            return new JsonResponse(["message"=>"User is not logged in"],400);
        } else {
            $user = $userRepository->findOneBy([
                'uuid' => $uuid,
            ]);

            if (!$user) {
                return new JsonResponse(["message"=>"User does not exist"],400);
            }

            $userTeamMemberCheck = $teamMembersRepository->findOneBy([
                'userId' => $user->getId(),
                'teamId' => $teamId,
            ]);

            if (!$userTeamMemberCheck) {
                return new JsonResponse(["message"=>"You are not a member of this team!"],400);
            }
        }

        $invitedMemberCheck = $teamMembersRepository->findOneBy([
            'userId' => $invitedMember->getId(),
            'teamId' => $teamId
        ]);

        if ($invitedMemberCheck) {
            return new JsonResponse(["message"=>"This member has already been invited"],400);
        }

        $teamMember = new TeamMembers();

        $teamMember->setTeamId($teamId);
        $teamMember->setUserId($invitedMember->getId());
        $teamMember->setIsDenied(false);
        $teamMember->setIsAccepted(false);
        $teamMember->setUsername($invitedMember->getUsername());

        $em->persist($teamMember);
        $em->flush();

        return new JsonResponse(["message"=>"Invited member: " .$invitedMember->getUsername()],200);
    }



    /**
     * @param $uuid
     * @return array|JsonResponse
     */
    public function fetchAllWithRoster($uuid)
    {
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        $teamRepository = $this->getDoctrine()->getRepository('AppBundle:Team');
        $teamMembersRepository = $this->getDoctrine()->getRepository('AppBundle:TeamMembers');

        if (!$uuid) {
            return null;
        } else {
            $user = $userRepository->findOneBy([
                'uuid' => $uuid,
            ]);

            if (!$user) {
                return null;
            }
        }

        $userId = $user->getId();

        $teamMembers = $teamMembersRepository->findBy([
            'userId' => $userId,
            'isAccepted' => true,
        ]);

        $teams = [];

        foreach ($teamMembers as $teamMember) {
            $teamId = $teamMember->getTeamId();
            $teams[] = $teamRepository->findOneBy([
                'id' => $teamId,
            ]);
        }

        $teamsWithRoster = [];


        foreach ($teams as $team) {
            /** @var Team $team */
            $teamId = $team->getId();
            $rosterCollection = $teamMembersRepository->findBy([
                'teamId' => $teamId,
                'isAccepted' => 1,
            ]);

            $rosterArray = [];
            foreach ($rosterCollection as $rosterItem) {
                $rosterArray[] = [
                    'user_id' => $rosterItem->getId(),
                    'username' => $rosterItem->getUsername(),
                ];
            }
            $teamsWithRoster[] = [
                'team_id' => $team->getId(),
                'team_name' => $team->getName(),
                'leader_uuid' => $team->getLeaderUuid(),
                'roster' => $rosterArray,
            ];
        }

        return $teamsWithRoster;
    }

    /**
     * @Rest\Get("/api/team/getAll")
     * @param Request $request
     * @throws \Exception
     * @return JsonResponse
     */
    public function getAllWithRoster(Request $request)
    {
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        $teamRepository = $this->getDoctrine()->getRepository('AppBundle:Team');
        $teamMembersRepository = $this->getDoctrine()->getRepository('AppBundle:TeamMembers');

        $uuid = $request->query->get('uuid');

        if (!$uuid) {
            return new JsonResponse(["message"=>"User is not logged in"],400);
        } else {
            $user = $userRepository->findOneBy([
                'uuid' => $uuid,
            ]);

            if (!$user) {
                return new JsonResponse(["message"=>"User does not exist"],400);
            }
        }

        $userId = $user->getId();

        $teamMembers = $teamMembersRepository->findBy([
            'userId' => $userId,
            'isAccepted' => true,
        ]);

        $teams = [];

        foreach ($teamMembers as $teamMember) {
            $teamId = $teamMember->getTeamId();
            $teams[] = $teamRepository->findOneBy([
                'id' => $teamId,
            ]);
        }

        $teamsWithRoster = [];


        foreach ($teams as $team) {
            /** @var Team $team */
            $teamId = $team->getId();
            $rosterCollection = $teamMembersRepository->findBy([
                'teamId' => $teamId,
                'isAccepted' => 1,
            ]);

            $rosterArray = [];
            foreach ($rosterCollection as $rosterItem) {
                $rosterArray[] = [
                    'user_id' => $rosterItem->getId(),
                    'username' => $rosterItem->getUsername(),
                ];
            }
            $teamsWithRoster[] = [
                'team_id' => $team->getId(),
                'team_name' => $team->getName(),
                'leader_uuid' => $team->getLeaderUuid(),
                'roster' => $rosterArray,
            ];
        }

        return new JsonResponse(["teamsWithRoster"=>$teamsWithRoster],200);
    }

    /**
     * @param $uuid
     * @return array
     */

    public function fetchInvites($uuid)
    {
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        $teamRepository = $this->getDoctrine()->getRepository('AppBundle:Team');
        $teamMembersRepository = $this->getDoctrine()->getRepository('AppBundle:TeamMembers');

        $user = $userRepository->findOneBy([
            'uuid' => $uuid,
        ]);

        $userId = $user->getId();

        $pendingInvites = $teamMembersRepository->findBy([
            'userId' => $userId,
            'isAccepted' => false,
            'isDenied' => false,
        ]);

        $invites = [];

        foreach ($pendingInvites as $pendingInvite) {
            $team = $teamRepository->findOneBy([
                'id' => $pendingInvite->getTeamId(),
            ]);

            $invites[] = [
                'id' => $pendingInvite->getId(),
                'teamName' => $team->getName(),
            ];
        }

        return $invites;
    }

    /**
     * @Rest\Get("/api/team/getInvites")
     * @param Request $request
     * @throws \Exception
     * @return JsonResponse
     */
    public function getInvites(Request $request)
    {
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        $teamRepository = $this->getDoctrine()->getRepository('AppBundle:Team');
        $teamMembersRepository = $this->getDoctrine()->getRepository('AppBundle:TeamMembers');

        $uuid = $request->query->get('uuid');

        if (!$uuid) {
            return new JsonResponse(["message"=>"User is not logged in"],400);
        } else {
            $user = $userRepository->findOneBy([
                'uuid' => $uuid,
            ]);

            if (!$user) {
                return new JsonResponse(["message"=>"User does not exist"],400);
            }
        }

        $userId = $user->getId();

        $pendingInvites = $teamMembersRepository->findBy([
            'userId' => $userId,
            'isAccepted' => false,
            'isDenied' => false,
        ]);

        $invites = [];

        foreach ($pendingInvites as $pendingInvite) {
            $team = $teamRepository->findOneBy([
                'id' => $pendingInvite->getTeamId(),
            ]);

            $invites[] = [
                'id' => $pendingInvite->getId(),
                'teamName' => $team->getName(),
            ];
        }

        return new JsonResponse(["invites"=>$invites],200);
    }

    /**
     * @Rest\Delete("/api/team/member/leave")
     * @param Request $request
     * @throws \Exception
     * @return JsonResponse
     */
    public function leaveTeam(Request $request)
    {
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        $teamMembersRepository = $this->getDoctrine()->getRepository('AppBundle:TeamMembers');
        $em = $this->getDoctrine()->getManager();

        $teamId = $request->query->get('teamId');

        if (!$teamId) {
            return new JsonResponse(["message"=>"No team specified"],400);
        }

        $uuid = $request->query->get('uuid');

        if (!$uuid) {
            return new JsonResponse(["message"=>"User does not exist"],400);
        } else {
            $user = $userRepository->findOneBy([
                'uuid' => $uuid,
            ]);

            if (!$user) {
                return new JsonResponse(["message"=>"User does not exist"],400);
            }
        }

        $teamMember = $teamMembersRepository->findOneBy([
            'teamId' => $teamId,
            'userId' => $user->getId(),
        ]);

        $em->remove($teamMember);
        $em->flush();

        $teamsWithRoster = $this->fetchAllWithRoster($uuid);

        return new JsonResponse(["teamsWithRoster"=>$teamsWithRoster, 'message' => 'Successfully left team'],200);
    }
}