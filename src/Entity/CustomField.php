<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CustomFieldRepository")
 * @ORM\Table(name="custom_field", indexes={
 *     @Index(name="index_3_keys", columns={"object_host_id", "object_host_type", "property"}),
 *     @Index(name="index_2_keys", columns={"object_host_id", "object_host_type"}),
 *     }
 *     , uniqueConstraints={@ORM\UniqueConstraint(name="uniq_entry", columns={"object_host_id", "object_host_type", "property"})}
 *     )
 */
class CustomField
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $property;

    /**
     * @ORM\Column(type="object")
     */
    private $propertyValue;

    /**
     * @ORM\Column(type="text", length=16777213, options={"default"=""})
     */
    private $propertyValueString;

    /**
     * @ORM\Column(type="bigint", options={"unsigned"=true})
     */
    private $object_host_id;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $object_host_type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $propertyValueType;

    public function __construct()
    {
        try {
            $this->setCreatedAt(new \DateTime());
        } catch (\Exception $e) {
        }
        try {
            $this->setUpdatedAt(new \DateTime());
        } catch (\Exception $e) {
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function setProperty(string $property): self
    {
        $this->property = $property;

        return $this;
    }

    public function getPropertyValue()
    {
        return $this->propertyValue;
    }

    public function setPropertyValue($propertyValue): self
    {
        $this->propertyValue = $propertyValue;
        $this->setPropertyValueString($propertyValue);
        $this->setPropertyValueType(gettype($propertyValue));
        return $this;
    }

    public function getPropertyValueString(): ?string
    {
        return $this->propertyValueString;
    }

    public function setPropertyValueString(string $propertyValueString): self
    {
        $this->propertyValueString = $propertyValueString;

        return $this;
    }

    public function getObjectHostId(): ?int
    {
        return $this->object_host_id;
    }

    public function setObjectHostId(int $object_host_id): self
    {
        $this->object_host_id = $object_host_id;

        return $this;
    }

    public function getObjectHostType(): ?string
    {
        return $this->object_host_type;
    }

    public function setObjectHostType(string $object_host_type): self
    {
        $this->object_host_type = $object_host_type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeInterface $created_at
     * @return CustomField
     */
    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getPropertyValueType(): ?string
    {
        return $this->propertyValueType;
    }

    public function setPropertyValueType(?string $propertyValueType): self
    {
        $this->propertyValueType = $propertyValueType;

        return $this;
    }
}
