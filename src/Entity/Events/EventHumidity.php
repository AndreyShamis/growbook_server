<?php

namespace App\Entity\Events;

use App\Entity\Event;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Events\EventHumidityRepository")
 */
class EventHumidity extends Event
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
        $this->setHumidity((float)$value);

        return $this;
    }
}
