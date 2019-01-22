<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TeamMembers
 *
 * @ORM\Table(name="team_members")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeamMembersRepository")
 */
class TeamMembers
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
     * @ORM\Column(name="team_id", type="integer")
     */
    private $teamId;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_accepted", type="boolean", nullable=true)
     */
    private $isAccepted;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_denied", type="boolean", nullable=true)
     */
    private $isDenied;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;


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
     * Set teamId.
     *
     * @param int $teamId
     *
     * @return TeamMembers
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

    /**
     * Set userId.
     *
     * @param int $userId
     *
     * @return TeamMembers
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set isAccepted.
     *
     * @param bool|null $isAccepted
     *
     * @return TeamMembers
     */
    public function setIsAccepted($isAccepted = null)
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }

    /**
     * Get isAccepted.
     *
     * @return bool|null
     */
    public function getIsAccepted()
    {
        return $this->isAccepted;
    }

    /**
     * Set isDenied.
     *
     * @param bool|null $isDenied
     *
     * @return TeamMembers
     */
    public function setIsDenied($isDenied = null)
    {
        $this->isDenied = $isDenied;

        return $this;
    }

    /**
     * Get isDenied.
     *
     * @return bool|null
     */
    public function getIsDenied()
    {
        return $this->isDenied;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return TeamMembers
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}
