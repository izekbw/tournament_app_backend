<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeamRepository")
 */
class Team
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
     * @var string
     *
     * @ORM\Column(name="leader_uuid", type="string", length=255)
     */
    private $leaderUuid;

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
     * @return Team
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
     * Set leaderUuid.
     *
     * @param int $leaderUuid
     *
     * @return Team
     */
    public function setLeaderUuid($leaderUuid)
    {
        $this->leaderUuid = $leaderUuid;

        return $this;
    }

    /**
     * Get leaderUuid.
     *
     * @return string
     */
    public function getLeaderUuid()
    {
        return $this->leaderUuid;
    }

}
