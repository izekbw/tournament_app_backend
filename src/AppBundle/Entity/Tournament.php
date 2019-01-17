<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tournament
 *
 * @ORM\Table(name="tournament")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TournamentRepository")
 */
class Tournament
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="game_id", type="integer")
     */
    private $gameId;

    /**
     * @var int
     *
     * @ORM\Column(name="teams_number", type="integer")
     */
    private $teamsNumber;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_completed", type="boolean", nullable=true, options={"default" : false})
     */
    private $isCompleted;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_started", type="boolean", nullable=true, options={"default" : false})
     */
    private $isStarted;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;


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
     * Set name.
     *
     * @param string $name
     *
     * @return Tournament
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set gameId.
     *
     * @param int $gameId
     *
     * @return Tournament
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;

        return $this;
    }

    /**
     * Get gameId.
     *
     * @return int
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * Set teamsNumber.
     *
     * @param int $teamsNumber
     *
     * @return Tournament
     */
    public function setTeamsNumber($teamsNumber)
    {
        $this->teamsNumber = $teamsNumber;

        return $this;
    }

    /**
     * Get teamsNumber.
     *
     * @return int
     */
    public function getTeamsNumber()
    {
        return $this->teamsNumber;
    }

    /**
     * Set isCompleted.
     *
     * @param bool|null $isCompleted
     *
     * @return Tournament
     */
    public function setIsCompleted($isCompleted = null)
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }

    /**
     * Get isCompleted.
     *
     * @return bool|null
     */
    public function getIsCompleted()
    {
        return $this->isCompleted;
    }

    /**
     * Set isStarted.
     *
     * @param bool|null $isStarted
     *
     * @return Tournament
     */
    public function setIsStarted($isStarted = null)
    {
        $this->isStarted = $isStarted;

        return $this;
    }

    /**
     * Get isStarted.
     *
     * @return bool|null
     */
    public function getIsStarted()
    {
        return $this->isStarted;
    }
    
    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return Tournament
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }
}
