<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\Interface\GameInterface;

class RandomizePositions
{
    public function randomizeInitialPlayerPositions(GameInterface $game)
    {
        $map = $game->getMap();

        $players = $game->getParticipants();

        $availableCells = $map->getCells(true)->toArray();

        $randKeys = array_rand($availableCells, count($players));

        foreach($players as $player) {
            /** @var Cell $randCell */
            $cell = $availableCells[array_shift($randKeys)];
            $cell->setPlayer($player);
        }
    }
}