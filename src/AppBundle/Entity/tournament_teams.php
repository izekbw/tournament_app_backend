<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * tournament_teams
 *
 * @ORM\Table(name="tournament_teams")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\tournament_teamsRepository")
 */
class tournament_teams
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="tournament_id", type="integer")
     */
    private $tournamentId;

    /**
     * @var int
     *
     * @ORM\Column(name="team_id", type="integer")
     */
    private $teamId;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tournamentId.
     *
     * @param int $tournamentId
     *
     * @return tournament_teams
     */
    public function setTournamentId($tournamentId)
    {
        $this->tournamentId = $tournamentId;

        return $this;
    }

    /**
     * Get tournamentId.
     *
     * @return int
     */
    public function getTournamentId()
    {
        return $this->tournamentId;
    }

    /**
     * Set teamId.
     *
     * @param int $teamId
     *
     * @return tournament_teams
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;

        return $this;
    }

    /**
     * Get teamId.
     *
     * @return int
     */
    public function getTeamId()
    {
        return $this->teamId;
    }
}
