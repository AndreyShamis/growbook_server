<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NodeCommandRepository")
 * @ORM\Table(name="commands", uniqueConstraints={@ORM\UniqueConstraint(name="unique_key_plant", columns={"cmd_key", "plant_id"})})
 * @ORM\HasLifecycleCallbacks()
 */
class NodeCommand
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint", options={"unsigned"=true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cmd_key = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cmd_value = '';

    /**
     * @ORM\Column(type="boolean", options={"default"="0"})
     */
    private $published = false;

    /**
     * @ORM\Column(type="boolean", options={"default"="0"})
     */
    private $received = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Plant", inversedBy="nodeCommands")
     * @ORM\JoinColumn(name="plant_id", nullable=false)
     */
    private $plant;

    /**
     * @ORM\Column(type="boolean", options={"default"="0"})
     */
    private $can_be_deleted = false;


    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCmdKey(): string
    {
        return $this->cmd_key;
    }

    public function setCmdKey(string $cmd_key): self
    {
        $this->cmd_key = $cmd_key;

        return $this;
    }

    public function getCmdValue(): string
    {
        return $this->cmd_value;
    }

    public function setCmdValue(string $cmd_value): self
    {
        $this->cmd_value = $cmd_value;

        return $this;
    }

    public function getPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getReceived(): bool
    {
        return $this->received;
    }

    public function setReceived(bool $received): self
    {
        $this->received = $received;

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

    /**
     * @ORM\PreFlush()
     * @throws \Exception
     */
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getPlant(): ?Plant
    {
        return $this->plant;
    }

    public function setPlant(?Plant $plant): self
    {
        $this->plant = $plant;

        return $this;
    }

    public function getCanBeDeleted(): bool
    {
        return $this->can_be_deleted;
    }

    public function setCanBeDeleted(bool $can_be_deleted): self
    {
        $this->can_be_deleted = $can_be_deleted;

        return $this;
    }

    public function __toString()
    {
        return $this->getCmdKey() . ':' . $this->getCmdValue();
    }
}
