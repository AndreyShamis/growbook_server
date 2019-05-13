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
        $this->humidity = $humidity;

        return $this;
    }

    public function getValue()
    {
        return $this->getHumidity();
    }
}
