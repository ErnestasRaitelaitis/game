<?php

namespace App\Entity\Interface;

use App\Entity\Map;
use Doctrine\Common\Collections\Collection;

interface GameInterface
{
    public function getId(): ?int;
    public function getState(): string;
    public function setState(string $state): self;
    public function addParticipant(ParticipantInterface $participant): self;
    public function getParticipants(): Collection;
    public function getWhosTurn(): ?ParticipantInterface;
    public function activatePlayer(ParticipantInterface $participant): self;
    public function getMap(): ?Map;
    public function setMap(Map $map): self;
}