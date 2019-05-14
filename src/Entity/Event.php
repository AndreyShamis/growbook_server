<?php

namespace App\Entity;

use App\Entity\Events\EventTemperature;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping\Index;

/**
 * @ApiResource()
 * @ORM\Entity()
 * @ ORM\Map pedSuperclass(repositoryClass="App\Repository\EventRepository")
 * @ORM\DiscriminatorColumn(name = "andrey_type", type = "string", fieldName="")
 * @ORM\InheritanceType(value="SINGLE_TABLE")
 * @ ORM\DiscriminatorMap({"EventHumidity" = "ParentEntity", "child_entity" = "AppBundle\Entity\ChildEntity"})
 * @ORM\Table(name="events", indexes={
 *     @Index(name="index_type", columns={"type"}),
 *     @Index(name="fulltext_type", columns={"type"}, flags={"fulltext"}),
 *     @Index(name="fulltext_note", columns={"note"}, flags={"fulltext"}),
 *     @Index(name="fulltext_type_note", columns={"type", "note"}, flags={"fulltext"}),
 *     })
 * @ORM\HasLifecycleCallbacks()
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, options={"default"=""})
     */
    protected $name = '';

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="string")
     */
    protected $type = self::class;

    /**
     * @ORM\Column(type="text", options={"default"=""})
     */
    protected $value = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $note;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Plant", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $plant;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensor", inversedBy="events")
     */
    private $sensor;

    public function __construct()
    {
        try {
            $this->setUpdatedAt();
            $this->setCreatedAt();
        } catch (\Exception $e) {
        }
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
     * @return Event
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

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

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getNote(): string
    {
        if ($this->note === null) {
            return '';
        }
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        if ($note === null && $this->note !== null) {
            $note = '';
        } else {
            $this->note = $note;
        }

        return $this;
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

    public function getSensor(): ?Sensor
    {
        return $this->sensor;
    }

    public function setSensor(?Sensor $sensor): self
    {
        $this->sensor = $sensor;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    public function castAs($newClass) {
        $obj = new $newClass;
        foreach (get_object_vars($this) as $key => $name) {
            $obj->$key = $name;
        }
        return $obj;
    }

    public function addNote(string $newNote): self
    {
        if ($this->getNote() === null) {
            $this->setNote('');
        }
        if ($this->getNote() === '') {
            $this->setNote($newNote);
        } else {
            $this->setNote($this->getNote() . "\n" . $newNote);
        }
        return $this;
    }
}
