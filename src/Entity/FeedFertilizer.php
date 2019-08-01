<?php

namespace App\Entity;

use App\Entity\Events\EventFeed;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FeedFertilizerRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="uniq", columns={"fertilizer", "event"})})
 */
class FeedFertilizer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint", options={"unsigned"=true})
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Fertilizer") // , cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="fertilizer")
     */
    protected $fertilizer;

    /**
     * @ORM\Column(type="float")
     */
    protected $amount = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Events\EventFeed", inversedBy="fertilizers")
     * @ORM\JoinColumn(name="event")
     */
    protected $event;

//    /**
//     * @ORM\ManyToOne(targetEntity="App\Entity\Fertilizer")
//     */
//    private $dddd;

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

    public function __toString()
    {
        return $this->getAmount() . 'ml - [' . $this->getFertilizer() . ']';
    }
//
//    public function getDddd(): ?Fertilizer
//    {
//        return $this->dddd;
//    }
//
//    public function setDddd(?Fertilizer $dddd): self
//    {
//        $this->dddd = $dddd;
//
//        return $this;
//    }
}
