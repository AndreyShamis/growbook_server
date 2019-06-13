<?php
/**
 * Date: 15/05/19
 * Time: 14:25
 */

namespace App\Model;


interface EventInterface
{
    public function getId(): ?int;
    public function getName(): string;
    public function setName(string $name): EventInterface;
    public function getCreatedAt(): \DateTimeInterface;
    public function setCreatedAt(): void;
    public function getUpdatedAt(): ?\DateTimeInterface;
    public function setUpdatedAt(): void;
    public function getType(): string;

    public function setType($type): EventInterface;
    public function getValue();

    public function setValue($value): EventInterface;
    public function getNote(): string;
    public function setNote(string $note=null): EventInterface;
    public function getPlant(): PlantInterface;
    public function setPlant(PlantInterface $plant): EventInterface;
    public function getSensor(): ?SensorInterface;
    public function setSensor(?SensorInterface $sensor): EventInterface;
    public function addNote(string $newNote): EventInterface;
    public function getValue1(): ?string;
    public function setValue1(?string $value1): EventInterface;
    public function getValue2(): ?string;
    public function setValue2(?string $value2): EventInterface;
    public function getValue3(): ?float;
    public function setValue3(?float $value3): EventInterface;

    public function diff(EventInterface $otherEvent, bool $abs);
    public function calculateThreshHold(float $diffThreshHold, int $round): float;
    public function needUpdate(EventInterface $otherEvent): bool;
}