<?php

namespace App\Entity\Events;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Event;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\Events\EventTemperatureRepository")
 */
class EventTemperature extends Event
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

    public function setValue($value)
    {
        $this->setTemperature((float)$value);

        return $this;
    }
}
