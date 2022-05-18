<?php

declare(strict_types=1);

namespace App\Builder\Interface;

use App\Entity\Interface\ParticipantInterface;

interface ParticipantBuilderInterface
{
    public function build(string $name): ParticipantInterface;
}