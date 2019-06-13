<?php
/**
 * User: werd
 * Date: 15/05/19
 * Time: 14:38
 */

namespace App\Model;

use App\Entity\Sensor;
use App\Model\SensorInterface;

interface SensorEventInterface extends EventInterface
{
    /**
     * @param EventInterface $otherEvent
     * @param bool $abs
     * @return mixed
     */
    public function diff(EventInterface $otherEvent, bool $abs=false);
    public function calculateThreshHold(float $diffThreshHold, int $round): float;
    public function getSensor(): ?SensorInterface;
    public function needUpdate(EventInterface $otherEvent): bool;
}