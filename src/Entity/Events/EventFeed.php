<?php


namespace App\Entity\Events;

use App\Entity\Event;
use App\Model\EventInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class EventFeed extends Event implements EventInterface
{
    /**
     * @ORM\Column(type="float", options={"unsigned"=true, "default"="0"})
     */
    protected $water = 0;

    /**
     * @ORM\Column(type="float", options={"unsigned"=true})
     */
    protected $ph = 0;

    /**
     * @ORM\Column(type="smallint", options={"unsigned"=true, "default"="300"})
     */
    protected $tds = 300;

    /**
     * @ORM\Column(type="smallint", options={"unsigned"=true, "default"="300"})
     */
    protected $ec = 300;

    /**
     * @ORM\Column(type="smallint", options={"unsigned"=true, "default"="300"})
     */
    protected $temperature = 24;

    /**
     * @return mixed
     */
    public function getWater()
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
    public function getPh()
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
    public function getTds()
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
    public function getEc()
    {
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
    public function getTemperature()
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


}