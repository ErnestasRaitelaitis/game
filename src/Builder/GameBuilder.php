<?php

namespace App\Builder;

use App\Builder\Interface\GameBuilderInterface;
use App\Entity\Game;
use App\Entity\Interface\GameInterface;
use App\Entity\Interface\ParticipantInterface;

class GameBuilder implements GameBuilderInterface
{
    public function build(ParticipantInterface $participant): GameInterface
    {
        $game = new Game();
        $game->addParticipant($participant);
        $game->setState(Game::STATE_WAITING_PARTICIPANTS);
        $game->activatePlayer($participant);

        return $game;
    }
}
