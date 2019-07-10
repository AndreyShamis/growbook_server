<?php


namespace App\Entity\Events;

use App\Entity\Fertilizer;
use App\Model\EventInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Events\EventFertilizeRepository")
 */
class EventFertilize extends EventFeed implements EventInterface
{
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Fertilizer")
     */
    private $fertilizer;

    /**
     * @ORM\Column(type="float", nullable=true, options={"unsigned"=true})
     */
    private $amount = 1.0;

    public function __construct()
    {
        parent::__construct();
        $this->fertilizer = new ArrayCollection();
    }

    /**
     * @return Collection|Fertilizer[]
     */
    public function getFertilizer(): Collection
    {
        return $this->fertilizer;
    }

    public function addFertilizer(Fertilizer $fertilizer): self
    {
        if (!$this->fertilizer->contains($fertilizer)) {
            $this->fertilizer[] = $fertilizer;
        }

        return $this;
    }

    public function removeFertilizer(Fertilizer $fertilizer): self
    {
        if ($this->fertilizer->contains($fertilizer)) {
            $this->fertilizer->removeElement($fertilizer);
        }

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}