<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EvernoteUser
 *
 * @ORM\Table(name="evernote_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EvernoteUserRepository")
 */
class EvernoteUser
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
     * @ORM\Column(name="evernote_id", type="integer", unique=true)
     */
    private $evernoteId;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, unique=true)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="token_expires_at", type="datetime")
     */
    private $tokenExpiresAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     *
     */
    public function __construct()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set evernoteId
     *
     * @param integer $evernoteId
     *
     * @return EvernoteUser
     */
    public function setEvernoteId($evernoteId)
    {
        $this->evernoteId = $evernoteId;

        return $this;
    }

    /**
     * Get evernoteId
     *
     * @return int
     */
    public function getEvernoteId()
    {
        return $this->evernoteId;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return EvernoteUser
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set tokenExpiresAt
     *
     * @param \DateTime $tokenExpiresAt
     *
     * @return EvernoteUser
     */
    public function setTokenExpiresAt($tokenExpiresAt)
    {
        $this->tokenExpiresAt = $tokenExpiresAt;

        return $this;
    }

    /**
     * Get tokenExpiresAt
     *
     * @return \DateTime
     */
    public function getTokenExpiresAt()
    {
        return $this->tokenExpiresAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return EvernoteUser
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return EvernoteUser
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}

