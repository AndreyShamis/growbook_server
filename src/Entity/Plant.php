<?php

namespace App\Entity;

use App\Model\EventInterface;
use App\Model\PlantInterface;
use App\Model\SensorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlantRepository")
 * @ORM\Table(name="plants", uniqueConstraints={@ORM\UniqueConstraint(name="unique_uniqId", columns={"uniq_id"})},
 *     indexes={
 *     @Index(name="index_name", columns={"name"}),
 *     @Index(name="fulltext_name", columns={"name"}, flags={"fulltext"}),
 *     @Index(name="fulltext_uniq_id", columns={"uniq_id"}, flags={"fulltext"}),
 *     @Index(name="fulltext_name_uniq_id", columns={"name", "uniq_id"}, flags={"fulltext"}),
 *     })
 * @ORM\HasLifecycleCallbacks()
 */
class Plant implements PlantInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finishedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Event", mappedBy="plant", orphanRemoval=true, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"updatedAt" = "DESC"})
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sensor", mappedBy="Plant")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $sensors;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uniqId;

    /**
     * @ORM\Column(type="string", length=50, columnDefinition="ENUM('unknown', 'soil', 'hydro', 'aero') NOT NULL DEFAULT 'soil'")
     */
    private $soilMedium = 'soil';

    /**
     * @ORM\Column(type="string", length=30, columnDefinition="ENUM('standart', 'felt', 'smart', 'air', 'hempty_bucket') NOT NULL DEFAULT 'standart'")
     */
    private $pot = 'standart';

    /**
     * @var int
     * @ORM\Column(type="bigint", options={"default"="0"})
     */
    protected $uptime = 0;
    /**
     * @var int
     * @ORM\Column(type="smallint", options={"default"="0"})
     */
    protected $rssi = 0;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default"="0"})
     */
    protected $resetCounter = 0;

    public function __construct()
    {
        try {
            $this->setUpdatedAt();
            $this->setCreatedAt();
            $this->setStartedAt(new \DateTime());
        } catch (\Exception $e) {
        }
        $this->events = new ArrayCollection();
        $this->sensors = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getRssi(): int
    {
        return $this->rssi;
    }

    /**
     * @param int $rssi
     */
    public function setRssi(int $rssi): void
    {
        $this->rssi = $rssi;
    }

    /**
     * @return int
     */
    public function getResetCounter(): int
    {
        return $this->resetCounter;
    }

    /**
     * @param int $resetCounter
     */
    public function setResetCounter(int $resetCounter): void
    {
        $this->resetCounter = $resetCounter;
    }

    /**
     * @return int
     */
    public function getUptime(): int
    {
        return $this->uptime;
    }

    /**
     * @param int $uptime
     */
    public function setUptime(int $uptime): void
    {
        try {
            $current_uptime = $this->getUptime();
            if ($current_uptime > $uptime) {
                $this->setResetCounter($this->getResetCounter() + 1);
            }
        } catch (\Throwable $ex) {

        }

        $this->uptime = $uptime;
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

    public function setName(string $name): PlantInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return Plant
     * @throws \Exception
     */
    public function setCreatedAt(): PlantInterface
    {
        $this->createdAt = new \DateTime();

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

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): PlantInterface
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeInterface
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTimeInterface $finishedAt): PlantInterface
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    /**
     * @param EventInterface $event
     * @return PlantInterface
     */
    public function addEvent(EventInterface $event): PlantInterface
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setPlant($this);
        }

        return $this;
    }

    /**
     * @param EventInterface $event
     * @return PlantInterface
     */
    public function removeEvent(EventInterface $event): PlantInterface
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getPlant() === $this) {
                $event->setPlant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sensor[]
     */
    public function getSensors(): Collection
    {
        return $this->sensors;
    }

    public function addSensor(SensorInterface $sensor): PlantInterface
    {
        if (!$this->sensors->contains($sensor)) {
            $this->sensors[] = $sensor;
            $sensor->setPlant($this);
        }

        return $this;
    }

    public function removeSensor(SensorInterface $sensor): PlantInterface
    {
        if ($this->sensors->contains($sensor)) {
            $this->sensors->removeElement($sensor);
            // set the owning side to null (unless already changed)
            if ($sensor->getPlant() === $this) {
                $sensor->setPlant(null);
            }
        }

        return $this;
    }

    public function getUniqId(): string
    {
        if ($this->uniqId === null) {
            return '';
        }
        return $this->uniqId;
    }

    public function setUniqId(string $uniqId): PlantInterface
    {
        $this->uniqId = $uniqId;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    public function getSoilMedium(): ?string
    {
        return $this->soilMedium;
    }

    public function setSoilMedium(string $soilMedium): PlantInterface
    {
        $this->soilMedium = $soilMedium;

        return $this;
    }

    public function getPot(): ?string
    {
        return $this->pot;
    }

    public function setPot(string $pot): PlantInterface
    {
        $this->pot = $pot;

        return $this;
    }
}
