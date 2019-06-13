<?php

namespace App\Entity;

use App\Model\PlantInterface;
use App\Model\SensorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
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
     * @ORM\Column(type="float", options={"default"="0"})
     */
    private $diffThreshold = 0;

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
     * @ORM\JoinColumn(name="last_event_id", onDelete="SET NULL")
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
    public function setName(string $name): SensorInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getUniqId(): ?string
    {
        return $this->uniqId;
    }

    public function setUniqId(string $uniqId): SensorInterface
    {
        $this->uniqId = $uniqId;

        return $this;
    }

    public function getPlant(): ?PlantInterface
    {
        return $this->Plant;
    }

    public function setPlant(?PlantInterface $Plant): SensorInterface
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

    public function addEvent(Event $event): SensorInterface
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setSensor($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): SensorInterface
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

    public function setWriteForceEveryXseconds(int $writeForceEveryXseconds): SensorInterface
    {
        $this->writeForceEveryXseconds = $writeForceEveryXseconds;

        return $this;
    }

    public function getDiffThreshold(): float
    {
        return $this->diffThreshold;
    }

    public function setDiffThreshold(float $diffThreshold): SensorInterface
    {
        $this->diffThreshold = $diffThreshold;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSupportEvents(): bool
    {
        if ($this->supportEvents === null) {
            return false;
        }
        return $this->supportEvents;
    }

    public function setSupportEvents(bool $supportEvents): SensorInterface
    {
        $this->supportEvents = $supportEvents;

        return $this;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(?string $eventType): SensorInterface
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

    public function setLastEvent(?Event $lastEvent): SensorInterface
    {
        $this->lastEvent = $lastEvent;

        return $this;
    }

}
