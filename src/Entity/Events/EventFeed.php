<?php


namespace App\Entity\Events;

use App\Entity\Event;
use App\Entity\FeedFertilizer;
use App\Model\EventInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Events\EventFeedRepository")
 */
class EventFeed extends Event
{
    /**
     * @ORM\Column(type="float", nullable=true, options={"unsigned"=true})
     */
    protected $water = 0;

    /**
     * @ORM\Column(type="float", nullable=true, options={"unsigned"=true})
     */
    protected $ph = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"unsigned"=true})
     */
    protected $tds = 0;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"unsigned"=true})
     */
    protected $ec = 0;

    /**
     * @ORM\Column(type="float", nullable=true, options={"unsigned"=true})
     */
    protected $temperature = 24.0;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FeedFertilizer", mappedBy="event", cascade={"persist"})
     */
    protected $fertilizers;

    public function cloneSelf(EventFeed $eventFeed): EventFeed
    {
        $this->setType($eventFeed->getType());
        $this->setWater($eventFeed->getWater());
        $this->setPh($eventFeed->getPh());
        $this->setTds($eventFeed->getTds());
        $this->setTemperature($eventFeed->getTemperature());
        $this->setEc($eventFeed->getEc());
        $this->setName($eventFeed->getName());
        $this->setPlant($eventFeed->getPlant());
        $this->setSensor($eventFeed->getSensor());
        $this->setNote($eventFeed->getNote() . "\nCloned");
        $this->setValue($eventFeed->getValue());
        $this->setValue1($eventFeed->getValue1());
        $this->setValue2($eventFeed->getValue2());
        $this->setValue3($eventFeed->getValue3());
        $listOfFerti = $eventFeed->getFertilizers();
        /** @var FeedFertilizer $ferti */
        foreach ($listOfFerti as $ferti) {
            $newFerti =  FeedFertilizer::cloneFrom($ferti);
            $this->addFertilizer($newFerti);
        }

        return $this;
    }

    public function __construct()
    {
        parent::__construct();
        $this->fertilizers = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getWater(): float
    {
        return $this->water;
    }

    /**
     * @param mixed $water
     */
    public function setWater($water): void
    {
        $this->water = $water;
    }

    /**
     * @return mixed
     */
    public function getPh(): float
    {
        return $this->ph;
    }

    /**
     * @param mixed $ph
     */
    public function setPh($ph): void
    {
        $this->ph = $ph;
    }

    /**
     * @return mixed
     */
    public function getTds(): int
    {
        return $this->tds;
    }

    /**
     * @param mixed $tds
     */
    public function setTds($tds): void
    {
        $this->tds = $tds;
    }

    /**
     * @return mixed
     */
    public function getEc(): int
    {
        if ($this->ec === null) {
            return 0;
        }
        return $this->ec;
    }

    /**
     * @param mixed $ec
     */
    public function setEc($ec): void
    {
        $this->ec = $ec;
    }

    /**
     * @return mixed
     */
    public function getTemperature(): float
    {
        return $this->temperature;
    }

    /**
     * @param mixed $temperature
     */
    public function setTemperature($temperature): void
    {
        $this->temperature = $temperature;
    }

    /**
     * @return Collection|FeedFertilizer[]
     */
    public function getFertilizers(): Collection
    {
        return $this->fertilizers;
    }

    public function addFertilizer(FeedFertilizer $fertilizer): self
    {
        if (!$this->fertilizers->contains($fertilizer)) {
            $this->fertilizers[] = $fertilizer;
            $fertilizer->setEvent($this);
        }

        return $this;
    }

    public function removeFertilizer(FeedFertilizer $fertilizer): self
    {
        if ($this->fertilizers->contains($fertilizer)) {
            $this->fertilizers->removeElement($fertilizer);
            // set the owning side to null (unless already changed)
            if ($fertilizer->getEvent() === $this) {
                $fertilizer->setEvent(null);
            }
        }

        return $this;
    }

//    /**
//     * @return float|mixed|string
//     */
//    public function getValue()
//    {
//        return $this->getWater();
//    }

//    public function setValue($value): EventInterface
//    {
////        if ($value !== null && $value === 'nan') {
////            $this->setWater(0);
////            $this->addNote('NaN_FOUND::' . $value . ';;');
////        } else {
////            $this->setWater((float)$value);
////        }
//
//        return $this;
//    }

}