<?php

namespace App\Entity;

use App\Entity\Events\EventFeed;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FeedFertilizerRepository")
 */
class FeedFertilizer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Fertilizer", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $fertilizer;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Events\EventFeed", inversedBy="fertilizers")
     */
    private $event;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFertilizer(): ?Fertilizer
    {
        return $this->fertilizer;
    }

    public function setFertilizer(Fertilizer $fertilizer): self
    {
        $this->fertilizer = $fertilizer;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getEvent(): ?EventFeed
    {
        return $this->event;
    }

    public function setEvent(?EventFeed $event): self
    {
        $this->event = $event;

        return $this;
    }
}
