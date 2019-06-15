<?php
/**
 * User: werd
 * Date: 17/05/19
 * Time: 14:25
 */

namespace App\Model;


use Doctrine\Common\Collections\Collection;

interface PlantInterface
{
    public function getId();
    public function getName(): string;
    public function setName(string $name): PlantInterface;

    public function getRssi(): int;
    public function setRssi(int $rssi): PlantInterface;
    public function getResetCounter(): int;
    public function setResetCounter(int $resetCounter): PlantInterface;
    public function getUptime(): int;
    public function setUptime(int $uptime): PlantInterface;

    public function getCreatedAt(): ?\DateTimeInterface;
    public function setCreatedAt(): PlantInterface;
    public function getUpdatedAt(): ?\DateTimeInterface;
    public function setUpdatedAt(): void;
    public function getStartedAt(): ?\DateTimeInterface;
    public function setStartedAt(\DateTimeInterface $startedAt): PlantInterface;
    public function getFinishedAt(): ?\DateTimeInterface;
    public function setFinishedAt(?\DateTimeInterface $finishedAt): PlantInterface;

    public function getEvents(): Collection;
    public function addEvent(EventInterface $event): PlantInterface;
    public function removeEvent(EventInterface $event): PlantInterface;

    public function getSensors(): Collection;
    public function addSensor(SensorInterface $sensor): PlantInterface;
    public function removeSensor(SensorInterface $sensor): PlantInterface;

    public function getUniqId(): string;
    public function setUniqId(string $uniqId): PlantInterface;
    public function getSoilMedium(): ?string;
    public function setSoilMedium(string $soilMedium): PlantInterface;
    public function getPot(): ?string;
    public function setPot(string $pot): PlantInterface;
}