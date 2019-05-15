<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Model\SensorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\SensorRepository")
 * @ORM\Table(name="sensors", uniqueConstraints={@ORM\UniqueConstraint(name="unique_uniqId", columns={"uniq_id"})},
 *     indexes={
 *     @Index(name="index_name", columns={"name"}),
 *     @Index(name="fulltext_name", columns={"name"}, flags={"fulltext"}),
 *     @Index(name="fulltext_uniq_id", columns={"uniq_id"}, flags={"fulltext"}),
 *     @Index(name="fulltext_name_uniq_id", columns={"name", "uniq_id"}, flags={"fulltext"}),
 *     })
 * @ORM\HasLifecycleCallbacks()
 */
class Sensor implements SensorInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uniqId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Plant", inversedBy="sensors")
     */
    private $Plant;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="sensor", fetch="EXTRA_LAZY", cascade={"all"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $events;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true, "default"="1000"})
     */
    private $writeForceEveryXseconds = 1000;

    /**
     * @ORM\Column(type="string", length=255, options={"default"=""})
     */
    private $diffThreshold = '';

    /**
     * @ORM\Column(type="boolean", options={"default"="0"})
     */
    private $supportEvents = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $eventType;

    /**
     * @ORM\Column(type="datetime", options={"default"="CURRENT_TIMESTAMP"})
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", options={"default"="CURRENT_TIMESTAMP"})
     */
    protected $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Event", cascade={"all"})
     */
    private $lastEvent;

    public function __construct()
    {
        try {
            $this->setUpdatedAt();
            $this->setCreatedAt();
        } catch (\Exception $e) {
        }
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        if ($this->name === null) {
            return '';
        }
        return $this->name;
    }

    /**
     * @param string $name
     * @return Sensor
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUniqId(): ?string
    {
        return $this->uniqId;
    }

    public function setUniqId(string $uniqId): self
    {
        $this->uniqId = $uniqId;

        return $this;
    }

    public function getPlant(): ?Plant
    {
        return $this->Plant;
    }

    public function setPlant(?Plant $Plant): self
    {
        $this->Plant = $Plant;

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setSensor($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getSensor() === $this) {
                $event->setSensor(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    public function getWriteForceEveryXseconds(): ?int
    {
        return $this->writeForceEveryXseconds;
    }

    public function setWriteForceEveryXseconds(int $writeForceEveryXseconds): self
    {
        $this->writeForceEveryXseconds = $writeForceEveryXseconds;

        return $this;
    }

    public function getDiffThreshold(): ?string
    {
        return $this->diffThreshold;
    }

    public function setDiffThreshold(string $diffThreshold): self
    {
        $this->diffThreshold = $diffThreshold;

        return $this;
    }

    public function getSupportEvents(): ?bool
    {
        return $this->supportEvents;
    }

    public function setSupportEvents(bool $supportEvents): self
    {
        $this->supportEvents = $supportEvents;

        return $this;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(?string $eventType): self
    {
        $this->eventType = $eventType;

        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @throws \Exception
     */
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTime();
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

    public function getLastEvent(): ?Event
    {
        return $this->lastEvent;
    }

    public function setLastEvent(?Event $lastEvent): self
    {
        $this->lastEvent = $lastEvent;

        return $this;
    }

}
