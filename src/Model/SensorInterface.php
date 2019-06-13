<?php
/**
 * User: werd
 * Date: 15/05/19
 * Time: 14:42
 */

namespace App\Model;


interface SensorInterface
{
    public function getSupportEvents(): bool;
    public function getDiffThreshold(): ?string;
    public function getId(): ?int;
    public function setPlant(?PlantInterface $Plant): SensorInterface;
    public function getPlant(): ?PlantInterface;
}