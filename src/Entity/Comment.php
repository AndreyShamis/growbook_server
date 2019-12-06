<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint", options={"unsigned"=true})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $happenedAt;

    /**
     * @ORM\Column(type="string", length=255, options={"default"=""})
     */
    private $commentName;

    /**
     * @ORM\Column(type="text", length=16383, options={"default"=""})
     */
    private $comment = '';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Plant", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plant;

    /**
     * @ORM\Column(type="boolean", options={"default"="1"})
     */
    private $enabled = true;

    public function __construct()
    {
        $now = new \DateTime();
        $this->setCreatedAt($now);
        $this->setUpdatedAt($now);
        $this->setHappenedAt($now);
        $this->setEnabled(true);
        $this->setCommentName($now->format('Y-m-d H:i:s'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getHappenedAt(): ?\DateTimeInterface
    {
        return $this->happenedAt;
    }

    public function setHappenedAt(\DateTimeInterface $happenedAt): self
    {
        $this->happenedAt = $happenedAt;

        return $this;
    }

    public function getCommentName(): string
    {
        return $this->commentName;
    }

    public function setCommentName(string $commentName): self
    {
        $this->commentName = $commentName;

        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getPlant(): Plant
    {
        return $this->plant;
    }

    public function setPlant(Plant $plant): self
    {
        $this->plant = $plant;

        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getId() . ':' . $this->getCommentName();
    }
}
