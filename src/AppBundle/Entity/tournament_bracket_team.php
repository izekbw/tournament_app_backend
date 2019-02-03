<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * tournament_bracket_team
 *
 * @ORM\Table(name="tournament_bracket_team")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\tournament_bracket_teamRepository")
 */
class tournament_bracket_team
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
     * @ORM\Column(name="bracket_place", type="integer")
     */
    private $bracketPlace;

    /**
     * @var int
     *
     * @ORM\Column(name="round", type="integer")
     */
    private $round;

    /**
     * @var int|null
     *
     * @ORM\Column(name="team_id", type="integer", nullable=true)
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
     * @return tournament_bracket_team
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
     * Set bracketPlace.
     *
     * @param int $bracketPlace
     *
     * @return tournament_bracket_team
     */
    public function setBracketPlace($bracketPlace)
    {
        $this->bracketPlace = $bracketPlace;

        return $this;
    }

    /**
     * Get bracketPlace.
     *
     * @return int
     */
    public function getBracketPlace()
    {
        return $this->bracketPlace;
    }

    /**
     * Set round.
     *
     * @param int $round
     *
     * @return tournament_bracket_team
     */
    public function setRound($round)
    {
        $this->round = $round;

        return $this;
    }

    /**
     * Get round.
     *
     * @return int
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Set teamId.
     *
     * @param int|null $teamId
     *
     * @return tournament_bracket_team
     */
    public function setTeamId($teamId = null)
    {
        $this->teamId = $teamId;

        return $this;
    }

    /**
     * Get teamId.
     *
     * @return int|null
     */
    public function getTeamId()
    {
        return $this->teamId;
    }
}
