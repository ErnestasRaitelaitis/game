<?php
 
declare(strict_types=1);

namespace App\Builder;

use App\Entity\Interface\GameInterface;
use App\Entity\Interface\ParticipantInterface;

class GameResponse
{
    public function build(GameInterface $game): array
    {
        $gameData = [
            'game-data'=> [
                'game-id' => $game->getId(),
                'state' => $game->getState(),
                'active-player' => $game->getWhosTurn()->getName() ?? ''
            ]
        ];

        $mapData = [];

        $map = $game->getMap();
        if ($map) {
            $mapData = [
                'map-data' => [
                    'size' => sprintf('%d X %d', $map->getWidth(), $map->getHeight()),
                    'map' => $map->getCellMatrix()
                ]
            ];
        }

        $participants = [];

        $position = ['first', 'second'];

        /** @var ParticipantInterface $participant */
        foreach ($game->getParticipants() as $participant) {
            $participants[] = [
                sprintf('%s player', array_shift($position)) => [
                    'name' => $participant->getName(),
                    'code' => $participant->getCode(),
                ],
            ];
        }

        return array_merge([], $gameData, $mapData, ['participants' => $participants]);
    }
}