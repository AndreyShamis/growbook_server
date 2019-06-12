<?php
/**
 * User: werd
 * Date: 12/06/19
 * Time: 19:32
 */

namespace App\Entity\Events;

use App\Entity\Event;
use App\Model\EventInterface;
use App\Model\SensorEventInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class EventSoilHydrometer extends Event implements SensorEventInterface
{
    /**
     * @ORM\Column(type="smallint")
     */
    private $hydrometer = 0;


    public function getValue()
    {
        return $this->getHydrometer();
    }

    public function setValue($value)
    {
        if ($value !== null && $value === 'nan') {
            $this->setHydrometer(1);
            $this->addNote('NaN_FOUND::' . $value . ';;');
        } else {
            $this->setHydrometer((int)$value);
        }


        return $this;
    }

    /**
     * @return mixed
     */
    public function getHydrometer()
    {
        return $this->hydrometer;
    }

    /**
     * @param mixed $hydrometer
     */
    public function setHydrometer($hydrometer): void
    {
        $this->hydrometer = $hydrometer;
    }


    /**
     * @param EventInterface $otherEvent
     * @param bool $abs
     * @return mixed
     */
    public function diff(EventInterface $otherEvent, bool $abs = false)
    {
        /** @var EventSoilHydrometer $otherEvent */
        $currentTmp = $this->getHydrometer();
        $ot = $otherEvent->getHydrometer();
        $td = $currentTmp - $ot;
        if ($abs) {
            $td = abs($td);
        }
        return $td;
    }

    public function hydroDiff(EventSoilHydrometer $otherEvent): bool
    {
        $td = $this->diff($otherEvent, true);
        if ($td < $this->calculateThreshHold()) {
            return false;
        }
        $this->addNote('DIFF_FOUND::' . round($td, 0) . ';;ThreshHold_USED::'. $this->calculateThreshHold() . ';;');
        return true;
    }

    public function calculateThreshHold(): float
    {
        $diffThreshHold = 3;
        if ($this->getSensor() !== null && $this->getSensor()->getDiffThreshold() !== null) {
            $val = (float)$this->getSensor()->getDiffThreshold();
            if ($val > 0.01) {
                $diffThreshHold = (float)$this->getSensor()->getDiffThreshold();
            }
        }
        return round($diffThreshHold, 2);
    }
}