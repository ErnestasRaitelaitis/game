<?php

namespace App\Entity\Interface;

interface ParticipantInterface
{
    public function getId(): ?int;
    public function getName(): string;
    public function setName(string $name): self;
    public function getCode(): string;
    public function setCode(string $code): self;
}