<?php

namespace AppBundle\Entity;

class Note
{
    /**
     * @var string
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
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId(string $id) : Note
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
    public function setTitle(string $title) : Note
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
    public function setContent(string $content) : Note
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
    public function setRemindAt(\DateTime $remindAt) : Note
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
