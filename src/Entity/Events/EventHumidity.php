<?php

namespace App\Entity\Events;

use App\Entity\Event;
use App\Model\EventInterface;
use App\Model\SensorEventInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Events\EventHumidityRepository")
 */
class EventHumidity extends Event implements SensorEventInterface
{
    /**
     * @ORM\Column(type="float")
     */
    private $humidity = 0;

    public function getHumidity(): float
    {
        return $this->humidity;
    }

    public function setHumidity(float $humidity): self
    {
        if ($humidity >= 0 && $humidity <= 100) {
            $this->humidity = $humidity;
        } else {
            $humidity = 0.01;
        }
        return $this;
    }

    public function getValue()
    {
        return $this->getHumidity();
    }

    public function setValue($value)
    {
        if ($value !== null && $value === 'nan') {
            $this->setHumidity(1);
            $this->addNote('NaN_FOUND::' . $value . ';;');
        } else {
            $this->setHumidity((float)$value);
        }


        return $this;
    }

    public function calculateThreshHold(): int
    {
        $diffThreshHold = 1;
        if ($this->getSensor() !== null && $this->getSensor()->getDiffThreshold() !== null) {
            $val = (float)$this->getSensor()->getDiffThreshold();
            if ($val >= 1 && $val <= 100) {
                $diffThreshHold = (int)$this->getSensor()->getDiffThreshold();
            }
        }
        return $diffThreshHold;
    }

    /**
     * @param EventInterface $otherEvent
     * @param bool $abs
     * @return float
     */
    public function diff(EventInterface $otherEvent, bool $abs=false): float
    {
        /** @var EventHumidity $otherEvent */
        $currentTmp = $this->getHumidity();
        $ot = $otherEvent->getHumidity();
        $td = $currentTmp - $ot;
        if ($abs) {
            $td = abs($td);
        }
        return $td;
    }

    public function humDiff(EventHumidity $otherEvent): bool
    {
        $td = $this->diff($otherEvent, true);
        if ($td < $this->calculateThreshHold()) {
            return false;
        }
        $this->addNote('DIFF_FOUND::' . $td . ';;ThreshHold_USED::'. $this->calculateThreshHold() . ';;OLD_VALUE::' . $otherEvent->getHumidity() . ';;');
        return true;
    }
}
