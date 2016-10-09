<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\EvernoteUser", mappedBy="user")
     */
    private $evernoteUser;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getEvernoteUser()
    {
        return $this->evernoteUser;
    }

    /**
     * @param mixed $evernoteUser
     *
     * @return $this
     */
    public function setEvernoteUser($evernoteUser)
    {
        $this->evernoteUser = $evernoteUser;

        return $this;
    }
}