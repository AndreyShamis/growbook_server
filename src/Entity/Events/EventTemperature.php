<?php

namespace App\Entity\Events;

use App\Entity\Event;
use App\Model\EventInterface;
use App\Model\SensorEventInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Events\EventTemperatureRepository")
 */
class EventTemperature extends Event implements SensorEventInterface
{

    /**
     * @ORM\Column(type="float", options={"default"="0"})
     */
    private $temperature = 0;

    /**
     * @return float
     */
    public function getTemperature(): float
    {
//        $t = $this->getValue();
//        if ($t !== null) {
//            return (float)$t;
//        }
//        return 0;
        return $this->temperature;
    }

    /**
     * @param float $temperature
     * @return EventTemperature
     */
    public function setTemperature(float $temperature): self
    {
        if ($temperature >= -276 && $temperature < 1000) {
            $this->temperature = $temperature;
        } else {
            $this->temperature = 0.12345;
        }

        return $this;
    }

    public function getValue()
    {
        return $this->getTemperature();
    }

    public function setValue($value): EventInterface
    {
        $this->setTemperature((float)$value);

        return $this;
    }

    public function calculateThreshHold(float $diffThreshHold=0.2, int $round=7): float
    {
        return parent::calculateThreshHold($diffThreshHold, $round);
    }

    /**
     * @param EventInterface $otherEvent
     * @param bool $abs
     * @return float
     */
    public function diff(EventInterface $otherEvent, bool $abs=false): float
    {
        /** @var EventTemperature $otherEvent */
        $currentTmp = $this->getTemperature();
        $ot = $otherEvent->getTemperature();
        $td = $currentTmp - $ot;
        if ($abs) {
            $td = abs($td);
        }
        return $td;
    }

//    public function tempDiff(EventTemperature $otherEvent): bool
//    {
//        $td = $this->diff($otherEvent, true);
//        if ($td < $this->calculateThreshHold()) {
//            return false;
//        }
//        $this->addNote('DIFF_FOUND::' . round($td, 4) . ';;ThreshHold_USED::'. $this->calculateThreshHold() . ';;');
//        return true;
//    }
}
