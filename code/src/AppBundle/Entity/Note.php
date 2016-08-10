<?php

namespace AppBundle\Entity;

class Note
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $content;

    /**
     * @var \DateTime
     */
    private $remindAt;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id) : Note
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title) : Note
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent() : string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content) : Note
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRemindAt() : \DateTime
    {
        return $this->remindAt;
    }

    /**
     * @param \DateTime $remindAt
     *
     * @return $this
     */
    public function setRemindAt($remindAt) : Note
    {
        $this->remindAt = $remindAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt() : \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt) : Note
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
