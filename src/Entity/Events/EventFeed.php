<?php


namespace App\Entity\Events;

use App\Entity\Event;
use Doctrine\ORM\Mapping as ORM;

class EventFeed extends Event
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
}