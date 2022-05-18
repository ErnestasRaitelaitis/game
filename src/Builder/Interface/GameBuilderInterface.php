<?php

declare(strict_types=1);

namespace App\Builder\Interface;

use App\Entity\Interface\GameInterface;
use App\Entity\Interface\ParticipantInterface;

interface GameBuilderInterface
{
    public function build(ParticipantInterface $participant): GameInterface;
}

