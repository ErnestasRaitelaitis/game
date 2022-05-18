<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\Map;
use App\Repository\MapRepository;

class MapForGame
{
    private MapRepository $mapRepository;

    public function __construct(MapRepository $mapRepository)
    {
        $this->mapRepository = $mapRepository;
    }

    public function selectRandom(): ?Map
    {
        $maps = $this->mapRepository->findAll();
        
        return count($maps) > 0 ? $maps[array_rand($maps, 1)] : null;
    }
    
}