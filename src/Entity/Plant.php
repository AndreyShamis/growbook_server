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
     * @ORM\OrderBy({"happenedAt" = "DESC"})
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sensor", mappedBy="Plant")
     * @ORM\OrderBy({"eventType" = "DESC"})
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
     * @ORM\Column(type="smallint", options={"default"="0"})
     */
    protected $resetCounter = 0;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default"="0"})
     */
    protected $light = false;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\CustomField")
     */
    protected $properties;

    /** @var bool  */
    protected $lightChanged = false;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="plants")
     * @ORM\JoinTable()
     */
    protected $owners;

    /**
     * @ORM\Column(type="smallint", options={"default"="0"})
     */
    protected $photoPeriod = 0;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NodeCommand", mappedBy="plant", orphanRemoval=true)
     */
    private $nodeCommands;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $version;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="plant", orphanRemoval=true)
     */
    private $comments;

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
        $this->properties = new ArrayCollection();
        $this->owners = new ArrayCollection();
        $this->nodeCommands = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getLightChanged(): bool
    {
        return $this->lightChanged;
    }
    /**
     * @return bool
     */
    public function isLight(): bool
    {
        return $this->light;
    }

    public function lightStatus(): string
    {
        if ($this->isLight()) {
            return 'On';
        }

        return 'Off';
    }

    /**
     * @param bool $light
     * @return PlantInterface
     */
    public function setLight(bool $light): PlantInterface
    {
        if ($this->light !== null && $this->light !== $light) {
            $this->lightChanged = true;
        }
        $this->light = $light;
        return $this;
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
     * @return PlantInterface
     */
    public function setRssi(int $rssi): PlantInterface
    {
        $this->rssi = $rssi;
        return $this;
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
     * @return PlantInterface
     */
    public function setResetCounter(int $resetCounter): PlantInterface
    {
        $this->resetCounter = $resetCounter;
        if ($this->resetCounter > 10000) {
            $this->resetCounter = 0;
        }
        return $this;
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
     * @return PlantInterface
     */
    public function setUptime(int $uptime): PlantInterface
    {
        try {
            $current_uptime = $this->getUptime();
            if ($current_uptime > ($uptime + 60)) {
                $this->setResetCounter($this->getResetCounter() + 1);
            }
        } catch (\Throwable $ex) {

        }

        $this->uptime = $uptime;

        return $this;
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

    /**
     * @return Collection|CustomField[]
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function addProperty(CustomField $property): self
    {
        if (!$this->properties->contains($property)) {
            $this->properties[] = $property;
        }

        return $this;
    }

    public function removeProperty(CustomField $property): self
    {
        if ($this->properties->contains($property)) {
            $this->properties->removeElement($property);
        }

        return $this;
    }

    /**
     * @return Collection|\App\Entity\User[]
     */
    public function getOwners(): Collection
    {
        return $this->owners;
    }

    public function addOwner(\App\Entity\User $owner): self
    {
        if (!$this->owners->contains($owner)) {
            $this->owners[] = $owner;
        }

        return $this;
    }

    public function removeOwner(\App\Entity\User $owner): self
    {
        if ($this->owners->contains($owner)) {
            $this->owners->removeElement($owner);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getPhotoPeriod(): int
    {
        return $this->photoPeriod;
    }

    /**
     * @param int $photoPeriod
     * @return Plant
     */
    public function setPhotoPeriod(int $photoPeriod): self
    {
        $this->photoPeriod = $photoPeriod;

        return $this;
    }

    public static function getPhotoPeriodList(): array
    {
        $ret = array();
        $ret[0] = 'Rooting';
        $ret[1] = 'Growing';
        $ret[2] = 'PreFlowering';
        $ret[3] = 'Flowering';
        $ret[4] = 'Ripening';
        $ret[5] = 'Cleaning';
        $ret[6] = 'Drying';
        $ret[7] = 'PreFinish';
        $ret[8] = 'Finish';
        return $ret;
    }

    public static function getPhotoPeriodListAsHelp(): string
    {
        $ret = '';
        $arr = self::getPhotoPeriodList();
        foreach ($arr as $key => $val) {
            if (strlen($ret) > 1) {
                $ret .= '/';
            }
            $ret .= $key . '=' . $val;
        }
        return $ret;
    }

    public function getPeriodName(): string
    {
        $p = $this->getPhotoPeriod();
        $arr = self::getPhotoPeriodList();
        if (array_key_exists($p, $arr)) {
            return $arr[$p];
        }
        return 'Unknown';
    }

    /**
     * @return NodeCommand
     */
    public function getNodeNotPublishedCommand(): ?NodeCommand
    {
        $cmds = $this->getNodeCommands();
        foreach ($cmds as $cmd) {
            if ($cmd->getPublished() === false) {
                return $cmd;
            }
        }
        return null;
    }

    /**
     * @return Collection|NodeCommand[]
     */
    public function getNodeCommands(): Collection
    {
        return $this->nodeCommands;
    }

    public function addNodeCommand(NodeCommand $nodeCommand): self
    {
        if (!$this->nodeCommands->contains($nodeCommand)) {
            $this->nodeCommands[] = $nodeCommand;
            $nodeCommand->setPlant($this);
        }

        return $this;
    }

    public function removeNodeCommand(NodeCommand $nodeCommand): self
    {
        if ($this->nodeCommands->contains($nodeCommand)) {
            $this->nodeCommands->removeElement($nodeCommand);
            // set the owning side to null (unless already changed)
            if ($nodeCommand->getPlant() === $this) {
                $nodeCommand->setPlant(null);
            }
        }

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPlant($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPlant() === $this) {
                $comment->setPlant(null);
            }
        }

        return $this;
    }
}
